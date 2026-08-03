@extends('layouts.pasien')
@section('header-title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 px-4 sm:px-0">
    
    <!-- Main Card Container -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Card Header -->
        <div class="bg-teal-700 px-6 py-5 text-white">
            <h2 class="text-lg font-bold uppercase tracking-wider">Informasi Profil</h2>
            <p class="text-sm text-teal-100 mt-1 font-light">Data akun dan identitas pasien</p>
        </div>

        <div class="p-6 lg:p-8">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Foto Profil & Identitas Dasar -->
                <div class="flex-shrink-0 text-center md:text-left border-b md:border-b-0 md:border-r border-gray-200 pb-6 md:pb-0 md:pr-8">
                    <div class="inline-block">
                        <!-- ambil huruf depan untuk profil -->
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama) }}&size=150&background=0f766e&color=fff&bold=true&font-size=0.4"
                             alt="Profile Photo" 
                             class="w-28 h-28 rounded-sm mx-auto md:mx-0 object-cover border-2 border-gray-200 shadow-sm">
                    </div>
                    <div class="mt-4">
                        <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">{{ auth()->user()->nama }}</h3>
                        <p class="text-sm text-teal-700 font-semibold mt-1">Pasien</p>
                    </div>
                </div>

                <!-- Informasi Detail -->
                <div class="flex-1">
                    <div class="divide-y divide-gray-100 border-t border-gray-100">
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- email -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Email</span>
                            <span class="text-sm text-gray-900 font-medium text-right">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- nik -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">NIK</span>
                            <span class="text-sm text-gray-900 font-mono tracking-wider">{{ auth()->user()->pasien?->nik ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- tgl lahir -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Tgl Lahir</span>
                            <span class="text-sm text-gray-900 font-medium">
                                @if(auth()->user()->pasien?->tgl_lahir)
                                    {{ \Carbon\Carbon::parse(auth()->user()->pasien->tgl_lahir)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- jenis kelamin -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Jenis Kelamin</span>
                            <span class="text-sm text-gray-900 font-medium">
                                @if(auth()->user()->pasien?->jns_kelamin == 'L') Laki-laki
                                @elseif(auth()->user()->pasien?->jns_kelamin == 'P') Perempuan
                                @else -
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- no hp -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Nomor HP</span>
                            <span class="text-sm text-gray-900 font-medium">{{ auth()->user()->pasien?->no_hp ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- pekerjaan -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Pekerjaan</span>
                            <span class="text-sm text-gray-900 font-medium">{{ auth()->user()->pasien?->pekerjaan ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- alamat -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Alamat</span>
                            <span class="text-sm text-gray-900 font-medium text-right max-w-[200px] sm:max-w-xs truncate">{{ auth()->user()->pasien?->alamat ?? '-' }}</span>
                        </div>
                        <div class="flex items-start justify-between py-3 px-1">
                            <!-- riwayat penyakit -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mt-0.5">Riwayat Penyakit</span>
                            <span class="text-sm text-gray-900 font-medium text-right max-w-[200px] sm:max-w-xs">{{ auth()->user()->pasien?->riwayat_penyakit ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-1">
                            <!-- pw -->
                            <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Password</span>
                            <span class="text-sm text-gray-900 font-medium tracking-widest">••••••••</span>
                        </div>
                    </div>
                    <!-- open modal -->
                    <div class="mt-6 flex justify-end">
                        <button onclick="openEditModal()" 
                                class="inline-flex items-center px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-sm font-bold transition-colors uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Ubah Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil-->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-sm shadow-xl max-w-2xl w-full mx-auto my-8 overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Edit Data Diri</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi lengkap Anda</p>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('pasien.profil.update') }}" method="POST" id="profilForm">
            @csrf
            @method('PUT')
            
            <!-- Agar modal bisa di-scroll di mobile jika form panjang -->
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                <!-- Section Informasi Pribadi -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200 pb-2">Informasi Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <!-- nama -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', auth()->user()->nama
                        ) }}" required
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                        @error('nama') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <!-- email tidak bisa diubah -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" disabled
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                        @error('email') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <!-- nik -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" maxlength="16" value="{{ old('nik', auth()->user()->pasien?->nik ?? '') }}" required
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-mono tracking-wider transition-colors" id="inputNik">
                        @error('nik') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <!-- tgl lahir -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', auth()->user()->pasien?->tgl_lahir ? \Carbon\Carbon::parse(auth()->user()->pasien->tgl_lahir)->format('Y-m-d') : '') }}" required
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                        @error('tgl_lahir') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <!-- jenis kelamin -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <div class="flex gap-6 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jns_kelamin" value="L" {{ old('jns_kelamin', auth()->user()->pasien?->jns_kelamin) == 'L' ? 'checked' : '' }} required
                                       class="text-teal-700 focus:ring-teal-500">
                                <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Laki-laki</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="jns_kelamin" value="P" {{ old('jns_kelamin', auth()->user()->pasien?->jns_kelamin) == 'P' ? 'checked' : '' }}
                                       class="text-teal-700 focus:ring-teal-500">
                                <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Perempuan</span>
                            </label>
                        </div>
                        @error('jns_kelamin') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Section Kontak & Alamat -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200 pb-2 pt-2">Kontak & Alamat</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <!-- no hp -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nomor Handphone <span class="text-red-500">*</span></label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp', auth()->user()->pasien?->no_hp ?? '') }}" required id="inputNoHp"
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                        @error('no_hp') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <!-- pekerjaan -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Pekerjaan</label>
                        <input type="text" name="pekerjaan" value="{{ old('pekerjaan', auth()->user()->pasien?->pekerjaan ?? '') }}"
                               class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <!-- alamat -->
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="2" required
                                  class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors resize-none">{{ old('alamat', auth()->user()->pasien?->alamat ?? '') }}</textarea>
                        @error('alamat') <p class="mt-1 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Section Kesehatan -->
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200 pb-2 pt-2">Riwayat Kesehatan</h3>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Riwayat Penyakit / Alergi</label>
                    <textarea name="riwayat_penyakit" rows="3"
                              class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors resize-none placeholder:text-gray-400"
                              placeholder="Contoh: Alergi penicillin, hipertensi, dll...">{{ old('riwayat_penyakit', auth()->user()->pasien?->riwayat_penyakit ?? '') }}</textarea>
                </div>

            </div>
            <!-- button modal -->
            <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-end gap-3 border-t border-gray-200 sticky bottom-0">
                <button type="button" onclick="closeEditModal()" 
                        class="w-full sm:w-auto px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 rounded-sm text-xs font-bold transition-colors uppercase tracking-wider text-center">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold transition-colors uppercase tracking-wider text-center inline-flex items-center justify-center gap-2">
                    <i class="fas fa-save text-[10px]"></i> Simpan Perubahan
                </button>
            </div>
        </form>
        
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
//membuka modal edit
function openEditModal() {
    const modal = document.getElementById('editModal'); //ambil elemen id
    modal.classList.remove('hidden'); //hapus class hidden
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden'; //halaman tidak bisa di scroll
}
//tutup edit modal
function closeEditModal() {
    const modal = document.getElementById('editModal'); //ambil elemen id
    modal.classList.add('hidden'); //tambah hidden
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto'; //halaman sudah bisa scroll
}
//tutup modal ketika klik area luar
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
//tutup modal dengan tombol esc
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeEditModal();
    }
});

// Validasi NIK hanya angka
const nikInput = document.getElementById('inputNik');
nikInput?.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, ''); //hapus karakter lain selain angka
    if (this.value.length > 16) this.value = this.value.slice(0, 16);
});

// Validasi Nomor HP hanya angka dan +
const noHpInput = document.getElementById('inputNoHp');
noHpInput?.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9+]/g, ''); //hapus karakter lain selain angka
});

// Notifikasi SweetAlert2
@if(session('success'))
Swal.fire({
    width: 340,
    html: `
        <div class="text-center">
            <div style="width:48px;height:48px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #ccfbf1;">
                <i class="fas fa-check" style="font-size:20px; color:#0f766e;"></i>
            </div>
            <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Berhasil!</h2>
            <p style="font-size:13px; color:#6b7280;">{{ session('success') }}</p>
        </div>
    `,
    confirmButtonText: 'Oke',
    buttonsStyling: false,
    customClass: {
        popup: 'rounded-sm p-5',
        confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
    }
});
@endif
//sweetalert error
@if(session('error'))
Swal.fire({
    width: 340,
    html: `
        <div class="text-center">
            <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                <i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i>
            </div>
            <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal!</h2>
            <p style="font-size:13px; color:#6b7280;">{{ session('error') }}</p>
        </div>
    `,
    confirmButtonText: 'Mengerti',
    buttonsStyling: false,
    customClass: {
        popup: 'rounded-sm p-5',
        confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
    }
});
@endif
</script>
@endsection