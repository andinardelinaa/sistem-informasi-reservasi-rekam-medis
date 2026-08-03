<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Klinik Gigi Dokter Ndeso')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50:  '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#0d9a89',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        },
                        slate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Glass Effect Header */
        .glass-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Link Hover Animation */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -6px;
            left: 50%;
            background-color: #0d9488;
            transition: width 0.35s ease, left 0.35s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #0d9488 !important;
            font-weight: 700;
        }

        /* mobile sidebar */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.35s ease, visibility 0.35s ease;
        }
        .sidebar-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 300px;
            max-width: 85vw;
            background: #ffffff;
            z-index: 100;
            transform: translateX(105%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: -6px 0 24px rgba(0,0,0,0.08);
        }
        .mobile-sidebar.open {
            transform: translateX(0);
            box-shadow: -12px 0 40px rgba(0,0,0,0.15);
        }

        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);
        }

        .sidebar-close {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }
        .sidebar-close:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            color: #334155;
            font-weight: 500;
            font-size: 0.938rem;
            transition: all 0.2s ease;
            position: relative;
            border: 1px solid transparent;
        }
        .sidebar-link:hover {
            background: #f0fdfa;
            color: #0d9488;
            border-color: #ccfbf1;
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
            color: #0f766e;
            font-weight: 700;
            border-color: #99f6e4;
            box-shadow: 0 1px 4px rgba(13,148,136,0.08);
        }
        .sidebar-link .sl-icon {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 15px;
            flex-shrink: 0;
            transition: all 0.2s ease;
            background: #f8fafc;
            color: #0d9488;
            border: 1px solid #e2e8f0;
        }
        .sidebar-link:hover .sl-icon {
            background: #ccfbf1;
            color: #0f766e;
            border-color: #99f6e4;
        }
        .sidebar-link.active .sl-icon {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: #fff;
            border-color: #0d9488;
            box-shadow: 0 2px 6px rgba(13,148,136,0.25);
        }

        .sidebar-section-label {
            display: block;
            padding: 8px 16px 6px;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 8px;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid #f1f5f9;
            flex-shrink: 0;
            background: #fafafa;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            margin-bottom: 12px;
        }
        .sidebar-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .sidebar-user-avatar.role-pasien {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            color: #fff;
        }
        .sidebar-user-avatar.role-admin {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
        }
        .sidebar-user-avatar.role-dokter {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            color: #fff;
        }

        .hamburger {
            width: 22px;
            height: 16px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .hamburger span {
            display: block;
            width: 100%;
            height: 2px;
            background: #334155;
            border-radius: 2px;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: center;
        }
        .hamburger.active span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
            background: #0d9488;
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        .hamburger.active span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
            background: #0d9488;
        }

        body.sidebar-open {
            overflow: hidden;
        }
    </style>
    @livewireStyles
    @yield('styles')
</head>

