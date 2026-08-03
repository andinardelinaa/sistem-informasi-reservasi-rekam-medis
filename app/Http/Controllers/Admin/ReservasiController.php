<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Klinik;
use App\Models\Jadwal;
use App\Models\Pasien;
use App\Models\PerubahanJadwalDokter;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservasiExport;
use App\Exports\ReservasiWordExport;
use PhpOffice\PhpWord\IOFactory;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ReservasiNotificationService;
use App\Services\NotifikasiService;


class ReservasiController extends Controller
{ //mapping hari
    protected $hariMapping = [
        'senin' => 1,
        'selasa' => 2,
        'rabu' => 3,
        'kamis' => 4,
        'jumat' => 5,
        'sabtu' => 6,
        'minggu' => 0,
    ];
    //memasukan service ke dalam class secara otomatis
    public function __construct(
        protected ReservasiNotificationService $notif,
        protected NotifikasiService $notifikasi,
    ) {}
    //ambil id klinik milik admi yang sedang login
    protected function getAdminKlinikId()
    {
        $user = auth()->user();
        if ($user->id_klinik) {
            return $user->id_klinik;
        }
        abort(403, 'Admin belum terhubung ke klinik.');
    }
    //ambil nama klinik dari admin yang sedang login
    protected function getAdminKlinikNama()
    {
        $user = auth()->user();
        return $user->klinik->nama_klinik ?? 'Klinik';
    }
    //mengambil reservasi dengan relasinya yaitu pasien, jadwal, klinik, dokter
    protected function baseQuery($klinikId)
    {  
        return Reservasi::with([
                'pasien',
                'jadwal',
                'jadwal.klinik',
                'jadwal.dokter'
            ])
            ->whereHas('jadwal', function ($q) use ($klinikId) {
                $q->where('id_klinik', $klinikId);
            });
    }
    // menampilkan data reservasi pada klinik yg di kelola admin
    public function index()
    {
        $klinikId = $this->getAdminKlinikId(); // ambil id_klinik dan nama klinik admin yang login
        $klinikNama = $this->getAdminKlinikNama();
        // membuat query dasar
        $query = $this->baseQuery($klinikId);
        // ambil data reservasi
        $reservasi = (clone $query)->latest()->paginate(10);
        // refresh setiap item
        $reservasi->getCollection()->transform(function ($item) {
            return $item->fresh([
                'pasien',
                'jadwal',
                'jadwal.dokter',
                'jadwal.klinik'
            ]);
        });
        // hitung statistik
        $stats = [
            'total' => (clone $query)->count(),
            'hari_ini' => (clone $query)->whereDate('tgl_reservasi', now())->count(),
            'menunggu' => (clone $query)->where('status', 'menunggu')->count(),
        ];

        return view('admin.reservasi.index', compact('reservasi', 'stats', 'klinikNama'));      
    }
    // form tambah reservasi
    public function create()
    {
        $klinikId = $this->getAdminKlinikId();
        $klinik = Klinik::where('id_klinik', $klinikId)->first(); //ambil klinik login
        // cek pasien yang tidak mempunyai reservasi aktif
        // DB::enableQueryLog();
        $pasiens = Pasien::whereDoesntHave('reservasi', function($q) {
                $q->whereIn('status', ['menunggu', 'tindakan', 'terdampak']);
            })
            ->orderBy('nama')
            ->get(); 
        // 
        return view('admin.reservasi.create', compact('klinik', 'pasiens'));
    }
    // simpan data reservasi baru dan validasi
    public function store(Request $request)
    {   //ambil id klinik admin login
        $klinikId = $this->getAdminKlinikId();
        // Validasi input
        $validated = $request->validate([
            'pasien_id' => 'required|exists:pasien,id_pasien',
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tgl_reservasi' => [
                'required',
                'date',
                'after_or_equal:today', //hari ini atau seteah hari ini
                function ($attribute, $value, $fail) {
                    $maxDate = now()->addDays(7)->format('Y-m-d');
                    if ($value > $maxDate) {
                        $fail('Tanggal reservasi maksimal 7 hari ke depan.');
                    }
                },
            ],
            'jam_reservasi' => 'required',
            'antrean' => 'required|integer|min:1',
            'keluhan' => 'nullable|string|max:500',
        ]);
        // Cek apakah pasien sudah punya reservasi aktif
        $cekAktif = Reservasi::where('id_pasien', $validated['pasien_id'])
            ->whereIn('status', ['menunggu', 'tindakan', 'terdampak'])
            ->exists();

        if ($cekAktif) {
            return back()->with('error', 'Pasien masih memiliki reservasi aktif!');
        }
        // Cek apakah slot sudah terisi
        $cekSlot = Reservasi::where('id_jadwal', $validated['id_jadwal'])
            ->where('tgl_reservasi', $validated['tgl_reservasi'])
            ->whereRaw(
                'REPLACE(jam_reservasi, " ", "") = ?', //hapus spasi dan input pada " - "
                [str_replace(' ', '', $validated['jam_reservasi'])]
            )
            ->whereIn('status', ['menunggu', 'tindakan']) //dicek untuk reservasi yang aktif
            ->exists();

