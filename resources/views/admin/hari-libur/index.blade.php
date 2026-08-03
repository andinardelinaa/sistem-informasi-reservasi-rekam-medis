@extends('layouts.admin')
@section('header-title', 'Kelola Hari Libur')
@include('layouts.partials.alert')

@section('content')
    <!-- Statistik-->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Hari Libur</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $hariLibur->total() }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-times text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Libur Bulan Ini</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">
                        {{ $hariLibur->where('tanggal', '>=', now()->startOfMonth())->count() }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->format('M Y') }}</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-calendar-day text-white text-sm"></i>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status Klinik</p>
                    <p class="mt-2 text-lg font-bold text-green-700">Buka</p>
                    <p class="text-xs text-gray-400 mt-1">Operasional Normal</p>
                </div>
                <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                    <i class="fas fa-clinic-medical text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Table Header (Putih) -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <div class="flex flex-col gap-4">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar Hari Libur</h2>
                
                <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-3">
                    <!-- Search & Filter -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                        <!-- search -->
                        <div class="relative flex-grow sm:flex-grow-0">
                            <input type="text" id="searchInput" placeholder="Cari keterangan/tanggal..." 
                                class="w-full sm:w-56 pl-8 pr-3 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        <!-- filter tanggal  -->
                        <input type="date" 
                               id="filterTanggal" 
                               name="tanggal"
                               class="px-3 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm text-gray-700 w-full sm:w-auto">
                    </div>

                    <!-- form tambah -->
                    <div class="flex flex-wrap gap-2 w-full xl:w-auto justify-end">
                        <a href="{{ route('admin.hari-libur.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            <i class="fas fa-plus mr-2 text-[10px]"></i> Tambah
                        </a>
                        <!-- popup import excel -->
                        <button type="button" onclick="openImportModal()"
                                class="inline-flex items-center justify-center px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            <i class="fas fa-file-import mr-2 text-[10px]"></i> Import
                        </button>
                        <!-- export -->
                        <a href="{{ route('admin.hari-libur.export') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">
                            <i class="fas fa-file-excel mr-2 text-[10px]"></i> Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-white border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Tanggal Libur</th>
                        <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Keterangan</th>
                        <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    @forelse($hariLibur as $item)
                    @php
                        $no = ($hariLibur->currentPage() - 1) * $hariLibur->perPage() + $loop->iteration;
                        $formattedDate = \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y');
                        $rawDate = \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                    @endphp
                    <!-- ambil data database -->
                    <tr class="hover:bg-gray-50/50 transition-colors harilibur-row"
                        data-name="{{ strtolower($item->keterangan) }}" 
                        data-tanggal="{{ $rawDate }}"
                        data-id="{{ $item->id }}">
                        
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                            {{ $no }}.
                        </td>
                        <!-- tanggal -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-sm bg-red-50 flex items-center justify-center border border-red-100">
                                    <i class="fas fa-calendar-day text-red-500 text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $formattedDate }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider">{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd') }}</p>
                                </div>
                            </div>
                        </td>
                        <!--keterangan -->
                        <td class="px-5 py-4">
                            <span class="inline-block px-2 py-0.5 rounded-sm text-[10px] font-bold uppercase tracking-wider border
                                {{ \Carbon\Carbon::parse($item->tanggal)->isPast() ? 'bg-gray-100 text-gray-600 border-gray-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                {{ $item->keterangan }}
                            </span>
                        </td>
                        
                        <!-- aksi -->
                        <td class="px-5 py-4">
                            <div class="flex items-stretch gap-2 h-9">
                                <!-- edit -->
                                <a href="{{ route('admin.hari-libur.edit', $item->id_hari_libur) }}" 
                                class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                    <i class="fas fa-pen text-[10px] mr-1"></i>
                                    Edit
                                </a>
                                <!-- delete -->
                                <form action="{{ route('admin.hari-libur.destroy', $item->id_hari_libur) }}" 
                                    method="POST" 
                                    class="flex-1 h-full delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full h-full inline-flex items-center justify-center px-3 bg-white border border-red-200 hover:bg-red-50 text-red-700 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                        <i class="fas fa-trash text-[10px] mr-1"></i>
                                        Hapus
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center bg-gray-50">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                    <i class="fas fa-umbrella-beach text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada hari libur</p>
                                <p class="text-xs text-gray-500 mt-1">Klik tombol tambah untuk mengatur jadwal libur klinik.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="px-6 py-3 border-t border-gray-200 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-xs text-gray-500">
                Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $hariLibur->count() }}</span> dari <span class="font-bold text-gray-800">{{ $hariLibur->total() }}</span> data
            </p>
        </div>
            {{ $hariLibur->links('pagination::tailwind') }}
        </div>
    </div>

    <!-- modal import excel -->
    <div id="importModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeImportModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-sm bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                    
                    <!-- Modal Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider" id="modal-title">Import Data Hari Libur</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5 uppercase tracking-wider">Unggah file Excel untuk menambah data massal</p>
                        </div>
                        <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-6">
                        <!-- Info Card -->
                        <div class="bg-gray-50 border border-gray-200 rounded-sm p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5">
                                    <i class="fas fa-info-circle text-gray-500 text-sm"></i>
                                </div>
                                <div class="text-xs text-gray-700">
                                    <p class="font-bold mb-1 uppercase tracking-wider">Format file yang didukung:</p>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Ekstensi: <strong>.xlsx, .xls, .csv</strong></li>
                                        <li>Maksimal ukuran: <strong>5MB</strong></li>
                                        <li>Kolom wajib: <strong>tanggal</strong> (YYYY-MM-DD) dan <strong>keterangan</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <form action="{{ route('admin.hari-libur.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="relative">
                                <label for="file" class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Pilih File Excel</label>
                                
                                <div class="mt-1 flex justify-center rounded-sm border-2 border-dashed border-gray-300 px-6 py-8 hover:border-teal-500 hover:bg-teal-50/20 transition-colors cursor-pointer" 
                                     onclick="document.getElementById('file').click()"
                                     id="dropZone">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3" id="uploadIcon"></i>
                                        <div class="flex text-xs text-gray-600 justify-center">
                                            <span class="relative cursor-pointer rounded-sm bg-white font-bold text-teal-700 focus-within:outline-none hover:text-teal-500 uppercase tracking-wider">
                                                Klik untuk upload
                                            </span>
                                            <p class="pl-1 font-medium">atau drag & drop</p>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-2 uppercase tracking-wider" id="fileName">Maksimal 5MB</p>
                                    </div>
                                    <input id="file" name="file" type="file" class="sr-only" required onchange="handleFileSelect(this)">
                                </div>
                            </div>

                            @error('file')
                                <div class="mt-3 text-red-600 text-xs bg-red-50 border border-red-200 p-3 rounded-sm font-bold uppercase tracking-wider">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ $message }}
                                </div>
                            @enderror

                            <!-- button aksi -->
                            <div class="mt-6 flex flex-col sm:flex-row gap-3 sm:justify-end">
                                <button type="button" onclick="closeImportModal()"
                                        class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-bold rounded-sm hover:bg-gray-100 transition-colors text-xs uppercase tracking-wider">
                                    Batal
                                </button>
                                <button type="submit" id="submitImport"
                                        class="px-4 py-2 bg-slate-700 text-white font-bold rounded-sm hover:bg-slate-800 transition-colors text-xs uppercase tracking-wider inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-file-import text-[10px]"></i>
                                    <span>Import Data</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    //ambil elemen html
    const searchInput = document.getElementById('searchInput'); //search
    const filterTanggal = document.getElementById('filterTanggal'); //filter tanggal
    const tableBody = document.getElementById('tableBody'); //table
    const showingCount = document.getElementById('showingCount'); //teks data yg tampil 
    // semua row table disiman ke array agar bisa di filter
    const allRows = tableBody ? Array.from(tableBody.querySelectorAll('.harilibur-row')) : [];
    //funtion filter
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase(); //crai nama
        const dateVal = filterTanggal.value; //cari tanggal
        let visibleCount = 0; //hitung data yg muncul
        allRows.forEach(row => {
            //ambil data row
            const name = (row.dataset.name || '').toLowerCase(); 
            const tanggal = row.dataset.tanggal;
            //cek kecocokan
            const matchesSearch = !searchTerm || name.includes(searchTerm) || tanggal.includes(searchTerm);
            const matchesDate = !dateVal || tanggal === dateVal;
            //kalo cocok tampil, kalo ga hilang
            if (matchesSearch && matchesDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        //update jumlah data
        if(showingCount) showingCount.textContent = visibleCount;
    }
    //realtime filter
    searchInput.addEventListener('input', filterTable);
    filterTanggal.addEventListener('change', filterTable);

    // SweetAlert Delete
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //jangan langsung hapus
            //ambil nama dataa
            const hariLiburName = this.closest('tr')?.querySelector('td:nth-child(3) span')?.textContent?.trim() || 'data ini';
            //popup
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Hapus Hari Libur</h2>
                        <p style="font-size:13px; color:#6b7280;">Yakin hapus <b>${hariLiburName}</b>?</p>
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
                //submit bisa langsung hapus
                if (result.isConfirmed) form.submit();
            });
        });
    });
    filterTable();

    //error import file
    let errFile = @json($errors->first('file'));
    let errImport = @json(session('import_error'));
    let errGeneral = @json(session('error'));
    
    // Debugging
    console.log("Error Import:", errImport);
    console.log("Error General:", errGeneral);

    let pesanErrorImport = null;
    if (errFile) pesanErrorImport = errFile;
    else if (errImport) pesanErrorImport = errImport;
    else if (errGeneral) pesanErrorImport = errGeneral;

    if (pesanErrorImport) {
        //popup
        Swal.fire({
            width: 380,
            html: `
                <div class="text-center">
                    <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                        <i class="fas fa-exclamation-triangle" style="font-size:20px; color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                        Gagal Import Data
                    </h2>
                    <p style="font-size:13px; color:#6b7280; text-align:left;">
                        ${pesanErrorImport}
                    </p>
                </div>
            `,
            confirmButtonText: 'Oke, Mengerti',
            customClass: {
                popup: 'rounded-sm p-5',
                confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
            }
        });
    }
});

