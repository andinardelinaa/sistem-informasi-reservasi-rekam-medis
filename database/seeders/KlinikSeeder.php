<?php

namespace Database\Seeders;

use App\Models\Klinik;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KlinikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $klinikData = [
            [
                'nama_klinik' => 'Klinik Gigi Pusat',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                'maps_url' => 'https://maps.google.com/?q=Jl.+Sudirman+No.+123,+Jakarta',
            ],
            [
                'nama_klinik' => 'Klinik Gigi Cabang A',
                'alamat' => 'Jl. Thamrin No. 456, Jakarta',
                'maps_url' => 'https://maps.google.com/?q=Jl.+Thamrin+No.+456,+Jakarta',
            ],
            [
                'nama_klinik' => 'Klinik Gigi Cabang B',
                'alamat' => 'Jl. Gatot Subroto No. 789, Jakarta',
                'maps_url' => 'https://maps.google.com/?q=Jl.+Gatot+Subroto+No.+789,+Jakarta',
            ],
        ];

        foreach ($klinikData as $data) {
            Klinik::create($data);
        }

        $this->command->info('Klinik seeder completed.');
    }
}
