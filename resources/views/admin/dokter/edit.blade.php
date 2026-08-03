@extends('layouts.admin')
@section('header-title', 'Edit Data Dokter')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- menunjukan halaman yg ditampilkan-->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dokter.index') }}" class="hover:text-teal-700 transition-colors font-bold">Dokter</a>
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
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Data Dokter</h1>
                <p class="text-xs text-gray-500 mt-1">Ubah informasi dokter {{ $dokter->nama }}</p>
            </div>
            <a href="{{ route('admin.dokter.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- Form Content -->
        <div class="p-6 lg:p-8"> 
            <!-- mengirimkan data ke method update -->
            <form action="{{ route('admin.dokter.update', $dokter->id_dokter) }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Section Informasi Profil -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Profil</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-10">
                    <div>
                        <!-- nama -->
                        <label for="nama" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $dokter->nama) }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Dr. Andin Ardela, Sp.KG">
                        @error('nama')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- spesialisisasi -->
                    <div>
                        <label for="spesialisasi" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Bidang Spesialisasi <span class="text-red-500">*</span></label>
                        <input type="text" id="spesialisasi" name="spesialisasi" value="{{ old('spesialisasi', $dokter->spesialisasi) }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Ortodonti, Bedah Mulut, Gigi Anak">
                        @error('spesialisasi')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Section Kontak & Akun Login -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Kontak & Akun Login</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                    <div>
                        <!-- no hp -->
                        <label for="no_hp" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nomor HP <span class="text-red-500">*</span></label>
                        <input type="number" id="no_hp" name="no_hp" value="{{ old('no_hp', $dokter->no_hp) }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="0812-3456-7890">
                        @error('no_hp')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- email -->
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Email Login <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $dokter->user->email ?? '') }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="dokter@namaklinik.com">
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Akan digunakan sebagai username login</p>
                        @error('email')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- password -->
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="w-full px-3 py-2.5 pr-10 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Kosongkan jika tidak ingin mengubah">
                            <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-teal-700">
                                <i class="fas fa-eye text-xs" id="toggleIcon"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Kosongkan jika tidak ingin mengubah password</p>
                        @error('password')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- konfirmasi pw -->
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3 py-2.5 pr-10 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Ulangi password baru">
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

                <!-- Section Informasi Akun User (menampilkan) -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2 mt-10">Informasi Akun User</h3>
                <!-- nama user -->
                <div class="mb-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Nama User</p>
                            <p class="text-sm font-medium text-gray-800">{{ $dokter->user->nama ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-800">{{ $dokter->user->email ?? '—' }}</p>
                        </div>
                    </div>
                    
                    @if($dokter->user->email_verified_at)
                    <div class="mt-4 p-3 bg-emerald-50 border border-emerald-200 rounded-sm">
                        <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">Email diverifikasi pada {{ $dokter->user->email_verified_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    @else
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-sm">
                        <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">Email belum diverifikasi</p>
                    </div>
                    @endif
                </div>

                <!-- Action Footer -->
                <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider italic mr-auto hidden sm:block">
                        Perubahan akan disimpan setelah menekan tombol update
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('admin.dokter.index') }}" 
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
// Toggle password ambil elemen html
const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('password');
const toggleIcon = document.getElementById('toggleIcon');

togglePassword?.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    toggleIcon.classList.toggle('fa-eye');
    toggleIcon.classList.toggle('fa-eye-slash');
});
// toogle konfirmasi pssword
const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
const confirmPasswordInput = document.getElementById('password_confirmation');
const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

toggleConfirmPassword?.addEventListener('click', function() {
    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    confirmPasswordInput.setAttribute('type', type);
    toggleConfirmIcon.classList.toggle('fa-eye');
    toggleConfirmIcon.classList.toggle('fa-eye-slash');
});

// Password match 
const matchStatus = document.getElementById('matchStatus');

function checkPasswordMatch() {
    if (!confirmPasswordInput.value) { //membandingkan kedua inptu
        matchStatus.innerHTML = '';
        return;
    }
    // jika cocok centang ijo
    if (passwordInput.value === confirmPasswordInput.value) {
        matchStatus.innerHTML = '<i class="fas fa-check-circle text-emerald-500 mr-1"></i> Kata sandi cocok';
        matchStatus.className = 'mt-1.5 text-[10px] text-emerald-600 font-bold uppercase tracking-wider';
    } else {
        // jika tidak cock menampillkan kata sandi tidak cocok
        matchStatus.innerHTML = '<i class="fas fa-times-circle text-red-400 mr-1"></i> Kata sandi tidak cocok';
        matchStatus.className = 'mt-1.5 text-[10px] text-red-500 font-bold uppercase tracking-wider';
    }
}
//setiap user mengetik fungsi akan jalan
passwordInput?.addEventListener('input', checkPasswordMatch);
confirmPasswordInput?.addEventListener('input', checkPasswordMatch);
</script>
@endsection