<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, 
        HasFactory, 
        Notifiable, 
        HasRoles;

    // fields tabel users
    protected $fillable = ['nama', 'email', 'password', 'role', 'email_verified_at'];

    protected $hidden = ['password', 'remember_token'];

    // mengubah tipe data otomatis
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // menjalankan event otomatis
    protected static function booted()
    {
        
    }

    public function dokter()
    {
        return $this->hasOne(Dokter::class, 'user_id');
    }

    public function pasien()
    {
        return $this->hasOne(Pasien::class, 'id_user', 'id');
    }
    
    public function klinik()
    {
        return $this->belongsTo(Klinik::class, 'id_klinik', 'id_klinik');
    }
}