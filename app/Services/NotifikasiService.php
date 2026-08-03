<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Reservasi;
use App\Models\PerubahanJadwalDokter;

class NotifikasiService
{
    //menentukan dokter yang sedang menangani reservasi tersebut
    private function getDokterAktif(Reservasi $reservasi): array
    {   //ambil dokter dr reservasi
        $reservasi->loadMissing(['jadwal.dokter', 'pasien']);
        $dokterId   = $reservasi->jadwal?->id_dokter;
        $dokterNama = $reservasi->jadwal?->dokter?->nama ?? 'Dokter';
        //cek apakah ada perubahan jadwal
        $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $dokterId)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->where('jenis_perubahan', 'pengganti')
            ->first();
        //jika ada pengganti maka dokter tersebut yg dipakai
        if ($perubahan && $perubahan->dokterPengganti) {
            $dokterId   = $perubahan->dokterPengganti->id_dokter;
            $dokterNama = $perubahan->dokterPengganti->nama;
        }

        return ['id' => $dokterId, 'nama' => $dokterNama];
    }
    //mengirim notifikasi ketika ada reservasi baru
    public function notifReservasiMasuk(Reservasi $reservasi): void
    {
        $dokter = $this->getDokterAktif($reservasi);
        $klinikId   = $reservasi->jadwal?->id_klinik;
        $idDokter   = $dokter['id'];
        $namaDokter = $dokter['nama'];
        $namaPasien = $reservasi->pasien?->nama ?? 'Pasien';
        $tgl        = $reservasi->tgl_reservasi;
        $jam        = $reservasi->jam_reservasi;

        // untuk admin
        $admins = User::role('admin')->where('id_klinik', $klinikId)->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id,
                'judul'   => 'Reservasi Baru Masuk',
                'pesan'   => "{$namaPasien} membuat reservasi dengan Dr. {$namaDokter} pada {$tgl} jam {$jam}",
                'tipe'    => 'success',
                'url'     => route('admin.reservasi.index'),
            ]);
        }

        // untuk dokter
        $this->kirimKeDokter($idDokter, 'Jadwal Pasien Baru', "{$namaPasien} reservasi pada {$tgl} jam {$jam}", route('dokter.jadwal'), 'success');
    }
    //kirim notifikasi apabila ada perubahan jadwal sehingga reservasi terdampak
    public function notifReservasiTerdampak(Reservasi $reservasi): void
    {
        $dokter = $this->getDokterAktif($reservasi);
        $klinikId   = $reservasi->jadwal?->id_klinik;
        $idDokter   = $dokter['id'];
        $namaDokter = $dokter['nama'];
        $namaPasien = $reservasi->pasien?->nama ?? 'Pasien';
        $tgl        = $reservasi->tgl_reservasi;
        $jam        = $reservasi->jam_reservasi;
        //untuk dokter
        $admins = User::role('admin')->where('id_klinik', $klinikId)->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id,
                'judul'   => 'Reservasi Terdampak',
                'pesan'   => "Jadwal Dr. {$namaDokter} berubah. Pasien {$namaPasien} ({$tgl} {$jam}) masuk daftar terdampak.",
                'tipe'    => 'danger',
                'url'     => route('admin.reservasi.index'),
            ]);
        }

        // dokter
        $this->kirimKeDokter($idDokter, 'Jadwal Anda Berubah', "Pasien {$namaPasien} ({$tgl} {$jam}) terdampak perubahan jadwal.", route('dokter.jadwal'), 'danger');
    }
    //mengirim notif apabila reservasi dibatalkan
    public function notifReservasiDibatalkan(Reservasi $reservasi, ?string $alasan = null): void
    {
        $dokter = $this->getDokterAktif($reservasi);
        $klinikId   = $reservasi->jadwal?->id_klinik;
        $idDokter   = $dokter['id'];
        $namaDokter = $dokter['nama'];
        $namaPasien = $reservasi->pasien?->nama ?? 'Pasien';
        $tgl        = $reservasi->tgl_reservasi;
        $jam        = $reservasi->jam_reservasi;
        $keterangan = !empty($alasan) ? " Alasan: {$alasan}." : '';
        //untuk admin
        $admins = User::role('admin')->where('id_klinik', $klinikId)->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id,
                'judul'   => 'Reservasi Dibatalkan',
                'pesan'   => "{$namaPasien} membatalkan reservasi dengan Dr. {$namaDokter} ({$tgl} {$jam}).{$keterangan}",
                'tipe'    => 'warning',
                'url'     => route('admin.reservasi.index'),
            ]);
        }

        //dokter
        $this->kirimKeDokter($idDokter, 'Reservasi Dibatalkan', "{$namaPasien} membatalkan jadwal {$tgl} {$jam}.{$keterangan}", route('dokter.jadwal'), 'warning');
    }
    //notif pasien sudah datang ke klinik
    public function notifPasienHadir(Reservasi $reservasi): void
    {
        $dokter = $this->getDokterAktif($reservasi);
        $this->kirimKeDokter($dokter['id'], 'Pasien Hadir', $reservasi->pasien?->nama . ' telah hadir dan siap ditindak.', route('dokter.jadwal'), 'success');
    }

    // metod helper mengirim notif ke dokter
    private function kirimKeDokter(?int $idDokter, string $judul, string $pesan, string $url, string $tipe = 'reservasi'): void
    {
        if (!$idDokter) return;
        $dokterUser = User::whereHas('dokter', function ($q) use ($idDokter) {
            $q->where('id_dokter', $idDokter);
        })->first();

        if ($dokterUser) {
            Notifikasi::create([
                'id_user' => $dokterUser->id,
                'judul'   => $judul,
                'pesan'   => $pesan,
                'tipe'    => $tipe,
                'url'     => $url,
            ]);
        }
    }
    //notif reservasi terdampak hari loibur
    public function notifReservasiTerdampakHariLibur( int $klinikId, string $tanggal, int $jumlahReservasi ): void
    {
        $admins = User::role('admin')
            ->where('id_klinik', $klinikId)
            ->get();

        foreach ($admins as $admin) {
            Notifikasi::create([
                'id_user' => $admin->id,
                'judul'   => 'Reservasi Terdampak Hari Libur',
                'pesan'   => "{$jumlahReservasi} reservasi terdampak karena hari libur pada {$tanggal}.",
                'tipe'    => 'warning',
                'url'     => route('admin.hari-libur.index'),
            ]);
        }
    }
    //mengambil notifikasi yang belum dibaca
    public function getUnread(int $userId): array
    {
        return Notifikasi::where('id_user', $userId)->where('is_read', false)->latest()->limit(10)->get()->toArray();
    }
    //menghitung notifikasi yg belum dibaca
    public function countUnread(int $userId): int
    {
        return Notifikasi::where('id_user', $userId)->where('is_read', false)->count();
    }
}