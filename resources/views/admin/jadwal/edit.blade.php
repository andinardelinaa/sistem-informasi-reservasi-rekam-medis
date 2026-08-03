@extends('layouts.admin')

@section('header-title', 'Edit Template Jadwal')

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
        max-height: 250px; 
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

    /* Time Input Styling */
    input[type="time"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(20%) sepia(89%) saturate(500%) hue-rotate(155deg) brightness(90%) contrast(90%);
        transition: all 0.3s ease;
    }
    input[type="time"]::-webkit-calendar-picker-indicator:hover {
        transform: scale(1.15);
        filter: invert(25%) sepia(89%) saturate(700%) hue-rotate(155deg) brightness(80%) contrast(90%);
    }
</style>

<div class="max-w-5xl mx-auto space-y-6">
    
    <!--menunjukan halaman yang sedang ditampilkan -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.jadwal.index') }}" class="hover:text-teal-700 transition-colors font-bold">Jadwal</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Template Jadwal</h1>
                <p class="text-xs text-gray-500 mt-1">Perbarui template jadwal praktik dokter</p>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i> Kembali
            </a>
        </div>

        <!-- Form edit -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.jadwal.update', $jadwal->id_jadwal) }}" method="POST" id="jadwalForm">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Klinik -->
                    <div style="overflow: visible;">
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Klinik <span class="text-red-500">*</span></label>
                        <div class="cs-wrap" id="id_klinik_wrap">
                            <button type="button" class="cs-btn">
                                <span class="text-gray-400">-- Pilih Klinik --</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                            </button>
                            <div class="cs-list">
                                @foreach($klinik as $k)
                                <div class="cs-item" data-val="{{ $k->id_klinik }}" data-search="{{ strtolower($k->nama_klinik) }}">{{ $k->nama_klinik }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="id_klinik" id="id_klinik" required value="{{ old('id_klinik', $jadwal->id_klinik) }}">
                        </div>
                        @error('id_klinik') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Dokter -->
                    <div style="overflow: visible;">
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Dokter <span class="text-red-500">*</span></label>
                        <div class="cs-wrap" id="id_dokter_wrap">
                            <button type="button" class="cs-btn">
                                <span class="text-gray-400">-- Pilih Dokter --</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                            </button>
                            <div class="cs-list">
                                <input type="text" class="cs-search" placeholder="Cari nama dokter...">
                                @foreach($dokter as $d)
                                <div class="cs-item" data-val="{{ $d->id_dokter }}" data-search="{{ strtolower($d->nama) }}">{{ $d->nama }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="id_dokter" id="id_dokter" required value="{{ old('id_dokter', $jadwal->id_dokter) }}">
                        </div>
                        @error('id_dokter') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Hari -->
                    <div style="overflow: visible;">
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Hari <span class="text-red-500">*</span></label>
                        <div class="cs-wrap" id="hari_wrap">
                            <button type="button" class="cs-btn">
                                <span class="text-gray-400">-- Pilih Hari --</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                            </button>
                            <div class="cs-list">
                                @foreach(['senin','selasa','rabu','kamis','jumat','sabtu','minggu'] as $h)
                                <div class="cs-item" data-val="{{ $h }}" data-search="{{ strtolower($h) }}">{{ ucfirst($h) }}</div>
                                @endforeach
                            </div>
                            <input type="hidden" name="hari" id="hari" required value="{{ old('hari', $jadwal->hari) }}">
                        </div>
                        @error('hari') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <div class="hidden md:block"></div> <!-- Spacer -->

                    <!-- Jam Mulai -->
                    <div>
                        <label for="jam_mulai" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')) }}" required 
                               class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white hover:border-teal-400 hover:bg-white hover:shadow-sm transition-all duration-300 ease-in-out">
                        @error('jam_mulai') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jam Selesai -->
                    <div>
                        <label for="jam_selesai" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Waktu Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_selesai" id="jam_selesai" value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i')) }}" required 
                               class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-sm text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white hover:border-teal-400 hover:bg-white hover:shadow-sm transition-all duration-300 ease-in-out">
                        @error('jam_selesai') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- aksi -->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.jadwal.index') }}" 
                       class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                            class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center justify-center gap-2">
                        <i class="fas fa-save text-[10px]"></i> Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // dropdown cutom
    function initCS(wrap) {
        if (!wrap) return;
        // ambil elemen html
        const btn = wrap.querySelector('.cs-btn'); //button
        const list = wrap.querySelector('.cs-list'); //list pilihan
        const input = wrap.querySelector('input[type="hidden"]'); //jidden inpit
        //kalo salah satu elemen tidak ada, funtion berhenti
        if (!btn || !list || !input) return;

        // fungsi berjalan ketika tombol di klik
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
        // Search Logic dalam dropdown
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
                const text = item.textContent.trim();

                input.value = val;
                btn.querySelector('span').textContent = text;
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

        // Initialize selected state on load
        if (input.value) {
            const selectedItem = list.querySelector(`.cs-item[data-val="${input.value}"]`);
            if (selectedItem) {
                selectedItem.classList.add('selected');
                btn.querySelector('span').textContent = selectedItem.textContent.trim();
                btn.querySelector('span').classList.remove('text-gray-400');
                btn.querySelector('span').classList.add('text-gray-800');
            }
        }
    }

    // Helper: Filter items dalam cs-list
    function filterCSItems(list, term) {
        const items = list.querySelectorAll('.cs-item'); //mengambil semua elemen
        let hasVisible = false; //loop satu satu
        items.forEach(item => {
            const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
            // kalo cocok item tampilkan kalo tidak semnbuyikan
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
    
    // klik diluar drodown dropdown akan tertutup
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.cs-wrap')) {
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
        }
    });

    // Inisialisasi semua dropdown saat load
    document.querySelectorAll('.cs-wrap').forEach(initCS);

    // fungsi berjalan ketika tombol di submit
    document.getElementById('jadwalForm').addEventListener('submit', function(e) {
        const klinikVal = document.getElementById('id_klinik').value; //ambil id klinik
        const dokterVal = document.getElementById('id_dokter').value; //ambil id dokter
        const hariVal = document.getElementById('hari').value; // ambil hari
        const mulaiVal = document.getElementById('jam_mulai').value; //ambil jam mulai
        const selesaiVal = document.getElementById('jam_selesai').value; //ambil jam selesai

        // Validasi Waktu
        if (mulaiVal && selesaiVal && mulaiVal >= selesaiVal) { //apakah jam selesai lebih kecil dr jam mulai
            e.preventDefault(); //kalau salah form dibatalkan
            Swal.fire({ //popup
                width: 380,
                html: `<div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Waktu Tidak Valid</h2>
                        <p style="font-size:13px; color:#6b7280;">Waktu selesai harus lebih besar dari waktu mulai.</p>
                       </div>`,
                confirmButtonText: 'Mengerti',
                customClass: { 
                    popup: 'rounded-sm p-5', 
                    confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' 
                }
            });
            return;
        }

        // Loading State supaya user tidak menekan tombol 2 kli
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-[10px] mr-1"></i> Memproses...';
    });
</script>
@endsection