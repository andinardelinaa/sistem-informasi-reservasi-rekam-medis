@extends('layouts.admin')
@section('header-title', 'Edit Klinik')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- petunjuk posisi halaman saat ini -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.klinik.index') }}" class="hover:text-teal-700 transition-colors font-bold">Klinik</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Klinik</h1>
                <p class="text-xs text-gray-500 mt-1">Ubah informasi klinik "{{ $klinik->nama_klinik ?? '—' }}"</p>
            </div>
            <a href="{{ route('admin.klinik.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i> Kembali
            </a>
        </div>

        <!-- form input -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.klinik.update', $klinik->id_klinik) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6 mb-8">
                    
                    <!-- Nama Klinik -->
                    <div>
                        <label for="nama_klinik" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Nama Klinik <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_klinik" name="nama_klinik" value="{{ old('nama_klinik', $klinik->nama_klinik) }}" required
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Klinik Gigi Dokter Ndeso">
                        @error('nama_klinik') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea id="alamat" name="alamat" rows="3" required
                                  class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-y placeholder:text-gray-400" 
                                  placeholder="Jl. Pandanaran No.45, Semarang">{{ old('alamat', $klinik->alamat) }}</textarea>
                        @error('alamat') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Link Google Maps -->
                    <div>
                        <label for="maps_url" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Link Google Maps</label>
                        <input type="url" id="maps_url" name="maps_url" value="{{ old('maps_url', $klinik->maps_url) }}" 
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="https://maps.app.goo.gl/xxxxx">
                        @error('maps_url') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Opsional, untuk menampilkan peta lokasi klinik</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.klinik.index') }}" 
                       class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection