<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasienRekamMedisController extends Controller
{
    //menampilkan riwayat rekam medis 
    public function index()
    {   //ambil pasien yang login
        $pasien = Auth::user()->pasien;
        //ambil data rm
        $rekammedis = RekamMedis::with(['dokter', 'reservasi.klinik'])
        ->whereHas('reservasi', function ($q) use ($pasien) {
            $q->where('id_pasien', $pasien->id_pasien);
        })// berdasarkan pasien
        ->latest()
        ->paginate(10);

        return view('pasien.rekam-medis.riwayat-rekam-medis', compact('rekammedis'));
    }
    //menampilkan detail rekam medis
    public function show($id_rm)
    {   //ambil pasien yang login
        $pasien = Auth::user()->pasien;  
        //ambil dtaa rm
        $rekamMedis = RekamMedis::with(['reservasi.pasien', 'dokter', 'detailGigi'])
            ->whereHas('reservasi', function ($q) use ($pasien) {  
                $q->where('id_pasien', $pasien->id_pasien);  
            })
            ->where('id_rm', $id_rm)
            ->firstOrFail();

        return view('pasien.rekam-medis.show', compact('rekamMedis'));
    }
    
}