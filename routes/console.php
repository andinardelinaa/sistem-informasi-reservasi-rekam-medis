<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

use App\Models\Jadwal;
use App\Models\Reservasi;
use App\Services\ReservasiNotificationService;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// fungsi schedule jadwal bulan otomatis setiap tanggal 1 jam 00:01
Schedule::call(function () {

    Log::info('Scheduler jadwal bulanan berjalan');
    // ambil bulan dan tahun sekarang
    $bulanIni = now()->month;
    $tahunIni = now()->year;

    // ambil bulan dan tahun bulan lalu
    $bulanLalu = now()->subMonth()->month;
    $tahunLalu = now()->subMonth()->year;

    // Hapus jadwal bulan lalu
    Jadwal::where('bulan', $bulanLalu)
        ->where('tahun', $tahunLalu)
        ->delete();

    // Aktifkan jadwal bulan ini
    Jadwal::where('bulan', $bulanIni)
        ->where('tahun', $tahunIni)
        ->where('is_aktif', false)
        ->update([
            'is_aktif' => true
        ]);

    // setiap tanggal 1 jam 00:01, hapus jadwal bulan lalu dan aktifkan jadwal bulan ini
})->monthlyOn(1, '00:00');


// reminder pagi untuk pasien yang reservasi hari ini
Schedule::call(function () {

    Log::info('Scheduler reminder pagi berjalan');

    try {
        // gunakan service untuk kirim notifikasi
        $service = app(ReservasiNotificationService::class);
        // ambil tanggal hari ini
        $today = now()->toDateString();

        $reservasis = Reservasi::with([
                'pasien.user',
                'jadwal.klinik',
                'jadwal.dokter'
            ])
            ->where('tgl_reservasi', $today)
            ->where('status', 'menunggu') //hanya untuk status menunggu
            ->whereNull('reminder_sent_at')
            ->get();

        Log::info('Jumlah reservasi ditemukan: ' . $reservasis->count());

        foreach ($reservasis as $reservasi) {

            Log::info(
                'Kirim reminder reservasi ID: '
                . $reservasi->id_reservasi
            );
            // kirim reminder, jika berhasil update kolom reminder_sent_at
            $result = $service->kirimReminder($reservasi);
            if ($result) {
                $reservasi->update([
                    'reminder_sent_at' => now()
                ]);
                Log::info('Reminder berhasil dikirim');
            } else {
                Log::error('Reminder gagal dikirim');
            }
        }
        Log::info('Reminder selesai');
    } catch (\Exception $e) {

        Log::error(
            'Scheduler reminder error: '
            . $e->getMessage()
        );
    }
// setiap hari jam 08.00, kirim reminder untuk pasien yang reservasi hari ini dan statusnya menunggu
})->dailyAt('08:00');

// auto-cancel untuk pasien yang tidak datang setiap 5 menit
Schedule::command('app:auto-cancel-reservasi')
    ->everyFiveMinutes();