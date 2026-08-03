<?php

namespace App\Http\Controllers\Admin;

use App\Models\Dokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DokterController extends \App\Http\Controllers\Controller
{
    // tampil semua data
    public function index()
    { // ambil data dokter beserta relasi user untuk diambil dlm 1 query
        $dokter = Dokter::with('user')->latest()->paginate(10);

        $spesialisasi = Dokter::select('spesialisasi')
            ->whereNotNull('spesialisasi')
            ->distinct()
            ->orderBy('spesialisasi')
            ->pluck('spesialisasi');

        return view('admin.dokter.index', compact('dokter', 'spesialisasi'));
    }
    // form tambah
    public function create()
    {
        $users = User::whereDoesntHave('dokter')->whereHas('roles', function($q) {
            $q->where('nama', 'dokter');
        })->get(); //ambil user yang belum punya profil dokter dan memiliki role dokter

        return view('admin.dokter.create', compact('users'));
    }
    // simpan data dan validasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:150',
            'spesialisasi'  => 'required|string|max:100',
            'no_hp'         => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        // Buat akun user baru (kolom role disinkronkan dengan Spatie)
        $user = User::create([
            'nama'     => $validated['nama'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'dokter',
        ]);

        $user->syncRoles(['dokter']); //menggantikan role user menjadi dokter (jika sebelumnya sudah ada role lain, akan dihapus dan diganti dengan dokter)
        
        // Buat profil dokter 
        Dokter::create([
            'user_id'      => $user->id,
            'nama'         => $validated['nama'],
            'spesialisasi' => $validated['spesialisasi'],
            'no_hp'        => $validated['no_hp'],
        ]);

        return redirect()->route('admin.dokter.index')
            ->with('success', 'Dokter berhasil ditambahkan beserta akun loginnya.');
    }
    // form edit
    public function edit(Dokter $dokter)
    {
        return view('admin.dokter.edit', compact('dokter'));
    }
    // update data route model binding
    public function update(Request $request, Dokter $dokter)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:150',
            'spesialisasi'  => 'required|string|max:100',
            'no_hp'         => 'required|string|max:20',
            'email'         => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($dokter->user_id),
            ],
            'password'      => 'nullable|string|min:8|confirmed',
        ]);
        DB::beginTransaction(); //memastikan tabel dokter dan user terupdate bersamaan

        try {

            // Update tabel dokter
            $dokter->update([
                'nama'         => $validated['nama'],
                'spesialisasi' => $validated['spesialisasi'],
                'no_hp'        => $validated['no_hp'],
            ]);

            // Update tabel users
            if ($dokter->user) {

                $userData = [
                    'nama'  => $validated['nama'],
                    'email' => $validated['email'],
                ];

                // Password hanya diupdate jika diisi
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $dokter->user->update($userData);
            }

            DB::commit();

            return redirect()->route('admin.dokter.index')
                ->with('success', 'Data dokter berhasil diperbarui.');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Gagal memperbarui data dokter.');
        }
    }
    // hapus data
    public function destroy($id)
    {
        try {
            $dokter = Dokter::findOrFail($id); //cari data dokter berdasarkan id, jika tidak ditemukan akan otomatis throw ModelNotFoundException yang akan ditangani oleh catch berikutnya
            $dokter->delete();

            return redirect()->route('admin.dokter.index')
                ->with('success', 'Data dokter berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) { //apabila dokter masih terhubung dgn tabel lain

            return redirect()->route('admin.dokter.index')
                ->with('error', 'Gagal menghapus dokter karena masih terhubung dengan data lain (jadwal atau reservasi).');
        } catch (\Exception $e) {

            return redirect()->route('admin.dokter.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data dokter.');
        }
    }
    //mengambil daftar spesialisasi dokter
    public function getSpesialisasi()
    {
        $spesialisasi = Dokter::select('spesialisasi')
            ->whereNotNull('spesialisasi')
            ->distinct()
            ->orderBy('spesialisasi')
            ->pluck('spesialisasi');

        return response()->json($spesialisasi);
    }
}