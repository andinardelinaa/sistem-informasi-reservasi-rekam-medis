<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Klinik Gigi</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#0d9488',
                            600: '#0f766e',
                            700: '#115e59',
                            800: '#134e4a',
                            900: '#f6f9f9',
                        }
                    },
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #f8fafc; /* slate-50 */
        }

        .sidebar-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        /* ACTIVE STATE - Flat & Professional */
        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 800;
            border-left: 4px solid white;
        }

        .sidebar-item:hover:not(.active) {
            background: rgba(255, 255, 255, 0.08);
            border-left-color: rgba(255,255,255,0.3);
        }

        /* Submenu Level 1 */
        .submenu-lvl-1 {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .submenu-lvl-1:hover {
            background: rgba(255, 255, 255, 0.08);
            border-left-color: rgba(255,255,255,0.5);
        }

        .submenu-lvl-1.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-weight: 700;
            border-left: 3px solid white;
        }

        /* Nested Level 2 */
        .submenu-lvl-2:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .submenu-lvl-2.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 800;
        }

        /* Dropdown */
        .dropdown-content {
            transition: max-height 0.35s ease-in-out;
            max-height: 0;
            overflow: hidden;
        }
        
        .dropdown-content.open {
            max-height: 800px;
        }

        /* Scrollbar */
        .no-scroll-nav::-webkit-scrollbar {
            width: 4px;
        }
        .no-scroll-nav::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }
    </style>
</head>

