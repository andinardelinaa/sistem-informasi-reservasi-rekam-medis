@extends('layouts.admin')

@section('header-title', 'Kelola Rekam Medis - ' . ($klinikNama ?? 'Klinik'))

@include('layouts.partials.alert')

@section('content')
<style>
.cs-wrap { position: relative; }
.cs-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.75rem; background: #fff; border: 1px solid #d1d5db; border-radius: 2px; font-size: 0.875rem; text-align: left; cursor: pointer; transition: all 0.15s; }
.cs-btn:disabled { background: #f3f4f6; color: #9ca3af; cursor: not-allowed; }
.cs-btn:not(:disabled):hover { border-color: #9ca3af; }
.cs-btn.active { border-color: #0f766e; box-shadow: 0 0 0 1px #0f766e; }
.cs-btn.active i { transform: rotate(180deg); }

.cs-list { position: absolute; top: 100%; left: 0; right: 0; margin-top: 4px; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 50; max-height: 250px; overflow-y: auto; opacity: 0; visibility: hidden; transform: translateY(-5px); transition: all 0.15s ease; }
.cs-list.active { opacity: 1; visibility: visible; transform: translateY(0); }
.cs-list::-webkit-scrollbar { width: 4px; }
.cs-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

.cs-item { padding: 0.5rem 0.75rem; font-size: 0.875rem; cursor: pointer; display: flex; align-items: center; transition: background 0.1s; }
.cs-item:hover { background: #f0fdfa; color: #0f766e; }
.cs-item.selected { background: #f0fdfa; color: #0f766e; font-weight: 600; }
</style>

<div class="space-y-8">
    <!-- Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 max-w-7xl mx-auto">
        <!-- Total Rekam Medis -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Rekam Medis</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Terdaftar dalam sistem</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-file-medical text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Hari Ini -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hari Ini</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['hari_ini'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Minggu Ini -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Minggu Ini</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['minggu_ini'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Minggu berjalan</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-week text-white text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Total Pasien -->
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Pasien</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pasien_unik'] ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Pasien unik tercatat</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Rekam Medis -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- Header Utama (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Rekam Medis</h2>
        </div>
        <!-- Filter Bar -->
        <div class="px-6 py-3 border-b border-gray-200 bg-white">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                    <!-- Search -->
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="text" id="searchInput" placeholder="Cari nama pasien/dokter..." 
                               class="w-full sm:w-56 pl-8 pr-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm transition-colors">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </div>
                    </div>
                    <!-- Klinik Filter -->
                    <div class="cs-wrap w-full sm:w-48" id="klinikFilter_wrap">
                        <button type="button" class="cs-btn">
                            <span>Semua Klinik</span>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                        </button>
                        <div class="cs-list">
                            <div class="cs-item selected" data-val="">Semua Klinik</div>
                            @foreach($klinik as $k)
                                <div class="cs-item" data-val="{{ $k->id_klinik }}">{{ $k->nama_klinik }}</div>
                            @endforeach
                        </div>
                        <input type="hidden" id="klinikFilter" name="klinikFilter" value="">
                    </div>
                    <!-- Bulan Filter -->
                    <div class="cs-wrap w-full sm:w-44" id="bulanFilter_wrap">
                        <button type="button" class="cs-btn">
                            <span>Semua Bulan</span>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                        </button>
                        <div class="cs-list">
                            <div class="cs-item selected" data-val="">Semua Bulan</div>
                            <div class="cs-item" data-val="01">Januari</div>
                            <div class="cs-item" data-val="02">Februari</div>
                            <div class="cs-item" data-val="03">Maret</div>
                            <div class="cs-item" data-val="04">April</div>
                            <div class="cs-item" data-val="05">Mei</div>
                            <div class="cs-item" data-val="06">Juni</div>
                            <div class="cs-item" data-val="07">Juli</div>
                            <div class="cs-item" data-val="08">Agustus</div>
                            <div class="cs-item" data-val="09">September</div>
                            <div class="cs-item" data-val="10">Oktober</div>
                            <div class="cs-item" data-val="11">November</div>
                            <div class="cs-item" data-val="12">Desember</div>
                        </div>
                        <input type="hidden" id="bulanFilter" name="bulanFilter" value="">
                    </div>
                </div>

                <div class="flex gap-2">
                    <!-- export word -->
                    <a href="{{ route('admin.rekam-medis.export.word', request()->only(['klinik', 'bulan'])) }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                        <i class="fas fa-file-word mr-2 text-[10px]"></i>WORD
                    </a>
                    <!-- export excel -->
                    <a href="{{ route('admin.rekam-medis.export.excel', request()->only(['klinik', 'bulan'])) }}" 
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-green-700 hover:bg-green-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                        <i class="fas fa-file-excel mr-2 text-[10px]"></i>EXCEL
                    </a>
                </div>
            </div>
        </div>
        <!-- tavle content -->
        <div id="tableContent" class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Pasien</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dokter</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Diagnosa</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Klinik</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    <!-- simpan attribut -->
                    @forelse($rekamMedis as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors rm-row"
                        data-name="{{ strtolower($item->reservasi->pasien->nama ?? '') }}" 
                        data-dokter="{{ strtolower($item->dokter->nama ?? '') }}"
                        data-dokter-id="{{ $item->id_dokter }}"
                        data-klinik-id="{{ $item->reservasi->jadwal->id_klinik ?? '' }}"
                        data-bulan="{{ $item->tgl_periksa->format('m') }}"
                        data-id="{{ $item->id_rm }}">
                        
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                            {{ ($rekamMedis->currentPage() - 1) * $rekamMedis->perPage() + $loop->iteration }}
                        </td>
                        <!--tanggal periksa -->
                        <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                            {{ $item->tgl_periksa->format('d M Y') }}
                        </td>
                        <!-- nama pasien -->
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $item->reservasi->pasien->nama ?? 'N/A' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ $item->reservasi->pasien->no_hp ?? '-' }}</p>
                        </td>
                        <!-- nama dokter -->
                        <td class="px-5 py-4">
                            <p class="text-sm font-bold text-gray-900">{{ $item->dokter->nama ?? 'N/A' }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ $item->dokter->spesialisasi ?? '-' }}</p>
                        </td>
                        <!-- diagnosa -->
                        <td class="px-5 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ Str::limit($item->diagnosa, 40) }}
                        </td>
                        <!-- klinik -->
                        <td class="px-5 py-4 text-center">
                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">
                               {{ $item->reservasi?->jadwal?->klinik?->nama_klinik ?? 'N/A' }}
                            </span>
                        </td>
                        <!-- aksi -->
                        <td class="px-5 py-4">
                            <div class="flex items-stretch gap-2 h-9 justify-center">
                                <!-- detail -->
                                <a href="{{ route('admin.rekam-medis.show', $item->id_rm) }}" 
                                   class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                    <i class="fas fa-eye text-[10px] mr-1"></i>
                                    Detail
                                </a>
                                <!-- delete -->
                                <form action="{{ route('admin.rekam-medis.destroy', $item->id_rm) }}" method="POST" class="flex-1 h-full delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full h-full inline-flex items-center justify-center px-3 bg-white border border-red-200 hover:bg-red-50 text-red-700 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider delete-btn">
                                        <i class="fas fa-trash text-[10px] mr-1"></i>
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center bg-gray-50">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                    <i class="fas fa-file-medical text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada data rekam medis</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider mt-1">Data akan muncul setelah dokter melakukan pemeriksaan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (FIX: Struktur nested yang salah sebelumnya) -->
        <div class="border-t border-gray-200 bg-white">
            <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $rekamMedis->count() }}</span> dari 
                    <span class="font-bold text-gray-800">{{ $rekamMedis->total() }}</span> rekam medis
                </p>
                {{ $rekamMedis->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// custom dropdown
function initCS(id) {
    // ambil elemen html
    const wrap = document.getElementById(id + '_wrap');
    if (!wrap) return null;
    const btn = wrap.querySelector('.cs-btn');
    const list = wrap.querySelector('.cs-list');
    const input = document.getElementById(id);
    if (!btn || !list || !input) return null;
    // fungsi dijalankan ketika button di klik
    btn.addEventListener('click', () => {
        if (btn.disabled) return; // buka tutup dropdown
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        list.classList.toggle('active');
        btn.classList.toggle('active');
    });
    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });
    // memilih item
    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => { //klik item
            input.value = item.dataset.val; //simpan hidden value
            btn.querySelector('span').innerHTML = item.innerHTML;
            list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected');
            list.classList.remove('active');
            btn.classList.remove('active');
            input.dispatchEvent(new Event('change'));
        });
    });

    if (input.value) {
        const selectedItem = list.querySelector(`.cs-item[data-val="${input.value}"]`);
        if (selectedItem) {
            btn.querySelector('span').innerHTML = selectedItem.innerHTML;
            selectedItem.classList.add('selected');
        }
    }

    return {
        disable: (val) => { btn.disabled = val; if(val) { list.classList.remove('active'); btn.classList.remove('active'); } },
        setPlaceholder: (text) => { if(!input.value) btn.querySelector('span').textContent = text; }
    };
}
// Inisialisasi Filter Dropdown
initCS('klinikFilter');
initCS('bulanFilter');

