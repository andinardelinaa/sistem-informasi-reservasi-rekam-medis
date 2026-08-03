@extends('layouts.pasien')

@section('header-title', 'Riwayat Reservasi')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background-color: #f1f5f9; /* Slate-100 */
}

.hero-section {
    /* Solid Teal Gradient, Professional */
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    position: relative;
    border-bottom: 4px solid #0f766e;
}

/* status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}
.status-menunggu { background-color: #fffbeb; color: #b45309; border: 1px solid #fcd34d; }
.status-tindakan { background-color: #f3e8ff; color: #6d28d9; border: 1px solid #d8b4fe; }
.status-selesai  { background-color: #ecfdf5; color: #047857; border: 1px solid #6ee7b7; }
.status-dibatalkan { background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* desktop */
.table-container {
    background: white;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.table-header {
    background-color: #0d9488; /* Solid Teal */
    border-radius: 8px 8px 0 0;
}

.reservasi-row {
    border-bottom: 1px solid #e2e8f0;
    transition: background-color 0.15s;
}
.reservasi-row:hover {
    background-color: #f0fdfa; /* Teal-50 */
}
.reservasi-row:last-child {
    border-bottom: none;
}

/* Tombol Aksi */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s;
    cursor: pointer;
    border: 1px solid transparent;
}
.btn-action:hover {
    opacity: 0.9;
}

.btn-struk {
    background-color: white;
    color: #0d9488;
    border: 1px solid #cbd5e1;
}
.btn-cancel {
    background-color: white;
    color: #dc2626;
    border: 1px solid #cbd5e1;
}
.btn-cancel:hover {
    background-color: #dc2626;
    color: white;
    border-color: #dc2626;
}

/*search*/
.search-wrapper {
    position: relative;
}
.search-input {
    width: 100%;
    padding: 10px 12px 10px 40px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #334155;
    transition: border-color 0.2s;
}
.search-input:focus {
    outline: none;
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}
.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

/* mobile*/
.reservasi-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}
.card-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 2px;
}
.card-value {
    font-size: 0.95rem;
    color: #1e293b;
    font-weight: 600;
}
.card-divider {
    height: 1px;
    background: #e2e8f0;
    margin: 12px 0;
}

/*responsive - DIHAPUS KARENA TAILWIND SUDAH MENGATUR MELALUI CLASS hidden md:block DAN md:hidden */

/* Agar modal Swal tidak terpotong di layar kecil */
.swal2-container .swal2-popup {
    max-width: 360px !important;
    width: 92% !important;
}

