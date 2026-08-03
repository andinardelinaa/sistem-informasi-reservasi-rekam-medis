@extends('layouts.admin')
@section('title', 'Detail Reservasi - Klinik Gigi Dokter Ndeso')

<style>
    /* Background */
    .nota-bg {
        background-color: #f1f5f9;
        min-height: 100vh;
        padding: 1rem;
    }

    /* Card Full Width */
    .nota-card {
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    /* Header */
    .nota-header {
        background: #ffffff;
        color: #0d9488;
        padding: 0.875rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 2px solid #0d9488;
    }

    /* Table */
    .nota-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }

    .nota-table th {
        background: #ffffff;
        color: #0d9488;
        font-weight: 700;
        text-align: left;
        padding: 0.75rem 1.5rem;
        border-bottom: 1px dashed #e2e8f0;
        width: 200px;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .nota-table td {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px dashed #e2e8f0;
        color: #0f172a;
        font-weight: 500;
    }

    .nota-table tr:last-child th,
    .nota-table tr:last-child td {
        border-bottom: none;
    }

    .nota-table tr:hover td {
        background: #f8fafc;
    }

    /* Nomor Antrean */
    .antrean-box {
        display: inline-block;
        background: #f0fdfa;
        border: 2px solid #0d9488;
        border-radius: 6px;
        padding: 0.35rem 1rem;
        font-family: 'Courier New', monospace;
        font-size: 1.5rem;
        font-weight: 900;
        color: #0d9488;
    }

    /* Status */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-menunggu { background: #fef3c7; color: #92400e; }
    .status-tindakan { background: #dbeafe; color: #1e40af; }
    .status-selesai { background: #d1fae5; color: #065f46; }
    .status-dibatalkan { background: #fee2e2; color: #991b1b; }

    /* Rekam Medis Button */
    .rm-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #0d9488;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }

    .rm-btn:hover {
        background: #0f766e;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
    }

    .rm-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Footer */
    .nota-footer {
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        border-top: 2px dashed #e2e8f0;
        background: #fafafa;
    }

    .btn {
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-back {
        background: #ffffff;
        color: #64748b;
        border-color: #d1d5db;
    }

    .btn-back:hover {
        background: #f3f4f6;
    }

    .btn-edit {
        background: #0d9488;
        color: white;
        border-color: #0d9488;
    }

    .btn-edit:hover {
        background: #0f766e;
    }

    .btn-cancel {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .btn-cancel:hover {
        background: #fee2e2;
    }

    /* Print */
    @media print {
        body { background: white !important; }
        .nota-bg { padding: 0 !important; background: white !important; }
        .nota-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    }
</style>

@section('content')
<section class="nota-bg">
    <!-- Card -->
    <div class="nota-card">
        <!-- Header -->
        <div class="nota-header">
            <i class="fas fa-file-invoice"></i>
            DETAIL RESERVASI PASIEN
        </div>

        <!-- Table List -->
        <table class="nota-table">
            <tr>
                <th>ID Reservasi</th>
                <td>#{{ $reservasi->id_reservasi }}</td>
            </tr>
            <tr>
                <th>Nama Pasien</th>
                <td>{{ $reservasi->pasien->nama ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Nama Dokter</th>
                <td>{{ $reservasi->jadwal->dokter->nama ?? '-' }}</td>
            </tr>
            <tr>
                <th>Spesialisasi</th>
                <td>{{ $reservasi->jadwal->dokter->spesialisasi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Pemeriksaan</th>
                <td>{{ \Carbon\Carbon::parse($reservasi->tgl_reservasi)->locale('id')->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <th>Jam Kunjungan</th>
                <td>
                    {{ \Carbon\Carbon::parse($reservasi->jam_reservasi)->format('H:i') ?? '-' }}
                    @if($reservasi->jam_selesai)
                    - {{ \Carbon\Carbon::parse($reservasi->jam_selesai)->format('H:i') }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Nomor Antrean</th>
                <td>
                    <span class="antrean-box">{{ $reservasi->antrean ?? '--' }}</span>
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="status-badge status-{{ $reservasi->status }}">
                        {{ ucfirst($reservasi->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Keluhan</th>
                <td>{{ $reservasi->keluhan ?? 'Tidak ada keluhan' }}</td>
            </tr>
            <tr>
                <th>Klinik</th>
                <td>Klinik Gigi Dokter Ndeso Sokaraja</td>
            </tr>
            <tr>
                <th>Rekam Medis</th>
                <td>
                    @if($reservasi->rekamMedis)
                        <a href="{{ route('admin.rekam-medis.show', $reservasi->rekamMedis->id_rm) }}" class="rm-btn">
                            <i class="fas fa-file-medical"></i>
                            Lihat Rekam Medis
                        </a>
                    @else
                        <span style="color: #94a3b8;">Belum tersedia</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="nota-footer">
            <a href="{{ route('admin.reservasi.index') }}" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            @if($reservasi->status === 'menunggu')
                <a href="{{ route('admin.reservasi.edit', $reservasi->id_reservasi) }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <button onclick="if(confirm('Yakin batalkan?')) window.location.href='{{ route('admin.reservasi.cancel', $reservasi->id_reservasi) }}'" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Batal
                </button>
            @endif
        </div>
    </div>
</section>

@endsection