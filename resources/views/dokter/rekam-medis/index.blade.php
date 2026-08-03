@extends('layouts.dokter')
@section('title', 'Riwayat Rekam Medis - Klinik Gigi Dokter Ndeso')
@section('header-title', 'Rekam Medis')
@include('layouts.partials.alert')

@section('content')
<div class="space-y-8">
    <!-- ambil dokter yang sedang login -->
    @php
        $dokter = Auth::user()->dokter;
    @endphp

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Rekam Medis -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Rekam Medis</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-file-medical text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Hari Ini -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Rekam Medis Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['hari_ini'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Pasien Unik -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Pasien Diperiksa</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pasien_unik'] ?? 0 }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header Utama (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">
                    Daftar Rekam Medis
                </h2>
                
                <!-- serach dan filter -->
                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto items-center">
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="text" id="searchInput" placeholder="Cari nama pasien..." 
                            class="w-full sm:w-56 pl-8 pr-3 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    
                    <input type="date" id="dateFilter" 
                        class="px-3 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-gray-700 text-sm w-full sm:w-auto">
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white border-b border-gray-200">
                    <tr>
                        <!-- header -->
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal Periksa</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Pasien</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Diagnosa</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Gigi</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    <!-- loop data rekam medis -->
                    @forelse($rekamMedis as $item)
                    @php
                        $no = ($rekamMedis->currentPage() - 1) * $rekamMedis->perPage() + $loop->iteration;
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors rm-row"
                        data-name="{{ strtolower($item->reservasi->pasien->nama ?? '') }}" 
                        data-tanggal="{{ $item->tgl_periksa->format('Y-m-d') }}"
                        data-id="{{ $item->id_rm }}">
                        
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                            {{ $no }}.
                        </td>
                        <!-- tgl periksa -->
                        <td class="px-5 py-4 text-sm text-gray-800 font-medium">
                            {{ $item->tgl_periksa->format('d M Y') }}
                        </td>
                        <!-- nama dan no hp pasien -->
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $item->reservasi->pasien->nama ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item->reservasi->pasien->no_hp ?? '-' }}</p>
                        </td>
                        <!-- diagnosa -->
                        <td class="px-5 py-4 text-sm text-gray-700 max-w-xs truncate">
                            {{ Str::limit($item->diagnosa, 50) }}
                        </td>
                        <!-- jmlh gigi yg ditindak -->
                        <td class="px-5 py-4 text-center">
                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">
                                {{ $item->detailGigi->count() }} Gigi
                            </span>
                        </td>
                        <!-- aksi detail -->
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('dokter.rekam-medis.show', $item->id_rm) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                <i class="fas fa-eye text-[10px] mr-1"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- jika data ksoong -->
                        <td colspan="6" class="py-16 text-center bg-gray-50">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                    <i class="fas fa-file-medical text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada rekam medis</p>
                                <p class="text-xs text-gray-500 mt-1">Data akan muncul setelah pemeriksaan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Table Footer -->
        <div class="px-6 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <p class="text-xs text-gray-500">
                Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $rekamMedis->count() }}</span> dari <span class="font-bold text-gray-800">{{ $rekamMedis->total() }}</span> data
            </p>
            {{ $rekamMedis->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// menunggu halaman selese dimuat
document.addEventListener('DOMContentLoaded', function() {
    //ambil elemen html
    const searchInput = document.getElementById('searchInput'); //search input
    const dateFilter = document.getElementById('dateFilter'); //input filter
    const tableBody = document.getElementById('tableBody'); //isi tabel
    const allRows = tableBody ? Array.from(tableBody.querySelectorAll('.rm-row')) : []; //seluruh baris data rekam medis
    const showingCount = document.getElementById('showingCount'); //jumlah data yg tampil
    //fucntion filter
    function filterTable() {
        //ambil elemen html
        const searchTerm = searchInput.value.toLowerCase(); //keyword pencarian
        const dateVal = dateFilter.value; //date filter
        let visibleCount = 0; //hitung data yang tampil
        //periksa setiap baris tabel
        allRows.forEach(row => {
            //ambil nama dan tanggal dari attribut data
            const name = (row.dataset.name || '').toLowerCase();
            const tanggal = row.dataset.tanggal || '';
            //cek kecocokan 
            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesDate = !dateVal || tanggal === dateVal;
            //jika cocok tampilkan jika tidak cocok sembunyikan
            if (matchesSearch && matchesDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        //perbarui jmlh data yang tampil
        showingCount.textContent = visibleCount;
    }
    //realtime
    searchInput.addEventListener('input', filterTable);
    dateFilter.addEventListener('change', filterTable);
});
</script>
@endsection