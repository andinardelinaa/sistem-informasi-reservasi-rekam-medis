@extends('layouts.admin')

@section('header-title', 'Detail Rekam Medis - ' . ($klinikNama ?? 'Klinik'))
@include('layouts.partials.alert')

@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

    /* ========================================
       BASE STYLES
       ======================================== */
    .rm-bg {
        background-color: #e5e7eb;
        min-height: 100vh;
        padding: 0.75rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    @media (min-width: 640px) { .rm-bg { padding: 1.5rem; } }

    .rm-paper {
        background: #ffffff;
        width: 100%;
        max-width: 950px;
        margin: 0 auto;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #374151;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    /* KOP */
    .rm-kop {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem 1.25rem 0.75rem;
        border-bottom: 2px solid #111827;
    }
    @media (min-width: 768px) {
        .rm-kop { padding: 1.5rem 2rem 1rem; }
    }
    .rm-kop-left { display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 0; }
    .rm-kop-logo {
        width: 48px; height: 48px; background: #fff; border-radius: 6px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid #111827; flex-shrink: 0;
    }
    .rm-kop-logo img { width: 28px; height: 28px; object-fit: contain; }
    .rm-kop-text h1 {
        font-size: 0.95rem; font-weight: 800; color: #111827;
        line-height: 1.2; letter-spacing: 0.5px; text-transform: uppercase;
    }
    @media (min-width: 640px) { .rm-kop-text h1 { font-size: 1.15rem; } }
    .rm-kop-text h2 {
        font-size: 0.72rem; font-weight: 700; color: #374151;
        margin-top: 0.15rem; letter-spacing: 2px; text-transform: uppercase;
    }
    @media (min-width: 640px) { .rm-kop-text h2 { font-size: 0.85rem; } }
    .rm-kop-text p { font-size: 0.68rem; color: #4b5563; margin-top: 0.2rem; }
    
    .rm-kop-right { 
        text-align: right; 
        flex-shrink: 0; 
    }
    .rm-kop-id {
        display: inline-block; border: 2px solid #111827;
        padding: 0.35rem 0.8rem; font-family: 'JetBrains Mono', monospace;
        font-size: 0.8rem; font-weight: 700; color: #111827;
        border-radius: 3px; background: #fff; letter-spacing: 1px;
    }
    .rm-kop-meta { font-size: 0.68rem; color: #4b5563; margin-top: 0.4rem; font-weight: 500; }

    /* SECTIONS */
    .rm-section {
        padding: 1rem 1.25rem;
        border-bottom: 1px dashed #9ca3af;
    }
    @media (min-width: 768px) { .rm-section { padding: 1.25rem 2rem; } }
    .rm-section-title {
        font-size: 0.62rem; color: #6b7280; text-transform: uppercase;
        letter-spacing: 2px; font-weight: 700; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .rm-section-title::after { content: ''; flex: 1; height: 1px; background: #d1d5db; }
    
    .rm-grid-2 { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 1.5rem; 
        width: 100%;
    }
    @media (min-width: 640px) { .rm-grid-2 { gap: 2.5rem; } }

    .rm-info-row {
        display: flex; 
        flex-direction: row; 
        align-items: baseline;
        padding: 0.35rem 0;
        font-size: 0.78rem; 
        border-bottom: 1px dotted #e5e7eb; 
        gap: 0.5rem;
    }
    .rm-info-row:last-child { border-bottom: none; }
    .rm-info-label { color: #6b7280; font-weight: 500; font-size: 0.75rem; width: 110px; flex-shrink: 0; }
    .rm-info-value { color: #111827; font-weight: 600; word-break: break-word; flex: 1; }

    /* BOXES */
    .rm-box {
        background: #f9fafb; border: 1px solid #e5e7eb;
        border-radius: 3px; padding: 0.875rem;
    }
    @media (min-width: 640px) { .rm-box { padding: 1rem; } }
    .rm-box-label {
        font-size: 0.58rem; color: #374151; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;
        display: flex; align-items: center; gap: 0.35rem;
    }
    .rm-box-value { font-size: 0.85rem; color: #374151; line-height: 1.6; word-break: break-word; }

    /* ODONTOGRAM */
    .rm-odontogram {
        padding: 1rem 1.25rem; border-bottom: 1px dashed #9ca3af; overflow-x: auto;
    }
    @media (min-width: 768px) { .rm-odontogram { padding: 1.25rem 2rem; } }
    .odo-title {
        font-size: 0.62rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .odo-title::after { content: ''; flex: 1; height: 1px; background: #d1d5db; }
    .odo-chart {
        display: flex; flex-direction: column; align-items: center;
        gap: 0.5rem; min-width: 480px;
    }
    .odo-row { display: flex; align-items: flex-end; gap: 0.25rem; }
    @media (min-width: 640px) { .odo-row { gap: 0.35rem; } }
    .odo-quadrant { display: flex; gap: 0.15rem; }
    @media (min-width: 640px) { .odo-quadrant { gap: 0.25rem; } }
    .odo-quadrant.reverse { flex-direction: row-reverse; }
    .odo-tooth { display: flex; flex-direction: column; align-items: center; gap: 0.1rem; }
    .odo-tooth-shape {
        width: 18px; height: 24px; background: #fff; border: 1.5px solid #9ca3af;
        border-radius: 3px 3px 5px 5px; display: flex; align-items: center; justify-content: center;
        font-family: 'JetBrains Mono', monospace; font-size: 0.5rem; font-weight: 700; color: #9ca3af;
    }
    @media (min-width: 640px) {
        .odo-tooth-shape { width: 22px; height: 28px; font-size: 0.6rem; }
    }
    .odo-tooth-shape.primary {
        width: 15px; height: 20px; border-radius: 2px 2px 4px 4px;
    }
    @media (min-width: 640px) {
        .odo-tooth-shape.primary { width: 18px; height: 24px; }
    }
    .odo-tooth-shape.has-record { border-color: #111827; background: #f3f4f6; color: #111827; font-weight: 800; }
    .odo-divider { width: 1px; height: 38px; background: #d1d5db; margin: 0 0.15rem; }
    @media (min-width: 640px) { .odo-divider { margin: 0 0.3rem; height: 45px; } }
    .odo-arc { font-size: 0.55rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; text-align: center; margin-top: 0.15rem; font-weight: 600; }

    /* TABLE */
    .rm-table-wrap { padding: 1rem 1.25rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    @media (min-width: 768px) { .rm-table-wrap { padding: 1.25rem 2rem; overflow-x: visible; } }
    .rm-table {
        width: 100%; min-width: 500px; border-collapse: collapse;
        font-size: 0.78rem; border: 2px solid #111827;
    }
    @media (min-width: 640px) { .rm-table { min-width: auto; font-size: 0.82rem; } }
    .rm-table thead th {
        background: #f3f4f6; color: #111827; font-weight: 700;
        text-transform: uppercase; font-size: 0.6rem; letter-spacing: 0.5px;
        padding: 0.55rem 0.65rem; border: 1px solid #6b7280; text-align: center; white-space: nowrap;
    }
    @media (min-width: 640px) { .rm-table thead th { font-size: 0.65rem; padding: 0.65rem 0.85rem; } }
    .rm-table tbody td {
        padding: 0.6rem 0.65rem; border: 1px solid #9ca3af;
        color: #374151; vertical-align: middle;
    }
    @media (min-width: 640px) { .rm-table tbody td { padding: 0.7rem 0.85rem; } }
    .rm-table tbody tr:nth-child(even) td { background: #f9fafb; }
    .rm-badge {
        display: inline-block; padding: 0.2rem 0.55rem; border-radius: 3px;
        font-size: 0.68rem; font-weight: 600; white-space: nowrap; border: 1px solid;
    }
    @media (min-width: 640px) { .rm-badge { padding: 0.22rem 0.65rem; font-size: 0.72rem; } }
    .badge-green { background: #f0fdf4; color: #166534; border-color: #166534; }
    .badge-red { background: #fef2f2; color: #991b1b; border-color: #991b1b; }
    .badge-amber { background: #fffbeb; color: #92400e; border-color: #92400e; }
    .badge-blue { background: #eff6ff; color: #1e40af; border-color: #1e40af; }
    .badge-gray { background: #f9fafb; color: #374151; border-color: #374151; }
    .badge-purple { background: #faf5ff; color: #6b21a8; border-color: #6b21a8; }
    .rm-gigi {
        width: 26px; height: 26px; background: #fff; color: #111827;
        border-radius: 3px; display: inline-flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.75rem; border: 2px solid #111827;
        font-family: 'JetBrains Mono', monospace;
    }
    @media (min-width: 640px) { .rm-gigi { width: 30px; height: 30px; font-size: 0.85rem; } }
    .count-badge {
        font-size: 0.68rem; color: #374151; background: #f3f4f6;
        padding: 0.2rem 0.65rem; border-radius: 3px; white-space: nowrap;
        border: 1px solid #6b7280; font-weight: 600;
    }
    .rm-empty {
        text-align: center; padding: 1.5rem; color: #9ca3af;
        background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 3px; font-size: 0.82rem;
    }

    /* FOOTER */
    .rm-footer {
        padding: 1.25rem; text-align: center; background: #fff;
        border-top: 1px dashed #9ca3af;
        break-inside: avoid;
        page-break-inside: avoid;
    }
    @media (min-width: 768px) { .rm-footer { padding: 1.5rem 2rem; } }
    .rm-footer .thanks { font-size: 0.85rem; color: #111827; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; }
    @media (min-width: 640px) { .rm-footer .thanks { font-size: 0.95rem; letter-spacing: 3px; } }
    .rm-footer .sub { font-size: 0.68rem; color: #6b7280; margin-top: 0.5rem; line-height: 1.5; }
    .rm-footer .barcode { margin-top: 1rem; padding: 0.75rem; background: #f9fafb; border-radius: 3px; display: inline-block; border: 1px solid #d1d5db; break-inside: avoid; page-break-inside: avoid; }
    .rm-footer .barcode-line {
        font-family: "Courier New", monospace !important;
        font-size: 24px !important;
        font-weight: 700;
        color: #111827;
        letter-spacing: -2px;
        line-height: 1;
        white-space: nowrap;
    }
    @media (min-width: 640px) { .rm-footer .barcode-line { font-size: 1.45rem; letter-spacing: 3px; } }
    .rm-footer .barcode-num { font-family: 'JetBrains Mono', monospace; font-size: 0.62rem; color: #6b7280; margin-top: 0.25rem; }

    /* FLOATING BUTTONS */
    .fab-container {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        z-index: 1000;
    }
    .fab {
        width: 52px; height: 52px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; border: none; cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25); transition: all 0.2s;
        font-size: 1.1rem; text-decoration: none;
    }
    .fab:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 6px 20px rgba(0,0,0,0.35); }
    .fab-back { background: #6b7280; } .fab-back:hover { background: #4b5563; }
    .fab-pdf { background: #dc2626; } .fab-pdf:hover { background: #b91c1c; }
    .fab-print { background: #1f2937; } .fab-print:hover { background: #111827; }
    
    .fab-label {
        position: absolute; right: 60px; background: #1f2937; color: white; padding: 0.4rem 0.85rem;
        border-radius: 4px; font-size: 0.75rem; font-weight: 600; white-space: nowrap;
        opacity: 0; visibility: hidden; transition: all 0.2s; pointer-events: none;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
        letter-spacing: 0.3px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
    .fab:hover .fab-label { opacity: 1; visibility: visible; right: 65px; }

    /* ========================================
       PRINT STYLES
       ======================================== */
    @media print {
        @page { margin: 6mm; size: A4 portrait; }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        html, body {
            background: white !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        body > *:not(#printArea):not(.pdf-clone-wrapper) {
            display: none !important;
        }
        
        .rm-bg {
            display: none !important;
        }
        
        #printArea,
        .pdf-clone-wrapper {
            display: block !important;
            position: static !important; 
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }
        
        #printArea *,
        .pdf-clone-wrapper * {
            visibility: visible !important;
        }
        
        .fab-container { 
            display: none !important; 
            visibility: hidden !important;
        }
        
        .rm-paper {
            max-width: 100% !important;
            width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }
        
        .rm-table th { background-color: #f3f4f6 !important; color: #111827 !important; border: 1px solid #6b7280 !important; }
        .rm-table td { border: 1px solid #9ca3af !important; color: #374151 !important; }
        .rm-table tbody tr:nth-child(even) td { background: #f9fafb !important; }
        .rm-box { border: 1px solid #e5e7eb !important; background: #f9fafb !important; }
        .rm-footer { border-top: 1px dashed #9ca3af !important; break-inside: avoid !important; page-break-inside: avoid !important; }
        
        .badge-green { background: #f0fdf4 !important; color: #166534 !important; border-color: #166534 !important; }
        .badge-red { background: #fef2f2 !important; color: #991b1b !important; border-color: #991b1b !important; }
        .badge-amber { background: #fffbeb !important; color: #92400e !important; border-color: #92400e !important; }
        .badge-blue { background: #eff6ff !important; color: #1e40af !important; border-color: #1e40af !important; }
        .badge-gray { background: #f9fafb !important; color: #374151 !important; border-color: #374151 !important; }
        .badge-purple { background: #faf5ff !important; color: #6b21a8 !important; border-color: #6b21a8 !important; }
        .rm-gigi { background: #fff !important; color: #111827 !important; border: 2px solid #111827 !important; }
        .odo-tooth-shape.has-record { border-color: #111827 !important; background: #f3f4f6 !important; color: #111827 !important; }
        
        .rm-odontogram, .rm-table-wrap { overflow-x: visible !important; padding-left: 0 !important; padding-right: 0 !important; }
        .odo-chart, .rm-table { min-width: 0 !important; width: 100% !important; }
        
        .rm-kop { padding: 1rem !important; }
        .rm-section { padding: 0.75rem 1rem !important; }
        .rm-odontogram { padding: 0.75rem 1rem !important; }
        .rm-table-wrap { padding: 0.75rem 1rem !important; }
        .rm-footer { padding: 1rem !important; }
    }

    /* ========================================
       PDF CLONE WRAPPER
       ======================================== */
    .pdf-clone-wrapper {
        position: absolute;
        top: -9999px;
        left: -9999px;
        width: 794px;
        height: auto;
        min-height: 100vh;
        background: #ffffff;
        overflow: visible;
        visibility: visible;
        opacity: 1;
    }
    .pdf-clone-wrapper .rm-paper {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        margin: 0 !important;
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        background: #ffffff !important;
        overflow: visible !important;
    }
    .pdf-clone-wrapper .rm-bg {
        background: #ffffff !important;
        padding: 0 !important;
        min-height: auto !important;
    }
    .pdf-clone-wrapper .rm-odontogram,
    .pdf-clone-wrapper .rm-table-wrap {
        overflow-x: visible !important;
    }
    .pdf-clone-wrapper .rm-footer {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
    .pdf-clone-wrapper .rm-footer .barcode {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
    .pdf-clone-wrapper .odo-chart,
    .pdf-clone-wrapper .rm-table {
        min-width: 0 !important;
        width: 100% !important;
    }

    ::-webkit-scrollbar { height: 5px; width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 3px; }
</style>
<!-- mendefinisikan nomor gigi -->
@php
    $upperRight = [18,17,16,15,14,13,12,11];
    $upperLeft  = [21,22,23,24,25,26,27,28];
    $lowerLeft  = [31,32,33,34,35,36,37,38];
    $lowerRight = [48,47,46,45,44,43,42,41];
    $primaryUpperRight = [55,54,53,52,51];
    $primaryUpperLeft  = [61,62,63,64,65];
    $primaryLowerLeft  = [71,72,73,74,75];
    $primaryLowerRight = [85,84,83,82,81];

    $detailByTooth = [];
    foreach($rekamMedis->detailGigi as $detail) {
        $detailByTooth[$detail->nomor_gigi] = $detail;
    }

    function toothClass($detail) {
        if(!$detail) return '';
        return 'has-record';
    }
@endphp

<div class="rm-bg">
    <div class="rm-paper" id="printArea">
        <!-- KOP -->
        <div class="rm-kop">
            <div class="rm-kop-left">
                <div class="rm-kop-logo">
                    <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo">
                </div>
                <div class="rm-kop-text">
                    <h1>KLINIK GIGI DOKTER NDESO</h1>
                    <h2>REKAM MEDIS</h2>
                    <p>{{ $rekamMedis->reservasi->jadwal?->klinik->nama_klinik ?? 'Klinik' }} â€¢ Telp: 08112784411</p>
                </div>
            </div>
            <div class="rm-kop-right"> <!--ambil id rm dan tanggal untuk di kop-->
                <div class="rm-kop-id">RM-{{ $rekamMedis->id_rm }}</div>
                <div class="rm-kop-meta">{{ $rekamMedis->tgl_periksa->translatedFormat('l, d F Y') }}</div>
            </div>
        </div>

        <!-- Informasi Pasien & Dokter -->
        <div class="rm-section">
            <div class="rm-section-title">
                <i class="fas fa-user-injured"></i>
                Informasi Pasien & Dokter
            </div>
            <div class="rm-grid-2">
                <div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Nama Pasien</span>
                        <span class="rm-info-value">{{ $rekamMedis->reservasi->pasien->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">No. HP</span>
                        <span class="rm-info-value">{{ $rekamMedis->reservasi->pasien->no_hp ?? '-' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Alamat</span>
                        <span class="rm-info-value">{{ $rekamMedis->reservasi->pasien->alamat ?? '-' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Keluhan Awal</span>
                        <span class="rm-info-value">{{ $rekamMedis->reservasi->keluhan ?? '-' }}</span>
                    </div>
                </div>
                <div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Dokter</span>
                        <span class="rm-info-value">{{ $rekamMedis->dokter->nama ?? 'N/A' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Spesialis</span>
                        <span class="rm-info-value">{{ $rekamMedis->dokter->spesialisasi ?? '-' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Klinik</span>
                        <span class="rm-info-value">{{ $rekamMedis->reservasi->jadwal->klinik->nama_klinik ?? '-' }}</span>
                    </div>
                    <div class="rm-info-row">
                        <span class="rm-info-label">Tanggal Periksa</span>
                        <span class="rm-info-value">{{ $rekamMedis->tgl_periksa->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Pemeriksaan -->
        <div class="rm-section">
            <div class="rm-section-title">
                <i class="fas fa-stethoscope"></i>
                Hasil Pemeriksaan
            </div>
            <div class="rm-grid-2">
                <div class="rm-box">
                    <div class="rm-box-label">
                        <i class="fas fa-diagnoses"></i> Diagnosa
                    </div>
                    <div class="rm-box-value">{{ $rekamMedis->diagnosa ?? 'Tidak ada diagnosa yang dicatat' }}</div>
                </div>
                <div class="rm-box">
                    <div class="rm-box-label">
                        <i class="fas fa-procedures"></i> Tindakan Utama
                    </div>
                    <div class="rm-box-value">{{ $rekamMedis->tindakan ?? 'Tidak ada tindakan yang dicatat' }}</div>
                </div>
            </div>
        </div>

        <!-- ODONTOGRAM -->
        <div class="rm-odontogram">
            <div class="odo-title">
                <!-- judul -->
                <i class="fas fa-tooth"></i>
                Odontogram
            </div>
            <div class="odo-chart">
                <div class="odo-row">
                    <!-- gigi susu kanan atas -->
                    <div class="odo-quadrant reverse">
                        @foreach($primaryUpperRight as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape primary {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="odo-divider"></div>
                    <div class="odo-quadrant">
                        <!-- gigi susu kiri atas -->
                        @foreach($primaryUpperLeft as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape primary {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="odo-arc">Gigi Susu</div>
                <div class="odo-row">
                    <div class="odo-quadrant reverse">
                        <!-- rahang atas permanen -->
                        @foreach($upperRight as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="odo-divider"></div>
                    <div class="odo-quadrant">
                        @foreach($upperLeft as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="odo-arc">Rahang Atas</div>
                <div class="odo-row">
                    <!-- rahang bawah permanen -->
                    <div class="odo-quadrant">
                        @foreach($lowerRight as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="odo-divider"></div>
                    <div class="odo-quadrant reverse">
                        @foreach($lowerLeft as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="odo-arc">Rahang Bawah</div>
                <div class="odo-row">
                    <div class="odo-quadrant">
                        <!-- gigi susu bawah -->
                        @foreach($primaryLowerRight as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape primary {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="odo-divider"></div>
                    <div class="odo-quadrant reverse">
                        @foreach($primaryLowerLeft as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape primary {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Gigi -->
        <div class="rm-table-wrap">
            <div class="rm-section-title" style="margin-bottom: 1rem;">
                <i class="fas fa-tooth"></i>
                Detail Pemeriksaan Gigi
                <span class="count-badge" style="margin-left: auto;">
                    {{ $rekamMedis->detailGigi->count() }} gigi
                </span>
            </div>
            @if($rekamMedis->detailGigi->isNotEmpty())
                <table class="rm-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No</th>
                            <th style="width: 12%;">Gigi</th>
                            <th style="width: 28%;">Kondisi</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- loop semua detail gigi pasien -->
                        @foreach($rekamMedis->detailGigi as $detail)
                            @php
                                $badgeClass = match($detail->kondisi_gigi) {
                                    'sehat' => 'badge-green',
                                    'karies', 'abses', 'pulpitis', 'periodontitis' => 'badge-red',
                                    'cabut' => 'badge-amber',
                                    'tambal' => 'badge-blue',
                                    'lainnya' => 'badge-purple',
                                    default => 'badge-gray',
                                };
                            @endphp
                            <tr>
                                <td style="text-align: center; font-weight: 700;">{{ $loop->iteration }}</td>
                                <td style="text-align: center;">
                                    <span class="rm-gigi">{{ $detail->nomor_gigi }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <span class="rm-badge {{ $badgeClass }}">
                                        {{ $detail->kondisi_tampil }}
                                    </span>
                                </td>
                                <td>{{ $detail->tindakan_tampil }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="rm-empty">
                    <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                    Tidak ada detail pemeriksaan gigi yang dicatat
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="rm-footer" style="break-inside: avoid; page-break-inside: avoid;">
            <div class="thanks">
                <i class="fas fa-heart" style="color: #dc2626; margin-right: 0.5rem;"></i>
                Semoga Lekas Sembuh
            </div>
            <div class="sub">
                Dokumen ini diterbitkan oleh Sistem Informasi Reservasi dan Rekam Medis Klinik Gigi Dokter Ndeso
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

    </div>
</div>

<!-- button -->
<div class="fab-container">
    <a href="{{ route('admin.rekam-medis.index') }}" class="fab fab-back">
        <i class="fas fa-arrow-left"></i>
        <span class="fab-label">Kembali</span>
    </a>
    <button onclick="downloadPDF()" class="fab fab-pdf">
        <i class="fas fa-file-pdf"></i>
        <span class="fab-label">Unduh PDF</span>
    </button>
    <button onclick="printRekamMedis()" class="fab fab-print">
        <i class="fas fa-print"></i>
        <span class="fab-label">Cetak Dokumen</span>
    </button>
</div>
@endsection

@section('scripts')
<script>
// salinan clone untuk print atau pdf
function createPdfClone() {
    var source = document.getElementById('printArea'); //area rekam medis
    var clone = source.cloneNode(true); //buat salinan
    
    // Wrapper TEPAT 794px (= 210mm A4)
    var wrapper = document.createElement('div'); //buat wrapper
    wrapper.className = 'pdf-clone-wrapper';
    
    // Clone: paksa lebar 100% dari wrapper, hapus batasan
    clone.style.width = '100%';
    clone.style.maxWidth = '100%';
    clone.style.minWidth = '100%';
    clone.style.margin = '0';
    clone.style.padding = '0';
    clone.style.border = 'none';
    clone.style.boxShadow = 'none';
    clone.style.borderRadius = '0';
    clone.style.background = 'white';
    clone.style.overflow = 'visible';
    
    // sesuaikan grid tetap 2 kolom
    var grids = clone.querySelectorAll('.rm-grid-2');
    for (var i = 0; i < grids.length; i++) { //loop
        grids[i].style.width = '100%';
        grids[i].style.display = 'grid';
        grids[i].style.gridTemplateColumns = '1fr 1fr';
    }
    
    // Pastikan table melebar penuh
    var tables = clone.querySelectorAll('.rm-table');
    for (var i = 0; i < tables.length; i++) {
        tables[i].style.width = '100%';
        tables[i].style.minWidth = '0';
    }
    
    // Pastikan odontogram & table wrap tidak scroll
    var odoWrap = clone.querySelectorAll('.rm-odontogram, .rm-table-wrap'); //hilangkan scroll horizontal
    for (var i = 0; i < odoWrap.length; i++) {
        odoWrap[i].style.overflowX = 'visible';
    }
    var odoChart = clone.querySelectorAll('.odo-chart');
    for (var i = 0; i < odoChart.length; i++) {
        odoChart[i].style.minWidth = '0';
        odoChart[i].style.width = '100%';
    }
    // masukan clone ke wrapper
    wrapper.appendChild(clone);
    document.body.appendChild(wrapper); //tambah wrapper halaman
    // kembalikan wrapper
    return wrapper;
}
// hapus clone
function removePdfClone(wrapper) {
    if (wrapper && wrapper.parentNode) { //jika wrapper masih ada
        wrapper.parentNode.removeChild(wrapper); //clone dibuang
    }
}
// fungsi ketika user klik tombol print
async function printRekamMedis() {
    var fab = document.querySelector('.fab-container');
    if (fab) fab.style.display = 'none'; //sembunyikan supaya tombol tidak ikut tercetak
    //buat clone
    var wrapper = createPdfClone();
    // jalankan setelah browser selesai menggambar halaman
    requestAnimationFrame(function() {
        window.print(); //panggil print
        //setelah 1 detik clone dihapus
        setTimeout(function() {
            removePdfClone(wrapper);
            if (fab) fab.style.display = ''; //fab dimunculkan lagi
        }, 1000);
    });
}
//download pdf
async function downloadPDF() {
    var pasienName = '{{ $rekamMedis->reservasi->pasien->nama ?? "Pasien" }}'; //ambil nama pasien  
    var fab = document.querySelector('.fab-container');
    
    if (fab) fab.style.display = 'none'; //sembunyikan tombol
    
    var wrapper = createPdfClone(); //buat clone

    // Tunggu font & layout stabil
    await document.fonts.ready;
    await new Promise(function(r) { setTimeout(r, 1500); }); //tunggu layoout stabil
    
    // ambil elemen rekam medis
    var clone = wrapper.querySelector('.rm-paper');
    // konfigurasi html to pdf
    var opt = {
        margin: 10, //jarak
        filename: 'RM_' + pasienName.replace(/\s+/g, '_') + '.pdf', //nama file
        image: { type: 'jpeg', quality: 0.98 }, //kualitas gambar
        html2canvas: { 
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff',
            logging: false,
            width: 794,
            windowWidth: 794,
            windowHeight: clone.scrollHeight,
            x: 0,
            y: 0,
            scrollX: 0,
            scrollY: 0,
            height: clone.scrollHeight
        },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }, //konfigurasi pdf, satuan mm, ukuran a4. verikal
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] } //hindari pemotongan halaman
    };
    try { // buat pdf
        await html2pdf().set(opt).from(clone).save();
    } catch (err) {
        console.error('PDF generation failed:', err);
        alert('Gagal generate PDF. Silakan coba lagi.');
    } finally {
        removePdfClone(wrapper);
        if (fab) fab.style.display = '';
    }
}
</script>
@endsection
