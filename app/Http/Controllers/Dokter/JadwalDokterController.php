<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Dokter;
use App\Models\Klinik;
use Illuminate\Support\Facades\Auth;
use App\Models\PerubahanJadwalDokter;
Use Carbon\Carbon;

class JadwalDokterController extends Controller
{
    // menampilkan jadwal aktif
    public function index()
    {   //ambil dokter yg login
        $user = Auth::user();
        $dokterLogin = $user->dokter;
        //ambil jadwal dan relasi dengan klinik untuk menampilkan jadwal aktif
        $jadwal = Jadwal::with(['dokter', 'klinik'])
            ->where('is_aktif', 1)
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        $perubahanJadwal = collect();
        //jika dokter login memiliki perubahan jadwal, sebagai dokter atau dokter pengganti
        if ($dokterLogin) {
            $perubahanJadwal = PerubahanJadwalDokter::with([ //mengambil data perbahan jadwal 
                'dokter',
                'dokterPengganti'
            ])
            ->where(function ($q) use ($dokterLogin) {
                $q->where('dokter_id', $dokterLogin->id_dokter)
                ->orWhere('dokter_pengganti_id', $dokterLogin->id_dokter);
            }) //dokter yg diganti maupun dokter pengganti
            ->where('jenis_perubahan', 'pengganti')
            ->where('status', 'aktif') //dengan status aktif atau yg akan datang
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->get();
            //loop perubahan jadwal
            foreach ($perubahanJadwal as $item) {

                $hari = Carbon::parse($item->tanggal)
                    ->locale('id')
                    ->translatedFormat('l');
                // cari jadwal asli
                $jadwalAsli = Jadwal::where('id_dokter', $item->dokter_id)
                    ->where('hari', $hari)
                    ->where('is_aktif', 1) //aktif
                    ->first();
                //menampilkan jam praktik
                $item->jam_praktik = $jadwalAsli
                    ? $jadwalAsli->jam_mulai->format('H:i') . ' - ' .
                    $jadwalAsli->jam_selesai->format('H:i')
                    : 'Jadwal tidak ditemukan';
            }
        }
        return view('dokter.jadwal', compact(
            'jadwal',
            'perubahanJadwal'
        ));
    }

    // menampilkan jadwal milik dokter yg sedang login
    public function mySchedule()
    {
        $user = Auth::user();
        $dokter = $user->dokter;

        if (!$dokter) {
            return response()->json([
                'success' => false,
                'message' => 'Data dokter tidak ditemukan'
            ], 404);
        }

        $jadwal = Jadwal::with('klinik')
            ->where('id_dokter', $dokter->id_dokter)
            ->where('is_aktif', 1)  //jadwal aktif
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id_jadwal,
                    'hari' => $item->hari,
                    'jam_mulai' => $item->jam_mulai->format('H:i'),
                    'jam_selesai' => $item->jam_selesai->format('H:i'),
                    'klinik' => $item->klinik->nama_klinik ?? '-',
                    'is_aktif' => $item->is_aktif
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }

    // semua jadwal aktif
    public function allSchedules()
    {   //ambil data jadwal dengan relasi dokter dan klinik
        $jadwal = Jadwal::with(['dokter', 'klinik'])
            ->where('is_aktif', 1)  //jadwal aktif
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get()
            ->groupBy(function ($item) {
                return $item->klinik->nama_klinik ?? 'Klinik Tidak Diketahui';
            });

        return response()->json([
            'success' => true,
            'data' => $jadwal
        ]);
    }
}