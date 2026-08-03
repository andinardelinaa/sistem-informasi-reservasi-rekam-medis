<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use App\Models\Dokter;
use App\Models\Klinik;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekamMedisExcelExport;
use App\Exports\RekamMedisWordExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RekamMedisAdminController extends Controller
{
    //menampilkan daftar rekam medis
    public function index()
    {   //ambil data rekam medi dengan relasinya
        // DB::enableQueryLog();
        $rekamMedis = RekamMedis::with([ //relasi data dapat diambil
            'reservasi.pasien',
            'reservasi.jadwal.klinik',
            'dokter',
            'detailGigi'
        ])->orderBy('tgl_periksa', 'desc')->paginate(10); 
        // Log::info(DB::getQueryLog()); //tampilkan berdasarkan tgl periksa

        $stats = [ //statistik rekam medis
            'total' => RekamMedis::count(),
            'hari_ini' => RekamMedis::whereDate('tgl_periksa', today())->count(),
            'minggu_ini' => RekamMedis::whereBetween('tgl_periksa', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'pasien_unik' => RekamMedis::distinct('id_reservasi')->count('id_reservasi'),
        ];

        $dokters = Dokter::all();
        $klinik = Klinik::all();

        return view('admin.rekam-medis.index', compact('rekamMedis', 'stats', 'dokters', 'klinik'));
    }
    //hapus rekam medis
    public function destroy($id_rm)
    {
        try { //cari data rekam medis 
            $rekamMedis = RekamMedis::findOrFail($id_rm);
            $rekamMedis->detailGigi()->delete(); //detail gigi dihapus dulu
            $rekamMedis->delete(); //hapus r,

            return redirect()->route('admin.rekam-medis.index')
                ->with('success', 'Rekam medis berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.rekam-medis.index')
                ->with('error', 'Gagal menghapus rekam medis: ' . $e->getMessage());
        }
    }
    //detail rekam medis
    public function show($id_rm)
    {   //ambil data rekem medis dan relasinya
        $rekamMedis = RekamMedis::with(['reservasi.pasien', 'reservasi.jadwal.klinik', 'reservasi.jadwal.dokter', 'dokter', 'detailGigi'])
            ->where('id_rm', $id_rm)
            ->firstOrFail();

        return view('admin.rekam-medis.show', compact('rekamMedis'));
    }
    //export word
    public function exportWord(Request $request)
    {   //ambil data rm dan relasinya
        $query = RekamMedis::with(['reservasi.pasien', 'reservasi.jadwal.klinik', 'dokter']);

        if ($request->filled('klinik')) {
            $query->whereHas('reservasi.jadwal', fn($q) => $q->where('id_klinik', $request->klinik));
        }    // filter berdasarkan klinik yg dipilih
        //filter berdasarkan tgl periksa
        if ($request->filled('date')) {
            $query->whereDate('tgl_periksa', $request->date);
        } elseif ($request->filled('bulan')) { //atau filter berdasarkan bulan periksa
            $query->whereMonth('tgl_periksa', $request->bulan);
        }

        $data = $query->orderBy('tgl_periksa', 'desc')->get();
        $klinikId = $request->klinik ?? auth()->user()->id_klinik;

        return (new RekamMedisWordExport($data, $klinikId))->download();
    }
    //export excel
    public function exportExcel()
    {   
        $klinikId = auth()->user()->id_klinik ?? null;
        //ambil data rm
        $data = RekamMedis::with([
            'reservasi.pasien',
            'dokter',
            'reservasi.jadwal.klinik'
        ])->latest()->get();

        $export = new RekamMedisExcelExport($data, $klinikId);
        return Excel::download($export, 'rekam-medis.xlsx');
    }
}
