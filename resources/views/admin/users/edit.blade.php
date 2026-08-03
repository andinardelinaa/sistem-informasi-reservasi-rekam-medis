@extends('layouts.admin')
@section('header-title', 'Edit User')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- menunjukan halaman saat ini -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.users.index') }}" class="hover:text-teal-700 transition-colors font-bold">Pengguna</a>
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
        <!-- Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit User</h1>
                <p class="text-xs text-gray-500 mt-1">Perbarui informasi akun user</p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" id="editUserForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Informasi Dasar -->
                    <div class="bg-white border border-gray-200 overflow-hidden rounded-sm">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                            <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Informasi Dasar</h3>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <!-- nama -->
                                <label for="nama" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" 
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                       required placeholder="Masukkan nama lengkap">
                                @error('nama')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <!-- email -->
                                <label for="email" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                       class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                       required placeholder="nama@example.com">
                                @error('email')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Kata Sandi -->
                    <div class="bg-white border border-gray-200 overflow-hidden rounded-sm">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                            <div>
                                <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Kata Sandi</h3>
                                <p class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">Kosongkan jika tidak ingin mengubah</p>
                            </div>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <label for="password" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Kata Sandi Baru</label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" 
                                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                           placeholder="Minimal 8 karakter">
                                    <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                                    </button>
                                </div>
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-[10px] text-gray-500 mb-1.5 font-bold uppercase tracking-wider">
                                        <span>Kekuatan</span>
                                        <span id="strengthText" class="font-bold">-</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-sm h-1.5 overflow-hidden">
                                        <div id="strengthBar" class="h-full rounded-sm transition-all duration-500 ease-out" style="width: 0%"></div>
                                    </div>
                                </div>
                                @error('password')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <!-- konfirmasi pw -->
                                <label for="password_confirmation" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Konfirmasi Kata Sandi</label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="w-full px-3 py-2.5 pr-10 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                           placeholder="Ulangi kata sandi">
                                    <button type="button" id="toggleConfirmPassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye text-xs" id="toggleConfirmIcon"></i>
                                    </button>
                                </div>
                                <div id="matchStatus" class="mt-1.5 text-[10px] font-bold uppercase tracking-wider"></div>
                                @error('password_confirmation')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="bg-white border border-gray-200 overflow-hidden rounded-sm">
                        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                            <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Role & Status</h3>
                        </div>
                        <div class="p-5 space-y-5">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-3">Role Pengguna</label>
                                <div class="space-y-2">
                                    @foreach(['admin', 'dokter', 'pasien'] as $role)
                                    <label class="flex items-center cursor-pointer p-2.5 bg-white border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="role" value="{{ $role }}" 
                                               class="mr-3 text-teal-700 focus:ring-teal-500" 
                                               {{ old('role', $user->roles->first()->name ?? '') == $role ? 'checked' : '' }}>
                                        <div>
                                            <div class="font-bold text-gray-800 text-xs uppercase tracking-wider">{{ $role }}</div>
                                            <div class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">
                                                @if($role == 'admin')
                                                    Akses penuh ke sistem
                                                @elseif($role == 'dokter')
                                                    Kelola rekam medis pasien
                                                @else
                                                    Akses terbatas untuk pasien
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                                @error('role')
                                    <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <!-- verifikasi -->
                                <label class="flex items-center cursor-pointer p-2.5 bg-white border border-gray-300 rounded-sm hover:bg-gray-50 transition-colors">
                                    <input type="checkbox" name="email_verified" value="1" 
                                           class="mr-3 text-teal-700 focus:ring-teal-500 rounded-sm" 
                                           {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }}>
                                    <div>
                                        <div class="font-bold text-gray-800 text-xs uppercase tracking-wider">Email Terverifikasi</div>
                                        <div class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">Lewati proses verifikasi email</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- aktivitas akun -->
                <div class="bg-white border border-gray-200 overflow-hidden rounded-sm mb-8">
                    <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                        <h3 class="text-[10px] font-bold text-gray-800 uppercase tracking-widest">Aktivitas Akun</h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Tanggal Dibuat</p>
                                <p class="text-sm font-medium text-gray-800">{{ optional($user->created_at)->format('d M Y, H:i') ?? '-' }} WIB</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Terakhir Diperbarui</p>
                                <p class="text-sm font-medium text-gray-800">{{ optional($user->updated_at)->format('d M Y, H:i') ?? '-' }} WIB</p>
                            </div>
                        </div>
                        <!-- status verifikasi -->
                        @if($user->email_verified_at)
                        <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-sm">
                            <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Email diverifikasi pada {{ optional($user->email_verified_at)->format('d M Y, H:i') ?? '-' }} WIB</p>
                        </div>
                        @else
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-sm">
                            <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">Email belum diverifikasi</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Aksi-->
                <div class="bg-white border border-gray-200 px-6 py-4 rounded-sm">
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider italic mr-auto hidden sm:block">
                            Perubahan akan disimpan setelah menekan tombol update
                        </div>
                        <div class="flex gap-3 w-full sm:w-auto">
                            <a href="{{ route('admin.users.index') }}" 
                               class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                                Batal
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                                Perbarui Data
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // tombol lihat password
    const togglePassword = document.getElementById('togglePassword'); //tombol pw
    const passwordInput = document.getElementById('password'); //ambil inputan pw
    const toggleIcon = document.getElementById('toggleIcon'); //ikon
    //jalankan fungsi apabila di klik
    togglePassword.addEventListener('click', function() {
        //ambil inputan password
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type); //ubah tipe inputan
        toggleIcon.classList.toggle('fa-eye'); //ganti ikon tutup buka
        toggleIcon.classList.toggle('fa-eye-slash');
    });
    
    //tombol konfirmasi pw
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword'); //tombok konfirm
    const confirmPasswordInput = document.getElementById('password_confirmation'); //inputan konfirm pw
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon'); //ikon konfirm
    //jalankan fungsi ketika di klik
    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type); //ubah tipe inputan
        toggleConfirmIcon.classList.toggle('fa-eye'); //buka tutup ikon
        toggleConfirmIcon.classList.toggle('fa-eye-slash');
    });
    
    // mengecek kekuatan pw
    const strengthBar = document.getElementById('strengthBar'); //ambil kekuatan indikator batang
    const strengthText = document.getElementById('strengthText'); //kekuatan pw
    const matchStatus = document.getElementById('matchStatus'); //kecocokan
    // fungsi cek kekuatam
    function checkPasswordStrength() {
        const value = passwordInput.value; //ambil inputan pw
        //jika tidak ada isinya,
        if (value === '') {
            strengthBar.style.width = '0%'; // kekuatan 0
            strengthText.textContent = '-'; // -
            strengthText.className = 'font-bold';
            matchStatus.textContent = '';
            return;
        }
        
        let strength = 0;
        //perhtungan
        if (value.length >= 8) strength += 25; //lebih dari 8 karakter point 25
        if (value.length >= 12) strength += 25; //lebih dr 12 karakter point 25
        if (/[A-Z]/.test(value) && /[a-z]/.test(value)) strength += 25; // jika terdapat huruf besar dan kecil + 25
        if (/[0-9]/.test(value)) strength += 12.5; //terdapat angka +12.5
        if (/[^A-Za-z0-9]/.test(value)) strength += 12.5; //terdapat simbol +12.5
        
        strengthBar.style.width = strength + '%';
        //indikator
        if (strength <= 25) {
            strengthBar.className = 'h-full rounded-sm transition-all duration-500 ease-out bg-red-400';
            strengthText.textContent = 'Lemah';
            strengthText.className = 'font-bold text-red-500 text-[10px] uppercase tracking-wider';
        } else if (strength <= 50) {
            strengthBar.className = 'h-full rounded-sm transition-all duration-500 ease-out bg-amber-400';
            strengthText.textContent = 'Sedang';
            strengthText.className = 'font-bold text-amber-600 text-[10px] uppercase tracking-wider';
        } else if (strength <= 75) {
            strengthBar.className = 'h-full rounded-sm transition-all duration-500 ease-out bg-teal-400';
            strengthText.textContent = 'Kuat';
            strengthText.className = 'font-bold text-teal-600 text-[10px] uppercase tracking-wider';
        } else {
            strengthBar.className = 'h-full rounded-sm transition-all duration-500 ease-out bg-emerald-500';
            strengthText.textContent = 'Sangat Kuat';
            strengthText.className = 'font-bold text-emerald-600 text-[10px] uppercase tracking-wider';
        }
        //ce kecocokan
        checkPasswordMatch();
    }
    //cek kecocokan
    function checkPasswordMatch() {
        if (confirmPasswordInput.value === '') {
            matchStatus.textContent = '';
            return;
        }
        //jika cocok
        if (passwordInput.value === confirmPasswordInput.value) {
            matchStatus.innerHTML = '<i class="fas fa-check-circle text-emerald-500 mr-1"></i> Kata sandi cocok';
            matchStatus.className = 'mt-1.5 text-[10px] text-emerald-600 font-bold uppercase tracking-wider';
        } else { //jika tidak cocok
            matchStatus.innerHTML = '<i class="fas fa-times-circle text-red-400 mr-1"></i> Kata sandi tidak cocok';
            matchStatus.className = 'mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-wider';
        }
    }
    //fungsi dijalankan real time
    passwordInput.addEventListener('input', checkPasswordStrength);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
</script>
@endsection