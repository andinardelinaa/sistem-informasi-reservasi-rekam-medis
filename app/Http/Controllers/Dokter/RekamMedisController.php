<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\RekamMedis;
use App\Models\DetailGigi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RekamMedisController extends Controller
{
    //menampilkan seluruh rekam medis yg diinputkan dokter
    public function index()
    {   //ambil dokter login
        $dokter = Auth::user()->dokter;
        //ambil data rekam medis
        $rekamMedis = RekamMedis::with(['reservasi.pasien', 'reservasi.jadwal.klinik', 'detailGigi'])
            ->where('id_dokter', $dokter->id_dokter)
            ->orderBy('tgl_periksa', 'desc')
            ->paginate(10);

        $stats = [ //statistik
            'total' => RekamMedis::where('id_dokter', $dokter->id_dokter)->count(),
            'hari_ini' => RekamMedis::where('id_dokter', $dokter->id_dokter)
                ->whereDate('tgl_periksa', today())->count(),
            'pasien_unik' => RekamMedis::where('id_dokter', $dokter->id_dokter)
                ->distinct('id_reservasi')->count('id_reservasi'),
        ];
        return view('dokter.rekam-medis.index', compact('rekamMedis', 'stats'));
    }
    //form rekam medis
    public function create($id_reservasi)
    {   //ambil dokter login
        $dokter = Auth::user()->dokter;
        //ambil reservasi pasien
        $reservasi = Reservasi::with(['pasien', 'jadwal'])
            ->where('id_reservasi', $id_reservasi)
            ->whereHas('jadwal', function($q) use ($dokter) { //memastika dokter sesuai
                $q->where(function ($q) use ($dokter) {
                    $q->where('id_dokter', $dokter->id_dokter)
                      ->orWhereHas('perubahanJadwal', function ($p) use ($dokter) { //dokter pengganti 
                          $p->where('dokter_pengganti_id', $dokter->id_dokter)
                            ->where('status', 'aktif');
                      });
                });            
            })
            ->whereIn('status', ['tindakan', 'selesai']) 
            ->firstOrFail();
        //cari apakah rm sudah ada
        $rekamMedis = RekamMedis::with('detailGigi')
            ->where('id_reservasi', $id_reservasi)
            ->where('id_dokter', $dokter->id_dokter)
            ->first();
        //readonly tidak bisa di edit kembali
        $isReadonly = $rekamMedis && $rekamMedis->status === 'final';

        return view('dokter.rekam-medis.create', compact('reservasi', 'dokter', 'rekamMedis', 'isReadonly'));
    }
    //simpan data dan validasi
    public function store(Request $request)
    {   //validasi
        $request->validate([
            'id_reservasi' => 'required|exists:reservasi,id_reservasi',
            'id_dokter' => 'required|exists:dokter,id_dokter',
            'diagnosa' => 'required|string|max:1000',
            'tindakan_utama' => 'nullable|string|max:1000',
            'detail_gigi' => 'required|array|min:1',
            'detail_gigi.*.nomor_gigi' => 'required|string|max:5',
            'detail_gigi.*.kondisi_gigi' => 'required|string|max:50',
            'detail_gigi.*.kondisi_lainnya' => 'nullable|string|max:255',
            'detail_gigi.*.tindakan' => 'required|string|max:255',
            'detail_gigi.*.tindakan_lainnya' => 'nullable|string|max:255',
        ], [
            'diagnosa.required' => 'Diagnosa wajib diisi.',
            'detail_gigi.required' => 'Pilih minimal satu gigi pada diagram.',
            'detail_gigi.*.kondisi_gigi.required' => 'Kondisi gigi wajib dipilih.',
            'detail_gigi.*.tindakan.required' => 'Tindakan gigi wajib dipilih.',
        ]);

        DB::beginTransaction();
        try { //cek rm lama
            $existingRM = RekamMedis::where('id_reservasi', $request->id_reservasi)
                ->where('id_dokter', $request->id_dokter)
                ->first();
            //jika rm sudah final maka tolak, karena tidak bisa diubah
            if ($existingRM && $existingRM->status === 'final') {
                return back()->with('error', 'Rekam medis sudah final dan tidak dapat diubah.');
            }
            //jika sudah ada (draft), perbarui
            if ($existingRM) {
                $rekamMedis = $existingRM;
                $rekamMedis->update([ //update
                    'diagnosa' => $request->diagnosa,
                    'tindakan' => $request->tindakan_utama,
                    'status' => 'draft',
                    'tgl_periksa' => now(),
                ]);
                //hapus detail gigi lama
                DetailGigi::where('id_rm', $rekamMedis->id_rm)->delete();
            } else { //jika belum ada
                $rekamMedis = RekamMedis::create([
                    'id_reservasi' => $request->id_reservasi,
                    'id_dokter' => $request->id_dokter,
                    'tgl_periksa' => now(),
                    'diagnosa' => $request->diagnosa,
                    'tindakan' => $request->tindakan_utama,
                    'status' => 'draft',
                ]);
            }
            //loop detail gigi
            foreach ($request->detail_gigi as $detail) {
                DetailGigi::create([ // buat detail giginya juga
                    'id_rm' => $rekamMedis->id_rm,
                    'nomor_gigi' => $detail['nomor_gigi'],
                    'kondisi_gigi' => $detail['kondisi_gigi'],
                    'kondisi_lainnya' => $detail['kondisi_lainnya'] ?? null,
                    'tindakan' => $detail['tindakan'],
                    'tindakan_lainnya' => $detail['tindakan_lainnya'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('dokter.antrean')
                ->with('success', 'Rekam medis berhasil disimpan. Klik "Lanjutkan" untuk melanjutkan pengisian, atau klik "Selesai" jika pemeriksaan sudah benar-benar selesai.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    //rekam medis selesai dan reservasi selesai
    public function finalize($id_reservasi)
    {   //dokter login
        $dokter = Auth::user()->dokter;
        // Cari rekam medis berdasarkan reservasi & dokter yang buat
        $rekamMedis = RekamMedis::where('id_reservasi', $id_reservasi)
            ->where('id_dokter', $dokter->id_dokter)
            ->first();
        //jika tidak ada rm
        if (!$rekamMedis) {
            return redirect()->route('dokter.antrean')
                ->with('error', 'Rekam medis tidak ditemukan. Pastikan sudah diisi terlebih dahulu.');
        }
        //jika sudah final
        if ($rekamMedis->status === 'final') {
            return redirect()->route('dokter.antrean')
                ->with('error', 'Rekam medis sudah difinalisasi.');
        }
        //jika masih draft, update statusnya
        $rekamMedis->update(['status' => 'final']); //rekam medis selesai
        Reservasi::where('id_reservasi', $id_reservasi)->update(['status' => 'selesai']); //reservasi selesai

        return redirect()->route('dokter.antrean')
            ->with('success', 'Rekam medis telah difinalisasi. Pasien selesai diperiksa.');
    }
    //menampilkan detai; rekam medis
    public function show($id_rm)
    {   //ambil data rm
        $rekamMedis = RekamMedis::with([
                'reservasi.pasien',
                'dokter',
                'detailGigi',
                'reservasi.jadwal.klinik'
            ])
            ->where('id_rm', $id_rm)
            ->firstOrFail();

        return view('dokter.rekam-medis.show', compact('rekamMedis'));
    }
    //menampilkan riwayat pemeriksaan pasien
    public function riwayatPasien($id_pasien)
    {   //cari pasien
        $pasien = \App\Models\Pasien::findOrFail($id_pasien);
        //ambil semua rm nya
        $rekamMedis = RekamMedis::with([
                'dokter',
                'reservasi.jadwal.dokter',
                'reservasi.jadwal.klinik',
                'detailGigi'
            ]) //cari rekam medis berdasarkan pasien
            ->whereHas('reservasi', function($q) use ($id_pasien) {
                $q->where('id_pasien', $id_pasien);
            })
            ->orderBy('tgl_periksa', 'desc')
            ->get();

        return view('dokter.rekam-medis.riwayat-pasien', compact('pasien', 'rekamMedis'));
    }
}