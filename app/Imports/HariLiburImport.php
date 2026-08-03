<?php

namespace App\Imports;

use App\Models\HariLibur;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class HariLiburImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row) //dipanggil untuk setiap baris excel
    {   //cek duplikasi data
        $existing = HariLibur::where('tanggal', $row['tanggal'])->first();
        
        if ($existing) {
            return null;
        } //jika sudah data tidak disimpan
        //jika belum. simpan hari libur baru
        return new HariLibur([
            'tanggal'     => $row['tanggal'],
            'keterangan'  => $row['keterangan'],
        ]);
    }
    //validasi
    public function rules(): array
    {
        return [
            'tanggal'    => 'required|date',
            'keterangan' => 'required|string|max:255',
        ];
    }
}