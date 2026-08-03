@extends('layouts.dokter')
@section('title', 'Riwayat Medis: ' . $pasien->nama)

@section('content')
<div class="space-y-6">
    <!-- variabel yang diperlukan, rekam medis terakhir dan total kunjungan -->
    @php
        $lastRm = $rekamMedis->first();
        $totalKunjungan = $rekamMedis->count();
    @endphp

    <!-- Patient Data Header -->
    <div class="bg-white border border-gray-200 shadow-sm overflow-hidden rounded-sm">
        <div class="bg-teal-700 px-6 py-5 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold tracking-wide uppercase">{{ $pasien->nama }}</h1>
                    <div class="flex flex-wrap gap-x-6 gap-y-1 mt-2 text-sm text-teal-100 font-light">
                        <span>NIK: {{ $pasien->nik ?? '-' }}</span>
                        <span>No. HP: {{ $pasien->no_hp ?? '-' }}</span>
                        <span>{{ $pasien->jns_kelamin ?? '-' }}</span>
                    </div>
                </div>
                <a href="{{ route('dokter.antrean') }}" 
                   class="inline-flex items-center px-4 py-2 bg-white text-teal-800 rounded-sm text-sm font-semibold transition-all self-start md:self-center hover:bg-gray-100">
                    Kembali ke Antrean
                </a>
            </div>
        </div>
        
        <!-- card informasi -->
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-200 bg-white">
            <div class="px-6 py-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Kunjungan</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $totalKunjungan }}</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Kunjungan Terakhir</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $lastRm ? $lastRm->tgl_periksa->format('d M Y') : '-' }}</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Diagnosa Terakhir</p>
                <p class="text-base font-bold text-gray-900 mt-1 truncate">{{ $lastRm->diagnosa ?? 'Belum ada' }}</p>
            </div>
        </div>
    </div>

    <!-- timeline -->
    <div class="bg-white border border-gray-200 shadow-sm overflow-hidden rounded-sm">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">
                Riwayat Pemeriksaan
            </h2>
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $totalKunjungan }} Catatan</span>
        </div>

        <div class="p-6 bg-white">
            <!-- loop rm -->
            @forelse($rekamMedis as $index => $rm)
                @php 
                    $isFirst = $index === 0;
                    $isLast = $index === $rekamMedis->count() - 1;
                @endphp

                <div class="relative flex items-start gap-6 pb-8 group">
                    
                    <!-- Timeline Line & Dot (Minimalist Square) -->
                    <div class="absolute left-[5px] top-7 bottom-0 w-px bg-gray-200 {{ $isLast ? 'hidden' : '' }}" aria-hidden="true"></div>
                    <div class="relative z-10 mt-2 flex-shrink-0 w-3 h-3 rounded-sm {{ $isFirst ? 'bg-teal-700' : 'bg-gray-300 border border-gray-300' }} transition-all"></div>

                    <!-- Card Content -->
                    <div class="flex-1 bg-white border {{ $isFirst ? 'border-teal-600' : 'border-gray-200' }} rounded-sm overflow-hidden transition-all hover:border-teal-500">
                        
                        <!-- Header -->
                        <div class="px-5 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b {{ $isFirst ? 'bg-teal-50 border-teal-100' : 'bg-gray-50 border-gray-100' }}">
                            <div>
                                <p class="text-sm font-bold {{ $isFirst ? 'text-teal-900' : 'text-gray-800' }}">
                                    {{ $rm->tgl_periksa->format('d F Y') }}
                                    <span class="text-gray-400 font-medium mx-1">|</span>
                                    <span class="font-medium {{ $isFirst ? 'text-teal-700' : 'text-gray-500' }}">{{ $rm->tgl_periksa->format('H:i') }} WIB</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Dokter: {{ optional($rm->dokter)->nama ?? '-' }}
                                </p>
                            </div>
                            
                            <a href="{{ route('dokter.rekam-medis.show', $rm->id_rm) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-200 hover:bg-teal-50 hover:border-teal-200 text-teal-800 rounded-sm text-xs font-semibold transition-all self-start sm:self-center">
                                Lihat Detail
                            </a>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-gray-100">
                            <!-- Diagnosa -->
                            <div class="space-y-1 md:pr-6">
                                <h4 class="text-[11px] font-bold uppercase tracking-widest text-gray-400">
                                    Diagnosa
                                </h4>
                                <p class="text-sm text-gray-800 leading-relaxed">
                                    {{ $rm->diagnosa ?? '-' }}
                                </p>
                            </div>

                            <!-- Tindakan -->
                            <div class="space-y-1 md:pl-6 pt-4 md:pt-0">
                                <h4 class="text-[11px] font-bold uppercase tracking-widest text-gray-400">
                                    Tindakan
                                </h4>
                                <p class="text-sm text-gray-800 leading-relaxed">
                                    {{ $rm->tindakan ?? '-' }}
                                </p>
                            </div>
                        </div>
                        
                        @if($isFirst)
                        <div class="px-5 py-2 bg-teal-700 border-t border-teal-600 text-right">
                            <span class="text-[10px] font-bold text-teal-50 uppercase tracking-widest">Kunjungan Terakhir</span>
                        </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-gray-50 border border-dashed border-gray-200 rounded-sm">
                    <h3 class="text-lg font-bold text-gray-700 mb-1">Belum Ada Riwayat Medis</h3>
                    <p class="text-sm text-gray-500 max-w-md mx-auto">Pasien ini belum memiliki catatan pemeriksaan di klinik.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection