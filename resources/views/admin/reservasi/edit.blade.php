@extends('layouts.admin')

@section('title', 'Edit Reservasi')

@section('header-title', 'Edit Reservasi')

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
    
    <!-- menunjukan halaman yg sedang dibuka-->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.reservasi.index') }}" class="hover:text-teal-700 transition-colors font-bold">Reservasi</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit Data</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Reservasi</h1>
                <p class="text-xs text-gray-500 mt-1">Perbarui data reservasi pasien</p>
            </div>
            <a href="{{ route('admin.reservasi.index') }}" 
               class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center whitespace-nowrap">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- Form Edit-->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.reservasi.update', $reservasi->id_reservasi) }}" method="POST" id="reservasiForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-4 items-start">
                    <div class="lg:col-span-4 space-y-8">
                        
                        <!-- Identitas Pasien -->
                        <div>
                            <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-user-injured text-teal-700 text-[9px]"></i> Identitas Pasien
                            </h3>
                            <div class="space-y-5">
                                <div>
                                    <!-- nama klinik tidak bisa dipilih (sesuai admin) -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Klinik</label>
                                    <input type="hidden" name="id_klinik" value="{{ $reservasi->jadwal->id_klinik }}">
                                    <div class="w-full px-3 py-2.5 bg-teal-50 border border-teal-200 rounded-sm text-teal-900 text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-hospital text-teal-700 text-xs"></i>
                                        {{ $reservasi->jadwal->klinik->nama_klinik ?? '-' }}
                                    </div>
                                    <p class="mt-1.5 text-[10px] text-gray-400 uppercase tracking-wider">Klinik tidak dapat diubah</p>
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
                                                    </div>

                                                </div>
                                            @empty
                                                <div class="px-4 py-3 text-center text-xs text-gray-400 uppercase tracking-wider italic">
                                                    Tidak ada pasien tersedia
                                                </div>
                                            @endforelse
                                        </div>
                                        <input type="hidden" name="pasien_id" id="pasien_id_hidden" value="{{ old('pasien_id', $reservasi->id_pasien) }}" required>
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
                                          required placeholder="Deskripsikan keluhan pasien...">{{ old('keluhan', $reservasi->keluhan) }}</textarea>
                                <div class="flex justify-between mt-1.5">
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider">Singkat & Jelas</p>
                                    <span id="charCount" class="text-[10px] text-gray-400 uppercase tracking-wider">0/500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-8 h-full flex flex-col">
                        <div class="bg-white border border-gray-200 flex flex-col max-h-[calc(100vh-220px)] sticky top-6 rounded-sm">
                            
                            <div class="px-6 py-3 border-b border-gray-200 flex items-center justify-between shrink-0">
                                <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest flex items-center gap-2">
                                    <i class="fas fa-calendar-check text-teal-700 text-[9px]"></i> Pilih Jadwal Dokter
                                </h3>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col bg-slate-50/50 min-h-0 overflow-hidden">
                                <!-- TANGGAL -->
                                <div class="mb-4 bg-white p-3 border border-gray-200 shadow-sm shrink-0 rounded-sm">
                                    <label for="tanggal" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5 flex items-center gap-2">
                                        <i class="fas fa-calendar-day text-teal-700 text-[9px]"></i> Tanggal Reservasi
                                    </label>
                                    <input type="date" id="tanggal" name="tgl_reservasi" 
                                        value="{{ old('tgl_reservasi', $reservasi->tgl_reservasi) }}"
                                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-medium transition-colors" 
                                        required>
                                    <div class="flex items-center gap-1.5 mt-1.5 text-[10px] text-gray-400 uppercase tracking-wider">
                                        <i class="fas fa-info-circle text-[8px]"></i>
                                        <span>Maks 7 hari ke depan</span>
                                    </div>
                                </div>

                                <!-- CONTAINER JADWAL -->
                                <div id="jadwalContainer" class="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
                                    <div class="h-full flex flex-col items-center justify-center border-2 border-dashed border-gray-200 bg-white min-h-[250px] rounded-sm">
                                        <div class="w-10 h-10 rounded-full border-4 border-gray-100 border-t-teal-700 animate-spin mb-3"></div>
                                        <p class="text-teal-700 font-bold animate-pulse text-xs uppercase tracking-wider">Memuat jadwal...</p>
                                    </div>
                                </div>
                                
                                <!-- Hidden inputs -->
                                <input type="hidden" name="id_jadwal" id="id_jadwal" 
                                       value="{{ old('id_jadwal', $reservasi->id_jadwal) }}" required>
                                <input type="hidden" name="jam_reservasi" id="jam_reservasi" 
                                       value="{{ old('jam_reservasi', $reservasi->jam_reservasi) }}" required>
                                <input type="hidden" name="antrean" id="antrean" 
                                       value="{{ old('antrean', $reservasi->antrean) }}" required>
                                
                                <!-- Info Box -->
                                <div id="jadwalInfo" class="hidden mt-3 p-3 bg-teal-50 border border-teal-200 rounded-sm text-xs text-teal-900 shadow-sm shrink-0">
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

                <!--aksi -->
                <div class="bg-white border border-gray-200 px-6 py-3 shadow-sm sticky bottom-0 z-50 mt-2 rounded-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-[10px] text-gray-500 uppercase tracking-wider italic">
                            <i class="fas fa-shield-alt text-teal-700 mr-1"></i>
                            Cek kembali data sebelum menyimpan.
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.reservasi.index') }}" 
                               class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                                Batal
                            </a>
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center gap-2">
                                <i class="fas fa-save text-[10px]"></i>
                                Simpan Perubahan
                            </button>
                        </div>
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
        // jika salah satu elemen tidak ada, fungsi dibatalkan
        if (!btn || !list || !input) return;

        //fungsi berjalan ketika button di klik
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
                
                // Fokuskan search input jika ada
                const searchInput = wrap.querySelector('.cs-search');
                if (searchInput) {
                    searchInput.value = '';
                    filterCSItems(list, '');
                    setTimeout(() => searchInput.focus(), 50);
                }
            }
        });

        // Search Logic di dropdown
        const searchInput = wrap.querySelector('.cs-search');
        if (searchInput) {
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('input', (e) => {
                filterCSItems(list, e.target.value.toLowerCase());
            });
        }
        // Pilih Item
        list.querySelectorAll('.cs-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = item.dataset.val;
                const text = item.dataset.display || item.textContent.trim();

                input.value = val; //simpan hidden input
                btn.querySelector('span').textContent = text;
                btn.querySelector('span').classList.remove('text-gray-400');
                btn.querySelector('span').classList.add('text-gray-800');
                
                // Visual Feedback
                list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                
                // Tutup Dropdown
                list.classList.remove('active');
                btn.classList.remove('active');

                // nilai input sudah berubah
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    // Helper: Filter items dalam cs-list
    function filterCSItems(list, term) {
        const items = list.querySelectorAll('.cs-item');
        let hasVisible = false;
        items.forEach(item => {
            const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
            if (text.includes(term)) {
                item.style.display = 'flex';
                hasVisible = true;
            } else {
                item.style.display = 'none';
            }
        });
        // Opsional: tampilkan pesan "tidak ditemukan"
        const noResultEl = list.querySelector('.cs-no-result');
        if (noResultEl) {
            noResultEl.style.display = hasVisible ? 'none' : 'block';
        }
    }
    // tutup dropdown jika di klik di luar dropdown
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.cs-wrap')) {
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
        }
    });
    // Inisialisasi semua dropdown saat load
    document.querySelectorAll('.cs-wrap').forEach(initCS);

    // langsung dijalankan saat halaman dibuka
    (function() {
        //ambil data pasien
        const currentPasienId = "{{ old('pasien_id', $reservasi->id_pasien) }}";
        if (!currentPasienId) return;
        
        const wrap = document.getElementById('pasien_id_wrap');
        if (!wrap) return;
        
        const btn = wrap.querySelector('.cs-btn');
        const input = wrap.querySelector('input[type="hidden"]');
        //cari item yang sama
        const item = wrap.querySelector(`.cs-item[data-val="${currentPasienId}"]`);
        
        if (item && btn && input) {
            const text = item.dataset.display || item.textContent.trim();
            input.value = currentPasienId; //isi hidden input
            btn.querySelector('span').textContent = text; //teks tombol berubah
            btn.querySelector('span').classList.remove('text-gray-400');
            btn.querySelector('span').classList.add('text-gray-800');
            item.classList.add('selected');
        }
    })();
    // mengambil input tanggal
    const tanggalInput = document.getElementById('tanggal');
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 7); //maksimal tanggal 7 hari kedepan
    //ubah date jadi format
    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    //user gabisa milih lebih dari 7 hari
    tanggalInput.setAttribute('min', formatDate(today));
    tanggalInput.setAttribute('max', formatDate(maxDate));

    const baseUrl = "{{ route('admin.reservasi.getJadwal') }}";
    let jadwalCache = {}; //cache jadwal

    // data reservasi saat ini
    const currentReservasiId = {{ $reservasi->id_reservasi }};
    const currentJadwalId = {{ $reservasi->id_jadwal }};
    const currentJamReservasi = "{{ str_replace(' ', '', $reservasi->jam_reservasi) }}";
    const currentAntrean = {{ $reservasi->antrean }};
    const klinikId = {{ $reservasi->jadwal->id_klinik }};

    //load jadwal, dipanggil saat tanggal berubah
    async function loadJadwal(tanggal, autoSelectCurrent = false) {
        // kalo klinik kosong, menampilkan pesan dan function berhenti
        if (!klinikId || !tanggal) {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-exclamation text-gray-400"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Data belum lengkap</p></div>');
            return;
        }

        const cacheKey = `${klinikId}_${tanggal}_${currentReservasiId}`;
        
        const container = document.getElementById('jadwalContainer');
        //menampilkan spinner
        container.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center min-h-[250px]">
                <div class="w-10 h-10 rounded-full border-4 border-gray-100 border-t-teal-700 animate-spin mb-3"></div>
                <p class="text-teal-700 font-bold animate-pulse text-xs uppercase tracking-wider">Memuat jadwal...</p>
            </div>
        `;
        //kalo sudah pernah langsung render tanpa fetch
        if (jadwalCache[cacheKey]) {
            renderJadwal(jadwalCache[cacheKey], autoSelectCurrent);
            return;
        }

        try {
            const response = await fetch( //mengirim request ke laravel
                `${baseUrl}?klinik_id=${klinikId}&tanggal=${tanggal}&exclude_reservasi_id=${currentReservasiId}`, 
                {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }
            );

            if (!response.ok) throw new Error('Gagal memuat jadwal');

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Gagal memuat jadwal');
            }
            //data disimpan
            jadwalCache[cacheKey] = result.data;
            renderJadwal(result.data, autoSelectCurrent);
            
        } catch (error) {
            console.error('Error:', error);
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-red-50 flex items-center justify-center border border-red-200 rounded-sm"><i class="fas fa-times text-red-400"></i></div><p class="text-red-500 text-[10px] font-bold uppercase tracking-wider">Gagal memuat data</p></div>');
        }
    }

    // render jadwal
    function renderJadwal(jadwalList, autoSelectCurrent = false) {
        const container = document.getElementById('jadwalContainer');

        if (!jadwalList || jadwalList.length === 0) {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-calendar-times text-gray-300"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Tidak ada jadwal</p></div>');
            return;
        }
        let html = '';
        // loop dokter
        jadwalList.forEach((jadwal, index) => {
            const delay = index * 50;
            // setiap dokter dibuat card
            html += `
                <div class="group bg-white border border-gray-200 overflow-hidden shadow-sm transition-all duration-200 animate-fade-in-up rounded-sm" style="animation-delay: ${delay}ms">
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
                            <div class="text-[10px] font-bold text-teal-700 uppercase tracking-widest flex items-center gap-1"?
                                <i class="far fa-clock text-[8px]"></i> ${jadwal.jam_mulai} - ${jadwal.jam_selesai}
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-3 bg-white">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                        ${jadwal.slots.map(slot => { //loop slot
                                const isAvailable = slot.tersedia; //slot tersedia, kalo tidak false
                                
                                let btnClass = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border transition-all duration-200 text-xs font-bold uppercase ';
                                
                                if (isAvailable) {
                                    btnClass += 'bg-white border-gray-300 text-gray-800 hover:border-teal-500 hover:bg-teal-50';
                                } else {
                                    btnClass += 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed line-through';
                                }
                                
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

        // Auto-select slot reservasi saat ini saat pertama kali load
        if (autoSelectCurrent && currentJadwalId && currentJamReservasi) {
            const currentSlot = container.querySelector(
                `.slot-btn[data-jadwal-id="${currentJadwalId}"][data-interval="${currentJamReservasi}"]`
            );
            if (currentSlot) {
                selectSlot(currentSlot);
            } else {
                const idJadwalVal = document.getElementById('id_jadwal').value;
                const antreanVal = document.getElementById('antrean').value;
                const jamVal = document.getElementById('jam_reservasi').value;
                if (idJadwalVal && antreanVal && jamVal) {
                    showInfoBox(antreanVal, jamVal, '');
                }
            }
        }
    }

    //ambil idjadwal, jam mulai, jam selesai, antrean, interval
    function selectSlot(btn) {
        const idJadwal = btn.dataset.jadwalId;
        const jamMulai = btn.dataset.jamMulai;
        const jamSelesai = btn.dataset.jamSelesai;
        const antrean = btn.dataset.antrean;
        const interval = btn.dataset.interval;
        // reset semua tombol
        document.querySelectorAll('.slot-btn[data-jadwal-id]').forEach(b => {
            b.className = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border transition-all duration-200 text-xs font-bold uppercase bg-white border-gray-300 text-gray-800 hover:border-teal-500 hover:bg-teal-50';
        });
        // tomboll yg dipilih berubah jadi hijau
        btn.className = 'slot-btn relative group/btn w-full aspect-[3/2] flex flex-col items-center justify-center rounded-sm border-2 transition-all duration-200 text-xs font-bold uppercase bg-teal-50 border-teal-700 text-teal-900 shadow-sm z-10';

        document.getElementById('id_jadwal').value = idJadwal;
        document.getElementById('jam_reservasi').value = interval;
        document.getElementById('antrean').value = antrean;

        showInfoBox(antrean, jamMulai, jamSelesai); //menampilkan yang trepilih dan menghilangkan hidden
    }
    //menampilkan informasi slot yang dipilih
    function showInfoBox(antrean, jamMulai, jamSelesai) {
        const info = document.getElementById('jadwalInfo'); //amil elemen info
        let timeDisplay = jamMulai; //menentukan tampilan jam
        if (jamSelesai) {
            timeDisplay = `${jamMulai} - ${jamSelesai}`;
        } //mengisi isi box
        info.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-sm bg-teal-700 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                    <div>
                        <div class="text-[9px] uppercase text-teal-700 font-bold tracking-widest">Terpilih</div>
                        <div class="font-bold text-gray-800 text-xs">#${antrean} &bull; ${timeDisplay}</div>
                    </div>
                </div>
            </div>
        `; 
        // menampilkan box
        info.classList.remove('hidden');
    }

    // klik pada seluruh area jadwal
    document.getElementById('jadwalContainer').addEventListener('click', function(e) {
        const btn = e.target.closest('.slot-btn[data-jadwal-id]'); //cari tombol slot yg di klik
        if (!btn || btn.disabled) return; //kalo bukan tombol slot , langsung berhenti

        selectSlot(btn);
    });

    // menampilkan pesan sementara
    function showPlaceholder(html) {
        const container = document.getElementById('jadwalContainer');
        container.innerHTML = `<div class="h-full flex flex-col items-center justify-center min-h-[250px]">${html}</div>`;
        
        document.getElementById('id_jadwal').value = '';
        document.getElementById('jam_reservasi').value = '';
        document.getElementById('antrean').value = '';
        document.getElementById('jadwalInfo').classList.add('hidden');
    }

    // jalankan kode ketika input tanggal berubah
    tanggalInput.addEventListener('change', function() {
        //kalo tanggal terisi panggil, kalo tanggal kosong isi tulisan "pilih tanggal"
        if (this.value) {
            loadJadwal(this.value, true);
        } else {
            showPlaceholder('<div class="flex flex-col items-center gap-2"><div class="w-10 h-10 bg-gray-50 flex items-center justify-center border border-gray-200 rounded-sm"><i class="fas fa-calendar-alt text-gray-300"></i></div><p class="text-gray-500 text-[10px] font-bold uppercase tracking-wider">Pilih tanggal</p></div>');
        }
    });

    // memgambil text area keluhan
    const keluhanInput = document.getElementById('keluhan');
    const charCount = document.getElementById('charCount'); //mengambil tempat jumlah karakter
    const maxChars = 500;
    //memberi attribut maxlength
    keluhanInput.setAttribute('maxlength', maxChars);
    keluhanInput.addEventListener('input', function() {
        const len = this.value.length;
        charCount.textContent = `${len}/${maxChars}`;
        // diatas 90 ubah warna merah, diatas 70 ubah warna kuning
        charCount.className = len >= maxChars * 0.9 ? 'text-[10px] text-red-500 font-bold uppercase tracking-wider' : 
                              len >= maxChars * 0.7 ? 'text-[10px] text-amber-500 font-bold uppercase tracking-wider' : 'text-[10px] text-gray-400 uppercase tracking-wider';
    });
    
    keluhanInput.dispatchEvent(new Event('input'));

    // validasi sebelum submiit
    document.getElementById('reservasiForm').addEventListener('submit', function(e) {
        if (!document.getElementById('pasien_id_hidden').value) { //cek pasien
            e.preventDefault();
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
            return;
        }
        //cek jadwal
        if (!document.getElementById('id_jadwal').value) {
            e.preventDefault(); //kalo slot bleum dipilih batalakan
            Swal.fire({
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
        //kalo sudah benar semua ambil tombol sumit
        const btn = document.getElementById('submitBtn');
        btn.disabled = true; //nonaktifkan tombol, agar user tidak klik 2 kali
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-[10px] mr-1"></i> Menyimpan...';
    });

    //mengambil tanggal yg sudah ada ketika halaman pertama dibuka
    const initialDate = tanggalInput.value;
    if (initialDate) {
        loadJadwal(initialDate, true);
    }
</script>
@endsection