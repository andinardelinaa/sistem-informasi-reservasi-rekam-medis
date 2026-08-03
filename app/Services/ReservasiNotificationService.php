<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\Reservasi;
use App\Models\Jadwal;
use App\Mail\TerdampakReservasiMail;
use App\Mail\ReservasiDisetujuiMail;
use App\Mail\ReservasiRescheduleMail;
use App\Mail\ReservasiDibatalkanMail;
use App\Mail\AutoCancelMail;

class ReservasiNotificationService
{   //mengirim reminder
     public function kirimReminder(Reservasi $reservasi): bool
    {   //ambil data dan relasi
        $reservasi->load(['pasien.user', 'jadwal.klinik', 'jadwal.dokter']);
        
        $pasien = $reservasi->pasien;
        $user = $pasien?->user; //pastikan pasien punya email

        if (!$user || !$user->email) {
            \Log::warning("Reminder gagal: Pasien ID {$pasien?->id_pasien} tidak punya email");
            return false;
        }
        //mengecek perubahan jadwal
        $perubahan = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $reservasi->jadwal->id_dokter)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->first();

        $dokter = $reservasi->jadwal->dokter; // pake dokter asli

        // Kalau ada pengganti, override jadi dokter pengganti
        if ($perubahan && $perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
            $dokter = $perubahan->dokterPengganti;
        }
    
