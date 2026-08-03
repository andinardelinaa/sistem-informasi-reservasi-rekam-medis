@extends('layouts.dokter')

@section('title', 'Dashboard Dokter - Klinik Gigi Dokter Ndeso')
@section('header-title', 'Dashboard')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .menu-card {
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
    .menu-card .icon-box {
        transition: all 0.3s ease;
    }
    .menu-card:hover .icon-box {
        transform: scale(1.1);
    }
</style>

    <!-- HERO -->
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-700 via-teal-600 to-cyan-600 p-5 lg:p-8 rounded-2xl shadow-xl mb-5" style="border-bottom: 4px solid #22d3ee;">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 left-20 w-32 h-32 bg-cyan-300/10 blur-2xl rounded-full"></div>

        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-lg lg:text-3xl font-black text-white leading-tight">
                    Selamat Datang, <span class="text-cyan-100">{{ auth()->user()->nama ?? 'Dokter' }}</span>
                </h1>
                <p class="text-teal-50/80 mt-2 text-xs lg:text-sm max-w-xl leading-relaxed">
                    Pantau antrean pasien, aktivitas reservasi, dan jadwal praktik harian Anda.
                </p>
                
                <div class="flex flex-wrap gap-4 lg:gap-8 mt-4 lg:mt-6">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.15em] text-teal-200 font-semibold">Reservasi</p>
                        <h3 class="text-xl lg:text-3xl font-black text-white mt-0.5">{{ $stats['hari_ini'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.15em] text-teal-200 font-semibold">Rekam Medis</p>
                        <h3 class="text-xl lg:text-3xl font-black text-white mt-0.5">{{ $stats['rekam_medis'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.15em] text-teal-200 font-semibold">Jadwal Aktif</p>
                        <h3 class="text-xl lg:text-3xl font-black text-white mt-0.5">{{ $stats['jadwal_aktif'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex items-center justify-center">
                <div class="w-24 h-24 bg-white/10 backdrop-blur-sm border-2 border-white/20 flex items-center justify-center shadow-xl rounded-2xl">
                    <i class="fas fa-stethoscope text-white text-4xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- JADWAL + ANTREAN — SEBEAHAN, COMPACT -->
    <div class="grid grid-cols-2 gap-3 mb-5">

        <!-- JADWAL — CANTIK & COMPACT -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-teal-600 to-teal-500 px-3 lg:px-4 py-2 lg:py-3 flex items-center gap-1.5">
                <i class="fas fa-calendar-day text-white text-[10px] lg:text-xs"></i>
                <h2 class="text-[10px] lg:text-xs font-bold text-white uppercase tracking-wider">Jadwal Hari Ini</h2>
            </div>
            <div class="p-3 lg:p-4">
                @if(isset($jadwalHariIniList) && $jadwalHariIniList->count() > 0)
                    <div class="space-y-2">
                        @foreach($jadwalHariIniList as $jadwal)
                            <div class="relative bg-teal-50 border border-teal-200 rounded-lg p-2.5 lg:p-3 {{ isset($jadwal->is_override) ? 'bg-amber-50 border-amber-200' : '' }} {{ isset($jadwal->is_pengganti) ? 'bg-blue-50 border-blue-200' : '' }}">
                                @if(isset($jadwal->is_pengganti))
                                    <div class="absolute -top-1.5 -right-1.5 bg-blue-500 text-white text-[7px] font-bold uppercase px-1.5 py-0.5 rounded">
                                        Pengganti
                                    </div>
                                @endif
                                @if(isset($jadwal->is_override))
                                    <div class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[7px] font-bold uppercase px-1.5 py-0.5 rounded">
                                        Pindah
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 lg:w-10 lg:h-10 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                                        <i class="far fa-clock text-teal-500 text-xs lg:text-sm {{ isset($jadwal->is_override) ? 'text-amber-500' : '' }}"></i>
                                    </div>
                                    <div class="min-w-0">
                                        @if(!isset($jadwal->is_override))
                                            <p class="text-sm lg:text-lg font-black text-gray-800 leading-none">
                                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} <span class="text-gray-300 text-xs">-</span> {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                            </p>
                                        @else
                                            <p class="text-sm lg:text-base font-black text-amber-600 leading-none">Dialihkan</p>
                                        @endif
                                        <p class="text-[10px] lg:text-xs text-gray-500 font-semibold mt-0.5 truncate">{{ $jadwal->klinik->nama_klinik ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6 lg:py-8">
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2 border border-gray-200">
                            <i class="fas fa-calendar-times text-gray-300 text-lg"></i>
                        </div>
                        <p class="text-[11px] lg:text-xs text-gray-400 font-medium">Tidak ada jadwal</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ANTREAN — LIST SEDERHANA -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-3 lg:px-4 py-2 lg:py-3 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <i class="fas fa-user-clock text-white text-[10px] lg:text-xs"></i>
                    <h2 class="text-[10px] lg:text-xs font-bold text-white uppercase tracking-wider">Antrean</h2>
                </div>
                @isset($antreanHariIniList)
                    @if($antreanHariIniList->count() > 0)
                        <span class="text-[9px] lg:text-[10px] font-bold text-white bg-white/20 px-1.5 py-0.5 rounded">{{ $antreanHariIniList->count() }}</span>
                    @endif
                @endisset
            </div>
            <div class="p-2 lg:p-3 max-h-[200px] lg:max-h-[220px] overflow-y-auto custom-scrollbar">
                @isset($antreanHariIniList)
                    @if($antreanHariIniList->isNotEmpty())
                        @foreach($antreanHariIniList as $antrean)
                            @php
                                $status = $antrean->status ?? 'menunggu';
                                $isTindakan = $status === 'tindakan';
                                $dotColor = match($status) {
                                    'menunggu' => 'bg-amber-400',
                                    'tindakan' => 'bg-blue-500',
                                    'selesai' => 'bg-green-500',
                                    default => 'bg-gray-400',
                                };
                            @endphp

                            @if($isTindakan)
                                <a href="{{ route('dokter.rekam-medis.create', $antrean->id_reservasi) }}"
                                   class="flex items-center gap-2 px-2 py-2 rounded-lg hover:bg-teal-50 transition-colors group">
                                    <span class="w-2 h-2 {{ $dotColor }} rounded-full flex-shrink-0"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[12px] lg:text-[13px] font-bold text-gray-800 truncate">{{ $antrean->pasien->nama ?? 'Pasien' }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $antrean->jam_reservasi ?? '-' }}</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover:text-teal-500 transition-colors"></i>
                                </a>
                            @else
                                <div class="flex items-center gap-2 px-2 py-2 opacity-60">
                                    <span class="w-2 h-2 {{ $dotColor }} rounded-full flex-shrink-0"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[12px] lg:text-[13px] font-bold text-gray-800 truncate">{{ $antrean->pasien->nama ?? 'Pasien' }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $antrean->jam_reservasi ?? '-' }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-center py-6 lg:py-8">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-2 border border-gray-200">
                                <i class="fas fa-user-slash text-gray-300 text-lg"></i>
                            </div>
                            <p class="text-[11px] lg:text-xs text-gray-400 font-medium">Tidak ada antrean</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-6 lg:py-8">
                        <p class="text-[11px] lg:text-xs text-gray-400 font-medium">Data tidak tersedia</p>
                    </div>
                @endisset
            </div>
        </div>

    </div>

    <!-- MENU UTAMA -->
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-1 h-4 lg:h-5 bg-teal-600 rounded-sm"></div>
            <h2 class="text-xs lg:text-sm font-extrabold text-gray-800 uppercase tracking-wider">Menu Utama</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

            <!-- Lihat Antrean -->
            <a href="{{ route('dokter.antrean') }}" 
               class="menu-card group bg-white border-2 border-gray-200 hover:border-teal-500 rounded-xl lg:rounded-2xl p-4 lg:p-6 shadow-sm hover:shadow-lg">
                <div class="absolute top-0 left-0 right-0 h-1 bg-teal-500 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl lg:rounded-t-2xl"></div>
                <div class="flex items-start justify-between mb-3 lg:mb-5">
                    <div class="icon-box w-10 h-10 lg:w-14 lg:h-14 bg-teal-50 border-2 border-teal-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-lg lg:text-2xl text-teal-600"></i>
                    </div>
                    <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-gray-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all group-hover:bg-teal-50">
                        <i class="fas fa-arrow-right text-teal-600 text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h3 class="text-xs lg:text-sm font-extrabold text-gray-800 uppercase tracking-wider group-hover:text-teal-700 transition-colors">Lihat Antrean</h3>
                <p class="text-[10px] lg:text-xs text-gray-400 mt-1 font-medium">Kelola pasien menunggu</p>
                <div class="mt-2 lg:mt-4">
                    <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 bg-teal-50 text-teal-700 text-[9px] lg:text-[10px] font-bold rounded-md lg:rounded-lg border border-teal-100">
                        {{ $stats['hari_ini'] ?? 0 }} Pasien
                    </span>
                </div>
            </a>

            <!-- Rekam Medis -->
            <a href="{{ route('dokter.rekam-medis.index') }}" 
               class="menu-card group bg-white border-2 border-gray-200 hover:border-blue-500 rounded-xl lg:rounded-2xl p-4 lg:p-6 shadow-sm hover:shadow-lg">
                <div class="absolute top-0 left-0 right-0 h-1 bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl lg:rounded-t-2xl"></div>
                <div class="flex items-start justify-between mb-3 lg:mb-5">
                    <div class="icon-box w-10 h-10 lg:w-14 lg:h-14 bg-blue-50 border-2 border-blue-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-medical text-lg lg:text-2xl text-blue-600"></i>
                    </div>
                    <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-gray-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all group-hover:bg-blue-50">
                        <i class="fas fa-arrow-right text-blue-600 text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h3 class="text-xs lg:text-sm font-extrabold text-gray-800 uppercase tracking-wider group-hover:text-blue-700 transition-colors">Rekam Medis</h3>
                <p class="text-[10px] lg:text-xs text-gray-400 mt-1 font-medium">Riwayat diagnosis</p>
                <div class="mt-2 lg:mt-4">
                    <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 bg-blue-50 text-blue-700 text-[9px] lg:text-[10px] font-bold rounded-md lg:rounded-lg border border-blue-100">
                        {{ $stats['rekam_medis'] ?? 0 }} Data
                    </span>
                </div>
            </a>

            <!-- Jadwal Praktik -->
            <a href="{{ route('dokter.jadwal') }}" 
               class="menu-card group bg-white border-2 border-gray-200 hover:border-purple-500 rounded-xl lg:rounded-2xl p-4 lg:p-6 shadow-sm hover:shadow-lg">
                <div class="absolute top-0 left-0 right-0 h-1 bg-purple-500 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl lg:rounded-t-2xl"></div>
                <div class="flex items-start justify-between mb-3 lg:mb-5">
                    <div class="icon-box w-10 h-10 lg:w-14 lg:h-14 bg-purple-50 border-2 border-purple-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-lg lg:text-2xl text-purple-600"></i>
                    </div>
                    <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-gray-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all group-hover:bg-purple-50">
                        <i class="fas fa-arrow-right text-purple-600 text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h3 class="text-xs lg:text-sm font-extrabold text-gray-800 uppercase tracking-wider group-hover:text-purple-700 transition-colors">Jadwal Praktik</h3>
                <p class="text-[10px] lg:text-xs text-gray-400 mt-1 font-medium">Kelola jadwal</p>
                <div class="mt-2 lg:mt-4">
                    <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 bg-purple-50 text-purple-700 text-[9px] lg:text-[10px] font-bold rounded-md lg:rounded-lg border border-purple-100">
                        {{ $stats['jadwal_aktif'] ?? 0 }} Aktif
                    </span>
                </div>
            </a>

            <!-- Profil Saya -->
            <a href="{{ route('dokter.profil') }}" 
               class="menu-card group bg-white border-2 border-gray-200 hover:border-amber-500 rounded-xl lg:rounded-2xl p-4 lg:p-6 shadow-sm hover:shadow-lg">
                <div class="absolute top-0 left-0 right-0 h-1 bg-amber-500 opacity-0 group-hover:opacity-100 transition-opacity rounded-t-xl lg:rounded-t-2xl"></div>
                <div class="flex items-start justify-between mb-3 lg:mb-5">
                    <div class="icon-box w-10 h-10 lg:w-14 lg:h-14 bg-amber-50 border-2 border-amber-100 rounded-lg lg:rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-cog text-lg lg:text-2xl text-amber-600"></i>
                    </div>
                    <div class="w-7 h-7 lg:w-8 lg:h-8 rounded-lg bg-gray-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all group-hover:bg-amber-50">
                        <i class="fas fa-arrow-right text-amber-600 text-[10px] lg:text-xs"></i>
                    </div>
                </div>
                <h3 class="text-xs lg:text-sm font-extrabold text-gray-800 uppercase tracking-wider group-hover:text-amber-700 transition-colors">Profil Saya</h3>
                <p class="text-[10px] lg:text-xs text-gray-400 mt-1 font-medium">Pengaturan akun</p>
                <div class="mt-2 lg:mt-4">
                    <span class="px-2 py-0.5 lg:px-2.5 lg:py-1 bg-amber-50 text-amber-700 text-[9px] lg:text-[10px] font-bold rounded-md lg:rounded-lg border border-amber-100">
                        Edit
                    </span>
                </div>
            </a>

        </div>
    </div>

@endsection

@section('styles')
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const ctxReservasi = document.getElementById('reservasiChart');
        if (ctxReservasi) { 
            const ctxR = ctxReservasi.getContext('2d');

            const gradientReservasi = ctxR.createLinearGradient(0, 0, 0, 320);
            gradientReservasi.addColorStop(0, 'rgba(20, 184, 166, 0.4)');
            gradientReservasi.addColorStop(0.6, 'rgba(59, 130, 246, 0.1)');
            gradientReservasi.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

            const labels = {{ Js::from($chartMingguan['labels'] ?? []) }};
            const data = {{ Js::from($chartMingguan['data'] ?? []) }};

            new Chart(ctxReservasi, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Reservasi',
                        data: data,
                        borderColor: '#0d9488',
                        backgroundColor: gradientReservasi,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointBackgroundColor: '#ffffff', 
                        pointBorderColor: '#0d9488',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#0d9488',
                        pointHoverBorderColor: '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#94a3b8',
                            bodyColor: '#ffffff',
                            padding: 14,
                            cornerRadius: 8, 
                            displayColors: false,
                            titleFont: { weight: '600', size: 12 },
                            bodyFont: { weight: '800', size: 16 },
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Pasien';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { 
                                color: '#e2e8f0', 
                                drawBorder: false,
                                borderDash: [5, 5]
                            },
                            ticks: { color: '#6b7280', padding: 12, font: { size: 11, weight: '600' } },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#6b7280', padding: 10, font: { size: 11, weight: '600' } },
                            border: { display: false }
                        }
                    },
                    animation: { duration: 1800, easing: 'easeOutQuart' }
                }
            });
        }
    });
</script>
@endsection