/* Layar sangat kecil */
@media (max-width: 374px) {
    .hero-section {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    .hero-section h1 {
        font-size: 1.25rem;
    }
    .hero-section p {
        font-size: 0.8rem;
    }
    .reservasi-card {
        padding: 16px;
    }
    .card-value {
        font-size: 0.85rem;
    }
    .status-badge {
        font-size: 0.65rem;
        padding: 4px 8px;
    }
    .btn-action {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

/* efek hover terjebak di perangkat touch/sentuh */
@media (hover: none) {
    .reservasi-row:hover {
        background-color: transparent;
    }
    .btn-action:hover {
        opacity: 1;
    }
    .btn-struk:hover {
        background-color: white;
        color: #0d9488;
        border-color: #cbd5e1;
    }
    .btn-cancel:hover {
        background-color: white;
        color: #dc2626;
        border-color: #cbd5e1;
    }
    /* Feedback sentuh yang lebih baik (active) */
    .btn-struk:active {
        background-color: #f0fdfa;
        border-color: #0d9488;
    }
    .btn-cancel:active {
        background-color: #dc2626;
        color: white;
        border-color: #dc2626;
    }
}

/* form cancel */
.form-cancel {
    margin: 0;
}
</style>

@section('content')
<!-- hero -->
<section class="hero-section py-10 md:py-12 relative">
    <div class="container mx-auto max-w-5xl px-4 md:px-6 text-center relative z-10">
        <p class="text-teal-100 text-[10px] uppercase tracking-[0.2em] mb-2 font-bold">
            Klinik Gigi Dokter Ndeso
        </p>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
            Riwayat Reservasi
        </h1>
        <div class="w-12 h-0.5 bg-teal-300 mx-auto mb-3"></div>
        <p class="text-teal-50 text-sm max-w-lg mx-auto">
            Kelola jadwal dan status reservasi pemeriksaan gigi Anda.
        </p>
    </div>
</section>

<!--main content -->
<section class="py-6 md:py-8 bg-slate-100 min-h-screen">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">

        <!-- tombol kembali, reservasi baru dan searchbar -->
        <div class="grid grid-cols-2 gap-3 mb-6 md:flex md:items-center md:gap-4">
            
            <a href="{{ route('pasien.pilih.klinik') }}"
               class="w-full md:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-medium rounded-lg text-sm hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-left mr-2 text-sm"></i>
                Kembali
            </a>

            <button onclick="checkActiveReservation()"
                class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg text-sm shadow-sm transition-colors md:order-last">
                <i class="fas fa-plus mr-2 text-sm"></i>
                Reservasi Baru
            </button>

            <!-- search -->
            <div class="relative w-full col-span-2 md:w-64 md:col-span-auto search-wrapper md:order-2 md:ml-auto">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari dokter atau klinik...">
            </div>
            
        </div>

        @if($reservasi->count() > 0)

            <!-- dekstop tabel -->
            <div class="table-container hidden md:block">
                <div class="table-header px-6 lg:px-8 py-4">
                    <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white items-center">
                        <div class="col-span-2">Tanggal</div>
                        <div class="col-span-1">Jam</div>
                        <div class="col-span-2">Dokter</div>
                        <div class="col-span-2">Klinik</div>
                        <div class="col-span-1 text-center">Antrean</div>
                        <div class="col-span-2 text-center">Status</div>
                        <div class="col-span-2 text-center">Aksi</div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100" id="desktopList">
                    @foreach($reservasi as $item)
                        @php
                         //mencari apakah ada perubahan jadwal
                            $perubahan = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
                                ->whereDate('tanggal', $item->tgl_reservasi)
                                ->where(function($q) use ($item) {
                                    $q->where('dokter_id', $item->jadwal->id_dokter)
                                      ->orWhere('dokter_pengganti_id', $item->jadwal->id_dokter);
                                })
                                ->whereNotNull('dokter_pengganti_id')
                                ->first();

                            $isDokterPengganti = $perubahan && $perubahan->dokterPengganti;
                            $namaDokter = $isDokterPengganti
                                ? $perubahan->dokterPengganti->nama
                                : ($item->jadwal->dokter->nama ?? 'Dokter');

                            //  Struk pakai variabel $perubahan yang sama 
                            $strukJson = json_encode([
                                'id_reservasi' => $item->id_reservasi,
                                'tgl_reservasi' => $item->tgl_reservasi,
                                'jam_reservasi' => $item->jam_reservasi,
                                'antrean' => $item->antrean,
                                'status' => $item->status,
                                'pasien_nama' => $item->pasien->nama ?? auth()->user()->name,
                                'dokter_nama' => $isDokterPengganti
                                    ? $perubahan->dokterPengganti->nama . ' (Dokter Pengganti)'
                                    : ($item->jadwal->dokter->nama ?? 'Dokter'),
                                'klinik_nama' => $item->jadwal->klinik->nama_klinik ?? $item->jadwal->klinik->nama ?? 'Klinik Gigi Dokter Ndeso',
                                'klinik_alamat' => $item->jadwal->klinik->alamat ?? 'Alamat Klinik',
                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                        @endphp

                       <div class="reservasi-row searchable-item grid grid-cols-12 gap-4 px-6 lg:px-8 py-4 items-center text-sm">

                            <!-- Tanggal -->
                            <div class="col-span-2">
                                <div class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($item->tgl_reservasi)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->tgl_reservasi)->translatedFormat('l') }}
                                </div>
                            </div>

                            <!-- Jam -->
                            <div class="col-span-1 font-medium text-slate-600">
                                {{ substr($item->jam_reservasi, 0, 5) }}
                            </div>

                            <!-- Dokter -->
                            <div class="col-span-2 text-teal-700" title="{{ $namaDokter }}">
                                <div class="font-semibold truncate">
                                    {{ $namaDokter }}
                                </div>

                                @if($isDokterPengganti)
                                    <div class="text-[10px] text-amber-600 font-bold mt-0.5">
                                        (Dokter Pengganti)
                                    </div>
                                @endif
                            </div>

                            <!-- Klinik -->
                            <div class="col-span-2 text-slate-600 font-medium">
                                <div class="line-clamp-2 leading-tight">
                                    {{ $item->jadwal->klinik->nama_klinik ?? 'Klinik' }}
                                </div>
                            </div>

                            <!-- Antrean -->
                            <div class="col-span-1 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-50 text-teal-700 text-xs font-bold rounded-full border border-teal-100">
                                    {{ $item->antrean }}
                                </span>
                            </div>

                            <!-- Status -->
                            <div class="col-span-2 text-center">
                                <span class="status-badge status-{{ $item->status }}">
                                    @if($item->status == 'menunggu')
                                        <i class="fas fa-clock text-[10px]"></i>
                                    @elseif($item->status == 'tindakan')
                                        <i class="fas fa-stethoscope text-[10px]"></i>
                                    @elseif($item->status == 'selesai')
                                        <i class="fas fa-check-circle text-[10px]"></i>
                                    @elseif($item->status == 'dibatalkan')
                                        <i class="fas fa-times-circle text-[10px]"></i>
                                    @endif

                                    {{ ucfirst($item->status) }}
                                </span>
                            </div>

                            <!-- Aksi -->
                            <div class="md:col-span-2 flex items-stretch justify-center gap-2">
                            <!-- bisa dibatalkan jika leih dr 12jam dr jam reservasi -->
                                @php
                                    $batasBatal = \Carbon\Carbon::parse(
                                        $item->tgl_reservasi . ' ' . $item->jam_reservasi
                                    )->subHours(12);

                                    $bisaBatal = now()->lessThan($batasBatal);
                                @endphp

                                <!-- Tombol Struk -->
                                <button
                                    class="btn-action btn-struk btn-lihat-struk min-w-[90px]"
                                    data-struk="{{ $strukJson }}"
                                    title="Lihat Struk"
                                >
                                    <i class="fas fa-receipt"></i>
                                    <span class="hidden lg:inline">Struk</span>
                                </button>

                                @if($item->status == 'menunggu' && $bisaBatal)

                                    <form
                                        action="{{ route('pasien.reservasi.destroy', $item->id_reservasi) }}"
                                        method="POST"
                                        class="form-cancel inline-flex"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn-action btn-cancel min-w-[90px] h-full"
                                            title="Batalkan"
                                        >
                                            <i class="fas fa-times"></i>
                                            <span class="hidden lg:inline">Batal</span>
                                        </button>
                                    </form>

                                @elseif($item->status == 'menunggu' && !$bisaBatal)

                                    <div class="min-w-[90px] flex items-center justify-center text-slate-400 text-[10px] italic border border-slate-200 rounded-md px-2 py-[7px]">
                                        Terkunci
                                    </div>

                                @else

                                    <div class="min-w-[90px] flex items-center justify-center text-slate-300 text-xs border border-slate-100 rounded-md px-2 py-[7px]">
                                        -
                                    </div>

                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- mobile -->
            <div class="flex flex-col gap-4 md:hidden" id="mobileList">
                @foreach($reservasi as $item)
                    @php
                        //  ambil reservasi dan perubahan jadwal dengan filter jenis_perubahan 
                        $perubahanMobile = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
                            ->whereDate('tanggal', $item->tgl_reservasi)
                            ->where(function($q) use ($item) {
                                $q->where('dokter_id', $item->jadwal->id_dokter)
                                  ->orWhere('dokter_pengganti_id', $item->jadwal->id_dokter);
                            })
                            ->whereNotNull('dokter_pengganti_id')
                            ->first();

                        $isDokterPenggantiMobile = $perubahanMobile && $perubahanMobile->dokterPengganti;
                        $namaDokterMobile = $isDokterPenggantiMobile
                            ? $perubahanMobile->dokterPengganti->nama
                            : ($item->jadwal->dokter->nama ?? 'Dokter');

                        // FIX: Struk pakai variabel $perubahanMobile yang sama 
                        $strukJson = json_encode([
                            'id_reservasi' => $item->id_reservasi,
                            'tgl_reservasi' => $item->tgl_reservasi,
                            'jam_reservasi' => $item->jam_reservasi,
                            'antrean' => $item->antrean,
                            'status' => $item->status,
                            'pasien_nama' => $item->pasien->nama ?? auth()->user()->name,
                            'dokter_nama' => $isDokterPenggantiMobile
                                ? $perubahanMobile->dokterPengganti->nama . ' (Dokter Pengganti)'
                                : ($item->jadwal->dokter->nama ?? 'Dokter'),
                            'klinik_nama' => $item->jadwal->klinik->nama_klinik ?? $item->jadwal->klinik->nama ?? 'Klinik Gigi Dokter Ndeso',
                            'klinik_alamat' => $item->jadwal->klinik->alamat ?? 'Alamat Klinik',
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
                    @endphp

                    <div class="reservasi-card searchable-item">
                        <!-- Tanggal & Status -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-lg font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($item->tgl_reservasi)->format('d M Y') }}
                                </div>
                                <div class="text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->tgl_reservasi)->translatedFormat('l') }}, {{ substr($item->jam_reservasi, 0, 5) }} WIB
                                </div>
                            </div>
                            <span class="status-badge status-{{ $item->status }}">
                                @if($item->status == 'menunggu') <i class="fas fa-clock text-[10px]"></i>
                                @elseif($item->status == 'tindakan') <i class="fas fa-stethoscope text-[10px]"></i>
                                @elseif($item->status == 'selesai') <i class="fas fa-check-circle text-[10px]"></i>
                                @elseif($item->status == 'dibatalkan') <i class="fas fa-times-circle text-[10px]"></i>
                                @endif
                                {{ ucfirst($item->status) }}
                            </span>
                        </div>

                        <div class="card-divider"></div>

                        <!-- Info Detail -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <!-- nama dokter -->
                                <div class="card-label">Dokter</div>
                                <div class="card-value text-teal-700">
                                    {{ $namaDokterMobile }}
                                    @if($isDokterPenggantiMobile)
                                        <div class="text-[10px] text-amber-600 font-bold mt-0.5">
                                            (Dokter Pengganti)
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <!-- klinik -->
                                <div class="card-label">Klinik</div>
                                <div class="card-value leading-tight">
                                    {{ $item->jadwal->klinik->nama_klinik ?? 'Klinik' }}
                                </div>
                            </div>
                            <div>
                                <!-- nomor antrean -->
                                <div class="card-label">No. Antrian</div>
                                <div class="card-value text-2xl text-teal-600">#{{ $item->antrean }}</div>
                            </div>
                            <div>
                                <!-- nomor resercasi -->
                                <div class="card-label">No. Reservasi</div>
                                <div class="card-value text-xs text-slate-400 font-mono mt-1">
                                    RS-{{ \Carbon\Carbon::parse($item->tgl_reservasi)->format('Ymd') }}-{{ str_pad($item->id_reservasi, 4, '0', STR_PAD_LEFT) }}
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="grid grid-cols-2 gap-2 mt-2 items-stretch">
                            <!-- bisa batal -->
                            @php
                                $batasBatal = \Carbon\Carbon::parse($item->tgl_reservasi . ' ' . $item->jam_reservasi)->subHours(12);
                                $bisaBatal = now()->lessThan($batasBatal);
                            @endphp
                            <!-- tombol lihat struk -->
                            <button class="btn-action btn-struk btn-lihat-struk w-full py-3 text-sm" data-struk="{{ $strukJson }}">                           
                            <i class="fas fa-receipt"></i> Lihat Struk
                            </button>
                            <!-- jika statusnya menunggu dan masih bisa dibatalkan, munculkan tombol hapus -->
                            @if($item->status == 'menunggu' && $bisaBatal)
                               <form action="{{ route('pasien.reservasi.destroy', $item->id_reservasi) }}"
                                    method="POST" class="form-cancel flex w-full">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-cancel w-full py-3 text-sm flex-1">
                                        <i class="fas fa-times"></i> Batalkan
                                    </button>
                                </form>
                            <!-- atau menunggu tp sudah tidak bisa dibatalkan. muncuk terkunci -->
                            @elseif($item->status == 'menunggu' && !$bisaBatal)
                                <button disabled class="btn-action w-full py-3 text-sm bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed">
                                    <i class="fas fa-lock"></i> Terkunci
                                </button>
                            <!-- yang lain -->
                            @else
                                <div class="w-full py-3 text-sm text-center text-slate-300 border border-slate-100 rounded-md flex items-center justify-center">
                                    -
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $reservasi->links('pagination::tailwind') }}
            </div>
        @else
        <!-- jika kosong blm ada reservasi -->
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm py-16 md:py-24 text-center border border-slate-200">
                <div class="mx-auto w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-times text-3xl text-teal-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Reservasi</h3>
                <p class="text-slate-500 max-w-md mx-auto mb-8 px-4 text-sm">
                    Anda belum memiliki riwayat reservasi. Mulai buat janji temu pertama Anda sekarang.
                </p>
                <a href="{{ route('pasien.pilih.klinik') }}"
                   class="inline-flex items-center px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg text-sm shadow-sm transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Reservasi Baru
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