        try {
            Mail::send('emails.reminder', [ //kirim email  reminder
                'reservasi' => $reservasi,
                'pasien' => $pasien,
                'klinik' => $reservasi->jadwal->klinik,
                'dokter' => $dokter,  
            ], function ($message) use ($user, $reservasi) {
                $message->to($user->email, $user->name)
                    ->subject('🔔 Reminder: Reservasi Gigi Hari Ini - ' . now()->format('d F Y'));
            });
            //update kolom reminder_sent_at
            $reservasi->update(['reminder_sent_at' => now()]);
            
            \Log::info("Reminder terkirim ke {$user->email} untuk reservasi ID {$reservasi->id_reservasi}");
            return true;

        } catch (\Exception $e) {
            \Log::error("Gagal kirim reminder: " . $e->getMessage());
            return false;
        }
    }
    //kirim auto cancel
    public function kirimAutoCancel(Reservasi $reservasi, string $alasan): void
    {   
        try { //ambil data pasien dan user
            $pasien = $reservasi->pasien;
            $user   = $pasien?->user;

            if (!$user?->email) {
                \Log::warning("Skip email auto-cancel: pasien {$pasien?->id_pasien} tidak punya email");
                return;
            }
            //ambil data klinik
            $klinik = $reservasi->jadwal?->klinik;
            //ambil data perubahan
            $perubahan = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
                ->where('dokter_id', $reservasi->jadwal->id_dokter)
                ->whereDate('tanggal', $reservasi->tgl_reservasi)
                ->where('status', 'aktif')
                ->first();

            $dokter = $reservasi->jadwal?->dokter; //dokter asli dari jadwal

            // Kalau ada pengganti, override jadi dokter pengganti
            if ($perubahan && $perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
                $dokter = $perubahan->dokterPengganti;
            }
            //buat data
            $data = [
                'detail_reservasi' => [
                    'pasien'       => $pasien->nama,
                    'tanggal'      => \Carbon\Carbon::parse($reservasi->tgl_reservasi)->format('d F Y'),
                    'jam'          => $reservasi->jam_reservasi,
                    'status_akhir' => 'Dibatalkan Otomatis',
                    'alasan'       => $alasan,
                ],
                'klinik' => $klinik,
                'dokter' => $dokter, 
            ];
            //kirim email auto cancel
            Mail::to($user->email)->send(new AutoCancelMail($data));

            \Log::info("Email auto-cancel terkirim ke {$user->email}");

        } catch (\Exception $e) {
            \Log::error("Gagal kirim auto-cancel email: " . $e->getMessage());
        }
    }

    //kirim email terdampak
    public function kirimTerdampak(Reservasi $reservasi, $dokterLama, $dokterPengganti)
    {
        $user = $reservasi->pasien->user ?? null; //pastikan pasien memiliki email

        if (!$user || !$user->email) {
            return false;
        }
        //kirim email terdampak
        Mail::to($user->email)->send(new TerdampakReservasiMail([
            'judul' => 'Pemberitahuan Perubahan Jadwal Dokter',
            'pesan' => 'Jadwal dokter Anda mengalami perubahan. Tim kami akan segera menghubungi Anda untuk konfirmasi lebih lanjut.',
            'detail_reservasi' => [
                'pasien'           => $reservasi->pasien->nama ?? '-',
                'tanggal'          => $reservasi->tgl_reservasi,
                'jam'              => $reservasi->jam_reservasi,
                'dokter_lama'      => $dokterLama,
                'dokter_pengganti' => $dokterPengganti,
            ],
        ]));

        return true;
    }

   //kirim email konfirmasi lanjut
    public function kirimKonfirmasiLanjut(Reservasi $reservasi, $dokterPengganti)
    {   //pasien mempunyai user dan email
        $user = $reservasi->pasien->user ?? null;

        if (!$user || !$user->email) {
            return false;
        }
        // kirim email konfrimasi lanjut jadwal
        Mail::to($user->email)->send(new ReservasiDisetujuiMail([
            'judul' => 'Konfirmasi: Reservasi Dilanjutkan',
            'pesan' => 'Setelah konfirmasi via telepon, reservasi Anda akan dilanjutkan dengan dokter pengganti.',
            'detail_reservasi' => [
                'pasien'           => $reservasi->pasien->nama ?? '-',
                'tanggal'          => $reservasi->tgl_reservasi,
                'jam'              => $reservasi->jam_reservasi,
                'dokter_pengganti' => $dokterPengganti,
                'status_akhir'     => 'Dilanjutkan dengan dokter pengganti',
            ],
        ]));

        return true;
    }

    //kirim email reschedule
    public function kirimKonfirmasiReschedule(Reservasi $reservasi, string $dokterLama = null, string $spesialisasiLama = null, string $dokterBaru = null, string $spesialisasiBaru = null, string $alasan = null): bool {
        //ambil data reservasi
        $reservasi = Reservasi::with(['pasien.user'])
            ->find($reservasi->id_reservasi);

        if (!$reservasi) {
            \Log::error("Email reschedule gagal: Reservasi tidak ditemukan");
            return false;
        }
        //mengecek email pasien
        $user = $reservasi->pasien->user ?? null;
        if (!$user || !$user->email) {
            \Log::warning("Email reschedule gagal: Pasien ID {$reservasi->id_pasien} tidak punya email");
            return false;
        }
        //mengambil jadwal baru
        $jadwalBaru = \App\Models\Jadwal::with(['dokter', 'klinik'])
            ->find($reservasi->id_jadwal);

        if (!$jadwalBaru) {
            \Log::error("Email reschedule gagal: Jadwal ID {$reservasi->id_jadwal} tidak ditemukan");
            return false;
        }

        // Cek perubahan jadwal (pengganti) untuk data baru
        $perubahanBaru = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $jadwalBaru->id_dokter)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->first();

        // pake dokter baru kalo ada pengganti
        if (empty($dokterBaru)) {
            $dokterBaru = $jadwalBaru->dokter->nama ?? '-';
        } //spesialisasi dokter baru
        if (empty($spesialisasiBaru)) {
            $spesialisasiBaru = $jadwalBaru->dokter->spesialisasi ?? '-';
        }

        if ($perubahanBaru && $perubahanBaru->jenis_perubahan == 'pengganti' && $perubahanBaru->dokterPengganti) {
            $dokterBaru = $perubahanBaru->dokterPengganti->nama;
            $spesialisasiBaru = $perubahanBaru->dokterPengganti->spesialisasi;
        }

        \Log::info('=== EMAIL RESCHEDULE DATA ===', [
            'id_reservasi' => $reservasi->id_reservasi,
            'id_jadwal' => $reservasi->id_jadwal,
            'dokter_lama' => $dokterLama,
            'dokter_baru_final' => $dokterBaru,
            'klinik' => $jadwalBaru->klinik->nama_klinik ?? 'NULL',
        ]);

        try {
            Mail::to($user->email)->send(
                new ReservasiRescheduleMail([ // kirim reservasi reschedule
                    'judul' => 'Jadwal Reservasi Anda Telah Diubah',
                    'pesan' => 'Jadwal reservasi Anda telah diperbarui. Berikut detail jadwal baru Anda:',
                    'detail_reservasi' => [
                        'pasien'            => $reservasi->pasien->nama ?? '-',
                        'tanggal_baru'      => \Carbon\Carbon::parse($reservasi->tgl_reservasi)->format('d F Y'),
                        'jam_baru'          => $reservasi->jam_reservasi,
                        'dokter_baru'       => $dokterBaru,
                        'spesialisasi'      => $spesialisasiBaru,
                        
                        'klinik'            => $jadwalBaru->klinik->nama_klinik ?? '-',
                        'status'            => 'Reservasi aktif kembali',
                        'dokter_lama'       => $dokterLama,
                        'spesialisasi_lama' => $spesialisasiLama,
                        'alasan'            => $alasan ?? 'Perubahan jadwal dokter',
                    ],
                ])
            );

            \Log::info("Email reschedule terkirim ke {$user->email}");
            return true;

        } catch (\Exception $e) {
            \Log::error("Gagal kirim email reschedule: " . $e->getMessage());
            return false;
        }
    }

    //kirim email konfirmasi batal
    public function kirimKonfirmasiBatalTerdampak(Reservasi $reservasi)
    {
        $user = $reservasi->pasien->user ?? null; //ambil data user pasien

        if (!$user || !$user->email) { //pastikan punya email
            return false;
        }
        //kirim email batal terdampak
        Mail::to($user->email)->send(new ReservasiDibatalkanMail([
            'judul' => 'Konfirmasi: Reservasi Dibatalkan',
            'pesan' => 'Reservasi Anda telah dibatalkan karena perubahan jadwal dokter. Silakan buat reservasi baru.',
            'detail_reservasi' => [
                'pasien'       => $reservasi->pasien->nama ?? '-',
                'tanggal'      => $reservasi->tgl_reservasi,
                'jam'          => $reservasi->jam_reservasi,
                'status_akhir' => 'Dibatalkan',
            ],
        ]));

        return true;
    }

    //kirim reservasi dibatalkan
    // public function kirimPembatalan(Reservasi $reservasi, $alasan = null)
    // {   //ambil data pasien dan user pastikan pasien memiliki email
    //     $user = $reservasi->pasien->user ?? null;
    //     if (!$user || !$user->email) return false;
    //     // kirim email dibataklan
    //     Mail::to($user->email)->send(new ReservasiDibatalkanMail([
    //         'judul'  => 'Reservasi Anda Dibatalkan',
    //         'pesan'  => 'Reservasi Anda pada tanggal ' . $reservasi->tgl_reservasi . ' telah dibatalkan.',
    //         'detail_reservasi' => [
    //             'pasien'  => $reservasi->pasien->nama ?? '-',
    //             'tanggal' => $reservasi->tgl_reservasi,
    //             'jam'     => $reservasi->jam_reservasi,
    //             'status_akhir' => 'Dibatalkan',
    //         ],
    //         'alasan' => $alasan
    //     ]));

    //     return true;
    // }
    //mengirim email update status
    // public function kirimUpdateStatus(Reservasi $reservasi, $status)
    // {
    //     $user = $reservasi->pasien->user ?? null;
    //     if (!$user || !$user->email) return false;

    //     $mailClass = match($status) {
    //         'dibatalkan' => ReservasiDibatalkanMail::class, //jika status dibatalkan kirim email dibatalkan
    //         'selesai'    => ReservasiDisetujuiMail::class, //jika status selesai kirim email disetujui
    //         default      => ReservasiDisetujuiMail::class,
    //     };

    //     Mail::to($user->email)->send(new $mailClass([
    //         'judul' => 'Update Status Reservasi',
    //         'pesan' => 'Status reservasi Anda berubah menjadi: ' . $status,
    //         'detail_reservasi' => [
    //             'pasien'       => $reservasi->pasien->nama ?? '-',
    //             'tanggal'      => $reservasi->tgl_reservasi,
    //             'jam'          => $reservasi->jam_reservasi,
    //             'status_akhir' => ucfirst($status),
    //         ],
    //     ]));

    //     return true;
    // }
}