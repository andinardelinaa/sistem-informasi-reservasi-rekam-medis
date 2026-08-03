@extends('layouts.admin')
@section('header-title', 'Kelola Reservasi - ' . ($klinikNama ?? 'Klinik'))
@include('layouts.partials.alert')

@section('content')

<style>
.cs-wrap { position: relative; }
.cs-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: #fff; border: 1px solid #d1d5db; border-radius: 2px; font-size: 0.875rem; text-align: left; cursor: pointer; transition: all 0.15s; }
.cs-btn:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.cs-btn:not(:disabled):hover { border-color: #9ca3af; }
.cs-btn.active { border-color: #0f766e; box-shadow: 0 0 0 1px #0f766e; }
.cs-btn.active i { transform: rotate(180deg); }

.cs-list { position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; max-height: 250px; overflow-y: auto; opacity: 0; visibility: hidden; transform: translateY(-5px); transition: all 0.15s ease; }
.cs-list.active { opacity: 1; visibility: visible; transform: translateY(0); }
.cs-list::-webkit-scrollbar { width: 4px; }
.cs-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.cs-item { padding: 0.5rem 0.75rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; transition: background 0.1s; }
.cs-item:hover { background: #f0fdfa; color: #0f766e; }
.cs-item.selected { background: #f0fdfa; color: #0f766e; font-weight: 600; }
</style>

<div class="space-y-8">

    <!--statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-7xl mx-auto">
        <!-- Total Reservasi -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Reservasi</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Terdaftar dalam sistem</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Reservasi Hari Ini -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Reservasi Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['hari_ini'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-clock text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Menunggu Konfirmasi -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Menunggu Konfirmasi</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['menunggu'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Perlu verifikasi</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- Header Utama (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Reservasi</h2>
        </div>

        <!-- Filter Bar -->
        <div class="px-6 py-3 border-b border-gray-200 bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <!-- Search -->
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="text" id="searchInput" placeholder="Cari nama pasien/dokter..." 
                               class="w-full sm:w-56 pl-8 pr-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    
                    <!-- Filter Status -->
                    <div class="cs-wrap w-full sm:w-44" id="statusFilter_wrap">
                        <button type="button" class="cs-btn">
                            <span>Semua Status</span>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                        </button>
                        <div class="cs-list">
                            <div class="cs-item selected" data-val="">Semua Status</div>
                            <div class="cs-item" data-val="menunggu">Menunggu</div>
                            <div class="cs-item" data-val="tindakan">Tindakan</div>
                            <div class="cs-item" data-val="dibatalkan">Dibatalkan</div>
                            <div class="cs-item" data-val="selesai">Selesai</div>
                            <div class="cs-item" data-val="terdampak">Terdampak</div>
                        </div>
                        <input type="hidden" id="statusFilter" name="statusFilter" value="">
                    </div>

                    <!-- Filter Bulan -->
                    <div class="cs-wrap w-full sm:w-44" id="monthFilter_wrap">
                        <button type="button" class="cs-btn">
                            <span>Semua Bulan</span>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                        </button>
                        <div class="cs-list">
                            <div class="cs-item selected" data-val="">Semua Bulan</div>
                            <div class="cs-item" data-val="1">Januari</div>
                            <div class="cs-item" data-val="2">Februari</div>
                            <div class="cs-item" data-val="3">Maret</div>
                            <div class="cs-item" data-val="4">April</div>
                            <div class="cs-item" data-val="5">Mei</div>
                            <div class="cs-item" data-val="6">Juni</div>
                            <div class="cs-item" data-val="7">Juli</div>
                            <div class="cs-item" data-val="8">Agustus</div>
                            <div class="cs-item" data-val="9">September</div>
                            <div class="cs-item" data-val="10">Oktober</div>
                            <div class="cs-item" data-val="11">November</div>
                            <div class="cs-item" data-val="12">Desember</div>
                        </div>
                        <input type="hidden" id="monthFilter" name="monthFilter" value="">
                    </div>
                </div>
               
                <div class="flex flex-wrap gap-2 w-full lg:w-auto justify-end">
                    <!-- tambah-->
                     <a href="{{ route('admin.reservasi.create') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                        <i class="fas fa-plus mr-2 text-[10px]"></i> Tambah
                    </a>
                    <!-- word -->
                     <a href="{{ route('admin.reservasi.export.word') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                        <i class="fas fa-file-word mr-2 text-[10px]"></i> Word
                    </a>
                    <!-- excel -->
                     <a href="{{ route('admin.reservasi.export.excel') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                        <i class="fas fa-file-excel mr-2 text-[10px]"></i> Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div id="tableContent" class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white border-b border-gray-200">
                    <tr>
                        <!-- header -->
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Pasien</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jam</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dokter</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-52">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    <!-- perulangan dr db -->
                    @forelse($reservasi as $item)
                    @php
                        $no = ($reservasi->currentPage() - 1) * $reservasi->perPage() + $loop->iteration;
                    @endphp
                    <!-- simpan data ke elemen html -->
                    <tr class="hover:bg-gray-50/50 transition-colors reservasi-row"
                        data-month="{{ \Carbon\Carbon::parse($item->tgl_reservasi)->format('n') }}"                        
                        data-name="{{ strtolower($item->pasien->nama ?? '') }}" 
                        data-nohp="{{ $item->pasien->no_hp }}"
                        data-email="{{ $item->pasien->user->email ?? '' }}"
                        data-dokter="{{ strtolower($dokterTampil->nama ?? '') }}"
                        data-tanggal="{{ $item->tgl_reservasi }}"
                        data-jam="{{ (!empty($item->jam_reservasi) && $item->jam_reservasi !== 'undefined')
                            ? \Carbon\Carbon::parse($item->jam_reservasi)->format('H:i')
                            : '-' }}"                        
                        data-status="{{ $item->status ?? 'menunggu' }}"
                        data-id="{{ $item->id_reservasi }}">
                        <!-- no -->
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                            {{ $no }}
                        </td>
                        <!-- nama pasien -->
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $item->pasien->nama ?? 'Pasien Tidak Ditemukan' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5" data-nohp="{{ $item->pasien->no_hp }}">{{ $item->pasien->no_hp }}</p>
                        </td>
                        <!-- tanggal reservasi -->
                        <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                            {{ $item->tgl_reservasi 
                                ? \Carbon\Carbon::parse($item->tgl_reservasi)->translatedFormat('d M Y') 
                                : '-' 
                            }}
                        </td>
                        <!--jam reservasi  -->
                        <td class="px-5 py-4">
                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">
                                {{ $item->jam_reservasi 
                                    ? \Carbon\Carbon::parse($item->jam_reservasi)->format('H:i') 
                                    : '-' 
                                }}
                            </span>
                        </td>
                        <!-- dokter -->
                        <td class="px-5 py-4">
                            <!-- cek perubahan jadwal dokter -->
                            @php
                                $perubahanAktif = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
                                    ->where('dokter_id', $item->jadwal->id_dokter)
                                    ->whereDate('tanggal', $item->tgl_reservasi)
                                    ->where('status', 'aktif')
                                    ->first();

                                $dokterTampil = $perubahanAktif?->dokterPengganti 
                                    ?? $item->jadwal->dokter;
                            @endphp

                            <p class="text-sm font-bold text-gray-900">
                                {{ $dokterTampil->nama ?? 'Belum Ditentukan' }}
                            </p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">
                                {{ $dokterTampil->spesialisasi ?? '-' }}
                            </p>
                        </td>
                        <!-- status -->
                        <td class="px-5 py-4 text-center">
                            <span class="status-badge inline-block px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider
                                @if($item->status === 'selesai') bg-green-50 text-green-700 border-green-200
                                @elseif($item->status === 'dibatalkan') bg-red-50 text-red-700 border-red-200
                                @elseif($item->status === 'tidak_hadir') bg-orange-50 text-orange-700 border-orange-200
                                @elseif($item->status === 'terdampak') bg-red-100 text-red-800 border-red-300
                                @elseif($item->status === 'tindakan') bg-teal-50 text-teal-700 border-teal-200
                                @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                @if($item->status === 'terdampak')
                                    Reservasi Terdampak
                                @else
                                    {{ ucfirst($item->status ?? 'menunggu') }}
                                @endif
                            </span>
                        </td>
                        <!-- jika status menunggu bisa edit, hapus, dan konfirm kehadiran -->
                        <td class="px-5 py-4 text-center action-cell">
                            @if($item->status === 'menunggu')
                                <div class="flex items-stretch gap-2 h-9 justify-center">
                                    <a href="{{ route('admin.reservasi.edit', $item->id_reservasi) }}" 
                                       class="flex-1 h-full inline-flex items-center justify-center px-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-[10px] font-semibold transition-colors uppercase tracking-wider">
                                        <i class="fas fa-pen mr-1"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.reservasi.destroy', $item->id_reservasi) }}" method="POST" class="flex-1 h-full delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full h-full inline-flex items-center justify-center px-2 bg-white border border-red-200 hover:bg-red-50 text-red-700 rounded-sm text-[10px] font-semibold transition-colors uppercase tracking-wider delete-btn">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="kehadiran-btn flex-1 h-full inline-flex items-center justify-center px-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-[10px] font-bold uppercase tracking-wider transition-colors"
                                            data-id="{{ $item->id_reservasi }}"
                                            data-pasien="{{ addslashes($item->pasien->nama ?? 'Pasien Tidak Ditemukan') }}">
                                        <i class="fas fa-user-check mr-1"></i> Hadir
                                    </button>
                                </div>
                                <!-- jika status terdampak, konfirmasi terdampak -->
                            @elseif($item->status === 'terdampak')
                                @php
                                    $perubahan = \App\Models\PerubahanJadwalDokter::with('dokterPengganti')
                                        ->where('dokter_id', $item->jadwal->id_dokter)
                                        ->whereDate('tanggal', $item->tgl_reservasi)
                                        ->where('status', 'aktif')
                                        ->first();
                                @endphp
                                <button type="button"
                                    class="konfirmasi-terdampak-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-sm text-[10px] font-bold uppercase tracking-wider transition-colors"
                                    data-pasien="{{ $item->pasien->nama }}"
                                    data-nohp="{{ $item->pasien->no_hp ?? '-' }}"
                                    data-email-terdampak="{{ $item->email_terdampak ? '1' : '0' }}"
                                    data-email="{{ $item->pasien->user->email }}"
                                    data-dokter="{{ $item->jadwal->dokter->nama ?? '-' }}"
                                    data-dokter-pengganti="{{ $perubahan?->dokterPengganti?->nama ?? 'Tidak ada dokter pengganti' }}"
                                    data-has-pengganti="{{ $perubahan?->dokterPengganti ? '1' : '0' }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($item->tgl_reservasi)->translatedFormat('d M Y') }}"
                                    data-id="{{ $item->id_reservasi }}">
                                    <i class="fas fa-phone-alt"></i> Konfirmasi
                                </button>
                            @else
                            <!-- jika bukan menunggu, dan terdampak, lihatkan detail -->
                                <a href="{{ route('admin.reservasi.show', ['reservasi' => $item->id_reservasi]) }}" 
                                   class="inline-flex items-center justify-center gap-2 w-full h-9 px-4 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-[10px] font-bold uppercase tracking-wider transition-colors">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                    <!-- jika data kosong -->
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center bg-gray-50">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada data reservasi</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1">Reservasi yang masuk ke klinik ini akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (FIX: Struktur nested yang salah sebelumnya) -->
        <div class="border-t border-gray-200 bg-white">
            <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $reservasi->count() }}</span> dari 
                    <span class="font-bold text-gray-800">{{ $reservasi->total() }}</span> reservasi
                </p>
                {{ $reservasi->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

</div>
<!-- modal kehadiran -->
<div id="kehadiranModal" class="fixed inset-0 hidden z-[9999]" style="isolation: isolate;">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm[3px]" style="backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);"></div>
    <!-- Content layer -->
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div class="bg-white shadow-sm max-w-sm w-full mx-4 overflow-hidden rounded-sm border border-gray-200">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <!-- judul -->
                <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Konfirmasi Kehadiran</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-5">
                <!-- informasi nama -->
                <div class="bg-teal-50 rounded-sm p-3 mb-4 border border-teal-200">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Pasien</p>
                    <p id="pasienNameModal" class="text-sm font-bold text-gray-800"></p>
                </div>
                <!-- pilih salah satu, hadir jika pasien datang ke klinik, dan tidak hadir jika pasienn tidak datang -->
                <p class="text-[10px] text-gray-600 mb-4 font-bold uppercase tracking-wider">Pilih status kehadiran:</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="btnHadir" class="p-3 border border-teal-200 hover:border-teal-500 hover:bg-teal-50 rounded-sm transition-all group">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-check-circle text-2xl text-teal-600 mb-1"></i>
                            <p class="font-bold text-teal-700 text-[10px] uppercase tracking-wider">Hadir</p>
                        </div>
                    </button>
                    <button type="button" id="btnTidakHadir" class="p-3 border border-red-200 hover:border-red-500 hover:bg-red-50 rounded-sm transition-all group">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-times-circle text-2xl text-red-600 mb-1"></i>
                            <p class="font-bold text-red-700 text-[10px] uppercase tracking-wider">Tidak Hadir</p>
                        </div>
                    </button>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 flex justify-end border-t border-gray-200">
                <button id="btnBatalModal" class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-sm font-bold text-[10px] uppercase tracking-wider transition-all">Batal</button>
            </div>
        </div>
    </div>
</div>

<!-- modal terdampak -->
<div id="terdampakModal" class="fixed inset-0 hidden z-[9999]" style="isolation: isolate;">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm[3px]" style="backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);"></div>
    <!-- Content layer -->
    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <div class="bg-white shadow-sm max-w-sm w-full mx-4 overflow-hidden rounded-sm border border-gray-200">
            <div class="px-4 py-3 border-b border-red-200 bg-red-600">
                <div class="flex items-center justify-between">
                    <div>
                        <!-- judul -->
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Pasien</h3>
                        <p class="text-[10px] text-red-100 mt-1 uppercase tracking-wider">Reservasi terdampak perubahan jadwal dokter</p>
                    </div>
                    <button id="closeTerdampakModal" class="text-white hover:text-red-100"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="p-4">
                <div class="space-y-3">
                    <!-- informasi pasien. nama, no hp, doklter lama, dokter pengganti -->
                    <div class="bg-gray-50 rounded-sm p-3 border border-gray-200">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Nama Pasien</p>
                        <p id="modalPasienNama" class="font-bold text-sm text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-sm p-3 border border-gray-200">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">No. HP</p>
                        <p id="modalPasienNoHp" class="font-bold text-sm text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-sm p-3 border border-gray-200">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Dokter Lama</p>
                        <p id="modalDokterNama" class="font-bold text-sm text-gray-800"></p>
                    </div>
                    <div class="bg-gray-50 rounded-sm p-3 border border-gray-200">
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Dokter Pengganti</p>
                        <p id="modalDokterPengganti" class="font-bold text-sm text-gray-800"></p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-sm p-3">
                        <p class="text-[10px] text-red-700 leading-relaxed uppercase tracking-wider">Pasien ini terdampak perubahan jadwal dokter. Silakan hubungi pasien.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <!-- button kirim email -->
                    <button id="btnEmailPasien" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-sm font-bold text-[10px] uppercase tracking-wider transition-all">
                        <i class="fas fa-envelope mr-2"></i>Kirim Email
                    </button>
                </div>
                <!-- aksi, setelah mendapat konfirmasi pasien -->
                <div id="aksiSetelahEmail" class="hidden grid grid-cols-3 gap-2 mt-3">
                    <button id="btnLanjutDokter" class="px-2 py-2 text-[10px] bg-teal-600 hover:bg-teal-700 text-white rounded-sm font-bold uppercase tracking-wider transition-all">
                        <i class="fas fa-user-md mb-0.5 block text-sm"></i>Lanjut
                    </button>
                    <a href="#" id="btnReschedule"
                    class="inline-block px-2 py-2 text-[10px] bg-yellow-500 hover:bg-yellow-600 text-white rounded-sm font-bold uppercase tracking-wider transition-all text-center">
                        <i class="fas fa-calendar-alt mb-0.5 block text-sm"></i>Jadwal
                    </a>
                    <button id="btnBatalkan" class="px-2 py-2 text-[10px] bg-red-600 hover:bg-red-700 text-white rounded-sm font-bold uppercase tracking-wider transition-all">
                        <i class="fas fa-times-circle mb-0.5 block text-sm"></i>Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// custom dropdown
function initCS(id) {
    const wrap = document.getElementById(id + '_wrap');
    if (!wrap) return null;
    // ambil elemen html
    const btn = wrap.querySelector('.cs-btn'); //button
    const list = wrap.querySelector('.cs-list'); //list pilihan
    const input = document.getElementById(id); //inputan
    if (!btn || !list || !input) return null; //jika salah satu elemen tidak ada maka fungsi dibatalkan
    //fungsi  dijalankan ketika di klik, buka tutup dropdown
    btn.addEventListener('click', () => {
        if (btn.disabled) return;
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        list.classList.toggle('active');
        btn.classList.toggle('active');
    });

    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });
    // memilih item
    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => { //klik item yang dipilih
            input.value = item.dataset.val; //simpan value
            btn.querySelector('span').innerHTML = item.innerHTML;
            list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            list.classList.remove('active');
            btn.classList.remove('active');
            input.dispatchEvent(new Event('change'));
        });
    });

    if (input.value) {
        const selectedItem = list.querySelector(`.cs-item[data-val="${input.value}"]`);
        if (selectedItem) {
            btn.querySelector('span').innerHTML = selectedItem.innerHTML;
            selectedItem.classList.add('selected');
        }
    }
    return {
        disable: (val) => { btn.disabled = val; if(val) { list.classList.remove('active'); btn.classList.remove('active'); } },
        setPlaceholder: (text) => { if(!input.value) btn.querySelector('span').textContent = text; }
    };
}
// Inisialisasi Filter Dropdown
initCS('statusFilter');
initCS('monthFilter');

