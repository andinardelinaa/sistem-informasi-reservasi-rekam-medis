@extends('layouts.admin')
@section('header-title', 'Jadwal Dokter')
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

    <!--card statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Template Aktif</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $jadwalAktif->whereNull('tanggal')->count() }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->translatedFormat('F Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-week text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Bulan Depan</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $draftBulanDepan->whereNull('tanggal')->count() }}
                        <span class="text-xs font-normal text-gray-500 uppercase tracking-wider">template</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->addMonthNoOverflow()->translatedFormat('F Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-file-alt text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Riwayat</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $riwayat->count() }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-history text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- generate card -->
    @if(!$sudahAdaDraft)
    <div class="bg-white border border-gray-200 rounded-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Buat Template Bulan Depan</h3>
                <p class="text-xs text-gray-500 mt-1">
                    Belum ada template untuk {{ now()->addMonthNoOverflow()->translatedFormat('F Y') }}. Input template mingguan baru.
                </p>
            </div>
            <a href="{{ route('admin.jadwal.create') }}?draft=1" 
               class="inline-flex items-center justify-center px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                <i class="fas fa-plus mr-2 text-[10px]"></i> Input Template {{ now()->addMonthNoOverflow()->translatedFormat('F Y') }}
            </a>
        </div>
    </div>
    @else
    <div class="bg-white border border-gray-200 rounded-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                    <i class="fas fa-check-circle mr-2 text-teal-700 text-xs"></i>
                    Jadwal {{ now()->addMonthNoOverflow()->translatedFormat('F Y') }} Siap
                </h3>
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">
                    Template sudah dibuat.
                    @if(!$sudahDigenerate)
                    <span class="text-orange-600 font-bold">Klik tombol untuk membuat jadwal harian.</span>
                    @else
                    <span class="text-green-700 font-bold">{{ $draftBulanDepan->whereNotNull('tanggal')->count() }} jadwal harian sudah tergenerate.</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.jadwal.generate') }}" method="POST" class="inline" id="formGenerateJadwal">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulanDepan }}">
                    <input type="hidden" name="tahun" value="{{ $tahunDepan }}">
                    <button type="submit" 
                            id="btnGenerate"
                            data-update="{{ $sudahDigenerate ? '1' : '0' }}"
                            class="inline-flex items-center px-4 py-2 {{ $sudahDigenerate ? 'bg-slate-700 hover:bg-slate-800' : 'bg-teal-700 hover:bg-teal-800' }} text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                        <i class="fas fa-sync-alt mr-2 text-[10px]"></i> 
                        {{ $sudahDigenerate ? 'Perbarui Jadwal' : 'Buat Jadwal Harian' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Container -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col gap-4">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Jadwal Dokter</h2>
                
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <div class="relative flex-grow sm:flex-grow-0"> 
                            <!-- search -->
                            <input type="text" id="searchInput" placeholder="Cari nama dokter atau klinik..." 
                                class="w-full sm:w-56 pl-8 pr-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        <!-- Filter Hari -->
                        <div class="cs-wrap w-full sm:w-48" id="hariFilter_wrap">
                            <button type="button" class="cs-btn">
                                <span>Semua Hari</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                            <!-- list hari -->
                            <div class="cs-list">
                                <div class="cs-item selected" data-val="">Semua Hari</div>
                                <div class="cs-item" data-val="senin">Senin</div>
                                <div class="cs-item" data-val="selasa">Selasa</div>
                                <div class="cs-item" data-val="rabu">Rabu</div>
                                <div class="cs-item" data-val="kamis">Kamis</div>
                                <div class="cs-item" data-val="jumat">Jumat</div>
                                <div class="cs-item" data-val="sabtu">Sabtu</div>
                                <div class="cs-item" data-val="minggu">Minggu</div>
                            </div>
                            <input type="hidden" id="hariFilter" name="hariFilter" value="">
                        </div>
                    </div>
                    <!-- button tambah -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.jadwal.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                            <i class="fas fa-plus mr-2 text-[10px]"></i> Tambah Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- 3 Tab jadwal -->
        <div class="flex border-b border-gray-200 bg-gray-50">
            <!-- tab 1, tab aktif -->
            <button onclick="showTab(1)" id="tab1"
                    class="tab-button flex-1 py-3 text-center font-bold text-teal-700 border-b-2 border-teal-700 text-xs uppercase tracking-wider transition-colors">
                Aktif {{ now()->translatedFormat('F Y') }}
            </button>
            <!-- tab 2, darft untuk bulan depan -->
            <button onclick="showTab(2)" id="tab2"
                    class="tab-button flex-1 py-3 text-center font-bold text-gray-500 hover:text-gray-700 text-xs uppercase tracking-wider transition-colors">
                Draft {{ now()->addMonthNoOverflow()->translatedFormat('F Y') }}
            </button>
            <!-- tab 3, riwayat sudah dihapus -->
            <button onclick="showTab(3)" id="tab3"
                    class="tab-button flex-1 py-3 text-center font-bold text-gray-500 hover:text-gray-700 text-xs uppercase tracking-wider transition-colors">
                Riwayat
            </button>
        </div>

        <!-- TAB 1 AKTIF BULAN INI -->
        <div id="tabContent1" class="tab-content p-6">
            <!-- ambil semua jadwal yg kolom tglnya osong, loop semua jadwal -->
            @php
                $templateAktif = $jadwalAktif->whereNull('tanggal');
                $groupedAktif = [];
                foreach($templateAktif as $item) {
                    $klinikId = $item->id_klinik ?? 0;
                    $hari = strtolower($item->hari ?? 'unknown');
                    if (!isset($groupedAktif[$klinikId])) {
                        $groupedAktif[$klinikId] = [
                            'nama_klinik' => $item->klinik->nama_klinik ?? 'Klinik Tidak Diketahui',
                            'hari' => []
                        ];
                    }
                    if (!isset($groupedAktif[$klinikId]['hari'][$hari])) {
                        $groupedAktif[$klinikId]['hari'][$hari] = [];
                    }
                    $groupedAktif[$klinikId]['hari'][$hari][] = $item;
                }
                $hariOrder = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="gridAktif">
                <!--menampilka card klinik  -->
            @forelse($groupedAktif as $klinikId => $klinikData)
                <div class="bg-white border border-gray-200 rounded-sm overflow-hidden jadwal-card" data-klinik="{{ strtolower($klinikData['nama_klinik']) }}">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">
                            {{ $klinikData['nama_klinik'] }}
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- loop hari, jika hari memiliki jadwal tampilkan, jika tidak lewat-->
                        @foreach($hariOrder as $hari)
                            @if(isset($klinikData['hari'][$hari]) && count($klinikData['hari'][$hari]) > 0)
                            <div class="hari-group" data-hari="{{ $hari }}">
                                <h4 class="font-bold text-gray-500 mb-3 text-[11px] uppercase tracking-widest border-b border-gray-100 pb-2">{{ ucfirst($hari) }}</h4>
                                <div class="space-y-2">
                                    <!-- tampil nama hari -->
                                    @foreach($klinikData['hari'][$hari] as $item)
                                    <div class="jadwal-item bg-white hover:bg-gray-50 border border-gray-200 rounded-sm p-3 transition-colors"
                                         data-dokter="{{ strtolower($item->dokter->nama ?? '') }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <!-- loop dokter -->
                                                <p class="font-bold text-sm text-gray-900">{{ $item->dokter->nama ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 mt-1 font-medium">
                                                <!-- jam praktik -->
                                                    {{ date('H:i', strtotime($item->jam_mulai)) }} - {{ date('H:i', strtotime($item->jam_selesai)) }} WIB
                                                </p>
                                            </div>
                                            <!-- edit -->
                                            <div class="flex gap-2 ml-4">
                                                <a href="{{ route('admin.jadwal.edit', $item->id_jadwal) }}" 
                                                   class="w-8 h-8 flex items-center justify-center border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm transition-colors" title="Edit">
                                                    <i class="fas fa-edit text-xs"></i>
                                                </a>
                                                <!-- hapus -->
                                                <form action="{{ route('admin.jadwal.destroy', $item->id_jadwal) }}" method="POST" class="inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="delete-btn w-8 h-8 flex items-center justify-center border border-red-200 hover:bg-red-50 text-red-700 rounded-sm transition-colors" title="Hapus">
                                                        <i class="fas fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- jika tidak ada jadwal -->
                @empty
                <div class="col-span-full py-16 text-center text-gray-500 empty-state bg-gray-50 border border-dashed border-gray-200 rounded-sm">
                    <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada template untuk bulan ini</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 2: BULAN DEPAN -->
        <div id="tabContent2" class="tab-content hidden p-6">
            <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-sm">
                <p class="text-xs text-gray-700 uppercase tracking-wider">
                    <i class="fas fa-info-circle mr-2 text-teal-700"></i>
                    <!-- kotak informasi -->
                    <strong>Auto-Generate Aktif:</strong> Pasien sudah bisa melihat dan reservasi jadwal untuk bulan ini kapan saja. 
                    Status formal "Aktif" akan otomatis diterapkan tanggal 1.
                </p>
            </div>
            <!-- mengecek apakah ada template bulan depan -->
            @if($draftBulanDepan->whereNull('tanggal')->count() > 0)
            <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <!-- judul tabel -->
                    <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Template Mingguan {{ now()->addMonthNoOverflow()->translatedFormat('F Y') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-white border-b border-gray-200">
                            <tr> 
                                <!-- header -->
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dokter</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Klinik</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hari</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jam</th>
                                <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- loop semua jadwal -->
                            @foreach($draftBulanDepan->whereNull('tanggal') as $item)
                            <!-- ambil data attribut -->
                            <tr class="hover:bg-gray-50/50 transition-colors" 
                                data-dokter="{{ strtolower($item->dokter->nama ?? '') }}" 
                                data-klinik="{{ strtolower($item->klinik->nama_klinik ?? '') }}" 
                                data-hari="{{ strtolower($item->hari ?? '') }}">
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-bold text-gray-900">{{ $item->dokter->nama ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600 uppercase tracking-wider">{{ $item->klinik->nama_klinik ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600 uppercase tracking-wider font-bold">{{ ucfirst($item->hari) }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-800 font-medium">
                                    {{ date('H:i', strtotime($item->jam_mulai)) }} - {{ date('H:i', strtotime($item->jam_selesai)) }}
                                </td>
                                <!-- edit -->
                                <td class="px-3 py-2">
                                    <div class="flex items-stretch gap-2 h-9">

                                        <a href="{{ route('admin.jadwal.edit', $item->id_jadwal) }}"
                                        class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                            <i class="fas fa-pen text-[10px] mr-1"></i>
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.jadwal.destroy', $item->id_jadwal) }}"
                                            method="POST"
                                            class="flex-1 h-full delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="w-full h-full inline-flex items-center justify-center px-3 bg-white border border-red-200 hover:bg-red-50 text-red-700 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider delete-btn">
                                                <i class="fas fa-trash text-[10px] mr-1"></i>
                                                Hapus
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else 
            <!-- jika belum ada template bulan depan -->
            <div class="text-center py-12 bg-gray-50 border border-dashed border-gray-200 rounded-sm">
                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file-alt text-gray-300 text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">Belum ada template untuk bulan depan</p>
                <a href="{{ route('admin.jadwal.create') }}?draft=1" 
                   class="inline-flex items-center px-4 py-2 bg-teal-700 text-white rounded-sm hover:bg-teal-800 text-xs font-bold uppercase tracking-wider transition-colors">
                    <i class="fas fa-plus mr-2 text-[10px]"></i> Buat Template
                </a>
            </div>
            @endif
        </div>

        <!-- TAB 3: RIWAYAT -->
        <div id="tabContent3" class="tab-content hidden p-6">
            <!-- ambil template riwayat, loop semua riwayat -->
            @php
                $templateRiwayat = $riwayat->whereNull('tanggal');
                $groupedRiwayat = [];
                foreach($templateRiwayat as $item) {
                    $klinikId = $item->id_klinik ?? 0;
                    $hari = strtolower($item->hari ?? 'unknown');
                    if(!isset($groupedRiwayat[$klinikId])) {
                        $groupedRiwayat[$klinikId] = [
                            'nama_klinik' => $item->klinik->nama_klinik ?? 'Klinik Tidak Diketahui',
                            'hari' => []
                        ];
                    }
                    if(!isset($groupedRiwayat[$klinikId]['hari'][$hari])) {
                        $groupedRiwayat[$klinikId]['hari'][$hari] = [];
                    }
                    $groupedRiwayat[$klinikId]['hari'][$hari][] = $item;
                }
            @endphp
            <!-- menampilkan card klinik -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" id="gridRiwayat">
                @forelse($groupedRiwayat as $klinikId => $klinikData)
                <div class="bg-white border border-gray-200 rounded-sm overflow-hidden jadwal-card" data-klinik="{{ strtolower($klinikData['nama_klinik']) }}">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <!-- nama klinik -->
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">
                            {{ $klinikData['nama_klinik'] }}
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">
                        <!-- loop hari, jika hari memiliki jadwal, tampilkan jika tidak lewati -->
                        @foreach($hariOrder as $hari)
                            @if(isset($klinikData['hari'][$hari]) && count($klinikData['hari'][$hari]) > 0)
                            <div class="hari-group" data-hari="{{ $hari }}">
                                <h4 class="font-bold text-gray-500 mb-3 text-[11px] uppercase tracking-widest border-b border-gray-100 pb-2">{{ ucfirst($hari) }}</h4>
                                <div class="space-y-2">
                                    <!-- loop jadwal -->
                                    @foreach($klinikData['hari'][$hari] as $item)
                                    <div class="jadwal-item bg-white border border-gray-200 rounded-sm p-3"
                                         data-dokter="{{ strtolower($item->dokter->nama ?? '') }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <!-- nama dokter, jam praktik -->
                                                <p class="font-bold text-sm text-gray-900">{{ $item->dokter->nama ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 mt-1 font-medium">
                                                    {{ date('H:i', strtotime($item->jam_mulai)) }} - {{ date('H:i', strtotime($item->jam_selesai)) }} WIB
                                                </p>
                                                <!-- waktu penghapusan -->
                                                <p class="text-[10px] text-gray-400 mt-2 uppercase tracking-wider">
                                                    Dihapus: {{ $item->deleted_at->format('d M Y H:i') }}
                                                </p>
                                            </div>
                                            <!-- tombol restore -->
                                            <button onclick="restoreJadwal({{ $item->id_jadwal }})" 
                                                    class="w-8 h-8 flex items-center justify-center border border-teal-200 hover:bg-teal-50 text-teal-700 rounded-sm transition-colors" title="Pulihkan">
                                                <i class="fas fa-undo text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center text-gray-500 empty-state bg-gray-50 border border-dashed border-gray-200 rounded-sm">
                    <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada riwayat</p>
                </div>
                @endforelse
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
    const btn = wrap.querySelector('.cs-btn'); //button 
    const list = wrap.querySelector('.cs-list'); //list pilihan
    const input = document.getElementById(id);
    if (!btn || !list || !input) return null;
    //jalankan fungsi ketika tombol di klik
    btn.addEventListener('click', () => {
        if (btn.disabled) return;
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        list.classList.toggle('active'); //buka tutup dropdown
        btn.classList.toggle('active');
    });

    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });
    //memilih item
    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => {
            input.value = item.dataset.val; //menyimpan value
            btn.querySelector('span').innerHTML = item.innerHTML; //mengubah tulisan tombol
            list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            list.classList.remove('active'); //menutup dropdown
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
initCS('hariFilter');

// notifikasi
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});
// mengatur perpindahak  tab
function showTab(tab) {
    // sembunyikan semua tab
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById(`tabContent${tab}`).classList.remove('hidden');

    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('text-teal-700', 'text-blue-700', 'border-b-2', 'border-teal-700', 'border-blue-700');
        btn.classList.add('text-gray-500');
    });
    //menampilkan tab yg dipilih
    const active = document.getElementById(`tab${tab}`);
    if (tab === 2) {
        active.classList.add('text-blue-700', 'border-b-2', 'border-blue-700');
    } else {
        active.classList.add('text-teal-700', 'border-b-2', 'border-teal-700');
    }
    // search dan filter
    document.getElementById('searchInput').value = '';
    document.getElementById('hariFilter').value = '';
    
    const csBtn = document.querySelector('#hariFilter_wrap .cs-btn span');
    const csItems = document.querySelectorAll('#hariFilter_wrap .cs-item');
    if (csBtn) csBtn.innerHTML = 'Semua Hari';
    csItems.forEach(i => i.classList.remove('selected'));
    const defaultItem = document.querySelector('#hariFilter_wrap .cs-item[data-val=""]');
    if (defaultItem) defaultItem.classList.add('selected');

    filterJadwal();
}
// funtion filter
function filterJadwal() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase(); //ambil keyword
    const hariFilter = document.getElementById('hariFilter').value.toLowerCase(); //ambil filter hari
    //ambil tab aktif
    const activeTab = document.querySelector('.tab-content:not(.hidden)');
    if (!activeTab) return;
    
    const grid = activeTab.querySelector('[id^="grid"]');
    // tab 1 dan 3
    if (grid) {
        const cards = grid.querySelectorAll('.jadwal-card');
        let visibleCards = 0;
        // loop card
        cards.forEach(card => {
            const klinikName = card.dataset.klinik || '';
            const hariGroups = card.querySelectorAll('.hari-group');
            let visibleHariGroups = 0;
            // loop hari
            hariGroups.forEach(group => {
                const hari = group.dataset.hari || '';
                const items = group.querySelectorAll('.jadwal-item');
                let groupHasMatch = false;
                // loop jadwal
                items.forEach(item => {
                    const dokter = item.dataset.dokter || '';
                    // cek kecocokan 
                    const matchesSearch = !searchTerm || dokter.includes(searchTerm) || klinikName.includes(searchTerm);
                    const matchesHari = !hariFilter || hari === hariFilter;
                    // jika sesuai tampilkan jika tidak sembunyikan
                    if (matchesSearch && matchesHari) {
                        item.style.display = '';
                        groupHasMatch = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                if (groupHasMatch) {
                    group.style.display = '';
                    visibleHariGroups++;
                } else {
                    group.style.display = 'none';
                }
            });
            
            if (visibleHariGroups > 0) {
                card.style.display = '';
                visibleCards++;
            } else {
                card.style.display = 'none';
            }
        });
        
        const emptyStates = activeTab.querySelectorAll('.empty-state');
        emptyStates.forEach(es => {
            es.style.display = visibleCards === 0 && !searchTerm && !hariFilter ? '' : 'none';
        });

    } 
    // tab2
    else {
        const tableBody = activeTab.querySelector('tbody');
        if (!tableBody) return;
        const rows = tableBody.querySelectorAll('tr');
        let visibleRows = 0;
         // loop
        rows.forEach(row => {
            const dokter = row.dataset.dokter || '';
            const klinik = row.dataset.klinik || '';
            const hari = row.dataset.hari || '';
            // cek kecocokan tampilkan kalo tidak semnyunyikan
            const matchesSearch = !searchTerm || dokter.includes(searchTerm) || klinik.includes(searchTerm);
            const matchesHari = !hariFilter || hari === hariFilter;
            
            if (matchesSearch && matchesHari) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });
    }
}
// realtime
document.getElementById('searchInput').addEventListener('input', filterJadwal);
document.getElementById('hariFilter').addEventListener('change', filterJadwal);
// delete confirm
document.querySelectorAll('.delete-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); //jangan langsung hapus
        // ambil nama dokter
        const dokterName = this.closest('tr, .jadwal-item')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';
        //popup
        Swal.fire({
            width: 340,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                        <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                        Hapus Jadwal
                    </h2>
                    <p style="font-size:13px; color:#6b7280;">
                        Yakin hapus <b>${dokterName}</b>?<br><span style="color:#ef4444;">Akan dipindahkan ke Riwayat</span>
                    </p>
                </div>
            `,
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
                cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
            }
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
// popup restore jadwal
function restoreJadwal(id) {
    Swal.fire({
        width: 340,
        html: `
            <div class="text-center">
                <div style="width:48px;height:48px;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #a7f3d0;">
                    <i class="fas fa-undo" style="font-size:20px; color:#059669;"></i>
                </div>
                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                    Pulihkan Jadwal
                </h2>
                <p style="font-size:13px; color:#6b7280;">
                    Yakin ingin memulihkan jadwal ini?<br><span style="color:#10b981;">Data akan aktif kembali</span>
                </p>
            </div>
        `,
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: 'Pulihkan',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-green-700 hover:bg-green-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
            cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
        }
    }).then(result => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/jadwal/${id}/restore`;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
// saat halaman dibuka langsung ke tab 1
document.addEventListener('DOMContentLoaded', () => {
    showTab(1);
    filterJadwal();
});
// notif warning
@if(session('warning'))
Toast.fire({
    icon: 'warning',
    title: @json(session('warning'))
});
@endif
</script>
@endsection