@extends('layouts.pasien')
@section('title', 'Isi Data Diri - Reservasi Klinik Gigi Dokter Ndeso')

@section('content')
<section class="min-h-screen bg-slate-50 py-8 md:py-12">
    <div class="container mx-auto px-4 max-w-4xl">
        
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            <!-- Header (Putih Senada Admin) -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Data Diri Pasien</h1>
                        <p class="text-xs text-gray-500 mt-1">Lengkapi data diri Anda untuk kelancaran reservasi</p>
                    </div>
                    <div class="flex items-center gap-2 text-teal-700">
                        <i class="fas fa-user-edit text-sm"></i>
                        <span class="text-[10px] font-bold uppercase tracking-widest">Formulir</span>
                    </div>
                </div>
            </div>
            
            <!-- Form Content -->
            <div class="p-6 lg:p-8">
                <form action="{{ route('pasien.form.store') }}" method="POST" id="pasienForm">
                    @csrf
                    <!-- Section Informasi Pribadi -->
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Pribadi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                        <div>
                            <!-- input nama -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama" 
                                   value="{{ old('nama', $pasien->nama ?? Auth::user()->name ?? '') }}"
                                   required
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400">
                            @error('nama')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <!-- input nik -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <input type="text" name="nik" maxlength="16"
                                   value="{{ old('nik', $pasien->nik ?? '') }}"
                                   required
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors font-mono tracking-wider placeholder:text-gray-400"
                                   placeholder="Masukkan 16 digit NIK">
                            @error('nik')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <!-- input tgl lahir -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="tgl_lahir"
                                   value="{{ old('tgl_lahir', $pasien->tgl_lahir ?? '') }}"
                                   required
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                            @error('tgl_lahir')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <!-- input jenis kelamin -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <div class="flex gap-6 mt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="jns_kelamin" value="L" 
                                           {{ old('jns_kelamin', $pasien?->jns_kelamin) == 'L' ? 'checked' : '' }} required
                                           class="text-teal-700 focus:ring-teal-500">
                                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Laki-laki</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="jns_kelamin" value="P" 
                                           {{ old('jns_kelamin', $pasien?->jns_kelamin) == 'P' ? 'checked' : '' }}
                                           class="text-teal-700 focus:ring-teal-500">
                                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Perempuan</span>
                                </label>
                            </div>
                            @error('jns_kelamin')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section Kontak & Alamat -->
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Kontak & Alamat</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                        <div>
                            <!-- input nomoh hp -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nomor Handphone <span class="text-red-500">*</span></label>
                            <input type="tel" name="no_hp"
                                   value="{{ old('no_hp', $pasien->no_hp ?? '') }}"
                                   required
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="0812XXXXXXXX">
                            @error('no_hp')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <!-- input pekerjaan -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pekerjaan</label>
                            <input type="text" name="pekerjaan"
                                   value="{{ old('pekerjaan', $pasien->pekerjaan ?? '') }}"
                                   class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                                   placeholder="Opsional">
                            @error('pekerjaan')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <!-- input alamat -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="alamat" rows="3" required
                                      class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400">{{ old('alamat', $pasien->alamat ?? '') }}</textarea>
                            @error('alamat')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section Kesehatan -->
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Riwayat Kesehatan</h3>

                    <div class="mb-8">
                        <!-- input riwayat penyakit -->
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Riwayat Penyakit / Alergi</label>
                        <textarea name="riwayat_penyakit" rows="4"
                                  class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400"
                                  placeholder="Contoh: Alergi penicillin, hipertensi, dll...">{{ old('riwayat_penyakit') }}</textarea>
                        
                        <!-- Catatan section -->
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-sm">
                            <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5 mb-1.5">
                                <i class="fas fa-exclamation-triangle text-[10px]"></i> Catatan Penting
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-amber-700 text-[10px] uppercase tracking-wider">
                                <li>Data Anda disimpan aman dan hanya digunakan untuk keperluan pelayanan klinik.</li>
                                <li>Informasi riwayat penyakit/alergi sangat penting untuk penanganan medis yang tepat.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('pasien.pilih.klinik') }}"
                           class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center justify-center gap-2">
                            <span>Simpan & Lanjut</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </form>
                <!-- kalo ada error tampilkan -->
                @if ($errors->any())
                    <div class="mt-6 p-3 bg-red-50 border border-red-200 rounded-sm text-red-700 text-[10px] font-bold uppercase tracking-wider">
                        <p class="mb-1 flex items-center gap-1"><i class="fas fa-exclamation-circle text-xs"></i> Terjadi kesalahan pada input berikut:</p>
                        <ul class="list-disc list-inside space-y-0.5 ml-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="text-center mt-6">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                &copy; Klinik Gigi Dokter Ndeso 
            </p> 
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // menampilkan sweetalert ketika sukses
    @if (session('success'))
        Swal.fire({ //popup
            width: 340,
            html: `
                <div class="text-center">
                    <div style="
                        width:48px;
                        height:48px;
                        background:#f0fdfa;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        margin:0 auto 12px;
                        border-radius:2px;
                        border:1px solid #ccfbf1;
                    ">
                        <i class="fas fa-check" style="font-size:20px; color:#0f766e;"></i>
                    </div>

                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                        Berhasil!
                    </h2>

                    <p style="font-size:13px; color:#6b7280;">
                        {{ session('success') }}
                    </p>
                </div>
            `,
            confirmButtonText: 'Lanjut Pilih Klinik',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('pasien.pilih.klinik') }}";
            }
        });
    @endif

    // Validasi NIK hanya angka
    const nikInput = document.querySelector('input[name="nik"]');
    nikInput?.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, ''); //karakter lain disembunyikan
    });

    // Validasi Nomor HP hanya angka
    const noHpInput = document.querySelector('input[name="no_hp"]');
    noHpInput?.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, ''); //karakter lain disembunyikan
    });
</script>
@endsection