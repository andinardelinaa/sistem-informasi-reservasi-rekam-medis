<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Jadwal extends Model
{
    use HasFactory, SoftDeletes;

    // nama table, primary key, dan field
    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';
    protected $fillable = [
        'id_klinik', 'id_dokter', 'hari', 'tanggal',
        'bulan', 'tahun', 'is_aktif',
        'jam_mulai', 'jam_selesai'
    ];

    // mengubah tipe data otomatis
    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'is_aktif' => 'boolean',
    ];

    // relasi klinik (fk ada di tabel jadwal)
    public function klinik()
    {
        return $this->belongsTo(Klinik::class, 'id_klinik');
    }

    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_jadwal');
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'id_dokter');
    }

    // mengambil jadwal template mingguan
    public function scopeTemplate($query)
    {
        return $query->whereNull('tanggal');
    }

    //  mengambil jadwal harian (yang sudah di-generate dari template)
    public function scopeHarian($query)
    {
        return $query->whereNotNull('tanggal');
    }

    // mengambil jadwal draft (is_aktif = false)
    public function scopeDraft($query)
    {
        return $query->where('is_aktif', 0);
    }

    // Jadwal yang bisa dilihat pasien (aktif + draft)
    public function scopeTersedia($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Jadwal yang bisa dilihat admin/dokter (aktif + draft + soft deleted)
    public function perubahanJadwalAktif()
    {
        return $this->hasOne(\App\Models\PerubahanJadwalDokter::class, 'dokter_id', 'id_dokter')
            ->where('status', 'aktif');
    }

    public function perubahanJadwal()
    {
        return $this->hasOne(PerubahanJadwalDokter::class, 'dokter_id', 'id_dokter');
    }
}