<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailGigi extends Model
{
    use HasFactory;

    // nama table, primary key, dan field
    protected $table = 'detail_gigi';
    protected $primaryKey = 'id_detail_gigi';
    public $timestamps = true;
    protected $fillable = [
        'id_rm',
        'nomor_gigi',
        'kondisi_gigi',
        'kondisi_lainnya',
        'tindakan',
        'tindakan_lainnya',
    ];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'id_rm', 'id_rm');
    }

    // tampilan kondisi gigi (kustom jika "lainnya")
    public function getKondisiTampilAttribute(): string
    {
        if ($this->kondisi_gigi === 'lainnya' && !empty($this->kondisi_lainnya)) {
            return $this->kondisi_lainnya;
        }
        return str_replace('_', ' ', $this->kondisi_gigi);
    }

    // tampilan tindakan (kustom jika "lainnya")
    public function getTindakanTampilAttribute(): string
    {
        if ($this->tindakan === 'lainnya' && !empty($this->tindakan_lainnya)) {
            return $this->tindakan_lainnya;
        }
        return str_replace('_', ' ', $this->tindakan);
    }
}