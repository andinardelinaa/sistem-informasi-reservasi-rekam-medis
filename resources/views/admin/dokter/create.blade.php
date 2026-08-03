@extends('layouts.admin')
@section('header-title', 'Tambah Dokter Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- posisi halaman yang sedang dibuka -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dokter.index') }}" class="hover:text-teal-700 transition-colors font-bold">Dokter</a>
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
        <!-- Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Tambah Dokter Baru</h1>
                <p class="text-xs text-gray-500 mt-1">Lengkapi informasi profil dan kredensial akun dokter</p>
            </div>
            <a href="{{ route('admin.dokter.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- form input-->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.dokter.store') }}" method="POST">
                @csrf
                <!-- Section Informasi Profil -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Profil</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-10">
                    <div>
                        <label for="nama" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Dr. Andin Ardelina, Sp.KG">
                        @error('nama')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="spesialisasi" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Bidang Spesialisasi <span class="text-red-500">*</span></label>
                        <input type="text" id="spesialisasi" name="spesialisasi" value="{{ old('spesialisasi') }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Ortodonti, Bedah Mulut, Gigi Anak">
                        @error('spesialisasi')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section Kontak & Akun Login -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Kontak & Akun Login</h3>
                <!-- no hp -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                    <div>
                        <label for="no_hp" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                        <input type="number" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="0812-3456-7890">
                        @error('no_hp')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- email -->
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Email Login <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="dokter@namaklinik.com">
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Akan digunakan sebagai username login</p>
                        @error('email')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- buat password -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                   class="w-full px-3 py-2.5 pr-10 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Minimal 8 karakter">
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-700">
                                <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="w-full px-3 py-2.5 pr-10 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Ulangi password">
                            <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-700">
                                <i class="fas fa-eye text-xs" id="toggleConfirmIcon"></i>
                            </button>
                        </div>
                        <div id="matchStatus" class="mt-1.5 text-[10px] font-bold uppercase tracking-wider h-4"></div>
                        @error('password_confirmation')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.dokter.index') }}" 
                       class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle Password
    const togglePassword = document.getElementById('togglePassword'); //ambil mata password
    const passwordInput = document.getElementById('password'); //ambil input password
    const toggleIcon = document.getElementById('toggleIcon'); // ambil ikon mata
    
    togglePassword?.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    });
    // toogle konfirmasi password
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');
    
    toggleConfirmPassword?.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        toggleConfirmIcon.classList.toggle('fa-eye');
        toggleConfirmIcon.classList.toggle('fa-eye-slash');
    });

    // Password Match
    const matchStatus = document.getElementById('matchStatus');

    function checkPasswordMatch() {
        if (!confirmPasswordInput.value) {
            matchStatus.innerHTML = '';
            return;
        }

        if (passwordInput.value === confirmPasswordInput.value) {
            matchStatus.innerHTML = '<i class="fas fa-check-circle text-emerald-500 mr-1"></i> Kata sandi cocok';
            matchStatus.className = 'mt-1.5 text-[10px] text-emerald-600 font-bold uppercase tracking-wider';
        } else {
            matchStatus.innerHTML = '<i class="fas fa-times-circle text-red-400 mr-1"></i> Kata sandi tidak cocok';
            matchStatus.className = 'mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-wider';
        }
    }
    passwordInput?.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput?.addEventListener('input', checkPasswordMatch);
</script>
@endsection