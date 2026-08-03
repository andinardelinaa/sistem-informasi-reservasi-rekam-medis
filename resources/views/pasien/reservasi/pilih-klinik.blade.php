@extends('layouts.pasien')

@section('title', 'Pilih Klinik - Reservasi Klinik Gigi Dokter Ndeso')

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
    background-color: #f1f5f9;
}

.pattern-bg {
    background-color: #f8fafc;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%230d9488' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.klinik-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
}

.klinik-card:hover {
    box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.1), 0 2px 4px -1px rgba(13, 148, 136, 0.06);
    border-color: #0d9488;
    transform: translateY(-2px);
}

.klinik-card:active {
    transform: translateY(0);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    border-color: #0d9488;
    background-color: #f0fdfa;
}

.btn-history {
    background-color: white;
    border: 1px solid #cbd5e1;
    color: #475569;
    transition: all 0.2s ease;
}

.btn-history:hover {
    border-color: #0d9488;
    color: #0d9488;
    background-color: #f0fdfa;
    box-shadow: 0 2px 4px rgba(13, 148, 136, 0.1);
}

.btn-history:active {
    transform: scale(0.98);
    background-color: #f0fdfa;
    border-color: #0d9488;
    color: #0d9488;
}

.icon-wrapper {
    background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
    border: 1px solid #99f6e4;
}

@media (max-width: 374px) {
    .klinik-card .card-inner {
        padding: 16px 12px;
    }
    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
    }
    .icon-wrapper i {
        font-size: 20px;
    }
    .klinik-card .clinic-name {
        font-size: 14px;
    }
    .klinik-card .clinic-sub {
        font-size: 11px;
    }
    .section-title {
        font-size: 20px;
    }
    .section-desc {
        font-size: 13px;
    }
}

@media (hover: none) {
    .klinik-card:hover {
        transform: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border-color: #e2e8f0;
    }
    .klinik-card:active {
        transform: scale(0.98);
        border-color: #0d9488;
        background-color: #f0fdfa;
    }
}
</style>

@section('content')
<section class="min-h-screen pattern-bg py-8 md:py-12 lg:py-16">
    <div class="container mx-auto px-4 sm:px-5 max-w-5xl">

        <div class="text-center mb-8 md:mb-10 lg:mb-12">
            <!-- judul -->
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-2 sm:mb-3 section-title">
                PILIH LOKASI KLINIK
            </h1>
            <p class="text-slate-500 max-w-lg mx-auto text-sm sm:text-base px-2 section-desc">
                Silakan pilih klinik tempat Anda ingin melakukan reservasi pemeriksaan.
            </p>
            <div class="w-12 sm:w-16 h-1 bg-teal-500 mx-auto mt-3 sm:mt-4 rounded-full"></div>
        </div>

        <!-- Grid Klinik  -->
        <div class="grid grid-cols-2 gap-3 sm:gap-6 max-w-4xl mx-auto">
            <!-- ambil data klinik -->
            @foreach($kliniks as $klinik)
            <a href="{{ route('pasien.reservasi.form-reservasi', $klinik->id_klinik) }}"
               class="block klinik-card group">
                <div class="card-inner p-4 sm:p-8 text-center">

                    <div class="icon-wrapper w-14 h-14 sm:w-20 sm:h-20 rounded-2xl mx-auto flex items-center justify-center mb-3 sm:mb-5 group-hover:scale-105 transition-transform duration-300">
                        <i class="fas fa-hospital text-xl sm:text-3xl text-teal-600"></i>
                    </div>
                    <!-- tampil nama klinik -->
                    <h3 class="clinic-name text-base sm:text-xl font-bold text-slate-800 mb-1 sm:mb-2 group-hover:text-teal-700 transition-colors">
                        {{ $klinik->nama_klinik }}
                    </h3>

                    <p class="clinic-sub text-slate-500 text-xs sm:text-sm mb-0">
                        Pilih untuk melihat jadwal dokter
                    </p>

                    <div class="mt-2 sm:mt-4 flex items-center justify-center text-slate-300 group-hover:text-teal-500 transition-colors">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </a>
            @endforeach

        </div>
        <!-- button lihat reservasi -->
        <div class="flex justify-center mt-8 sm:mt-10 md:mt-12 px-0 sm:px-0">
            <a href="{{ route('pasien.riwayat') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center px-6 sm:px-8 py-3 rounded-lg btn-history font-medium text-sm">
                <i class="fas fa-history mr-2 text-xs"></i>
                Lihat Riwayat Reservasi
            </a>
        </div>
    </div>
    <div class="text-center mt-6">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                &copy; Klinik Gigi Dokter Ndeso 
            </p> 
    </div>
</section>

@endsection