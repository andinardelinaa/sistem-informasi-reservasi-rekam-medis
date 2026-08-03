<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Klinik;
use App\Models\Dokter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua klinik dan dokter yang ada
        $klinik = Klinik::all();
        $dokter = Dokter::all();

        if ($klinik->isEmpty() || $dokter->isEmpty()) {
            $this->command->info('Tidak ada data klinik atau dokter. Jalankan KlinikSeeder dan DokterSeeder terlebih dahulu.');
            return;
        }

        $hari = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
        $jamMulai = ['08:00', '09:00', '10:00', '14:00', '15:00'];
        $jamSelesai = ['12:00', '13:00', '16:00', '18:00', '19:00'];

        // Buat jadwal untuk setiap kombinasi klinik-dokter-hari
        foreach ($klinik as $k) {
            foreach ($dokter as $d) {
                // Pilih hari acak (misal 3-5 hari per dokter per klinik)
                $hariDipilih = collect($hari)->random(rand(3, 5));

                foreach ($hariDipilih as $h) {
                    // Cek apakah sudah ada
                    $exists = Jadwal::where('id_klinik', $k->id_klinik)
                                    ->where('id_dokter', $d->id_dokter)
                                    ->where('hari', $h)
                                    ->exists();

                    if (!$exists) {
                        $jamMulaiRand = collect($jamMulai)->random();
                        $jamSelesaiRand = collect($jamSelesai)->random();

                        // Pastikan jam selesai > jam mulai
                        if (strtotime($jamSelesaiRand) > strtotime($jamMulaiRand)) {
                            Jadwal::create([
                                'id_klinik' => $k->id_klinik,
                                'id_dokter' => $d->id_dokter,
                                'hari' => $h,
                                'jam_mulai' => $jamMulaiRand,
                                'jam_selesai' => $jamSelesaiRand,
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('Jadwal seeder completed.');
    }
}
