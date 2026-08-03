<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'dokter';
    protected $primaryKey = 'id_dokter';

    protected $fillable = [
        'user_id',
        'nama',
        'spesialisasi',
        'no_hp',
    ];

    // relasi user (fk ada di tabel dokter)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_dokter', 'id_dokter');
    }

    public function perubahanJadwal()
    {
        return $this->hasMany(PerubahanJadwalDokter::class, 'dokter_id', 'id_dokter');
    }
}