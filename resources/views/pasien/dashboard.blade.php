@extends('layouts.pasien')

@section('title', 'Klinik Gigi Dokter Ndeso - Pelayanan Prima & Terpercaya')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap');

:root {
    --primary: #0f3d3e;
    --primary-mid: #134e4a;
    --accent: #14b8a6;
    --accent-dim: #0d9488;
    --text-dark: #1e293b;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --border: #e2e8f0;
    --surface: #ffffff;
    --surface-dim: #f8fafc;
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--text-dark);
    background: var(--surface-dim);
    line-height: 1.7;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
}

/* ── Reveal Animation ── */
.reveal { opacity: 0; transform: translateY(20px); transition: opacity .6s ease, transform .6s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
.reveal-delay-1 { transition-delay: .1s; }
.reveal-delay-2 { transition-delay: .2s; }

/* ── HERO (Gambar Jalan + Overlay Hitam + Watermark Gigi) ── */
.hero {
    position: relative;
    overflow: hidden;
    padding: 8rem 2rem 6rem;
    background: #000000b5;
}

.swiper-hero { width: 100%; height: 100%; position: absolute; inset: 0; z-index: 1; }

.hero-slide {
    position: relative;
    width: 100%; height: 100%;
    background-size: cover;
    background-position: center;
}

.hero-slide::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(105deg, rgba(7, 27, 24, 0.95) 0%, rgba(15, 37, 33, 0.92) 40%, rgba(2, 41, 35, 0.85) 100%);
    z-index: 1;
}


@keyframes floatSlow {
    0% { transform: translateY(-50%) rotate(-5deg); }
    100% { transform: translateY(-48%) rotate(5deg); }
}

.hero-inner {
    position: relative; z-index: 2;
    max-width: 1200px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between; gap: 4rem;
}

.hero-content { max-width: 650px; }

.hero-label {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: .7rem; font-weight: 700;
    color: var(--accent);
    background: rgba(20,184,166,0.1);
    border: 1px solid rgba(20,184,166,0.2);
    padding: 6px 14px; border-radius: 4px;
    letter-spacing: .12em; text-transform: uppercase;
    margin-bottom: 1.5rem;
}

.hero-title {
    font-size: 3rem; font-weight: 900;
    color: #ffffff; line-height: 1.1;
    margin-bottom: 1.5rem; letter-spacing: -.03em;
}
.hero-title span { color: var(--accent); text-shadow: 0 0 20px rgba(20,184,166,0.4); }

.hero-desc {
    font-size: 1.05rem; color: rgba(255,255,255,0.6);
    line-height: 1.8; margin-bottom: 2.5rem; max-width: 500px;
}

.hero-actions { display: flex; gap: 1rem; flex-wrap: wrap; }

