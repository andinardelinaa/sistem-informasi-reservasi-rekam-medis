@extends('layouts.admin')
@section('title', 'Dashboard Admin - Klinik Gigi Dokter Ndeso')
@section('header-title', 'Dashboard')
@include('layouts.partials.alert')

@section('content')

    <!-- hero -->
    <div class="relative overflow-hidden bg-gradient-to-r from-teal-700 via-teal-600 to-cyan-600 p-8 shadow-xl mb-8 border-b-4 border-cyan-400">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-20 w-32 h-32 bg-cyan-300/10 blur-2xl"></div>

        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm uppercase tracking-[0.3em] font-bold mb-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-cyan-300 inline-block animate-pulse"></span> Dashboard Admin
                </p>
                <!-- ambil nama user admin yang login -->
                <h1 class="text-3xl font-black text-white leading-tight">
                    Selamat Datang, <span class="text-cyan-100">{{ auth()->user()->nama ?? 'Admin' }}</span>
                </h1>
                <p class="text-teal-50/80 mt-3 text-sm max-w-xl leading-relaxed">
                    Kelola operasional klinik, pantau performa dokter, dan analisis tren reservasi secara menyeluruh.
                </p>
                <!-- statistik -->
                <div class="flex flex-wrap gap-8 mt-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-teal-100 font-semibold">Reservasi Hari Ini</p>
                        <h3 class="text-3xl font-black text-white mt-1">{{ $stats['reservasi_hari_ini'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-teal-100 font-semibold">Total Pasien</p>
                        <h3 class="text-3xl font-black text-white mt-1">{{ $stats['total_pasien'] ?? 0 }}</h3>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-teal-100 font-semibold">Dokter Aktif</p>
                        <h3 class="text-3xl font-black text-white mt-1">{{ $stats['dokter_aktif'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="hidden lg:flex items-center justify-center">
                <div class="w-24 h-24 bg-white/10 backdrop-blur-sm border-2 border-white/20 flex items-center justify-center shadow-xl">
                    <i class="fas fa-hospital-user text-white text-4xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <form method="GET" action="{{ route('admin.dashboard') }}" class="bg-white shadow-sm border border-gray-100 p-4 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <!-- filter klinik -->
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-teal-50 border border-teal-200 flex items-center justify-center">
                <i class="fas fa-filter text-teal-600 text-sm"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Filter Data</p>
                <p class="text-xs text-gray-400">
                    <!-- ambil data klinik -->
                    @if($klinikTerpilih)
                        Menampilkan: <span class="text-teal-600 font-semibold">{{ $klinikTerpilih->nama_klinik }}</span>
                    @else
                        Semua klinik
                    @endif
                </p>
            </div>
        </div>  

        <div class="flex flex-wrap items-center gap-3">
            <select name="klinik_id" class="h-10 px-4 pr-10 bg-white border-2 border-gray-200 text-sm font-semibold text-gray-700 rounded-none focus:outline-none focus:border-teal-500 min-w-[200px]">
                <option value="">Semua Klinik</option>
                <!-- loop data klinik -->
                @foreach($klinikList as $klinik)
                    <option value="{{ $klinik->id_klinik }}" {{ (int) request('klinik_id') == $klinik->id_klinik ? 'selected' : '' }}>
                        {{ $klinik->nama_klinik }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="h-10 px-5 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-check text-xs"></i> Terapkan
            </button>
            <!-- button reset -->
            @if(request('klinik_id'))
                <a href="{{ route('admin.dashboard') }}" class="h-10 px-4 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-bold uppercase tracking-wider flex items-center gap-2 border border-gray-200">
                    <i class="fas fa-times text-xs"></i> Reset
                </a>
            @endif
        </div>
    </form>
    <!-- card statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Reservasi -->
        <div class="relative overflow-hidden bg-white shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 border-l-4 border-l-teal-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Reservasi</p>
                    <p class="mt-2 text-4xl font-extrabold text-gray-900">{{ $stats['total_reservasi'] ?? 0 }}</p>
                </div>
                <div class="h-14 w-14 bg-teal-500 flex items-center justify-center shadow-md shadow-teal-500/30">
                    <i class="fas fa-calendar-check text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Reservasi Hari Ini -->
        <div class="relative overflow-hidden bg-white shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Reservasi Hari Ini</p>
                    <p class="mt-2 text-4xl font-extrabold text-gray-900">{{ $stats['reservasi_hari_ini'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1 font-medium">{{ now()->format('d M Y') }}</p>
                </div>
                <div class="h-14 w-14 bg-amber-500 flex items-center justify-center shadow-md shadow-amber-500/30">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Rekam Medis Bulan Ini -->
        <div class="relative overflow-hidden bg-white shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Rekam Medis</p>
                    <p class="mt-2 text-4xl font-extrabold text-gray-900">{{ $stats['rekam_medis_bulan_ini'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1 font-medium">Bulan ini</p>
                </div>
                <div class="h-14 w-14 bg-blue-500 flex items-center justify-center shadow-md shadow-blue-500/30">
                    <i class="fas fa-file-medical-alt text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Dokter Aktif -->
        <div class="relative overflow-hidden bg-white shadow-[0_4px_20px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 p-6 border border-gray-100 border-l-4 border-l-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Dokter Aktif</p>
                    <p class="mt-2 text-4xl font-extrabold text-gray-900">{{ $stats['dokter_aktif'] ?? 0 }}</p>
                </div>
                <div class="h-14 w-14 bg-purple-500 flex items-center justify-center shadow-md shadow-purple-500/30">
                    <i class="fas fa-user-md text-white text-2xl"></i>
                </div>
            </div>
        </div>

    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Line Chart Tren Reservasi 7 hari -->
        <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-teal-500">
            <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-md font-bold text-gray-800 uppercase tracking-wider">
                    <i class="fas fa-chart-line mr-2 text-teal-500"></i> Tren Reservasi
                </h2>
                <div class="flex items-center gap-2">
                    <button data-range="minggu" class="chart-range-btn text-xs font-semibold bg-teal-100 text-teal-700 px-3 py-1.5 border border-teal-200 hover:bg-teal-200 transition-colors">Mingguan</button>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-teal-50 border border-teal-100 p-4">
                        <!-- ambil data -->
                        <p class="text-xs uppercase font-bold text-teal-600 tracking-wider">Rata-rata/Hari</p>
                        <p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $stats['rata_rata_reservasi'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-100 p-4">
                        <p class="text-xs uppercase font-bold text-blue-600 tracking-wider">Hari Terpadat</p>
                        <p class="mt-2 text-lg font-extrabold text-gray-900">{{ $stats['hari_terpadat'] ?? '-' }}</p>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 p-4">
                        <p class="text-xs uppercase font-bold text-amber-600 tracking-wider">Performa</p>
                        <p class="mt-2 text-lg font-extrabold text-gray-900 flex items-center gap-1"><i class="fas fa-arrow-up text-amber-500 text-sm"></i> Naik</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <!-- tampilkan di chart -->
                    <canvas id="reservasiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Donat Chart Status Reservasi -->
        <div class="bg-white shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-indigo-500">
            <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-md font-bold text-gray-800 uppercase tracking-wider">
                    <i class="fas fa-chart-pie mr-2 text-indigo-500"></i> Status Reservasi
                </h2>
                <span class="text-xs font-semibold bg-indigo-100 text-indigo-700 px-3 py-1.5 border border-indigo-200">Keseluruhan</span>
            </div>
            <div class="p-6">
                <div class="relative h-72 flex items-center justify-center">
                    <!-- tampilkan xhart -->
                    <canvas id="statusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <p class="text-4xl font-black text-gray-800">{{ array_sum($chartStatus['data'] ?? []) }}</p>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Bar Chart Dokter Teraktif -->
        <div class="bg-white shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-purple-500">
            <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-md font-bold text-gray-800 uppercase tracking-wider">
                    <i class="fas fa-trophy mr-2 text-purple-500"></i> Dokter Teraktif
                </h2>
                <span class="text-xs font-semibold bg-purple-100 text-purple-700 px-3 py-1.5 border border-purple-200">Bulan Ini</span>
            </div>
            <div class="p-6">
                <div class="relative h-80">
                    <!-- tampilkan chart -->
                    <canvas id="dokterAktifChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart Rekam Medis per Bulan -->
        <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-blue-500">
            <div class="bg-gray-50/80 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-md font-bold text-gray-800 uppercase tracking-wider">
                    <i class="fas fa-notes-medical mr-2 text-blue-500"></i> Rekam Medis per Bulan
                </h2>
                <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-3 py-1.5 border border-blue-200">Tahun Berjalan</span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-100 p-4">
                        <!-- ambil data -->
                        <p class="text-xs uppercase font-bold text-blue-600 tracking-wider">Total RM Tahun Ini</p>
                        <p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $stats['total_rm_tahunan'] ?? 0 }}</p>
                    </div>
                    <div class="bg-cyan-50 border border-cyan-100 p-4">
                        <p class="text-xs uppercase font-bold text-cyan-600 tracking-wider">Rata-rata/Bulan</p>
                        <p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $stats['rata_rata_rm'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="relative h-80">
                    <!-- tampilkan chart -->
                    <canvas id="rekamMedisChart"></canvas>
                </div>
            </div>
        </div>

    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        <!-- Reservasi Terbaru -->
        <div class="lg:col-span-2 bg-white shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-history mr-2 text-cyan-400"></i>
                    <h2 class="text-md font-bold text-white uppercase tracking-wider">Reservasi Terbaru</h2>
                </div>
                <span class="w-2 h-2 bg-cyan-400 animate-pulse"></span>
            </div>
            <div class="p-5 bg-white flex-grow overflow-x-auto">
                <!-- ambil data reservasi  -->
                @isset($reservasiTerbaru)
                    @if($reservasiTerbaru->isNotEmpty())
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b-2 border-gray-200">
                                    <th class="py-3 px-4 text-xs font-black uppercase tracking-wider text-gray-500">Pasien</th>
                                    <th class="py-3 px-4 text-xs font-black uppercase tracking-wider text-gray-500">Dokter</th>
                                    <th class="py-3 px-4 text-xs font-black uppercase tracking-wider text-gray-500">Tanggal</th>
                                    <th class="py-3 px-4 text-xs font-black uppercase tracking-wider text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- loop reservasi -->
                                @foreach($reservasiTerbaru as $reservasi)
                                    @php
                                        $status = $reservasi->status ?? 'menunggu';
                                        $badgeClass = match($status) {
                                            'menunggu' => 'bg-amber-50 text-amber-600 border-amber-200',
                                            'tindakan' => 'bg-blue-50 text-blue-600 border-blue-200',
                                            'selesai' => 'bg-green-50 text-green-600 border-green-200',
                                            'dibatalkan' => 'bg-red-50 text-red-600 border-red-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200',
                                        };
                                    @endphp
                                    <tr class="border-b border-red-100 hover:bg-red-50/50 transition-colors">
                                        <!-- ambil daranya -->
                                        <td class="py-3 px-4 text-sm font-bold text-gray-800">{{ $reservasi->pasien->nama ?? '-' }}</td>
                                        <td class="py-3 px-4 text-sm font-medium text-gray-600">{{ $reservasi->jadwal->dokter->nama ?? '-' }}</td>
                                        <td class="py-3 px-4 text-xs font-semibold text-gray-500">{{ \Carbon\Carbon::parse($reservasi->tgl_reservasi)->format('d M Y') }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest border {{ $badgeClass }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                    <!-- jika data reservasi kosong -->
                        <div class="text-center py-16">
                            <div class="w-16 h-16 bg-red-50 flex items-center justify-center mx-auto mb-4 border border-gray-200">
                                <i class="fas fa-calendar-xmark text-3xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 font-bold uppercase tracking-wider text-sm">Belum ada reservasi</p>
                        </div>
                    @endif
                @endisset
            </div>
        </div>

        <!-- aksi cepat -->
        <div class="bg-white shadow-sm border border-gray-100 overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-bolt mr-2 text-white"></i>
                    <h2 class="text-md font-bold text-white uppercase tracking-wider">Aksi Cepat</h2>
                </div>
            </div>
            <div class="p-6 bg-white flex-grow flex flex-col justify-center gap-4">
                <!-- kelola dokter -->
                <a href="{{ route('admin.dokter.index') }}" class="group flex items-center gap-4 p-4 bg-white border-2 border-gray-200 hover:bg-teal-600 hover:border-teal-600 transition-all duration-300">
                    <div class="w-10 h-10 bg-teal-50 border border-teal-200 flex items-center justify-center group-hover:bg-white/20 group-hover:border-white/40 transition-colors">
                        <i class="fas fa-user-md text-teal-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-white uppercase tracking-wider transition-colors">Kelola Dokter</span>
                </a>
                <!-- reservasi -->
                <a href="{{ route('admin.reservasi.index') }}" class="group flex items-center gap-4 p-4 bg-white border-2 border-gray-200 hover:bg-blue-600 hover:border-blue-600 transition-all duration-300">
                    <div class="w-10 h-10 bg-blue-50 border border-blue-200 flex items-center justify-center group-hover:bg-white/20 group-hover:border-white/40 transition-colors">
                        <i class="fas fa-calendar-alt text-blue-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-white uppercase tracking-wider transition-colors">Kelola Reservasi</span>
                </a>
                <!-- pasien -->
                <a href="{{ route('admin.pasien.index') }}" class="group flex items-center gap-4 p-4 bg-white border-2 border-gray-200 hover:bg-purple-600 hover:border-purple-600 transition-all duration-300">
                    <div class="w-10 h-10 bg-purple-50 border border-purple-200 flex items-center justify-center group-hover:bg-white/20 group-hover:border-white/40 transition-colors">
                        <i class="fas fa-users text-purple-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-white uppercase tracking-wider transition-colors">Data Pasien</span>
                </a>
                <!-- jadwal -->
                <a href="{{ route('admin.jadwal.index') }}" class="group flex items-center gap-4 p-4 bg-white border-2 border-gray-200 hover:bg-amber-500 hover:border-amber-500 transition-all duration-300">
                    <div class="w-10 h-10 bg-amber-50 border border-amber-200 flex items-center justify-center group-hover:bg-white/20 group-hover:border-white/40 transition-colors">
                        <i class="fas fa-chart-bar text-amber-600 group-hover:text-white"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-white uppercase tracking-wider transition-colors">Jadwal Dokter</span>
                </a>
            </div>
        </div>

    </div>

@endsection

@section('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // menungggu halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function () {
        
        //mengatur tampilan global chart js
        Chart.defaults.font.family = "'Inter', 'sans-serif'"; //font
        Chart.defaults.plugins.tooltip.cornerRadius = 0; //kotak yg muncul ketika curson diarahkan ke grafik

        //ambil elemen canvas
        const ctxReservasi = document.getElementById('reservasiChart');
        if (ctxReservasi) { //cek apakah canvas ada
            const ctxR = ctxReservasi.getContext('2d'); //ambil context canvas
            //buang warna gradasi
            const gradientReservasi = ctxR.createLinearGradient(0, 0, 0, 400);
            gradientReservasi.addColorStop(0, 'rgba(20, 184, 166, 0.5)');
            gradientReservasi.addColorStop(1, 'rgba(20, 184, 166, 0.0)');
            // ambil data dari laravel
            const labels = {{ Js::from($chartReservasi['labels'] ?? []) }};
            const data = {{ Js::from($chartReservasi['data'] ?? []) }};
            // buat grafik
            const reservasiChart = new Chart(ctxReservasi, {
                type: 'line', //line chart
                data: { //data chart
                    labels: labels, //sumbu x
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
                            displayColors: false,
                            titleFont: { weight: '600', size: 12 },
                            bodyFont: { weight: '800', size: 16 },
                            callbacks: { label: (c) => c.parsed.y + ' Reservasi' } //sesuai tooltip
                        }
                    },
                    scales: {
                        y: { //sumbu y, vertikal
                            beginAtZero: true,
                            grid: { color: '#e2e8f0', drawBorder: false, borderDash: [5, 5] },
                            ticks: { color: '#6b7280', padding: 12, font: { size: 11, weight: '600' } },
                            border: { display: false }
                        },
                        x: { //sumbu x, horizontal
                            grid: { display: false },
                            ticks: { color: '#6b7280', padding: 10, font: { size: 11, weight: '600' } },
                            border: { display: false }
                        }
                    },
                    animation: { duration: 1800, easing: 'easeOutQuart' }
                }
            });

        }

        // donat chart, ambil elemen canvas
        const ctxStatus = document.getElementById('statusChart');
        if (ctxStatus) { //cek apakah canvas ada?
            //ambil data dari laravel
            const statusLabels = {{ Js::from($chartStatus['labels'] ?? []) }};
            const statusData = {{ Js::from($chartStatus['data'] ?? []) }};
            // buat chart baru
            new Chart(ctxStatus, {
                type: 'doughnut', //jenis chart
                data: { //data chart
                    labels: statusLabels,
                    datasets: [{ //kumpulan angka yg akan digambar
                        data: statusData,
                        backgroundColor: ['#94a3b8', '#b910b0', '#67ea72', '#f43f5e', '#f59e0b'],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 8,
                        spacing: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, pointStyle: 'rectRounded', padding: 16, color: '#374151', font: { size: 11, weight: '700' } }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a', padding: 12,
                            titleFont: { weight: '600' }, bodyFont: { weight: '700' },
                            callbacks: { label: (c) => ' ' + c.label + ': ' + c.parsed + ' Reservasi' }
                        }
                    },
                    animation: { animateRotate: true, duration: 1500 }
                }
            });
        }

        //  Bar Chart Dokter Teraktif, ambil canvas
        const ctxDokter = document.getElementById('dokterAktifChart');
        if (ctxDokter) { //cek apakah canvas ada
        //ambil data dari laravel
            const dokterLabels = {{ Js::from($chartDokterAktif['labels'] ?? []) }};
            const dokterData = {{ Js::from($chartDokterAktif['data'] ?? []) }};
        // buat chart
            new Chart(ctxDokter, {
                type: 'bar', //jenis chart
                data: {
                    labels: dokterLabels,
                    datasets: [{
                        label: 'Tindakan',
                        data: dokterData,
                        backgroundColor: '#8b5cf6',
                        hoverBackgroundColor: '#7c3aed',
                        borderWidth: 0,
                        borderSkipped: false,
                    }]
                },
                options: {
                    indexAxis: 'y', // Membuat bar menjadi horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a', padding: 12,
                            callbacks: { label: (c) => c.parsed.x + ' Tindakan' }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0', drawBorder: false, borderDash: [5, 5] },
                            ticks: { color: '#6b7280', font: { weight: '600' } },
                            border: { display: false }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#1f2937', font: { size: 12, weight: '800' } },
                            border: { display: false }
                        }
                    },
                    animation: { duration: 1500, easing: 'easeOutQuart' }
                }
            });
        }

        // Line Area Chart Rekam Medis
        const ctxRM = document.getElementById('rekamMedisChart');
        if (ctxRM) {
            // ambil context canvas
            const ctxRMContext = ctxRM.getContext('2d');
            // buat gradasi
            const gradientRM = ctxRMContext.createLinearGradient(0, 0, 0, 400);
            gradientRM.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
            gradientRM.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
            // ambil rmlabels dan data
            const rmLabels = {{ Js::from($chartRekamMedis['labels'] ?? []) }}; // ['Jan', 'Feb', 'Mar', ...]
            const rmData = {{ Js::from($chartRekamMedis['data'] ?? []) }};
            // buat chart
            new Chart(ctxRM, {
                type: 'line',
                data: {
                    labels: rmLabels,
                    datasets: [{
                        label: 'Rekam Medis',
                        data: rmData,
                        borderColor: '#3b82f6',
                        backgroundColor: gradientRM,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#ffffff', 
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#3b82f6',
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
                            padding: 14,
                            displayColors: false,
                            callbacks: { label: (c) => c.parsed.y + ' Rekam Medis' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#e2e8f0', drawBorder: false, borderDash: [5, 5] },
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