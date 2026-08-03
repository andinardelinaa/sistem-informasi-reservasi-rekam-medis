<?php

namespace App\Services;

use App\Models\Jadwal;
use App\Models\Reservasi;
use Carbon\Carbon;

class AntreanService
{
    //membuat slot antrean per 1 jam
    public static function generateSlots(string $jamMulai, string $jamSelesai, string $tanggal, int $idJadwal): array
    {
        $slots = [];
        $antrean = 1;

        $start = Carbon::parse($jamMulai);
        $end = Carbon::parse($jamSelesai);

        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addHour();

            // Lewati istirahat 12.00-13.00
            if ($start->format('H:i') === '12:00') {
                $start = $start->copy()->addHour();
                continue;
            }

            // Pastikan slot tidak melebihi jam selesai
            if ($slotEnd->gt($end)) {
                break;
            }

            // Cek apakah slot sudah direservasi
            $isBooked = Reservasi::where('id_jadwal', $idJadwal)
                ->where('tgl_reservasi', $tanggal)
                ->where('jam_reservasi', $start->format('H:i:s'))
                ->whereIn('status', ['menunggu', 'dikonfirmasi', 'diproses'])
                ->exists();

            $slots[] = [
                'antrean' => $antrean,
                'jam_mulai' => $start->format('H:i'),
                'jam_selesai' => $slotEnd->format('H:i'),
                'tersedia' => !$isBooked,
                'terisi' => $isBooked,
            ];

            $antrean++;
            $start = $slotEnd;
        }

        return $slots;
    }

    //ambil semua jadwal dokter yang aktif dan menambaahkan slot antrean
    public static function getJadwalWithSlots(?int $idKlinik, string $tanggal): array
    {   //ambil jadwal
        $query = Jadwal::with(['dokter', 'klinik'])
            ->where('is_aktif', 1);

        if ($idKlinik) {
            $query->where('id_klinik', $idKlinik);
        }
        //urutkan berdasarkan hari
        $jadwals = $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();
        $result = [];

        foreach ($jadwals as $jadwal) {
            $slots = self::generateSlots( //menghasilkan slot antrean
                $jadwal->jam_mulai->format('H:i'),
                $jadwal->jam_selesai->format('H:i'),
                $tanggal,
                $jadwal->id_jadwal
            );

            // Hanya tampilkan jadwal yang masih punya slot kosong
            $slotKosong = collect($slots)->where('tersedia', true)->count();

            $result[] = [
                'id_jadwal' => $jadwal->id_jadwal,
                'id_dokter' => $jadwal->id_dokter,
                'id_klinik' => $jadwal->id_klinik,
                'hari' => $jadwal->hari,
                'dokter_nama' => $jadwal->dokter->nama ?? '-',
                'dokter_spesialisasi' => $jadwal->dokter->spesialisasi ?? '-',
                'klinik_nama' => $jadwal->klinik->nama_klinik ?? '-',
                'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
                'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
                'slots' => $slots,
                'slot_kosong' => $slotKosong,
                'slot_terisi' => count($slots) - $slotKosong,
            ];
        }

        return $result;
    }

    //mengambil slot untuk satu jadwal dokter tertentu
    public static function getSlotsByJadwal(int $idJadwal, string $tanggal): ?array
    {   //cari jadwal
        $jadwal = Jadwal::with(['dokter', 'klinik'])
            ->where('id_jadwal', $idJadwal)
            ->where('is_aktif', 1)
            ->first();

        if (!$jadwal) {
            return null;
        }

        $slots = self::generateSlots( //panggil untuk membuat slot
            $jadwal->jam_mulai->format('H:i'),
            $jadwal->jam_selesai->format('H:i'),
            $tanggal,
            $jadwal->id_jadwal
        );

        return [
            'id_jadwal' => $jadwal->id_jadwal,
            'dokter_nama' => $jadwal->dokter->nama ?? '-',
            'dokter_spesialisasi' => $jadwal->dokter->spesialisasi ?? '-',
            'klinik_nama' => $jadwal->klinik->nama_klinik ?? '-',
            'jam_mulai' => $jadwal->jam_mulai->format('H:i'),
            'jam_selesai' => $jadwal->jam_selesai->format('H:i'),
            'slots' => $slots,
        ];
    }
}