<body class="bg-slate-50 font-jakarta antialiased min-h-screen flex flex-col">

    <!-- overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!--mobile sidebar -->
    <aside id="mobile-sidebar" class="mobile-sidebar">

        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <a href="{{ route('home') }}" class="flex items-center space-x-3 group" onclick="closeSidebar()">
                <!-- logo -->
                <img src="{{ asset('images/logo-klinik.png') }}"
                    alt="Klinik Gigi Dokter Ndeso"class="h-9 w-auto">
                <div class="flex flex-col leading-tight">
                    <span class="text-base font-bold text-primary-700 tracking-tight">Klinik Gigi</span>
                    <span class="text-xs font-medium text-gray-500">Dokter Ndeso</span>
                </div>
            </a>
            <button class="sidebar-close" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <div class="sidebar-nav">

            <span class="sidebar-section-label">Menu</span>

            <!-- Beranda -->
            <a href="{{ route('home') }}" class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" onclick="closeSidebar()">
                <span class="sl-icon"><i class="fas fa-home"></i></span>
                <span>Beranda</span>
            </a>
            <!-- jika sudah login dan memiliki user dengan role pasien, tampilkan menu -->
            @auth
                @if (strtolower(Auth::user()->role ?? '') === 'pasien')
                    <span class="sidebar-section-label">Layanan</span>

                    <!-- Reservasi -->
                    <a href="{{ route('pasien.reservasi') }}" class="sidebar-link {{ request()->routeIs('pasien.riwayat') || request()->routeIs('pasien.pilih.klinik') || request()->routeIs('pasien.reservasi.*') ? 'active' : '' }}" onclick="closeSidebar()">
                        <span class="sl-icon"><i class="fas fa-calendar-check"></i></span>
                        <span>Reservasi</span>
                    </a>

                    <!-- Rekam Medis -->
                    <a href="{{ route('pasien.rekam-medis.index') }}" class="sidebar-link {{ request()->routeIs('pasien.rekam-medis.*') ? 'active' : '' }}" onclick="closeSidebar()">
                        <span class="sl-icon"><i class="fas fa-notes-medical"></i></span>
                        <span>Rekam Medis</span>
                    </a>

                    <!-- Profil -->
                    <a href="{{ route('pasien.profil') }}" class="sidebar-link {{ request()->routeIs('pasien.profil') || request()->routeIs('pasien.profil.update') ? 'active' : '' }}" onclick="closeSidebar()">
                        <span class="sl-icon"><i class="fas fa-user"></i></span>
                        <span>Profil</span>
                    </a>
                @endif
            @endauth
        </div>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <!-- jika blm login -->
            @guest
                <a href="{{ route('login') }}"
                   class="flex items-center justify-center gap-3 w-full px-5 py-3.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login / Masuk</span>
                </a>
            @endguest
            <!-- user yang sudah login -->
            @auth
                <div class="sidebar-user">
                    <!-- ambil huruf pertama untuk profil jika tidak ada pake default u -->
                    <div class="sidebar-user-avatar role-{{ strtolower(Auth::user()->role ?? 'pasien') }}">
                        {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <!-- tampilkan role -->
                        <p class="font-semibold text-gray-900 text-sm truncate">{{ Auth::user()->nama ?? 'Pengguna' }}</p>
                        <p class="text-xs text-gray-500">
                            @if (strtolower(Auth::user()->role ?? '') === 'admin') Administrator
                            @elseif (strtolower(Auth::user()->role ?? '') === 'dokter') Dokter
                            @else Pasien @endif
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center gap-2 w-full px-5 py-3 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-xl transition text-sm border border-red-200 hover:border-red-300"
                            onclick='confirmLogout(event, this.form, {{ json_encode(Auth::user()->nama ?? "Pengguna") }});'>
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endauth
        </div>
    </aside>

    <!-- header -->
    <header class="glass-header shadow-sm fixed w-full z-50 top-0 transition-all duration-300">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <img
                        src="{{ asset('images/logo-klinik.png') }}"
                        alt="Klinik Gigi Dokter Ndeso"
                        class="h-11 w-auto transition-transform duration-300 group-hover:scale-105"
                    >
                    <div class="flex flex-col leading-tight">
                        <span class="text-xl font-bold text-primary-700 tracking-tight">Klinik Gigi</span>
                        <span class="text-sm font-medium text-gray-600">Dokter Ndeso</span>
                    </div>
                </a>

                <!-- dekstop navigasi-->
                <div class="hidden md:flex items-center space-x-10">
                    <nav class="flex items-center space-x-10">
                        <!-- Beranda -->
                        <a href="{{ route('home') }}" class="nav-link text-gray-700 font-medium text-base py-2 flex items-center {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="fas fa-home mr-2 text-primary-500"></i>
                            Beranda
                        </a>
                        <!-- jika sudah login dan memiliki user dengan role pasien -->
                        @auth
                            @if (strtolower(Auth::user()->role ?? '') === 'pasien')
                                <!-- Riwayat Reservasi -->
                                <a href="{{ route('pasien.reservasi') }}" class="nav-link text-gray-700 font-medium text-base py-2 flex items-center {{ request()->routeIs('pasien.riwayat') || request()->routeIs('pasien.pilih.klinik') || request()->routeIs('pasien.reservasi.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-check mr-2 text-primary-500"></i>
                                    Reservasi
                                </a>
                                <!-- Rekam Medis -->
                                <a href="{{ route('pasien.rekam-medis.index') }}" class="nav-link text-gray-700 font-medium text-base py-2 flex items-center {{ request()->routeIs('pasien.rekam-medis.*') ? 'active' : '' }}">
                                    <i class="fas fa-notes-medical mr-2 text-primary-500"></i>
                                    Rekam Medis
                                </a>
                                <!-- Profil -->
                                <a href="{{ route('pasien.profil') }}" class="nav-link text-gray-700 font-medium text-base py-2 flex items-center {{ request()->routeIs('pasien.profil') || request()->routeIs('pasien.profil.update') ? 'active' : '' }}">
                                    <i class="fas fa-user mr-2 text-primary-500"></i>
                                    Profil
                                </a>
                            @endif
                        @endauth
                    </nav>

                    <!-- Guest Login Button -->
                    @guest
                        <a href="{{ route('login') }}"
                           class="ml-6 px-7 py-3 bg-primary-600 text-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md hover:bg-primary-700 flex items-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Login</span>
                        </a>
                    @endguest

                    <!-- Authenticated Profil & Logout -->
                    @auth
                        <div class="ml-6 flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <!-- ambil huruf pertama  untuk profil, jika tidak ada pake user u -->
                                <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-semibold text-lg border border-slate-200">
                                    {{ substr(Auth::user()->nama ?? 'U', 0, 1) }}
                                </div>
                                <div class="text-right">
                                    <!-- ambil nama dan role pengguna -->
                                    <p class="font-medium text-gray-800 text-sm leading-tight">
                                        {{ Auth::user()->nama ?? 'Pengguna' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        @if (strtolower(Auth::user()->role ?? '') === 'admin') Admin klinik
                                        @elseif (strtolower(Auth::user()->role ?? '') === 'dokter') Dokter
                                        @else Pasien @endif
                                    </p>
                                </div>
                            </div>
                            <!-- button logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="px-5 py-2.5 bg-red-50 hover:bg-red-100 text-red-700 font-medium rounded-lg transition text-sm border border-red-200 hover:border-red-300"
                                        onclick='confirmLogout(event, this.form, {{ json_encode(Auth::user()->nama ?? 'Pengguna') }});'>
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button
                    id="mobile-menu-btn"
                    class="md:hidden p-2.5 rounded-xl text-gray-700 hover:text-primary-600 hover:bg-slate-50 transition-all focus:outline-none focus:ring-2 focus:ring-slate-200"
                    onclick="toggleSidebar()">
                    <div id="hamburger" class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>

            </div>
        </div>
    </header>

    <main class="pt-20 flex-grow">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ambil elemen html
        const sidebar = document.getElementById('mobile-sidebar'); //sidebar menu menu
        const overlay = document.getElementById('sidebar-overlay'); 
        const hamburger = document.getElementById('hamburger'); //toggle menu
        // dipanggil ketika sidebar mau dibuka
        function openSidebar() {
            sidebar.classList.add('open'); //tambah class open
            overlay.classList.add('open'); //tampilkan overlay
            hamburger.classList.add('active'); // ubah ikon humberger
            document.body.classList.add('sidebar-open'); //cegah body scroll
        }
        // tutup sidebar
        function closeSidebar() {
            sidebar.classList.remove('open'); //sidebar hilang
            overlay.classList.remove('open');//overlay hilang
            hamburger.classList.remove('active'); //huberger kembali
            document.body.classList.remove('sidebar-open'); //bisa scroll
        }
        // toogle sidebar
        function toggleSidebar() {
            // jika sidebar dibuka maka tutup, jika sidebar tutup maka  buka
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // tutup sidear overlay otomatis nutup
        overlay.addEventListener('click', closeSidebar);

        // tombo; esc juga nutup
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });

        // dipanggil ketika ukurannya berubah
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768 && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });

        //tutup sidebar ketika klik menu
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                setTimeout(closeSidebar, 100);
            });
        });
        // fungsi logout
        function confirmLogout(event, form, userName) {
            event.preventDefault(); //jgn langsung submit
            // muncul popup
            Swal.fire({
                width: 340,
                html: `
                    <div class="text-center">
                        <div style="width:48px;height:48px;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;border-radius:2px;border:1px solid #fecaca;">
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
                    confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs rounded-sm font-bold uppercase tracking-wider transition',
                    cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs rounded-sm font-bold uppercase tracking-wider ml-2 transition'
                }
                // jika tekan logout, form dubmit.
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        }
    </script>
    @livewireScripts
    @yield('scripts')
</body>
</html>