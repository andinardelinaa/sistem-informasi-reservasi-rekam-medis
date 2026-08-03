<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilDokterController extends Controller
{
    // Menampilkan halaman profil dokter
    public function index()
    {
        return view('dokter.profil');
    }

    // Memperbarui profil dokter yang sedang login
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:100',
            'no_hp' => 'required|string|max:20',
        ]);
        // Mengambil data user yang sedang login
        $user = auth()->user();
        // Mengambil data dokter berdasarkan relasi dengan user
        $dokter = $user->dokter;

        // Jika profil dokter tidak ditemukan
        if (!$dokter) {
            return back()->with('error', 'Profil dokter tidak ditemukan.');
        }
        // Memperbarui data pada tabel users
        $user->update([
            'nama' => $request->nama,
        ]);

        // Memperbarui data pada tabel dokter
        $dokter->update([
            'spesialisasi' => $request->spesialisasi,
            'no_hp' => $request->no_hp,
        ]);

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}