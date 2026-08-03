<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Dokter Klinik Gigi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        body { background: #f8fafc; }

        /* Semua elemen kotak */
        *, *::before, *::after {
            border-radius: 0 !important;
        }
        /* Badge notif tetap bulat */
        .badge-bulat {
            border-radius: 9999px !important;
        }
        /* Dot indikator di list notif juga bulat */
        .dot-bulat {
            border-radius: 9999px !important;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .btn-back { position: relative; overflow: hidden; }
        .btn-back::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.12), transparent);
            opacity: 0;
            transition: opacity 200ms;
        }
        .btn-back:hover::before { opacity: 1; }

        @keyframes notif-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        .notif-pulse { animation: notif-pulse 2s ease-in-out infinite; }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .toast-in { animation: slideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .toast-out { animation: slideOutRight 0.3s ease-in forwards; }
    </style>
    @yield('styles')
</head>

<body class="min-h-screen antialiased text-slate-700 font-sans">

    @include('layouts.partials.alert')

    <!-- NAVBAR -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200/70 shadow-[0_1px_2px_rgba(0,0,0,0.04)]" style="border-top: 2.5px solid #0d9488;">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-[60px]">

                <!-- Logo -->
               <a href="{{ route('dokter.dashboard') }}" class="flex items-center gap-3.5 group">
                    <div class="w-10 h-10 bg-white rounded-md flex items-center justify-center">
                        <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-extrabold text-[15px] text-gray-900 tracking-tight leading-none">Klinik Gigi</h1>
                        <p class="text-[10.5px] text-teal-600 font-bold tracking-[0.15em] uppercase leading-none mt-1">Dokter Panel</p>
                    </div>
                </a>

                <!-- Kanan -->
                <div class="flex items-center gap-2.5">

                    <!-- Notifikasi -->
                    <div class="relative" id="notif-container">
                        <button onclick="toggleNotif()"
                                class="relative w-10 h-10 bg-gray-50 hover:bg-teal-50 border border-gray-200/80 hover:border-teal-300 text-gray-400 hover:text-teal-600 transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-bell text-[15px]"></i>
                            <span id="notif-badge" class="badge-bulat absolute -top-2 -right-2 min-w-[20px] h-[20px] bg-red-500 text-white text-[9px] font-bold flex items-center justify-center px-1 hidden notif-pulse shadow-sm shadow-red-500/30">0</span>
                        </button>

                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2.5 w-[370px] bg-white shadow-xl shadow-gray-300/40 border border-gray-100 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-teal-500/30 flex items-center justify-between bg-gradient-to-r from-teal-600 to-teal-700">
                                <h3 class="text-[13px] font-bold text-white tracking-wide">Notifikasi</h3>
                                <button onclick="markAllRead()" class="text-[11px] text-teal-100 hover:text-white font-semibold transition-colors">Tandai dibaca</button>
                            </div>
                            <div id="notif-list" class="max-h-[340px] overflow-y-auto custom-scrollbar">
                                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                    <i class="fas fa-bell-slash mb-2 block text-lg opacity-50"></i>
                                    Memuat...
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pemisah -->
                    <div class="w-px h-7 bg-teal-200/60 mx-0.5"></div>

                    <!-- User -->
                    <div class="relative" id="user-dropdown-container">
                        <button onclick="toggleUserDropdown()"
                                class="flex items-center gap-3 pl-2 pr-3.5 py-2 hover:bg-gray-50 transition-all duration-200 border border-transparent hover:border-gray-200/80">
                            <div class="w-9 h-9 bg-gradient-to-br from-teal-500 to-teal-700 text-white flex items-center justify-center font-bold text-[13px] shadow-sm shadow-teal-500/25">
                                {{ substr(auth()->user()->nama ?? 'D', 0, 1) }}
                            </div>
                            <div class="hidden lg:block text-left leading-none">
                                <p class="text-[13px] font-bold text-gray-800">{{ auth()->user()->nama ?? 'Dokter' }}</p>
                                <p class="text-[10.5px] text-teal-600 font-semibold mt-0.5">Dokter</p>
                            </div>
                            <i class="fas fa-chevron-down text-[9px] text-gray-400 hidden lg:block ml-0.5 transition-transform duration-200" id="user-chevron"></i>
                        </button>

                        <div id="user-dropdown" class="hidden absolute right-0 mt-2.5 w-56 bg-white shadow-xl shadow-gray-300/40 border border-gray-100 z-50 overflow-hidden">
                            <div class="px-4 py-3.5 bg-gradient-to-r from-teal-600 to-teal-700">
                                <p class="text-[13px] font-bold text-white truncate">{{ auth()->user()->nama ?? 'Dokter' }}</p>
                                <p class="text-[11px] text-teal-100/80 truncate mt-0.5">{{ auth()->user()->email ?? '—' }}</p>
                            </div>
                            <div class="py-1.5">
                                <a href="{{ route('dokter.profil') }}" class="flex items-center gap-3 px-4 py-3 text-[13px] text-gray-600 hover:bg-teal-50 hover:text-teal-700 transition-colors">
                                    <i class="fas fa-user-cog text-teal-500/70 w-4 text-center text-xs"></i>
                                    Profil Saya
                                </a>
                                <div class="h-px bg-gray-100 mx-3 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                    @csrf
                                    <button type="submit" onclick="confirmLogout(event, this.form, '{{ auth()->user()->nama ?? 'Pengguna' }}')"
                                            class="w-full flex items-center gap-3 px-3 py-3 text-[13px] text-red-500 hover:bg-red-50 transition-colors text-left font-medium">
                                        <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <!-- TOAST -->
    <div id="toast-container" class="fixed flex flex-col gap-3 pointer-events-none z-[60]" style="top:80px; right:20px;"></div>

    <!-- MAIN -->
    <div class="pt-[60px] min-h-screen">

        <!-- Sub Header -->
        <header class="bg-white border-b border-gray-100 px-5 sm:px-6 lg:px-8 py-4 flex items-center justify-between sticky top-[60px] z-40">
            <div class="flex items-center gap-4">
                @if(!request()->is('dokter/dashboard'))
                <a href="{{ route('dokter.dashboard') }}"
                   class="btn-back flex items-center gap-2.5 px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-[12.5px] font-semibold shadow-sm shadow-teal-600/25 hover:shadow-teal-600/40 transition-all duration-200 active:scale-[0.97]">
                    <i class="fas fa-arrow-left text-[11px]"></i>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>
                @endif
                <div class="pl-3.5 py-1" style="border-left: 3px solid #0d9488;">
                    <h1 class="text-[15px] font-extrabold text-gray-800 tracking-tight">
                        @yield('header-title', 'Dashboard')
                    </h1>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-2 text-[13px] text-gray-400 font-medium">
                <i class="far fa-calendar text-teal-500"></i>
                <span>{{ now()->locale('id')->translatedFormat('l, d M Y') }}</span>
            </div>
        </header>

        <main class="p-5 lg:p-8">
            <div class="max-w-[1600px] mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        AOS.init({ duration: 600, once: true, offset: 50 });

        function toggleUserDropdown() {
            const dropdown = document.getElementById('user-dropdown');
            const chevron = document.getElementById('user-chevron');
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
            document.getElementById('notif-dropdown').classList.add('hidden');
        }

        function toggleNotif() {
            const dropdown = document.getElementById('notif-dropdown');
            const isHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            document.getElementById('user-dropdown').classList.add('hidden');
            if (document.getElementById('user-chevron')) document.getElementById('user-chevron').style.transform = '';
            if (isHidden) loadNotifikasi();
        }

        document.addEventListener('click', (e) => {
            const nc = document.getElementById('notif-container');
            const uc = document.getElementById('user-dropdown-container');
            if (nc && !nc.contains(e.target)) document.getElementById('notif-dropdown').classList.add('hidden');
            if (uc && !uc.contains(e.target)) {
                document.getElementById('user-dropdown').classList.add('hidden');
                if (document.getElementById('user-chevron')) document.getElementById('user-chevron').style.transform = '';
            }
        });

        function confirmLogout(event, form, userName) {
            event.preventDefault();
            Swal.fire({
                width: 360,
                html: `<div class="text-center">
                    <div class="w-14 h-14 bg-red-50 flex items-center justify-center mx-auto mb-4 border border-red-100">
                        <i class="fas fa-sign-out-alt text-red-500 text-xl"></i>
                    </div>
                    <h2 class="text-[15px] font-extrabold text-gray-800 mb-1.5">Keluar dari akun?</h2>
                    <p class="text-[12px] text-gray-500">Sampai jumpa, <b class="text-gray-700">${userName}</b></p>
                </div>`,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'p-6',
                    confirmButton: 'px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-[12.5px] font-semibold transition-all',
                    cancelButton: 'px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[12.5px] font-semibold ml-2 transition-all'
                }
            }).then(r => {
                if (r.isConfirmed) {
                    localStorage.removeItem('lastNotifId');
                    localStorage.removeItem('firstLoadDone');
                    form.submit();
                }
            });
        }

        // ==================== NOTIFIKASI ====================
        let notifPolling = null;
        let lastNotifId = parseInt(localStorage.getItem('lastNotifId')) || 0;
        let firstLoadDone = localStorage.getItem('firstLoadDone') === 'true';

        const tipeConfig = {
            'success':  { bg: 'bg-emerald-50', text: 'text-emerald-600', border: 'border-emerald-500', dot: 'bg-emerald-500', icon: 'fa-calendar-check' },
            'danger':   { bg: 'bg-red-50',     text: 'text-red-600',     border: 'border-red-500',     dot: 'bg-red-500',     icon: 'fa-exclamation-triangle' },
            'warning':  { bg: 'bg-amber-50',   text: 'text-amber-600',   border: 'border-amber-500',   dot: 'bg-amber-500',   icon: 'fa-ban' },
            'reservasi':{ bg: 'bg-teal-50',    text: 'text-teal-600',    border: 'border-teal-500',    dot: 'bg-teal-500',    icon: 'fa-calendar-check' },
            'info':     { bg: 'bg-blue-50',    text: 'text-blue-600',    border: 'border-blue-500',    dot: 'bg-blue-500',    icon: 'fa-info-circle' }
        };
        const getCfg = (t) => tipeConfig[t] || tipeConfig['info'];

        async function loadNotifikasi() {
            try {
                const res = await fetch('/notifikasi/unread');
                const data = await res.json();
                const badge = document.getElementById('notif-badge');
                const list = document.getElementById('notif-list');

                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');

                    list.innerHTML = data.data.map(n => {
                        const c = getCfg(n.tipe);
                        return `<div class="group relative px-4 py-3.5 hover:bg-gray-50/80 border-b border-gray-100 transition-colors">
                            <a href="${n.url || '#'}" onclick="markReadOne(${n.id_notif})" class="block pr-8">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 ${c.bg} flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fas ${c.icon} ${c.text} text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[12.5px] font-bold text-gray-800 truncate">${n.judul}</p>
                                        <p class="text-[11.5px] text-gray-500 line-clamp-2 mt-0.5 leading-relaxed">${n.pesan}</p>
                                        <p class="text-[10.5px] text-gray-400 mt-1.5">${timeAgo(n.created_at)}</p>
                                    </div>
                                    <div class="w-2 h-2 ${c.dot} dot-bulat flex-shrink-0 mt-2"></div>
                                </div>
                            </a>
                            <button onclick="hapusNotif(${n.id_notif}, event)" class="absolute top-3.5 right-3 p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100" title="Hapus">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                        </div>`;
                    }).join('');

                    if (!firstLoadDone) {
                        if (data.data.length > 0) {
                            const maxId = Math.max(...data.data.map(n => n.id_notif));
                            lastNotifId = maxId;
                            localStorage.setItem('lastNotifId', maxId);
                        }
                        localStorage.setItem('firstLoadDone', 'true');
                        firstLoadDone = true;
                    } else {
                        data.data.forEach(n => {
                            if (n.id_notif > lastNotifId) {
                                showToast(n);
                                lastNotifId = n.id_notif;
                                localStorage.setItem('lastNotifId', lastNotifId);
                            }
                        });
                    }
                } else {
                    badge.classList.add('hidden');
                    list.innerHTML = `<div class="px-4 py-12 text-center text-gray-400">
                        <div class="w-11 h-11 bg-gray-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-bell-slash text-gray-300 text-sm"></i>
                        </div>
                        <p class="text-[12px] font-semibold text-gray-400">Tidak ada notifikasi baru</p>
                    </div>`;
                }
            } catch (err) { console.error('Gagal load notifikasi:', err); }
        }

        async function hapusNotif(id, event) {
            event.preventDefault();
            event.stopPropagation();
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus notifikasi?',
                text: 'Tindakan ini tidak bisa dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                buttonsStyling: false,
                customClass: {
                    popup: 'p-6',
                    confirmButton: 'px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-[12.5px] font-semibold transition-all',
                    cancelButton: 'px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-[12.5px] font-semibold ml-2 transition-all'
                }
            });
            if (!isConfirmed) return;
            try {
                const res = await fetch(`/notifikasi/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) loadNotifikasi();
            } catch (err) { console.error('Gagal hapus:', err); }
        }

        function showToast(n) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const c = getCfg(n.tipe);
            toast.className = `pointer-events-auto bg-white border-l-4 ${c.border} shadow-lg shadow-gray-200/50 p-4 w-[320px] toast-in flex items-start gap-3`;
            toast.innerHTML = `
                <div class="w-9 h-9 ${c.bg} flex items-center justify-center flex-shrink-0">
                    <i class="fas ${c.icon} ${c.text} text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[12.5px] font-bold text-gray-800">${n.judul}</p>
                    <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">${n.pesan}</p>
                    <a href="${n.url || '#'}" onclick="markReadOne(${n.id_notif})" class="text-[10.5px] ${c.text} font-semibold mt-1.5 inline-block hover:underline">Lihat detail →</a>
                </div>
                <button onclick="dismissToast(this)" class="text-gray-300 hover:text-gray-500 flex-shrink-0 p-1 -mr-1 -mt-1">
                    <i class="fas fa-times text-xs"></i>
                </button>`;
            container.appendChild(toast);
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.classList.remove('toast-in');
                    toast.classList.add('toast-out');
                    setTimeout(() => toast.remove(), 300);
                }
            }, 6000);
        }

        function dismissToast(btn) {
            const toast = btn.parentElement;
            toast.classList.remove('toast-in');
            toast.classList.add('toast-out');
            setTimeout(() => toast.remove(), 300);
        }

        async function markAllRead() {
            try {
                await fetch('/notifikasi/read-all', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                loadNotifikasi();
            } catch (err) { console.error(err); }
        }

        async function markReadOne(id) {
            try {
                await fetch(`/notifikasi/read/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
            } catch (err) { console.error(err); }
        }

        function timeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'Baru saja';
            if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
            if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
            return `${Math.floor(diff / 86400)} hari lalu`;
        }

        notifPolling = setInterval(() => { loadNotifikasi(); }, 5000);
        document.addEventListener('DOMContentLoaded', loadNotifikasi);
    </script>

    @yield('scripts')
</body>
</html>