<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends \App\Http\Controllers\Controller
{
    // tampil semua user
    public function index()
    {   
        //DB::enableQueryLog();
        $users = User::latest()->paginate(10);
        // Log::info(DB::getQueryLog());
        
        $totalUsers = User::count();
        $usersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $totalPasien = User::role('pasien')->count();
        $totalDokter = User::role('dokter')->count();
        //mengitung statistik
        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'usersThisMonth',
            'totalPasien',
            'totalDokter'
        ));
    }

    // form tambah user baru
    public function create()
    {
        return view('admin.users.create');
    }

    // simpan data user baru dan validasi
    public function store(Request $request)
    {   //validasi
        $request->validate([
            'nama'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|min:8|confirmed',
            'role'           => 'required|in:admin,dokter,pasien', //validasi role hanya boleh admin, dokter, atau pasien
            'email_verified' => 'nullable|boolean',
        ]);
        //simpan ke table user
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,  
        ]);
       
        // akses izin role dengan Spatie
        $user->syncRoles([$request->role]);

        // otomatis verifikasi email jika checkbox dicentang
        if ($request->has('email_verified') && $request->email_verified == 1) {
            $user->email_verified_at = now();
            $user->save();
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan dengan role ' . ucfirst($request->role));
    }

    // form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id); //berdasarkan id_user
        return view('admin.users.edit', compact('user'));
    }

    // update data user dan validasi
    public function update(Request $request, User $user)
    {   //validasi
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'password'  => 'nullable|min:8|confirmed',
            'role'      => 'required|in:admin,dokter,pasien',
        ]);
        //update ke table user
        
        $user->update([
            'nama'  => $validated['nama'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);
        //singkronisasi nama di tabel pasien atau dokter jika ada
        if ($user->pasien) {
            $user->pasien->update([
                'nama' => $validated['nama']
            ]);
        }
        if ($user->dokter) {
            $user->dokter->update([
                'nama' => $validated['nama']
            ]);
        }
        //update password jika diisi
        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }
        // update role dengan Spatie
        $user->syncRoles([$validated['role']]);
        // otomatis verifikasi email jika checkbox dicentang
        $user->email_verified_at = $request->has('email_verified') ? now() : null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui');
    }

    // hapus data user
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id); //berdasarkan id_user

            //admin tidak bisa menghapus akun yang sedang digunakan
            if(auth()->id() == $user->id) {
                return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
            }

            $user->delete();

            return redirect()->route('admin.users.index')->with('success', 'Data user berhasil dihapus.');

        } catch (QueryException $e) {
            // tidak bisa menghapus user jika masih terkait dengan data lain (misal: pasien atau dokter)
            if (
                $e->getCode() == '23000' ||
                str_contains($e->getMessage(), 'Cannot delete or update a parent row')
            ) 

            return redirect()->route('admin.users.index')->with('error', 'Data masih terkait dengan data lain.');
        }
    }
}