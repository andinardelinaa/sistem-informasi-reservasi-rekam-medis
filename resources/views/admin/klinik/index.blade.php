@extends('layouts.admin')
@section('header-title', 'Data Klinik')
@include('layouts.partials.alert')

@section('content')
    <div class="space-y-8">

        <!-- card statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-5xl mx-auto">
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Klinik</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $klinik->count() }}</p> 
                        <p class="text-xs text-gray-400 mt-1">Terdaftar dalam sistem</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-clinic-medical text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Klinik -->
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            <!-- Header (Putih) -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Klinik</h2>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Search -->
                        <div class="relative flex-grow sm:flex-grow-0">
                            <input type="text" id="searchInput" 
                                   placeholder="Cari nama klinik atau alamat..." 
                                   class="w-full sm:w-56 pl-8 pr-3 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>

                        <!-- Tambah Klinik -->
                        <a href="{{ route('admin.klinik.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            <i class="fas fa-plus mr-2 text-[10px]"></i> Tambah
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableContent" class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-white border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Nama Klinik</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Alamat</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Maps</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                        </tr>
                    </thead>
                    <!-- tampil data klinik -->
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                    <!--perulangan untuk menampilkan seluruh data klinik  -->
                    @forelse($klinik as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors klinik-row" 
                            data-name="{{ strtolower($item->nama_klinik) }}" 
                            data-address="{{ strtolower($item->alamat ?? '') }}">
                            <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                            <!--nomor urut data  -->
                            {{ $loop->iteration }}.
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $item->nama_klinik }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 max-w-md">
                                {{ Str::limit($item->alamat, 85) }}
                            </td>
                            <td class="px-5 py-4">
                                @if($item->maps_url)
                                    <button
                                        type="button"
                                        class="preview-map-btn inline-flex items-center gap-1 text-xs text-teal-700 hover:text-teal-800 font-bold uppercase tracking-wider hover:underline"
                                        data-map="{{ $item->maps_url }}">
                                        <i class="fas fa-map-marker-alt text-[10px]"></i>
                                        <span>Lihat Maps</span>
                                    </button>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-stretch gap-2 h-9">

                                    <a href="{{ route('admin.klinik.edit', $item->id_klinik) }}" 
                                    class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                        <i class="fas fa-pen text-[10px] mr-1"></i>
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.klinik.destroy', $item->id_klinik) }}" 
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
                            <td colspan="5" class="py-16 text-center bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                        <i class="fas fa-inbox text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada data klinik</p>
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
                    Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $klinik->count() }}</span> dari 
                    <span class="font-bold text-gray-800">{{ $klinik->count() }}</span> klinik
                </p>
                @if($klinik instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $klinik->links('pagination::tailwind') }}
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#tableBody .klinik-row');
    const showingCount = document.getElementById('showingCount');
    // pencarian data
    function performFilter() {
        const searchTerm = searchInput.value.toLowerCase().trim(); //mengambil isi dr searchbar
        let visible = 0; //hitung jumlag data
        //memeriksa data satu persatu
        tableRows.forEach(row => {
            //ambil nama dan alamat klinik
            const name = (row.dataset.name || '').toLowerCase();
            const address = (row.dataset.address || '').toLowerCase();
            //pengecekan kata kunci
            const match = !searchTerm || name.includes(searchTerm) || address.includes(searchTerm);
            if (match) {
                row.style.display = '';
                visible++;
            } else {
                row.style.display = 'none';
            }
        });

        if(showingCount) showingCount.textContent = visible;
    }
    //menjalankan fungsi saat mengetik
    searchInput.addEventListener('input', performFilter);

    //konfirmasi delete
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) { //fungsi jalan ketika tombol diklik
            e.preventDefault();
            const form = this.closest('form');
            //ambil nama pada baris yang sama
            const klinikName = this.closest('tr')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';
            //popup hapus
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Hapus Klinik
                        </h2>
                        <p style="font-size:13px; color:#6b7280;">
                            Yakin hapus <b>${klinikName}</b>?
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
    //preview gmaps
    document.querySelectorAll('.preview-map-btn').forEach(btn => {
        btn.addEventListener('click', function() { 
            const mapUrl = this.dataset.map; //ambil link maps
            //tampil popup
            Swal.fire({
                width: 800,
                showConfirmButton: false,
                showCloseButton: true,
                html: `
                    <iframe
                        src="${mapUrl}"
                        width="100%"
                        height="450"
                        style="border:0;border-radius:4px;"
                        loading="lazy"
                        allowfullscreen>
                    </iframe>
                `
            });
        });
    });

    // gagal hapus jika ada fk di tavel lain
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