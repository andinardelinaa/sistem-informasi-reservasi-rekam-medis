@extends('layouts.dokter')
@section('title', 'Rekam Medis - Klinik Gigi Dokter Ndeso')
@section('header-title', 'Rekam Medis')

@section('content')
<div class="space-y-6">
    <!-- buat variabel untuk ambil doter yg login, cek rm draft atau final -->
    @php
        $dokter = Auth::user()->dokter;
        $isEdit = isset($rekamMedis) && $rekamMedis->status === 'draft';
        $isView = isset($rekamMedis) && $rekamMedis->status === 'final';
    @endphp
    <!-- buat form, dengan operator ternary, jika isedit maka ddata dikirim ke controller, 
     jika isview atau final form tidak mengirim, rm sudah terkunci-->
    <form action="{{ $isEdit ? route('dokter.rekam-medis.store') : ($isView ? '#' : route('dokter.rekam-medis.store')) }}" 
          method="POST" 
          id="formRekamMedis"
          @if($isView) onsubmit="return false;" @endif>
        @csrf
        <!-- kirim id reservasi dan id dokter -->
        <input type="hidden" name="id_reservasi" value="{{ $reservasi->id_reservasi }}">
        <input type="hidden" name="id_dokter" value="{{ $dokter->id_dokter }}">

        <!-- Card Data Rekam Medis -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-center">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-file-medical text-teal-700 text-sm"></i>
                    Data Rekam Medis
                </h2>
                @if($isEdit)
                    <span class="text-[10px] bg-amber-50 text-amber-700 px-2 py-1 rounded-sm font-bold border border-amber-200 uppercase tracking-wider">
                        <i class="fas fa-edit mr-1"></i>Draft - Bisa Diedit
                    </span>
                @elseif($isView)
                    <span class="text-[10px] bg-green-50 text-green-700 px-2 py-1 rounded-sm font-bold border border-green-200 uppercase tracking-wider">
                        <i class="fas fa-lock mr-1"></i>Final - Tidak Bisa Diedit
                    </span>
                @endif
            </div>
            <div class="p-6 space-y-6">
                <!-- Info Pasien -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 border border-gray-200 rounded-sm">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-sm bg-teal-50 flex items-center justify-center border border-teal-200">
                            <i class="fas fa-user text-teal-700 text-xs"></i>
                        </div>
                        <div>
                            <!-- tampil nama pasien -->
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Nama Pasien</p>
                            <p class="font-bold text-gray-900 text-sm">{{ $reservasi->pasien->nama ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <!--keluhan  -->
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-sm bg-amber-50 flex items-center justify-center border border-amber-200">
                            <i class="fas fa-notes-medical text-amber-700 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Keluhan</p>
                            <p class="font-bold text-gray-900 text-sm">{{ $reservasi->keluhan ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!--input Diagnosa & Tindakan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">
                            Diagnosa <span class="text-red-500">*</span>
                        </label>
                        <textarea name="diagnosa" rows="4"
                            class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400 {{ $isView ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                            placeholder="Masukkan diagnosa pasien..."
                            {{ $isView ? 'readonly' : '' }}>{{ old('diagnosa', $rekamMedis->diagnosa ?? '') }}</textarea>
                        @error('diagnosa')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">
                            Tindakan Utama
                        </label>
                        <textarea name="tindakan_utama" rows="4"
                            class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400 {{ $isView ? 'bg-gray-50 cursor-not-allowed' : '' }}"
                            placeholder="Masukkan tindakan utama..."
                            {{ $isView ? 'readonly' : '' }}>{{ old('tindakan_utama', $rekamMedis->tindakan ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Detail Gigi -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tooth text-teal-700 text-sm"></i>
                    <!-- judul -->
                    Detail Pemeriksaan Gigi
                </h2>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    @if($isView)
                    <!-- jika status rm final -->
                        <p class="text-gray-500 text-[10px] bg-green-50 inline-block px-4 py-2 rounded-sm border border-green-200 uppercase tracking-widest font-bold text-green-700">
                            <i class="fas fa-lock mr-2"></i>
                            Data Pemeriksaan Gigi (Final)
                        </p>
                    @else
                    <!-- jika belum final -->
                        <p class="text-gray-500 text-[10px] bg-slate-50 inline-block px-4 py-2 rounded-sm border border-gray-200 uppercase tracking-widest font-bold">
                            <i class="fas fa-hand-pointer text-teal-500 mr-2"></i>
                            Klik gigi untuk menentukan kondisi dan tindakan
                        </p>
                    @endif
                </div>

                <!-- Diagram Gigi Odontogram (5 Segmen) -->
                <div class="overflow-x-auto pb-2 {{ $isView ? 'pointer-events-none opacity-80' : '' }}">
                    <div class="flex flex-col items-center gap-2 mb-8 select-none min-w-[640px] mx-auto">

                        <!-- Label Kanan / Kiri -->
                        <div class="flex items-center justify-between w-full max-w-3xl px-2 mb-1">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                <i class="fas fa-arrow-right mr-1 text-gray-300"></i>Kanan
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                Kiri<i class="fas fa-arrow-left ml-1 text-gray-300"></i>
                            </span>
                        </div>

                        <!-- rahang atas -->
                        <!-- Gigi Susu Rahang Atas (55-51 | 61-65) -->
                        <div class="flex flex-col items-center w-full max-w-3xl">
                            <span class="text-[9px] font-bold text-pink-400 tracking-widest uppercase mb-1">Gigi Susu Atas</span>
                            <div class="flex items-center justify-center gap-[3px] sm:gap-1 w-full">
                                <!-- loop gigi kiri -->
                                @foreach([55, 54, 53, 52, 51] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:34px;height:44px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <!-- untuk mengahsilkan 5 tombol gigi -->
                                        <svg class="w-5 h-5 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[7px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach

                                <div class="w-4 flex justify-center items-center"><div class="w-px h-5 bg-gray-300"></div></div>
                                <!-- loop gigi kanan -->
                                @foreach([61, 62, 63, 64, 65] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:34px;height:44px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-5 h-5 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[7px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Gigi Tetap Rahang Atas (18-11 | 21-28) -->
                        <div class="flex flex-col items-center w-full max-w-3xl">
                            <span class="text-[9px] font-bold text-teal-600 tracking-widest uppercase mb-1">Gigi Tetap Atas</span>
                            <div class="flex items-center justify-center gap-1 sm:gap-1.5 w-full">
                                <!-- loop gigi kiri -->
                                @foreach([18, 17, 16, 15, 14, 13, 12, 11] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:40px;height:52px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-6 h-7 sm:w-7 sm:h-8 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[8px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach

                                <div class="w-5 flex justify-center items-center"><div class="w-px h-8 bg-gray-300"></div></div>
                                <!-- loop gigi kanan -->
                                @foreach([21, 22, 23, 24, 25, 26, 27, 28] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:40px;height:52px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-6 h-7 sm:w-7 sm:h-8 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[8px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                       <!-- pembatas rahang atas dan rahang bawah, arcus dentis -->
                        <div class="w-full max-w-3xl flex items-center justify-center gap-3 py-3">
                            <div class="h-px bg-gray-300 flex-1"></div>
                            <span class="text-[9px] text-gray-500 uppercase font-extrabold tracking-[0.2em] bg-white px-3 py-1 rounded-sm border border-gray-200">Arcus Dentis</span>
                            <div class="h-px bg-gray-300 flex-1"></div>
                        </div>

                        <!-- rahang bawah -->
                        <!-- Gigi Tetap Rahang Bawah (48-41 | 31-38) -->
                        <div class="flex flex-col items-center w-full max-w-3xl">
                            <span class="text-[9px] font-bold text-teal-600 tracking-widest uppercase mb-1">Gigi Tetap Bawah</span>
                            <div class="flex items-center justify-center gap-1 sm:gap-1.5 w-full">
                                <!-- rahang kiri -->
                                @foreach([48, 47, 46, 45, 44, 43, 42, 41] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:40px;height:52px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-6 h-7 sm:w-7 sm:h-8 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleY(-1)">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[8px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach

                                <div class="w-5 flex justify-center items-center"><div class="w-px h-8 bg-gray-300"></div></div>
                                <!-- rahang kanan -->
                                @foreach([31, 32, 33, 34, 35, 36, 37, 38] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:40px;height:52px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-6 h-7 sm:w-7 sm:h-8 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleY(-1)">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[8px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Gigi Susu Rahang Bawah (85-81 | 71-75) -->
                        <div class="flex flex-col items-center w-full max-w-3xl">
                            <span class="text-[9px] font-bold text-pink-400 tracking-widest uppercase mb-1">Gigi Susu Bawah</span>
                            <div class="flex items-center justify-center gap-[3px] sm:gap-1 w-full">
                            <!-- rahang kiri  -->
                            @foreach([85, 84, 83, 82, 81] as $gigi)
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:34px;height:44px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <svg class="w-5 h-5 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleY(-1)">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <span class="text-[7px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach

                                <div class="w-4 flex justify-center items-center"><div class="w-px h-5 bg-gray-300"></div></div>
                                <!-- rahang kanan -->
                                @foreach([71, 72, 73, 74, 75] as $gigi)
                                <!-- tombol setiap gigi, select gigi(72) -->
                                    <button type="button"
                                        class="gigi-btn group relative border-0 rounded-sm bg-transparent hover:bg-teal-50/30 transition-all duration-150 flex flex-col items-center justify-center overflow-hidden"
                                        style="width:34px;height:44px;"
                                        data-nomor="{{ $gigi }}" onclick="selectGigi({{ $gigi }})">
                                        <!-- gambar bentuk gigi -->
                                        <svg class="w-5 h-5 text-gray-300 group-hover:text-teal-500 transition-colors" viewBox="0 0 32 40" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="transform:scaleY(-1)">
                                            <path d="M4 4 L28 4 L24 12 L8 12 Z" />
                                            <path d="M8 28 L24 28 L28 36 L4 36 Z" />
                                            <path d="M4 4 L8 12 L8 28 L4 36 Z" />
                                            <path d="M28 4 L24 12 L24 28 L28 36 Z" />
                                            <rect x="8" y="12" width="16" height="16" />
                                        </svg>
                                        <!-- tampil, agar dokter tau gigi yg dipilih -->
                                        <span class="text-[7px] font-bold text-gray-300 group-hover:text-teal-700 leading-none mt-0.5">{{ $gigi }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Label Kanan / Kiri -->
                        <div class="flex items-center justify-between w-full max-w-3xl px-2 mt-1">
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                <i class="fas fa-arrow-right mr-1 text-gray-300"></i>Kanan
                            </span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                Kiri<i class="fas fa-arrow-left ml-1 text-gray-300"></i>
                            </span>
                        </div>

                        <!-- Keterangan Warna -->
                        <div class="flex flex-wrap items-center justify-center gap-4 mt-4 px-4">
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-wider">Keterangan:</span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-green-500 inline-block border border-green-600"></span><span class="text-[10px] text-gray-700 font-medium">Sehat</span></span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500 inline-block border border-red-600"></span><span class="text-[10px] text-gray-700 font-medium">Masalah</span></span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-500 inline-block border border-amber-600"></span><span class="text-[10px] text-gray-700 font-medium">Perawatan</span></span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-500 inline-block border border-blue-600"></span><span class="text-[10px] text-gray-700 font-medium">Tambal</span></span>
                            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-indigo-500 inline-block border border-indigo-600"></span><span class="text-[10px] text-gray-700 font-medium">Lainnya</span></span>
                        </div>
                    </div>
                </div>

                @error('detail_gigi')
                    <p class="text-red-500 text-[10px] font-bold uppercase tracking-wider text-center mb-4">{{ $message }}</p>
                @enderror

                <!-- Container Detail Gigi Terpilih -->
                <div id="detail-gigi-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                </div>

                <!-- Hidden Inputs -->
                <div id="hidden-inputs"></div>
            </div>
        </div>

        <!-- Tombol Aksi  -->
        <div class="bg-white border border-gray-200 px-6 py-3 shadow-sm sticky bottom-0 z-50 mt-2 rounded-sm">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dokter.antrean') }}"
                   class="px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                    <i class="fas fa-arrow-left mr-2 text-[10px]"></i>Kembali ke Antrean
                </a>
                
                @if(!$isView)
                    <button type="submit"
                            class="px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-save text-[10px]"></i>
                        {{ $isEdit ? 'Update Rekam Medis' : 'Simpan Rekam Medis' }}
                    </button>
                @else
                    <span class="px-6 py-2.5 bg-gray-100 text-gray-500 rounded-sm text-xs font-bold uppercase tracking-wider inline-flex items-center gap-2 cursor-not-allowed">
                        <i class="fas fa-lock text-[10px]"></i>Rekam Medis Final
                    </span>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// menyimpan data gigi yang dipilih
let selectedTeeth = {};

// Opsi kondisi
const kondisiOptions = [
    { value: '', label: 'Pilih Kondisi Gigi' },
    { value: 'sehat', label: '✓ Sehat' },
    { value: 'karies', label: '● Karies' },
    { value: 'tambal', label: '▣ Tambal' },
    { value: 'cabut', label: '✕ Perlu Pencabutan' },
    { value: 'goyang', label: '〜 Goyang' },
    { value: 'abses', label: '▲ Abses' },
    { value: 'pulpitis', label: '◉ Pulpitis' },
    { value: 'periodontitis', label: '◎ Periodontitis' },
    { value: 'fraktur', label: '╱ Fraktur' },
    { value: 'hipoplasia', label: '○ Hipoplasia' },
    { value: 'erupsi', label: '↑ Erupsi' },
    { value: 'impaksi', label: '↓ Impaksi' },
    { value: 'resorpsi', label: '◊ Resorpsi' },
    { value: 'lainnya', label: '✎ Lainnya' }
];

// Opsi tindakan
const tindakanOptions = [
    { value: '', label: 'Pilih Tindakan' },
    { value: 'pemeriksaan', label: 'Pemeriksaan Umum' },
    { value: 'tambal', label: 'Penambalan' },
    { value: 'cabut', label: 'Pencabutan' },
    { value: 'scaling', label: 'Scaling & Root Planing' },
    { value: 'root_canal', label: 'Root Canal Treatment' },
    { value: 'krown', label: 'Pemasangan Crown' },
    { value: 'bleaching', label: 'Bleaching' },
    { value: 'ortho', label: 'Orthodonti' },
    { value: 'gigi_palsu', label: 'Pemasangan Gigi Palsu' },
    { value: 'fluoride', label: 'Aplikasi Fluoride' },
    { value: 'pemadatan', label: 'Pemadatan Fissure' },
    { value: 'lainnya', label: '✎ Lainnya' }
];

// Warna kondisi untuk tombol diagram (Flat Formal)
const kondisiButtonColors = {
    'sehat':       { bg: '#22c55e', border: '#16a34a', text: '#fff' },
    'karies':      { bg: '#ef4444', border: '#dc2626', text: '#fff' },
    'tambal':      { bg: '#3b82f6', border: '#2563eb', text: '#fff' },
    'cabut':       { bg: '#6b7280', border: '#4b5563', text: '#fff' },
    'goyang':      { bg: '#f59e0b', border: '#d97706', text: '#fff' },
    'abses':       { bg: '#dc2626', border: '#b91c1c', text: '#fff' },
    'pulpitis':    { bg: '#f97316', border: '#ea580c', text: '#fff' },
    'periodontitis':{ bg: '#a855f7', border: '#9333ea', text: '#fff' },
    'fraktur':     { bg: '#ea580c', border: '#c2410c', text: '#fff' },
    'hipoplasia':  { bg: '#eab308', border: '#ca8a04', text: '#fff' },
    'erupsi':      { bg: '#06b6d4', border: '#0891b2', text: '#fff' },
    'impaksi':     { bg: '#64748b', border: '#475569', text: '#fff' },
    'resorpsi':    { bg: '#ec4899', border: '#db2777', text: '#fff' },
    'lainnya':     { bg: '#6366f1', border: '#4f46e5', text: '#fff' }
};

// Warna kondisi untuk card detail
const kondisiCardColors = {
    'sehat':       { border: 'border-green-300', bg: 'bg-green-50/40' },
    'karies':      { border: 'border-red-300', bg: 'bg-red-50/40' },
    'tambal':      { border: 'border-blue-300', bg: 'bg-blue-50/40' },
    'cabut':       { border: 'border-gray-300', bg: 'bg-gray-50/40' },
    'goyang':      { border: 'border-amber-300', bg: 'bg-amber-50/40' },
    'abses':       { border: 'border-red-400', bg: 'bg-red-50/50' },
    'pulpitis':    { border: 'border-orange-300', bg: 'bg-orange-50/40' },
    'periodontitis':{ border: 'border-purple-300', bg: 'bg-purple-50/40' },
    'fraktur':     { border: 'border-orange-400', bg: 'bg-orange-50/40' },
    'hipoplasia':  { border: 'border-yellow-300', bg: 'bg-yellow-50/40' },
    'erupsi':      { border: 'border-cyan-300', bg: 'bg-cyan-50/40' },
    'impaksi':     { border: 'border-slate-300', bg: 'bg-slate-50/40' },
    'resorpsi':    { border: 'border-pink-300', bg: 'bg-pink-50/40' },
    'lainnya':     { border: 'border-indigo-300', bg: 'bg-indigo-50/40' }
};

//fungsi dijalankan ketika dokter klik sbuah gigi
function selectGigi(nomorGigi) {
    //ambil tombol gigi yang di klik
    const btn = document.querySelector(`[data-nomor="${nomorGigi}"]`);
    //cek apakah gigi sudah pernah dipilih
    if (selectedTeeth[nomorGigi]) {
        delete selectedTeeth[nomorGigi]; //jika sudah hapud data gigi dari selectedTeeth
        resetButtonStyle(btn); //mengembalikan warna tombol jd semula
        removeDetailCard(nomorGigi); //hapus card detail gigi
    } else { //jika gigi belum dipilih
        selectedTeeth[nomorGigi] = { //tambahkan ke selectedTeeth
            kondisi: '',
            tindakan: '',
            kondisi_lainnya: '',
            tindakan_lainnya: ''
        };
        setSelectedButtonStyle(btn); //beri warna pada gigi yang dipilih
        addDetailCard(nomorGigi); //buat card detail
    }
    updateHiddenInputs(); //perbarui hidden input
}
//fungsi ketika gigi dibatalkan
function resetButtonStyle(btn) {
    btn.style.backgroundColor = ''; //hapus background
    btn.style.borderColor = ''; //hapud border
    btn.style.color = ''; //hapus tulisan
    btn.style.boxShadow = ''; //hapus bayangan
    const svg = btn.querySelector('svg'); //ambil svg gigi
    const span = btn.querySelector('span'); //ambil nomor gigi
    if (svg) svg.style.color = ''; //kembalikan warna
    if (span) span.style.color = ''; //kembalikan warna angka
}
//fungsi dipanggil ketika gigi baru dippilih
function setSelectedButtonStyle(btn) {
    btn.style.backgroundColor = 'transparent';
    btn.style.borderColor = 'transparent';
    btn.style.color = '#0d9488'; //beri warna teal
    btn.style.boxShadow = 'none'; 
    btn.querySelector('svg').style.color = '#0d9488'; //ubah warna svg
    btn.querySelector('span').style.color = '#0d9488'; //ubah warna nomor gigi
}
//fungsi ini dijalakan ketika dokter memilih kondisi gigi
function updateButtonCondition(nomorGigi) {
    //cari tombol gigi
    const btn = document.querySelector(`[data-nomor="${nomorGigi}"]`);
    if (!btn || !selectedTeeth[nomorGigi]) return;
    const kondisi = selectedTeeth[nomorGigi].kondisi; //ambil kondisi
    const svg = btn.querySelector('svg'); //ambil svg dan angka
    const span = btn.querySelector('span');
    //cek apakah kondisi punya warna
    if (kondisi && kondisiButtonColors[kondisi]) {
        const colors = kondisiButtonColors[kondisi]; //ambil warna
        btn.style.backgroundColor = 'transparent'; //ubah warna
        btn.style.borderColor = 'transparent'; 
        btn.style.boxShadow = 'none';
        if (svg) svg.style.color = colors.bg; //ubah gambar gigi
        if (span) span.style.color = colors.bg; // ubbah warna nomor gigi
    } else { 
        btn.style.backgroundColor = 'transparent';
        btn.style.borderColor = 'transparent';
        btn.style.boxShadow = 'none';
        if (svg) svg.style.color = '#0d9488';
        if (span) span.style.color = '#0d9488';
    }
}

// membuat card / detail pemeriksaan untuk setiap gigi yang dipilih
function addDetailCard(nomorGigi, prefillData = null) {
    // ambil tempat card akan ditaruh
    const container = document.getElementById('detail-gigi-container');
    //buat elemen card baru
    const card = document.createElement('div');
    card.id = `detail-card-${nomorGigi}`; //beri id card
    //beri class css
    card.className = 'bg-white border border-gray-200 rounded-sm p-5 shadow-sm hover:shadow-md transition-all duration-200 animate-fade-in';
    //menentukan gigi susu atau tetap
    const isSusu = [51,52,53,54,55,61,62,63,64,65,71,72,73,74,75,81,82,83,84,85].includes(Number(nomorGigi));
    //membuat label 
    const jenisLabel = isSusu ? '<span class="text-[9px] bg-pink-50 text-pink-700 px-1.5 py-0.5 rounded-sm font-bold border border-pink-200 uppercase tracking-wider">Susu</span>' : '<span class="text-[9px] bg-teal-50 text-teal-700 px-1.5 py-0.5 rounded-sm font-bold border border-teal-200 uppercase tracking-wider">Tetap</span>';
    //buat isi dropdwon kondisi, memberi otomatis data lama
    const kondisiHtml = kondisiOptions.map(opt =>
        `<option value="${opt.value}" ${opt.value === '' ? 'disabled' : ''} ${prefillData && prefillData.kondisi === opt.value ? 'selected' : (!prefillData && opt.value === '' ? 'selected' : '')}>${opt.label}</option>`
    ).join('');
    //dropdown tindakan, memberi otomatis data lama
    const tindakanHtml = tindakanOptions.map(opt =>
        `<option value="${opt.value}" ${opt.value === '' ? 'disabled' : ''} ${prefillData && prefillData.tindakan === opt.value ? 'selected' : (!prefillData && opt.value === '' ? 'selected' : '')}>${opt.label}</option>`
    ).join('');
    // ambil isi lainnya
    const kondisiLainnyaValue = prefillData ? (prefillData.kondisi_lainnya || '') : '';
    const tindakanLainnyaValue = prefillData ? (prefillData.tindakan_lainnya || '') : '';
    const kondisiLainnyaHidden = prefillData && prefillData.kondisi === 'lainnya' ? '' : 'hidden';
    const tindakanLainnyaHidden = prefillData && prefillData.tindakan === 'lainnya' ? '' : 'hidden';
    //cek mode view
    const isReadonly = {{ $isView ? 'true' : 'false' }};
    //membuat isi card
    card.innerHTML = `
        <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-sm bg-teal-700 flex items-center justify-center shadow-sm">
                    <span class="text-white font-bold text-sm">${nomorGigi}</span>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-xs flex items-center gap-2 uppercase tracking-wider">Gigi ${nomorGigi} ${jenisLabel}</h3>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest">Nomor FDI</p>
                </div>
            </div>
            ${!isReadonly ? `
            <button type="button" onclick="removeGigi(${nomorGigi})"
                    class="w-8 h-8 flex items-center justify-center rounded-sm text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all border border-transparent hover:border-red-200">
                <i class="fas fa-times text-xs"></i>
            </button>
            ` : ''}
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Kondisi</label>
                <select id="kondisi-select-${nomorGigi}"
                        onchange="handleKondisiChange(${nomorGigi}, this.value)"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-medium transition-colors ${isReadonly ? 'bg-gray-50 cursor-not-allowed' : ''}"
                        ${isReadonly ? 'disabled' : ''}>
                    ${kondisiHtml}
                </select>
                <div id="kondisi-lainnya-${nomorGigi}" class="${kondisiLainnyaHidden} mt-2">
                    <input type="text"
                           placeholder="Tulis kondisi lainnya..."
                           value="${kondisiLainnyaValue}"
                           class="w-full px-3 py-2.5 bg-white border border-indigo-300 rounded-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium transition-colors placeholder:text-gray-400 ${isReadonly ? 'bg-gray-50 cursor-not-allowed' : ''}"
                           oninput="updateGigiData(${nomorGigi}, 'kondisi_lainnya', this.value)"
                           ${isReadonly ? 'readonly' : ''}>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tindakan</label>
                <select id="tindakan-select-${nomorGigi}"
                        onchange="handleTindakanChange(${nomorGigi}, this.value)"
                        class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm font-medium transition-colors ${isReadonly ? 'bg-gray-50 cursor-not-allowed' : ''}"
                        ${isReadonly ? 'disabled' : ''}>
                    ${tindakanHtml}
                </select>
                <div id="tindakan-lainnya-${nomorGigi}" class="${tindakanLainnyaHidden} mt-2">
                    <input type="text"
                           placeholder="Tulis tindakan lainnya..."
                           value="${tindakanLainnyaValue}"
                           class="w-full px-3 py-2.5 bg-white border border-indigo-300 rounded-sm focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-medium transition-colors placeholder:text-gray-400 ${isReadonly ? 'bg-gray-50 cursor-not-allowed' : ''}"
                           oninput="updateGigiData(${nomorGigi}, 'tindakan_lainnya', this.value)"
                           ${isReadonly ? 'readonly' : ''}>
                </div>
            </div>
        </div>
    `;
    container.appendChild(card);
}
// dipanggil ketika dropdown kondisi berubah
function handleKondisiChange(nomorGigi, value) {
    updateGigiData(nomorGigi, 'kondisi', value); //menyimoan data
    const lainnyaDiv = document.getElementById(`kondisi-lainnya-${nomorGigi}`);
    if (value === 'lainnya') { //jika memilih lainnta
        lainnyaDiv.classList.remove('hidden'); //textbox jadi muncul
        if (!{{ $isView ? 'true' : 'false' }}) {
            lainnyaDiv.querySelector('input').focus(); //cursor fokus ke text box
        }
    } else { //kalo bukan lainnya textbox disembunyikan lagi
        lainnyaDiv.classList.add('hidden');
        updateGigiData(nomorGigi, 'kondisi_lainnya', '');
    }
    //ubah warna gigi di diagram
    updateButtonCondition(nomorGigi);
    updateCardStyle(nomorGigi); //ubah warna card
}
//mengubah drodwon tindakan
function handleTindakanChange(nomorGigi, value) {
    updateGigiData(nomorGigi, 'tindakan', value); //simpan data

    const lainnyaDiv = document.getElementById(`tindakan-lainnya-${nomorGigi}`);
    if (value === 'lainnya') { //jika piloih lainnya 
        lainnyaDiv.classList.remove('hidden'); //muncul textbox
        if (!{{ $isView ? 'true' : 'false' }}) {
            lainnyaDiv.querySelector('input').focus();
        }
    } else { //jika bukan lainnya, textbox tidak tampil
        lainnyaDiv.classList.add('hidden');
        updateGigiData(nomorGigi, 'tindakan_lainnya', '');
    }
}
//hapus card detail gigi
function removeDetailCard(nomorGigi) {
    const card = document.getElementById(`detail-card-${nomorGigi}`); //ambil elemen card
    if (card) { //kasih animasi transparan dan mengecil
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        setTimeout(() => card.remove(), 200);
    }
}
//dipanggil ketika tombol silang ditekan
function removeGigi(nomorGigi) {
    //cari tombol gigi
    const btn = document.querySelector(`[data-nomor="${nomorGigi}"]`);
    if (btn) btn.click();
}
//update gigi
function updateGigiData(nomorGigi, field, value) {
    if (selectedTeeth[nomorGigi]) {
        selectedTeeth[nomorGigi][field] = value;
        updateHiddenInputs();
    }
}
//ubah warna card berdasarkan kondisi
function updateCardStyle(nomorGigi) {
    const card = document.getElementById(`detail-card-${nomorGigi}`);
    if (!card) return;

    const kondisi = selectedTeeth[nomorGigi]?.kondisi;
    //card akan selalu direset ke warna normal
    card.className = 'bg-white border border-gray-200 rounded-sm p-5 shadow-sm hover:shadow-md transition-all duration-200 animate-fade-in';
    //ambil sesuai kondisi gigi
    if (kondisi && kondisiCardColors[kondisi]) {
        const colors = kondisiCardColors[kondisi];
        card.classList.remove('border-gray-200');
        card.classList.add(colors.border, colors.bg);
    }
}
//input tersembunyi untuk mengirim card terkirim ke laravel
function updateHiddenInputs() {
    const container = document.getElementById('hidden-inputs');
    container.innerHTML = ''; //kosongkan supaya tidak menumpuk

    let index = 0;
    // loop
    for (const [nomor, data] of Object.entries(selectedTeeth)) {
        container.innerHTML += `
            <input type="hidden" name="detail_gigi[${index}][nomor_gigi]" value="${nomor}">
            <input type="hidden" name="detail_gigi[${index}][kondisi_gigi]" value="${data.kondisi}">
            <input type="hidden" name="detail_gigi[${index}][kondisi_lainnya]" value="${data.kondisi_lainnya || ''}">
            <input type="hidden" name="detail_gigi[${index}][tindakan]" value="${data.tindakan}">
            <input type="hidden" name="detail_gigi[${index}][tindakan_lainnya]" value="${data.tindakan_lainnya || ''}">
        `;
        index++;
    }
}

//mengisi ulang data lama
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($rekamMedis) && $rekamMedis->detailGigi) //cek apakah rekam medis sudah ada, jika sudah ada tampilkan semua
        const existingDetails = @json($rekamMedis->detailGigi); //mengubah data php jadi js
        existingDetails.forEach(detail => { //mengambil satu persatu data gigi
            const nomorGigi = detail.nomor_gigi; //ambil nomor gigi
            
            //cari tombol gigi pada diagram
            const btn = document.querySelector(`[data-nomor="${nomorGigi}"]`);
            if (btn) {
                // isi object selectedTeeth
                selectedTeeth[nomorGigi] = {
                    kondisi: detail.kondisi_gigi,
                    tindakan: detail.tindakan,
                    kondisi_lainnya: detail.kondisi_lainnya || '',
                    tindakan_lainnya: detail.tindakan_lainnya || ''
                };
                
                // Ubah warna tombol gigi
                const svgEl = btn.querySelector('svg');
                const spanEl = btn.querySelector('span'); //ambil angka
                if (kondisiButtonColors[detail.kondisi_gigi]) { //cek data gigi
                    const colors = kondisiButtonColors[detail.kondisi_gigi]; //ubah warna
                    btn.style.backgroundColor = 'transparent';
                    btn.style.borderColor = 'transparent';
                    btn.style.boxShadow = 'none';
                    if (svgEl) svgEl.style.color = colors.bg;
                    if (spanEl) spanEl.style.color = colors.bg;
                } else {
                    setSelectedButtonStyle(btn);
                }
                //buat card otomatis
                addDetailCard(nomorGigi, selectedTeeth[nomorGigi]);
                updateCardStyle(nomorGigi);
            }
        });
        updateHiddenInputs();
    @endif
});

// animasi
const style = document.createElement('style'); //buat tag style
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.25s ease-out;
    }
    .gigi-btn:active {
        transform: scale(0.92) !important;
    }
    .gigi-btn svg {
        transition: color 0.15s ease;
    }
    .gigi-btn span {
        transition: color 0.15s ease;
    }
`;
document.head.appendChild(style);

// validasi form
document.getElementById('formRekamMedis').addEventListener('submit', function(e) { //ketika tombol simpan diklik
    e.preventDefault(); //jangan langsung kirim
    //ambil dignosa
    const diagnosa = document.querySelector('textarea[name="diagnosa"]').value.trim();
    //jika diagnosa kosong, munculkan popup
    if (!diagnosa) {
        Swal.fire({
            width: 340,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Diagnosa Wajib Diisi</h2>
                    <p style="font-size:13px; color:#6b7280;">Silakan isi diagnosa pasien terlebih dahulu.</p>
                </div>
            `,
            confirmButtonText: 'Mengerti',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            }
        });
        return;
    }
    //cek apakah gigi sudah dipilih
    if (Object.keys(selectedTeeth).length === 0) { //hitung gigi yg dipilih jika 0 berarti bllm ada yg dipilih
        Swal.fire({ //muncul popup
            width: 340,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gigi Belum Dipilih</h2>
                    <p style="font-size:13px; color:#6b7280;">Silakan pilih minimal satu gigi pada diagram.</p>
                </div>
            `,
            confirmButtonText: 'Mengerti',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            }
        });
        return;
    }
    //membuat array untuk kesalahan
    const incomplete = []; 
    //loop gigi 1 per 1
    for (const [nomor, data] of Object.entries(selectedTeeth)) {
        //jika kondisinya kosong , tampilkan pesan
        if (!data.kondisi) incomplete.push(`Gigi ${nomor}: kondisi belum dipilih`);
        //kondisi lainnya kosong, tampilkan pesan
        else if (data.kondisi === 'lainnya' && !data.kondisi_lainnya.trim()) incomplete.push(`Gigi ${nomor}: tuliskan kondisi lainnya`);
        //jika tindakan kosong, tampilkan
        if (!data.tindakan) incomplete.push(`Gigi ${nomor}: tindakan belum dipilih`);
        //jika tindakan lainnya kosong, tampilkan
        else if (data.tindakan === 'lainnya' && !data.tindakan_lainnya.trim()) incomplete.push(`Gigi ${nomor}: tuliskan tindakan lainnya`);
    }
    //jika ada kesalahan
    if (incomplete.length > 0) {
        Swal.fire({ //popup menampilkan seluruh daftar kesalahan
            width: 380,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Data Belum Lengkap</h2>
                    <div style="font-size:12px; color:#6b7280; text-align:left; max-height: 150px; overflow-y: auto; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 10px;">
                        <ul style="list-style-type: disc; padding-left: 20px;">
                            ${incomplete.map(i => `<li style="margin-bottom: 4px;">${i}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'Mengerti',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            }
        });
        return;
    }
    //ambil nama pasien
    const namaPasien = @json($reservasi->pasien->nama ?? '');
    //konfirm terakhur
    Swal.fire({
        width: 360,
        html: `
            <div class="text-center">
                <div style="width:48px;height:48px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #ccfbf1;">
                    <i class="fas fa-question-circle" style="font-size:20px; color:#0f766e;"></i>
                </div>
                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                    {{ $isEdit ? 'Update' : 'Simpan' }} Rekam Medis?
                </h2>
                <p style="font-size:13px; color:#6b7280;">
                    Anda akan {{ $isEdit ? 'memperbarui' : 'menyimpan' }} rekam medis untuk pasien <strong>${namaPasien}</strong>.<br>
                    Data masih dapat diubah kembali sebelum diklik "Selesai" di antrean.
                </p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Ya, {{ $isEdit ? 'Update' : 'Simpan' }}',
        cancelButtonText: 'Batal',
        buttonsStyling: false,
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
            cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
        }
        //jika iya, submit form
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
</script>
@endsection