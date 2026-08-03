@extends('layouts.admin')
@section('header-title', 'Tambah Data Pasien')

@section('content')
<style>
    .cs-wrap { position: relative; z-index: 9999; }
    .cs-wrap:has(.cs-list.active) { z-index: 99999; }
    .cs-btn { 
        width: 100%; display: flex; align-items: center; justify-content: space-between; 
        padding: 0.625rem 0.75rem; background: #f9fafb; border: 1px solid #d1d5db; 
        border-radius: 2px; font-size: 0.875rem; text-align: left; cursor: pointer; 
        transition: all 0.15s; color: #1f2937; user-select: none;
    }
    .cs-btn:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
    .cs-btn:not(:disabled):hover { border-color: #9ca3af; background: #fff; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
    .cs-btn.active { border-color: #6b7280; box-shadow: 0 0 0 1px #6b7280; background: #fff; }
    .cs-btn.active i { transform: rotate(180deg); }
    .cs-list { 
        position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; 
        border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
        z-index: 9999; max-height: 250px; overflow-y: auto; opacity: 0; visibility: hidden; 
        transform: translateY(-5px); transition: all 0.15s ease; pointer-events: none;
    }
    .cs-list.active { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
    .cs-list::-webkit-scrollbar { width: 4px; }
    .cs-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .cs-search {
        width: 100%; padding: 8px 12px; border: none; border-bottom: 1px solid #e5e7eb;
        font-size: 0.8rem; outline: none; position: sticky; top: 0; background: #fff; z-index: 10;
    }
    .cs-search:focus { background: #f9fafb; }
    .cs-item { 
        padding: 0.5rem 0.75rem; font-size: 0.875rem; cursor: pointer; display: flex; 
        align-items: center; justify-content: space-between; transition: background 0.1s; color: #374151;
    }
    .cs-item:hover { background: #f0fdfa; color: #0f766e; }
    .cs-item.selected { background: #f0fdfa; color: #0f766e; font-weight: 600; }
    .radio-card { 
        flex: 1; border: 1px solid #d1d5db; border-radius: 4px; padding: 0.875rem 1rem; 
        cursor: pointer; transition: all 0.15s; background: #fff; display: flex; align-items: center; gap: 0.75rem;
    }
    .radio-card:hover { border-color: #0f766e; background: #f0fdfa; }
    .radio-card.active { border-color: #6b7280; background: #f9fafb; box-shadow: 0 0 0 1px #6b7280; }
    
    .panel-section { display: none; }
    .panel-section.active { display: block; }
</style>

<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- menunjukan halaman saat ini -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.pasien.index') }}" class="hover:text-gray-700 transition-colors font-bold">Pasien</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Tambah Data</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Tambah Data Pasien Baru</h1>
                <p class="text-xs text-gray-500 mt-1">Isi formulir untuk mendaftarkan pasien dan akun pengguna</p>
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
            <form action="{{ route('admin.pasien.store') }}" method="POST" id="pasienForm">
                @csrf
                <input type="hidden" name="create_user" id="create_user" value="{{ old('create_user', '0') }}">

                <!-- Section Akun Pengguna -->
                <div class="border border-gray-200 rounded-sm mb-8">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Akun Pengguna Pasien</h3>
                    </div>
                    
                    <div class="p-5 bg-white">
                        <!-- Card Selection -->
                        <div class="flex flex-col sm:flex-row gap-4 mb-6 w-full">
                            <!-- user terdaftar -->
                            <label class="radio-card {{ old('create_user', '0') == '0' ? 'active' : '' }}" id="label_user_terdaftar" onclick="switchAccountType('0')">
                                <input type="radio" name="tipe_akun_selector" value="0" {{ old('create_user', '0') == '0' ? 'checked' : '' }} class="h-4 w-4 text-gray-600 focus:ring-gray-400 shrink-0">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 uppercase tracking-wider">Pilih User Terdaftar</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider">Hubungkan ke akun yang sudah ada</span>
                                </div>
                            </label>
                            <!-- user baru -->
                            <label class="radio-card {{ old('create_user', '1') == '1' ? 'active' : '' }}" id="label_user_baru" onclick="switchAccountType('1')">
                                <input type="radio" name="tipe_akun_selector" value="1" {{ old('create_user', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 text-gray-600 focus:ring-gray-400 shrink-0">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 uppercase tracking-wider">Buat Akun Baru</span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider">Buatkan akun baru untuk pasien</span>
                                </div>
                            </label>
                        </div>

                        <!-- Panel User Terdaftar -->
                        <div id="panel-terdaftar" class="panel-section {{ old('create_user', '0') == '0' ? 'active' : '' }}">
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Cari & Pilih User <span class="text-red-500">*</span></label>
                            <div class="cs-wrap" id="id_user_wrap">
                                <!-- dropdown cari user -->
                                <button type="button" class="cs-btn">
                                    <span class="text-gray-400">-- Cari Nama / Email User --</span>
                                    <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                                </button>
                                <div class="cs-list">
                                    <input type="text" class="cs-search" placeholder="Cari nama atau email...">
                                    @forelse($users as $u)
                                    <div class="cs-item" data-val="{{ $u->id }}" data-search="{{ strtolower($u->nama . ' ' . $u->email) }}">
                                        <span class="font-bold text-xs uppercase">{{ $u->nama }}</span>
                                        <span class="text-gray-500 text-[11px] ml-2">({{ $u->email }})</span>
                                    </div>
                                    @empty
                                    <div class="px-3 py-2 text-xs text-gray-400 italic">Tidak ada user tersedia</div>
                                    @endforelse
                                </div>
                                <input type="hidden" name="id_user" id="id_user" value="{{ old('id_user') }}">
                            </div>
                            @error('id_user')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Panel Akun Baru -->
                        <div id="panel-baru" class="panel-section {{ old('create_user', '1') == '1' ? 'active' : '' }}">
                            <div class="p-4 bg-gray-50 border border-gray-200 rounded-sm space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <!-- input email -->
                                    <input type="email" name="user_email" value="{{ old('user_email') }}" 
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors" 
                                           placeholder="email@contoh.com">
                                    @error('user_email')
                                        <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- input password -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="user_password" 
                                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors" 
                                               placeholder="Min. 8 karakter">
                                        @error('user_password')
                                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <!-- konfirmasi password -->
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                                        <input type="password" name="user_password_confirmation" 
                                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors" 
                                               placeholder="Ulangi password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section Data Pribadi -->
                <div class="border border-gray-200 rounded-sm">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 rounded-t-sm">
                        <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Data Pribadi Pasien</h3>
                    </div>
                    
                    <div class="p-5 bg-white">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-6">
                            <div class="space-y-6">
                                <div>
                                    <!-- nama lengkap -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama" value="{{ old('nama') }}" required 
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors">
                                    @error('nama') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <!-- nik -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                                    <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}" required 
                                           class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors font-mono tracking-wider" 
                                           placeholder="Masukkan 16 digit NIK">
                                    @error('nik') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                </div>
                                <!-- Gender & Tgl Lahir -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Gender <span class="text-red-500">*</span></label>
                                        <div class="cs-wrap" id="gender_wrap">
                                            <button type="button" class="cs-btn">
                                                <span class="text-gray-400">-- Pilih --</span>
                                                <i class="fas fa-chevron-down text-[9px] text-gray-400 ml-2"></i>
                                            </button>
                                            <div class="cs-list">
                                                <div class="cs-item" data-val="L">Laki-laki</div>
                                                <div class="cs-item" data-val="P">Perempuan</div>
                                            </div>
                                            <input type="hidden" name="jns_kelamin" id="jns_kelamin" value="{{ old('jns_kelamin') }}" required>
                                        </div>
                                        @error('jns_kelamin') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tgl Lahir <span class="text-red-500">*</span></label>
                                        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir') }}" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors">
                                        @error('tgl_lahir') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div>
                                    <!-- no hp -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" required class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" placeholder="0812...">
                                    @error('no_hp') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <!-- pekerjaan -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pekerjaan</label>
                                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" placeholder="Opsional">
                                    @error('pekerjaan') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                <!-- alamat -->
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                                    <textarea name="alamat" rows="3" required class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                    @error('alamat') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Riwayat Penyakit</label>
                                    <textarea name="riwayat_penyakit" rows="3" 
                                            class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-gray-400 focus:border-gray-500 text-sm transition-colors placeholder:text-gray-400" 
                                            placeholder="Contoh: Diabetes, Hipertensi, Alergi obat, dll.">{{ old('riwayat_penyakit') }}</textarea>
                                    @error('riwayat_penyakit')
                                        <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                    @enderror
                                </div>                                
                            </div>
                        </div>
                    </div>
                </div>
                <!-- button aksi -->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.pasien.index') }}" 
                           class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            Simpan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    //dropdown custom
    function initCS(wrap) {
        if (!wrap) return;
        // ambil elemen html
        const btn = wrap.querySelector('.cs-btn'); //tombol dropdown
        const list = wrap.querySelector('.cs-list'); //isi list
        const input = wrap.querySelector('input[type="hidden"]'); 
        if (!btn || !list || !input) return;
        //klik tombol dropdown, jalankan fungsi
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (btn.disabled) return;
            const isOpen = list.classList.contains('active');
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
            if (!isOpen) { list.classList.add('active'); btn.classList.add('active'); }
        });

        // Search Logic dalam dropdown
        const searchInput = wrap.querySelector('.cs-search');
        if (searchInput) {
            searchInput.addEventListener('click', (e) => e.stopPropagation());
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                list.querySelectorAll('.cs-item').forEach(item => {
                    const text = item.getAttribute('data-search') || item.textContent.toLowerCase();
                    // user mengetik, filter realtime
                    item.style.display = text.includes(term) ? 'flex' : 'none';
                });
            });
        }

        list.querySelectorAll('.cs-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                // ambil value
                const val = item.dataset.val;
                const text = item.textContent.trim();
                input.value = val; //simpan ke hidden input
                btn.querySelector('span').textContent = text;
                btn.querySelector('span').classList.remove('text-gray-400');
                list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                item.classList.add('selected');
                list.classList.remove('active'); //tutup dropdown
                btn.classList.remove('active');
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }
    // kalo klik diluar dropdown tertutup
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.cs-wrap')) {
            document.querySelectorAll('.cs-list').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.cs-btn').forEach(b => b.classList.remove('active'));
        }
    });

    // semua dropdown custom di halaman langsung aktif
    document.querySelectorAll('.cs-wrap').forEach(initCS);
    // cari item yang sesuai value daatabase
    const userInput = document.getElementById('id_user');
    const userWrap = document.getElementById('id_user_wrap');
    if (userInput && userWrap && userInput.value) {
        const val = userInput.value;
        const item = userWrap.querySelector(`.cs-item[data-val="${val}"]`);
        if(item) {
            const mainText = item.querySelector('.font-bold').textContent.trim();
            const btnSpan = userWrap.querySelector('.cs-btn span');
            if(btnSpan) { btnSpan.textContent = mainText; btnSpan.classList.remove('text-gray-400'); }
            item.classList.add('selected');
        }
    }

    // Set Gender Dropdown
    const genderInput = document.getElementById('jns_kelamin');
    const genderWrap = document.getElementById('gender_wrap');
    if (genderInput && genderWrap && genderInput.value) {
        const val = genderInput.value;
        const item = genderWrap.querySelector(`.cs-item[data-val="${val}"]`);
        if(item) {
            const text = item.textContent.trim();
            const btnSpan = genderWrap.querySelector('.cs-btn span');
            if(btnSpan) { btnSpan.textContent = text; btnSpan.classList.remove('text-gray-400'); }
            item.classList.add('selected');
        }
    }
    //global function
    window.switchAccountType = function(type) {
        document.getElementById('create_user').value = type; //0 user terdafrat, 1 user baru
        //jika tredaftar panel terdaftar juka baru panel baru
        const cardTerdaftar = document.getElementById('label_user_terdaftar');
        const cardBaru = document.getElementById('label_user_baru');
        
        cardTerdaftar.classList.remove('active');
        cardBaru.classList.remove('active');

        if(type === '0') {
            cardTerdaftar.classList.add('active');
            document.getElementById('panel-terdaftar').classList.add('active');
            document.getElementById('panel-baru').classList.remove('active');
        } else {
            cardBaru.classList.add('active');
            document.getElementById('panel-terdaftar').classList.remove('active');
            document.getElementById('panel-baru').classList.add('active');
        }
    }

    //clean input sebelum submit
    document.getElementById('pasienForm').addEventListener('submit', function(e) {
        const createUserVal = document.getElementById('create_user').value;
        
        if (createUserVal === '0') {
            // Jika Pilih User Terdaftar, kosongkan input akun baru agar validasi Laravel di-skip
            document.querySelector('input[name="user_email"]').value = '';
            document.querySelector('input[name="user_password"]').value = '';
            document.querySelector('input[name="user_password_confirmation"]').value = '';
        } else {
            // Jika Buat Akun Baru, kosongkan id_user agar validasi Laravel di-skip
            document.getElementById('id_user').value = '';
        }
    });

    // nik hanya angka
    const nikInput = document.querySelector('input[name="nik"]');
    if(nikInput) { nikInput.addEventListener('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); }); }
    // no hp boleh + dan angka
    const noHpInput = document.querySelector('input[name="no_hp"]');
    if(noHpInput) { noHpInput.addEventListener('input', function() { this.value = this.value.replace(/[^0-9+]/g, ''); }); }
});
</script>
@endsection