        if ($cekSlot) {
            return back()->with('error', 'Slot jam sudah terisi!');
        }
        // cari data pasien berdasarkan id yang sudah lolo validasi lalu disimpan divariabel $pasien
        $pasien = Pasien::find($validated['pasien_id']);
        //simpan ke tabel reservasi
        $reservasi = Reservasi::create([
            'id_pasien' => $validated['pasien_id'],
            'id_jadwal' => $validated['id_jadwal'],
            'tgl_reservasi' => $validated['tgl_reservasi'],
            'jam_reservasi' => str_replace(' ', '', $validated['jam_reservasi']),
            'antrean' => $validated['antrean'],
            'status' => 'menunggu', // status awal selalu menunggu
            'keluhan' => $validated['keluhan'],
        ]);
        //kirim notifikasi ke dokter dan admin terkait reservasi baru
        $this->notifikasi->notifReservasiMasuk($reservasi);

        return redirect()->route('admin.reservasi.index')
            ->with('success', "Reservasi untuk {$pasien->nama} berhasil dibuat!")
            ->with('id_reservasi', $reservasi->id_reservasi);
    }
    //menampilkan detail reservasi
    public function show(Reservasi $reservasi)
    {   //ambil id klinik admin yang login
        $klinikId = $this->getAdminKlinikId();
        if (!$reservasi->jadwal || $reservasi->jadwal->id_klinik != $klinikId) {
            abort(403);
        }
        // Load relasi dasar
        $reservasi->load([
            'pasien.user',
            'jadwal.dokter',
            'jadwal.klinik',
            'rekamMedis',
            'rekamMedis.dokter'
        ]);

        // cek apakah ada perubahan jadwal dokter
        $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $reservasi->jadwal->id_dokter)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->first();
        // Override data dokter di jadwal kalo ada perubahan
        if ($perubahan) {
            if ($perubahan->jenis_perubahan == 'libur') {
                $reservasi->jadwal->dokter_status = 'libur';
                $reservasi->jadwal->dokter_info = 'Dokter sedang libur';
            } 
            elseif ($perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
                $reservasi->jadwal->dokter_asli = $reservasi->jadwal->dokter; // simpan dokter asli
                $reservasi->jadwal->dokter = $perubahan->dokterPengganti;     // override dengan pengganti
                $reservasi->jadwal->dokter_status = 'pengganti';
                $reservasi->jadwal->alasan_perubahan = $perubahan->alasan;
            }
            elseif ($perubahan->jenis_perubahan == 'tambahan') {
                $reservasi->jadwal->jam_mulai = $perubahan->jam_mulai;
                $reservasi->jadwal->jam_selesai = $perubahan->jam_selesai;
                $reservasi->jadwal->dokter_status = 'tambahan';
            }
        }
        return view('admin.reservasi.show', compact('reservasi'));
    }
    //form edit
    public function edit(Reservasi $reservasi)
    {   //ambil id klinik admin yang login
        $klinikId = $this->getAdminKlinikId();
        
        if (!$reservasi->jadwal || $reservasi->jadwal->id_klinik != $klinikId) {
            abort(403);
        }

        $tglReservasi = Carbon::parse($reservasi->tgl_reservasi);
        
        $jadwals = collect();
        //ambil jadwal sesuai tanggal reservasi
        $jadwalsHarian = Jadwal::with(['dokter'])
            ->where('id_klinik', $klinikId) //id kliniknya sama
            ->whereDate('tanggal', $tglReservasi->toDateString()) //tanggalnya sama
            ->whereNull('deleted_at') //belum dihapus
            ->when($tglReservasi->isCurrentMonth(), function ($q) {
                $q->where('is_aktif', 1);
            }) //tanggalnya adalah jadwal yang aktuf
            ->orderBy('jam_mulai') //diurutkan dari jam mulai
            ->get();
        //jika jadwal harian tidak kosong, gunakan jadwal tersebut
        if ($jadwalsHarian->isNotEmpty()) {
            $jadwals = $jadwalsHarian;
        } else { //kalo tidak ada, ambil na ahari dan tgl reservasi
            $hari = strtolower($tglReservasi->format('l'));
            $hariMap = [
                'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu'
            ];
            $hariIndonesia = $hariMap[$hari] ?? $hari;
            //cari jadwal rutin beserta dokter terkait
            $jadwals = Jadwal::with(['dokter'])
                ->where('id_klinik', $klinikId) //klinik yg sama
                ->where('hari', $hariIndonesia) //hari yg sama
                ->whereNull('tanggal') 
                ->where('is_aktif', 1)
                ->whereNull('deleted_at')
                ->orderBy('jam_mulai')
                ->get();
        }
        // setiap jadwal dokter, lakukan proses berikut
        $jadwals = $jadwals->map(function ($jadwal) use ($reservasi, $tglReservasi) {
            // cek perubahan jadwal dokter (libur/pengganti/tambahan)
            $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
                ->where('dokter_id', $jadwal->id_dokter)
                ->whereDate('tanggal', $tglReservasi->toDateString())
                ->where('status', 'aktif')
                ->first();

            if ($perubahan && $perubahan->jenis_perubahan == 'libur') {
                return null; // skip jadwal ini
            }
            // ambiljadwal nama, spesialisasi, jam mulai jam selesai dokter
            $dokterNama = $jadwal->dokter->nama ?? '-';
            $dokterSpesialisasi = $jadwal->dokter->spesialisasi ?? '-';
            $jamMulai = $jadwal->jam_mulai;
            $jamSelesai = $jadwal->jam_selesai;

            // terapkan perubahan jadwal
            if ($perubahan) {
                if ($perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
                    $dokterNama = $perubahan->dokterPengganti->nama;
                    $dokterSpesialisasi = $perubahan->dokterPengganti->spesialisasi;
                }
                if ($perubahan->jenis_perubahan == 'tambahan') {
                    $jamMulai = $perubahan->jam_mulai;
                    $jamSelesai = $perubahan->jam_selesai;
                }
            }

            // generate slot dengan tanggal RESERVASI
            $slots = $this->generateSlotsWithBookingInfo(
                $jamMulai,
                $jamSelesai,
                $tglReservasi->toDateString(),  
                $jadwal->id_jadwal,
                $reservasi->id_reservasi
            );
            // menambahkan informasi ke object jadwal
            $jadwal->slots = $slots; //menyimpan semua slot 
            $jadwal->slot_kosong = collect($slots)->where('tersedia', true)->count(); //hitung slot yg tersedia
            $jadwal->slot_total = count($slots);
            $jadwal->dokter_nama = $dokterNama; //tambahkan nama dokter yg mungkin sudah diganti
            $jadwal->dokter_spesialisasi = $dokterSpesialisasi; //tambah spesialisasi
            $jadwal->ada_perubahan = $perubahan ? true : false; //true jika ada perubahan jadwal
            $jadwal->jenis_perubahan = $perubahan->jenis_perubahan ?? null;
            $jadwal->is_selected = $jadwal->id_jadwal == $reservasi->id_jadwal; //cek apakah ini jadwal yg sedang dipakai sekarang

            return $jadwal;
        })->filter(function ($jadwal) {
            return $jadwal !== null; //hapus jadwal yg null
        });
        //bisa edit apabila status menunggu atau tindakan
        $pasiens = Pasien::where(function($query) use ($reservasi) {
                $query->whereDoesntHave('reservasi', function($q) { //pasien tidak punya reservasi aktif
                    $q->whereIn('status', ['menunggu', 'tindakan']); 
                })
                ->orWhere('id_pasien', $reservasi->id_pasien); //pengecualian pasien yg diedit
            })
            ->orderBy('nama')
            ->get();

        return view('admin.reservasi.edit', compact('reservasi', 'jadwals', 'pasiens'));
    }
    //ubah data reservasi
    public function update(Request $request, Reservasi $reservasi)
    {   //log
        \Log::info('=== UPDATE START ===', [
            'id_reservasi' => $reservasi->id_reservasi,
            'current_id_jadwal' => $reservasi->id_jadwal,
            'current_tgl' => $reservasi->tgl_reservasi,
            'current_jam' => $reservasi->jam_reservasi,
        ]);
        //ambil id klinik admin yg login
        $klinikId = $this->getAdminKlinikId();
        
        if ($reservasi->jadwal->id_klinik != $klinikId) {
            abort(403);
        }

        \Log::info('=== UPDATE INPUT RAW ===', [
            'all_input' => $request->all(),
            'jam_reservasi_raw' => $request->input('jam_reservasi'),
            'id_jadwal_raw' => $request->input('id_jadwal'),
        ]);
        //validasi data
        $validated = $request->validate([
            'pasien_id' => 'required|exists:pasien,id_pasien',
            'id_jadwal' => 'required|exists:jadwal,id_jadwal',
            'tgl_reservasi' => [
                'required', 'date', 'after_or_equal:today', //hari ini atau hari ke depan
                function ($attribute, $value, $fail) {
                    $maxDate = now()->addDays(7)->format('Y-m-d'); //7 hari
                    if ($value > $maxDate) $fail('Tanggal reservasi maksimal 7 hari ke depan.');
                },
            ],
            'jam_reservasi' => 'required',
            'antrean' => 'required|integer|min:1',
            'keluhan' => 'nullable|string|max:500',
        ]);

        \Log::info('=== AFTER VALIDATE ===', [
            'input_id_jadwal' => $validated['id_jadwal'],
            'input_tgl' => $validated['tgl_reservasi'],
            'input_jam' => $validated['jam_reservasi'],
        ]);
        //cek reservasi aktif pasein
        $cekAktif = Reservasi::where('id_pasien', $validated['pasien_id'])
            ->where('id_reservasi', '!=', $reservasi->id_reservasi)
            ->whereIn('status', ['menunggu', 'tindakan'])
            ->exists();
        if ($cekAktif) return back()->with('error', 'Pasien masih memiliki reservasi aktif lain!');
        
        // cek slot yg dipilih
        $cekSlot = Reservasi::where('id_jadwal', $validated['id_jadwal'])
            ->where('tgl_reservasi', $validated['tgl_reservasi'])
            ->whereRaw(
                'REPLACE(jam_reservasi, " ", "") = ?',
                [str_replace(' ', '', $validated['jam_reservasi'])]
            )
            ->where('id_reservasi', '!=', $reservasi->id_reservasi)
            ->whereIn('status', ['menunggu', 'tindakan', 'terdampak']) //slot punya reservasi aktif
            ->exists();
        if ($cekSlot) return back()->with('error', 'Slot jam sudah terisi!');

        // simpan data lama
        $jadwalLamaId = $reservasi->id_jadwal;
        $tglLama = $reservasi->tgl_reservasi;
        $jamLama = $reservasi->jam_reservasi;

        \Log::info('=== DATA LAMA DISIMPAN ===', [
            'jadwal_lama' => $jadwalLamaId,
            'tgl_lama' => $tglLama,
            'jam_lama' => $jamLama,
        ]);

        $dokterLama = $reservasi->jadwal->dokter->nama ?? null;
        $spesialisasiLama = $reservasi->jadwal->dokter->spesialisasi ?? null;

        // Update Reservasi
        $reservasi->id_pasien = $validated['pasien_id'];
        $reservasi->id_jadwal = $validated['id_jadwal'];
        $reservasi->tgl_reservasi = $validated['tgl_reservasi'];
        $reservasi->jam_reservasi = str_replace(' ', '', $validated['jam_reservasi']);
        $reservasi->antrean = $validated['antrean'];
        $reservasi->keluhan = $validated['keluhan'];
        $reservasi->save();

        $reservasi->refresh()->load([
            'jadwal.dokter',
            'jadwal.klinik',
            'pasien'
        ]);

        // cek perubahan jadwal dokter setelah update
        $perubahanBaru = PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $reservasi->jadwal->id_dokter)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->first();
        // Override data dokter di jadwal kalo ada perubahan baru
        $dokterBaru = $reservasi->jadwal->dokter->nama ?? null;
        $spesialisasiBaru = $reservasi->jadwal->dokter->spesialisasi ?? null;

        if ($perubahanBaru && $perubahanBaru->jenis_perubahan == 'pengganti' && $perubahanBaru->dokterPengganti) {
            $dokterBaru = $perubahanBaru->dokterPengganti->nama;
            $spesialisasiBaru = $perubahanBaru->dokterPengganti->spesialisasi;
        }
        
        // cek apakah ada perubahan di jadwal, tanggal, atau jam
        $adaPerubahan = ($jadwalLamaId != $reservasi->id_jadwal) 
            || ($tglLama != $reservasi->tgl_reservasi) 
            || ($jamLama != $reservasi->jam_reservasi);

        // Jika status terdampak, ubah ke menunggu setelah reschedule
        if ($reservasi->status === 'terdampak') {
            $reservasi->update(['status' => 'menunggu']);
            \Log::info('=== STATUS TERDAMPAK -> MENUNGGU ===');
        }

        \Log::info('=== PERUBAHAN CHECK ===', [
            'jadwal_lama' => $jadwalLamaId,
            'jadwal_baru' => $reservasi->id_jadwal,
            'tgl_lama' => $tglLama,
            'tgl_baru' => $reservasi->tgl_reservasi,
            'jam_lama' => $jamLama,
            'jam_baru' => $reservasi->jam_reservasi,
            'ada_perubahan' => $adaPerubahan,
        ]);
        // Kirim email konfirmasi reschedule jika ada perubahan
        if ($adaPerubahan) {
            \Log::info('=== MASUK IF PERUBAHAN ===');
            try {
                $result = $this->notif->kirimKonfirmasiReschedule(
                    $reservasi,
                    $dokterLama,         
                    $spesialisasiLama,  
                    $dokterBaru,        
                    $spesialisasiBaru,  
                    'Reschedule oleh admin' 
                );
                \Log::info('=== EMAIL RESULT ===', ['result' => $result]);
                
                return redirect()->route('admin.reservasi.index')
                    ->with('success', 'Reservasi diperbarui & email konfirmasi reschedule terkirim!');
            } catch (\Exception $e) {
                \Log::error('=== EMAIL ERROR ===', ['error' => $e->getMessage()]);
                return redirect()->route('admin.reservasi.index')
                    ->with('warning', 'Reservasi diperbarui, tapi email gagal terkirim.');
            }
        }
        return redirect()->route('admin.reservasi.index')
            ->with('success', 'Reservasi berhasil diperbarui!');
    }
    //membuat slot antrean otomatis, methode private vum bisa dipake di kelas reservasi
    private function generateSlotsWithBookingInfo(string $jamMulai, string $jamSelesai, string $tanggal, int $idJadwal, ?int $excludeReservasiId = null): array 
    {   //jika jam mulai kosong, jam selesai kosong, maka tidak ada slot yang dibuat
        if ($jamMulai === 'undefined' || $jamSelesai === 'undefined' || empty($jamMulai) || empty($jamSelesai)) {
            return [];
        }
        $slots = []; //buat array slot, antrean dimulai dr 1
        $antrean = 1;

        try {
            $start = Carbon::parse($jamMulai);
            $end = Carbon::parse($jamSelesai);
        } catch (\Exception $e) {
            \Log::error('Error parsing jam in generateSlotsWithBookingInfo: ' . $e->getMessage());
            return [];
        }
        //cek hari
        $isToday = Carbon::parse($tanggal)->isToday();
        $currentTime = now(); //waktu sekarang
        //perulangan membuat slot
        while ($start->lt($end)) { //slot endnya 1 jam
            $slotEnd = $start->copy()->addHour();
            //lewati jam istirahat
            if ($start->format('H:i') === '12:00') {
                $start = $start->copy()->addHour();
                continue;
            }
            if ($slotEnd->gt($end)) break;
            //reservasi hari ini
            if ($isToday) {
                $slotTime = Carbon::parse($tanggal . ' ' . $start->format('H:i:s'));
                if ($currentTime->greaterThan($slotTime)) { //bandingkan, dan lewati jika slot sudah lewat
                    $start = $slotEnd;
                    $antrean++;
                    continue;
                }
            }
            // membentuk interval
            $interval = sprintf('%s-%s', $start->format('H:i'), $slotEnd->format('H:i'));
            
            // Cek booking dengan exclude + status terdampak
            $bookingQuery = Reservasi::where('id_jadwal', $idJadwal)
                ->where('tgl_reservasi', $tanggal)
                ->whereRaw('REPLACE(jam_reservasi, " ", "") = ?', [$interval])
                ->whereIn('status', ['menunggu', 'tindakan', 'terdampak']);
                
            if ($excludeReservasiId) {
                $bookingQuery->where('id_reservasi', '!=', $excludeReservasiId);
            }
            //cek apakah data ditemukan, kalo ada berarti true
            $isBooked = $bookingQuery->exists();
            //tambah slot ke array
            $slots[] = [
                'jam_mulai' => $start->format('H:i'),
                'jam_selesai' => $slotEnd->format('H:i'),
                'interval' => $interval,
                'antrean' => $antrean,
                'tersedia' => !$isBooked
            ];
            //naikan antrean
            $antrean++;
            $start = $slotEnd; //geser jam, lalu loop
        }
        return $slots; //mengaambil seluruh slot
    }

    //mengelola kehadiran pasien
    public function kehadiran(Request $request, $id_reservasi)
    { //ambil id klinik admin yang login
        $klinikId = $this->getAdminKlinikId();
        // ambil data reservasi, yang memiliki jadwal menggunakan id klinik admin login
        $reservasi = Reservasi::where('id_reservasi', $id_reservasi)
            ->whereHas('jadwal', function ($q) use ($klinikId) {
                $q->where('id_klinik', $klinikId);
            })
            ->first();
        if (!$reservasi) {
            return response()->json(['success' => false, 'message' => 'Reservasi tidak ditemukan.'], 404);
        }

        //auto cancel pasien menunggu dan terdampak
        if (in_array($reservasi->status, ['menunggu', 'terdampak']) 
            && !empty($reservasi->jam_reservasi) 
            && $reservasi->jam_reservasi !== 'undefined') {
            
            try {
                $jamMulai = explode('-', $reservasi->jam_reservasi)[0]; //ambil jam mulai
                $jadwalTime = Carbon::parse($reservasi->tgl_reservasi . ' ' . $jamMulai);
                $batasWaktu = $jadwalTime->addMinutes(30); //maximal 30 menit
                // jika sekarang sampai batas waktu, maka udate status reservasi jd di batalkan 
                if (now()->greaterThan($batasWaktu)) { //membandingkan 2 waktu
                    $reservasi->update([
                        'status' => 'dibatalkan',
                        'cancelled_at' => now(),
                    ]);
                    //kirim  konfrimasi melalui
                    $this->notifikasi->notifReservasiDibatalkan(
                        $reservasi, 
                        'Dibatalkan otomatis karena melewati batas waktu 30 menit'
                    );
                    return response()->json([
                        'success' => false, 
                        'message' => 'Reservasi telah kedaluwarsa dan dibatalkan otomatis.',
                        'auto_cancelled' => true,
                        'new_status' => 'dibatalkan'
                    ], 422);
                }
            } catch (\Exception $e) {
                \Log::error('Error parsing waktu auto-cancel: ' . $e->getMessage());
            }
        }
        if ($reservasi->status === 'terdampak') {
            return response()->json([
                'success' => false, 
                'message' => 'Reservasi terdampak harus ditindaklanjuti via menu Konfirmasi.'
            ], 422);
        }
        if (!in_array($reservasi->status, ['menunggu', 'tindakan'])) {
            return response()->json([
                'success' => false, 
                'message' => 'Status saat ini tidak bisa diubah.'
            ], 422);
        }

        $newStatus = $request->input('status');
        if (!in_array($newStatus, ['tindakan', 'dibatalkan'])) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 422);
        }

        try {
            $reservasi->status = $newStatus;
            $reservasi->hadir_at = $newStatus === 'tindakan' ? now() : null;
            $reservasi->cancelled_at = $newStatus === 'dibatalkan' ? now() : null;
            $reservasi->save();

            if ($newStatus === 'dibatalkan') {
                $this->notifikasi->notifReservasiDibatalkan($reservasi, 'Pasien tidak hadir');
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }

        $message = $newStatus === 'tindakan'
            ? 'Pasien ditandai hadir, status diubah ke Tindakan.'
            : 'Reservasi berhasil dibatalkan.';

        return response()->json([
            'success' => true, 
            'message' => $message, 
            'new_status' => $newStatus
        ]);
    }
    //hapus data reservasi
    public function destroy($id_reservasi)
    {
        try {
            DB::beginTransaction();
            $klinikId = $this->getAdminKlinikId(); //ambil id klinik admin yang login

            $reservasi = Reservasi::where('id_reservasi', $id_reservasi)
                ->whereHas('jadwal', function ($q) use ($klinikId) {
                    $q->where('id_klinik', $klinikId);
                })
                ->first();
            if (!$reservasi) {
                return redirect()->route('admin.reservasi.index')->with('error', 'Reservasi tidak ditemukan.');
            }

            $namaPasien = $reservasi->pasien->nama ?? 'Tidak Diketahui';
            $this->notifikasi->notifReservasiDibatalkan($reservasi, 'Reservasi dihapus oleh admin');
            $reservasi->delete();
            DB::commit();

            return redirect()->route('admin.reservasi.index')->with('success', "Reservasi pasien {$namaPasien} berhasil dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.reservasi.index')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    // mengekspor data reservasi ke file excel
    public function exportExcel()
    {   //ambil id klinik dan nama admin yang login
        $klinikId = $this->getAdminKlinikId();
        $klinikNama = $this->getAdminKlinikNama();
        return Excel::download(new ReservasiExport($klinikId), "reservasi-{$klinikNama}.xlsx");
    }
    //export data reservasi ke file word
    public function exportWord()
    {   //ambil id dan nama klinik admin login
        $klinikId = $this->getAdminKlinikId();
        $klinikNama = $this->getAdminKlinikNama();
        $data = $this->baseQuery($klinikId)->get();
        $export = new ReservasiWordExport($data, $klinikNama);
        $phpWord = $export->generate();
        $fileName = "reservasi-{$klinikNama}.docx";
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        IOFactory::createWriter($phpWord, 'Word2007')->save($tempFile);
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    // mwnampilkan jadwal dokter yg tersedia pada tanggal tertentu
    public function getJadwal(Request $request)
    {
        try { // validasi 
            $request->validate([
                'klinik_id' => 'required|exists:klinik,id_klinik',
                'tanggal' => 'required|date',
                'exclude_reservasi_id' => 'nullable|integer',
            ]);

            $tanggal = Carbon::parse($request->tanggal);
            $maxDate = now()->addDays(7);
            if ($tanggal->gt($maxDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal maksimal 7 hari ke depan.'
                ], 422);
            }
            //cek hari libur
            $isHariLibur = HariLibur::whereDate('tanggal', $tanggal->toDateString())->exists();
            if ($isHariLibur) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            // jadwal sudah ada
            $jadwalSudahAda = Jadwal::whereMonth('tanggal', $tanggal->month)
                ->whereYear('tanggal', $tanggal->year)
                ->whereNull('deleted_at')
                ->exists();
            // cek aktif, apakah tangga; yg dipilih ada di bulan sekaramgg
            $isAktif = $tanggal->isCurrentMonth() ? 1 : 0;   
            //kalo jadwal harian gaada, sistem otomatis buat jadwal dari template
            if (!$jadwalSudahAda) {
                $this->generateHarianForBulan($tanggal->month, $tanggal->year, $isAktif);
            }
            $jadwals = collect();
            // ambil jadwal harian
            $jadwalsHarian = Jadwal::with(['dokter'])
                ->where('id_klinik', $request->klinik_id)
                ->whereDate('tanggal', $tanggal->toDateString())
                ->whereNull('deleted_at')
                ->where('is_aktif', $tanggal->isCurrentMonth() ? 1 : 0)
                ->orderBy('jam_mulai')
                ->get();
            //gunakan jadwal harian bila tersedia
            if ($jadwalsHarian->isNotEmpty()) {
                $jadwals = $jadwalsHarian;
            } else {
                //jadwal template apabila jadwal harian tidak tersedia
                $hari = strtolower($tanggal->format('l'));
                $hariMap = [
                    'monday' => 'senin', 'tuesday' => 'selasa', 'wednesday' => 'rabu',
                    'thursday' => 'kamis', 'friday' => 'jumat', 'saturday' => 'sabtu', 'sunday' => 'minggu'
                ];
                $hariIndonesia = $hariMap[$hari] ?? $hari;

                // cari jadwal template
                $jadwals = Jadwal::with(['dokter'])
                    ->where('id_klinik', $request->klinik_id)
                    ->where('hari', $hariIndonesia)
                    ->whereNull('tanggal')
                    ->where('is_aktif', $tanggal->isCurrentMonth() ? 1 : 0)
                    ->whereNull('deleted_at')
                    ->orderBy('jam_mulai')
                    ->get();
            }
            $result = [];
            //cek perubahan jadwal dokter
            foreach ($jadwals as $jadwal) {
                $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
                    ->where('dokter_id', $jadwal->id_dokter)
                    ->whereDate('tanggal', $tanggal->toDateString())
                    ->where('status', 'aktif')
                    ->first();

                if ($perubahan && $perubahan->jenis_perubahan == 'libur') {
                    continue; //dilewati atau tidak ditampilkan
                }
                // ambil dokter normal
                $dokterNama = $jadwal->dokter->nama ?? '-';
                $dokterSpesialisasi = $jadwal->dokter->spesialisasi ?? '-';
                $jamMulai = $jadwal->jam_mulai;
                $jamSelesai = $jadwal->jam_selesai;

                if ($perubahan) {
                    if ($perubahan->jenis_perubahan == 'pengganti' && $perubahan->dokterPengganti) {
                        $dokterNama = $perubahan->dokterPengganti->nama;
                        $dokterSpesialisasi = $perubahan->dokterPengganti->spesialisasi;
                    }
                    if ($perubahan->jenis_perubahan == 'tambahan') {
                        $jamMulai = $perubahan->jam_mulai;
                        $jamSelesai = $perubahan->jam_selesai;
                    }
                }
                // generate slot
                $slots = $this->generateSlots(
                    $jamMulai,
                    $jamSelesai,
                    $tanggal->toDateString(),
                    $jadwal->id_jadwal,
                    $request->input('exclude_reservasi_id')
                );
                // hitung solt kosong
                $slotKosong = collect($slots)->where('tersedia', true)->count();
                // memasukan jadwal yang masih memiliki slot
                if ($slotKosong > 0) {
                    $result[] = [
                        'id_jadwal' => $jadwal->id_jadwal,
                        'dokter_nama' => $dokterNama,
                        'dokter_spesialisasi' => $dokterSpesialisasi,
                        'jam_mulai' => Carbon::parse($jamMulai)->format('H:i'),
                        'jam_selesai' => Carbon::parse($jamSelesai)->format('H:i'),
                        'slots' => $slots,
                        'slot_kosong' => $slotKosong,
                        'slot_total' => count($slots),
                        'is_template' => $jadwal->tanggal === null,
                        'ada_perubahan' => $perubahan ? true : false,
                        'jenis_perubahan' => $perubahan->jenis_perubahan ?? null
                    ];
                }
            }
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error('GET JADWAL ERROR: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    //membuat slot jam reservasi otomatis
    private function generateSlots(string $jamMulai, string $jamSelesai, string $tanggal, int $idJadwal, ?int $excludeReservasiId = null): array
    {
        if ($jamMulai === 'undefined' || $jamSelesai === 'undefined' || empty($jamMulai) || empty($jamSelesai)) {
            return [];
        }

        $slots = [];
        $antrean = 1;

        try {
            $start = Carbon::parse($jamMulai);
            $end = Carbon::parse($jamSelesai);
        } catch (\Exception $e) {
            \Log::error('Error parsing jam in generateSlots: ' . $e->getMessage());
            return [];
        }
        // reservasi hari ini
        $isToday = Carbon::parse($tanggal)->isToday();
        $currentTime = now();
        // jam yang sudah lewat tidak ditampilkan
        while ($start->lt($end)) {
            $slotEnd = $start->copy()->addHour();
            //lewati jam istirahat
            if ($start->format('H:i') === '12:00') {
                $start = $start->copy()->addHour();
                continue;
            }
            if ($slotEnd->gt($end)) break;
            // reservasi hari ini
            if ($isToday) {
                $slotTime = Carbon::parse($tanggal . ' ' . $start->format('H:i:s'));
                if ($currentTime->greaterThan($slotTime)) {
                    $start = $slotEnd;
                    $antrean++;
                    continue;
                }
            }
            // buat interval
            $interval = sprintf('%s-%s', $start->format('H:i'), $slotEnd->format('H:i'));
            
            // Cek booking dengan exclude reservasi yang sedang diedit + tambah status terdampak
            $bookingQuery = Reservasi::where('id_jadwal', $idJadwal)
                ->where('tgl_reservasi', $tanggal)
                ->whereRaw('REPLACE(jam_reservasi, " ", "") = ?', [$interval])
                ->whereIn('status', ['menunggu', 'tindakan', 'terdampak']);
                
            if ($excludeReservasiId) {
                $bookingQuery->where('id_reservasi', '!=', $excludeReservasiId);
            }
            //cek apakah data ditemukan, kalo ada berarti true
            $isBooked = $bookingQuery->exists();
            // tambah slot ke array
            $slots[] = [
                'jam_mulai' => $start->format('H:i'),
                'jam_selesai' => $slotEnd->format('H:i'),
                'interval' => $interval,
                'antrean' => $antrean,
                'tersedia' => !$isBooked
            ];
            // antrean bertambah
            $antrean++;
            $start = $slotEnd;
        }

        return $slots;
    }
    // mengubah jadwal template menjadi jadwal harian
    private function generateHarianForBulan($bulan, $tahun, $isAktif = 0)
    {   //buat tnggal awal bulan
        try {
            $target = Carbon::create((int)$tahun, (int)$bulan, 1);
            
            Log::info('Generate jadwal harian dimulai', [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'target_date' => $target->toDateString()
            ]);
            // ambil seluruh template jadwal
           $templates = Jadwal::with(['klinik', 'dokter'])
            ->whereNull('tanggal')
            ->whereNull('deleted_at')
            ->get();

            Log::info('Template ditemukan', ['count' => $templates->count()]);
            //cek apakah template kosong
            if ($templates->isEmpty()) {
                Log::warning('Tidak ada template untuk bulan ini');
                return 0;
            }
            //menentukan awal dan akhir bulan
            $startOfMonth = $target->copy()->startOfMonth();
            $endOfMonth = $target->copy()->endOfMonth();
            $generatedCount = 0;

            DB::beginTransaction();
            // melakukan perulangan setiap bulan
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dayOfWeek = $date->dayOfWeek; //menentukan nomor hari
                //memproses seluruh tenplate
                foreach ($templates as $template) {
                    $templateHari = strtolower($template->hari);
                    //memastikan nama harid valid
                    if (!isset($this->hariMapping[$templateHari])) {
                        Log::error('Hari tidak valid: ' . $template->hari);
                        continue;
                    }
                    //membandingkan hari template dengan tanggal saat ini
                    if ($this->hariMapping[$templateHari] == $dayOfWeek) {
                        //cek apakah jadwal sudah ada
                        $exists = Jadwal::where('tanggal', $date->toDateString())
                            ->where('id_klinik', $template->id_klinik)
                            ->where('id_dokter', $template->id_dokter)
                            ->where('jam_mulai', $template->jam_mulai)
                            ->whereNull('deleted_at')
                            ->exists();
                        //jika belum ada, buat jadwal baru berdasarkan template
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
                            $generatedCount++; //hitung jumlah jadwal yang berhasil dibuat
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
    // melanjutkan dokter pengganti untuk reservasi terdampak
    public function lanjutDokterPengganti($id)
    {   //ambil id klinik admin yg login
        $klinikId = $this->getAdminKlinikId();
        //cari reservasi
        $reservasi = Reservasi::with([
            'pasien.user',
            'jadwal.dokter',
            'jadwal.klinik'
        ])->findOrFail($id);

        if (!$reservasi->jadwal || $reservasi->jadwal->id_klinik != $klinikId) {
            abort(403);
        }
        //ambil perubahan jadwal
        $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
            ->where('dokter_id', $reservasi->jadwal->id_dokter)
            ->whereDate('tanggal', $reservasi->tgl_reservasi)
            ->where('status', 'aktif')
            ->first();
        // jenis perubahan dokter pengganti
        if (!$perubahan || !$perubahan->dokter_pengganti_id) {
            return response()->json([
                'success' => false,
                'message' => 'Dokter pengganti tidak ditemukan'
            ], 404);
        }

        // cukup lanjutkan reservasi
        $reservasi->update([
            'status' => 'menunggu'
        ]);

        $dokterPenggantiNama = $perubahan->dokterPengganti->nama ?? '-';
        //konfirmasi email
        $this->notif->kirimKonfirmasiLanjut(
            $reservasi,
            $dokterPenggantiNama
        );

        return response()->json([
            'success' => true,
            'message' => 'Reservasi dilanjutkan dengan dokter pengganti.'
        ]);
    }
    //ubah status batalkan
    public function updateStatus(Request $request, $id)
    {
        try {
            $reservasi = Reservasi::with('pasien.user')->find($id);

            if (!$reservasi) {
                return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
            }

            $status = $request->status ?? 'dibatalkan';
            $reservasi->status = $status;
            $reservasi->save();

            if ($status === 'dibatalkan') {
                $this->notif->kirimKonfirmasiBatalTerdampak($reservasi);
            }

            return response()->json([
                'message' => 'Status berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //kirim email ke pasin yang tedampak
    public function sendEmail(Request $request, $id)
    {
        try { //ambil id klinik dari admin yg login
            $klinikId = $this->getAdminKlinikId();
            //cari reservasi pasien
            $reservasi = Reservasi::with(['pasien.user', 'jadwal.dokter'])->findOrFail($id);

            if (!$reservasi->jadwal || $reservasi->jadwal->id_klinik != $klinikId) {
                abort(403);
            }
            //cek perubahan jadwal
            $perubahan = PerubahanJadwalDokter::with('dokterPengganti')
                ->where('dokter_id', $reservasi->jadwal->id_dokter)
                ->whereDate('tanggal', $reservasi->tgl_reservasi)
                ->where('status', 'aktif')
                ->first();
            //simpan dokter lama dan dokter pengganti
            $dokterLama      = $reservasi->jadwal->dokter->nama ?? '-';
            $dokterPengganti = $perubahan?->dokterPengganti?->nama ?? 'Tidak tersedia';

            // Kirim email dulu
            $terkirim = $this->notif->kirimTerdampak($reservasi, $dokterLama, $dokterPengganti);

            if (!$terkirim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email pasien tidak ditemukan atau gagal dikirim.'
                ], 422);
            }

            // Update dengan DB facade kalau fillable bermasalah
            $updated = \DB::table('reservasi')
                ->where('id_reservasi', $reservasi->id_reservasi)
                ->update([
                    'email_terdampak' => true,
                    'updated_at' => now()
                ]);

            if (!$updated) {
                \Log::warning('email_terdampak tidak ter-update', [
                    'id_reservasi' => $reservasi->id_reservasi
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email pemberitahuan berhasil dikirim ke pasien.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Send email terdampak error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    //pengarahakan reschedule
    public function konfirmasiReschedule($id)
    {   
        $reservasi = Reservasi::with([
            'jadwal.dokter',
            'pasien.user',
            'jadwal.klinik'
        ])->findOrFail($id); // ambil data reservais
        //simpan pasien jadwal dan dokter
        $pasien = $reservasi->pasien;
        $jadwal = $reservasi->jadwal;
        $dokter = $jadwal->dokter;

        \Log::info('Email reschedule - Dokter:', [
            'id_jadwal' => $jadwal->id_jadwal,
            'id_dokter' => $dokter->id_dokter ?? 'NULL',
            'nama_dokter' => $dokter->nama ?? 'NULL',
        ]);
        //array data
        $data = [
            'pasien_nama' => $pasien->nama,
            'dokter_nama' => $dokter->nama ?? 'Dokter',
            'dokter_spesialisasi' => $dokter->spesialisasi ?? '-',
            'tgl_reservasi' => $reservasi->tgl_reservasi,
            'jam_reservasi' => $reservasi->jam_reservasi,
            'klinik_nama' => $jadwal->klinik->nama_klinik ?? 'Klinik',
            'antrean' => $reservasi->antrean,
        ];
        
        return redirect()->route('admin.reservasi.edit', $id)
            ->with('info', 'Silakan ubah jadwal/dokter, lalu simpan. Email konfirmasi akan otomatis terkirim.');
    }
}