<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $primaryKey = 'id_reservasi';
    protected $table = 'reservasi';
    public $incrementing = true; 
    public $timestamps = true;
    protected $fillable = [
        'id_pasien',
        'id_jadwal', 
        'tgl_reservasi', 
        'jam_reservasi', 
        'antrean', 
        'status', 
        'email_terdampak',
        'keluhan', 
        'hadir_at', 
        'reminder_sent_at'
    ];

    public function getRouteKeyName()
    {
        return 'id_reservasi';
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'id_jadwal')
            ->withTrashed(); 
    }

    public function klinik()
    {
        return $this->belongsTo(Klinik::class, 'id_klinik');
    }

    // scope untuk filter reservasi berdasarkan klinik
    public function scopeByKlinik($query, $klinikId)
    {
        return $query->whereHas('jadwal', function ($q) use ($klinikId) {
            $q->where('id_klinik', $klinikId);
        });
    }

    // modifikasi data sebelum disimpan ke db
    public function setTglReservasiAttribute($value)
    {
        // Jika input "undefined" atau kosong, simpan sebagai NULL
        if ($value === 'undefined' || empty($value)) {
            $this->attributes['tgl_reservasi'] = null;
        } else {
            $this->attributes['tgl_reservasi'] = $value;
        }
    }

    public function rekamMedis()
    {
        return $this->hasOne(\App\Models\RekamMedis::class, 'id_reservasi', 'id_reservasi');
    }
    
}