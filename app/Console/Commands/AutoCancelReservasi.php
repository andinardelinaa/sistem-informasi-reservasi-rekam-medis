<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservasi;
use App\Services\ReservasiNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoCancelReservasi extends Command
{
    // command signature untuk menjalankan scheduler
    protected $signature = 'app:auto-cancel-reservasi';
    protected $description =
        'Auto-cancel reservasi menunggu/terdampak yang lewat 30 menit dari jam kunjungan';

    public function __construct(
        protected ReservasiNotificationService $notifService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        // ambil waktu sekarang dan tanggal hari ini
        $now = now();
        $today = $now->toDateString();

        $this->info("[" . $now . "] Memulai auto-cancel...");

        Log::info('Scheduler auto cancel berjalan');
        // ambil reservasi yang statusnya menunggu atau terdampak, dan tgl_reservasi 
        $reservasis = Reservasi::with([
                'pasien.user',
                'jadwal.dokter',
                'jadwal.klinik'
            ])
            ->whereIn('status', ['menunggu', 'terdampak'])  //status menunggu atau terdampak
            ->whereNotNull('tgl_reservasi')
            ->whereNotNull('jam_reservasi')
            ->where('jam_reservasi', '!=', 'undefined')
            ->whereDate('tgl_reservasi', '<=', $today) //hari ini
            ->get();

        $updatedCount = 0;

        foreach ($reservasis as $reservasi) {

            try {
                $jamMulai = explode('-', $reservasi->jam_reservasi)[0]; //ambil jam mulai dari format 

                $jadwalTime = Carbon::parse(
                    $reservasi->tgl_reservasi . ' ' . $jamMulai
                );

                // Toleransi 30 menit setelah jam kunjungan
                $batasWaktu = $jadwalTime->copy()->addMinutes(30);

                if ($now->greaterThan($batasWaktu)) { //cek waktu sekarang sudah lewat batas waktu

                    $statusLama = $reservasi->status;

                    $reservasi->update([ //update status jadi dibatalkan
                        'status' => 'dibatalkan',
                        'cancelled_at' => now(),
                    ]);

                    // Notifikasi beda untuk terdampak vs menunggu
                    $alasan = $reservasi->status === 'terdampak'
                        ? 'Reservasi terdampak tidak ditindaklanjuti dalam 30 menit (auto-cancel sistem)'
                        : 'Pasien tidak hadir (auto-cancel sistem)';

                    // Kirim email notifikasi
                    $this->notifService->kirimAutoCancel(
                        $reservasi,
                        $alasan
                    );

                    $updatedCount++;

                    $message =
                        "✓ Reservasi ID {$reservasi->id_reservasi} [{$reservasi->status}] dibatalkan otomatis";

                    $this->info($message);

                    Log::info($message);
                }

            } catch (\Exception $e) {

                $error =
                    "✗ Error reservasi ID {$reservasi->id_reservasi}: "
                    . $e->getMessage();

                $this->error($error);

                Log::error($error);
            }
        }

        $this->info(
            "Selesai: {$updatedCount} reservasi dibatalkan."
        );

        Log::info(
            "Auto cancel selesai. Total: {$updatedCount}"
        );

        return Command::SUCCESS;
    }
}