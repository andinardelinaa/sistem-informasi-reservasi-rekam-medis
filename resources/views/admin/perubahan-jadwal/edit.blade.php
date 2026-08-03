@extends('layouts.admin')

@section('header-title', 'Edit Perubahan Jadwal')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Breadcrumb -->
    <nav class="flex text-gray-500 text-xs uppercase tracking-wider" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.perubahan-jadwal.index') }}" class="hover:text-teal-700 transition-colors font-bold">Perubahan Jadwal</a>
            </li>
            <li>
                <div class="flex items-center">
                    <span class="text-gray-300 mx-2">/</span>
                    <span class="text-gray-400 font-medium">Edit Data</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Main Card Container -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-sm">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider">Edit Perubahan Jadwal</h1>
                <p class="text-xs text-gray-500 mt-1">Ubah detail perubahan jadwal dokter</p>
            </div>
            <a href="{{ route('admin.perubahan-jadwal.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors self-start md:self-center">
                <i class="fas fa-arrow-left mr-2 text-[10px]"></i> Kembali
            </a>
        </div>

        @if(session('error'))
        <div class="px-6 py-3 border-b border-red-200 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-[10px]"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Form Content -->
        <div class="p-6 lg:p-8">
            <form action="{{ route('admin.perubahan-jadwal.update', $item->id_perubahan) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="klinik_id" name="klinik_id" value="{{ $klinikId ?? '' }}">

                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Informasi Utama</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-10 gap-y-6 mb-8">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Tanggal Berlaku <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal" name="tanggal" required value="{{ old('tanggal', $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') : '') }}" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Ubah tanggal untuk memuat ulang jadwal</p>
                        @error('tanggal') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Dokter Custom Select -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Dokter <span class="text-red-500">*</span></label>
                        <div class="cs-wrap" id="dokter_id_wrap">
                            <button type="button" class="cs-btn" disabled>
                                <span>Memuat...</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                            <div class="cs-list"></div>
                            <input type="hidden" id="dokter_id" name="dokter_id" required value="{{ old('dokter_id', $item->dokter_id) }}">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-wider">Dokter yang terjadwal di klinik</p>
                        @error('dokter_id') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Jenis Perubahan Custom Select (Tanpa Ikon) -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Jenis Perubahan <span class="text-red-500">*</span></label>
                        <div class="cs-wrap" id="jenis_perubahan_wrap">
                            <button type="button" class="cs-btn">
                                <span>-- Pilih Jenis --</span>
                                <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                            <div class="cs-list">
                                <div class="cs-item" data-val="libur">Libur</div>
                                <div class="cs-item" data-val="pengganti">Ganti Dokter</div>
                                <div class="cs-item" data-val="tambahan">Tambahan</div>
                            </div>
                            <input type="hidden" id="jenis_perubahan" name="jenis_perubahan" required value="{{ old('jenis_perubahan', $item->jenis_perubahan) }}">
                        </div>
                        @error('jenis_perubahan') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                </div>

                <h3 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4 border-b border-gray-200 pb-2">Rincian Perubahan</h3>

                <div class="space-y-6 mb-8">
                    <!-- Pengganti Custom Select -->
                    <div id="field_dokter_pengganti" class="{{ old('jenis_perubahan', $item->jenis_perubahan) != 'pengganti' ? 'hidden' : '' }}">
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Dokter Pengganti <span class="text-red-500">*</span></label>
                        <div class="md:w-1/2">
                            <div class="cs-wrap" id="dokter_pengganti_id_wrap">
                                <button type="button" class="cs-btn" disabled>
                                    <span>Memuat...</span>
                                    <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                                </button>
                                <div class="cs-list"></div>
                                <input type="hidden" id="dokter_pengganti_id" name="dokter_pengganti_id" value="{{ old('dokter_pengganti_id', $item->dokter_pengganti_id) }}">
                            </div>
                        </div>
                        @error('dokter_pengganti_id') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <div id="field_jam" class="{{ old('jenis_perubahan', $item->jenis_perubahan) != 'tambahan' ? 'hidden' : '' }} grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                            <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai', $item->jam_mulai ? \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') : '') }}" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                            @error('jam_mulai') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                            <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai', $item->jam_selesai ? \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') : '') }}" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors">
                            @error('jam_selesai') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Keterangan / Alasan</label>
                        <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-3 py-2.5 bg-white border border-gray-300 rounded-sm focus:ring-1 focus:ring-teal-500 focus:border-teal-500 text-sm transition-colors placeholder:text-gray-400" placeholder="Contoh: Sakit / Menghadiri seminar">{{ old('keterangan', $item->keterangan) }}</textarea>
                        @error('keterangan') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>

                    <!-- Status Custom Select (Tanpa Ikon) -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-widest mb-1.5">Status Perubahan</label>
                        <div class="md:w-1/3">
                            <div class="cs-wrap" id="status_wrap">
                                <button type="button" class="cs-btn">
                                    <span>-- Pilih Status --</span>
                                    <i class="fas fa-chevron-down text-[9px] text-gray-400"></i>
                            </button>
                                <div class="cs-list">
                                    <div class="cs-item" data-val="aktif">Aktif</div>
                                    <div class="cs-item" data-val="selesai">Selesai</div>
                                    <div class="cs-item" data-val="dibatalkan">Dibatalkan</div>
                                </div>
                                <input type="hidden" id="status" name="status" value="{{ old('status', $item->status) }}">
                            </div>
                        </div>
                        @error('status') <p class="mt-1.5 text-red-500 text-[10px] font-bold uppercase tracking-wider">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-10 pt-6 border-t border-gray-200 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('admin.perubahan-jadwal.index') }}" class="w-full sm:w-auto px-5 py-2.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded-sm text-xs font-bold uppercase tracking-wider transition-colors text-center">Batal</a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-teal-700 hover:bg-teal-800 text-white rounded-sm text-xs font-bold uppercase tracking-wider transition-colors">Perbarui Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
