<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Jadwal;
use App\Models\Reservasi;
use Illuminate\Support\Facades\Mail;
use App\Services\ReservasiNotificationService;

class Kernel extends ConsoleKernel
{  
    protected function schedule(Schedule $schedule): void
    {
        // ==========================================
        // 1. AKTIVASI JADWAL BULANAN (Tanggal 1, Jam 00:00)
        // ==========================================
        $schedule->call(function () {
            $bulanIni = now()->month;
            $tahunIni = now()->year;
            $bulanDepan = now()->addMonth()->month;
            $tahunDepan = now()->addMonth()->year;

            Jadwal::where('bulan', $bulanIni)
                ->where('tahun', $tahunIni)
                ->whereNull('deleted_at')
                ->delete();

            Jadwal::where('bulan', $bulanDepan)
                ->where('tahun', $tahunDepan)
                ->where('is_aktif', false)
                ->update(['is_aktif' => true]);

            \Log::info('Jadwal bulanan: Soft delete bulan ini, aktifkan bulan depan');

        })->monthlyOn(1, '00:00');

        // ==========================================
        // 2. REMINDER EMAIL (Jam 08:00 pagi)
        // ==========================================
       $schedule->call(function () {
            $service = app(ReservasiNotificationService::class);
            $today = now()->toDateString();
            $now = now();

            $reservasis = Reservasi::with(['pasien.user', 'jadwal.klinik', 'jadwal.dokter'])
                ->where('tgl_reservasi', $today)
                ->where('status', ['menunggu', 'terdampak'])
                ->where('jam_reservasi', '>=', '08:00:00')
                ->whereNull('reminder_sent_at') // Kirim sekali saja
                ->get();

            $sentCount = 0;

            foreach ($reservasis as $reservasi) {
                // Skip kalau sudah expired (auto-cancel akan handle)
                $jadwalTime = \Carbon\Carbon::parse($reservasi->tgl_reservasi . ' ' . $reservasi->jam_reservasi);
                if ($now->greaterThan($jadwalTime->copy()->addMinutes(30))) {
                    continue;
                }

                if ($service->kirimReminder($reservasi)) {
                    $sentCount++;
                }
            }

            \Log::info("Reminder selesai: {$sentCount} email terkirim");

        })->dailyAt('08:00');

        $schedule->command('app:auto-cancel-reservasi')->everyFiveMinutes();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}