/* ── Buttons ── */
.btn-hero {
    display: inline-flex; align-items: center; gap: 8px;
    padding: .875rem 1.75rem;
    font-weight: 700; font-size: .85rem;
    text-decoration: none; transition: all .3s ease;
    border: 2px solid transparent; cursor: pointer;
    text-transform: uppercase; letter-spacing: .05em; border-radius: 4px;
}
.btn-hero-primary { background: var(--accent); color: #000000; border-color: var(--accent); }
.btn-hero-primary:hover { background: #ffffff; color: #000000; border-color: #ffffff; transform: translateY(-2px); }
.btn-hero-outline { background: transparent; color: #ffffff; border-color: rgba(255,255,255,0.2); }
.btn-hero-outline:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.5); color: #ffffff; }

.btn-direction {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: .7rem 1.25rem; background: var(--primary); color: white;
    font-weight: 700; font-size: .8rem; text-decoration: none; transition: all .3s ease;
    border: none; cursor: pointer; border-radius: 4px; align-self: flex-start; text-transform: uppercase;
}
.btn-direction:hover { background: var(--accent); color: #000; transform: translateY(-2px); }

.btn-cta {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--accent); color: #000000; padding: .875rem 2rem;
    font-weight: 800; font-size: .9rem; text-decoration: none; transition: all .3s ease;
    border: none; cursor: pointer; text-transform: uppercase; border-radius: 4px;
}
.btn-cta:hover { background: #ffffff; transform: translateY(-2px); }

/* ── Info Widget di Kanan Hero ── */
.hero-widget {
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid rgba(17, 57, 52, 0.92);
    border-top: 3px solid var(--accent);
    backdrop-filter: blur(10px);
    border-radius: 8px; padding: 2rem;
    max-width: 380px; width: 100%;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}
.widget-title { font-size: .8rem; color: var(--accent); font-weight: 700; letter-spacing: .1em; text-transform: uppercase; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; }
.widget-item { display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.2rem; }
.widget-icon { width: 40px; height: 40px; background: rgba(20,184,166,0.15); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1rem; flex-shrink: 0; }
.widget-item h4 { font-size: .9rem; color: #fff; margin-bottom: 2px; }
.widget-item p { font-size: .8rem; color: rgba(255,255,255,0.5); line-height: 1.4; }

/* ── Quick Access ── */
.quick-access { position: relative; z-index: 10; margin-top: -30px; padding: 0 1.5rem; margin-bottom: 3rem; }
.quick-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(3, 1fr); background: var(--surface); box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid var(--border); border-top: 4px solid var(--accent); }
.quick-item { padding: 1.25rem; display: flex; align-items: center; gap: 1rem; border-right: 1px solid var(--border); transition: all .3s ease; text-decoration: none; color: var(--text-dark); }
.quick-item:last-child { border-right: none; }
.quick-item:hover { background: #f1f5f9; }
.quick-item:hover .quick-icon { background: var(--primary); color: white; }
.quick-icon { width: 44px; height: 44px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; color: var(--primary-mid); flex-shrink: 0; transition: all .3s ease; border-radius: 4px; }
.quick-text-title { font-size: .9rem; font-weight: 700; margin-bottom: 2px; color: var(--text-dark); }
.quick-text-desc { font-size: .75rem; color: var(--text-muted); line-height: 1.4; }

/* ── Section Common ── */
.section { padding: 4rem 1.5rem; }
.section-inner { max-width: 1200px; margin: 0 auto; }
.section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; border-bottom: 2px solid var(--accent); padding-bottom: 1rem; }
.section-title { font-size: 1.75rem; font-weight: 800; color: var(--text-dark); letter-spacing: -.02em; line-height: 1.2; }

/* ── Services ── */
.services-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
.svc-card { background: var(--surface); border: 1px solid var(--border); padding: 1.75rem; transition: all .3s ease; text-decoration: none; color: var(--text-dark); display: flex; flex-direction: column; border-radius: 4px; position: relative; overflow: hidden; }
.svc-card::after { content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 3px; background: var(--accent); transform: scaleX(0); transform-origin: left; transition: transform .3s ease; }
.svc-card:hover { border-color: rgba(20,184,166,0.3); transform: translateY(-4px); box-shadow: 0 10px 20px rgba(20,184,166,0.06); }
.svc-card:hover::after { transform: scaleX(1); }
.svc-card:hover .svc-icon { background: var(--accent); color: #021716; }
.svc-icon { width: 44px; height: 44px; background: #f0fdfa; border: 1px solid rgba(20,184,166,0.2); display: flex; align-items: center; justify-content: center; color: var(--primary-mid); font-size: 1rem; margin-bottom: 1.25rem; transition: all .3s ease; border-radius: 4px; }
.svc-name { font-size: .95rem; font-weight: 700; margin-bottom: .4rem; color: var(--text-dark); text-transform: capitalize; }
.svc-desc { font-size: .85rem; color: var(--text-secondary); line-height: 1.6; }

/* ── Locations ── */
.locations-section { background: #ffffff; }
.loc-split { display: grid; grid-template-columns: 1.2fr 1fr; background: var(--surface-dim); border: 1px solid var(--border); overflow: hidden; margin-bottom: 1.5rem; transition: all .3s ease; border-radius: 4px; }
.loc-split:hover { border-color: var(--accent); }
.loc-split.reversed { direction: rtl; }
.loc-split.reversed > * { direction: ltr; }
.loc-map-split { position: relative; min-height: 250px; background: var(--border); }
.loc-map-split iframe { width: 100%; height: 100%; border: none; position: absolute; inset: 0; }
.loc-body-split { padding: 2rem; display: flex; flex-direction: column; justify-content: center; }
.loc-name { font-size: 1.1rem; font-weight: 800; color: var(--text-dark); margin-bottom: .5rem; display: flex; align-items: center; gap: 8px; text-transform: uppercase; letter-spacing: .02em; }
.loc-name i { color: var(--accent); }
.loc-address { font-size: .85rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: .75rem; }
.loc-phone { display: flex; align-items: center; gap: 6px; font-size: .85rem; color: var(--text-secondary); margin-bottom: 1.25rem; font-weight: 600; }
.loc-phone i { color: var(--accent); width: 16px; text-align: center; }

/* ── CTA ── */
.cta-section { position: relative; background: linear-gradient(135deg, var(--primary-mid) 0%, #000000 100%); color: #fff; padding: 5rem 2rem; text-align: center; overflow: hidden; }
.cta-watermark { position: absolute; top: 50%; left: -10%; transform: translateY(-50%); font-size: 30rem; color: rgba(20, 184, 166, 0.03); pointer-events: none; }
.cta-inner { max-width: 700px; margin: 0 auto; position: relative; z-index: 2; }
.cta-heading { font-size: 2rem; font-weight: 900; margin-bottom: 1rem; line-height: 1.15; }
.cta-heading span { color: var(--accent); text-shadow: 0 0 15px rgba(20,184,166,0.4); }
.cta-text { font-size: 1rem; color: rgba(255,255,255,0.6); margin-bottom: 2.5rem; line-height: 1.7; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .hero-inner { flex-direction: column; text-align: center; }
    .hero-content { max-width: 100%; }
    .hero-desc { margin-left: auto; margin-right: auto; }
    .hero-actions { justify-content: center; }
    .hero-widget { max-width: 100%; }
    .loc-split { grid-template-columns: 1fr; }
    .loc-map-split { min-height: 220px; }
}
@media (max-width: 768px) {
    .hero-widget {
        text-align: left;
        align-items: flex-start;
    }
    
    .hero-widget .widget-title {
        justify-content: flex-start;
    }
    
    .hero-widget .widget-item {
        text-align: left;
        justify-content: flex-start;
    }
}
@media (max-width: 768px) {
    .hero { padding: 5rem 1.5rem 3.5rem; }
    .hero-title { font-size: 2rem; }
    .hero-watermark { font-size: 20rem; right: -15%; opacity: 0.03; }
    
    .quick-access { margin-top: -25px; padding: 0 1rem; margin-bottom: 2rem; }
    /* PAKSA QUICK ACCESS 3 GRID SEBELAHAN */
    .quick-inner { grid-template-columns: repeat(3, 1fr) !important; }
    .quick-item { 
        flex-direction: column; 
        text-align: center; 
        padding: 1rem 0.5rem; 
        gap: 0.5rem;
    }
    .quick-icon { margin: 0; }
    .quick-text-title { font-size: 0.7rem; }
    .quick-text-desc { display: none; } /* Sembunyikan deskripsi biar muat sebelahan */
    
    .section { padding: 3rem 1rem; }
    .section-header { flex-direction: column; align-items: flex-start; gap: .5rem; }
    .section-title { font-size: 1.5rem; }
    
    /* PAKSA LAYANAN 3 GRID DI HP */
    .services-grid { grid-template-columns: repeat(3, 1fr) !important; gap: 0.5rem !important; }
    .svc-card { padding: 1rem 0.5rem; align-items: center; text-align: center; }
    .svc-icon { width: 36px; height: 36px; font-size: 0.85rem; margin-bottom: 0.75rem; }
    .svc-name { font-size: 0.7rem; line-height: 1.2; margin-bottom: 0; }
    .svc-desc { display: none; }

    /* PAKSA LOKASI JADI 2 GRID SEBELAHAN */
    #locations .section-inner {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    /* Biar Judul "Lokasi Klinik" gak masuk 2 kolom */
    #locations .section-header { 
        grid-column: 1 / -1; 
    }
    .loc-split { 
        margin-bottom: 0 !important; 
        grid-template-columns: 1fr !important;
    }
    .loc-map-split { min-height: 120px !important; }
    .loc-body-split { padding: 1rem; }
    .loc-name { font-size: 0.8rem; margin-bottom: 0.25rem; }
    .loc-address { font-size: 0.7rem; margin-bottom: 0.5rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .loc-phone { font-size: 0.7rem; margin-bottom: 0.75rem; }
    .btn-direction { font-size: 0.7rem; padding: 0.5rem 0.75rem; align-self: center; width: 100%; }

    .cta-heading { font-size: 1.5rem; }
}

@media (max-width: 480px) {
    .hero-title { font-size: 1.7rem; }
    .svc-name { font-size: 0.65rem; }
}
</style>

<!-- hero-->
<section class="hero">
    <div class="swiper swiper-hero">
        <div class="swiper-wrapper">
            <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('images/hero-1.png') }}')"></div>
            <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('images/hero-2.png') }}')"></div>
            <div class="swiper-slide hero-slide" style="background-image: url('{{ asset('images/hero-3.png') }}')"></div>
        </div>
    </div>

    <div class="hero-inner">
        <div class="hero-content reveal">
            <div class="hero-label">
                <i class="fas fa-tooth"></i> Spesialis Kesehatan Gigi
            </div>
            <h1 class="hero-title">Jaga <span>Kesehatan Gigi</span> Untuk Masa Depan Lebih Baik</h1>
            <p class="hero-desc">Perawatan gigi profesional dengan teknologi terkini. Dokter berpengalaman, pelayanan cepat, dan hasil maksimal untuk senyum Anda.</p>
            <div class="hero-actions">
                <a href="{{ auth()->check() ? route('pasien.reservasi') : route('login') }}" class="btn-hero btn-hero-primary">
                    <i class="fas fa-calendar-alt"></i> Buat Reservasi
                </a>
                <a href="#services" class="btn-hero btn-hero-outline">
                    <i class="fas fa-stethoscope"></i> Layanan Kami
                </a>
            </div>
        </div>

        <div class="hero-widget reveal reveal-delay-2">
            <div class="widget-title"><i class="fas fa-hospital"></i> Informasi Klinik</div>
            <div class="widget-item">
                <div class="widget-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h4>Jam Operasional</h4>
                    <p>Senin - Minggu: 09.00 - 18.00 WIB</p>
                </div>
            </div>
            <div class="widget-item">
                <div class="widget-icon"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <h4>Standar Kesehatan</h4>
                    <p>Sterilisasi alat 100% sesuai WHO</p>
                </div>
            </div>
            <div class="widget-item">
                <div class="widget-icon"><i class="fas fa-heart-pulse"></i></div>
                <div>
                    <h4>Pelayanan Ramah</h4>
                    <p>Konsultasi nyaman dan penanganan profesional</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- akses cepat-->
<section class="quick-access">
    <div class="quick-inner reveal">
        <a href="{{ auth()->check() ? route('pasien.reservasi.create') : route('login') }}" class="quick-item">
            <div class="quick-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
                <div class="quick-text-title">Reservasi Online</div>
                <div class="quick-text-desc">Buat janji temu dengan mudah</div>
            </div>
        </a>
        <a href="#services" class="quick-item">
            <div class="quick-icon"><i class="fas fa-tooth"></i></div>
            <div>
                <div class="quick-text-title">Layanan Klinik</div>
                <div class="quick-text-desc">Perawatan gigi profesional</div>
            </div>
        </a>
        <a href="#locations" class="quick-item">
            <div class="quick-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <div class="quick-text-title">Lokasi Klinik</div>
                <div class="quick-text-desc">Temukan klinik terdekat</div>
            </div>
        </a>
    </div>
</section>

<!-- pelayanan -->
<section class="section" id="services">
    <div class="section-inner">
        <div class="section-header reveal">
            <h2 class="section-title">Layanan Unggulan</h2>
        </div>
        <div class="services-grid">
            @foreach([
                ['icon' => 'fas fa-check',             'name' => 'Pemeriksaan Rutin',  'desc' => 'Pemeriksaan menyeluruh untuk mendeteksi masalah gigi dan gusi lebih awal'],
                ['icon' => 'fas fa-spray-can',         'name' => 'Pembersihan Karang', 'desc' => 'Pembersihan profesional untuk menghilangkan karang gigi dan menjaga kesehatan gusi'],
                ['icon' => 'fas fa-tooth',             'name' => 'Tambal Gigi',        'desc' => 'Penambalan gigi berlubang dengan material berkualitas tinggi yang tahan lama'],
                ['icon' => 'fas fa-head-side-virus',   'name' => 'Cabut Gigi',         'desc' => 'Pencabutan gigi dengan teknik profesional dan penanganan pasca operasi'],
                ['icon' => 'fas fa-wand-magic-sparkles','name' => 'Pemutihan Gigi',     'desc' => 'Layanan pemutihan gigi profesional untuk senyum yang lebih cerah'],
                ['icon' => 'fas fa-crown',             'name' => 'Pemasangan Mahkota',  'desc' => 'Pemasangan mahkota gigi dengan desain natural sesuai warna gigi asli'],
            ] as $i => $svc)
                <div class="svc-card reveal reveal-delay-{{ ($i % 3) + 1 }}">
                    <div class="svc-icon"><i class="{{ $svc['icon'] }}"></i></div>
                    <h3 class="svc-name">{{ $svc['name'] }}</h3>
                    <p class="svc-desc">{{ $svc['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- lokasi -->
<section class="section locations-section" id="locations">
    <div class="section-inner">
        <div class="section-header reveal">
            <h2 class="section-title">Lokasi Klinik</h2>
        </div>
        <!-- loop data klinik -->
        @php $kliniks = $kliniks ?? \App\Models\Klinik::all(); @endphp
        @foreach($kliniks as $index => $klinik)
            <div class="loc-split reveal {{ $index % 2 !== 0 ? 'reversed' : '' }}">
                <div class="loc-map-split">
                    @if($klinik->maps_url)
                        <iframe src="{{ $klinik->maps_url }}" allowfullscreen loading="lazy"></iframe>
                    @else
                        <div style="width:100%;height:100%;background:var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);"><i class="fas fa-map fa-3x"></i></div>
                    @endif
                </div>
                <div class="loc-body-split">
                    <h3 class="loc-name"><i class="fas fa-map-marker-alt"></i> {{ $klinik->nama_klinik }}</h3>
                    <p class="loc-address">{{ $klinik->alamat }}</p>
                    @if($klinik->no_telepon)
                        <div class="loc-phone"><i class="fas fa-phone"></i> {{ $klinik->no_telepon }}</div>
                    @endif
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($klinik->nama_klinik . ' ' . $klinik->alamat) }}" target="_blank" rel="noopener" class="btn-direction">
                        <i class="fas fa-directions"></i> Petunjuk Arah
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- cta-->
<section class="cta-section">
    <div class="cta-inner reveal">
        <h2 class="cta-heading">Siap untuk Senyuman <span>Lebih Sehat</span>?</h2>
        <p class="cta-text">Jangan tunda lagi perawatan gigi Anda. Reservasi sekarang dan dapatkan pelayanan terbaik kami.</p>
        @guest
            <a href="{{ route('login') }}" class="btn-cta"><i class="fas fa-tooth"></i> Reservasi Sekarang</a>
        @endguest
        @auth
            <a href="{{ route('pasien.reservasi') }}" class="btn-cta"><i class="fas fa-tooth"></i> Reservasi Sekarang</a>
        @endauth
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const heroSwiper = new Swiper('.swiper-hero', { //slider otomatis
        loop: true, //loop muter terus
        autoplay: { delay: 5000, disableOnInteraction: false }, //otomatis slide 5 detik
        effect: 'fade', //efect fdade memudar
        fadeEffect: { crossFade: true },
        speed: 1500,
    });
    //animasi scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible'); //kalo elemen terlihat di layar
        });
    }, { threshold: .1, rootMargin: '0px 0px -30px 0px' });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    document.querySelectorAll('a[href^="#"]').forEach(anchor => { //klik link scroll halus antar section
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });
});
</script>
@endsection