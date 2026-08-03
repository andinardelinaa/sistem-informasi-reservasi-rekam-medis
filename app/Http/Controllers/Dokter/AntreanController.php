<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\RekamMedis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AntreanController extends Controller
{
    // menampilkan antrean pasien hari ini
    public function index()
    {   //ambil dokter yang login
        $dokter = Auth::user()->dokter;
        //jika tidak ada dokter
        if (!$dokter) {
            return redirect()->back()->with('error', 'Data dokter tidak ditemukan.');
        }
        //tampilkan untuk hari ini
        $today = Carbon::now()->format('Y-m-d');
        //ambil reservasi
        $reservasi = Reservasi::with(['pasien', 'jadwal', 'rekamMedis']) 
        ->whereDate('tgl_reservasi', $today) //tanggalnya adalah hari ini
        ->whereHas('jadwal', function ($query) use ($dokter, $today) {
            $query->where(function ($q) use ($dokter, $today) {
                // dokter asli bisa liat antrean
                $q->where(function ($qq) use ($dokter, $today) {
                    $qq->where('id_dokter', $dokter->id_dokter) //apabila tidak ada perubahan
                        ->whereDoesntHave('perubahanJadwal', function ($p) use ($today) {
                            $p->whereDate('tanggal', $today)
                            ->where('status', 'aktif');
                        });
                })
                // jadwal dokter pengganti bisa lihat antrean
                ->orWhereHas('perubahanJadwal', function ($p) use ($dokter, $today) {
                    $p->where('dokter_pengganti_id', $dokter->id_dokter)
                    ->whereDate('tanggal', $today)
                    ->where('status', 'aktif');
                });
            });
        }) 
        //sorting antrean
        ->orderByRaw("  
            CASE
                WHEN status = 'dibatalkan' THEN 1
                ELSE 0
            END
        ")
        //urutkan dr jam reservasi
        ->orderBy('jam_reservasi')
        ->get();
        return view('dokter.antrean', compact('reservasi', 'dokter'));
    }

    //pengecekan kembali sebelum di submit
    public function selesai($id)
    {   //ambil dokter yg login
        $dokter = Auth::user()->dokter;
        //ambil reservasi, relasi jadwal dan perubahan jadwal
        $reservasi = Reservasi::with('jadwal.perubahanJadwal')->findOrFail($id);
        $isAuthorized = $reservasi->jadwal->id_dokter === $dokter->id_dokter;
        if (!$isAuthorized) {
            $isPengganti = $reservasi->jadwal->perubahanJadwal() //mengecek dokyter pengganti
                ->where('dokter_pengganti_id', $dokter->id_dokter)
                ->where('status', 'aktif')
                ->exists();  
            $isAuthorized = $isPengganti;
        }
        
        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses untuk reservasi ini.');
        }

        // Cari rekam medis yang dibuat oleh dokter ini
        $rekamMedis = RekamMedis::where('id_reservasi', $id)
            ->where('id_dokter', $dokter->id_dokter)
            ->first();
        if (!$rekamMedis) {
            return redirect()->route('dokter.antrean')
                ->with('error', 'Rekam medis belum diisi. Silakan isi rekam medis terlebih dahulu.');
        }
        if ($rekamMedis->status === 'final') {
            return redirect()->route('dokter.antrean')
                ->with('error', 'Rekam medis sudah difinalisasi.');
        }
        // Finalisasi selesai tidak dapat diubah lagi
        $rekamMedis->update(['status' => 'final']);
        $reservasi->update(['status' => 'selesai']);

        return redirect()->route('dokter.antrean')
            ->with('success', 'Pasien telah selesai diperiksa.');
    }

    //form rekam medis
    public function rekamMedis($id)
    {   //cari reservasi dengan jadwal dan pasien
        $reservasi = Reservasi::with('jadwal', 'pasien')->findOrFail($id);
        //jika tidak ada reservasi dengan jadwal dan dokter tersebut
        if (!$reservasi->jadwal || $reservasi->jadwal->id_dokter !== $dokter->id_dokter) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }
        // rm menggunakan id reservasi
        $rekamMedis = RekamMedis::where('id_reservasi', $id)->first();

        return view('dokter.rekam-medis.form', compact('reservasi', 'rekamMedis'));
    }
}