@section('scripts')
<!-- Library untuk download struk -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// ubah tanggal jadi format indo
function formatTanggalIndo(tanggal) {
    //digunakan untuk nama hari
    const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    //digunakan untuk nama bulan
    const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const d = new Date(tanggal); //ubah string jadi object date
    if (isNaN(d)) return tanggal;
    return `${hari[d.getDay()]}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
}
//menyimpan data struk yag telah dibuka
window.currentStrukData = null;
//membuat html struk
function generateStrukHTML(data, mode) {
    const isDownload = mode === 'download'; //menentukan apakah untuk di download
    //buat nomor reservasi(tanggal reservasi dan id reservasi)
    const noReservasi = 'RS-' + data.tgl_reservasi.replace(/-/g,'') + '-' + String(data.id_reservasi).padStart(4,'0');
    //memformat tanggal
    const tglFormatted = formatTanggalIndo(data.tgl_reservasi);
    const jamFormatted = data.jam_reservasi.substring(0,5) + ' WIB'; //memformat jam
    //tentukan warna badge status
    let statusColor = '#6b7280';
    if (data.status === 'menunggu') statusColor = '#b45309';
    if (data.status === 'tindakan') statusColor = '#6d28d9';
    if (data.status === 'selesai') statusColor = '#047857';
    if (data.status === 'dibatalkan') statusColor = '#b91c1c';
    //kalo mode download = true
    if (isDownload) {
        return `
            <div class="struk-download" style="font-family: 'Courier New', monospace; background: #ffffff; padding: 30px 25px; border: 1.5px solid #333; border-radius: 2px; width: 100%; max-width: 320px; margin: 0 auto; text-align: center;">

                <!-- Header -->
                <div style="margin-bottom: 18px; border-bottom: 2px dashed #333; padding-bottom: 14px;">
                    <h2 style="margin: 0; font-size: 16px; color: #111827; font-weight: bold; letter-spacing: 0.5px;">${data.klinik_nama}</h2>
                    <p style="margin: 6px 0 0 0; font-size: 11px; color: #555;">${data.klinik_alamat}</p>
                    <p style="margin: 2px 0 0 0; font-size: 10px; color: #777;">08112784411</p>
                </div>

                <!-- Info -->
                <div style="text-align: left; margin-bottom: 16px; font-size: 12px; line-height: 2; color: #374151;">
                    <p style="margin: 0;">No. Reservasi <span style="float: right; font-weight: bold; color: #111827;">${noReservasi}</span></p>
                    <p style="margin: 0;">Tanggal <span style="float: right; font-weight: bold; color: #111827;">${tglFormatted}</span></p>
                    <p style="margin: 0;">Jam <span style="float: right; font-weight: bold; color: #111827;">${jamFormatted}</span></p>
                    <p style="margin: 0;">Status <span style="float: right; font-weight: bold; color: ${statusColor}; text-transform: capitalize;">${data.status}</span></p>
                </div>

                <div style="border-top: 1px dashed #bbb; margin: 14px 0;"></div>

                <!-- Pasien & Dokter -->
                <div style="text-align: left; margin-bottom: 16px;">
                    <div style="margin-bottom: 12px;">
                        <p style="margin: 0; font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">Nama Pasien</p>
                        <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: bold; color: #111827;">${data.pasien_nama}</p>
                    </div>
                    <div>
                        <p style="margin: 0; font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.05em;">Dokter</p>
                        <p style="margin: 4px 0 0 0; font-size: 14px; font-weight: bold; color: #111827;">${data.dokter_nama}</p>
                    </div>
                </div>

                <!-- Nomor Antrian -->
                <div style="margin: 20px 0; background: #f0fdfa; padding: 18px; border-radius: 2px; border: 1.5px solid #99f6e4;">
                    <p style="margin: 0 0 8px 0; font-size: 11px; color: #0f766e; font-weight: 700; letter-spacing: 0.05em;">NOMOR ANTRIAN ANDA</p>
                    <div style="background: #14b8a6; color: white; width: 64px; height: 64px; margin: 0 auto; border-radius: 2px; font-size: 26px; font-weight: bold; text-align: center; line-height: 64px; box-sizing: border-box; box-shadow: 0 4px 12px rgba(20, 184, 166, 0.25);">
                        ${data.antrean}
                    </div>
                </div>

                <!-- Catatan -->
                <div style="margin-top: 16px; font-size: 10px; color: #666; text-align: center; line-height: 1.7;">
                    <p style="margin: 0;">• Datang 30 menit sebelum jadwal</p>
                    <p style="margin: 0;">• Bawa KTP dan kartu peserta</p>
                    <p style="margin: 0;">• Simpan struk ini sebagai bukti</p>
                </div>

                <!-- Footer -->
                <div style="text-align: center; margin-top: 16px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 9px; color: #999;">
                    <p style="margin: 0;">Terima kasih telah mempercayai layanan kami</p>
                </div>
            </div>
        `;
    } else {
        //mode popup
        return `
            <div class="struk-rumah-sakit" style="font-family: 'Courier New', monospace; background: white; padding: 10px; border: 1px solid #d1d5db; border-radius: 2px; width: 100%; max-width: 280px; margin: 0 auto; text-align: left;">

                <div style="text-align: center; margin-bottom: 6px; border-bottom: 1px dashed #d1d5db; padding-bottom: 6px;">
                    <h2 style="margin: 0; font-size: 13px; color: #111827; font-weight: bold;">${data.klinik_nama}</h2>
                </div>
                <div style="margin-bottom: 6px; font-size: 10px; line-height: 1.6; color: #374151;">
                    <p style="margin: 0;">No: <strong style="color: #111827;">${noReservasi}</strong></p>
                    <p style="margin: 0;">Tgl: <strong style="color: #111827;">${tglFormatted}</strong></p>
                    <p style="margin: 0;">Jam: <strong style="color: #111827;">${jamFormatted}</strong></p>
                    <p style="margin: 0;">Status: <strong style="color: ${statusColor}; text-transform: capitalize;">${data.status}</strong></p>
                </div>
                <div style="border-top: 1px dashed #d1d5db; margin: 6px 0;"></div>
                <div style="margin-bottom: 4px;">
                    <p style="margin: 0; font-size: 9px; color: #9ca3af;">Pasien: <strong style="color: #111827; font-size: 11px;">${data.pasien_nama}</strong></p>
                </div>
                <div style="margin-bottom: 6px;">
                    <p style="margin: 0; font-size: 9px; color: #9ca3af;">Dokter: <strong style="color: #111827; font-size: 11px;">${data.dokter_nama}</strong></p>
                </div>
                <div style="text-align: center; margin: 8px 0; background: #f0fdfa; padding: 8px; border-radius: 2px; border: 1px solid #99f6e4;">
                    <p style="margin: 0 0 4px 0; font-size: 9px; color: #0f766e; font-weight: 700;">NOMOR ANTRIAN</p>
                    <div style="background: #14b8a6; color: white; width: 48px; height: 48px; margin: 0 auto; border-radius: 2px; font-size: 18px; font-weight: bold; line-height: 48px; text-align: center;">
                        ${data.antrean}
                    </div>
                </div>
                <div style="margin-top: 6px; font-size: 9px; color: #6b7280; text-align: center; line-height: 1.4;">
                    <p style="margin: 0;">• Datang 30 menit sebelum jadwal</p>
                    <p style="margin: 0;">• Bawa KTP atau identitas diri</p>
                    <p style="margin: 0;">• Pembatalan maksimal 12 jam sebelum jadwal</p>
                </div>
            </div>
            <div style="margin-top: 10px; display: flex; gap: 6px; justify-content: center;">
                <button onclick="window.exportStrukToImage()" style="padding: 6px 12px; background: #14b8a6; color: white; border: none; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 600;">
                    <i class="fas fa-image"></i> PNG
                </button>
                <button onclick="window.exportStrukToPDF()" style="padding: 6px 12px; background: #0d9488; color: white; border: none; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: 600;">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        `;
    }
}
//function dipanggil ketika user tekan tombol lihat struk
function showStruk(data) {
    window.currentStrukData = data; //simpan data struk
    Swal.fire({ //popup
        title: 'BUKTI RESERVASI',
        html: generateStrukHTML(data, 'show'),
        icon: false,
        showConfirmButton: true,
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#14b8a6',
        width: 'auto',
        padding: '0.5em',
        allowOutsideClick: true,
        customClass: {
            popup: 'rounded-sm',
            confirmButton: 'rounded-sm'
        }
    });
}
//membuat file pdf dan png
function doExport(type) {
    const data = window.currentStrukData; //ambil data struk
    if (!data) { //jika data kosong maka tidak ada data struk
        Swal.fire('Error', 'Data struk tidak ditemukan', 'error');
        return;
    }

    // Buat container hidden untuk versi download
    const container = document.createElement('div');
    container.innerHTML = generateStrukHTML(data, 'download'); //html struk download
    container.style.position = 'fixed';
    container.style.top = '-9999px';
    container.style.left = '-9999px';
    document.body.appendChild(container); //masukan ke dalam body
    //ambil elemen struk
    const element = container.querySelector('.struk-download');
    //buat nomor reservasi
    const noReservasi = 'RS-' + data.tgl_reservasi.replace(/-/g,'') + '-' + String(data.id_reservasi).padStart(4,'0');
    //memfoto elemen menjadi gambar
    html2canvas(element, {
        scale: 2,
        backgroundColor: '#ffffff'
    }).then(canvas => { //setelah foto selesai
        document.body.removeChild(container); //hapus div smeentara
        //jika tipe png 
        if (type === 'png') { //buat tag a
            const link = document.createElement('a');
            link.download = `struk-reservasi-${noReservasi}.png`; //nama file
            link.href = canvas.toDataURL('image/png');  //canvas menjadi gambar
            link.click(); //user klik download , png ternduh
        //jika tipe pdf
        } else if (type === 'pdf') {
            //ambil js pdf
            const { jsPDF } = window.jspdf;
            //ubah canvas jadi png
            const imgData = canvas.toDataURL('image/png');
            // Konversi pixel ke mm 
            const pxToMm = 0.264583;
            const imgWidth = canvas.width * pxToMm; //lebar gambar
            const imgHeight = canvas.height * pxToMm;
            // Buat PDF custom size sesuai gambar + margin 10mm
            const pdfWidth = imgWidth + 20;
            const pdfHeight = imgHeight + 20;
            //membuat pdf
            const pdf = new jsPDF({
                orientation: 'p',
                unit: 'mm',
                format: [pdfWidth, pdfHeight]
            });
            //memasukan gambar
            pdf.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            pdf.save(`struk-reservasi-${noReservasi}.pdf`); //simpan pdf
        }
        //pesan berhasil
        Swal.fire({
            text: `Struk ${type.toUpperCase()} berhasil diunduh`,
            timer: 1800,
            showConfirmButton: false,
            width: 250,
            padding: '0.4em'
        });
        //pesan jika tidak bisa mengunduh
    }).catch(err => {
        console.error(err);
        Swal.fire('Error', 'Gagal membuat struk', 'error');
    });
}
//shortcut png dan pdf
window.exportStrukToImage = function() { doExport('png'); };
window.exportStrukToPDF = function() { doExport('pdf'); };

// kode baru jalan setelah halaman seleseai dimuat
document.addEventListener('DOMContentLoaded', function () {
    // search
    //ambil input search
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() { //event ketik
        const filter = this.value.toLowerCase(); //ambil keyword
        const items = document.querySelectorAll('.searchable-item'); //ambil semua item
        //loop item
        items.forEach(item => {
            const text = item.textContent.toLowerCase(); //ambil keyword
            if(text.includes(filter)) { //kalo cocok tampilkan kalo  tidak cocok sembuyikan
                item.style.display = ""; // Reset display
            } else {
                item.style.display = "none";
            }
        });
    });
    // Klik tombol Lihat Struk
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-lihat-struk'); //cek tombol
        if (!btn) return;

        try {
            //ambil data html ubah jadi object js
            const data = JSON.parse(btn.dataset.struk);
            showStruk(data); //tampilkan struk
        } catch (err) {
            console.error('Gagal parse data struk:', err);
            //kalo json rusak
            Swal.fire('Error', 'Gagal memuat data struk', 'error');
        }
    });
    // Popup struk setelah reservasi BARU
    @if(session('success') && session('reservasiBaru'))
    @php
    //ambil data php
        $r = session('reservasiBaru');
        $tanggal = \Carbon\Carbon::parse($r->tgl_reservasi);
        $tanggalFormatted = $tanggal->translatedFormat('l, d F Y');
        $jamFormatted = \Carbon\Carbon::parse($r->jam_reservasi)->format('H:i') . ' WIB';
        $noReservasi = 'RS-' . date('Ymd') . '-' . str_pad($r->id_reservasi, 4, '0', STR_PAD_LEFT);

        // cek dokter pengganti
        $perubahanBaru = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
            ->whereDate('tanggal', $r->tgl_reservasi)
            ->where(function($q) use ($r) {
                $q->where('dokter_id', $r->jadwal->id_dokter)
                  ->orWhere('dokter_pengganti_id', $r->jadwal->id_dokter);
            })
            ->whereNotNull('dokter_pengganti_id')
            ->first();

        $isPenggantiBaru = $perubahanBaru && $perubahanBaru->dokterPengganti;
        $namaDokterBaru = $isPenggantiBaru
            ? $perubahanBaru->dokterPengganti->nama . ' (Dokter Pengganti)'
            : (optional($r->jadwal->dokter)->nama ?? 'Dokter');
    @endphp
    //alert reservasi berhasil
    Swal.fire({
        width: 'auto',
        showConfirmButton: true,
        confirmButtonText: 'Oke',
        confirmButtonColor: '#14b8a6',
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-sm font-medium'
        },
        html: `
            <div class="text-center">

                <!-- Icon -->
                <div style="
                    width:55px;
                    height:55px;
                    border-radius:2px;
                    background:linear-gradient(135deg,#fef3c7,#fde68a);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 12px;
                ">
                    <i class="fas fa-check-circle" style="font-size:22px; color:#d97706;"></i>
                </div>

                <!-- Title -->
                <h2 style="font-size:17px; font-weight:600; color:#1f2937; margin-bottom:4px;">
                    Reservasi Berhasil
                </h2>

                <p style="font-size:13px; color:#6b7280; margin-bottom:10px;">
                    No: <b>{{ $noReservasi }}</b>
                </p>

                <!-- Info kecil -->
                <div style="font-size:12px; color:#374151; line-height:1.6; text-align:left; display:inline-block;">
                    <div>Tanggal: <b>{{ $tanggalFormatted }}</b></div>
                    <div>Jam: <b>{{ $jamFormatted }}</b></div>
                    <div>Pasien: <b>{{ $r->pasien->nama ?? auth()->user()->name }}</b></div>
                    <div>Dokter: <b>{{ $namaDokterBaru }}</b></div>
                </div>

                <!-- Antrian -->
                <div style="
                    margin-top:12px;
                    background:#ecfeff;
                    padding:10px;
                    border-radius:2px;
                    border:1px solid #99f6e4;
                ">
                    <div style="font-size:10px; color:#0f766e; font-weight:700;">
                        NOMOR ANTRIAN
                    </div>
                    <div style="
                        width:44px;
                        height:44px;
                        margin:6px auto 0;
                        border-radius:2px;
                        background:#14b8a6;
                        color:white;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        font-weight:bold;
                        font-size:18px;
                    ">
                        {{ $r->antrean }}
                    </div>
                </div>

                <div style="margin-top:10px; font-size:10px; color:#6b7280;">
                    Datang 30 menit sebelum jadwal
                </div>

            </div>
        `
    });
    @endif

    // Notifikasi biasa (selain popup struk)
    @if(session('success') && !session('reservasiBaru'))
    Swal.fire({
        width: 'auto',
        timer: 3000,
        showConfirmButton: true,
        confirmButtonText: 'Oke',
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-sm font-medium'
        },
        html: `
            <div class="text-center">

                <div style="
                    width:55px;
                    height:55px;
                    border-radius:2px;
                    background:linear-gradient(135deg,#dcfce7,#bbf7d0);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 12px;
                ">
                    <i class="fas fa-check" style="font-size:20px; color:#16a34a;"></i>
                </div>

                <h2 style="font-size:17px; font-weight:600; color:#1f2937; margin-bottom:4px;">
                    Berhasil
                </h2>

                <p style="font-size:13px; color:#6b7280;">
                    {{ session('success') }}
                </p>

            </div>
        `
    });
    @endif
    //popup error
    @if(session('error'))
    Swal.fire({
        width: 'auto',
        showConfirmButton: true,
        confirmButtonText: 'Mengerti',
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-sm font-medium'
        },
        html: `
            <div class="text-center">

                <!-- Icon Warning -->
                <div style="
                    width:55px;
                    height:55px;
                    border-radius:2px;
                    background:linear-gradient(135deg,#fef3c7,#fde68a);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 12px;
                ">
                    <i class="fas fa-exclamation-triangle" style="font-size:22px; color:#d97706;"></i>
                </div>

                <h2 style="font-size:17px; font-weight:600; color:#1f2937; margin-bottom:4px;">
                    Gagal
                </h2>

                <p style="font-size:13px; color:#6b7280;">
                    {{ session('error') }}
                </p>

            </div>
        `
    });
    @endif

    //cancel reservasi
    document.querySelectorAll('.form-cancel').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //jangan langsung submit

            // Ambil nama pasien
            const pasienName =
            //dekstop
                this.closest('.reservasi-row')?.querySelector('.font-semibold.text-teal-700, .font-semibold.truncate')?.textContent?.trim() || // Desktop
            //mobile
                this.closest('.reservasi-card')?.querySelector('.card-value.text-teal-700')?.firstChild?.textContent?.trim() || // Mobile
                'reservasi ini';
            // popup
            Swal.fire({
                width: 'auto',
                html: `
                    <div class="text-center">
                        <div style="
                            width:55px;
                            height:55px;
                            border-radius:2px;
                            background:linear-gradient(135deg,#fee2e2,#fecaca);
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:0 auto 12px;
                        ">
                            <i class="fas fa-trash" style="font-size:22px; color:#dc2626;"></i>
                        </div>

                        <h2 style="font-size:17px; font-weight:600; color:#1f2937; margin-bottom:4px;">
                            Batalkan Reservasi
                        </h2>

                        <p style="font-size:13px; color:#6b7280;">
                            Yakin batalkan reservasi <b>${pasienName}</b>?<br>
                            <span style="color:#ef4444;">Tindakan ini tidak dapat dikembalikan</span>
                        </p>
                    </div>
                `,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Batalkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-sm p-5',
                    confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-sm font-medium',
                    cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-sm font-medium ml-2'
                }
                //jika batalkan, maka js kirim ke form laravel
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
//cek apakah user masih punya reservasi aktif
function checkActiveReservation() {
    //ambil data dari laravel status menunggu tindakan, maka hasActive = true
    const hasActive = @json($reservasi->whereIn('status', ['menunggu', 'tindakan'])->count() > 0);
    //jika masih ada reservasi aktif
    if (hasActive) {
        Swal.fire({
            width: 'auto',
            html: `
                <div class="text-center">
                    <div style="
                        width:55px;
                        height:55px;
                        border-radius:2px;
                        background:linear-gradient(135deg,#fef3c7,#fde68a);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:0 auto 12px;
                    ">
                        <i class="fas fa-lock" style="font-size:22px; color:#d97706;"></i>
                    </div>

                    <h2 style="font-size:17px; font-weight:600; color:#1f2937; margin-bottom:4px;">
                        Reservasi Masih Aktif
                    </h2>

                    <p style="font-size:13px; color:#6b7280;">
                        Selesaikan atau batalkan reservasi aktif terlebih dahulu.
                    </p>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Mengerti',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-sm p-5',
                cancelButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-sm font-medium'
            }
        });
    } else {
        //jika tidak ada diarahkan ke halaman klinik
        window.location.href = "{{ route('pasien.pilih.klinik') }}";
    }
}
</script>
@endsection