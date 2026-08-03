<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klinik extends Model
{
    // nama table, primary key, dan field
    protected $table = 'klinik';
    protected $primaryKey = 'id_klinik';

    public $timestamps = false;

    protected $fillable = [
        'nama_klinik',
        'alamat',
        'maps_url'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_klinik', 'id_klinik');
    }
}