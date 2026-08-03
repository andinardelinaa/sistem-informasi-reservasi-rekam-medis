@extends('layouts.admin')

@section('header-title', 'Edit Data Pasien')

@section('content')
<style>
    .cs-wrap { 
        position: relative; 
        z-index: 10; 
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
        border-color: #6b7280; 
        box-shadow: 0 0 0 1px #6b7280; 
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
        max-height: 300px; 
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

    .cs-item { 
        padding: 0.5rem 0.75rem; 
        font-size: 0.875rem; 
        cursor: pointer; 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        transition: background 0.1s; 
        color: #374151;
    }
    
    .cs-item:hover { background: #f0fdfa; color: #0f766e; }
    .cs-item.selected { 
        background: #f0fdfa; 
        color: #0f766e; 
        font-weight: 600; 
    }
</style>

<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- menunjukan sedang berada pada halaman edit pasien-->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.pasien.index') }}" class="hover:text-gray-700 transition-colors font-bold">Pasien</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit Data</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Data Pasien</h1>
                <p class="text-xs text-gray-500 mt-1">Perbarui rekam medis pasien</p>
            </div>
            <a href="{{ route('admin.pasien.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i> Kembali
            </a>
        </div>
        <!-- pesan error -->
        @if(session('error'))
        <div class="px-6 py-3 border-b border-red-200 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-[10px]"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Form -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.pasien.update', $pasien->id_pasien) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Info Akun (Read Only) -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Akun Pengguna</h3>
                @if($pasien->user)
                <div class="mb-6 p-3 bg-teal-50 border border-teal-200 rounded-sm text-teal-800 flex items-center gap-2">
                    <i class="fas fa-link text-[10px]"></i>
                    <p class="text-[10px] font-bold uppercase tracking-wider">
                        Terhubung ke: {{ $pasien->user->nama }} ({{ $pasien->user->email }})
                    </p>
                </div>
                @else
                <div class="mb-6 p-3 bg-gray-50 border border-gray-200 rounded-sm text-gray-500 flex items-center gap-2">
                    <i class="fas fa-unlink text-[10px]"></i>
                    <p class="text-[10px] font-bold uppercase tracking-wider">Belum memiliki akun pengguna</p>
                </div>
                @endif

                <!-- Section Data Pribadi -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Data Pribadi Pasien</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                    <div class="space-y-6">
                        <div>
                            <!-- nama -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $pasien->nama) }}" required 
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors">
                            @error('nama')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <!-- nik -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" maxlength="16" value="{{ old('nik', $pasien->nik) }}" required 
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors font-mono tracking-wider">
                            @error('nik')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4" style="overflow: visible;">
                            <!-- Custom Gender Dropdown -->
                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Gender <span class="text-red-500">*</span></label>
                                <div class="cs-wrap" id="gender_wrap">
                                    <button type="button" class="cs-btn">
                                        <span class="text-gray-400">-- Pilih Gender --</span>
                                        <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                                    </button>
                                    <div class="cs-list">
                                        <div class="cs-item" data-val="L">
                                            <span class="font-bold text-xs uppercase">Laki-laki</span>
                                        </div>
                                        <div class="cs-item" data-val="P">
                                            <span class="font-bold text-xs uppercase">Perempuan</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="jns_kelamin" id="jns_kelamin" value="{{ old('jns_kelamin', $pasien->jns_kelamin) }}" required>
                                </div>
                                @error('jns_kelamin')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Tgl Lahir -->
                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tgl Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tgl_lahir" 
                                       value="{{ old('tgl_lahir', $pasien->tgl_lahir ? \Carbon\Carbon::parse($pasien->tgl_lahir)->format('Y-m-d') : '') }}" 
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors">
                                @error('tgl_lahir')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <!-- no hp -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                            <input type="text" name="no_hp" value="{{ old('no_hp', $pasien->no_hp) }}" required 
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="0812...">
                            @error('no_hp')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <!-- pekerjaan -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pekerjaan</label>
                            <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $pasien->pekerjaan) }}" 
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Opsional">
                            @error('pekerjaan')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <!-- alamat -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat" rows="3" required 
                                      class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400">{{ old('alamat', $pasien->alamat) }}</textarea>
                            @error('alamat')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Riwayat Penyakit</label>
                            <textarea name="riwayat_penyakit" rows="3" 
                                    class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400">{{ old('riwayat_penyakit', $pasien->riwayat_penyakit) }}</textarea>
                            @error('riwayat_penyakit')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- aksi-->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.pasien.index') }}" 
                           class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            Perbarui Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    //custom dropdown
    function initCS(wrap) {
        //ambil elemen html
        const btn = wrap.querySelector('.cs-btn'); //tombol dropdown
        const list = wrap.querySelector('.cs-list'); //pilihan dropdown
        const input = wrap.querySelector('input[type="hidden"]'); //tempat simpan value 
        //memastikan semua elemen ada
        if (!btn || !list || !input) return;

        // Toggle Dropdown di klik
        btn.addEventListener('click', (e) => {
            e.stopPropagation(); //menghentikan event
            if (btn.disabled) return;
            
            const isOpen = list.classList.contains('active');
            closeAllDropdowns(); // Tutup lain dulu
            //membuka dropdown
            if (!isOpen) {
                list.classList.add('active');
                btn.classList.add('active');
            }
        });

        // semua item dropdown akan diberi event klik
        list.querySelectorAll('.cs-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const val = item.dataset.val; //ambil value
                const text = item.textContent.trim(); 

                input.value = val;
                btn.querySelector('span').textContent = text;
                btn.querySelector('span').classList.remove('text-gray-400');
                
                list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                
                closeAllDropdowns();
            });
        });
    }
    //setelah memilih dropdown langsung tutup, menghapus semua class aktif
    function closeAllDropdowns() {
        document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
        document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
    }
    //saat klik luar dropdoown tertutup
    document.addEventListener('click', () => {
        if (!event.target.closest('.cs-wrap')) {
            closeAllDropdowns();
        }
    });

    // Inisialisasi Semua Custom Select
    document.querySelectorAll('.cs-wrap').forEach(initCS);
    // ambil input jenis kelamin
    const genderInput = document.getElementById('jns_kelamin');
    const genderWrap = document.getElementById('gender_wrap');
    //mengecek apakah ada nilai, maka sistem akan menampilkan
    if (genderInput && genderWrap && genderInput.value) {
        const val = genderInput.value;
        //menentukan teks
        const text = val === 'L' ? 'Laki-laki' : 'Perempuan';
        
        // Update tampilan tombol
        const btnSpan = genderWrap.querySelector('.cs-btn span');
        if(btnSpan) {
            btnSpan.textContent = text;
            btnSpan.classList.remove('text-gray-400');
        }
        //pilihan yang sesuai akan diberi tand select
        const item = genderWrap.querySelector(`.cs-item[data-val="${val}"]`);
        if(item) item.classList.add('selected');
    }

    // Validasi NIK
    const nikInput = document.querySelector('input[name="nik"]');
    nikInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validasi Nomor HP
    const noHpInput = document.querySelector('input[name="no_hp"]');
    noHpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, '');
    });
});
</script>
@endsection