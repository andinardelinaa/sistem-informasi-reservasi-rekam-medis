<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerubahanJadwalDokter extends Model
{
    // nama table, primary key, dan field
    protected $table = 'perubahan_jadwal_dokter';
    protected $primaryKey = 'id_perubahan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'dokter_id',
        'dokter_pengganti_id',
        'tanggal',
        'jenis_perubahan',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'status',
    ];

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dokter_id', 'id_dokter');
    }

    public function dokterPengganti()
    {
        return $this->belongsTo(Dokter::class, 'dokter_pengganti_id', 'id_dokter');
    }

    // scope untuk filter perubahan jadwal berdasarkan tanggal
    public function scopeUntukTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }
    
    // mengubah jenis_perubahan jadi teks
    public function getJenisLabelAttribute()
    {
        return match($this->jenis_perubahan) {
            'libur' => 'Libur',
            'pengganti' => 'Ganti Dokter',
            'tambahan' => 'Tambah Jadwal',
            default => $this->jenis_perubahan,
        };
    }

    // tampilan badge status
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'aktif' => '<span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Aktif</span>',
            'selesai' => '<span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Selesai</span>',
            'dibatalkan' => '<span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Dibatalkan</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">'.$this->status.'</span>',
        };
    }

}
