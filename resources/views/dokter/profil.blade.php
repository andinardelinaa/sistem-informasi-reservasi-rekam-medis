@extends('layouts.dokter')
@section('header-title', 'Profil Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- Main Card Container -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <!-- Card Header -->
        <div class="bg-teal-700 px-6 py-5 text-white">
            <h2 class="text-lg font-bold uppercase tracking-wider">Informasi Profil</h2>
            <p class="text-sm text-teal-100 mt-1 font-light">Data akun dan identitas dokter</p>
        </div>

        <div class="p-6 lg:p-8">
            <div class="flex flex-col md:flex-row gap-8">
                
                <!-- Foto Profil & Identitas Dasar -->
                <div class="flex-shrink-0 text-center md:text-left border-b md:border-b-0 md:border-r border-gray-200 pb-6 md:pb-0 md:pr-8">
                    <div class="inline-block">
                        <!-- ambil huruf depan nama untuk profil -->
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->nama }}&size=150&background=0f766e&color=fff&bold=true&font-size=0.4"
                             alt="Profile Photo" 
                             class="w-28 h-28 rounded-sm mx-auto md:mx-0 object-cover border-2 border-gray-200 shadow-sm">
                    </div>
                    <div class="mt-4">
                        <!-- ambil nama dokter login dan spesialisasinya -->
                        <h3 class="text-lg font-bold text-gray-900 uppercase tracking-wide">{{ auth()->user()->nama }}</h3>
                        <p class="text-sm text-teal-700 font-semibold mt-1">{{ auth()->user()->dokter?->spesialisasi ?? 'Dokter Umum' }}</p>
                    </div>
                </div>

                <!-- Informasi Detail -->
                <div class="flex-1">
                    <div class="divide-y divide-gray-100 border-t border-gray-100">
                        <div class="flex items-center justify-between py-4 px-1">
                            <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Email</span>
                            <span class="text-sm text-gray-900 font-medium">{{ auth()->user()->email }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-4 px-1">
                            <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Spesialisasi</span>
                            <span class="text-sm text-gray-900 font-medium">{{ auth()->user()->dokter?->spesialisasi ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-4 px-1">
                            <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Nomor HP</span>
                            <span class="text-sm text-gray-900 font-medium">{{ auth()->user()->dokter?->no_hp ?? '-' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between py-4 px-1">
                            <span class="text-xs text-gray-500 uppercase tracking-widest font-bold">Password</span>
                            <span class="text-sm text-gray-900 font-medium tracking-widest">••••••••</span>
                        </div>
                    </div>
                    <!-- modal edit -->
                    <div class="mt-6 flex justify-end">
                        <button onclick="openEditModal()" 
                                class="inline-flex items-center px-5 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-sm font-bold transition-colors uppercase tracking-wider">
                            Ubah Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profi -->
<div id="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
    <div class="bg-white rounded-sm shadow-xl max-w-md w-full mx-4 overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Edit Profil</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui informasi akun Anda</p>
            </div>
            <!-- close edit -->
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('dokter.profil.update') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <!-- nama -->
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ auth()->user()->nama }}" required
                           class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                </div>
                <div>
                    <!-- email readonly -->
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}" disabled
                           class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                </div>
                <div>
                    <!-- spesialisasi -->
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Spesialisasi</label>
                    <input type="text" name="spesialisasi" value="{{ auth()->user()->dokter?->spesialisasi ?? '' }}"
                           class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                </div>
                <div>
                    <!-- nomor hp -->
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Nomor HP</label>
                    <input type="tel" name="no_hp" value="{{ auth()->user()->dokter?->no_hp ?? '' }}"
                           class="w-full border border-gray-300 rounded-sm px-3 py-2.5 focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm font-medium transition-colors">
                </div>
            </div>
            <!-- button batal dan simpan -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 rounded-sm text-xs font-bold transition-colors uppercase tracking-wider">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold transition-colors uppercase tracking-wider">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
//function buka edit modal
function openEditModal() {
    //ambil elemen html
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
//close modal edit
function closeEditModal() {
    //ambil elemen html
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}
//function dijalankan ketika button di klik
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
//tombol keyboard ditekan event dijalankan
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeEditModal();
    }
});
</script>
@endsection