function initCS(id) {
    const wrap = document.getElementById(id + '_wrap');
    if (!wrap) return null;
    const btn = wrap.querySelector('.cs-btn');
    const list = wrap.querySelector('.cs-list');
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

    // Set value awal dari database saat load
    if (input.value) {
        const selectedItem = list.querySelector(`.cs-item[data-val="${input.value}"]`);
        if (selectedItem) {
            btn.querySelector('span').innerHTML = selectedItem.innerHTML;
            selectedItem.classList.add('selected');
        }
    }

    return {
        disable: (val) => { btn.disabled = val; if(val) { list.classList.remove('active'); btn.classList.remove('active'); } },
        setPlaceholder: (text) => { if(!input.value) btn.querySelector('span').textContent = text; },
        populate: (items, emptyMsg, placeholder) => {
            list.innerHTML = '';
            const currentValue = input.value;
            let isSelected = false;

            if (!items.length) {
                list.innerHTML = `<div class="px-3 py-2 text-sm text-gray-400 italic">${emptyMsg}</div>`;
                btn.querySelector('span').textContent = emptyMsg;
                btn.disabled = true;
                input.value = '';
                return;
            }

            items.forEach(d => {
                const div = document.createElement('div');
                div.className = 'cs-item';
                div.dataset.val = d.id_dokter;
                const htmlContent = `<span class="font-medium">${d.nama}</span> <span class="text-[11px] text-gray-400 ml-1">${d.spesialisasi}</span>`;
                div.innerHTML = htmlContent;
                
                if (String(currentValue) === String(d.id_dokter)) {
                    div.classList.add('selected');
                    btn.querySelector('span').innerHTML = htmlContent;
                    isSelected = true;
                }
                
                div.addEventListener('click', () => {
                    input.value = d.id_dokter;
                    btn.querySelector('span').innerHTML = htmlContent;
                    list.querySelectorAll('.cs-item').forEach(i => i.classList.remove('selected'));
                    div.classList.add('selected');
                    list.classList.remove('active');
                    btn.classList.remove('active');
                    input.dispatchEvent(new Event('change'));
                });

                list.appendChild(div);
            });

            if (!isSelected) {
                input.value = ''; 
                btn.querySelector('span').textContent = placeholder;
            }
            btn.disabled = false;
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const dokterCS = initCS('dokter_id');
    const jenisCS = initCS('jenis_perubahan');
    const penggantiCS = initCS('dokter_pengganti_id');
    const statusCS = initCS('status');

    function handlePerubahanChange() {
        const jenis = document.getElementById('jenis_perubahan').value;
        const fieldPengganti = document.getElementById('field_dokter_pengganti');
        const fieldJam = document.getElementById('field_jam');
        const inputPengganti = document.getElementById('dokter_pengganti_id');
        const inputMulai = document.getElementById('jam_mulai');
        const inputSelesai = document.getElementById('jam_selesai');

        fieldPengganti.classList.add('hidden');
        fieldJam.classList.add('hidden');
        inputPengganti.removeAttribute('required');
        inputMulai.removeAttribute('required');
        inputSelesai.removeAttribute('required');

        if (jenis === 'pengganti') {
            fieldPengganti.classList.remove('hidden');
            inputPengganti.setAttribute('required', 'true');
        } else if (jenis === 'tambahan') {
            fieldJam.classList.remove('hidden');
            inputMulai.setAttribute('required', 'true');
            inputSelesai.setAttribute('required', 'true');
        }

        if (jenis !== 'pengganti') inputPengganti.value = '';
        if (jenis !== 'tambahan') { inputMulai.value = ''; inputSelesai.value = ''; }
    }

    document.getElementById('jenis_perubahan').addEventListener('change', handlePerubahanChange);
    handlePerubahanChange();

    // AJAX Tanggal -> Dokter
    document.getElementById('tanggal').addEventListener('change', async function () {
        const tanggal = this.value;
        if (!tanggal) { 
            dokterCS.setPlaceholder('-- Pilih Dokter --'); 
            dokterCS.disable(true); 
            penggantiCS.setPlaceholder('-- Pilih Dokter Dahulu --');
            penggantiCS.disable(true);
            return; 
        }

        dokterCS.setPlaceholder('Memuat...');
        dokterCS.disable(true);

        try {
            const klinikId = document.getElementById('klinik_id').value;
            const excludeId = '{{ $item->id_perubahan }}';
            let url = `{{ route('admin.perubahan-jadwal.dokter-available') }}?tanggal=${tanggal}&exclude_perubahan_id=${excludeId}`;
            if (klinikId) url += `&klinik_id=${klinikId}`;

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            
            dokterCS.populate(data, '-- Tidak ada dokter terjadwal --', '-- Pilih Dokter --');
            
            // Trigger pengganti load otomatis jika dokter sudah kepilih
            if (document.getElementById('dokter_id').value) {
                document.getElementById('dokter_id').dispatchEvent(new Event('change'));
            } else {
                penggantiCS.setPlaceholder('-- Pilih Dokter Dahulu --');
                penggantiCS.disable(true);
            }
        } catch (error) {
            console.error(error);
            dokterCS.setPlaceholder('-- Gagal memuat --');
            dokterCS.disable(true);
        }
    });

    // AJAX Dokter -> Pengganti
    document.getElementById('dokter_id').addEventListener('change', async function () {
        const dokterId = this.value;
        const tanggal = document.getElementById('tanggal').value;

        if (!dokterId || !tanggal) { 
            penggantiCS.setPlaceholder('-- Pilih Dokter Dahulu --'); 
            penggantiCS.disable(true); 
            return; 
        }

        penggantiCS.setPlaceholder('Memuat...');
        penggantiCS.disable(true);

        try {
            const klinikId = document.getElementById('klinik_id').value;
            const excludeId = '{{ $item->id_perubahan }}';
            let url = `{{ route('admin.perubahan-jadwal.dokter-pengganti') }}?tanggal=${tanggal}&dokter_id=${dokterId}&exclude_perubahan_id=${excludeId}`;
            if (klinikId) url += `&klinik_id=${klinikId}`;

            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();

            penggantiCS.populate(data, '-- Tidak ada pengganti tersedia --', '-- Pilih Pengganti --');
        } catch (error) {
            console.error(error);
            penggantiCS.setPlaceholder('-- Gagal memuat --');
            penggantiCS.disable(true);
        }
    });

    // Trigger awal untuk load data edit
    const tanggalInput = document.getElementById('tanggal');
    if (tanggalInput.value) {
        tanggalInput.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection