@extends('layouts.pasien')

@section('header-title', 'Form Reservasi - ' . ucfirst($klinik->nama ?? 'Klinik'))

<style>
/* Custom Dropdown Styling - Flat & Professional */
.custom-dropdown-trigger {
    width: 100%;
    padding: 0.625rem 0.75rem; /* py-2.5 px-3 */
    background-color: white;
    border: 1px solid #d1d5db; /* gray-300 */
    border-radius: 2px; /* rounded-sm */
    font-size: 0.875rem;
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.15s ease;
    cursor: pointer;
    min-height: 38px;
    color: #1f2937;
}

.custom-dropdown-trigger:focus, .custom-dropdown-trigger.active {
    outline: none;
    border-color: #14b8a6; /* teal-500 */
    box-shadow: 0 0 0 1px #14b8a6;
}

.custom-dropdown-trigger.disabled {
    background-color: #f9fafb;
    cursor: not-allowed;
    opacity: 0.7;
}

.custom-dropdown-content {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb; /* gray-200 */
    border-radius: 2px; /* rounded-sm */
    box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1), 0 1px 2px -1px rgba(0,0,0,0.1);
    z-index: 50;
    max-height: 280px;
    overflow-y: auto;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-4px);
    transition: all 0.15s ease;
    padding: 8px;
}

.custom-dropdown-content.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Grid Layout Schedule */
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
}

.schedule-card {
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.15s ease;
    background: white;
    text-align: center;
}

.schedule-card:hover {
    border-color: #14b8a6;
    background-color: #f0fdfa;
}

.schedule-card.selected {
    border-color: #0f766e; /* teal-700 */
    background-color: #f0fdfa;
    box-shadow: 0 0 0 1px #0f766e;
}

.doctor-name {
    font-weight: 700;
    color: #1f2937;
    font-size: 11px;
    margin-bottom: 2px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.time-slot {
    font-size: 10px;
    color: #6b7280;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.no-data, .loading-state {
    padding: 24px;
    text-align: center;
    color: #6b7280;
    grid-column: 1 / -1;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(15, 118, 110, 0.2);
    border-radius: 50%;
    border-top-color: #0f766e;
    animation: spin 0.8s linear infinite;
    margin-bottom: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 640px) {
    .schedule-grid {
        grid-template-columns: 1fr;
    }
}
</style>

@section('content')
<section class="min-h-screen bg-slate-50 py-8 md:py-12">
    <div class="container mx-auto px-4 max-w-5xl">
        
        <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <!-- ambil nama klinik -->
                    <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Form Reservasi</h1>
                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($klinik->nama ?? 'Klinik') }}</p>
                </div>
                <div class="flex items-center gap-2 text-teal-700">
                    <i class="fas fa-calendar-plus text-sm"></i>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Reservasi</span>
                </div>
            </div>
            
            <!-- Form Content -->
            <div class="p-6 lg:p-8">
                <form action="{{ route('pasien.reservasi.store') }}" method="POST" id="reservasiForm">
                    @csrf
                    <input type="hidden" name="klinik_id" value="{{ $klinik->id_klinik }}">
                    <input type="hidden" id="selected_jadwal" name="id_jadwal" value="">
                    
                    <!-- Section Pilih Jadwal -->
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Pilih Jadwal</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 mb-8">
                        <div>
                            <!-- pilih tgl kunjungan -->
                            <label for="tgl_reservasi" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tanggal Kunjungan <span class="text-red-500">*</span></label>
                            <input type="date" id="tgl_reservasi" name="tgl_reservasi"
                                value="{{ old('tgl_reservasi') }}"
                                required
                                class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                            @error('tgl_reservasi')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <!-- pilih jadwal -->
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Pilih Jadwal <span class="text-red-500">*</span></label>
                            <div class="custom-dropdown relative">
                                <div id="dropdown-trigger" class="custom-dropdown-trigger">
                                    <span id="dropdown-placeholder" class="text-gray-400 text-sm">-- Pilih tanggal terlebih dahulu --</span>
                                    <i class="fas fa-chevron-down text-gray-400 text-[10px] transition-transform" id="dropdown-icon"></i>
                                </div>
                                <div id="dropdown-content" class="custom-dropdown-content">
                                    <div class="schedule-grid">
                                        <div class="loading-state">
                                            <div class="spinner"></div>
                                            <p>Memuat jadwal...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('id_jadwal')
                                <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section Keluhan -->
                    <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Keluhan Pasien</h3>

                    <div class="mb-8">
                        <!-- input keluhan -->
                        <label for="keluhan" class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Keluhan</label>
                        <textarea id="keluhan" name="keluhan" rows="3"
                                  class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors resize-none placeholder:text-gray-400"
                                  placeholder="Jelaskan keluhan gigi Anda (opsional)">{{ old('keluhan') }}</textarea>
                        @error('keluhan')
                            <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- aksi -->
                    <div class="pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('pasien.pilih.klinik') }}"
                           class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">
                            Batal
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors inline-flex items-center justify-center gap-2">
                            <span>Konfirmasi Reservasi</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="text-center mt-6">
            <p class="text-[10px] text-gray-400 uppercase tracking-wider">
                &copy; Klinik Gigi Dokter Ndeso
            </p>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// jalankan kode setelah html siap
