@extends('layouts.admin')
@section('title', 'Buat Reservasi Baru')
@section('header-title', 'Buat Reservasi Baru')
@include('layouts.partials.alert')

@section('content')
<style>
    /* Custom Select Base */
    .cs-wrap { 
        position: relative; 
        z-index: 9999; 
    }
    
    .cs-wrap:has(.cs-list.active) {
        z-index: 99999;
    }

    .cs-btn { 
        width: 100%; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        padding: 0.625rem 0.75rem; 
        background: #f9fafb; 
        border: 1px solid #d1d5db; 
        border-radius: 2px; 
        font-size: 0.875rem; 
        text-align: left; 
        cursor: pointer; 
        transition: all 0.15s; 
        color: #1f2937; 
        user-select: none;
    }
    
    .cs-btn:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
    
    .cs-btn:not(:disabled):hover { 
        border-color: #9ca3af; 
        background: #fff; 
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); 
    }
    
    .cs-btn.active { 
        border-color: #0f766e; 
        box-shadow: 0 0 0 1px #0f766e; 
        background: #fff; 
    }
    
    .cs-btn.active i { transform: rotate(180deg); }

    .cs-list { 
        position: absolute; 
        top: 100%; 
        left: 0; 
        right: 0; 
        margin-top: 4px; 
        background: #fff; 
        border: 1px solid #e5e7eb; 
        border-radius: 4px; 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
        z-index: 9999; 
        max-height: 280px; 
        overflow-y: auto; 
        opacity: 0; 
        visibility: hidden; 
        transform: translateY(-5px); 
        transition: all 0.15s ease; 
        pointer-events: none;
    }
    
    .cs-list.active { 
        opacity: 1; 
        visibility: visible; 
        transform: translateY(0); 
        pointer-events: auto;
    }
    
    .cs-list::-webkit-scrollbar { width: 4px; }
    .cs-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .cs-search {
        width: 100%;
        padding: 8px 12px;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.8rem;
        outline: none;
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
    }
    .cs-search:focus { background: #f9fafb; }

    .cs-item { 
        padding: 0.5rem 0.75rem; 
        font-size: 0.875rem; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        transition: background 0.1s; 
        color: #374151;
    }
    
    .cs-item:hover { background: #f0fdfa; color: #0f766e; }
    
    .cs-item.selected { 
        background: #f0fdfa; 
        color: #0f766e; 
        font-weight: 600; 
    }

    /* Pasien Item Extra Styles */
    .cs-item .cs-item-info {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .cs-item .cs-item-name {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }
    .cs-item .cs-item-meta {
        font-size: 0.65rem;
        color: #9ca3af;
        letter-spacing: 0.03em;
    }
    .cs-item:hover .cs-item-meta,
    .cs-item.selected .cs-item-meta {
        color: #5eead4;
    }

    /* Custom Scrollbar thin & nice */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
</style>

<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- menjunjukan halaman saat ini -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.reservasi.index') }}" class="hover:text-teal-700 transition-colors font-bold">Reservasi</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Tambah Baru</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Buat Reservasi Baru</h1>
                <p class="text-xs text-gray-500 mt-1">Jadwalkan kunjungan pasien ke klinik</p>
            </div>
            <a href="{{ route('admin.reservasi.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- Form input -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.reservasi.store') }}" method="POST" id="reservasiForm">
                @csrf
                <!-- Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-4 items-start">
                    <div class="lg:col-span-4 space-y-8">
                        <!-- Identitas Pasien -->
                        <div>
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-user-injured text-teal-700 text-[9px]"></i> Identitas Pasien
                            </h3>
                            <div class="space-y-5">
                                <!-- Klinik, tidak memilih -->
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Klinik</label>
                                    @if(isset($klinik) && $klinik !== null)
                                        <input type="hidden" name="id_klinik" value="{{ $klinik->id_klinik }}">
                                        <div class="w-full px-3 py-2.5 bg-teal-50 border border-teal-200 rounded-sm text-teal-900 text-sm font-medium flex items-center gap-2">
                                            <i class="fas fa-hospital text-teal-700 text-xs"></i>
                                            {{ $klinik->nama_klinik }}
                                        </div>
                                    @else
                                        <select name="id_klinik" id="klinik_id" 
                                                class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors" 
                                                required>
                                            <option value="">-- Pilih Klinik --</option>
                                            @foreach($kliniks ?? [] as $k)
                                                <option value="{{ $k->id_klinik }}" {{ old('id_klinik') == $k->id_klinik ? 'selected' : '' }}>
                                                    {{ $k->nama_klinik }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <!-- Pasien -->
                                <div style="overflow: visible;">
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Pasien <span class="text-red-500">*</span></label>
                                    <div class="cs-wrap" id="pasien_id_wrap">
                                        <button type="button" class="cs-btn">
                                            <span class="text-gray-400">-- Pilih Pasien --</span>
                                            <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                                        </button>
                                        <div class="cs-list">
                                            <!-- Input Pencarian -->
                                            <input type="text" class="cs-search" placeholder="Cari nama atau No. HP pasien...">
                                            <!-- loop data pasien -->
                                            @forelse($pasiens as $p)
                                                <div class="cs-item" 
                                                     data-val="{{ $p->id_pasien }}" 
                                                     data-display="{{ $p->nama }}"
                                                     data-search="{{ strtolower($p->nama) }} {{ strtolower($p->no_hp ?? '') }}">
                                                    <div class="cs-item-info">
                                                        <span class="cs-item-name">{{ $p->nama }}</span>
                                                        <!-- @if(isset($p->no_hp) && $p->no_hp)
                                                            <span class="cs-item-meta"><i class="fas fa-phone text-[7px] mr-1"></i>{{ $p->no_hp }}</span>
                                                        @endif -->
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="px-4 py-3 text-center text-xs text-gray-400 uppercase tracking-wider italic">
                                                    Tidak ada pasien tersedia
                                                </div>
                                            @endforelse
                                        </div>
                                        <input type="hidden" name="pasien_id" id="pasien_id_hidden" value="{{ old('pasien_id') }}" required>
                                    </div>
                                    @error('pasien_id')
                                        <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Keluhan -->
                        <div>
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-clipboard-list text-teal-700 text-[9px]"></i> Keluhan
                            </h3>
                            <div>
                                <label for="keluhan" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Detail Keluhan</label>
                                <textarea name="keluhan" id="keluhan" rows="3" 
                                          class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400" 
                                          required placeholder="Deskripsikan keluhan...">{{ old('keluhan') }}</textarea>
                                <div class="flex justify-between mt-1.5">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Singkat & Jelas</p>
                                    <span id="charCount" class="text-[10px] text-gray-400 uppercase tracking-wider">0/500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- pilih jadwal -->
                    <div class="lg:col-span-8 h-full flex flex-col">
                        <div class="bg-white border border-gray-200 flex flex-col max-h-[calc(100vh-220px)] sticky top-6 rounded-sm">
                            
                            <div class="px-6 py-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                                <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-teal-700 text-[9px]"></i> Pilih Jadwal Dokter
                                </h3>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col bg-slate-50/50 min-h-0 overflow-hidden">
                                
                                <!-- pilih tanggal -->
                                <div class="mb-4 bg-white p-3 border border-gray-200 shadow-sm shrink-0 rounded-sm">
                                    <label for="tanggal" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                                        <i class="fas fa-calendar-day text-teal-700 text-[9px]"></i> Pilih Tanggal Praktek
                                    </label>
                                    <input type="date" id="tanggal" name="tgl_reservasi" 
                                        value="{{ old('tgl_reservasi') }}"
                                        class="w-full px-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-medium bg-white transition-colors" 
                                        required>
                                    <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-info-circle text-[8px]"></i>
                                        <span>Maks 7 hari ke depan</span>
                                    </div>
                                </div>

  
                                <!-- menampilkan jadwal yang sesuai tanggal -->
                                <div id="jadwalContainer" class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                                    <div class="h-full flex flex-col items-center justify-center border-2 border-dashed border-gray-200 bg-white min-h-[250px] rounded-sm">
                                        <div class="w-14 h-14 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-200">
                                            <i class="fas fa-calendar-check text-gray-300 text-2xl"></i>
                                        </div>
                                        <h4 class="text-gray-700 font-bold text-xs uppercase tracking-wider">Pilih Tanggal</h4>
                                        <p class="text-gray-400 text-[10px] mt-1 uppercase tracking-wider">Lihat jadwal di sini setelah tanggal dipilih</p>
                                    </div>
                                </div>
                                
                                <!-- Hidden inputs -->
                                <input type="hidden" name="id_jadwal" id="id_jadwal" required>
                                <input type="hidden" name="jam_reservasi" id="jam_reservasi" required>
                                <input type="hidden" name="antrean" id="antrean" required>
                                
                                <!-- Info Box -->
                                <div id="jadwalInfo" class="hidden mt-3 p-3 bg-teal-50 border border-teal-200 rounded-sm text-xs text-teal-900 shadow-sm shrink-0">
                                    <!-- Content injected by JS -->
                                </div>
                                
                                @error('id_jadwal')
                                    <div class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 bg-red-50 p-2 rounded-sm border border-red-200">
                                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <!-- aksi -->
                <div class="bg-white border border-gray-200 px-6 py-3 shadow-sm sticky bottom-0 z-50 mt-2 rounded-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider italic">
                            <i class="fas fa-shield-alt text-teal-700 mr-1"></i>
                            Cek kembali data sebelum menyimpan.
                        </div>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-paper-plane text-[10px]"></i>
                            Buat Reservasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // custom dropdown
    function initCS(wrap) {
        if (!wrap) return;
        // ambil elemen html
        const btn = wrap.querySelector('.cs-btn');
        const list = wrap.querySelector('.cs-list');
        const input = wrap.querySelector('input[type="hidden"]');
        // jika salah satu elemen tidak ada, fungsi tidak dijalankan
        if (!btn || !list || !input) return;
        // jalankan fungsi ketika di klik
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (btn.disabled) return;
            
            const isOpen = list.classList.contains('active');
            // Tutup semua dropdown lain dulu
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
            // Jika sebelumnya tertutup, buka sekarang
            if (!isOpen) {
                list.classList.add('active');
                btn.classList.add('active');
            }
        });
        // Search Logic di dropdown
        const searchInput = wrap.querySelector('.cs-search');
        if (searchInput) {
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                const items = list.querySelectorAll('.cs-item');
                let hasVisible = false;
                items.forEach(item => {
                    const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
                    // kalo cocok ditampilkan jika tidak cocok sembunyikan
                    if (text.includes(term)) {
                        item.style.display = 'flex';
                        hasVisible = true;
                    } else {
                        item.style.display = 'none';
                    }
                });
                // Tampilkan pesan kosong jika perlu (opsional)
                const noResultEl = list.querySelector('.cs-no-result');
                if (noResultEl) {
                    noResultEl.style.display = hasVisible ? 'none' : 'block';
                }
            });
        }
        // Pilih Item
        list.querySelectorAll('.cs-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = item.dataset.val; //ambil value
                // Gunakan data-display jika ada
                const text = item.dataset.display || item.textContent.trim();

                input.value = val; //simpan ke input hidden
                btn.querySelector('span').textContent = text; //tulisan tombol berubah
                btn.querySelector('span').classList.remove('text-gray-400');
                btn.querySelector('span').classList.add('text-gray-800');
                
                // Visual Feedback
                list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                
                // Tutup Dropdown
                list.classList.remove('active');
                btn.classList.remove('active');

                // Trigger event change
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }
    //tutup jika klik diluar dropdown
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.cs-wrap')) {
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
        }
    });
    // Inisialisasi semua dropdown saat load
    document.querySelectorAll('.cs-wrap').forEach(initCS);

    // mengembalikan pilihan lama jika validasi gagal
    @if(old('pasien_id'))
        (function() {
            const oldPasienId = "{{ old('pasien_id') }}";
            const wrap = document.getElementById('pasien_id_wrap');
            if (!wrap) return;
            
            const btn = wrap.querySelector('.cs-btn');
            const input = wrap.querySelector('input[type="hidden"]');
            const item = wrap.querySelector(`.cs-item[data-val="${oldPasienId}"]`);
            
            if (item && btn && input) {
                const text = item.dataset.display || item.textContent.trim();
                input.value = oldPasienId;
                btn.querySelector('span').textContent = text;
                btn.querySelector('span').classList.remove('text-gray-400');
                btn.querySelector('span').classList.add('text-gray-800');
                item.classList.add('selected');
            }
        })();
    @endif


    // mengatur tanggal yang boleh dipilih
    const tanggalInput = document.getElementById('tanggal');
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 7); // 7 hari kedepan

    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    // mengatur tanggal input
    tanggalInput.setAttribute('min', formatDate(today));
    tanggalInput.setAttribute('max', formatDate(maxDate));

    const baseUrl = "{{ route('admin.reservasi.getJadwal') }}";
    let jadwalCache = {}; //penyimpanan sementara
    // mengambil id klinik yang sedang dipilih
    function getCurrentKlinikId() {
        @if(isset($klinik) && $klinik !== null)
            return "{{ $klinik->id_klinik }}";
        @else
            const klinikSelect = document.getElementById('klinik_id');
            return klinikSelect ? klinikSelect.value : null;
        @endif
    }

    //load jadwal, dipanggil saat tanggal berubah
    async function loadJadwal(tanggal) {
        //ambil klinik
        const klinikId = getCurrentKlinikId();
        //jika klonik atau tgl kosong
        if (!klinikId || !tanggal) {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-exclamation text-gray-400"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Lengkapi data di kiri</p></div>');
            return;
        }
        const cacheKey = `${klinikId}_${tanggal}`;
    
        const container = document.getElementById('jadwalContainer');
        // menampilkan loading
        container.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center min-h-[250px]">
                <div class="w-10 h-10 rounded-full border-4 border-gray-100 border-t-teal-700 animate-spin mb-3"></div>
                <p class="text-teal-700 font-bold animate-pulse text-xs uppercase tracking-wider">Memuat jadwal...</p>
            </div>
        `;
        //kalao data sudah pernah diambil, langsung render jadwal tanpa panggil server
        if (jadwalCache[cacheKey]) {
            renderJadwal(jadwalCache[cacheKey]);
            return;
        }

        try { //ambil data ke server
            const response = await fetch(`${baseUrl}?klinik_id=${klinikId}&tanggal=${tanggal}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) throw new Error('Gagal memuat jadwal');
            //ambil json jadi objject js
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal memuat jadwal');
            }
            //data disimmpan
            jadwalCache[cacheKey] = result.data;
            renderJadwal(result.data);
        } catch (error) {
            console.error('Error:', error);
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-red-50 flex items-center justify-center border border-red-200 rounded-sm"><i class="fas fa-times text-red-400"></i></div><p class="text-red-500 text-[10px] font-bold uppercase tracking-wider">Gagal memuat data</p></div>');
        }
    }
    // membuat kartu jadwal dokter
    function renderJadwal(jadwalList) {
        const container = document.getElementById('jadwalContainer');
        // cek apakah jadwal kosong
        if (!jadwalList || jadwalList.length === 0) {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-calendar-times text-gray-300"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Tidak ada jadwal</p></div>');
            return;
        }
        // menyiapkan variabel ksoong
        let html = '';    
        //perulangan dokter    
        jadwalList.forEach((jadwal, index) => {
            const delay = index * 50; //untuk animasi
            //membuat kartu dokter
            html += `
                <div class="group bg-white border border-gray-200 overflow-hidden shadow-sm transition-all duration-200 animate-fade-in-up rounded-sm" style="animation-delay: ${delay}ms">
                    <!-- Header Dokter -->
                    <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-sm bg-teal-700 text-white flex items-center justify-center font-bold text-[10px] uppercase">
                                ${jadwal.dokter_nama.charAt(0)}
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-800 leading-tight uppercase tracking-wider">${jadwal.dokter_nama}</h4>
                                <span class="text-[10px] font-bold text-gray-500 bg-gray-200 px-1.5 py-0.5 rounded-sm uppercase tracking-widest">${jadwal.dokter_spesialisasi}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-teal-700 uppercase tracking-widest flex items-center gap-1">
                                <i class="far fa-clock text-[8px]"></i> ${jadwal.jam_mulai} - ${jadwal.jam_selesai}
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                            ${jadwal.slots.map(slot => {
                                const isAvailable = slot.tersedia; //mengambil apakah slot tersedia
                                //mengambil warna tombol
                                let btnClass = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border transition-all duration-200 text-xs font-bold uppercase ';
                                //jika tersedia, tombol normal dan bisa di klik
                                if (isAvailable) {
                                    btnClass += 'bg-white border-gray-300 text-gray-800 hover:border-teal-500 hover:bg-teal-50';
                                } else { //jika tidak tersedia tombol abu abu dan di coret
                                    btnClass += 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed line-through';
                                }
                                // membuat tombol slot
                                return `
                                    <button type="button" 
                                            class="${btnClass}"
                                            ${isAvailable ? `
                                                data-jadwal-id="${jadwal.id_jadwal}"
                                                data-jam-mulai="${slot.jam_mulai}"
                                                data-jam-selesai="${slot.jam_selesai}"
                                                data-antrean="${slot.antrean}"
                                                data-interval="${slot.interval}"
                                            ` : 'disabled'}
                                            title="${isAvailable ? 'Slot Antrean #' + slot.antrean : 'Terisi'}">
                                        <div class="text-sm leading-none mb-0.5">#${slot.antrean}</div>
                                        <div class="text-[9px] leading-none opacity-70">${slot.jam_mulai}</div>
                                        
                                        <div class="absolute top-1 right-1 w-1.5 h-1.5 rounded-full ${isAvailable ? 'bg-teal-500' : 'bg-gray-400'}"></div>
                                    </button>
                                `;
                            }).join('')}
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    //kalau ada klik di area jadwal container
    document.getElementById('jadwalContainer').addEventListener('click', function(e) {
        const btn = e.target.closest('.slot-btn[data-jadwal-id]'); //cari tombol yang di klik
        if (!btn) return; //kalo yg di klik bukn slot, berhenti
        
        const idJadwal = btn.dataset.jadwalId; //ambil data tombol
        const jamMulai = btn.dataset.jamMulai; // ambil jam mulai
        const jamSelesai = btn.dataset.jamSelesai; //ambil jam selesai
        const antrean = btn.dataset.antrean; //ambil antrean
        const interval = btn.dataset.interval;
        
        // Reset warna tombol
        document.querySelectorAll('.slot-btn[data-jadwal-id]').forEach(b => {
            b.className = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border transition-all duration-200 text-xs font-bold uppercase bg-white border-gray-300 text-gray-800 hover:border-teal-500 hover:bg-teal-50';
        });
        
        // beri warna pada slot yg dipilih
        btn.className = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border-2 transition-all duration-200 text-xs font-bold uppercase bg-teal-50 border-teal-700 text-teal-900 shadow-sm z-10';
        
        // Set values menyimpan ke input hidden
        document.getElementById('id_jadwal').value = idJadwal;
        document.getElementById('jam_reservasi').value = interval;
        document.getElementById('antrean').value = antrean; //simpan antrean
        
        // Info Box
        const info = document.getElementById('jadwalInfo'); //sbelumnya tresemmbunyi jd keliatan terpilih
        info.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-sm bg-teal-700 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase text-teal-700 font-bold tracking-widest">Terpilih</div>
                        <div class="font-bold text-gray-800 text-xs">#${antrean} &bull; ${jamMulai} - ${jamSelesai}</div>
                    </div>
                </div>
            </div>
        `;
        info.classList.remove('hidden');
    });
    // menampilkan pesan
    function showPlaceholder(html) {
        const container = document.getElementById('jadwalContainer');
        container.innerHTML = `<div class="h-full flex flex-col items-center justify-center min-h-[250px]">${html}</div>`;
        
        document.getElementById('id_jadwal').value = '';
        document.getElementById('jam_reservasi').value = '';
        document.getElementById('antrean').value = '';
        document.getElementById('jadwalInfo').classList.add('hidden');
    }

    tanggalInput.addEventListener('change', function() {
        if (this.value) {
            loadJadwal(this.value);
        } else {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-calendar-alt text-gray-300"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Pilih tanggal</p></div>');
        }
    });
    // event klinik berubah
    @unless(isset($klinik) && $klinik !== null)
        document.getElementById('klinik_id').addEventListener('change', function() {
            jadwalCache = {};
            if (tanggalInput.value) {
                loadJadwal(tanggalInput.value);
            }
        });
    @endunless

    // char count
    const keluhanInput = document.getElementById('keluhan'); //ambil text area keluhan
    const charCount = document.getElementById('charCount'); //ambil jlmh karakter
    const maxChars = 500; //tentukan maximal

    keluhanInput.setAttribute('maxlength', maxChars); //max input
    //saat user mengetik langsung jalankan function
    keluhanInput.addEventListener('input', function() {
        const len = this.value.length; //hitung jumlah karakter
        charCount.textContent = `${len}/${maxChars}`;
        charCount.className = len >= maxChars * 0.9 ? 'text-[10px] text-red-500 font-bold uppercase tracking-wider' : 
                              len >= maxChars * 0.7 ? 'text-[10px] text-amber-500 font-bold uppercase tracking-wider' : 'text-[10px] text-gray-400 uppercase tracking-wider';
    });
    if (keluhanInput.value) keluhanInput.dispatchEvent(new Event('input'));

    // Submit Validation
    document.getElementById('reservasiForm').addEventListener('submit', function(e) {
        // Validasi Pasien
        if (!document.getElementById('pasien_id_hidden').value) {
            e.preventDefault(); //batalkan submit form
            Swal.fire({
                width: 340,
                html: `<div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Pasien Belum Dipilih</h2>
                        <p style="font-size:13px; color:#6b7280;">Silakan pilih nama pasien terlebih dahulu.</p>
                       </div>`,
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-sm p-5',
                    confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
                }
            });
            return; //hentikan proses
        }

        // Validasi Jadwal jika slot antrean blm dipilih
        if (!document.getElementById('id_jadwal').value) {
            e.preventDefault(); 
            Swal.fire({ //popup
                width: 340,
                html: `<div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Slot Belum Dipilih</h2>
                        <p style="font-size:13px; color:#6b7280;">Silakan pilih slot antrean di panel kanan terlebih dahulu.</p>
                       </div>`,
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-sm p-5',
                    confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
                }
            });
            return;
        }
        //ambil tombol submit
        const btn = document.getElementById('submitBtn');
        btn.disabled = true; //supaya user tidak klik brkali kali
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-[10px] mr-1"></i> Memproses...';
    });
</script>
@endsection