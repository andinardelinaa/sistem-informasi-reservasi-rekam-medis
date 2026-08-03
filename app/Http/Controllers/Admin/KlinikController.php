<?php

namespace App\Http\Controllers\Admin;

use App\Models\Klinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class KlinikController extends \App\Http\Controllers\Controller
{
    // tampil semua data
    public function index()
    {
        $klinik = Klinik::all();
        return view('admin.klinik.index', compact('klinik'));
    }

    // form tambah
    public function create()
    {
        return view('admin.klinik.create');
    }

    // simpan data dan validasi
    public function store(Request $request)
    {
        $request->validate([
            'nama_klinik' => 'required|max:100',
            'alamat' => 'required|max:255',
            'maps_url' => 'nullable'
        ]);
        // menyimpan data ke database
        Klinik::create($request->all());

        return redirect()->route('admin.klinik.index')
            ->with('success', 'Data klinik berhasil ditambahkan');
    }

    // form edit
    public function edit($id)
    {   //mengambil data berdasarkan id dan menampilkan form edit
        $klinik = Klinik::findOrFail($id);
        return view('admin.klinik.edit', compact('klinik'));
    }

    // update data
    public function update(Request $request, $id)
    {
        $klinik = Klinik::findOrFail($id); //cari data berdasarkan id

        $request->validate([ //validasi input
            'nama_klinik' => 'required|max:100',
            'alamat' => 'required|max:255',
            'maps_url' => 'nullable'
        ]);
        $klinik->update($request->all()); //update data

        return redirect()->route('admin.klinik.index')
            ->with('success', 'Data klinik berhasil diupdate');
    }

    // hapus data
    public function destroy($id)
    {
        try {
            $klinik = Klinik::findOrFail($id);
            $klinik->delete(); //hapus data

            return redirect()->route('admin.klinik.index')
                ->with('success', 'Data klinik berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) {

            return redirect()->route('admin.klinik.index')
                ->with('error', 'Gagal menghapus klinik karena masih terhubung dengan data lain.');

        } catch (\Exception $e) {

            return redirect()->route('admin.klinik.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data klinik.');
        }
    }
}
