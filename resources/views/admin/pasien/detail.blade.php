@extends('layouts.admin')

@section('header-title', 'Detail Pasien')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!--menunjukan berada di halaman detail pasien-->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.pasien.index') }}" class="hover:text-teal-700 transition-colors font-bold">Pasien</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Detail</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Card Container -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Detail Pasien</h1>
                <p class="text-xs text-gray-500 mt-1">Informasi lengkap data pasien dan akun pengguna</p>
            </div>
            <a href="{{ route('admin.pasien.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i>
                Kembali
            </a>
        </div>

        <!-- Content -->
        <div class="p-6 lg:p-8 space-y-8">
            <!-- Profile-->
            <div class="pb-6 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-16 h-16 bg-teal-700 flex items-center justify-center rounded-sm shrink-0">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <div>
                        <!-- menampilkan informasi singkat pasein, nama, nik, jenis kelamin, umur -->
                        <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">{{ $pasien->nama }}</h2>
                        <p class="text-sm text-gray-500 font-mono mt-0.5">NIK: {{ $pasien->nik }}</p>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider 
                                {{ $pasien->jns_kelamin === 'L' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-pink-50 text-pink-700 border-pink-200' }}">
                                {{ $pasien->jenis_kelamin_text }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                <i class="fas fa-birthday-cake mr-1"></i>
                                {{ $pasien->umur_detail }} ({{ $pasien->umur }} tahun)
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Informasi Pribadi -->
            <div>
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Lengkap</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->nama }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">NIK</p>
                        <p class="text-sm text-gray-800 mt-1 font-mono tracking-wider">{{ $pasien->nik }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jenis Kelamin</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->jenis_kelamin_text }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal Lahir</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->tgl_lahir->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nomor HP</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->no_hp }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pekerjaan</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->pekerjaan ?? '—' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Alamat Lengkap</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->alamat }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Riwayat Penyakit</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->riwayat_penyakit ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Section Informasi Akun Pengguna -->
            <div>
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Akun Pengguna</h3>
                
                @if($pasien->user)
                <div class="mb-4 p-3 bg-cyan-50 border border-cyan-200 rounded-sm text-cyan-800 flex items-center gap-2">
                    <i class="fas fa-link text-[10px]"></i>
                    <p class="text-[10px] font-bold uppercase tracking-wider">Pasien ini memiliki akun pengguna terhubung</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">ID User</p>
                        <p class="text-sm text-gray-800 mt-1">#{{ $pasien->user->id }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Akun</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->user->nama }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Email Login</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Role</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->user->roles->first()->nama ?? 'PASIEN' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status Verifikasi Email</p>
                        @if($pasien->user->email_verified_at)
                        <div class="mt-1 inline-flex items-center gap-2 px-2 py-1 bg-emerald-50 border border-emerald-200 rounded-sm">
                            <i class="fas fa-check-circle text-emerald-600 text-xs"></i>
                            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Terverifikasi - {{ $pasien->user->email_verified_at->format('d M Y, H:i') }}</p>
                        </div>
                        @else
                        <div class="mt-1 inline-flex items-center gap-2 px-2 py-1 bg-amber-50 border border-amber-200 rounded-sm">
                            <i class="fas fa-exclamation-circle text-amber-600 text-xs"></i>
                            <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Belum Terverifikasi</p>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-sm text-amber-800 flex items-center gap-2">
                    <i class="fas fa-unlink text-[10px]"></i>
                    <p class="text-[10px] font-bold uppercase tracking-wider">Pasien ini belum memiliki akun pengguna</p>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.pasien.edit', $pasien->id_pasien) }}" 
                       class="inline-flex items-center px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                        <i class="fas fa-user-plus mr-2 text-[10px]"></i>
                        Buat Akun Pengguna
                    </a>
                </div>
                @endif
            </div>

            <!-- Section: Riwayat Reservasi & Rekam Medis -->
            @php
                // Defensif: Jika relasi null, pake empty collection 
                $riwayatReservasi = $pasien->reservasi ?? collect([]);
            @endphp
            <div>
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Riwayat Reservasi & Rekam Medis</h3>
                @if($riwayatReservasi->count() > 0)
                <div class="overflow-x-auto border border-gray-200 rounded-sm">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-left">Tanggal</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-left">Keluhan</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-left">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">Aksi RM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatReservasi->sortByDesc('tanggal') as $reservasi)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-bold text-gray-800 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($reservasi->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">
                                    {{ $reservasi->keluhan ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider 
                                        {{ $reservasi->status === 'selesai' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 
                                          ($reservasi->status === 'dibatalkan' ? 'bg-red-50 text-red-700 border-red-200' : 
                                          ($reservasi->status === 'diproses' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-amber-50 text-amber-700 border-amber-200')) }}">
                                        {{ $reservasi->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if($reservasi->rekamMedis)
                                        <a href="{{ route('admin.rekam-medis.show', $reservasi->rekamMedis->id_rm) }}" 
                                           class="text-[10px] font-bold text-teal-700 hover:text-teal-900 uppercase tracking-wider transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-eye"></i> Lihat RM
                                        </a>
                                    @elseif($reservasi->status === 'selesai')
                                        <a href="{{ route('admin.rekam-medis.create', ['reservasi_id' => $reservasi->id_reservasi]) }}" 
                                           class="text-[10px] font-bold text-amber-700 hover:text-amber-900 uppercase tracking-wider transition-colors inline-flex items-center gap-1">
                                            <i class="fas fa-plus-circle"></i> Buat RM
                                        </a>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-6 border border-dashed border-gray-300 bg-gray-50/50 flex flex-col items-center justify-center rounded-sm">
                    <i class="fas fa-notes-medical text-gray-300 text-2xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Belum ada data reservasi & rekam medis</p>
                </div>
                @endif
            </div>

            <!-- Section Aktivitas Sistem -->
            <div>
                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Aktivitas Sistem</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-4">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal Dibuat</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Terakhir Diperbarui</p>
                        <p class="text-sm text-gray-800 mt-1">{{ $pasien->updated_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
            </div>

            <!-- Action Footer -->
            <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                <div class="text-[10px] text-gray-400 uppercase tracking-wider italic mr-auto hidden sm:block">
                    Data pasien dan akun pengguna terhubung
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.pasien.index') }}" 
                       class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                        Kembali
                    </a>
                    <a href="{{ route('admin.pasien.edit', $pasien->id_pasien) }}" 
                       class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                        Edit Data
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection