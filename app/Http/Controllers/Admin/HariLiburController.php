<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\HariLiburImport;
use App\Exports\HariLiburExport;
use App\Models\Reservasi;

class HariLiburController extends Controller
{   //mengambil seluruh data hari libur
    public function index()
    {
        $hariLibur = HariLibur::latest('tanggal')->paginate(10);
        return view('admin.hari-libur.index', compact('hariLibur'));
    }
    //form hari libur
    public function create()
    {
        return view('admin.hari-libur.create');
    }
    //simpan data dan validasi
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);
        // menyimpan data
        HariLibur::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);
        //update reservasi jadi terdampak
        Reservasi::whereDate('tgl_reservasi', $request->tanggal)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->update([
                'status' => 'terdampak'
            ]);

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }
    //form edit
    public function edit($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        return view('admin.hari-libur.edit', compact('hariLibur'));
    }
    //update data
    public function update(Request $request, $id)
    {
        $hariLibur = HariLibur::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal,' . $id . ',id_hari_libur',
            'keterangan' => 'required|string|max:255',
        ]);
        try {
            $hariLibur->update([
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()
                ->route('admin.hari-libur.index')
                ->with('success', 'Hari libur berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui: ' . $e->getMessage());
        }
    }
    // hapus hari libur
    public function destroy($id)
    {
        try {
            $hariLibur = HariLibur::findOrFail($id);
            $hariLibur->delete();

            return redirect()
                ->route('admin.hari-libur.index')
                ->with('success', 'Hari libur berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.hari-libur.index')
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
    //import hari libur
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]); //file yg diterima sistem

        if ($validator->fails()) {
            return redirect()
                ->route('admin.hari-libur.index')
                ->with('error', 'File tidak valid. Pastikan format .xlsx, .xls, atau .csv dan maksimal 5MB.');
        }
        try {
            Excel::import(new HariLiburImport, $request->file('file'));

            return redirect()
                ->route('admin.hari-libur.index')
                ->with('success', 'Data hari libur berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.hari-libur.index')
                ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
    
    //ekspor hari libur menghasilkan file excel
    public function export(Request $request)
    {
        $query = HariLibur::query();
        
        if ($request->has('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }
        
        $hariLibur = $query->orderBy('tanggal', 'desc')->get();
        
        return Excel::download(new HariLiburExport($hariLibur), 'hari_libur.xlsx');
    }
}