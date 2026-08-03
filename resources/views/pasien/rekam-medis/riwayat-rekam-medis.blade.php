@extends('layouts.pasien')

@section('header-title', 'Riwayat Rekam Medis')

<style>
/* ========== GLOBAL & HERO (Formal Teal) ========== */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background-color: #f1f5f9;
}

.hero-section {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    position: relative;
    border-bottom: 4px solid #0f766e;
}

/* ========== DESKTOP TABLE (Formal & Clean) ========== */
.table-container {
    background: white;
    border: 1px solid #cbd5e1;
    border-radius: 8px; /* Diubah jadi kotak */
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.table-header {
    background-color: #0d9488;
    border-radius: 8px 8px 0 0; /* Diubah jadi kotak */
}

.rekammedis-row {
    border-bottom: 1px solid #e2e8f0;
    transition: background-color 0.15s;
}
.rekammedis-row:hover {
    background-color: #f0fdfa;
}
.rekammedis-row:last-child {
    border-bottom: none;
}

/* Tombol Aksi */
.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px; /* Diubah jadi kotak */
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
}
.btn-action:hover {
    opacity: 0.9;
}
.btn-detail {
    background-color: white;
    color: #0d9488;
    border: 1px solid #cbd5e1;
}

/* ========== SEARCH BAR ========== */
.search-wrapper {
    position: relative;
}
.search-input {
    width: 100%;
    padding: 10px 12px 10px 40px;
    border: 1px solid #cbd5e1;
    border-radius: 8px; /* Diubah jadi kotak */
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

/* ========== MOBILE CARD ========== */
.rekammedis-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px; /* Diubah jadi kotak */
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

/* ========== RESPONSIVE ========== */
@media (min-width: 768px) {
    .mobile-only { display: none !important; }
}
@media (max-width: 767px) {
    .desktop-only { display: none !important; }
}

/* ============================================== */
/* PENYESUAIAN MOBILE RESPONSIF (TAMBAHAN)       */
/* ============================================== */

/* Agar modal Swal tidak terpotong di layar kecil */
.swal2-container .swal2-popup {
    max-width: 360px !important;
    width: 92% !important;
}

/* Layar sangat kecil (seperti iPhone SE, 320px) */
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
    .rekammedis-card {
        padding: 16px;
    }
    .rekammedis-card .grid {
        gap: 0.75rem; /* gap-3 */
    }
    .card-value {
        font-size: 0.85rem;
    }
    .btn-action {
        font-size: 0.7rem;
        padding: 4px 8px;
    }
}

/* Fix efek hover terjebak di perangkat touch/sentuh */
@media (hover: none) {
    .rekammedis-row:hover {
        background-color: transparent;
    }
    .btn-action:hover {
        opacity: 1;
    }
    .btn-detail:hover {
        background-color: white;
        color: #0d9488;
        border-color: #cbd5e1;
    }
    /* Feedback sentuh yang lebih baik (active) */
    .btn-detail:active {
        background-color: #f0fdfa;
        border-color: #0d9488;
    }
    /* Hover back button */
    a:hover {
        background-color: white;
    }
    a:active {
        background-color: #f8fafc;
    }
}
</style>

@section('content')

<!--  -->
<section class="hero-section py-10 md:py-12 relative">
    <div class="container mx-auto max-w-5xl px-4 md:px-6 text-center relative z-10">
        <p class="text-teal-100 text-[10px] uppercase tracking-[0.2em] mb-2 font-bold">
            Klinik Gigi Dokter Ndeso
        </p>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
            Riwayat Rekam Medis
        </h1>
        <div class="w-12 h-0.5 bg-teal-300 mx-auto mb-3"></div>
        <p class="text-teal-50 text-sm max-w-lg mx-auto">
            Lihat catatan kesehatan dan hiwayat perawatan gigi Anda.
        </p>
    </div>
</section>