document.addEventListener('DOMContentLoaded', function() {
    // ambil elemen
    const tanggalInput = document.getElementById('tgl_reservasi');
    const dropdownTrigger = document.getElementById('dropdown-trigger'); 
    const dropdownContent = document.getElementById('dropdown-content');
    const dropdownPlaceholder = document.getElementById('dropdown-placeholder');
    const dropdownIcon = document.getElementById('dropdown-icon');
    const selectedJadwalInput = document.getElementById('selected_jadwal');
    
    const form = document.getElementById('reservasiForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    // nonaktifkan tombol submit saat ditekan
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        //loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-[10px]"></i> Memproses...';
    });
    
    // membuka dan menutup dropdown
    dropdownTrigger.addEventListener('click', function() {
        if (dropdownContent.classList.contains('show')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });
    
    // tutup dropdown jika klik diluar dropdown
    document.addEventListener('click', function(event) {
        if (!dropdownTrigger.contains(event.target) && !dropdownContent.contains(event.target)) {
            closeDropdown();
        }
    });
    // function buka dropdown
    function openDropdown() {
        dropdownContent.classList.add('show');
        dropdownTrigger.classList.add('active');
        dropdownIcon.style.transform = 'rotate(180deg)'; //ikon panah putar ke bawah
    }
    //function tutup dropdown
    function closeDropdown() {
        dropdownContent.classList.remove('show');
        dropdownTrigger.classList.remove('active');
        dropdownIcon.style.transform = 'rotate(0deg)'; //ikon panah kembali normal
    }
    //buat objek tanggal hari ini dan batas paling akhir dipilih
    const today = new Date();
    const maxDate = new Date();
    //ambil nilai laravel
    const maxTanggalStr = '{{ $maxTanggal ?? "" }}';
    if (maxTanggalStr) { //jika max tanggal ada, jika tidak maka otomatis hari ini +7
        const [y, m, d] = maxTanggalStr.split('-').map(Number);
        maxDate.setFullYear(y, m - 1, d);
    } else {
        maxDate.setDate(today.getDate() + 7);
    }
    //function format tanggal
    function formatDate(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    // membatasi input tanggal
    tanggalInput.setAttribute('min', formatDate(today));
    tanggalInput.setAttribute('max', formatDate(maxDate));
    
    // saat tanggal berubah function ini dijalankan
    tanggalInput.addEventListener('change', function() {
        const tanggal = this.value; //ambil tanggal
        if (!tanggal) return; //kaalo kosong berhenti
        //memuat jadwal
        dropdownPlaceholder.textContent = 'Memuat jadwal...';
        dropdownPlaceholder.classList.add('text-gray-400'); //ubah warna tulisan
        dropdownPlaceholder.classList.remove('text-gray-800');
        dropdownTrigger.disabled = true; //nonaktifkan dropdown
        selectedJadwalInput.value = '';
        //hilangkan selected
        document.querySelectorAll('.schedule-card').forEach(card => {
            card.classList.remove('selected');
        });
        //mengambil jadwal ke be pake ajax
        fetch(`{{ route('pasien.jadwal.available') }}?tanggal=${tanggal}&klinik_id={{ $klinik->id_klinik }}`)
            .then(response => response.json()) //respon diterima
            .then(data => {
                const gridContainer = dropdownContent.querySelector('.schedule-grid');
                gridContainer.innerHTML = ''; //mengosongkan isi dropdow lama
                //kalo be error
                if (data.error) {
                    gridContainer.innerHTML = `
                        <div class="no-data" style="color: #b91c1c;">
                            <i class="fas fa-exclamation-triangle text-lg mb-2"></i>
                            <p>${data.error}</p>
                        </div>
                    `;
                    dropdownPlaceholder.textContent = 'Terjadi kesalahan';
                    dropdownTrigger.disabled = false;
                    return;
                }
                //jika tidak ada jadwal
                if (data.length === 0) {
                    gridContainer.innerHTML = `
                        <div class="no-data">
                            <i class="fas fa-calendar-times text-lg mb-2 text-gray-300"></i>
                            <p>Tidak ada jadwal tersedia</p>
                        </div>
                    `;
                    dropdownPlaceholder.textContent = 'Tidak ada jadwal tersedia';
                } else { //jika ada jadwal
                    data.forEach(jadwal => {
                        const scheduleCard = document.createElement('div'); //membuat card
                        scheduleCard.className = 'schedule-card';
                        //menympan data
                        scheduleCard.dataset.value = `${jadwal.id_jadwal}|${jadwal.jam_mulai}-${jadwal.jam_selesai}|${jadwal.antrean}`;
                        //isi card
                        scheduleCard.innerHTML = `
                            <div class="time-slot">
                                <i class="far fa-clock text-teal-700"></i>
                                ${jadwal.jam_mulai} - ${jadwal.jam_selesai}
                            </div>
                            <div class="doctor-name">
                                ${jadwal.dokter_nama}
                            </div>
                        `;
                        //ketika card di klik
                        scheduleCard.addEventListener('click', function() {
                            document.querySelectorAll('.schedule-card').forEach(card => { //hilangkan selected semua card
                                card.classList.remove('selected');
                            });
                            //beri warna pada card yang dipilih
                            this.classList.add('selected');
                            //mengambil nama dokter dan jam dlot
                            const doctorName = jadwal.dokter_nama;
                            const timeSlot = `${jadwal.jam_mulai} - ${jadwal.jam_selesai}`;
                            //menampilkan pilihan di dropdown
                            dropdownPlaceholder.textContent = `${doctorName}, ${timeSlot}`;
                            dropdownPlaceholder.classList.remove('text-gray-400');
                            dropdownPlaceholder.classList.add('text-gray-800');
                            //menyimpan jadwal yang dipilih
                            selectedJadwalInput.value = this.dataset.value;
                            //menutup dropdown
                            closeDropdown();
                        });
                        //menambah card kedalam container
                        gridContainer.appendChild(scheduleCard);
                    });
                    //aktifkan dropdown kembali
                    dropdownTrigger.disabled = false;
                }
            })
            //jika fetch gagal
            .catch(error => {
                //menampilkan error di console
                console.error('Error:', error);
                const gridContainer = dropdownContent.querySelector('.schedule-grid');
                gridContainer.innerHTML = `
                    <div class="no-data" style="color: #b91c1c;">
                        <i class="fas fa-exclamation-triangle text-lg mb-2"></i>
                        <p>Terjadi kesalahan memuat jadwal</p>
                    </div>
                `;
                dropdownPlaceholder.textContent = 'Terjadi kesalahan';
                dropdownTrigger.disabled = false;
            });
    });
    //sweetalert popup
    @if(session('error'))
    Swal.fire({
        width: 340,
        html: `
            <div class="text-center">
                <div style="
                    width:48px;
                    height:48px;
                    background:#fef2f2;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    margin:0 auto 12px;
                    border-radius:2px;
                    border:1px solid #fecaca;
                ">
                    <i class="fas fa-exclamation-circle" style="font-size:20px; color:#dc2626;"></i>
                </div>

                    <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                        Gagal
                    </h2>

                    <p style="font-size:13px; color:#6b7280;">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        `,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-sm p-5',
            confirmButton: 'px-4 py-2 bg-teal-700 hover:bg-teal-800 text-white text-xs rounded-sm font-bold uppercase tracking-wider'
        }
    });
    @endif
});
</script>
@endsection