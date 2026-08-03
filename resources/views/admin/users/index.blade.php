@extends('layouts.admin')
@section('header-title', 'Kelola Pengguna')
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

        <!-- statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <!-- Total Pengguna -->
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total User</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $users->total() }}</p>
                        <p class="text-xs text-gray-400 mt-1">Terdaftar dalam sistem</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
            </div>
            <!-- Total Pasien -->
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Pasien</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $totalPasien }}</p>
                        <p class="text-xs text-gray-400 mt-1">Terdaftar</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-user-injured text-white text-sm"></i>
                    </div>
                </div>
            </div>
            <!-- Total Dokter -->
            <div class="bg-white shadow-sm p-6 border border-gray-200 rounded-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Total Dokter</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $totalDokter }}</p>
                        <p class="text-xs text-gray-400 mt-1">Terdaftar</p>
                    </div>
                    <div class="h-10 w-10 rounded-sm bg-teal-700 flex items-center justify-center">
                        <i class="fas fa-user-md text-white text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Container -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
            <!-- Header Utama (Putih) -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">Daftar User</h2>
            </div>

            <!-- Filter Bar -->
            <div class="px-6 py-3 border-b border-gray-200 bg-white">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Search -->
                        <div class="relative flex-grow sm:flex-grow-0">
                            <input type="text" id="searchInput" 
                                   placeholder="Cari nama atau email..." 
                                   class="w-full sm:w-56 pl-8 pr-3 py-2.5 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs"></i>
                            </div>
                        </div>
                        <!-- Filter Peran -->
                        <div class="cs-wrap w-full sm:w-60" id="roleFilter_wrap">
                            <button type="button" class="cs-btn">
                                <span>Semua Peran</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                            <div class="cs-list">
                                <div class="cs-item selected" data-val="">Semua Peran</div>
                                <div class="cs-item" data-val="admin">Admin</div>
                                <div class="cs-item" data-val="dokter">Dokter</div>
                                <div class="cs-item" data-val="pasien">Pasien</div>
                            </div>

                            <input type="hidden" id="roleFilter" name="roleFilter" value="">
                        </div>
                    </div>

                    <!-- Tambah Pengguna -->
                    <div class="flex justify-end w-full lg:w-auto">
                        <a href="{{ route('admin.users.create') }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors whitespace-nowrap">
                            <i class="fas fa-plus mr-2 text-[10px]"></i> Tambah
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table-->
            <div id="tableViewContent" class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-white border-b border-gray-200">
                        <tr> 
                            <!-- header -->
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-10">No</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Pengguna</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Peran</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Bergabung</th>
                            <th class="px-5 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest w-44">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="tableBody">
                        <!-- loop data -->
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors user-row" 
                            data-nama="{{ strtolower($user->nama ?? '') }}" 
                            data-email="{{ strtolower($user->email ?? '') }}" 
                            data-role="{{ implode(',', $user->getRoleNames()->toArray()) }}">
                            
                            <td class="px-5 py-4 text-center text-sm font-semibold text-gray-500">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                        
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900">{{ $user->nama ?? '—' }}</p>
                                <p class="text-[10px] text-gray-500 font-bold tracking-wider mt-0.5">{{ $user->email }}</p>
                            </td>
                            
                            <td class="px-5 py-4">
                                @if($user->getRoleNames()->isNotEmpty())
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-800 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">
                                            {{ ucfirst($role) }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-sm border border-gray-200 uppercase tracking-wider">Tidak Ada</span>
                                @endif
                            </td>
                            <!-- status verifikasi -->
                            <td class="px-5 py-4">
                                <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded-sm border uppercase tracking-wider
                                    {{ $user->hasVerifiedEmail() ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
                                    {{ $user->hasVerifiedEmail() ? 'Terverifikasi' : 'Belum Verifikasi' }}
                                </span>
                            </td>
                            <!-- tanggal buat akun -->
                            <td class="px-5 py-4 text-sm text-gray-700 font-medium">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <!-- aksi -->
                            <td class="px-5 py-4">
                                <div class="flex items-stretch gap-2 h-9 justify-center">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                       class="flex-1 h-full inline-flex items-center justify-center px-3 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider">
                                        <i class="fas fa-pen text-[10px] mr-1"></i>
                                        Edit
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="flex-1 h-full delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                class="w-full h-full inline-flex items-center justify-center px-3 bg-white border border-red-200 hover:bg-red-50 text-red-700 rounded-sm text-xs font-semibold transition-colors uppercase tracking-wider delete-btn">
                                            <i class="fas fa-trash text-[10px] mr-1"></i>
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mb-4">
                                        <i class="fas fa-users-slash text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada data user</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="border-t border-gray-200 bg-white">
                <div class="px-6 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span id="showingCount" class="font-bold text-gray-800">{{ $users->count() }}</span> dari 
                        <span class="font-bold text-gray-800">{{ $users->total() }}</span> users
                    </p>
                    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $users->links('pagination::tailwind') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// dropdown custom
function initCS(id) {
    // ambil elemen html
    const wrap = document.getElementById(id + '_wrap');
    if (!wrap) return null;
    const btn = wrap.querySelector('.cs-btn'); //mengambil rombol dropdown
    const list = wrap.querySelector('.cs-list'); //ambil list pilihan
    const input = document.getElementById(id); //ambil hidden input
    if (!btn || !list || !input) return null; //validasi jika elemen tidak ada
    //jalankan fungsi ketika tombol di klik
    btn.addEventListener('click', () => {
        if (btn.disabled) return;
        //tutup dropdown lain
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        //klik sekali buka, klik lagi tutup
        list.classList.toggle('active');
        btn.classList.toggle('active');
    });
    // klik diluar dropdown, tutup
    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });
    // klik salah satu item
    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => { //saat item di klik
            input.value = item.dataset.val; //simpan value
            btn.querySelector('span').innerHTML = item.innerHTML; //ubah tulisan tombol
            list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
            item.classList.add('selected'); //tandai item terpilih
            list.classList.remove('active'); //tutup dropdown
            btn.classList.remove('active');
            input.dispatchEvent(new Event('change'));
        });
    });
    // saat halaman di reload pilihan tidak hilang
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
initCS('roleFilter');
//search dan filter
document.addEventListener('DOMContentLoaded', function() {
    //ambil elemen html
    const searchInput = document.getElementById('searchInput'); //search
    const roleFilter = document.getElementById('roleFilter'); //filter
    const tableRows = document.querySelectorAll('#tableBody .user-row'); //  baris tabel
    const showingCount = document.getElementById('showingCount'); //jumlah data yg tampil

    function performSearchAndFilter() {
        const searchTerm = searchInput.value.toLowerCase().trim();   //ambil keyword
        const roleTerm = roleFilter.value.toLowerCase(); //ambil role
        let visibleCount = 0; //hitung data yg tampil
        //loop semua user
        tableRows.forEach(row => {
            // ambil data row
            const nama = (row.dataset.nama || '').toLowerCase();
            const email = (row.dataset.email || '').toLowerCase();
            const roles = (row.dataset.role || '').toLowerCase();
            //cocokan serach dan role
            const matchesSearch = !searchTerm || nama.includes(searchTerm) || email.includes(searchTerm);
            const matchesRole = !roleTerm || roles.includes(roleTerm);
            // kalo cocok tampilkan kalo tidak sembunyikan
            if (matchesSearch && matchesRole) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        //update jumlah data
        if(showingCount) showingCount.textContent = visibleCount;
    }
    //event search realtime
    searchInput.addEventListener('input', performSearchAndFilter);
    roleFilter.addEventListener('change', performSearchAndFilter);

    // Delete Confirmation 
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); //jangan langsung hapus
            const form = this.closest('form'); //ambil form
            //ambil nama username
            const userName = this.closest('tr')?.querySelector('p.font-bold')?.textContent?.trim() || 'data ini';
            //popup
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
                            <i class="fas fa-trash" style="font-size:20px; color:#dc2626;"></i>
                        </div>
                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Hapus User
                        </h2>
                        <p style="font-size:13px; color:#6b7280;">
                            Yakin hapus user <b>${userName}</b>?
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
                //form submit jika pilih ya
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Jika Gagal Hapus Karena FK (User masih punya relasi data pasien/dokter/reservasi)
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

    performSearchAndFilter();
});
</script>
@endsection