<body class="min-h-screen flex antialiased text-slate-800">

    @include('layouts.partials.alert')

    <!-- SIDEBAR -->
    <aside class="sidebar-overlay fixed inset-y-0 left-0 z-40 w-64 bg-teal-700 text-white flex flex-col lg:translate-x-0 border-r border-teal-800">

        <!-- Logo -->
        <div class="p-5 border-b border-teal-800">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white p-1.5 flex items-center justify-center rounded-sm">
                    <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-extrabold text-sm tracking-wide uppercase">Klinik Gigi</h1>
                    <p class="text-[10px] text-teal-200 uppercase font-bold tracking-widest">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto no-scroll-nav flex flex-col gap-1">

            <!-- DASHBOARD -->
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-item flex items-center gap-3 px-4 py-2.5 rounded-sm text-xs font-bold uppercase tracking-wider {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie w-5 text-center text-sm"></i>
                <span>Dashboard</span>
            </a>

            <div class="h-px bg-teal-800 my-2 mx-4"></div>

            <!-- DATA MASTER -->
            <div>
                <a href="#" onclick="toggleDropdown('menu-master', 'icon-master'); return false;" 
                   class="sidebar-item group flex items-center justify-between px-4 py-2.5 rounded-sm text-xs font-bold uppercase tracking-wider
                   {{ request()->routeIs('admin.klinik.*') || request()->routeIs('admin.dokter.*') || request()->routeIs('admin.pasien.*') || request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-database w-5 text-center text-sm"></i>
                        <span>Data Master</span>
                    </div>
                    <i id="icon-master" class="fas fa-chevron-right text-[10px] transition-transform {{ (request()->routeIs('admin.klinik.*') || request()->routeIs('admin.dokter.*') || request()->routeIs('admin.pasien.*') || request()->routeIs('admin.users.*')) ? 'rotate-90' : '' }}"></i>
                </a>

                <div id="menu-master" class="dropdown-content pl-6 mt-1 {{ request()->routeIs('admin.klinik.*') || request()->routeIs('admin.dokter.*') || request()->routeIs('admin.pasien.*') || request()->routeIs('admin.users.*') ? 'open' : '' }}">
                    <div class="flex flex-col gap-0.5 py-1">
                        <!-- submenu klinik, dokter, pasien, users -->
                        <a href="{{ route('admin.klinik.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.klinik.*') ? 'active' : '' }}">Klinik</a>
                        <a href="{{ route('admin.dokter.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.dokter.*') ? 'active' : '' }}">Dokter</a>
                        <a href="{{ route('admin.pasien.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.pasien.*') ? 'active' : '' }}">Pasien</a>
                        <a href="{{ route('admin.users.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">User</a>
                    </div>
                </div>
            </div>

            <!--  OPERASIONAL -->
            <div>
                <a href="#" onclick="toggleDropdown('menu-operasional', 'icon-operasional'); return false;" 
                   class="sidebar-item group flex items-center justify-between px-4 py-2.5 rounded-sm text-xs font-bold uppercase tracking-wider
                   {{ request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*') || request()->routeIs('admin.reservasi.*') || request()->routeIs('admin.rekam-medis.*') || request()->routeIs('admin.hari-libur.*') ? 'active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-layer-group w-5 text-center text-sm"></i>
                        <span>Operasional</span>
                    </div>
                    <i id="icon-operasional" class="fas fa-chevron-right text-[10px] transition-transform {{ (request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*') || request()->routeIs('admin.reservasi.*') || request()->routeIs('admin.rekam-medis.*') || request()->routeIs('admin.hari-libur.*')) ? 'rotate-90' : '' }}"></i>
                </a>

                <div id="menu-operasional" class="dropdown-content pl-6 mt-1 {{ request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*') || request()->routeIs('admin.reservasi.*') || request()->routeIs('admin.rekam-medis.*') || request()->routeIs('admin.hari-libur.*') ? 'open' : '' }}">
                    <div class="flex flex-col gap-0.5 py-1">
                        
                        <!-- JADWAL -->
                        <div>
                            <a href="#" onclick="toggleDropdown('nested-jadwal', 'icon-nested-jadwal'); return false;"
                               class="submenu-lvl-1 flex items-center justify-between px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm
                               {{ request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*') ? 'active' : '' }}">
                                <span>Jadwal</span>
                                <i id="icon-nested-jadwal" class="fas fa-chevron-right text-[10px] transition-transform {{ (request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*')) ? 'rotate-90' : '' }}"></i>
                            </a>

                            <div id="nested-jadwal" class="dropdown-content pl-6 mt-1 border-l border-white/20 {{ request()->routeIs('admin.jadwal.*') || request()->routeIs('admin.perubahan-jadwal.*') ? 'open' : '' }}">
                                <a href="{{ route('admin.jadwal.index') }}" class="submenu-lvl-2 block px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-sm {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">Jadwal Reguler</a>
                                <a href="{{ route('admin.perubahan-jadwal.index') }}" class="submenu-lvl-2 block px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest rounded-sm {{ request()->routeIs('admin.perubahan-jadwal.*') ? 'active' : '' }}">Perubahan Jadwal</a>
                            </div>
                        </div>

                        <!-- MENU OPERASIONAL-->
                        <a href="{{ route('admin.reservasi.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.reservasi.*') ? 'active' : '' }}">Reservasi</a>
                        <a href="{{ route('admin.rekam-medis.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.rekam-medis.*') ? 'active' : '' }}">Rekam Medis</a>
                        <a href="{{ route('admin.hari-libur.index') }}" class="submenu-lvl-1 px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm {{ request()->routeIs('admin.hari-libur.*') ? 'active' : '' }}">Hari Libur</a>
                    </div>
                </div>
            </div>

        </nav>

        <!-- User Profile -->
        <div class="p-4 mt-auto border-t border-teal-800">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-9 h-9 rounded-sm bg-teal-800 flex items-center justify-center font-bold text-sm border border-teal-900">
                <!-- mbil nama huruf dr user yang login, ambil huruf pertama, kalo kosong pake A-->
                {{ substr(auth()->user()->nama ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <!-- nama user -->
                    <p class="font-bold text-xs truncate uppercase tracking-wider">{{ auth()->user()->nama ?? 'Admin' }}</p>
                    <!-- email user -->
                    <p class="text-[10px] text-teal-200 truncate">{{ auth()->user()->email ?? '—' }}</p>
                </div>
            </div>
            <!-- tombol logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" onclick="confirmLogout(event, this.form, '{{ auth()->user()->nama ?? 'Pengguna' }}')"
                        class="w-full flex items-center justify-center gap-2 py-2 rounded-sm bg-white/5 hover:bg-red-600 border border-white/10 hover:border-red-600 text-xs font-bold uppercase tracking-wider transition-all">
                    <i class="fas fa-sign-out-alt text-[10px]"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 lg:ml-64 flex flex-col min-h-screen bg-slate-50">
        <!-- header navbar atas -->
        <header class="bg-white border-b border-gray-200 px-7 py-4 flex items-center justify-between sticky top-0 z-30">
            <h1 class="text-base font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                <!-- judul halaman -->
                <span class="text-teal-700"><i class="fas fa-layer-group text-sm"></i></span>
                @yield('header-title', 'Dashboard')
            </h1>
            
            <!--tanggal -->
            <div class="flex items-center gap-6">
                <div class="hidden md:block text-right">
                    <!-- tanggal -->
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ now()->locale('id')->translatedFormat('l') }}</p>
                    <p class="text-xs font-bold text-gray-800 uppercase tracking-wider">{{ now()->locale('id')->translatedFormat('d M Y') }}</p>
                </div>
                
               <!-- notifikasi -->
                <div class="relative" id="notif-container">
                    <button onclick="toggleNotif()" class="relative p-2 rounded-sm bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-teal-700 transition-colors border border-gray-200">
                        <i class="fas fa-bell text-sm"></i>
                        <!-- jumlah notif -->
                        <span id="notif-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center hidden">0</span>
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Notifikasi</h3>
                            <button onclick="markAllRead()" class="text-[10px] text-teal-600 hover:text-teal-800 font-semibold uppercase tracking-wider">Tandai dibaca</button>
                        </div>
                        <div id="notif-list" class="max-h-80 overflow-y-auto">
                            <div class="px-4 py-8 text-center text-gray-400 text-sm">
                                <i class="fas fa-bell-slash mb-2 block text-lg"></i>
                                Memuat...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOAST notif -->
                <div id="toast-container" class="fixed top-20 right-6 z-[60] flex flex-col gap-3 pointer-events-none"></div>
                </div>
        </header>
        <!-- isi halaman dinamis -->
        <main class="flex-1 p-6 overflow-auto">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Mobile Toggle -->
    <button id="sidebar-toggle" class="lg:hidden fixed bottom-6 right-6 z-50 bg-teal-700 text-white p-4 rounded-sm shadow-lg hover:bg-teal-800 transition-colors border border-teal-800">
        <i class="fas fa-bars text-lg"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // dropdown sidebar
        function toggleDropdown(menuId, iconId) {
            const menu = document.getElementById(menuId);
            const icon = document.getElementById(iconId);
            // kalo menu sudah terbuka, submenu ditutup
            if (menu.classList.contains('open')) {
                menu.classList.remove('open');
                icon.classList.remove('rotate-90');
            } else { //kalo masih tertutup, panah berputar 90
                menu.classList.add('open');
                icon.classList.add('rotate-90');
            }
        }
        // tombol sidebar mobile
        // ketika diklik, overlay akan muncul dan hilang bergantian
        document.getElementById('sidebar-toggle')?.addEventListener('click', () => {
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        });
        // konfirmasi logout
        function confirmLogout(event, form, userName) {
            event.preventDefault(); //hentikan submit form
            // popup logout
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
                            <i class="fas fa-sign-out-alt" style="font-size:20px; color:#dc2626;"></i>
                        </div>

                        <h2 style="font-size:16px; font-weight:700; color:#1f2937; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.05em;">
                            Konfirmasi Logout
                        </h2>

                        <p style="font-size:13px; color:#6b7280;">
                            Halo <b>${userName}</b>, yakin mau keluar?
                        </p>
                    </div>
                `,
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Batal',

                customClass: {
                    popup: 'rounded-sm p-5',

                    confirmButton: `
                        px-4 py-2 bg-red-600 hover:bg-red-700 
                        text-white text-xs rounded-sm font-bold uppercase tracking-wider
                        transition
                    `,

                    cancelButton: `
                        px-4 py-2 bg-gray-200 hover:bg-gray-300 
                        text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2
                        transition
                    `
                }
                // jika tekan iya maka form di submit
            }).then(result => {
                if (result.isConfirmed) {
                    localStorage.removeItem('lastNotifId');
                localStorage.removeItem('firstLoadDone');

                form.submit();
                }
            });
        }
    // mengingat id notifikasi terakhir yang sudah pernah muncul di toast
    let lastNotifId = parseInt(localStorage.getItem('lastNotifId')) || 0;
    let firstLoadDone = localStorage.getItem('firstLoadDone') === 'true'; //apakah user baru pertama kali membuka halaman
    // buka dropdown notifikasi
    function toggleNotif() {
        const dropdown = document.getElementById('notif-dropdown');
        dropdown.classList.toggle('hidden'); //hidden hapus
        // kalo dropdown berhasil terbuka,  panggil loadnotifikasi()
        if (!dropdown.classList.contains('hidden')) {
            loadNotifikasi();
        }
    }
// load notifikasi
async function loadNotifikasi() {
    try {
        const res = await fetch('/notifikasi/unread'); //ambil data laravel
        const data = await res.json(); //ubah json jadi object js
        
        const badge = document.getElementById('notif-badge'); //ambil elemen html
        const list = document.getElementById('notif-list');
        
        // membuat daftar warna
        const tipeConfig = {
            'success': { 
                bg: 'bg-emerald-50', 
                text: 'text-emerald-600', 
                border: 'border-emerald-500',
                icon: 'fa-calendar-check' 
            },
            'danger': { 
                bg: 'bg-red-50', 
                text: 'text-red-600', 
                border: 'border-red-500',
                icon: 'fa-exclamation-triangle' 
            },
            'warning': { 
                bg: 'bg-amber-50', 
                text: 'text-amber-600', 
                border: 'border-amber-500',
                icon: 'fa-ban' 
            },
            'reservasi': { 
                bg: 'bg-teal-50', 
                text: 'text-teal-600', 
                border: 'border-teal-500',
                icon: 'fa-calendar-check' 
            },
            'info': { 
                bg: 'bg-blue-50', 
                text: 'text-blue-600', 
                border: 'border-blue-500',
                icon: 'fa-info-circle' 
            }
        };
        const getConfig = (tipe) => tipeConfig[tipe] || tipeConfig['info'];
        // cek apakah ada notofikasi, kalo jumlah notif lebih dari nol
        if (data.count > 0) {
            //menampilkan badge merah
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.classList.remove('hidden'); //badge merah tidak muncul
            // menampilkan daftar notifikasi
            list.innerHTML = data.data.map(n => {
                const cfg = getConfig(n.tipe); //ambil warna sesuai tipe
                return `
                    <div class="group relative px-4 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                        <a href="${n.url || '#'}" onclick="markReadOne(${n.id_notif})" class="block pr-8">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full ${cfg.bg} flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i class="fas ${cfg.icon} ${cfg.text} text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate">${n.judul}</p>
                                    <p class="text-[11px] text-gray-500 line-clamp-2 mt-0.5">${n.pesan}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">${timeAgo(n.created_at)}</p>
                                </div>
                                <div class="w-2 h-2 ${cfg.bg.replace('bg-', 'bg-').replace('50', '500')} rounded-full flex-shrink-0 mt-2"></div>
                            </div>
                        </a>
                        <button onclick="hapusNotif(${n.id_notif}, event)" 
                                class="absolute top-3 right-3 p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition-all opacity-0 group-hover:opacity-100"
                                title="Hapus">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                        </button>
                    </div>
                `;
            }).join('');
            
           if (!firstLoadDone) {

            // cek apakah user pertama membuka halaman
            if (data.data.length > 0) {
                // jika iya, cari id dan simpan
                const maxId = Math.max(
                    ...data.data.map(n => n.id_notif)
                );

                lastNotifId = maxId;
                // simpan di browser juga
                localStorage.setItem('lastNotifId', maxId);
            }

            localStorage.setItem('firstLoadDone', 'true');
            firstLoadDone = true;
        }
        else { //jika bukan
            // perulangan
            data.data.forEach(n => {
                // cek id terakhir
                if (n.id_notif > lastNotifId) {
                    // panggil
                    showToast(n);

                    lastNotifId = n.id_notif;
                    // simpan
                    localStorage.setItem(
                        'lastNotifId',
                        lastNotifId
                    );
                }
            });
        }
        //jika tidak ada notifikasi
        } else {
            badge.classList.add('hidden');
            list.innerHTML = `
                <div class="px-4 py-8 text-center text-gray-400 text-sm">
                    <i class="fas fa-bell-slash mb-2 block text-lg"></i>
                    Tidak ada notifikasi baru
                </div>
            `;
        }
    } catch (err) {
        console.error('Gagal load notifikasi:', err);
    }
}
// menampilkan popup kecil ketika ada notofikasi baru
function showToast(n) {
    const container = document.getElementById('toast-container'); //ambil tempat toast
    const toast = document.createElement('div'); //buat elemen baru
    //tentukan warna dan ikon
    const tipeConfig = {
        'success': { 
            bg: 'bg-emerald-50', 
            text: 'text-emerald-600', 
            border: 'border-emerald-500',
            icon: 'fa-calendar-check' 
        },
        'danger': { 
            bg: 'bg-red-50', 
            text: 'text-red-600', 
            border: 'border-red-500',
            icon: 'fa-exclamation-triangle' 
        },
        'warning': { 
            bg: 'bg-amber-50', 
            text: 'text-amber-600', 
            border: 'border-amber-500',
            icon: 'fa-ban' 
        },
        'reservasi': { 
            bg: 'bg-teal-50', 
            text: 'text-teal-600', 
            border: 'border-teal-500',
            icon: 'fa-calendar-check' 
        },
        'info': { 
            bg: 'bg-blue-50', 
            text: 'text-blue-600', 
            border: 'border-blue-500',
            icon: 'fa-info-circle' 
        }
    };

    const cfg = tipeConfig[n.tipe] || tipeConfig['info'];
    // mengisi toast
    toast.className = `pointer-events-auto bg-white border-l-4 ${cfg.border} rounded-lg shadow-lg p-4 w-80 transform translate-x-full transition-all duration-300 flex items-start gap-3`;
    
    toast.innerHTML = `
        <div class="w-8 h-8 rounded-full ${cfg.bg} flex items-center justify-center flex-shrink-0">
            <i class="fas ${cfg.icon} ${cfg.text} text-xs"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-bold text-gray-800">${n.judul}</p>
            <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-2">${n.pesan}</p>
            <a href="${n.url || '#'}" onclick="markReadOne(${n.id_notif})" class="text-[10px] ${cfg.text} font-semibold mt-1 inline-block hover:underline">Lihat detail →</a>
        </div>
        <button onclick="this.parentElement.remove()" class="text-gray-300 hover:text-gray-500 flex-shrink-0">
            <i class="fas fa-times text-xs"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-full');
    });
    // hilang otomaatis
    setTimeout(() => {
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 6000);
}
    // menandai semua notifikasi sebagai sudah dibaa
    async function markAllRead() {
        try {
            await fetch('/notifikasi/read-all', { //kirim request laravel
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            // memperbarui tampilan
            loadNotifikasi();
        } catch (err) {
            console.error(err);
        }
    }
    // menandai satu notifikasi sebagai sudah dibaca 
    async function markReadOne(id) {
        try {
            await fetch(`/notifikasi/read/${id}`, { //request laravel
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
        } catch (err) {
            console.error(err);
        }
    }
    // hapus notifikasi
    async function hapusNotif(id, event) {
        event.preventDefault(); //menghentikan kamu
        event.stopPropagation();
        try {
            await fetch(`/notifikasi/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            // perbarui daftar
            loadNotifikasi();
        } catch (err) {
            console.error(err);
        }
    }
    // ubah tinggal db yg urutan menjadi nntulisan yang mudah dibaca
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'Baru saja';
        if (diff < 3600) return `${Math.floor(diff/60)} menit lalu`;
        if (diff < 86400) return `${Math.floor(diff/3600)} jam lalu`;
        return `${Math.floor(diff/86400)} hari lalu`;
    }

    // Polling setiap 5 detik (lebih cepat)
    notifPolling = setInterval(() => {
        loadNotifikasi();
    }, 5000);

    // Load pertama kali
    document.addEventListener('DOMContentLoaded', loadNotifikasi);

    // Close dropdown saat klik luar
    document.addEventListener('click', (e) => {
        const container = document.getElementById('notif-container');
        if (container && !container.contains(e.target)) {
            document.getElementById('notif-dropdown').classList.add('hidden');
        }
    });
    </script>

    @yield('scripts')
</body>
</html>