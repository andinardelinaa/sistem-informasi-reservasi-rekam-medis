@extends('layouts.pasien')

@section('title', 'Detail Rekam Medis - ' . ($klinikNama ?? 'Klinik'))
@section('header-title', 'Detail Rekam Medis')
@include('layouts.partials.alert')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap');

    .rm-bg {
        background-color: #e5e7eb;
        min-height: 100vh;
        padding: 0.5rem;
        font-family: 'Inter', sans-serif;
    }

    @media (min-width: 640px) {
        .rm-bg { padding: 1.5rem; }
    }

    .rm-paper {
        background: #ffffff;
        width: 100%;
        max-width: 850px;
        margin: 0 auto;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #374151;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    /* ===== KOP ===== */
    .rm-kop {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.75rem;
        padding: 0.85rem 0.85rem 0.65rem;
        border-bottom: 2px solid #111827;
        align-items: center;
    }

    @media (min-width: 768px) {
        .rm-kop {
            padding: 1.5rem 2rem 1rem;
            align-items: start;
            gap: 1rem;
        }
    }

    .rm-kop-left {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    @media (min-width: 768px) {
        .rm-kop-left { gap: 1rem; }
    }

    .rm-kop-logo {
        width: 36px;
        height: 36px;
        background: #ffffff;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #111827;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .rm-kop-logo {
            width: 48px;
            height: 48px;
            border-radius: 6px;
        }
    }

    .rm-kop-logo img {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }

    @media (min-width: 768px) {
        .rm-kop-logo img {
            width: 28px;
            height: 28px;
        }
    }

    .rm-kop-text h1 {
        font-size: 0.72rem;
        font-weight: 800;
        color: #111827;
        line-height: 1.2;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    @media (min-width: 768px) {
        .rm-kop-text h1 { font-size: 1.15rem; }
    }

    .rm-kop-text p {
        font-size: 0.58rem;
        color: #4b5563;
        margin-top: 0.15rem;
    }

    @media (min-width: 768px) {
        .rm-kop-text p { font-size: 0.68rem; margin-top: 0.2rem; }
    }

    .rm-kop-right {
        text-align: right;
    }

    .rm-kop-id {
        display: inline-block;
        border: 2px solid #111827;
        padding: 0.2rem 0.5rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        font-weight: 700;
        color: #111827;
        border-radius: 3px;
        background: #ffffff;
        letter-spacing: 0.5px;
    }

    @media (min-width: 768px) {
        .rm-kop-id {
            padding: 0.35rem 0.8rem;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
    }

    .rm-kop-meta {
        font-size: 0.55rem;
        color: #4b5563;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    @media (min-width: 768px) {
        .rm-kop-meta { font-size: 0.68rem; margin-top: 0.4rem; }
    }

    /* ===== SECTIONS ===== */
    .rm-section {
        padding: 0.75rem 0.85rem;
        border-bottom: 1px dashed #9ca3af;
    }

    @media (min-width: 768px) {
        .rm-section { padding: 1.25rem 2rem; }
    }

    .rm-section-title {
        font-size: 0.55rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (min-width: 768px) {
        .rm-section-title { font-size: 0.62rem; margin-bottom: 1rem; }
    }

    .rm-section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #d1d5db;
    }

    .rm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }

    @media (min-width: 768px) {
        .rm-grid { gap: 2rem; }
    }

    .rm-info-row {
        display: flex;
        flex-direction: row;
        padding: 0.3rem 0;
        font-size: 0.65rem;
        border-bottom: 1px dotted #e5e7eb;
        gap: 0;
        align-items: baseline;
    }

    @media (min-width: 768px) {
        .rm-info-row {
            padding: 0.35rem 0;
            font-size: 0.82rem;
        }
    }

    .rm-info-row:last-child { border-bottom: none; }

    .rm-info-label {
        color: #6b7280;
        font-weight: 500;
        font-size: 0.55rem;
        width: 85px;
        flex-shrink: 0;
    }

    @media (min-width: 768px) {
        .rm-info-label {
            width: 130px;
            font-size: 0.72rem;
        }
    }

    .rm-info-value {
        color: #111827;
        font-weight: 600;
        word-break: break-word;
        font-size: 0.65rem;
    }

    @media (min-width: 768px) {
        .rm-info-value { font-size: 0.82rem; }
    }

    /* ===== BOXES ===== */
    .rm-box {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 3px;
        padding: 0.65rem;
    }

    @media (min-width: 768px) {
        .rm-box { padding: 1rem; }
    }

    .rm-box-label {
        font-size: 0.5rem;
        color: #374151;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.4rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    @media (min-width: 768px) {
        .rm-box-label { font-size: 0.58rem; margin-bottom: 0.5rem; }
    }

    .rm-box-value {
        font-size: 0.7rem;
        color: #374151;
        line-height: 1.55;
        word-break: break-word;
    }

    @media (min-width: 768px) {
        .rm-box-value { font-size: 0.88rem; line-height: 1.6; }
    }

    /* ===== ODONTOGRAM ===== */
    .rm-odontogram {
        padding: 0.75rem 0.85rem;
        border-bottom: 1px dashed #9ca3af;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (min-width: 768px) {
        .rm-odontogram { padding: 1.25rem 2rem; }
    }

    .odo-title {
        font-size: 0.55rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (min-width: 768px) {
        .odo-title { font-size: 0.62rem; margin-bottom: 1rem; }
    }

    .odo-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #d1d5db;
    }

    .odo-chart {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
        min-width: 0;
        width: 100%;
    }

    .odo-row {
        display: flex;
        align-items: flex-end;
        gap: 0.15rem;
        flex-wrap: nowrap;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .odo-row { gap: 0.3rem; }
    }

    .odo-quadrant {
        display: flex;
        gap: 0.1rem;
        flex-wrap: nowrap;
    }

    @media (min-width: 768px) {
        .odo-quadrant { gap: 0.2rem; }
    }

    .odo-quadrant.reverse {
        flex-direction: row-reverse;
    }

    .odo-tooth {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.1rem;
    }

    .odo-tooth-shape {
        width: 18px;
        height: 22px;
        background: #fff;
        border: 1px solid #9ca3af;
        border-radius: 2px 2px 4px 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.42rem;
        font-weight: 700;
        color: #9ca3af;
        transition: all 0.2s;
    }

    @media (min-width: 768px) {
        .odo-tooth-shape {
            width: 26px;
            height: 34px;
            font-size: 0.65rem;
            border: 1.5px solid #9ca3af;
            border-radius: 3px 3px 6px 6px;
        }
    }

    .odo-tooth-shape.primary {
        width: 15px;
        height: 18px;
        border-radius: 1px 1px 3px 3px;
    }

    @media (min-width: 768px) {
        .odo-tooth-shape.primary {
            width: 22px;
            height: 28px;
            border-radius: 2px 2px 5px 5px;
        }
    }

    .odo-tooth-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.42rem;
        color: #9ca3af;
        font-weight: 600;
    }

    @media (min-width: 768px) {
        .odo-tooth-num { font-size: 0.55rem; }
    }

    .odo-tooth-shape.has-record {
        border-color: #111827;
        background: #f3f4f6;
        color: #111827;
        font-weight: 800;
    }

    .odo-divider {
        width: 1px;
        height: 28px;
        background: #d1d5db;
        margin: 0 0.1rem;
        align-self: center;
    }

    @media (min-width: 768px) {
        .odo-divider { margin: 0 0.4rem; height: 45px; }
    }

    .odo-arc {
        font-size: 0.42rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
        font-weight: 600;
        padding: 0.15rem 0;
    }

    @media (min-width: 768px) {
        .odo-arc { font-size: 0.6rem; padding: 0.2rem 0; }
    }

    /* ===== TABLE ===== */
    .rm-table-wrap {
        padding: 0.75rem 0.85rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (min-width: 768px) {
        .rm-table-wrap { padding: 1.25rem 2rem; overflow-x: visible; }
    }

    .rm-table {
        width: 100%;
        min-width: 0;
        border-collapse: collapse;
        font-size: 0.65rem;
        border: 1.5px solid #111827;
    }

    @media (min-width: 768px) {
        .rm-table {
            font-size: 0.82rem;
            border: 2px solid #111827;
        }
    }

    .rm-table thead th {
        background: #f3f4f6;
        color: #111827;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.5rem;
        letter-spacing: 0.5px;
        padding: 0.4rem 0.35rem;
        border: 1px solid #6b7280;
        text-align: center;
        white-space: nowrap;
    }

    @media (min-width: 768px) {
        .rm-table thead th {
            font-size: 0.65rem;
            padding: 0.65rem 0.85rem;
        }
    }

    .rm-table tbody td {
        padding: 0.4rem 0.35rem;
        border: 1px solid #9ca3af;
        color: #374151;
        vertical-align: middle;
    }

    @media (min-width: 768px) {
        .rm-table tbody td { padding: 0.65rem 0.85rem; }
    }

    .rm-table tbody tr:nth-child(even) td {
        background: #f9fafb;
    }

    .rm-badge {
        display: inline-block;
        padding: 0.12rem 0.35rem;
        border-radius: 2px;
        font-size: 0.52rem;
        font-weight: 600;
        white-space: nowrap;
        border: 1px solid;
    }

    @media (min-width: 768px) {
        .rm-badge {
            padding: 0.2rem 0.55rem;
            border-radius: 3px;
            font-size: 0.72rem;
        }
    }

    .badge-green { background: #f0fdf4; color: #166534; border-color: #166534; }
    .badge-red { background: #fef2f2; color: #991b1b; border-color: #991b1b; }
    .badge-amber { background: #fffbeb; color: #92400e; border-color: #92400e; }
    .badge-blue { background: #eff6ff; color: #1e40af; border-color: #1e40af; }
    .badge-gray { background: #f9fafb; color: #374151; border-color: #374151; }
    .badge-purple { background: #faf5ff; color: #6b21a8; border-color: #6b21a8; }

    .rm-gigi {
        width: 20px;
        height: 20px;
        background: #ffffff;
        color: #111827;
        border-radius: 2px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.6rem;
        border: 1.5px solid #111827;
        font-family: 'JetBrains Mono', monospace;
    }

    @media (min-width: 768px) {
        .rm-gigi {
            width: 30px;
            height: 30px;
            font-size: 0.85rem;
            border-radius: 3px;
            border: 2px solid #111827;
        }
    }

    .count-badge {
        font-size: 0.5rem;
        color: #374151;
        background: #f3f4f6;
        padding: 0.12rem 0.4rem;
        border-radius: 2px;
        white-space: nowrap;
        border: 1px solid #6b7280;
        font-weight: 600;
    }

    @media (min-width: 768px) {
        .count-badge { font-size: 0.72rem; padding: 0.2rem 0.65rem; border-radius: 3px; }
    }

    .rm-empty {
        text-align: center;
        padding: 1rem;
        color: #9ca3af;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 3px;
        font-size: 0.72rem;
    }

    @media (min-width: 768px) {
        .rm-empty { padding: 2rem; font-size: 0.88rem; }
    }

    /* ===== FOOTER ===== */
    .rm-footer {
        padding: 0.85rem;
        text-align: center;
        background: #ffffff;
        border-top: 1px dashed #9ca3af;
    }

    @media (min-width: 768px) {
        .rm-footer { padding: 1.5rem 2rem; }
    }

    .rm-footer .thanks {
        font-size: 0.7rem;
        color: #111827;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    @media (min-width: 768px) {
        .rm-footer .thanks { font-size: 0.95rem; letter-spacing: 3px; }
    }

    .rm-footer .sub {
        font-size: 0.55rem;
        color: #6b7280;
        margin-top: 0.35rem;
        line-height: 1.5;
    }

    @media (min-width: 768px) {
        .rm-footer .sub { font-size: 0.68rem; margin-top: 0.5rem; }
    }

    .rm-footer .barcode {
        margin-top: 0.65rem;
        padding: 0.5rem;
        background: #f9fafb;
        border-radius: 3px;
        display: inline-block;
        border: 1px solid #d1d5db;
    }

    @media (min-width: 768px) {
        .rm-footer .barcode { margin-top: 1rem; padding: 0.75rem; }
    }

    .rm-footer .barcode-line {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        color: #111827;
        letter-spacing: 1.5px;
        line-height: 1;
    }

    @media (min-width: 768px) {
        .rm-footer .barcode-line { font-size: 1.45rem; letter-spacing: 3px; }
    }

    .rm-footer .barcode-num {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.45rem;
        color: #6b7280;
        margin-top: 0.15rem;
    }

    @media (min-width: 768px) {
        .rm-footer .barcode-num { font-size: 0.62rem; margin-top: 0.25rem; }
    }

    /* ===== ACTION BAR ===== */
    .action-bar {
        padding: 0.75rem 0.85rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        background: #ffffff;
        border-top: 2px solid #e5e7eb;
    }

    @media (min-width: 768px) {
        .action-bar {
            padding: 1rem 2rem;
            justify-content: flex-end;
        }
    }

    .btn {
        padding: 0.45rem 0.85rem;
        border-radius: 3px;
        font-size: 0.65rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        text-decoration: none;
        width: auto;
    }

    @media (min-width: 768px) {
        .btn {
            padding: 0.5rem 1.25rem;
            font-size: 0.78rem;
            border-radius: 4px;
            gap: 0.4rem;
        }
    }

    .btn-back {
        background: #ffffff;
        color: #374151;
        border-color: #6b7280;
    }

    .btn-back:hover { background: #f3f4f6; }

    ::-webkit-scrollbar { height: 3px; width: 3px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 3px; }
</style>

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
    <div class="rm-paper">

        <!-- KOP -->
        <div class="rm-kop">
            <div class="rm-kop-left">
                <div class="rm-kop-logo">
                    <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo">
                </div>
                <div class="rm-kop-text">
                    <h1>KLINIK GIGI DOKTER NDESO</h1>
                    <h2>REKAM MEDIS</h2>
                    <p>{{ $rekamMedis->reservasi->jadwal?->klinik->nama_klinik ?? 'Klinik' }} • Telp: 08112784411</p>
                </div>
            </div>
            <div class="rm-kop-right">
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
            <div class="rm-grid">
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
            <div class="rm-grid">
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
                <i class="fas fa-tooth"></i>
                Odontogram
            </div>
            <div class="odo-chart">
                <!-- Primary Upper -->
                <div class="odo-row">
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
                        @foreach($primaryUpperLeft as $num)
                            @php $d = $detailByTooth[$num] ?? null; @endphp
                            <div class="odo-tooth">
                                <div class="odo-tooth-shape primary {{ toothClass($d) }}">{{ $num }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="odo-arc">Gigi Susu</div>

                <!-- Permanent Upper -->
                <div class="odo-row">
                    <div class="odo-quadrant reverse">
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

                <!-- Permanent Lower -->
                <div class="odo-row">
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

                <!-- Primary Lower -->
                <div class="odo-row">
                    <div class="odo-quadrant">
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

        <!-- Detail Gigi — HANYA YANG ADA RECORD -->
        <div class="rm-table-wrap">
            <div class="rm-section-title" style="margin-bottom: 0.75rem;">
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
                            <th style="width: 10%;">No</th>
                            <th style="width: 15%;">Gigi</th>
                            <th style="width: 25%;">Kondisi</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
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
        <div class="rm-footer">
            <div class="thanks">
                <i class="fas fa-heart" style="color: #dc2626; margin-right: 0.5rem;"></i>
                Semoga Lekas Sembuh
            </div>
            <div class="sub">
                Dokumen ini diterbitkan oleh Sistem Informasi Reservasi dan Rekam Medis Klinik Gigi Dokter Ndeso • 
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <a href="{{ route('pasien.rekam-medis.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

    </div>
</div>
@endsection