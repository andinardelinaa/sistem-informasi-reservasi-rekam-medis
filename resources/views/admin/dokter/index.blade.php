@extends('layouts.admin')
@section('header-title', 'Kelola Dokter')
@include('layouts.partials.alert')

@section('content')
    <div class="space-y-8">

        <!-- card statistik-->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-5xl mx-auto">
            <!-- Total Dokter -->
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Dokter</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $dokter->total() }}</p>
                        <p class="text-xs text-gray-400 mt-1">Terdaftar dalam sistem</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-user-md text-white text-sm"></i>
                    </div>
                </div>
            </div>

            <!-- Dokter Baru Bulan Ini -->
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Dokter Baru</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">
                            {{ $dokter->where('created_at', '>=', now()->startOfMonth())->count() }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-user-plus text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Dokter -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-sm">

            <!-- Header(Putih) -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Dokter</h2>
            </div>

            <div class="px-6 py-3 border-b border-gray-200 bg-white">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Search -->
                        <div class="relative flex-grow sm:flex-grow-0">
                            <input type="text" id="searchInput" 
                                placeholder="Cari nama, spesialisasi, email..." 
                                class="w-full sm:w-56 pl-8 pr-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <!-- Filter Spesialisasi -->
                        <div class="cs-wrap w-full sm:w-60" id="specializationFilter_wrap">
                            <button type="button" class="cs-btn">
                                <span>Semua Spesialisasi</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                            <div class="cs-list">
                                <div class="cs-item selected" data-val="">Semua Spesialisasi</div>
                                <!-- dropdown spesialisasi -->
                                @foreach($spesialisasi as $item)
                                    <div class="cs-item" data-val="{{ $item }}">
                                        {{ ucwords($item) }}
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="specializationFilter" name="specializationFilter" value="">
                        </div>
                    </div>

                    <!-- tambah -->
                    <div class="flex justify-end w-full lg:w-auto">
                        <a href="{{ route('admin.dokter.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            <i class="fas fa-plus mr-2 text-[10px]"></i>
                            Tambah
                        </a>
                    </div>
                </div>
            </div>
            <!-- menampilkan seluruh data dokter -->
            <div id="tableContent" class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-white border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Dokter</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Spesialisasi</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Email Login</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">No. HP</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <!-- digunakan untuk menampilkan seluruh data dokter -->
                        @forelse($dokter as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors dokter-row"
                            data-name="{{ strtolower($item->nama) }}" 
                            data-specialization="{{ strtolower($item->spesialisasi) }}" 
                            data-email="{{ strtolower($item->user->email ?? '') }}"
                            data-phone="{{ strtolower($item->no_hp ?? '') }}">
                            <!-- nomor urut -->
                            <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                                {{ $dokter->firstItem() + $loop->index }}
                            </td>
                            <!-- nama dokter -->
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $item->nama }}</p>
                            </td>
                            <!-- spesialisasi -->
                            <td class="px-5 py-4">
                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">
                                    {{ $item->spesialisasi }}
                                </span>
                            </td>
                            <!-- email login -->
                            <td class="px-5 py-4 text-sm text-gray-700">
                                {{ $item->user->email ?? '—' }}
                            </td>
                            <!-- no hp -->
                            <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                                {{ $item->no_hp }}
                            </td>
                            <!-- aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-stretch gap-2 h-9">
                                    <a href="{{ route('admin.dokter.edit', $item->id_dokter) }}" 
                                    class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                        <i class="fas fa-pen text-[10px] mr-1"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.dokter.destroy', $item->id_dokter) }}" 
                                        method="POST" 
                                        class="flex-1 h-full delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
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
                            <td colspan="6" class="py-16 text-center bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                        <i class="fas fa-user-md text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada data dokter</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <p class="text-xs text-gray-500">
                    Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $dokter->count() }}</span> dari 
                    <span class="font-bold text-gray-800">{{ $dokter->total() }}</span> dokter
                </p>
                {{ $dokter->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- CSS Custom Select (Senada) -->
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

<script>
// ubah dropdown custom
function initCS(id) {
    const wrap = document.getElementById(id + '_wrap'); //ambil elemen wrapper
    if (!wrap) return null;
    const btn = wrap.querySelector('.cs-btn'); //ambil tombol dropdown
    const list = wrap.querySelector('.cs-list'); //ambil list
    const input = document.getElementById(id);
    if (!btn || !list || !input) return null;

    btn.addEventListener('click', () => {
        if (btn.disabled) return;
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        list.classList.toggle('active');
        btn.classList.toggle('active');
    });

    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });

    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => {
            input.value = item.dataset.val;
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
initCS('specializationFilter');

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput'); //serach 
    const specializationFilter = document.getElementById('specializationFilter'); //dropdown
    const tableRows = document.querySelectorAll('#tableBody .dokter-row');
    const showingCount = document.getElementById('showingCount'); //jumlah data yg ditampilkan
    //filter data
    function performFilter() {
        const searchTerm = searchInput.value.toLowerCase().trim(); // ambil teks dr kotak pencarian
        const specTerm = specializationFilter.value.toLowerCase(); // Ambil value dari input hidden
        let visible = 0;
        //membaca dataset
        tableRows.forEach(row => {
            const name = (row.dataset.name || '').toLowerCase();
            const spec = (row.dataset.specialization || '').toLowerCase();
            const email = (row.dataset.email || '').toLowerCase();
            const phone = (row.dataset.phone || '').toLowerCase();
            //memeriksa apakah nama dokter menganduk kata yg dicari
            const matchSearch = !searchTerm || name.includes(searchTerm) || spec.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm);
            const matchSpec = !specTerm || spec === specTerm;

            if (matchSearch && matchSpec) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        showingCount.textContent = visible;
    }
    //fungsi berjalan ketika masih diketik
    searchInput.addEventListener('input', performFilter);
    specializationFilter.addEventListener('change', performFilter); // Tetap listen 'change' di hidden input

    // konfirmasi hapus
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const dokterName = this.closest('tr')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';

            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Hapus Dokter
                        </h2>
                        <p style="font-size:13px; color:#6b7280;">
                            Yakin hapus <b>${dokterName}</b>?
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
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    // allert error
    @if(session('error'))
        Swal.fire({
            width: 380,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fde68a;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#d97706;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Gagal Menghapus</h2>
                    <p style="font-size:13px; color:#6b7280;">{{ session('error') }}</p>
                </div>
            `,
            confirmButtonText: 'Mengerti',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            }
        });
    @endif
    performFilter();
});
</script>
@endsection