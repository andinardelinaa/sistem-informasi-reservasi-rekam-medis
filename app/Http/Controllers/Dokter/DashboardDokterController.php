<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservasi;
use App\Models\Jadwal;
use App\Models\RekamMedis;
use App\Models\Dokter;
use App\Models\PerubahanJadwalDokter;
use Carbon\Carbon;

class DashboardDokterController extends Controller
{
    public function dashboard()
    {   //cari dokter yang login
        $dokter = Dokter::where('user_id', Auth::id())->first();

        if (!$dokter) {
            abort(404, 'Data dokter tidak ditemukan');
        }
        $dokterId = $dokter->id_dokter;
        $hariMap = [ //mapping hari
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
            'Sunday'    => 'minggu',
        ];
        $hariIni = $hariMap[now()->format('l')];

        //cek apakah dokter sedang melakukan perubahan jadwal
        $perubahanSebagaiAsal = PerubahanJadwalDokter::where('dokter_id', $dokterId)
            ->whereDate('tanggal', today())
            ->where('status', 'aktif')
            ->first();

        // Cek apakah menjadi dokter pengganti
        $perubahanSebagaiPengganti = PerubahanJadwalDokter::where('dokter_pengganti_id', $dokterId)
            ->whereDate('tanggal', today())
            ->where('status', 'aktif')
            ->get();
        
        // colletion laravel
        $jadwalHariIniList = collect();

        if ($perubahanSebagaiAsal) {
            if ( //jika jadwal diganti atau tambahan
                $perubahanSebagaiAsal->jenis_perubahan === 'pengganti' ||
                $perubahanSebagaiAsal->jenis_perubahan === 'tambahan'
            ) {
                $perubahanSebagaiAsal->is_override = true;
                $jadwalHariIniList->push($perubahanSebagaiAsal);
            }
            
        // jika libur, tidak ada perubahan
        } else {
            $jadwalNormal = Jadwal::with('klinik')
                ->where('id_dokter', $dokterId)
                ->where('hari', $hariIni)
                ->where('is_aktif', 1)
                ->orderBy('jam_mulai')
                ->get();
            
            $jadwalHariIniList = $jadwalNormal;
        }
        //untuk dokter pengganti, menampilkan jadwal dokter yang digantikan
        foreach ($perubahanSebagaiPengganti as $p) {
            //ambil jadwal dokter yang digantikan
            $jadwalPengganti = Jadwal::with('klinik')
                ->where('id_dokter', $p->dokter_id)
                ->where('hari', $hariIni)
                ->where('is_aktif', 1)
                ->first();
            
            if ($jadwalPengganti) {
                $jadwalPengganti->is_pengganti = true; // penanda hari ini jadwal pengganti
                $jadwalPengganti->nama_dokter_diganti = $p->dokter->nama; // Relasi dokter asli
                $jadwalHariIniList->push($jadwalPengganti);
            }
        }
        //urut berdasarkan jam
        $jadwalHariIniList = $jadwalHariIniList->sortBy('jam_mulai');

        //antrean mengambil relasi pasien dan jadwal
        $antreanHariIniList = Reservasi::with(['pasien', 'jadwal'])
            ->whereDate('tgl_reservasi', today()) //hari ini
            ->whereHas('jadwal', function ($q) use ($dokterId) { //milik dokter yg login
                $q->where('id_dokter', $dokterId);
            });
        //kalo dokter punya pengganti
        if ($perubahanSebagaiAsal && $perubahanSebagaiAsal->dokter_pengganti_id) {
            // pasien yang masih menunggu dipenggantikan ke dokter pengganti
            $antreanHariIniList->where('status', '!=', 'menunggu');

            // jangan tampilkan pasien yang sudah dibuat rekam medis oleh dokter pengganti
            $antreanHariIniList->whereDoesntHave('rekamMedis', function ($q) use ($dokterId) {
                $q->where('id_dokter', '!=', $dokterId);
            });
        }

        $antreanHariIniList = $antreanHariIniList
            ->orderBy('jam_reservasi')
            ->get();
        //menambahkan pasien dr dokter yg digantikan
        foreach ($perubahanSebagaiPengganti as $p) {
            //ambil data reservasi dengan relasi pasien dan jadwal
            $antreanPengganti = Reservasi::with(['pasien', 'jadwal'])
                ->whereDate('tgl_reservasi', today()) //hanya hari ini
                ->whereIn('status', ['menunggu', 'tindakan', 'selesai'])// hanya pasien yang masih menunggu
                ->whereHas('jadwal', function ($q) use ($p) {
                    $q->where('id_dokter', $p->dokter_id);
                })
                ->get();

            foreach ($antreanPengganti as $reservasi) {
                $reservasi->is_pengganti = true; //menambah attribut sementara
                //menyimpan nama dokter yg digantikan
                $reservasi->nama_dokter_diganti = $p->dokter->nama;

                $antreanHariIniList->push($reservasi);
            }
        }
        
        // Sort ulang berdasarkan jam reservasi
        $antreanHariIniList = $antreanHariIniList
            ->sortBy('jam_reservasi')
            ->values();

        // total statistik
        $totalReservasi = Reservasi::whereHas('jadwal', function ($q) use ($dokterId) {
            $q->where('id_dokter', $dokterId);
        })->count();
        //menghitung rekam medis
        $rekamMedis = RekamMedis::where('id_dokter', $dokterId)->count();
        //jadwal aktif
        $jadwalAktif = Jadwal::where('id_dokter', $dokterId)
            ->where('is_aktif', 1)
            ->distinct('hari')
            ->count('hari');
        // array statistik
        $stats = [
            'total'        => $totalReservasi,
            'hari_ini'     => $antreanHariIniList->count(),
            'rekam_medis'  => $rekamMedis,
            'jadwal_aktif' => $jadwalAktif,
        ];

        // chart mingguan
        $labelsMingguan = [];
        $dataMingguan = [];
        // perulangan selama 7 hari
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i); //ambil tanggal
            // label mingguan
            $labelsMingguan[] = $date->translatedFormat('d M');
            // jumlah reservasi perhari berdasarkan dokter
            $jumlah = Reservasi::whereHas('jadwal', function ($q) use ($dokterId) {
                $q->where('id_dokter', $dokterId);
            }) // filter berdasarkan tanggal
            ->whereDate('tgl_reservasi', $date->format('Y-m-d'))
            ->count();

            $dataMingguan[] = $jumlah;
        } //buat chart mingguan
        $chartMingguan = [
            'labels' => $labelsMingguan,
            'data'   => $dataMingguan,
        ];

        // hitung antrean berdasarkan status chart status
        $menunggu = $antreanHariIniList->where('status', 'menunggu')->count();
        $tindakan = $antreanHariIniList->where('status', 'tindakan')->count();
        $selesai  = $antreanHariIniList->where('status', 'selesai')->count();
        $batal    = $antreanHariIniList->where('status', 'batal')->count();
        // menyusun data chart status
        $chartStatus = [
            'labels' => ['Menunggu', 'Tindakan', 'Selesai', 'Batal'],
            'data'   => [$menunggu, $tindakan, $selesai, $batal]
        ];
        //mengirim ke dashboard blade 
        return view('dokter.dashboard', compact(
            'stats',
            'jadwalHariIniList',
            'antreanHariIniList',
            'chartMingguan',
            'chartStatus'
        ));
    }
}