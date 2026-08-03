@extends('layouts.admin')
@section('header-title', 'Tambah Template Jadwal')

@section('content')
<style>
    /* wrapper buat dropdown biar z-index-nya keatas pas lagi kebuka */
    .cs-wrap { position: relative; z-index: 1; }
    .cs-wrap:has(.cs-list.active) { z-index: 99999; }

    /* tampilan tombol dropdown */
    .cs-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: #f9fafb; border: 1px solid #d1d5db; border-radius: 2px; font-size: 0.875rem; text-align: left; cursor: pointer; transition: all 0.15s; color: #1f2937; user-select: none; }
    .cs-btn:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
    .cs-btn:not(:disabled):hover { border-color: #9ca3af; background: #fff; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
    .cs-btn.active { border-color: #0f766e; box-shadow: 0 0 0 1px #0f766e; background: #fff; }
    .cs-btn.active i { transform: rotate(180deg); }

    /* tampilan list dropdown pas muncul */
    .cs-list { position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 9999; max-height: 250px; overflow-y: auto; opacity: 0; visibility: hidden; transform: translateY(-5px); transition: all 0.15s ease; pointer-events: none; }
    .cs-list.active { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }

    /* kolom pencarian di dalam dropdown */
    .cs-search { width: 100%; padding: 8px 12px; border: none; border-bottom: 1px solid #e5e7eb; font-size: 0.8rem; outline: none; position: sticky; top: 0; background: #fff; z-index: 10; }
    .cs-search:focus { background: #f9fafb; }

    /* tiap item pilihan di dropdown */
    .cs-item { padding: 0.5rem 0.75rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; transition: background 0.1s; color: #374151; }
    .cs-item:hover { background: #f0fdfa; color: #0f766e; }
    .cs-item.selected { background: #f0fdfa; color: #0f766e; font-weight: 600; }

    /* ikon jam di input time biar warnanya nyambung */
    input[type="time"]::-webkit-calendar-picker-indicator { cursor: pointer; filter: invert(20%) sepia(89%) saturate(500%) hue-rotate(155deg) brightness(90%) contrast(90%); transition: all 0.3s ease; }
    input[type="time"]::-webkit-calendar-picker-indicator:hover { transform: scale(1.15); filter: invert(25%) sepia(89%) saturate(700%) hue-rotate(155deg) brightness(80%) contrast(90%); }

    /* warna bantu tema teal */
    .text-custom-teal { color: #0f766e; }
    .border-custom-teal { border-color: #0f766e; }

    /* biar font sweetalert2 sama */
    .swal2-popup {font-family: 'Inter', sans-serif !important;font-size: 13px;line-height: 1.6;letter-spacing: 0.2px;}
    .swal2-html-container {font-family: 'Inter', sans-serif !important;font-size: 13px;line-height: 1.6;}

    /* tampilan error inline di bawah field */
    .field-error { color: #dc2626; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; margin-top: 6px; display: none; }
    .field-error.show { display: block; }
    .input-error { border-color: #dc2626 !important; background-color: #fef2f2 !important; }
    .input-error:focus { border-color: #dc2626 !important; box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important; }
    .card-error { border-color: #dc2626 !important; }
</style>

<div class="max-w-7xl mx-auto space-y-6">
    <!-- breadcrumb navigasi -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center"><a href="{{ route('admin.jadwal.index') }}" class="hover:text-teal-700 transition-colors font-bold">Jadwal</a></li>
            <li><div class="flex items-center"><span class="text-gray-300 mx-2">/</span><span class="text-gray-400 font-medium">Tambah Baru</span></div></li>
        </ol>
    </nav>

    <!-- card utama -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- header card: judul + tombol kembali -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Tambah Template Jadwal</h1>
                <p class="text-xs text-gray-500 mt-1">Atur template jadwal mingguan dokter di klinik</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center"><i class="fas fa-arrow-left mr-2 text-[10px]"></i> Kembali</a>
        </div>

        <!-- form utama -->
        <form action="{{ route('admin.jadwal.store') }}" method="POST" id="jadwalForm" autocomplete="off" novalidate>
            @csrf
            <!-- hidden: nyimpen mode input & status draft -->
            <input type="hidden" name="input_mode" id="input_mode" value="single">
            <input type="hidden" name="is_draft" id="is_draft_hidden" value="{{ $isDraft ? '1' : '0' }}">

            <div class="p-6 lg:p-8 space-y-6">

                <!-- pilih target bulan: bulan ini atau bulan depan -->
                <div class="border border-gray-200 rounded-sm">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Target Bulan</h3>
                    </div>
                    <div class="p-5 bg-white">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <label class="flex items-center cursor-pointer p-3 rounded-sm border-2 {{ !$isDraft ? 'border-teal-700 bg-teal-50' : 'border-gray-200 hover:border-teal-300' }} transition-all" id="label_bulan_ini">
                                <input type="radio" name="target_bulan" value="0" {{ !$isDraft ? 'checked' : '' }} class="h-4 w-4 text-teal-600 focus:ring-teal-500" onchange="updateDraftStatus(0)">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-gray-900 uppercase tracking-wider">Bulan Ini</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ now()->translatedFormat('F Y') }} - Aktif Langsung</span>
                                </div>
                            </label>
                            <label class="flex items-center cursor-pointer p-3 rounded-sm border-2 {{ $isDraft ? 'border-blue-700 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }} transition-all" id="label_bulan_depan">
                                <input type="radio" name="target_bulan" value="1" {{ $isDraft ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500" onchange="updateDraftStatus(1)">
                                <div class="ml-3">
                                    <span class="block text-xs font-bold text-gray-900 uppercase tracking-wider">Bulan Depan</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider">{{ now()->addMonth()->translatedFormat('F Y') }} - Simpan Draft</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- baris 3 kolom: pilih dokter, mode input, info -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 overflow-visible">                   
                    <!-- kolom kiri: dropdown pilih dokter -->
                    <div class="border border-gray-200 rounded-sm overflow-visible">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dokter</h3>
                        </div>
                        <div class="p-5 bg-white" style="overflow: visible;">
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Dokter <span class="text-red-500">*</span></label>
                            <div class="cs-wrap" id="id_dokter_wrap">
                                <button type="button" class="cs-btn" id="dokterBtn"><span class="text-gray-400">-- Pilih Dokter --</span><i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i></button>
                                <div class="cs-list">
                                    <input type="text" class="cs-search" placeholder="Cari nama dokter...">
                                    @foreach($dokter as $d)
                                    <div class="cs-item" data-val="{{ $d->id_dokter }}" data-search="{{ strtolower($d->nama) }}">{{ $d->nama }}</div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="id_dokter" id="id_dokter">
                            </div>
                            <p class="field-error" id="dokterError"><i class="fas fa-exclamation-circle mr-1"></i>Silakan pilih dokter terlebih dahulu.</p>
                        </div>
                    </div>

                    <!-- kolom tengah: pilih mode input (satu hari / seminggu) -->
                    <div class="border border-gray-200 rounded-sm">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Mode Input</h3>
                        </div>
                        <div class="p-5 bg-white space-y-2">
                            <label class="flex items-center cursor-pointer p-2 rounded-sm hover:bg-gray-50 transition-all" onclick="switchMode('single')">
                                <input type="radio" name="mode_selector" value="single" checked class="h-4 w-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                <span class="ml-2 text-xs text-gray-800 font-bold uppercase tracking-wider">Input Satu Hari</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-2 rounded-sm hover:bg-gray-50 transition-all" onclick="switchMode('weekly')">
                                <input type="radio" name="mode_selector" value="weekly" class="h-4 w-4 text-teal-600 border-gray-300 focus:ring-teal-500">
                                <span class="ml-2 text-xs text-gray-800 font-bold uppercase tracking-wider">Input Seminggu</span>
                            </label>
                        </div>
                    </div>

                    <!-- kolom kanan: info tambahan sesuai mode yang dipilih -->
                    <div class="border border-gray-200 rounded-sm">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Informasi</h3>
                        </div>
                        <div class="p-5 bg-white">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-info-circle text-gray-400 mt-0.5 text-sm"></i>
                                <div class="text-xs text-gray-600 space-y-1">
                                    <p id="infoText" class="font-bold uppercase tracking-wider">Input satu hari untuk jadwal tunggal.</p>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Template akan digunakan untuk generate jadwal bulanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODE SATU HARI -->
                <div id="singleDayInput" class="border border-gray-200 rounded-sm">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                        <h3 class="text-[10px] font-bold text-teal-700 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-calendar-day text-[10px]"></i> Detail Hari Tunggal</h3>
                    </div>
                    <div class="p-5 bg-white">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- dropdown klinik -->
                            <div style="overflow: visible;">
                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Klinik <span class="text-red-500">*</span></label>
                                <div class="cs-wrap" id="id_klinik_single_wrap">
                                    <button type="button" class="cs-btn" id="klinikSingleBtn"><span class="text-gray-400">-- Pilih Klinik --</span><i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i></button>
                                    <div class="cs-list">
                                        @foreach($klinik as $k)
                                        <div class="cs-item" data-val="{{ $k->id_klinik }}">{{ $k->nama_klinik }}</div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="id_klinik" id="id_klinik_single">
                                </div>
                                <p class="field-error" id="klinikSingleError"><i class="fas fa-exclamation-circle mr-1"></i>Silakan pilih klinik.</p>
                            </div>
                            <!-- dropdown hari -->
                            <div style="overflow: visible;">
                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Hari <span class="text-red-500">*</span></label>
                                <div class="cs-wrap" id="hari_single_wrap">
                                    <button type="button" class="cs-btn" id="hariSingleBtn"><span class="text-gray-400">-- Pilih Hari --</span><i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i></button>
                                    <div class="cs-list">
                                        <div class="cs-item" data-val="senin">Senin</div>
                                        <div class="cs-item" data-val="selasa">Selasa</div>
                                        <div class="cs-item" data-val="rabu">Rabu</div>
                                        <div class="cs-item" data-val="kamis">Kamis</div>
                                        <div class="cs-item" data-val="jumat">Jumat</div>
                                        <div class="cs-item" data-val="sabtu">Sabtu</div>
                                        <div class="cs-item" data-val="minggu">Minggu</div>
                                    </div>
                                    <input type="hidden" name="hari" id="hari_single">
                                </div>
                                <p class="field-error" id="hariSingleError"><i class="fas fa-exclamation-circle mr-1"></i>Silakan pilih hari.</p>
                            </div>
                            <!-- jam mulai -->
                            <div>
                                <label for="jam_mulai_single" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                                <input type="time" name="jam_mulai" id="jam_mulai_single" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white hover:border-teal-400 hover:bg-white hover:shadow-sm transition-all duration-300 ease-in-out">
                                <p class="field-error" id="jamMulaiSingleError"><i class="fas fa-exclamation-circle mr-1"></i>Waktu mulai wajib diisi.</p>
                            </div>
                            <!-- jam selesai -->
                            <div>
                                <label for="jam_selesai_single" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Waktu Selesai <span class="text-red-500">*</span></label>
                                <input type="time" name="jam_selesai" id="jam_selesai_single" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white hover:border-teal-400 hover:bg-white hover:shadow-sm transition-all duration-300 ease-in-out">
                                <p class="field-error" id="jamSelesaiSingleError"><i class="fas fa-exclamation-circle mr-1"></i>Waktu selesai wajib diisi.</p>
                                <p class="field-error" id="jamSelesaiAfterError"><i class="fas fa-exclamation-circle mr-1"></i>Jam selesai harus lebih lambat dari jam mulai.</p>
                            </div>
                        </div>
                        <!-- pesan error global buat mode single -->
                        <p class="field-error mt-4" id="singleGlobalError"><i class="fas fa-exclamation-circle mr-1"></i>Harap lengkapi semua data (Klinik, Hari, Jam Mulai, Jam Selesai).</p>
                    </div>
                </div>

                <!-- MODE SEMINGGU -->
                <div id="weeklyInput" class="border border-gray-200 rounded-sm hidden">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                        <h3 class="text-[10px] font-bold text-teal-700 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-calendar-week text-[10px]"></i> Detail Jadwal Seminggu</h3>
                        <p class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">Pilih klinik dan jam untuk setiap hari. Kosongkan hari yang libur.</p>
                    </div>
                    <div class="p-5 bg-white">
                        <!-- card per hari, dari senin sampai minggu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $hari)
                            <div class="bg-gray-50 border border-gray-200 rounded-sm p-4 hover:border-teal-500 transition-colors flex flex-col" style="overflow: visible;" id="{{ $hari }}_card">
                                <div class="flex items-center gap-2 mb-3 border-b border-gray-200 pb-2">
                                    <h4 class="font-bold text-gray-800 text-xs uppercase tracking-wider">{{ ucfirst($hari) }}</h4>
                                </div>
                                <div class="space-y-3 flex-1">
                                    <!-- dropdown klinik per hari -->
                                    <div style="overflow: visible;">
                                        <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1">Klinik</label>
                                        <div class="cs-wrap" id="{{ $hari }}_id_klinik_wrap">
                                            <button type="button" class="cs-btn"><span class="text-gray-400">-- Libur --</span><i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i></button>
                                            <div class="cs-list">
                                                @foreach($klinik as $k)
                                                <div class="cs-item" data-val="{{ $k->id_klinik }}">{{ $k->nama_klinik }}</div>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="{{ $hari }}_id_klinik" id="{{ $hari }}_id_klinik">
                                        </div>
                                    </div>
                                    <!-- jam mulai & selesai per hari -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1">Mulai</label>
                                            <input type="time" name="{{ $hari }}_mulai" id="{{ $hari }}_mulai" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 hover:border-teal-400 hover:shadow-sm transition-all duration-300 ease-in-out">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-600 uppercase tracking-widest mb-1">Selesai</label>
                                            <input type="time" name="{{ $hari }}_selesai" id="{{ $hari }}_selesai" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 hover:border-teal-400 hover:shadow-sm transition-all duration-300 ease-in-out">
                                        </div>
                                    </div>
                                    <!-- pesan error per hari -->
                                    <p class="field-error" id="{{ $hari }}_error"><i class="fas fa-exclamation-circle mr-1"></i>Data tidak lengkap. Isi klinik, jam mulai, dan jam selesai.</p>
                                    <p class="field-error" id="{{ $hari }}_jam_error"><i class="fas fa-exclamation-circle mr-1"></i>Jam selesai harus lebih lambat dari jam mulai.</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <!-- pesan error global buat mode weekly -->
                        <p class="field-error mt-3" id="weeklyGlobalError"><i class="fas fa-exclamation-circle mr-1"></i>Harap isi minimal 1 hari dengan lengkap.</p>
                        <!-- info konfirmasi -->
                        <div class="mt-5 p-3 bg-teal-50 border border-teal-100 rounded-sm flex items-center gap-3">
                            <i class="fas fa-check-circle text-teal-700 text-xs"></i>
                            <span class="text-[10px] text-teal-800 font-bold uppercase tracking-wider">Semua jadwal di atas akan otomatis menjadi template untuk generate bulanan</span>
                        </div>
                    </div>
                </div>

                <!-- footer form: tombol batal & simpan -->
                <div class="border border-gray-200 rounded-sm px-6 py-4 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-[10px] text-gray-500 flex items-center gap-2 uppercase tracking-wider">
                            <i class="fas fa-info-circle text-gray-400"></i>
                            <span id="modeDescription">Input satu hari jadwal dokter</span>
                        </div>
                        <div class="flex gap-3 w-full sm:w-auto justify-end">
                            <a href="{{ route('admin.jadwal.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-800 font-bold rounded-sm hover:bg-gray-100 transition-colors text-xs uppercase tracking-wider text-center">Batal</a>
                            <button type="submit" id="submitBtn" class="px-6 py-2.5 bg-teal-700 text-white font-bold rounded-sm hover:bg-teal-800 transition-colors text-xs uppercase tracking-wider"><i class="fas fa-save mr-2 text-[10px]"></i>Simpan Template</button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // bantuan: bikin huruf pertama kapital
    function ucfirst(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ''; }

    // inisialisasi dropdown custom (buka-tutup, cari, pilih)
    function initCS(wrap) {
        if (!wrap) return;
        const btn = wrap.querySelector('.cs-btn');
        const list = wrap.querySelector('.cs-list');
        const input = wrap.querySelector('input[type="hidden"]');
        if (!btn || !list || !input) return;

        // klik tombol buka/tutup dropdown
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (btn.disabled) return;
            const isOpen = list.classList.contains('active');
            // tutup semua dropdown dulu
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
            // kalau sebelumnya tutup, buka sekarang
            if (!isOpen) { list.classList.add('active'); btn.classList.add('active'); }
        });

        // fitur cari di dropdown
        const searchInput = wrap.querySelector('.cs-search');
        if (searchInput) {
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                const items = list.querySelectorAll('.cs-item');
                items.forEach(item => {
                    const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
                    item.style.display = text.includes(term) ? 'flex' : 'none';
                });
            });
        }

        // pas klik salah satu item
        list.querySelectorAll('.cs-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = item.dataset.val; 
                const text = item.textContent.trim();
                input.value = val;
                btn.querySelector('span').textContent = text;
                list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                list.classList.remove('active');
                btn.classList.remove('active');
                // trigger event change biar bisa didenger kode lain
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    // klik di luar dropdown → tutup semua
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.cs-wrap')) {
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
        }
    });

    // jalankan initCS buat semua dropdown yang ada
    document.querySelectorAll('.cs-wrap').forEach(initCS);

    // ganti status draft pas pindah radio bulan ini / bulan depan
    function updateDraftStatus(value) {
        document.getElementById('is_draft_hidden').value = value;
        const labelBulanIni = document.getElementById('label_bulan_ini');
        const labelBulanDepan = document.getElementById('label_bulan_depan');
        const activeClass = ['border-teal-700', 'bg-teal-50'];
        const draftClass = ['border-blue-700', 'bg-blue-50'];
        const inactiveClass = ['border-gray-200'];
        if (value == 1) {
            labelBulanDepan.classList.remove(...inactiveClass);
            labelBulanDepan.classList.add(...draftClass);
            labelBulanIni.classList.remove(...activeClass);
            labelBulanIni.classList.add(...inactiveClass);
        } else {
            labelBulanIni.classList.remove(...inactiveClass);
            labelBulanIni.classList.add(...activeClass);
            labelBulanDepan.classList.remove(...draftClass);
            labelBulanDepan.classList.add(...inactiveClass);
        }
    }

    // pindah mode single / weekly
    function switchMode(mode) {
        const singleDayInput = document.getElementById('singleDayInput');
        const weeklyInput = document.getElementById('weeklyInput');
        const modeDescription = document.getElementById('modeDescription');
        const infoText = document.getElementById('infoText');
        const inputMode = document.getElementById('input_mode');
        const submitBtn = document.getElementById('submitBtn');
        document.querySelectorAll('input[name="mode_selector"]').forEach(radio => { radio.checked = (radio.value === mode); });
        clearAllInlineErrors();
        if (mode === 'single') {
            singleDayInput.classList.remove('hidden');
            weeklyInput.classList.add('hidden');
            modeDescription.textContent = 'Input satu hari jadwal dokter';
            infoText.textContent = 'Input satu hari untuk jadwal tunggal.';
            inputMode.value = 'single';
            submitBtn.innerHTML = '<i class="fas fa-save mr-2 text-[10px]"></i>Simpan Template';
            toggleRequired('singleDayInput', true);
            toggleRequired('weeklyInput', false);
        } else {
            singleDayInput.classList.add('hidden');
            weeklyInput.classList.remove('hidden');
            modeDescription.textContent = 'Input jadwal untuk seluruh minggu (bisa beda klinik per hari)';
            infoText.textContent = 'Isi klinik dan jam untuk setiap hari yang aktif.';
            inputMode.value = 'weekly';
            submitBtn.innerHTML = '<i class="fas fa-save mr-2 text-[10px]"></i>Simpan Seminggu';
            toggleRequired('singleDayInput', false);
            toggleRequired('weeklyInput', false);
        }
    }

    // atur atribut required di input sesuai mode yang aktif
    function toggleRequired(containerId, isRequired) {
        const container = document.getElementById(containerId);
        const inputs = container.querySelectorAll('input');
        inputs.forEach(input => {
            if (isRequired) { input.setAttribute('required', 'required'); }
            else { input.removeAttribute('required'); }
        });
    }

    // tampilin error inline di field tertentu
    function showInlineError(elementId, inputId) {
        const errorEl = document.getElementById(elementId);
        if (errorEl) errorEl.classList.add('show');
        if (inputId) {
            const inputEl = document.getElementById(inputId);
            if (inputEl) inputEl.classList.add('input-error');
        }
    }
    // sembunyiin error inline
    function hideInlineError(elementId, inputId) {
        const errorEl = document.getElementById(elementId);
        if (errorEl) errorEl.classList.remove('show');
        if (inputId) {
            const inputEl = document.getElementById(inputId);
            if (inputEl) inputEl.classList.remove('input-error');
        }
    }
    // bersihin semua error yang tampil
    function clearAllInlineErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.classList.remove('show'));
        document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        document.querySelectorAll('.card-error').forEach(el => el.classList.remove('card-error'));
    }

    // pas dokter udah dipilih, hilangin error dokter
    document.getElementById('id_dokter').addEventListener('change', function() {
        if (this.value) hideInlineError('dokterError', 'dokterBtn');
    });

    // validasi form pas submit
    document.getElementById('jadwalForm').addEventListener('submit', function(e) {
        const mode = document.getElementById('input_mode').value;
        let hasError = false;
        clearAllInlineErrors();

        // cek dokter udah dipilih belum
        const dokterVal = document.getElementById('id_dokter').value;
        if (!dokterVal) {
            e.preventDefault();
            showInlineError('dokterError', 'dokterBtn');
            hasError = true;
        }

        if (mode === 'single') {
            // validasi mode single: cek klinik, hari, jam mulai, jam selesai
            const klinik = document.getElementById('id_klinik_single').value;
            const hari = document.getElementById('hari_single').value;
            const mulai = document.getElementById('jam_mulai_single').value;
            const selesai = document.getElementById('jam_selesai_single').value;

            let singleIncomplete = false;
            if (!klinik) { showInlineError('klinikSingleError', 'klinikSingleBtn'); singleIncomplete = true; }
            if (!hari) { showInlineError('hariSingleError', 'hariSingleBtn'); singleIncomplete = true; }
            if (!mulai) { showInlineError('jamMulaiSingleError', 'jam_mulai_single'); singleIncomplete = true; }
            if (!selesai) { showInlineError('jamSelesaiSingleError', 'jam_selesai_single'); singleIncomplete = true; }

            if (singleIncomplete) {
                e.preventDefault();
                showInlineError('singleGlobalError');
                hasError = true;
            }

            // cek jam selesai harus lebih lambat dari jam mulai
            if (mulai && selesai && mulai >= selesai) {
                e.preventDefault();
                showInlineError('jamSelesaiAfterError', 'jam_selesai_single');
                hasError = true;
            }
        }

        if (mode === 'weekly') {
            // validasi mode weekly: cek tiap hari yang diisi
            const hariList = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];
            let hasFilledDay = false;
            let hasIncompleteDay = false;

            hariList.forEach(hari => {
                const klinikInput = document.querySelector(`input[name="${hari}_id_klinik"]`);
                const mulaiInput = document.querySelector(`input[name="${hari}_mulai"]`);
                const selesaiInput = document.querySelector(`input[name="${hari}_selesai"]`);

                if(klinikInput && mulaiInput && selesaiInput) {
                    const klinik = klinikInput.value;
                    const mulai = mulaiInput.value;
                    const selesai = selesaiInput.value;

                    // kalau salah satu field diisi, berarti hari itu mau diisi — harus lengkap semua
                    if (klinik || mulai || selesai) {
                        if (klinik && mulai && selesai) {
                            // cek jam selesai > jam mulai
                            if (mulai >= selesai) {
                                e.preventDefault();
                                showInlineError(hari + '_jam_error');
                                document.getElementById(hari + '_card').classList.add('card-error');
                                hasError = true;
                            } else {
                                hasFilledDay = true;
                            }
                        } else {
                            // ada yang belum keisi
                            hasIncompleteDay = true;
                            e.preventDefault();
                            showInlineError(hari + '_error');
                            document.getElementById(hari + '_card').classList.add('card-error');
                            hasError = true;
                        }
                    }
                }
            });

            // harus ada minimal 1 hari yang keisi lengkap
            if (!hasFilledDay && !hasError) {
                e.preventDefault();
                showInlineError('weeklyGlobalError');
                hasError = true;
            }
        }

        if (hasError) return;

        // kalau lolos validasi, tombol jadi loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2 text-[10px]"></i>Memproses...';
    });

    // set mode awal ke single
    switchMode('single');

    @php
        // siapin error dari backend buat ditampilkan di popup
        $backendErrors = [];
        $bentrokDetailHtml = '';

        // ambil error bentrok single
        if ($errors->has('bentrok_single')) {
            $backendErrors[] = $errors->first('bentrok_single');
        }

        // kalau ada detail bentrok weekly, pakai itu. kalau cuma weekly_error biasa, pakai itu
        if ($errors->has('weekly_bentrok_detail')) {
            $bentrokDetailHtml = $errors->first('weekly_bentrok_detail');
        } elseif ($errors->has('weekly_error')) {
            $backendErrors[] = $errors->first('weekly_error');
        }

        $backendErrors = array_unique($backendErrors); 
    @endphp

    // popup error dari server (misalnya jadwal bentrok)
    @if(count($backendErrors) > 0 || !empty($bentrokDetailHtml))
    Swal.fire({
        width: 380,
        html: `<div class="text-center">
                <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                    <i class="fas fa-times-circle" style="font-size:20px; color:#dc2626;"></i>
                </div>
                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal Menyimpan</h2>
                <p style="font-size:13px; color:#6b7280;">{!! implode('<br>', array_map('e', $backendErrors)) !!}</p>
                {!! $bentrokDetailHtml !!}
               </div>`,
        confirmButtonText: 'Mengerti',
        customClass: { popup: 'rounded-sm p-5', confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' }
    });
    @endif

    // popup sukses
    @if(session('success'))
    Swal.fire({
        width: 380,
        html: `<div class="text-center">
                <div style="width:48px;height:48px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #bbf7d0;">
                    <i class="fas fa-check-circle" style="font-size:20px; color:#16a34a;"></i>
                </div>
                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Berhasil</h2>
                <p style="font-size:13px; color:#6b7280;">{{ session('success') }}</p>
               </div>`,
        confirmButtonText: 'OK',
        timer: 3000,
        timerProgressBar: true,
        customClass: { popup: 'rounded-sm p-5', confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' }
    });
    @endif

    // popup error flash
    @if(session('error'))
    Swal.fire({
        width: 380,
        html: `<div class="text-center">
                <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                    <i class="fas fa-times-circle" style="font-size:20px; color:#dc2626;"></i>
                </div>
                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal</h2>
                <p style="font-size:13px; color:#6b7280;">{{ session('error') }}</p>
               </div>`,
        confirmButtonText: 'Mengerti',
        customClass: { popup: 'rounded-sm p-5', confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' }
    });
    @endif
</script>
@endsection