<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerubahanJadwalDokter;
use App\Models\Dokter;
use App\Models\Jadwal;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\NotifikasiService;

class PerubahanJadwalDokterController extends Controller
{   //menampilkan semua data perubahan jadwal
    public function index()
    {
        $klinikId = auth()->user()->id_klinik; //id_klinik user login
        PerubahanJadwalDokter::where('status', 'aktif')
        ->whereDate('tanggal', '<', now()->toDateString())
        ->update(['status' => 'selesai']);

        $perubahan = PerubahanJadwalDokter::with(['dokter', 'dokterPengganti'])
            ->whereHas('dokter.jadwal', function($q) use ($klinikId) {
                $q->where('id_klinik', $klinikId)
                  ->whereNull('deleted_at');
            })
            ->latest()
            ->paginate(10);

        return view('admin.perubahan-jadwal.index', compact('perubahan'));
    }
    //form tambah perubahan jadwal
    public function create()
    {
        $klinikId = auth()->user()->id_klinik;
        return view('admin.perubahan-jadwal.create', compact('klinikId'));
    }
    //simpan data dan validasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dokter_id' => 'required|exists:dokter,id_dokter',
            'dokter_pengganti_id' => 'nullable|exists:dokter,id_dokter|different:dokter_id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jenis_perubahan' => 'required|in:libur,pengganti,tambahan',
            'jam_mulai' => 'nullable|required_if:jenis_perubahan,tambahan',
            'jam_selesai' => 'nullable|required_if:jenis_perubahan,tambahan|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500',
            'klinik_id' => 'required|exists:klinik,id_klinik',
        ]);

        $tanggalParsed = Carbon::parse($validated['tanggal']);
        //memastikan dokter punya jadwal di tanggal tersebut 
        $jadwalDokter = $this->getJadwalDokter(
            $validated['dokter_id'],
            $tanggalParsed,
            $validated['klinik_id']
        );

        if (!$jadwalDokter) {
            return back()->withInput()->with('error', 'Dokter tidak memiliki jadwal pada tanggal/hari tersebut.');
        }
        //hanya boleh 1 perubahan jadwal aktif per dokter per tanggal
        $perubahanAktif = PerubahanJadwalDokter::where('dokter_id', $validated['dokter_id'])
            ->whereDate('tanggal', $tanggalParsed->toDateString())
            ->where('status', 'aktif')
            ->exists();

        if ($perubahanAktif) {
            return back()->withInput()->with('error', 'Dokter sudah memiliki perubahan jadwal aktif pada tanggal ini.');
        }
        // jika jenis perubahan pengganti, sistem cek
        if ($validated['jenis_perubahan'] === 'pengganti') {
            if (!$validated['dokter_pengganti_id']) {
                return back()->withInput()->withErrors([
                    'dokter_pengganti_id' => 'Dokter pengganti wajib diisi.'
                ]);
            }
            $hari = strtolower($tanggalParsed->format('l'));
            $mapHari = [
                'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
            ];
            $hariIndo = $mapHari[$hari] ?? null;
            // menampilkan dokter yang avaible di tanggal tersebut (tidak punya jadwal & tidak punya perubahan jadwal lain)
            $punyaJadwal = Jadwal::where('id_dokter', $validated['dokter_pengganti_id'])
                ->where(function ($q) use ($tanggalParsed, $hariIndo) {
                    $q->whereDate('tanggal', $tanggalParsed->toDateString())
                      ->orWhere(function ($query) use ($hariIndo) {
                          $query->where('hari', $hariIndo)->whereNull('tanggal');
                      });
                })
                ->whereNull('deleted_at')
                ->exists();
            if ($punyaJadwal) {
                return back()->withInput()->with('error', 'Dokter pengganti sudah memiliki jadwal pada tanggal tersebut.');
            }
            // cek dokter pengganti sudah menjadi pengganti di perubahan jadwal lain
            $penggantiBentrok = PerubahanJadwalDokter::where('dokter_pengganti_id', $validated['dokter_pengganti_id'])
                ->whereDate('tanggal', $tanggalParsed->toDateString())
                ->where('status', 'aktif')
                ->exists();
            if ($penggantiBentrok) {
                return back()->withInput()->with('error', 'Dokter pengganti sudah dijadwalkan sebagai pengganti di tanggal ini.');
            }
            //cek dokter pengganti apakah libur di tanggal tersebut
            $penggantiLibur = PerubahanJadwalDokter::where('dokter_id', $validated['dokter_pengganti_id'])
                ->whereDate('tanggal', $tanggalParsed->toDateString())
                ->where('jenis_perubahan', 'libur')
                ->where('status', 'aktif')
                ->exists();

            if ($penggantiLibur) {
                return back()->withInput()->with('error', 'Dokter pengganti sedang libur pada tanggal ini.');
            }
        }

        if ($validated['jenis_perubahan'] === 'libur') {
            $validated['dokter_pengganti_id'] = null;
        }

        if ($validated['jenis_perubahan'] === 'tambahan') {
            if (!$validated['jam_mulai'] || !$validated['jam_selesai']) {
                return back()->withInput()->with('error', 'Jam mulai dan jam selesai harus diisi untuk jadwal tambahan.');
            }
        }

        try {
            DB::beginTransaction(); //simpan data

            PerubahanJadwalDokter::create([
                'dokter_id' => $validated['dokter_id'],
                'dokter_pengganti_id' => $validated['dokter_pengganti_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'jenis_perubahan' => $validated['jenis_perubahan'],
                'jam_mulai' => $validated['jam_mulai'] ?? null,
                'jam_selesai' => $validated['jam_selesai'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => 'aktif',
            ]);
            //cari reservasi terdampak
            $queryReservasi = Reservasi::whereDate('tgl_reservasi', $validated['tanggal'])
                ->whereHas('jadwal', function ($q) use ($validated) {
                    $q->where('id_dokter', $validated['dokter_id']);
                })
                ->where('status', 'menunggu');

            if (!empty($validated['jam_mulai'])) {
                $jamMulaiPerubahan = $validated['jam_mulai'];
                $queryReservasi->whereRaw("TIME(jam_reservasi) >= TIME(?)", [$jamMulaiPerubahan]); // reservasi setelah jam tersebut
            }

            $reservasis = $queryReservasi->get();
            // kirim notifikasi ke pasien yang reservasinya terdampak
            foreach ($reservasis as $reservasi) {
                $reservasi->update(['status' => 'terdampak']); // ubah statusnya jd terdampak
                app(NotifikasiService::class)->notifReservasiTerdampak($reservasi);
            }

            DB::commit();

            return redirect()->route('admin.perubahan-jadwal.index')->with('success', 'Perubahan jadwal berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
    //form edit perubahan jadwal
    public function edit($id)
    {
        $item = PerubahanJadwalDokter::findOrFail($id);
        $klinikId = auth()->user()->id_klinik;

        $dokter = Dokter::orderBy('nama')->get();

        return view('admin.perubahan-jadwal.edit', compact('item', 'dokter', 'klinikId'));
    }
    //update data perubahan jadwal dan validasi
    public function update(Request $request, $id)
    {
        $perubahan = PerubahanJadwalDokter::findOrFail($id);
        //perubahan yg sudah selesai atau dibatalkan tidak bisa diubah
        if (in_array($perubahan->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Tidak bisa mengubah perubahan jadwal yang sudah selesai atau dibatalkan.');
        }

        $validated = $request->validate([
            'dokter_id' => 'required|exists:dokter,id_dokter',
            'dokter_pengganti_id' => 'nullable|exists:dokter,id_dokter|different:dokter_id',
            'tanggal' => 'required|date',
            'jenis_perubahan' => 'required|in:libur,pengganti,tambahan',
            'jam_mulai' => 'nullable|required_if:jenis_perubahan,tambahan',
            'jam_selesai' => 'nullable|required_if:jenis_perubahan,tambahan|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500',
            'status' => 'required|in:aktif,selesai,dibatalkan',
            'klinik_id' => 'required|exists:klinik,id_klinik',
        ]);

        $tanggalParsed = Carbon::parse($validated['tanggal']);
        //cek jadwal dokter di tanggal tersebut
        $jadwalDokter = $this->getJadwalDokter(
            $validated['dokter_id'],
            $tanggalParsed,
            $validated['klinik_id']
        );

        if (!$jadwalDokter) {
            return back()->withInput()->with('error', 'Dokter tidak memiliki jadwal pada tanggal/hari tersebut.');
        }
        //cek perubahan jadwal aktif lain untuk dokter ini di tanggal yang sama
        $perubahanAktif = PerubahanJadwalDokter::where('dokter_id', $validated['dokter_id'])
            ->whereDate('tanggal', $tanggalParsed->toDateString())
            ->where('status', 'aktif')
            ->where('id_perubahan', '!=', $id)
            ->exists();

        if ($perubahanAktif) {
            return back()->withInput()->with('error', 'Sudah ada perubahan jadwal aktif lain untuk dokter ini di tanggal yang sama.');
        }
    
        if ($validated['jenis_perubahan'] === 'pengganti') {
            if (!$validated['dokter_pengganti_id']) {
                return back()->withInput()->withErrors([
                    'dokter_pengganti_id' => 'Dokter pengganti wajib diisi.'
                ]);
            }

            $hari = strtolower($tanggalParsed->format('l'));
            $mapHari = [
                'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
            ];
            $hariIndo = $mapHari[$hari] ?? null;
            //cek dokter pengganti punya jadwal di tanggal tersebut
            $punyaJadwal = Jadwal::where('id_dokter', $validated['dokter_pengganti_id'])
                ->where(function ($q) use ($tanggalParsed, $hariIndo) {
                    $q->whereDate('tanggal', $tanggalParsed->toDateString())
                      ->orWhere(function ($query) use ($hariIndo) {
                          $query->where('hari', $hariIndo)->whereNull('tanggal');
                      });
                })
                ->whereNull('deleted_at')
                ->exists();

            if ($punyaJadwal) {
                return back()->withInput()->with('error', 'Dokter pengganti sudah memiliki jadwal pada tanggal tersebut.');
            }
            //cek dokter pengganti bentrok jadwal
            $penggantiBentrok = PerubahanJadwalDokter::where('dokter_pengganti_id', $validated['dokter_pengganti_id'])
                ->whereDate('tanggal', $tanggalParsed->toDateString())
                ->where('status', 'aktif')
                ->where('id_perubahan', '!=', $id)
                ->exists();

            if ($penggantiBentrok) {
                return back()->withInput()->with('error', 'Dokter pengganti sudah dijadwalkan sebagai pengganti di tanggal ini.');
            }
            //cek dokter pengganti apakah libur di tanggal tersebut
            $penggantiLibur = PerubahanJadwalDokter::where('dokter_id', $validated['dokter_pengganti_id'])
                ->whereDate('tanggal', $tanggalParsed->toDateString())
                ->where('jenis_perubahan', 'libur')
                ->where('status', 'aktif')
                ->where('id_perubahan', '!=', $id)
                ->exists();

            if ($penggantiLibur) {
                return back()->withInput()->with('error', 'Dokter pengganti sedang libur pada tanggal ini.');
            }
        }

        if ($validated['jenis_perubahan'] === 'libur') {
            $validated['dokter_pengganti_id'] = null;
        }

        if ($validated['jenis_perubahan'] === 'tambahan') {
            if (!$validated['jam_mulai'] || !$validated['jam_selesai']) {
                return back()->withInput()->with('error', 'Jam mulai dan jam selesai harus diisi untuk jadwal tambahan.');
            }
        }

        try {
            DB::beginTransaction(); //simpan data

            $perubahan->update([
                'dokter_id' => $validated['dokter_id'],
                'dokter_pengganti_id' => $validated['dokter_pengganti_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'jenis_perubahan' => $validated['jenis_perubahan'],
                'jam_mulai' => $validated['jam_mulai'] ?? null,
                'jam_selesai' => $validated['jam_selesai'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()->route('admin.perubahan-jadwal.index')->with('success', 'Perubahan jadwal berhasil diupdate.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }
    }
    //hapus data perubahan jadwal
    public function destroy($id)
    {
        $perubahan = PerubahanJadwalDokter::findOrFail($id);

        try {
            $perubahan->delete(); 
            return redirect()->route('admin.perubahan-jadwal.index')->with('success', 'Perubahan jadwal berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
    // menampilkan dokter yg bisa dipilih untuk perubahan jadwal
    public function dokterAvailable(Request $request)
    {
        try {
            $validated = $request->validate([ // memiliki jadwal pada tanggal tersebut & tidak punya perubahan jadwal lain yang bentrok
                'tanggal' => 'required|date|after_or_equal:today',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        $tanggalParsed = Carbon::parse($validated['tanggal']);
        $hari = strtolower($tanggalParsed->format('l'));

        $mapHari = [
            'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
            'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
        ];
        $hariIndo = $mapHari[$hari] ?? null;

        $klinikId = auth()->user()->id_klinik;
        $excludeId = $request->input('exclude_perubahan_id');

        try {
            //jadwal harian
            $dokterHarianIds = Jadwal::whereDate('tanggal', $tanggalParsed->toDateString())
                ->whereDate('tanggal', '>=', Carbon::today())
                ->where('id_klinik', $klinikId)
                ->whereNull('deleted_at')
                ->pluck('id_dokter');

            // jadwal template
            $dokterTemplateIds = collect();
            
            if ($tanggalParsed->gte(Carbon::today()->startOfMonth())) {
                $bulanSekarang = Carbon::today()->month;
                $tahunSekarang = Carbon::today()->year;
                $bulanDipilih = $tanggalParsed->month;
                $tahunDipilih = $tanggalParsed->year;

                // Cek apakah bulan yang dipilih bulan ini atau bulan depan
            $isBulanIni = $tanggalParsed->isSameMonth(Carbon::today());

                $dokterTemplateIds = Jadwal::where('hari', $hariIndo)
                    ->whereNull('tanggal')
                    ->where('id_klinik', $klinikId)
                    ->whereNull('deleted_at')
                    ->where(function($q) use ($isBulanIni, $bulanSekarang, $tahunSekarang, $bulanDipilih, $tahunDipilih) {
                        if ($isBulanIni) {
                            // Bulan ini tampilin yang AKTIF
                            $q->where('is_aktif', 1)
                            ->where('bulan', $bulanSekarang)
                            ->where('tahun', $tahunSekarang);
                        } else {
                            // Bulan depan tampilin yang DRAFT untuk bulan itu
                            $q->where('is_aktif', 0)
                            ->where('bulan', $bulanDipilih)
                            ->where('tahun', $tahunDipilih);
                        }
                    })
                    ->pluck('id_dokter');
            }

            $dokterIds = $dokterHarianIds->merge($dokterTemplateIds)->unique()->values();

            //dokter yg sudah punya perubahan tidak ditampilkan
            $queryBentrok = PerubahanJadwalDokter::whereDate('tanggal', $tanggalParsed->toDateString())
                ->whereIn('status', ['aktif', 'draft'])
                ->whereNotNull('dokter_id');

            if ($excludeId) {
                $queryBentrok->where('id_perubahan', '!=', $excludeId);
            }

            $dokterBentrokIds = $queryBentrok->pluck('dokter_id')->unique()->toArray();

            // hanya dokter yg tersedia yg ditampilkan
            $dokterAvailable = Dokter::whereIn('id_dokter', $dokterIds)
                ->whereNotIn('id_dokter', $dokterBentrokIds)
                ->orderBy('nama')
                ->get(['id_dokter', 'nama', 'spesialisasi']);

            return response()->json($dokterAvailable);

        } catch (\Exception $e) {
            \Log::error('Error di dokterAvailable: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //menampilkan dokter pengganti
    public function dokterPengganti(Request $request)
    {
        try { //validasi input tanggal dan dokter_id
            $validated = $request->validate([
                'tanggal' => 'required|date|after_or_equal:today',
                'dokter_id' => 'required|exists:dokter,id_dokter',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        $tanggalParsed = Carbon::parse($validated['tanggal']);
        $dokterId = $validated['dokter_id'];
        $excludeId = $request->input('exclude_perubahan_id');

        $hari = strtolower($tanggalParsed->format('l'));
        $mapHari = [
            'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
            'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
        ];
        $hariIndo = $mapHari[$hari] ?? null;

        try {
            // sudah punya jadwal harian
            $dokterJadwalHarianIds = Jadwal::whereDate('tanggal', $tanggalParsed->toDateString())
                ->whereDate('tanggal', '>=', Carbon::today())
                ->whereNull('deleted_at')
                ->pluck('id_dokter');

            //sudah punya template
            $dokterTemplateIds = collect();
            
            if ($tanggalParsed->gte(Carbon::today()->startOfMonth())) {
    
                $bulanSekarang = Carbon::today()->month;
                $tahunSekarang = Carbon::today()->year;
                $bulanDipilih = $tanggalParsed->month;
                $tahunDipilih = $tanggalParsed->year;

                $isBulanIni = ($bulanDipilih == $bulanSekarang && $tahunDipilih == $tahunSekarang);

                $dokterTemplateIds = Jadwal::where('hari', $hariIndo)
                    ->whereNull('tanggal')
                    ->whereNull('deleted_at')
                    ->where(function($q) use ($isBulanIni, $bulanSekarang, $tahunSekarang, $bulanDipilih, $tahunDipilih) {
                        if ($isBulanIni) {
                            $q->where('is_aktif', 1)
                            ->where('bulan', $bulanSekarang)
                            ->where('tahun', $tahunSekarang);
                        } else {
                            $q->where('is_aktif', 0)
                            ->where('bulan', $bulanDipilih)
                            ->where('tahun', $tahunDipilih);
                        }
                    })
                    ->pluck('id_dokter');
            }

            // cari dokter bentrok karena perubahan jadwal
            $queryBentrok = PerubahanJadwalDokter::whereDate('tanggal', $tanggalParsed->toDateString())
                ->whereIn('status', ['aktif', 'draft']);

            if ($excludeId) {
                $queryBentrok->where('id_perubahan', '!=', $excludeId);
            }

            $dokterBentrokIds = $queryBentrok->get()
                ->pluck('dokter_id')
                ->merge(
                    PerubahanJadwalDokter::whereDate('tanggal', $tanggalParsed->toDateString())
                        ->whereIn('status', ['aktif', 'draft'])
                        ->whereNotNull('dokter_pengganti_id')
                        ->when($excludeId, function($q) use ($excludeId) {
                            $q->where('id_perubahan', '!=', $excludeId);
                        })
                        ->pluck('dokter_pengganti_id')
                )
                ->filter()
                ->unique()
                ->toArray();
            //gabung semua dokter tidak tersedia
            $dokterTidakAvailable = $dokterJadwalHarianIds
                ->merge($dokterTemplateIds)
                ->merge($dokterBentrokIds)
                ->unique()
                ->toArray();

            // menampilkan sisa dokter (avaible di tanggal tersebut)
            $dokterAvailable = Dokter::whereNotIn('id_dokter', $dokterTidakAvailable)
                ->where('id_dokter', '!=', $dokterId)
                ->orderBy('nama')
                ->get(['id_dokter', 'nama', 'spesialisasi']);

            return response()->json($dokterAvailable);

        } catch (\Exception $e) {
            \Log::error('Error di dokterPengganti: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    //mencari apakah dokter memiliki jadwal di tanggal tersebut
    private function getJadwalDokter($dokterId, Carbon $tanggal, $klinikId): ?Jadwal
    {
        $hari = strtolower($tanggal->format('l'));
        $mapHari = [
            'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
            'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu',
        ];
        $hariIndo = $mapHari[$hari] ?? null;

        // Cek jadwal harian untuk tanggal ini
        $jadwalHarian = Jadwal::where('id_dokter', $dokterId)
            ->where('id_klinik', $klinikId)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->whereDate('tanggal', '>=', Carbon::today())
            ->whereNull('deleted_at')
            ->first();

        if ($jadwalHarian) {
            return $jadwalHarian;
        }

        // Kalo tanggal sudah lewatm gausha cek template
        if ($tanggal->lt(Carbon::today()->startOfMonth())) {
            return null;
        }

        // Cek template sesuai bulan
        $bulanSekarang = Carbon::today()->month;
        $tahunSekarang = Carbon::today()->year;
        $bulanDipilih = $tanggal->month;
        $tahunDipilih = $tanggal->year;

        $isBulanIni = ($bulanDipilih == $bulanSekarang && $tahunDipilih == $tahunSekarang);

        return Jadwal::where('id_dokter', $dokterId)
            ->where('id_klinik', $klinikId)
            ->where('hari', $hariIndo)
            ->whereNull('tanggal')
            ->whereNull('deleted_at')
            ->where(function($q) use ($isBulanIni, $bulanSekarang, $tahunSekarang, $bulanDipilih, $tahunDipilih) {
                if ($isBulanIni) {
                    // Bulan ini ambil yang aktif
                    $q->where('is_aktif', 1)
                    ->where('bulan', $bulanSekarang)
                    ->where('tahun', $tahunSekarang);
                } else {
                    // Bulan depan ambil yang draft untuk bulan itu
                    $q->where('is_aktif', 0)
                    ->where('bulan', $bulanDipilih)
                    ->where('tahun', $tahunDipilih);
                }
            })
            ->first();
    }
}