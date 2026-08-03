<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Klinik;
use App\Models\Dokter;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{  //mapping hari ke angka untuk memudahkan generate jadwal harian
    private $hariMapping = [
        'senin' => 1, 'selasa' => 2, 'rabu' => 3, 'kamis' => 4,
        'jumat' => 5, 'sabtu' => 6, 'minggu' => 0
    ];
    //menampilkan jadwal aktif, draft bulan depan, dan riwayat jadwal yang dihapus
    public function index()
    {   
        $this->autoActivateIfFirstDayOfMonth();
        //ambil data bulan dan tahun saat ini, bulan depan 
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        $nextMonth = now()->addMonthNoOverflow();
        //ambil data jadwal draft untuk bulan depan
        $bulanDepan = $nextMonth->month;
        $tahunDepan = $nextMonth->year;
        //ambil data jadwal aktif bulan sekarang
        $jadwalAktif = Jadwal::with(['klinik', 'dokter'])
            ->where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->where('is_aktif', true) //aktif
            ->whereNull('deleted_at')
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
            ->orderBy('jam_mulai')
            ->get();
        //ambil data jadwal draft untuk bulan depan
        $draftBulanDepan = Jadwal::with(['klinik', 'dokter'])
            ->where('bulan', $bulanDepan)
            ->where('tahun', $tahunDepan)
            ->whereNull('deleted_at')
            ->orderByRaw("FIELD(hari, 'senin','selasa','rabu','kamis','jumat','sabtu','minggu')")
            ->orderBy('jam_mulai')
            ->get();
        //ambil data jadwal yang sudah dihapus (riwayat)
        $riwayat = Jadwal::onlyTrashed()
            ->with(['klinik', 'dokter'])
            ->orderBy('deleted_at', 'desc')
            ->get();
        //template draft apakah sudah di generate
        $sudahAdaDraft = $draftBulanDepan->whereNull('tanggal')->isNotEmpty();
        $sudahDigenerate = $draftBulanDepan->whereNotNull('tanggal')->isNotEmpty();

        return view('admin.jadwal.index', compact(
            'jadwalAktif',
            'draftBulanDepan',
            'riwayat',
            'sudahAdaDraft',
            'sudahDigenerate',
            'bulanDepan',
            'tahunDepan'
        ));
    }

    //otomatis aktifkan draft jadwal jika sudah tanggal 1
    private function autoActivateIfFirstDayOfMonth()
    {   //ambil waktu saat ini
        $sekarang = Carbon::now('Asia/Jakarta');
        $bulanIni = $sekarang->month;
        $tahunIni = $sekarang->year;
        // cek apakah bulan ini punya jadwal aktif
        $sudahAktif = Jadwal::where('bulan', $bulanIni)
        ->where('tahun', $tahunIni)
        ->where('is_aktif', true)
        ->whereNull('deleted_at')
        ->exists();

        // cek apakah jadwal sudah aktif
        if ($sudahAktif) {
            Log::info('Auto-activate: sudah aktif, skip');
            return;
        }
        // cek apakah ada draft
        $adaDraft = Jadwal::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->where(function($q) {
                $q->where('is_aktif', 0)
                ->orWhere('is_aktif', false)
                ->orWhereNull('is_aktif');
            })
            ->whereNull('deleted_at')
            ->exists();
        if (!$adaDraft) {
            Log::info('Auto-activate: tidak ada draft untuk diaktifkan');
            return;
        }
        // jalankan aktivasi
        DB::beginTransaction();
        try {
            
            $bulanLalu = $sekarang->copy()->subMonth()->month;
            $tahunLalu = $sekarang->copy()->subMonth()->year;
            // hapus jadwal bulan lalu (soft delete)
            Jadwal::where('bulan', $bulanLalu)
                ->where('tahun', $tahunLalu)
                ->whereNull('deleted_at')
                ->delete();

            // aktifkan bulan ini
            Jadwal::where('bulan', $bulanIni)
                ->where('tahun', $tahunIni)
                ->whereNull('deleted_at')
                ->update(['is_aktif' => true]); //aktifkan 

            DB::commit(); //simpan perubahan ke database
            Log::info('Auto-activate: SUKSES');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-activate: GAGAL - ' . $e->getMessage());
        }
    }

    // form tambah
    public function create()
    {
        $klinik = Klinik::orderBy('nama_klinik')->get(); //ambil data klinik
        $dokter = Dokter::orderBy('nama')->get(); //ambil data dokter

        $isDraft = request('draft') == '1';
        $targetBulan = $isDraft ? now()->addMonthNoOverflow() : now();
        return view('admin.jadwal.create', compact('klinik', 'dokter', 'isDraft', 'targetBulan'));
    }

    // simpan data dan validasi
    public function store(Request $request)
    {
        $mode = $request->input('input_mode', 'single');
        $isDraft = $request->boolean('is_draft');

        if ($isDraft) {
            $target = now()->addMonthNoOverflow();
        } else {
            $target = now();
        }

        if ($mode === 'weekly') {
            $result = $this->storeWeekly($request, $target, $isDraft); 
        } else {
            $result = $this->storeSingle($request, $target, $isDraft);
        }

        // HANYA generate harian kalau SUCCESS (bukan error/bentrok)
        if ($result instanceof \Illuminate\Http\RedirectResponse 
            && $result->getSession()->has('success')) {
            
            $generated = $this->generateHarianForBulan($target->month, $target->year);
            if ($generated > 0) {
                $existingMsg = $result->getSession()->get('success');
                $result->with('success', $existingMsg . ' dan jadwal harian berhasil digenerate.');
            }
        }

        return $result;
    }

    // validasi dan simpan jadwal harian
    private function storeSingle(Request $request, Carbon $target, bool $isDraft)
    {
        $validated = $request->validate([
            'id_klinik'   => 'required|exists:klinik,id_klinik',
            'id_dokter'   => 'required|exists:dokter,id_dokter',
            'hari'        => ['required', Rule::in(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'])],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        // Cek bentrok
        $bentrok = Jadwal::where('id_dokter', $validated['id_dokter'])
            ->where('hari', $validated['hari'])
            ->where('bulan', $target->month)
            ->where('tahun', $target->year)
            ->whereNull('tanggal')
            ->whereNull('deleted_at')
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai'])
            ->first();

        if ($bentrok) {
            $klinikNama = \App\Models\Klinik::find($bentrok->id_klinik)->nama_klinik ?? 'Klinik';
            $errorMsg = 'Dokter sudah memiliki jadwal di ' . $klinikNama . ' pada hari ' . ucfirst($validated['hari']) . '. Jadwal yang Anda input (' . 
                $validated['jam_mulai'] . ' - ' . $validated['jam_selesai'] . ') bentrok dengan jadwal tersebut.';

            return back()->withErrors(['bentrok_single' => $errorMsg])->withInput();
        }

        // Simpan
        Jadwal::firstOrCreate([
            'id_klinik'   => $validated['id_klinik'],
            'id_dokter'   => $validated['id_dokter'],
            'hari'        => $validated['hari'],
            'jam_mulai'   => $validated['jam_mulai'],
            'jam_selesai' => $validated['jam_selesai'],
            'bulan'       => $target->month,
            'tahun'       => $target->year,
            'is_aktif'    => $isDraft ? 0 : 1,
        ]);

        $msg = $isDraft 
            ? 'Template draft berhasil disimpan.'
            : 'Template jadwal berhasil ditambahkan.';

        return redirect()->route('admin.jadwal.index')->with('success', $msg);
    }

    // validasi dan simpan jadwal mingguan
    private function storeWeekly(Request $request, Carbon $target, bool $isDraft)
    {
        $validated = $request->validate([
            'id_dokter'   => 'required|exists:dokter,id_dokter',
        ]);

        $dokterId = $validated['id_dokter'];
        $hariList = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
        $createdCount = 0;
        $errorDetails = []; // Gabungkan semua error di sini

        foreach ($hariList as $hari) {
            $klinikId = $request->input("{$hari}_id_klinik");
            $jamMulai = $request->input("{$hari}_mulai");
            $jamSelesai = $request->input("{$hari}_selesai");

            if (empty($klinikId) || empty($jamMulai) || empty($jamSelesai)) {
                continue;
            }

            if (strtotime($jamSelesai) <= strtotime($jamMulai)) {
                $errorDetails[] = [
                    'hari' => ucfirst($hari),
                    'type' => 'jam_invalid',
                    'msg' => 'Jam selesai harus setelah jam mulai.',
                ];
                continue;
            }

            $bentrok = Jadwal::where('id_dokter', $dokterId)
                ->where('hari', $hari)
                ->where('bulan', $target->month)
                ->where('tahun', $target->year)
                ->whereNull('tanggal')
                ->whereNull('deleted_at')
                ->where('jam_mulai', '<', $jamSelesai)
                ->where('jam_selesai', '>', $jamMulai)
                ->first();

            if ($bentrok) {
                $klinikNama = \App\Models\Klinik::find($bentrok->id_klinik)->nama_klinik ?? 'Klinik';
                $errorDetails[] = [
                    'hari' => ucfirst($hari),
                    'type' => 'bentrok',
                    'jam_input' => substr($jamMulai, 0, 5) . ' - ' . substr($jamSelesai, 0, 5),
                    'jam_existing' => substr($bentrok->jam_mulai, 0, 5) . ' - ' . substr($bentrok->jam_selesai, 0, 5),
                    'klinik' => $klinikNama,
                ];
                continue;
            }

            Jadwal::create([
                'id_klinik'   => $klinikId,
                'id_dokter'   => $dokterId,
                'hari'        => $hari,
                'jam_mulai'   => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'bulan'       => $target->month,
                'tahun'       => $target->year,
                'is_aktif'    => $isDraft ? 0 : 1,
            ]);

            $createdCount++;
        }

        // Ada error (bentrok atau jam invalid)
        if (!empty($errorDetails)) {
            // Ada yang berhasil → warning ke index
            if ($createdCount > 0) {
                $errorParts = [];
                foreach ($errorDetails as $ed) {
                    if ($ed['type'] === 'jam_invalid') {
                        $errorParts[] = $ed['hari'] . ': ' . $ed['msg'];
                    } else {
                        $errorParts[] = $ed['hari'] . ': Jadwal bentrok dengan jadwal di ' . $ed['klinik'];
                    }
                }
                return redirect()->route('admin.jadwal.index')
                    ->with('error', "Berhasil membuat {$createdCount} template, namun ada error: " . implode(', ', $errorParts));
            }

            // SEMUA GAGAL → back ke form dengan popup
            $bentrokMsg = '<ul style="text-align:left; margin-top:8px; padding-left:16px;">';
            $errorLines = [];
            foreach ($errorDetails as $ed) {
                if ($ed['type'] === 'jam_invalid') {
                    $errorLines[] = $ed['hari'] . ': ' . $ed['msg'];
                    $bentrokMsg .= '<li style="margin-bottom:4px;">' . $ed['hari'] . '</strong>: ' . $ed['msg'] . '</li>';
                } else {
                    $errorLines[] = $ed['hari'] . ': Jadwal bentrok dengan jam di ' . $ed['klinik'];
                    $bentrokMsg .= '<li style="margin-bottom:4px;">' . $ed['hari']. ': bentrok dengan jadwal di ' . $ed['klinik'] . '</li>';
                }
            }
            $bentrokMsg .= '</ul>';

            return back()
                ->withErrors(['weekly_error' => implode('<br>', $errorLines), 'weekly_bentrok_detail' => $bentrokMsg])
                ->withInput();
        }

        if ($createdCount === 0) {
            return back()->with('error', 'Tidak ada hari yang diisi.')->withInput();
        }

        $msg = $isDraft
            ? "Berhasil membuat {$createdCount} template draft."
            : "Berhasil membuat {$createdCount} template jadwal.";

        return redirect()->route('admin.jadwal.index')->with('success', $msg);
    }

    //ubah template menjadi jadwal harian
    public function generateHarianForBulan($bulan, $tahun)
    {
        try { 
            $target = Carbon::create((int)$tahun, (int)$bulan, 1);
            
            Log::info('Generate jadwal harian dimulai', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'target_date' => $target->toDateString()
            ]);
            // ambil semua template untuk bulan dan tahun target
            $templates = Jadwal::with(['klinik', 'dokter'])
                ->where('bulan', $target->month)
                ->where('tahun', $target->year)
                ->whereNull('tanggal') //template tidak memiliki tanggal
                ->whereNull('deleted_at')
                ->get();

            Log::info('Template ditemukan', ['count' => $templates->count()]);
            // gaada template gabisa generate
            if ($templates->isEmpty()) {
                Log::warning('Tidak ada template untuk bulan ini');
                return 0;
            }
        
            $startOfMonth = $target->copy()->startOfMonth();
            $endOfMonth = $target->copy()->endOfMonth();
            $generatedCount = 0;

            DB::beginTransaction();
            //loop seluruh tanggal pada bulan target
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dayOfWeek = $date->dayOfWeek; //cek hari apa
                //loop seluruh template
                foreach ($templates as $template) {
                    $templateHari = strtolower($template->hari);
                    
                    if (!isset($this->hariMapping[$templateHari])) {
                        Log::error('Hari tidak valid: ' . $template->hari);
                        continue;
                    }
                    //jadwal dibuat jika hari template sesuai dengan hari tanggal tersebut 
                    if ($this->hariMapping[$templateHari] == $dayOfWeek) {
                        //cek apakah jadwal sudah pernah dibuat
                        $exists = Jadwal::where('tanggal', $date->toDateString())
                            ->where('id_klinik', $template->id_klinik)
                            ->where('id_dokter', $template->id_dokter)
                            ->where('jam_mulai', '<', $template->jam_selesai)
                            ->where('jam_selesai', '>', $template->jam_mulai)
                            ->whereNull('deleted_at')
                            ->exists();

                        if (!$exists) { //membuat jadwal harian
                            Jadwal::create([
                                'id_klinik'   => $template->id_klinik,
                                'id_dokter'   => $template->id_dokter,
                                'hari'        => $templateHari,
                                'tanggal'     => $date->toDateString(),
                                'bulan'       => $target->month,
                                'tahun'       => $target->year,
                                'jam_mulai'   => $template->jam_mulai,
                                'jam_selesai' => $template->jam_selesai,
                                'is_aktif'    => 0,
                            ]);
                            $generatedCount++; //hitung jumlah jadwal yg berhasil dibuat
                        }
                    }
                }
            }

            DB::commit();
            
            Log::info('Generate jadwal harian selesai', ['generated' => $generatedCount]);
            return $generatedCount;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Generate jadwal harian gagal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'bulan' => $bulan,
                'tahun' => $tahun
            ]);
            return -1;
        }
    }

    //generate jadwal 
    public function generate(Request $request)
    {   
        $nextMonth = now()->addMonthNoOverflow();
        $bulanTarget = (int) $request->input('bulan', $nextMonth->month);
        $tahunTarget = (int) $request->input('tahun', $nextMonth->year);

        Log::info('Admin generate jadwal', [
            'bulan' => $bulanTarget,
            'tahun' => $tahunTarget,
            'request_all' => $request->all()
        ]);
        //panggil method generate jadwal harian
        $generatedCount = $this->generateHarianForBulan($bulanTarget, $tahunTarget);

        $target = Carbon::create($tahunTarget, $bulanTarget, 1);

        if ($generatedCount === -1) {
            return redirect()->route('admin.jadwal.index')
                ->with('error', 'Terjadi kesalahan saat generate jadwal. Cek log untuk detail.');
        }

        if ($generatedCount === 0) {
            return redirect()->route('admin.jadwal.index')
                ->with('warning', 'Jadwal ' . $target->translatedFormat('F Y') . ' sudah tersedia atau tidak ada template.');
        }

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Berhasil generate {$generatedCount} jadwal untuk {$target->translatedFormat('F Y')}. Pasien sekarang sudah bisa melihat dan melakukan reservasi (status: draft, aktif tanggal 1).");
    }

    //dipakai pasien untuk reservasi
    public function getJadwalForPasien(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $tanggal = $request->input('tanggal');
        $carbonDate = Carbon::parse($tanggal);
        //apakah libur
        $hariLibur = HariLibur::where('tanggal', $tanggal)
        ->where('is_active', true)
        ->exists();
            //jika libur
            if ($hariLibur) {
                return response()->json([
                    'tanggal' => $tanggal,
                    'bulan' => $carbonDate->translatedFormat('F Y'),
                    'jadwal' => [],
                    'message' => 'Klinik tutup karena hari libur.'
                ]);
            }

        $this->generateHarianForBulan($carbonDate->month, $carbonDate->year);
        //ambil jadwal untuk tanggal tersebut
        $jadwal = Jadwal::with(['klinik', 'dokter'])
            ->where('tanggal', $tanggal)
            ->whereNotNull('tanggal')
            ->whereNull('deleted_at')
            ->orderBy('jam_mulai')
            ->get()
            ->map(function($item) {
                return [
                    'id_jadwal' => $item->id_jadwal,
                    'dokter' => $item->dokter->nama ?? '-',
                    'spesialisasi' => $item->dokter->spesialisasi ?? '-',
                    'klinik' => $item->klinik->nama_klinik ?? '-',
                    'hari' => $item->hari,
                    'tanggal' => $item->tanggal,
                    'jam_mulai' => $item->jam_mulai,
                    'jam_selesai' => $item->jam_selesai,
                    'is_aktif' => $item->is_aktif,
                ];
            });

        return response()->json([
            'tanggal' => $tanggal,
            'bulan' => $carbonDate->translatedFormat('F Y'),
            'jadwal' => $jadwal
        ]);
    }
    //aktifkan jadwal bulan depan secara manual
    public function aktifkanBulanDepan()
    {
        $bulanIni = now()->month;
        $tahunIni = now()->year;

        Jadwal::where('bulan', $bulanIni)
            ->where('tahun', $tahunIni)
            ->whereNull('deleted_at')
            ->delete();

        $nextMonth = now()->addMonthNoOverflow();
        $bulanDepan = $nextMonth->month;
        $tahunDepan = $nextMonth->year;

        Jadwal::where('bulan', $bulanDepan)
            ->where('tahun', $tahunDepan)
            ->where('is_aktif', false)
            ->update(['is_aktif' => true]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal bulan ini dipindahkan ke riwayat. Jadwal bulan depan telah diaktifkan.');
    }

    //form edit jadwal
    public function edit(Jadwal $jadwal)
    {
        $klinik = Klinik::orderBy('nama_klinik')->get(); //ambil data klinik dan dokter
        $dokter = Dokter::orderBy('nama')->get();
        return view('admin.jadwal.edit', compact('jadwal', 'klinik', 'dokter'));
    }

    //update data jadwal dan validasi
    public function update(Request $request, Jadwal $jadwal)
    {   //validasi input
        $validated = $request->validate([
            'id_klinik'   => 'required|exists:klinik,id_klinik',
            'id_dokter'   => 'required|exists:dokter,id_dokter',
            'hari'        => ['required', Rule::in(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'])],
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);
        //cek bentrok
        $exists = Jadwal::where('id_dokter', $validated['id_dokter'])
            ->where('hari', $validated['hari'])
            ->where('bulan', $jadwal->bulan)
            ->where('tahun', $jadwal->tahun)
            ->where('id_jadwal', '!=', $jadwal->id_jadwal)
            ->whereNull('tanggal')
            ->whereNull('deleted_at')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [
                    $validated['jam_mulai'],
                    $validated['jam_selesai']
                ])
                ->orWhereBetween('jam_selesai', [
                    $validated['jam_mulai'],
                    $validated['jam_selesai']
                ])
                ->orWhere(function ($q) use ($validated) {
                    $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                    ->where('jam_selesai', '>=', $validated['jam_selesai']);
                });
            })
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'jam_mulai' => 'Dokter sudah memiliki jadwal lain yang bentrok di jam tersebut.'
                ])
                ->withInput();
        }

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    //hapus data jadwal (soft delete)
    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }

    //restore jadwal yang sudah dihapus
    public function restore($id)
    { 
        $jadwal = Jadwal::withTrashed()->findOrFail($id);
        $jadwal->restore();
        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dipulihkan.');
    }
}