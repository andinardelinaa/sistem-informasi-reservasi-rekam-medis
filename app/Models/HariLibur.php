<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'hari_libur';
    protected $primaryKey = 'id_hari_libur';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'tanggal',
        'keterangan',    
    ];

    // mengubah tipe data otomatis
    protected $casts = [
        'tanggal' => 'date',
    ];
}