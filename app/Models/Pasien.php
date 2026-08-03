<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'pasien';
    protected $primaryKey = 'id_pasien';
    protected $fillable = [
        'id_user', 'nama', 'nik', 'jns_kelamin', 'tgl_lahir', 
        'no_hp', 'alamat', 'pekerjaan', 'riwayat_penyakit'
    ];

    // mengubah tipe data otomatis
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    // relasi user (fk ada di tabel pasien)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // menghitung umur secara otomatis 
    public function getUmurAttribute()
    {
        return $this->tgl_lahir ? $this->tgl_lahir->age : '-';
    }

    // mengubah kode jenis kelamin menjadi teks
    public function getJenisKelaminTextAttribute()
    {
        return $this->jns_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // cek user sudah punya profile pasien atau belum
    public static function hasProfile($userId)
    {
        return self::where('id_user', $userId)->exists();
    }

    // Accessor untuk umur detail (tahun, bulan, hari) 
    public function getUmurDetailAttribute()
    {
        $now = \Carbon\Carbon::now();
        $birthdate = \Carbon\Carbon::parse($this->tgl_lahir);
        
        $diff = $birthdate->diff($now);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;
        
        $parts = [];
        if ($years > 0) $parts[] = "{$years} tahun";
        if ($months > 0) $parts[] = "{$months} bulan";
        if ($days > 0 && count($parts) < 2) $parts[] = "{$days} hari";
        
        return implode(' ', $parts) ?: 'Baru lahir';
    }
    
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_pasien');
    }
}