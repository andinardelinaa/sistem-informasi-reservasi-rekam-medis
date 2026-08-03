@extends('layouts.admin')

@section('header-title', 'Edit Hari Libur')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- menunjukan sedang ada di halaman edit -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.hari-libur.index') }}" class="hover:text-teal-700 transition-colors font-bold">Hari Libur</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Card Container -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Hari Libur</h1>
                <p class="text-xs text-gray-500 mt-1">Perbarui informasi jadwal libur klinik</p>
            </div>
            <a href="{{ route('admin.hari-libur.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- pesan error-->
        @if(session('error'))
        <div class="px-6 py-3 border-b border-red-200 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-[10px]"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Form edit -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.hari-libur.update', $hariLibur->id_hari_libur) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-10 gap-y-8">
                    <!-- Tanggal Libur -->
                    <div>
                        <label for="tanggal" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">
                            Pilih Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="tanggal" name="tanggal" required
                               value="{{ old('tanggal', $hariLibur->tanggal ? \Carbon\Carbon::parse($hariLibur->tanggal)->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                        @error('tanggal')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Keterangan -->
                    <div>
                        <label for="keterangan" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">
                            Keterangan Libur <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="keterangan" name="keterangan" required
                               value="{{ old('keterangan', $hariLibur->keterangan) }}"
                               class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" 
                               placeholder="Contoh: Libur Idul Fitri, Cuti Bersama">
                        @error('keterangan')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.hari-libur.index') }}" 
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