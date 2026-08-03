<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PasienController extends Controller
{   //tampil semua data pasien
    public function index()
    { //ambil data pasien beserta relasi user untuk diambil dlm 1 query
        $pasiens = Pasien::with('user')->latest('id_pasien')->paginate(10);
        return view('admin.pasien.index', compact('pasiens'));
    }
    // form tambah
    public function create()
    { //ambil user yang belum punya profil pasien dan memiliki role pasien
        $users = User::role('pasien')
            ->whereDoesntHave('pasien')
            ->orderBy('nama')
            ->get();

        return view('admin.pasien.create', compact('users'));
    }
    // simpan data dan validasi
    public function store(Request $request)
    {
        try {
            \Log::info('=== STORE DEBUG ===', $request->all());
            //akun baru atau pakai akun lama
            $request->validate([
                'create_user' => 'required|in:0,1', //akun baru 1 atau pakai akun lama 0
                'id_user' => [ //wajib jika buat akun yg sudah terdaftar
                    'nullable',
                    'required_if:create_user,0', //validasi hanya diperlukan jika create_user=0
                    'exists:users,id',
                    Rule::unique('pasien', 'id_user')
                ],
                'user_email' => 'nullable|required_if:create_user,1|email|unique:users,email',
                'user_password' => 'nullable|required_if:create_user,1|string|min:8|confirmed',
                'nama' => 'required|string|max:100',
                'nik' => 'required|digits:16|unique:pasien,nik',
                'jns_kelamin' => 'required|in:L,P',
                'tgl_lahir' => 'required|date|before:today',
                'no_hp' => 'required|string|max:20',
                'alamat' => 'required|string|max:255',
                'pekerjaan' => 'nullable|string|max:100',
                'riwayat_penyakit' => 'nullable|string',
            ]);

            DB::beginTransaction();
            \Log::info('Transaction started'); //ubah pasein dan user bersamaan agar tidak terjadi data pasien tanpa user atau sebaliknya
            //masuk tabel user
            if ($request->create_user == '1') {
                $user = User::create([
                    'nama' => $request->nama,
                    'email' => $request->user_email,
                    'password' => Hash::make($request->user_password),
                    'role' => 'pasien',
                    'email_verified_at' => now(),
                ]);

                $user->syncRoles(['pasien']); //menggantikan role user menjadi pasien (jika sebelumnya sudah ada role lain, akan dihapus dan diganti dengan pasien)
                $userId = $user->id;
                \Log::info('User baru dibuat', ['id' => $userId]);
            } else {
                $userId = $request->id_user;
                \Log::info('User dipilih', ['id' => $userId]);
                
                $selectedUser = User::find($userId); //ambil data user yang dipilih dan memiliki role pasien
                \Log::info('User found?', ['user' => $selectedUser ? 'yes' : 'no']);

                if ($selectedUser) {
                    if (!$selectedUser->hasRole('pasien')) {  //cek a[akash udah punya role pasien]
                        $selectedUser->assignRole('pasien'); //tambah kalo belum
                        \Log::info('Role pasien assigned');
                    }
                    $selectedUser->update(['role' => 'pasien']);
                }
            }
            // masuk tabel pasien
            $pasien = Pasien::create([
                'id_user' => $userId,
                'nama' => $request->nama,
                'nik' => $request->nik,
                'jns_kelamin' => $request->jns_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'pekerjaan' => $request->pekerjaan,
                'riwayat_penyakit' => $request->riwayat_penyakit,
            ]);
            
            \Log::info('Pasien created', ['id_pasien' => $pasien->id_pasien ?? 'null']);

            DB::commit();
            \Log::info('Transaction committed');

            return redirect()->route('admin.pasien.index')
                ->with('success', 'Data pasien berhasil ditambahkan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('VALIDATION FAILED', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ERROR STORE', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    // form edit
    public function edit(Pasien $pasien)
    {
        return view('admin.pasien.edit', compact('pasien'));
    }
    // update data 
    public function update(Request $request, Pasien $pasien)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => ['required','digits:16', Rule::unique('pasien', 'nik')->ignore($pasien->id_pasien, 'id_pasien')
            ],
            'jns_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date|before:today',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'pekerjaan' => 'nullable|string|max:100',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        try { //update data pasien
            $pasien->update([
                'nama' => $request->nama,
                'nik' => $request->nik,
                'jns_kelamin' => $request->jns_kelamin,
                'tgl_lahir' => $request->tgl_lahir,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'pekerjaan' => $request->pekerjaan,
                'riwayat_penyakit' => $request->riwayat_penyakit,
            ]);

            $pasien->load('user');
            //update user
            if ($pasien->user) {
                $pasien->user->update([
                    'nama' => $request->nama,
                    'email' => $pasien->user->email, //email tidak diubah
                    'password' => $pasien->user->password, //password tidak diubah
                    
                ]);
            }

            return redirect()->route('admin.pasien.index')
                ->with('success', 'Data pasien berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    // hapus data
    public function destroy(Pasien $pasien)
    {
        try {
            DB::beginTransaction();
            $pasien->delete();
            DB::commit();

            return redirect()->route('admin.pasien.index')
                ->with('success', 'Data pasien berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.pasien.index')
                ->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    //lihat detail
    public function show($id)
    {
        $pasien = Pasien::with(['user.roles', 'reservasi.jadwal.dokter', 'reservasi.rekamMedis'])->findOrFail($id);
        return view('admin.pasien.detail', compact('pasien'));
    }
}