// tunggu html selese dimuat 
document.addEventListener('DOMContentLoaded', function() { 
    // ambil elemen elemen html
    const searchInput = document.getElementById('searchInput');
    const bulanFilter = document.getElementById('bulanFilter');
    const tableBody = document.getElementById('tableBody');
    const allRows = tableBody ? Array.from(tableBody.querySelectorAll('.rm-row')) : [];
    const showingCount = document.getElementById('showingCount');
    const klinikFilter = document.getElementById('klinikFilter');
    //function fillter tabel
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase().trim(); //ambil keyword
        const bulanVal = bulanFilter.value; //ambil bulan
        const klinikId = klinikFilter.value; //ambil klinik
        let visibleCount = 0; //hitung data yang tampil
        // loop semua baris
        allRows.forEach(row => {
            // ambil data dari dataset
            const name = (row.dataset.name || '').toLowerCase();
            const dokter = (row.dataset.dokter || '').toLowerCase();
            const rowBulan = row.dataset.bulan || '';
            const rowKlinikId = row.dataset.klinikId || '';
            // cek kecocokan
            const matchesSearch = !searchTerm || name.includes(searchTerm) || dokter.includes(searchTerm);
            const matchesBulan = !bulanVal || rowBulan === bulanVal;
            const matchesKlinik = !klinikId || rowKlinikId === klinikId;
            // jika cocok tampilkan jika tidak cocok sembuyinkan
            if (matchesSearch && matchesBulan && matchesKlinik) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        // hitung data yg tampil
        showingCount.textContent = visibleCount;
    }
    // realtime
    klinikFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    bulanFilter.addEventListener('change', filterTable);

    // konfirm delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //jangan langsung hapus data
            //ambil nama pasien
            const pasienName = this.closest('tr')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';
            //popup
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="
                            width:48px;
                            height:48px;
                            background:#fee2e2;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:0 auto 12px;
                            border-radius:2px;
                            border:1px solid #fecaca;
                        ">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>

                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Hapus Rekam Medis
                        </h2>

                        <p style="font-size:13px; color:#6b7280;">
                            Yakin hapus rekam medis pasien <b>${pasienName}</b>?
                        </p>
                    </div>
                `,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-sm p-5',
                    confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider',
                    cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2'
                }
                //jika iya setuju di hapus, hapus perb=manen dr database
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-[10px] mr-1"></i>Hapus';
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection