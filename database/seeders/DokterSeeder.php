<?php

namespace Database\Seeders;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DokterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dokterData = [
            [
                'nama' => 'Dr. Ahmad Santoso',
                'spesialisasi' => 'Gigi Umum',
                'no_hp' => '081234567890',
                'email' => 'ahmad@klinikgigi.com',
            ],
            [
                'nama' => 'Dr. Siti Nurhaliza',
                'spesialisasi' => 'Orthodonti',
                'no_hp' => '081234567891',
                'email' => 'siti@klinikgigi.com',
            ],
            [
                'nama' => 'Dr. Budi Prasetyo',
                'spesialisasi' => 'Bedah Mulut',
                'no_hp' => '081234567892',
                'email' => 'budi@klinikgigi.com',
            ],
            [
                'nama' => 'Dr. Maya Sari',
                'spesialisasi' => 'Prosthodonti',
                'no_hp' => '081234567893',
                'email' => 'maya@klinikgigi.com',
            ],
            [
                'nama' => 'Dr. Rudi Hartono',
                'spesialisasi' => 'Gigi Umum',
                'no_hp' => '081234567894',
                'email' => 'rudi@klinikgigi.com',
            ],
        ];

        foreach ($dokterData as $data) {
            // Buat user untuk dokter
            $user = User::create([
                'name' => $data['nama'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'dokter',
            ]);

            // Buat dokter
            Dokter::create([
                'nama' => $data['nama'],
                'spesialisasi' => $data['spesialisasi'],
                'no_hp' => $data['no_hp'],
                'user_id' => $user->id,
            ]);
        }

        $this->command->info('Dokter seeder completed.');
    }
}