// jalankan kode setelah html selesai dibuat
document.addEventListener('DOMContentLoaded', function() {
    // mengambil csrf token, dipakai ketika js mengirim ajax
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    // menyimpan url 
    const kehadiranUrlTemplate = "{{ route('admin.reservasi.kehadiran', ['id_reservasi' => 'ID_PLACEHOLDER']) }}";
    const detailUrlTemplate    = "{{ route('admin.reservasi.show', ['reservasi' => 'ID_PLACEHOLDER']) }}";
    const sendEmailUrlTemplate = "{{ route('admin.reservasi.send-email', ['id' => 'ID_PLACEHOLDER']) }}";
    const updateStatusTemplate = "{{ route('admin.reservasi.update-status', ['id' => 'ID_PLACEHOLDER']) }}";
    const editUrlTemplate      = "{{ route('admin.reservasi.edit', ['reservasi' => 'ID_PLACEHOLDER']) }}";
    // menyimpan reservasi yang sedang diproses
    let currentReservasiId = null;
    let currentTerdampakId = null; //menyimpan pasien terdampak
    const processedIds = new Set(); //tombol hanya 1 kali klik

    // modal kehadira, ambil elemen html
    const kehadiranModal  = document.getElementById('kehadiranModal');
    const pasienNameModal = document.getElementById('pasienNameModal');
    const btnHadir        = document.getElementById('btnHadir');
    const btnTidakHadir   = document.getElementById('btnTidakHadir');
    const btnBatalModal   = document.getElementById('btnBatalModal');
    const closeModalBtn   = document.getElementById('closeModal');
    // function open modal
    function openKehadiranModal(id, name) {
        if (processedIds.has(id)) return;
        currentReservasiId = id; //menyimpan id reservasi
        pasienNameModal.textContent = name; //menampilkan nama pasien
        kehadiranModal.classList.remove('hidden'); //buka modal
        document.body.style.overflow = 'hidden'; // mengunci scroll halaman
    }
    //close modal
    function closeKehadiranModal() {
        kehadiranModal.classList.add('hidden'); //tutup modal
        currentReservasiId = null; //emghapus id
        document.body.style.overflow = ''; //mengembalikan scroll
    }
    //mengubah tampilan table tanpa reload
    function updateRowUI(id, status) {
        const row = document.querySelector(`tr[data-id="${id}"]`); //status berubah
        if (!row) return;
        row.dataset.status = status;
        const badge = row.querySelector('.status-badge'); //mengubah badge
        if (badge) {
            if (status === 'tindakan') {
                badge.className = 'status-badge inline-block px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider bg-teal-50 text-teal-700 border-teal-200';
                badge.innerHTML = 'Tindakan';
            } else if (status === 'dibatalkan') {
                badge.className = 'status-badge inline-block px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider bg-red-50 text-red-700 border-red-200';
                badge.innerHTML = 'Dibatalkan';
            }
        }
        //tidak perlu refresh
        const actionCell = row.querySelector('.action-cell');
        if (actionCell) {
            const detailUrl = detailUrlTemplate.replace('ID_PLACEHOLDER', id);
            actionCell.innerHTML = `<div class="flex justify-center"><a href="${detailUrl}" class="inline-flex items-center justify-center gap-2 w-full h-9 px-4 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-[10px] font-bold uppercase tracking-wider transition-colors"><i class="fas fa-eye"></i> Detail</a></div>`;
        }
    }
    //update status kehadiran
    function updateStatusViaAjax(id, status) {
        if (processedIds.has(id)) return; //cek apakah sudah diproses, jika sudah berhenti
        processedIds.add(id); //supaya tidak bisa di klik 2kali
        const url = kehadiranUrlTemplate.replace('ID_PLACEHOLDER', id);
        updateRowUI(id, status); //ubah tampilan
        closeKehadiranModal(); //tutup modal
        //kirim data ke laravel
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'status=' + encodeURIComponent(status)
        })
        //kalo berhasil muncul swetalert. kalo gagal muncul popup
        .then(res => {
            if (!res.ok) throw new Error('Gagal');
            return res.json().catch(() => ({}));
        })
        .then(() => {
            const isHadir = (status === 'tindakan');
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="
                            width:48px;
                            height:48px;
                            background:${isHadir ? '#f0fdfa' : '#fef2f2'};
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:0 auto 12px;
                            border-radius:2px;
                            border:1px solid ${isHadir ? '#ccfbf1' : '#fecaca'};
                        ">
                            <i class="fas ${isHadir ? 'fa-user-check' : 'fa-times-circle'}" style="font-size:20px; color:${isHadir ? '#0f766e' : '#dc2626'};"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            ${isHadir ? 'Pasien Hadir' : 'Reservasi Dibatalkan'}
                        </h2>
                        <p style="font-size:13px; color:#6b7280;">Status kehadiran berhasil diperbarui.</p>
                    </div>`,
                showConfirmButton: false,
                timer: 1000,
                customClass: { popup: 'rounded-sm p-5' }
            });
        })
        .catch(err => {
            console.error(err);
            processedIds.delete(id);
            Swal.fire({
                width: 340,
                html: `<div class="text-center"><div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;"><i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal</h2><p style="font-size:13px; color:#6b7280;">Terjadi kesalahan saat memperbarui status.</p></div>`,
                showConfirmButton: false,
                timer: 2000,
                customClass: { popup: 'rounded-sm p-5' }
            });
        });
    }
    //event tombl kehadiran
    document.querySelectorAll('.kehadiran-btn').forEach(btn => {
        //fungsi berjalan ketika tombol di klik
        btn.addEventListener('click', function() {
            openKehadiranModal(this.dataset.id, this.dataset.pasien || 'Pasien');
        });
    });
    //tombol hadir ketika ditekan, status pasien berubah menjadi tindakan
    btnHadir?.addEventListener('click', () => { if (currentReservasiId) updateStatusViaAjax(currentReservasiId, 'tindakan'); });
    // tombol tidak hadir ketiaka ditekan status pasienn menjadi dibatalkan
    btnTidakHadir?.addEventListener('click', () => { if (currentReservasiId) updateStatusViaAjax(currentReservasiId, 'dibatalkan'); });
    // close dan batal menutup popup
    btnBatalModal?.addEventListener('click', closeKehadiranModal);
    closeModalBtn?.addEventListener('click', closeKehadiranModal);
    kehadiranModal?.addEventListener('click', (e) => { if (e.target === kehadiranModal) closeKehadiranModal(); });

    // konfirmasi delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //jangan langsung hapus
            // ambil nama pasien
            const pasienName = this.closest('tr')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';
            // popup hapus
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="
                            width:48px;
                            height:48px;
                            background:#fee2e2;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:0 auto 12px;
                            border-radius:2px;
                            border:1px solid #fecaca;
                        ">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Hapus Reservasi
                        </h2>
                        <p style="font-size:13px; color:#6b7280;">
                            Yakin hapus <b>${pasienName}</b>?
                        </p>
                    </div>`,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-sm p-5',
                    confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
                    cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
                }
                // jika setuju hapus, baru hapus permanen
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });

    // search dan filter
    // ambil elemen html
    const showingCount = document.getElementById('showingCount');
    const searchInput  = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const monthFilter = document.getElementById('monthFilter');
    const tableBody    = document.getElementById('tableBody');
    const allRows      = tableBody ? Array.from(tableBody.querySelectorAll('.reservasi-row')) : [];
    // funtion filter
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim(); //keyword
        const statusVal  = statusFilter.value.toLowerCase(); //status
        const monthVal   = monthFilter.value; //bulan
        let visibleCount = 0;
        allRows.forEach(row => { //setiap baris ambil attribut data
            const name    = (row.dataset.name || '').toLowerCase();
            const dokter  = (row.dataset.dokter || '').toLowerCase();
            const tanggal = (row.dataset.tanggal || '').toLowerCase();
            const status  = (row.dataset.status || '').toLowerCase();
            // cek kecocokan
            const matchesSearch = !searchTerm || name.includes(searchTerm) || dokter.includes(searchTerm) || tanggal.includes(searchTerm);
            const matchesStatus = !statusVal || status === statusVal;
            const matchesMonth = !monthVal || row.dataset.month === monthVal;
            // jika cocok tampilkan, tidak cocok sembunyikan
            if (matchesSearch && matchesStatus && matchesMonth) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        showingCount.textContent = visibleCount;
    }
    // realtime
    searchInput?.addEventListener('input', filterTable);
    statusFilter?.addEventListener('change', filterTable);
    monthFilter?.addEventListener('change', filterTable);
    filterTable();

    // modal terdampak
    const terdampakModal       = document.getElementById('terdampakModal');
    const closeTerdampakModalBtn = document.getElementById('closeTerdampakModal');
    const modalPasienNama      = document.getElementById('modalPasienNama');
    const modalPasienNoHp      = document.getElementById('modalPasienNoHp');
    const modalDokterNama      = document.getElementById('modalDokterNama');
    const modalDokterPengganti = document.getElementById('modalDokterPengganti');
    const aksiSetelahEmail     = document.getElementById('aksiSetelahEmail');
    const btnEmailPasien       = document.getElementById('btnEmailPasien');
    const btnReschedule        = document.getElementById('btnReschedule');
    // fungsi open modal terdampak
    function openTerdampakModal(button) {
        currentTerdampakId = button.dataset.id; //menyimpan id
        // isi data nama, no hp, dokter asli, dokter penngganti
        modalPasienNama.textContent      = button.dataset.pasien;
        modalPasienNoHp.textContent      = button.dataset.nohp || '-';
        modalDokterNama.textContent      = button.dataset.dokter;
        modalDokterPengganti.textContent = button.dataset.dokterPengganti;
        // mengatur tombol reschedule
        if (btnReschedule) {
            btnReschedule.href = editUrlTemplate.replace('ID_PLACEHOLDER', currentTerdampakId);
        }
        // cek apakah ada dokter pengganti
        const hasPengganti = button.dataset.hasPengganti === '1';
        const btnLanjut = document.getElementById('btnLanjutDokter'); //kalo ada
        if (btnLanjut) { //kalo gaada
            btnLanjut.style.display = hasPengganti ? '' : 'none';
        }
        //cek email apakah sudah pernah terkirim
        const emailTerkirim = button.dataset.emailTerdampak === '1';
        // jika email terkirim
        if (emailTerkirim) {
            aksiSetelahEmail?.classList.remove('hidden');
            btnEmailPasien.disabled = true;
            btnEmailPasien.innerHTML = '<i class="fas fa-check mr-2"></i>Email Terkirim';
            btnEmailPasien.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btnEmailPasien.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');

        } else {
            //jika belum di kirim
            aksiSetelahEmail?.classList.add('hidden');
            btnEmailPasien.disabled = false;
            btnEmailPasien.innerHTML = '<i class="fas fa-envelope mr-2"></i>Kirim Email';
            btnEmailPasien.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            btnEmailPasien.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
        terdampakModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    // tutup modal terdampak
    function closeTerdampakModalFn() {
        terdampakModal.classList.add('hidden');
        currentTerdampakId = null;
        document.body.style.overflow = '';
    }
    closeTerdampakModalBtn?.addEventListener('click', closeTerdampakModalFn);
    terdampakModal?.addEventListener('click', (e) => { if (e.target === terdampakModal) closeTerdampakModalFn(); });

    // tombol kirim email
    btnEmailPasien?.addEventListener('click', function () {
        if (!currentTerdampakId) return;

        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        this.disabled = true;
        // mengirim request ke laravel
        const url = sendEmailUrlTemplate.replace('ID_PLACEHOLDER', currentTerdampakId);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // popup berhasi atau tidak terkirimnya email
                Swal.fire({
                    width: 340,
                    html: `<div class="text-center"><div style="width:48px;height:48px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #ccfbf1;"><i class="fas fa-envelope" style="font-size:20px; color:#0f766e;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Email Terkirim</h2><p style="font-size:13px; color:#6b7280;">${data.message}</p></div>`,
                    showConfirmButton: false,
                    timer: 1000,
                    customClass: { popup: 'rounded-sm p-5' }
                });
                aksiSetelahEmail?.classList.remove('hidden');
                this.innerHTML = '<i class="fas fa-check mr-2"></i>Terkirim';
            } else {
                throw new Error(data.message || 'Gagal kirim email');
            }
        })
        .catch(err => {
            Swal.fire({
                width: 340,
                html: `<div class="text-center"><div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;"><i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal</h2><p style="font-size:13px; color:#6b7280;">${err.message || 'Gagal kirim email'}</p></div>`,
                customClass: { popup: 'rounded-sm p-5' }
            });
            this.disabled = false;
            this.innerHTML = originalText;
        });
    });

    //lanjut dokter pengganti, fngsi berjalan ketika button di klik
    document.getElementById('btnLanjutDokter')?.addEventListener('click', function () {
        if (!currentTerdampakId) return;

        const originalHTML = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mb-0.5 block text-sm"></i>Proses...';
        // mengirim request ke laravel
        const url = `/admin/reservasi/${currentTerdampakId}/lanjut-dokter-pengganti`;
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        // popup jika berhasil dan gagal
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    width: 340,
                    html: `<div class="text-center"><div style="width:48px;height:48px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #ccfbf1;"><i class="fas fa-user-md" style="font-size:20px; color:#0f766e;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Berhasil</h2><p style="font-size:13px; color:#6b7280;">${data.message}</p></div>`,
                    showConfirmButton: false,
                    timer: 1200,
                    customClass: { popup: 'rounded-sm p-5' }
                });
                setTimeout(() => location.reload(), 1100);
            } else {
                throw new Error(data.message || 'Gagal');
            }
        })
        .catch(err => {
            this.disabled = false;
            this.innerHTML = originalHTML;
            Swal.fire({
                width: 340,
                html: `<div class="text-center"><div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;"><i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal</h2><p style="font-size:13px; color:#6b7280;">${err.message || 'Terjadi kesalahan'}</p></div>`,
                customClass: { popup: 'rounded-sm p-5' }
            });
        });
    });

    //tombol batalkan, fungsi berjalan jika butto di klik
    document.getElementById('btnBatalkan')?.addEventListener('click', function () {
        if (!currentTerdampakId) { //cek id
            Swal.fire({ icon: 'error', title: 'ID tidak ditemukan' });
            return;
        }

        const originalHTML = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mb-0.5 block text-sm"></i>Proses...';
        //kirim ewquest ke laravel
        const url = updateStatusTemplate.replace('ID_PLACEHOLDER', currentTerdampakId);
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: 'dibatalkan',
                is_terdampak: true
            })
        })
        .then(async res => {
            if (!res.ok) {
                const err = await res.text();
                throw new Error(err);
            }
            return res.json().catch(() => ({}));
        })
        .then(() => {
            closeTerdampakModalFn();
            Swal.fire({
                width: 340,
                html: `<div class="text-center"><div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;"><i class="fas fa-times-circle" style="font-size:20px; color:#dc2626;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Reservasi Dibatalkan</h2><p style="font-size:13px; color:#6b7280;">Status pasien berhasil diperbarui.</p></div>`,
                showConfirmButton: false,
                timer: 1000,
                customClass: { popup: 'rounded-sm p-5' }
            });
            setTimeout(() => location.reload(), 1100);
        })
        .catch(err => {
            console.error(err);
            this.disabled = false;
            this.innerHTML = originalHTML;
            Swal.fire({
                width: 340,
                html: `<div class="text-center"><div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;"><i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i></div><h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal</h2><p style="font-size:13px; color:#6b7280;">Tidak bisa membatalkan reservasi</p></div>`,
                customClass: { popup: 'rounded-sm p-5' }
            });
        });
    });

    // jika button di klik open terdampak modal
    document.querySelectorAll('.konfirmasi-terdampak-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openTerdampakModal(this);
        });
    });
});
</script>
@endsection