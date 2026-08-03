@extends('layouts.dokter')
@section('title', 'Antrean Pasien - Klinik Gigi Dokter Ndeso')
@section('header-title', 'Antrean Pasien')
@include('layouts.partials.alert')

@section('content')
<div class="space-y-8">
    <!-- ambil dokter yang login -->

    @php
        $dokter = Auth::user()->dokter;

        // Urutkan aktif (menunggu, tindakan) di atas, selesai/dibatalkan di bawah
        $sortedReservasi = $reservasi->sortBy(function($item) {
            $priority = in_array($item->status, ['selesai', 'dibatalkan']) ? 1 : 0;
            return [$priority, $item->jam_reservasi];
        });
    @endphp
    <!-- jika reservasi tidak kosong -->
    @if($reservasi->isNotEmpty())
        <!-- Card Antrean Utama -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            <!-- Card Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 uppercase tracking-wider">
                            Antrean Pasien Hari Ini
                        </h2>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 px-4 py-2 rounded-sm border border-gray-200">
                        <div class="h-9 w-9 rounded-sm bg-teal-700 flex items-center justify-center">
                            <i class="fas fa-user-md text-white text-sm"></i>
                        </div>
                        <div>
                            <!-- informasi dokter bertugas -->
                            <p class="text-[10px] text-gray-500 font-medium uppercase">Dokter Bertugas</p>
                            <p class="font-bold text-gray-900 text-sm">
                                {{ $dokter->nama ?? 'Dokter' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Antrean -->
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <!-- header -->
                            <th class="text-center py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest w-12">No</th>
                            <th class="text-left py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jam</th>
                            <th class="text-left py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pasien</th>
                            <th class="text-left py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Keluhan</th>
                            <th class="text-center py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="text-center py-3 px-4 text-[10px] font-bold text-gray-500 uppercase tracking-widest w-[220px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- loop reservasi -->
                        @foreach($sortedReservasi as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 px-4 text-center text-sm font-semibold text-gray-500">
                                {{ $loop->index + 1 }}.
                            </td>
                            <td class="py-4 px-4 text-gray-700">
                                <!-- jam reservasi -->
                                <span class="font-bold text-sm text-gray-800">{{ $item->jam_reservasi }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <!-- nama pasien dan nomor hp -->
                                <p class="font-bold text-gray-900 text-sm">{{ $item->pasien->nama ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->pasien->no_hp ?? '-' }}</p>
                                @if($item->rekamMedis()->exists())
                                    <span class="inline-block mt-1 text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded-sm border border-blue-100">
                                        RM Tersimpan
                                    </span>
                                @endif
                            </td>
                            <!-- keluhan -->
                            <td class="py-4 px-4 text-sm text-gray-600 max-w-xs truncate">
                                {{ $item->keluhan ?? '-' }}
                            </td>
                            <!-- status -->
                            <td class="py-4 px-4 text-center">
                                @if($item->status == 'menunggu')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-sm bg-gray-200 text-gray-700 uppercase tracking-wider">
                                        Menunggu
                                    </span>
                                @elseif($item->status == 'tindakan')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-sm bg-teal-600 text-white uppercase tracking-wider">
                                        Siap Periksa
                                    </span>
                                @elseif($item->status == 'selesai')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-sm bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                        Selesai
                                    </span>
                                @elseif($item->status == 'dibatalkan')
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold rounded-sm bg-red-50 text-red-600 uppercase tracking-wider">
                                        Batal
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <!-- jika pasien sudah di konfirmasi hadir oleh admin, otomatis jd tindakan -->
                                @if($item->status == 'tindakan')
                                    <div class="flex flex-col items-center gap-1.5 w-full">
                                        <!-- isi rm -->
                                        <a href="{{ route('dokter.rekam-medis.create', $item->id_reservasi) }}"
                                           class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs rounded-sm font-semibold transition-colors">
                                            <i class="fas fa-file-medical mr-1.5 text-[10px]"></i> Isi Rekam Medis
                                        </a>
                                        <!-- konfrimasi selesai -->
                                        @if($item->rekamMedis()->exists())
                                            <button type="button"
                                                    id="btn-selesai-{{ $item->id_reservasi }}"
                                                    onclick="confirmSelesai({{ $item->id_reservasi }}, '{{ addslashes($item->pasien->nama ?? '') }}')"
                                                    class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-sm font-semibold transition-colors">
                                                <i class="fas fa-check mr-1.5 text-[10px]"></i> Selesai
                                            </button>
                                        @else
                                            <button type="button"
                                                    disabled
                                                    title="Rekam medis belum diisi"
                                                    class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-gray-200 text-gray-400 text-xs rounded-sm font-semibold cursor-not-allowed">
                                                <i class="fas fa-check mr-1.5 text-[10px]"></i> Selesai
                                            </button>
                                        @endif
                                        <!-- lihat riwayat rm pasien -->
                                        <a href="{{ route('dokter.rekam-medis.riwayat', $item->pasien->id_pasien) }}"
                                           class="w-full inline-flex items-center justify-center px-3 py-1.5 bg-purple-50 border border-purple-200 text-purple-700 hover:bg-purple-100 text-xs rounded-sm font-semibold transition-colors">
                                            <i class="fas fa-history mr-1.5 text-[10px]"></i> Riwayat RM
                                        </a>
                                    </div>
                                @elseif($item->status == 'selesai')
                                    <a href="{{ route('dokter.rekam-medis.riwayat', $item->pasien->id_pasien) }}"
                                       class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-sm font-semibold transition-colors">
                                        <i class="fas fa-history mr-1.5 text-[10px]"></i> Riwayat RM
                                    </a>
                                @elseif($item->status == 'dibatalkan')
                                    <span class="text-gray-300 text-xs">-</span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Menunggu antrian</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 text-xs text-gray-500 flex justify-between items-center">
                <p>Total antrean hari ini: <span class="font-bold text-gray-800">{{ $reservasi->count() }} pasien</span></p>
                <p class="font-medium">Hanya status <span class="text-teal-700 font-bold">"Siap Periksa"</span> yang dapat diproses</p>
            </div>
        </div>
    @else
        <!-- Empty State (Kotak/Formal) -->
        <div class="bg-white border border-dashed border-gray-300 p-16 text-center rounded-sm">
            <div class="h-16 w-16 bg-gray-100 flex items-center justify-center mb-4 mx-auto border border-gray-200">
                <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-700 mb-1">Tidak Ada Antrean Hari Ini</h3>
            <p class="text-sm text-gray-500">Belum ada reservasi pasien untuk jadwal Anda hari ini.</p>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
// function dipanggil ketika tombol selesa ditekan
function confirmSelesai(id, namaPasien) {
    Swal.fire({ //tampilkan sweet alert
        width: 340,
        html: `
            <div class="text-center">
                <div style="
                    width:48px;
                    height:48px;
                    background:#d1fae5;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 12px;
                    border-radius:2px;
                    border:1px solid #a7f3d0;
                ">
                    <i class="fas fa-check" style="font-size:20px; color:#059669;"></i>
                </div>

                <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                    Selesai Pemeriksaan
                </h2>

                <p style="font-size:13px; color:#6b7280;">
                    Tandai <b>${namaPasien}</b> selesai?
                </p>
            </div>
        `,
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: 'Ya, Selesai', //tombol konfrim
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
            cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
        }
        //jika memilih ya, maka selesai
    }).then((result) => {
        if (result.isConfirmed) {
            disableSelesaiButton(id);
            submitSelesai(id);
        }
    });
}
//funtion nonaktifkan rombol agar tidak di klik berkali kali
function disableSelesaiButton(id) {
    const btn = document.getElementById('btn-selesai-' + id); //ambil button selesai
    if (btn) {
        btn.disabled = true; //nonaktifkan button
        //hapus class lama
        btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'text-white', 'transition-colors');
        //tambah class baru
        btn.classList.add('bg-emerald-200', 'text-emerald-700', 'cursor-wait');
        //ubah isi tombol
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5 text-[10px]"></i> Menyelesaikan...';
    }
}
//kirim data ke laravel
function submitSelesai(id) {
    const form = document.createElement('form'); //buat form baru
    form.method = 'POST'; //data dikirim dengan menggunakan method post
    form.action = `/dokter/selesai/${id}`;
    //membuat inputan csrf
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden'; //hidden
    csrfInput.name = '_token';
    csrfInput.value = csrfToken;
    form.appendChild(csrfInput);
    //menambahkan form ke halaman
    document.body.appendChild(form);
    form.submit(); //kirim form
}
</script>
@endsection