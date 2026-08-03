<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Pasien;
use App\Models\Dokter;
use App\Models\Klinik;
use App\Models\Jadwal;
use App\Models\RekamMedis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // cari id klinik
        $klinikId = (int) ($request->klinik_id ?? 0);
        //tampilkan list
        $klinikList = Klinik::orderBy('nama_klinik')->get();

        $klinikTerpilih = $klinikId ? Klinik::find($klinikId) : null;

        $baseReservasi = Reservasi::query();
        //jika klinik lebih dari 1 yang memiliki jadwal, dimana id klinik yg dicari adalah id tersebut
        if ($klinikId > 0) {
            $baseReservasi->whereHas('jadwal', function ($q) use ($klinikId) {
                $q->where('id_klinik', $klinikId);
            });
        }

        // statistik
        $stats = [
            'total_reservasi' => (clone $baseReservasi)->count(),

            'reservasi_hari_ini' => (clone $baseReservasi)
                ->whereDate('tgl_reservasi', today())
                ->count(),

            'total_pasien' => $klinikId > 0
                ? Pasien::whereHas('reservasi.jadwal', function ($q) use ($klinikId) {
                    $q->where('id_klinik', $klinikId);
                })->distinct()->count() 
                : Pasien::count(),

            'dokter_aktif' => $klinikId > 0
                ? Jadwal::where('id_klinik', $klinikId)->distinct()->count('id_dokter')
                : Dokter::count(),

            'rekam_medis_bulan_ini' => RekamMedis::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->when($klinikId > 0, function ($q) use ($klinikId) {
                    $q->whereHas('reservasi.jadwal', function ($q2) use ($klinikId) {
                        $q2->where('id_klinik', $klinikId);
                    });
                })
                ->count(),
        ];

        // chart reservasi 
        $labelsReservasi = [];
        $dataReservasi = [];
        // loop 7 hari 
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);

            $labelsReservasi[] = $tanggal->translatedFormat('D');

            $dataReservasi[] = (clone $baseReservasi)
                ->whereDate('tgl_reservasi', $tanggal->format('Y-m-d'))
                ->count();
        }

        $chartReservasi = [
            'labels' => $labelsReservasi,
            'data' => $dataReservasi,
        ];

        // status reservasi
        $statusList = ['menunggu', 'tindakan', 'selesai', 'dibatalkan', 'terdampak'];
        $statusData = [];
        // loop status reservasi pasien
        foreach ($statusList as $status) {
            $statusData[] = (clone $baseReservasi)
                ->where('status', $status)
                ->count();
        }

        $chartStatus = [
            'labels' => ['Menunggu', 'Tindakan', 'Selesai', 'Dibatalkan', 'Terdampak'],
            'data' => $statusData,
        ];

        // dokter teraktif
        $dokterTeraktif = Reservasi::join('jadwal', 'reservasi.id_jadwal', '=', 'jadwal.id_jadwal')
            ->join('dokter', 'jadwal.id_dokter', '=', 'dokter.id_dokter')
            ->select('dokter.nama', DB::raw('COUNT(reservasi.id_reservasi) as total_pasien'))
            ->groupBy('dokter.id_dokter', 'dokter.nama')
            ->when($klinikId > 0, function ($q) use ($klinikId) {
                $q->where('jadwal.id_klinik', $klinikId);
            })
            ->orderByDesc('total_pasien')
            ->take(5)
            ->get();

        $chartDokterAktif = [
            'labels' => $dokterTeraktif->pluck('nama'),
            'data' => $dokterTeraktif->pluck('total_pasien'),
        ];

        // rekam medis perbulan
        $bulanLabels = [];
        $bulanData = [];
        // loop rekam medis dalam setahun
        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $bulanLabels[] = Carbon::create()->month($bulan)->translatedFormat('M');
            $bulanData[] = RekamMedis::whereMonth('created_at', $bulan)
                ->whereYear('created_at', now()->year)
                ->when($klinikId > 0, function ($q) use ($klinikId) {
                    $q->whereHas('reservasi.jadwal', function ($q2) use ($klinikId) {
                        $q2->where('id_klinik', $klinikId);
                    });
                })
                ->count();
        }

        $chartRekamMedis = [
            'labels' => $bulanLabels,
            'data' => $bulanData,
        ];

        // reservasi terbaru
        $reservasiTerbaru = (clone $baseReservasi)
            ->with(['pasien', 'jadwal.dokter'])
            ->latest()
            ->take(5)
            ->get();

        // tambahan statistik
        $stats['rata_rata_reservasi'] = round(array_sum($dataReservasi) / 7, 2);

        $hariMax = array_keys($dataReservasi, max($dataReservasi))[0];
        $stats['hari_terpadat'] = $labelsReservasi[$hariMax] ?? '-';

        $stats['total_rm_tahunan'] = array_sum($bulanData);
        $stats['rata_rata_rm'] = round(array_sum($bulanData) / 12);

        // tampil
        return view('admin.dashboard', compact(
            'stats',
            'chartReservasi',
            'chartStatus',
            'chartDokterAktif',
            'chartRekamMedis',
            'reservasiTerbaru',
            'klinikList',
            'klinikTerpilih'
        ));
    }
}