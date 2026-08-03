<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\Reservasi;
use App\Models\Jadwal;
use App\Models\Klinik;
use App\Models\PerubahanJadwalDokter;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PasienReservasiController extends Controller
{   //mengecek apakah pasien sudah memiliki profil dan reservasi aktif
    public function index()
    {   //ambil user login
        $user = Auth::user();
        //cari pasien dengan id user
        $pasien = Pasien::where('id_user', $user->id)->first();
        if (!$pasien) { //jika tidak ada
            return view('pasien.reservasi.form-pasien', compact('pasien'));
        }
        //cek reservasi aktif
        $cek = Reservasi::where('id_pasien', $pasien->id_pasien)
            ->whereIn('status', ['menunggu', 'tindakan', 'terdampak'])
            ->exists();
        //jika punya reservasi aktif
        if ($cek) {
            return redirect()->route('pasien.riwayat')
                ->with('error', 'Kamu masih punya reservasi aktif!');
        }

        return redirect()->route('pasien.pilih.klinik');
    }
    //update reservasi
    public function update(Request $request)
    { //validasi
        $request->validate([
            'nama' => 'required|string|max:100',
            'nik' => 'required|string|size:16|unique:pasien,nik',
            'jns_kelamin' => 'required|in:L,P',
            'tgl_lahir' => 'required|date',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string|max:255',
            'pekerjaan' => 'nullable|string|max:100',
            'riwayat_penyakit' => 'nullable|string',
        ]);
        //pastikan pasien yg update atau create adalah user yang sedang login
        //kalo ada update datanya kalo belum buat baru
        Auth::user()->pasien()->updateOrCreate(
            ['id_user' => Auth::id()],
            $request->only([
                'nama', 'nik', 'tgl_lahir', 'jns_kelamin',
                'no_hp', 'pekerjaan', 'alamat', 'riwayat_penyakit'
            ])
        );

        return redirect()->route('pasien.pilih-klinik')
            ->with('success', 'Data diri berhasil disimpan!');
    }

    //menampilkan daftar klinik yang tersedia
    public function pilihJadwal()
    {   //ambil pasien yang sedang login
        $pasien = Pasien::where('id_user', Auth::id())->firstOrFail();
        $kliniks = Klinik::all(); //ambil data klinik

        return view('pasien.reservasi.pilih-klinik', compact('pasien', 'kliniks'));
    }
    //menampilkan form reservasi
    public function formReservasi($id)
    {   //ambil data pasien login
        $pasien = Pasien::where('id_user', Auth::id())->firstOrFail();
        $cek = Reservasi::where('id_pasien', $pasien->id_pasien) //cek reservasi aktif
            ->whereIn('status', ['menunggu', 'tindakan', 'terdampak'])
            ->exists();
        //jika ada reservasi aktif arahkan ke riwayat
        if ($cek) {
            return redirect()->route('pasien.riwayat')
                ->with('error', 'Kamu masih punya reservasi aktif!');
        }
        //ambil data klinik
        $klinik = Klinik::findOrFail($id);
        //bulan ini, bulan depan
        $bulanIni = now();
        $bulanDepan = now()->addMonth();
        //generate jadwal otomatis
        $this->generateJadwalPasien($bulanIni->month, $bulanIni->year, 1); // aktif
        $this->generateJadwalPasien($bulanDepan->month, $bulanDepan->year, 0); // draft
        
        //Cek jadwal tersedia bulan ini aktif, bulan depan semua (termasuk draft)
        $jadwalTersedia = Jadwal::where('id_klinik', $id)
            ->where(function($q) use ($bulanIni, $bulanDepan) {
                // Template (tanpa tanggal) selalu tersedia
                $q->whereNull('tanggal')
                  // Atau jadwal harian bulan ini yang aktif
                  ->orWhere(function($sq) use ($bulanIni) {
                      $sq->whereBetween('tanggal', [
                          $bulanIni->copy()->startOfMonth()->toDateString(),
                          $bulanIni->copy()->endOfMonth()->toDateString()
                      ])->where('is_aktif', 1);
                  })
                  // Atau jadwal harian bulan depan (termasuk draft)
                  ->orWhereBetween('tanggal', [
                      $bulanDepan->copy()->startOfMonth()->toDateString(),
                      $bulanDepan->copy()->endOfMonth()->toDateString()
                  ]);
            })
            ->whereNull('deleted_at')
            ->exists();
        ////maksimal tanggal reservasi 7 hari kedepan
        $maxTanggal = now()->addDays(7)->toDateString();
        return view('pasien.reservasi.form-reservasi', compact('klinik', 'jadwalTersedia', 'maxTanggal'));
    }
    //mengubah template jadwal mingguan menjadi jadwal harian
    private function generateJadwalPasien($bulan, $tahun, $isAktif = 0)
    {
        try { //buat objek tgl bulan targer
            $target = Carbon::create((int)$tahun, (int)$bulan, 1);
            //cek apakah jadwal bulan sudah pernah dibuat
            $sudahAda = Jadwal::where('bulan', $target->month)
                ->where('tahun', $target->year)
                ->whereNotNull('tanggal')
                ->whereNull('deleted_at')
                ->exists();
            //jika sudah, stop proses
            if ($sudahAda) {
                return 0;
            }
            //ambil template jadwal
            $templates = Jadwal::with([
                    'klinik',
                    'dokter' => function ($q) {
                        $q->whereNull('deleted_at');
                    }
                ])
                ->whereHas('dokter', function ($q) {
                    $q->whereNull('deleted_at');
                })                
                ->whereNull('tanggal')  // Hanya template (tanpa tanggal)
                ->whereNull('deleted_at')
                ->get();
            //kalo template kosong tidak ada jadwal yg bisa dibuat
            if ($templates->isEmpty()) {
                return 0;
            }
            //mapping hari
            $hariMapping = [
                'senin' => 1,
                'selasa' => 2,
                'rabu' => 3,
                'kamis' => 4,
                'jumat' => 5,
                'sabtu' => 6,
                'minggu' => 0,
            ];
            //set up range bulan
            $startOfMonth = $target->copy()->startOfMonth();
            $endOfMonth = $target->copy()->endOfMonth();
            $generatedCount = 0;
            
            DB::beginTransaction();
            //loop semua tanggal dalam sebulan
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dayOfWeek = $date->dayOfWeek; //ambil hari dari tanggal
                //loop semu template
                foreach ($templates as $template) {
                    $templateHari = strtolower($template->hari);
                    
                    if (!isset($hariMapping[$templateHari])) {
                        continue;
                    }
                    //cocokan hari
                    if ($hariMapping[$templateHari] == $dayOfWeek) {
                        $exists = Jadwal::where('tanggal', $date->toDateString())
                            ->where('id_klinik', $template->id_klinik)
                            ->where('id_dokter', $template->id_dokter)
                            ->where('jam_mulai', $template->jam_mulai)
                            ->whereNull('deleted_at')
                            ->exists(); //cek duplikat
                        // generate harian jika tidak ada duplikat
                        if (!$exists) {
                            Jadwal::create([
                                'id_klinik'   => $template->id_klinik,
                                'id_dokter'   => $template->id_dokter,
                                'hari'        => $templateHari,
                                'tanggal'     => $date->toDateString(),
                                'bulan'       => $target->month,
                                'tahun'       => $target->year,
                                'jam_mulai'   => $template->jam_mulai,
                                'jam_selesai' => $template->jam_selesai,
                                'is_aktif'    => $isAktif,
                            ]);
                            $generatedCount++; //hitung jadwal yg berhasi dibuat
                        }
                    }
                }
            }
            
            DB::commit();
            return $generatedCount;
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Generate jadwal pasien gagal: ' . $e->getMessage());
            return -1;
        }
    }
    //mengambil jadwal yang tersedia pada tanggal tertentu
    public function jadwalAvailable(Request $request)
    {
        try { //validasi input
            $request->validate([
                'tanggal' => [
                    'required',
                    'date',
                    'after_or_equal:today', //validasi tanggal
                    function ($attribute, $value, $fail) {
                        $maxDate = now()->addDays(7)->format('Y-m-d');
                        if ($value > $maxDate) {
                            $fail('Tanggal maksimal 7 hari ke depan.');
                        }
                    },
                ],
                'klinik_id' => 'required|exists:klinik,id_klinik'
            ]);

            $carbonDate = Carbon::parse($request->tanggal);
            $maxDate = now()->addDays(7);

            if ($carbonDate->gt($maxDate)) {
                return response()->json([
                    'error' => 'Tanggal maksimal 7 hari ke depan.'
                ], 422);
            }
            //cek hari libur
            $isHariLibur = HariLibur::whereDate('tanggal', $carbonDate->toDateString())->exists();
            if ($isHariLibur) {
                return response()->json([]);
            }
            $hariInggris = $carbonDate->format('l');
            $mapHari = [
                'Monday' => 'senin', 'Tuesday' => 'selasa', 'Wednesday' => 'rabu',
                'Thursday' => 'kamis', 'Friday' => 'jumat', 'Saturday' => 'sabtu', 'Sunday' => 'minggu',
            ];
            $hari = $mapHari[$hariInggris] ?? strtolower($hariInggris);

            // Generate jadwal untuk bulan yang dipilih jika belum ada
            if (!$carbonDate->isCurrentMonth()) {
                $this->generateJadwalPasien($carbonDate->month, $carbonDate->year, 0);
            }
            //cek hari ini apa bukan
            $isToday = $carbonDate->isToday();
            $currentTime = now();

            //Bulan ini hanya tampilkan yang aktif, bulan lain tampilkan yang di draft
            $jadwalsHarian = Jadwal::with('dokter')
                ->where('id_klinik', $request->klinik_id)
                ->whereDate('tanggal', $carbonDate->toDateString())
                ->whereNull('deleted_at')
                ->where('is_aktif', $carbonDate->isCurrentMonth() ? 1 : 0)
                ->orderBy('jam_mulai')
                ->get();

            $jadwals = collect();
            //jika jadwal harian tidak kosong maka jadwal tampil
            if ($jadwalsHarian->isNotEmpty()) {
                $jadwals = $jadwalsHarian;
            } else {
                // Fallback ke template
                $jadwals = Jadwal::with('dokter')
                    ->where('id_klinik', $request->klinik_id)
                    ->where('hari', $hari)
                    ->whereNull('tanggal')
                    ->where('is_aktif', $carbonDate->isCurrentMonth() ? 1 : 0)
                    ->whereNull('deleted_at')
                    ->orderBy('jam_mulai')
                    ->get();
            }
            $result = [];
            //loop semua jadwal
            foreach ($jadwals as $jadwal) {
                if (!$jadwal->dokter) {
                    continue;
                }
                // Cek perubahan jadwal dokter (libur/pengganti/tambahan)
                $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
                    ->where('dokter_id', $jadwal->id_dokter)
                    ->whereDate('tanggal', $carbonDate->toDateString())
                    ->where('status', 'aktif')
                    ->first();

                // Jika dokter libur, skip jadwal ini
                if ($perubahan && $perubahan->jenis_perubahan == 'libur') {
                    continue;
                }
                //simpan data dokter
                $dokter = $jadwal->dokter;
                $jamMulai = $jadwal->jam_mulai;
                $jamSelesai = $jadwal->jam_selesai;

                // Terapkan perubahan jadwal jika ada
                if ($perubahan) {
                    if ($perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
                        $dokter = $perubahan->dokterPengganti;
                    }
                    if ($perubahan->jenis_perubahan == 'tambahan') {
                        $jamMulai = $perubahan->jam_mulai;
                        $jamSelesai = $perubahan->jam_selesai;
                    }
                }

                $start = Carbon::parse($jamMulai);
                $end = Carbon::parse($jamSelesai);
                $slot = 1;
                //buat slot jam
                while ($start < $end) {
                    $next = $start->copy()->addHour();
                    // Skip istirahat jam 12
                    if ($start->format('H:i') === '12:00') {
                        $start = $next;
                        continue;
                    }
                    // Skip slot yang sudah lewat (hari ini)
                    if ($isToday) {
                        $slotTime = $carbonDate->copy()->setTimeFrom($start);
                        if ($currentTime->greaterThan($slotTime)) {
                            $start = $next;
                            $slot++;
                            continue;
                        }
                    }

                    $jamSlot = $start->format('H:i') . '-' . $next->format('H:i');
                    $jamDisplay = $start->format('H:i');
                    $jamSelesaiDisplay = $next->format('H:i');

                    // Cek apakah slot sudah dibooking
                    $dipakai = Reservasi::where('id_jadwal', $jadwal->id_jadwal)
                        ->where('tgl_reservasi', $carbonDate->toDateString())
                        ->where('jam_reservasi', $jamSlot)
                        ->whereIn('status', ['menunggu', 'tindakan', 'terdampak'])
                        ->exists();
                    //jika dipakai slot hilang dicoret
                    if ($dipakai) {
                        $start = $next;
                        $slot++;
                        continue;
                    }
                    //masuk ke  result
                    $result[] = [
                        'id_jadwal' => $jadwal->id_jadwal,
                        'dokter_nama' => $dokter->nama ?? 'Dokter',
                        'dokter_spesialisasi' => $dokter->spesialisasi ?? '-',
                        'jam_mulai' => $jamDisplay,
                        'jam_selesai' => $jamSelesaiDisplay,
                        'jam' => $jamSlot,
                        'antrean' => $slot,
                        'is_template' => is_null($jadwal->tanggal),
                        'tanggal' => $carbonDate->toDateString(),
                        'ada_perubahan' => $perubahan ? true : false,
                        'jenis_perubahan' => $perubahan->jenis_perubahan ?? null,
                    ];
                    $start = $next;
                    $slot++;
                }
            }

            return response()->json($result);

        } catch (\Throwable $e) {
            \Log::error('JADWAL AVAILABLE ERROR: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
    //menyimpan dan validasi
    public function store(Request $request)
    {   //validasi
        $request->validate([
            'id_jadwal' => 'required',
            'tgl_reservasi' => [
                'required',
                'date',
                'after_or_equal:today', //hari ini dan hari ke depan
                function ($attribute, $value, $fail) {
                    $maxDate = now()->addDays(7)->format('Y-m-d');
                    if ($value > $maxDate) {
                        $fail('Reservasi maksimal 7 hari ke depan.');
                    }
                }
            ],
            'keluhan' => 'nullable|string',
        ]);
        //validasi format jadwal
        if (!str_contains($request->id_jadwal, '|')) {
            return back()->with('error', 'Format jadwal tidak valid!');
        }
        //split data
        [$id_jadwal, $jamRange, $antrean] = explode('|', $request->id_jadwal);
        $jam = $jamRange;
        //ambil pasien login
        $pasien = Pasien::where('id_user', Auth::id())->firstOrFail();

        // Cek apakah pasien masih punya reservasi aktif
        $cekPasien = Reservasi::where('id_pasien', $pasien->id_pasien)
            ->whereIn('status', ['menunggu', 'tindakan'])
            ->exists();
        if ($cekPasien) {
            return back()->with('error', 'Kamu masih punya reservasi aktif!');
        }

        // Cek slot tersedia
        $cekSlot = Reservasi::where('id_jadwal', $id_jadwal)
            ->where('tgl_reservasi', $request->tgl_reservasi)
            ->where('jam_reservasi', $jam)
            ->whereIn('status', ['menunggu', 'tindakan'])
            ->exists();
        //jika slot sudah isi
        if ($cekSlot) {
            return back()->with('error', 'Slot sudah dibooking!');
        }
        //simpan reservasi
        $reservasi = Reservasi::create([
            'id_pasien'      => $pasien->id_pasien,
            'id_jadwal'      => $id_jadwal,
            'tgl_reservasi'  => $request->tgl_reservasi,           
            'jam_reservasi'  => $jam,
            'antrean'        => $antrean,
            'status'         => 'menunggu',
            'keluhan'        => $request->keluhan,
        ]);
        //ambil data engkap biar bisa dipakai di view
        $reservasi->load(['jadwal.klinik', 'jadwal.dokter', 'pasien']);

        return redirect()->route('pasien.riwayat')
            ->with('success', 'Reservasi berhasil dibuat!')
            ->with('reservasiBaru', $reservasi);
    }
    //form tambah
    public function create()
    {   //ambil pasien login
        $pasien = Auth::user()->pasien;
        return view('pasien.reservasi.index', compact('pasien'));
    }
    //menampilkan seluruh riwayat reservasi pasien
    public function riwayat()
    {   //ambil pasien yang login
        $pasien = Pasien::where('id_user', Auth::id())->firstOrFail();

        // load relasi lengkap agar pasien bisa lihat perubahan jadwal & dokter pengganti
        $reservasi = $pasien->reservasi()
            ->with([
                'pasien',
                'jadwal.dokter',
                'jadwal.klinik',
                'jadwal.perubahanJadwalAktif',
                'jadwal.perubahanJadwalAktif.dokterPengganti'
            ])
            ->whereHas('jadwal', function ($q) {
                $q->whereNull('deleted_at'); //belum dihapus
            })
            ->latest()
            ->paginate(10);

        return view('pasien.reservasi.riwayat', compact('reservasi'));
    }
    //batalkan reservasi
    public function destroy($id)
    {      //ambil pasien yang login
        $pasien = Pasien::where('id_user', Auth::id())->firstOrFail();
        //ambil reservasi
        $reservasi = $pasien->reservasi()
            ->where('id_reservasi', $id)
            ->firstOrFail();

        // Hanya bisa batal jika status masih menunggu
        if ($reservasi->status !== 'menunggu') {
            return back()->with('error', 'Tidak bisa dibatalkan!');
        }

        // Cek batas waktu batal (min 12 jam sebelum jadwal)
        $batasBatal = Carbon::parse($reservasi->tgl_reservasi . ' ' . $reservasi->jam_reservasi)->subHours(12);
        //jika sudah lewat batas batal
        if (now()->greaterThanOrEqualTo($batasBatal)) {
            return back()->with('error', 'Reservasi tidak bisa dibatalkan karena kurang dari 12 jam dari jadwal kunjungan.');
        }

        $reservasi->update(['status' => 'dibatalkan']);

        return back()->with('success', 'Reservasi dibatalkan.');
    }
}