<!-- main content-->
<section class="py-6 md:py-8 bg-slate-100 min-h-screen">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">

        <!-- Tombol Aksi  -->
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 mb-6">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <a href="{{ route('pasien.dashboard') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-medium rounded-lg text-sm hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-sm"></i>
                    Kembali
                </a>
                <!-- SEARCH INPUT -->
                <div class="relative w-full sm:w-64 search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari dokter atau klinik...">
                </div>
            </div>
        </div>

        @if($rekammedis->count() > 0)

            <!-- dekstop tabel -->
            <div class="table-container desktop-only">
                <div class="table-header px-6 lg:px-8 py-4">
                    <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white items-center">
                        <div class="col-span-2">Tanggal</div>
                        <div class="col-span-2">Dokter</div>
                        <div class="col-span-2">Klinik</div>
                        <div class="col-span-2">Diagnosa</div>
                        <div class="col-span-2">Keluhan</div>
                        <div class="col-span-2 text-center">Aksi</div>
                    </div>
                </div>

                <div class="divide-y divide-slate-100" id="desktopList">
                    <!-- loop rekam medis -->
                    @foreach($rekammedis as $item)
                        <div class="rekammedis-row searchable-item grid grid-cols-12 gap-4 px-6 lg:px-8 py-4 items-start text-sm">
                            
                            <!-- Tanggal -->
                            <div class="col-span-2">
                                <div class="font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l') }}
                                </div>
                            </div>

                            <!-- Dokter -->
                            <div class="col-span-2 font-semibold text-teal-700" title="{{ $item->dokter->nama }}">
                                {{ $item->dokter->nama }}
                            </div>

                            <!-- Klinik  -->
                            <div class="col-span-2 text-slate-600 font-medium">
                               {{ $item->reservasi->jadwal->klinik->nama_klinik ?? 'N/A' }}
                            </div>

                            <!-- Diagnosa -->
                            <div class="col-span-2 text-slate-600 font-medium">
                                {{ $item->diagnosa }}
                            </div>

                            <!-- Keluhan  -->
                            <div class="col-span-2 text-slate-600 font-medium">
                                {{ $item->reservasi->keluhan }}
                            </div>

                            <!-- Aksi -->
                            <div class="col-span-2 flex items-center justify-center mt-1">
                                <a href="{{ route('pasien.rekam-medis.show', $item->id_rm) }}"
                                   class="btn-action btn-detail"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                    <span class="hidden lg:inline">Detail</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!--mobile card list -->
            <div class="flex flex-col gap-4 mobile-only" id="mobileList">
                @foreach($rekammedis as $item)
                    <div class="rekammedis-card searchable-item">
                        <!-- Baris Tanggal -->
                        <div class="mb-3">
                            <div class="text-lg font-bold text-slate-800">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </div>
                            <div class="text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l') }}
                            </div>
                        </div>

                        <div class="card-divider"></div>

                        <!-- Baris Info Detail -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <div class="card-label">Dokter</div>
                                <div class="card-value text-teal-700">{{ $item->dokter->nama }}</div>
                            </div>
                            <div>
                                <div class="card-label">Klinik</div>
                                <div class="card-value">{{ $item->reservasi->jadwal->klinik->nama_klinik ?? 'N/A' }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="card-label">Diagnosa</div>
                                <div class="card-value text-sm">{{ $item->diagnosa }}</div>
                            </div>
                            <div class="col-span-2">
                                <div class="card-label">Keluhan</div>
                                <div class="card-value text-sm">{{ $item->reservasi->keluhan }}</div>
                            </div>
                        </div>

                        <!-- Baris 3 Tombol Aksi -->
                        <div class="flex gap-2 mt-2">
                            <a href="{{ route('pasien.rekam-medis.show', $item->id_rm) }}"
                               class="btn-action btn-detail flex-1 py-3 text-sm justify-center">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $rekammedis->links('pagination::tailwind') }}
            </div>
        @else
            <!-- jika data kosong -->
            <div class="bg-white rounded-lg shadow-sm py-16 md:py-24 text-center border border-slate-200">
                <div class="mx-auto w-16 h-16 bg-teal-50 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas fa-file-medical text-3xl text-teal-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Rekam Medis</h3>
                <p class="text-slate-500 max-w-md mx-auto mb-8 px-4 text-sm">
                    Anda belum pernah melakukan perawatan gigi di klinik kami.
                </p>
                <a href="{{ route('pasien.pilih.klinik') }}"
                   class="inline-flex items-center px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg text-sm shadow-sm transition-colors">
                    <i class="fas fa-calendar-check mr-2"></i>
                    Buat Janji Temu
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    
    //search
    const searchInput = document.getElementById('searchInput'); //ambil serach input
    searchInput.addEventListener('keyup', function() { //ambil keyword
        const filter = this.value.toLowerCase(); //ambil isi input lowercase
        const items = document.querySelectorAll('.searchable-item'); //semua data list yg dicari
        //loop semua item satu satu
        items.forEach(item => {
            const text = item.textContent.toLowerCase(); //ambil isi text dr item
            item.style.display = text.includes(filter) ? "" : "none"; //kalo mengandung keyword tampil jika tidak sembuyikan
        });
    });

    // Notifikasi sukses
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#14b8a6',
        timer: 3000
    });
    @endif
    //notifikasi error
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#ef4444'
    });
    @endif
});
</script>
@endsection