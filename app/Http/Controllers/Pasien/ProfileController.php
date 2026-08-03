<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{   //form profil
    public function create()
    {   //ambil user yang lohin
        $user = Auth::user();

        if (Pasien::hasProfile($user->id)) { //memastikan pasien belum pernah mengisi prfil
            return redirect()->route('pasien.dashboard')
                             ->with('info', 'Anda sudah mengisi data pasien.');
        }
        return view('pasien.profile.create');
    }
    //simpan data dan validasi
    public function store(Request $request)
    {   //ambil user login
        $user = Auth::user();
        //jika pasien punya profil arahkan ke dashboard
        if (Pasien::hasProfile($user->id)) {
            return redirect()->route('pasien.dashboard');
        }
        //validasi
        $validated = $request->validate([
            'nama'        => 'required|string|max:100',
            'nik'         => 'required|size:16|unique:pasien,nik',
            'jns_kelamin' => ['required', Rule::in(['L','P'])],
            'tgl_lahir'   => 'required|date|before:today',
            'no_hp'       => 'required|string|max:20',
            'alamat'      => 'required|string|max:255',
            'pekerjaan'   => 'nullable|string|max:100',
        ]);
        //simpan data pasien
        Pasien::create([
            'id_user'     => $user->id,
            'nama'        => $validated['nama'],
            'nik'         => $validated['nik'],
            'jns_kelamin' => $validated['jns_kelamin'],
            'tgl_lahir'   => $validated['tgl_lahir'],
            'no_hp'       => $validated['no_hp'],
            'alamat'      => $validated['alamat'],
            'pekerjaan'   => $validated['pekerjaan'],
        ]);
        //update nama di user
        $user->update([
            'nama' => $validated['nama'],
        ]);

        return redirect()->route('pasien.pilih.klinik')
                         ->with('success', 'Data pasien berhasil disimpan. Anda dapat melakukan reservasi sekarang.');
    }
    //form edit
    public function edit()
    {   //ambil user login
        $user = Auth::user();
        //cari data pasien milik user tresebut
        $pasien = Pasien::where('id_user', $user->id)->first();
        //jika tidak ada data pasien
        if (!$pasien) {
            return redirect()->route('pasien.profile.create')
                            ->with('error', 'Data pasien belum tersedia.');
        }
        return view('pasien.profile.edit', compact('pasien'));
    }
    //update profil pasien
    public function update(Request $request)
    {   //ambil user login
        $user = Auth::user();
        //cari data pasien user tersebut
        $pasien = Pasien::where('id_user', $user->id)->first();
        //jika tidak ada pasien
        if (!$pasien) {
            return redirect()->route('pasien.profile.create');
        }   
        //validasi update
        $validated = $request->validate([
            'nama'        => 'required|string|max:100',
            'nik'         => ['required','size:16',Rule::unique('pasien', 'nik')->ignore($pasien->id_pasien, 'id_pasien')],
            'jns_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tgl_lahir'   => 'required|date|before:today',
            'no_hp'       => 'required|string|max:20',
            'alamat'      => 'required|string|max:255',
            'pekerjaan'   => 'nullable|string|max:100',
        ]);

        // update tabel user
        $user->update([
            'nama'  => $validated['nama'],
        ]);

        // update pasien
        $pasien->update([
            'nama'        => $validated['nama'],
            'nik'         => $validated['nik'],
            'jns_kelamin' => $validated['jns_kelamin'],
            'tgl_lahir'   => $validated['tgl_lahir'],
            'no_hp'       => $validated['no_hp'],
            'alamat'      => $validated['alamat'],
            'pekerjaan'   => $validated['pekerjaan'],
        ]);

        return redirect()->route('pasien.profil')
                        ->with('success', 'Profil pasien berhasil diperbarui.');
    }
}