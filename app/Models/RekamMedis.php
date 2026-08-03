<?php
// app/Models/RekamMedis.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'rekam_medis';
    protected $primaryKey = 'id_rm';
    public $incrementing = true;
    protected $fillable = [
        'id_dokter',
        'id_reservasi',
        'tgl_periksa',
        'diagnosa',
        'tindakan',
        'status',
    ];

    // mengubah tipe data otomatis
    protected $casts = [
        'tgl_periksa' => 'datetime',
        'status' => 'string',
    ];

    // mengambil rekam medis final (status = 'final')
    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    // cek reka medis bisa diedit (status = 'draft')
    public function getIsEditableAttribute(): bool
    {
        return $this->status === 'draft';
    }
    
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'id_dokter', 'id_dokter');
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'id_reservasi', 'id_reservasi');
    }

    public function detailGigi()
    {
        return $this->hasMany(DetailGigi::class, 'id_rm', 'id_rm');
    }
    public function pasien() 
    {
        return $this->belongsTo(Pasien::class);
    }
    
}