<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notif';
    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'tipe',
        'url',
        'is_read',
        'read_at',
    ];

    // mengubah tipe data otomatis
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // fungsi untuk menandai notifikasi sebagai sudah dibaca
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}