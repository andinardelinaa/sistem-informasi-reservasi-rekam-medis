@extends('layouts.dokter')
@section('title', 'Jadwal Praktik')
@section('header-title', 'Jadwal Praktik')

@section('content')
<!-- CSS Custom Select -->
<style>
.cs-wrap { position: relative; }
.cs-btn { width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; background: #fff; border: 1px solid #d1d5db; border-radius: 2px; font-size: 0.875rem; text-align: left; cursor: pointer; transition: all 0.15s; }
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
    <!-- jadwal praktik pribadi -->
    <div class="bg-white shadow-sm border border-gray-200 overflow-hidden rounded-sm">
        <div class="bg-teal-700 px-6 py-5 text-white">
            <h2 class="text-lg font-bold uppercase tracking-wider">Jadwal Praktik Saya</h2>
            <!-- dokter yang login -->
            <p class="text-sm text-teal-100 mt-1 font-light">
                {{ auth()->user()->name }} — {{ auth()->user()->dokter->spesialisasi ?? '-' }}
            </p>
        </div>

        @php
            $dokterSayaId = auth()->user()->dokter->id_dokter ?? null;
            $mySchedules = $jadwal->where('id_dokter', $dokterSayaId)->whereNull('tanggal');
            $hariOrder = ['senin','selasa','rabu','kamis','jumat','sabtu','minggu'];
        @endphp
        <!-- jika jadwal saya tidak kosong, tampilkan -->
        @if($mySchedules->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr> 
                            <!-- header -->
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Hari</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Jam Praktik</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-widest">Klinik</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <!-- loop berdasarkan hari -->
                        @foreach($hariOrder as $hari)
                            @php
                                $slot = $mySchedules->firstWhere('hari', ucfirst($hari)) 
                                      ?? $mySchedules->firstWhere('hari', strtolower($hari));
                            @endphp
                            @if($slot)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- hari -->
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 text-sm uppercase tracking-wider">
                                    {{ ucfirst($hari) }}
                                </td>
                                <!-- jam mulai dan jam selesai -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-medium">
                                    {{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i') }} WIB
                                </td>
                                <!-- nama klinik -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $slot->klinik->nama_klinik ?? '-' }}
                                </td>
                                <!-- aktif -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2.5 py-1 inline-block text-[10px] font-bold rounded-sm bg-teal-600 text-white uppercase tracking-widest">
                                        Aktif
                                    </span>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
        <!-- jik jadwal saya kosong -->
            <div class="py-16 text-center border-t border-gray-100">
                <div class="w-16 h-16 bg-gray-50 border border-gray-200 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum memiliki jadwal praktik</p>
                <p class="text-xs text-gray-500 mt-1">Hubungi administrator untuk penjadwalan</p>
            </div>
        @endif
    </div>
    <!-- jika ada peurbahan jadwal, tampilkan -->
    @if(isset($perubahanJadwal) && $perubahanJadwal->count())
    <div class="bg-white shadow-sm border border-amber-200 overflow-hidden rounded-sm">
        <div class="bg-amber-500 px-6 py-4 text-white">
            <h3 class="text-sm font-bold uppercase tracking-wider">
                Perubahan Jadwal Praktik
            </h3>
        </div>
        <!-- loop perubahan jadwal -->
        <div class="divide-y divide-amber-100">
            @foreach($perubahanJadwal as $item)
                <div class="px-6 py-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        
                        <div>
                            <!-- ambil tanggal perubahan -->
                            <p class="font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}
                            </p>
                            <!-- jam praktik -->
                            <p class="text-sm text-gray-500">
                                {{ $item->jam_praktik }}
                            </p>
                        </div>

                        <div>
                            <!-- jika dokter yg digantikan -->
                            @if($item->dokter_id == $dokterSayaId)
                                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-sm">
                                    Digantikan oleh {{ $item->dokterPengganti->nama }}
                                </span>
                            <!-- dokter pengganti -->
                            @elseif($item->dokter_pengganti_id == $dokterSayaId)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-sm">
                                    Menggantikan {{ $item->dokter->nama }}
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- semua jadwal dokter -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        <!-- header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
                <h2 class="text-base font-bold text-gray-800 uppercase tracking-wider">
                    Semua Jadwal Dokter
                </h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                    <!-- serach -->
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="text" id="searchInput" 
                               placeholder="Cari dokter / klinik..." 
                               class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 bg-white text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- filter hari -->
                    <div class="cs-wrap w-full sm:w-48" id="hariFilter_wrap">
                        <button type="button" class="cs-btn">
                            <span>Semua Hari</span>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                        </button>

                        <div class="cs-list">
                            <div class="cs-item selected" data-val="">Semua Hari</div>
                            <div class="cs-item" data-val="senin">Senin</div>
                            <div class="cs-item" data-val="selasa">Selasa</div>
                            <div class="cs-item" data-val="rabu">Rabu</div>
                            <div class="cs-item" data-val="kamis">Kamis</div>
                            <div class="cs-item" data-val="jumat">Jumat</div>
                            <div class="cs-item" data-val="sabtu">Sabtu</div>
                            <div class="cs-item" data-val="minggu">Minggu</div>
                        </div>

                        <input type="hidden" id="hariFilter" name="hariFilter" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- jadwal grid, ambil template jadwwal. loop template -->
        <div id="allSchedules" class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
            @php
                $templateJadwal = $jadwal->whereNull('tanggal');
                $groupedJadwal = [];
                foreach($templateJadwal as $item) {
                    $klinikId = $item->id_klinik ?? 0;
                    $hari = strtolower($item->hari ?? 'unknown');
                    $groupedJadwal[$klinikId]['nama_klinik'] = $item->klinik->nama_klinik ?? 'Klinik Tidak Diketahui';
                    $groupedJadwal[$klinikId]['hari'][$hari][] = $item;
                }
            @endphp
            <!-- grup by klinik -->
            @forelse($groupedJadwal as $klinikId => $klinik)
            <div class="schedule-card bg-white border border-gray-200 rounded-sm overflow-hidden" data-klinik="{{ strtolower($klinik['nama_klinik']) }}">
                <div class="bg-white px-5 py-3 border-b border-gray-200">
                    <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">
                        {{ $klinik['nama_klinik'] }}
                    </h3>
                </div>
                <!-- grup by hari -->
                <div class="p-5 space-y-5">
                    @foreach($hariOrder as $hari)
                        @if(!empty($klinik['hari'][$hari]))
                        <div class="hari-group" data-hari="{{ $hari }}">
                            <h4 class="font-bold text-gray-500 mb-3 text-[11px] uppercase tracking-widest border-b border-gray-100 pb-2">
                                {{ ucfirst($hari) }}
                            </h4>
                            <div class="space-y-2">
                                @foreach($klinik['hari'][$hari] as $item)
                                    <div class="jadwal-item bg-white hover:bg-gray-50 border border-gray-200 rounded-sm p-3 transition-colors"
                                         data-dokter="{{ strtolower($item->dokter->nama ?? '') }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <p class="font-bold text-sm text-gray-900">{{ $item->dokter->nama ?? '-' }}</p>
                                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-1.5">
                                                    <span class="font-medium text-gray-800">
                                                        {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} – {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                                    </span>
                                                    @if($item->dokter->spesialisasi ?? false)
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-sm text-[10px] font-bold uppercase tracking-wider border border-gray-200">
                                                        {{ $item->dokter->spesialisasi }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center text-gray-500 empty-state border border-dashed border-gray-200 rounded-sm bg-gray-50/50">
                <div class="w-16 h-16 bg-white border border-gray-200 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-times text-gray-300 text-2xl"></i>
                </div>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">Belum ada jadwal dokter</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('scripts')

<script>
//custom drodown
function initCS(id) {
    // ambil elemen html
    const wrap = document.getElementById(id + '_wrap');
    if (!wrap) return null;
    const btn = wrap.querySelector('.cs-btn');
    const list = wrap.querySelector('.cs-list');
    const input = document.getElementById(id);
    if (!btn || !list || !input) return null;
    //fungsi dijalankan ketika button di klik
    btn.addEventListener('click', () => {
        if (btn.disabled) return;
        document.querySelectorAll('.cs-list.active').forEach(l => { if (l !== list) l.classList.remove('active'); });
        document.querySelectorAll('.cs-btn.active').forEach(b => { if (b !== btn) b.classList.remove('active'); });
        list.classList.toggle('active');
        btn.classList.toggle('active');
    });
    //buka tutup dropdown
    document.addEventListener('click', e => {
        if (!wrap.contains(e.target)) { list.classList.remove('active'); btn.classList.remove('active'); }
    });
    //pilih list 
    list.querySelectorAll('.cs-item').forEach(item => {
        item.addEventListener('click', () => { //klik pilihan
            input.value = item.dataset.val; //simpan ke hidden value
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
initCS('hariFilter');

// filter dan serach
function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim(); //ambil keyword
    const selectedHari = document.getElementById('hariFilter').value.toLowerCase(); //ambil hari selected
    //loop jadwal
    document.querySelectorAll('.jadwal-item').forEach(item => {
        //amnil elemen
        const dokterName = item.dataset.dokter || ''; //ambil nama dokter
        const hariGroup = item.closest('.hari-group');  //cari  grup hari
        const hari = hariGroup ? hariGroup.dataset.hari.toLowerCase() : ''; //ambil nama hari
        const klinikName = item.closest('.schedule-card')?.dataset.klinik || ''; //ambil nama klinik
        //cek kecocokan
        const matchSearch = searchTerm === '' || dokterName.includes(searchTerm) || klinikName.includes(searchTerm);
        const matchHari = selectedHari === '' || hari === selectedHari;
        //tampilkan atau sembunyikan jadwal
        item.style.display = (matchSearch && matchHari) ? '' : 'none';
    });
    //mengecek setiap grup hari
    document.querySelectorAll('.hari-group').forEach(group => {
        const hasVisible = Array.from(group.querySelectorAll('.jadwal-item')).some(i => i.style.display !== 'none');
        group.style.display = hasVisible ? 'block' : 'none';
    });
    //menampilkan atau mneyembunyikan grup hari
    document.querySelectorAll('.schedule-card').forEach(card => {
        const hasVisibleHari = Array.from(card.querySelectorAll('.hari-group')).some(g => g.style.display !== 'none');
        card.style.display = hasVisibleHari ? 'block' : 'none';
    });
}
//realtime
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('hariFilter').addEventListener('change', applyFilters);
</script>
@endsection