//open modal import
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
//tutup modal import
function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('importForm').reset(); //form direset
    document.getElementById('fileName').textContent = 'Maksimal 5MB';
    document.getElementById('uploadIcon').className = 'fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3';
    document.getElementById('dropZone').classList.remove('border-teal-500', 'bg-teal-50/20');
}
//validasi file upload
function handleFileSelect(input) {
    const file = input.files[0];
    const fileNameEl = document.getElementById('fileName');
    const uploadIcon = document.getElementById('uploadIcon');
    const dropZone = document.getElementById('dropZone'); 
    
    if (file) {
        const validExtensions = ['.xlsx', '.xls', '.csv']; //extension
        const fileName = file.name.toLowerCase();
        const isValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
        const isValidSize = file.size <= 5 * 1024 * 1024; //ukuran

        if (!isValidExtension) {
            Swal.fire({
                width: 380,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-file-excel" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Format File Salah</h2>
                        <p style="font-size:13px; color:#6b7280;">Hanya file dengan ekstensi <b>.xlsx</b>, <b>.xls</b>, atau <b>.csv</b> yang boleh diimport!</p>
                    </div>
                `,
                confirmButtonText: 'Oke, Mengerti',
                customClass: { popup: 'rounded-sm p-5', confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' }
            });
            input.value = ''; 
            fileNameEl.textContent = 'Maksimal 5MB';
            uploadIcon.className = 'fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3';
            dropZone.classList.remove('border-teal-500', 'bg-teal-50/20');
            return;
        }

        if (!isValidSize) {
            Swal.fire({
                width: 380,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-weight-hanging" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">Ukuran File Terlalu Besar</h2>
                        <p style="font-size:13px; color:#6b7280;">Maksimal ukuran file adalah <b>5MB</b>. Silakan kecilkan file Anda.</p>
                    </div>
                `,
                confirmButtonText: 'Oke, Mengerti',
                customClass: { popup: 'rounded-sm p-5', confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider' }
            });
            input.value = ''; 
            fileNameEl.textContent = 'Maksimal 5MB';
            uploadIcon.className = 'fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3';
            dropZone.classList.remove('border-teal-500', 'bg-teal-50/20');
            return;
        }

        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameEl.innerHTML = `<span class="font-bold text-gray-900">${file.name}</span> <span class="text-gray-500">(${fileSize} MB)</span>`;
        uploadIcon.className = 'fas fa-file-excel text-3xl text-green-600 mb-3';
        dropZone.classList.add('border-teal-500', 'bg-teal-50/20');
    }
}

// Drag & Drop
const dropZone = document.getElementById('dropZone');
if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    //drag
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) { dropZone.classList.add('border-teal-500', 'bg-teal-50/30'); }
    function unhighlight(e) { dropZone.classList.remove('border-teal-500', 'bg-teal-50/30'); }

    dropZone.addEventListener('drop', handleDrop, false);
    //drop
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        const fileInput = document.getElementById('file');
        fileInput.files = files;
        handleFileSelect(fileInput); //panggil ahar validasi handle tetap jalan
    }
}
//loading ketika import
document.getElementById('importForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('submitImport');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin text-[10px]"></i> <span>Mengimport...</span>';
});
</script>
@endsection