<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Reservasi Diperbarui</title>

    <style>
        body{
            margin:0;
            padding:0;
            background:#f5f5f4;
            font-family:'Segoe UI',Tahoma,sans-serif;
        }

        .wrapper{
            max-width:560px;
            margin:40px auto;
            background:#ffffff;
            border-radius:16px;
            border:1px solid #e7e5e4;
            overflow:hidden;
        }

        .header{
            background:linear-gradient(135deg,#0f766e,#134e4a);
            padding:32px 24px;
            text-align:center;
        }

        .header h1{
            color:#ffffff;
            margin:0;
            font-size:22px;
            font-weight:700;
        }

        .header p{
            margin-top:8px;
            color:#ccfbf1;
            font-size:13px;
        }

        .body{
            padding:32px 28px;
        }

        .info{
            background:#f0fdfa;
            border-left:4px solid #14b8a6;
            border-radius:8px;
            padding:16px 20px;
            margin-bottom:24px;
        }

        .info p{
            margin:0;
            color:#134e4a;
            font-size:14px;
            line-height:1.7;
        }

        .section-title{
            font-size:13px;
            font-weight:700;
            color:#0f766e;
            margin-bottom:12px;
            text-transform:uppercase;
            letter-spacing:0.05em;
        }

        .detail-table{
            width:100%;
            border-collapse:collapse;
        }

        .detail-table td{
            padding:12px 4px;
            font-size:13px;
            border-bottom:1px solid #f5f5f4;
        }

        .detail-table td:first-child{
            color:#78716c;
            width:40%;
        }

        .detail-table td:last-child{
            color:#1c1917;
            font-weight:600;
            text-align:right;
        }

        .detail-table tr.changed{
            background:#fefce8;
        }

        .detail-table tr.changed td:last-child{
            color:#a16207;
        }

        .status{
            color:#047857;
            font-weight:700;
        }

        .note{
            margin-top:24px;
            padding:14px 16px;
            background:#fafaf9;
            border-radius:8px;
            border:1px solid #e7e5e4;
        }

        .note p{
            margin:0;
            font-size:12px;
            color:#57534e;
            line-height:1.7;
        }

        .footer{
            background:#fafaf9;
            padding:20px 28px;
            text-align:center;
            border-top:1px solid #e7e5e4;
        }

        .footer p{
            margin:0;
            color:#a8a29e;
            font-size:12px;
        }

        .brand{
            color:#134e4a;
            font-weight:700;
            font-size:13px;
            margin-bottom:4px;
        }

        .alasan-box{
            background:#fff7ed;
            border-left:4px solid #f97316;
            border-radius:8px;
            padding:14px 18px;
            margin-bottom:20px;
        }

        .alasan-box p{
            margin:0;
            color:#9a3412;
            font-size:13px;
            line-height:1.6;
        }

        .alasan-box strong{
            color:#c2410c;
        }
    </style>
</head>

<body>

<div class="wrapper">

    <!-- HEADER -->
    <div class="header">
        <h1>📅 Jadwal Reservasi Diperbarui</h1>
        <p>Klinik Gigi Dokter Ndeso</p>
    </div>

    <!-- BODY -->
    <div class="body">

        <!-- INFO -->
        <div class="info">
            <p>
                <strong>
                    Halo, {{ $data['detail_reservasi']['pasien'] ?? 'Pasien' }}.
                </strong>
            </p>

            <p style="margin-top:8px;">
                Jadwal reservasi Anda telah berhasil diperbarui karena adanya perubahan jadwal dokter.
                Berikut detail jadwal terbaru reservasi Anda.
            </p>
        </div>

        <!-- TAMPILKAN DOKTER LAMA HANYA JIKA ADA -->
        @if(!empty($data['detail_reservasi']['dokter_lama']) && ($data['detail_reservasi']['dokter_lama'] != $data['detail_reservasi']['dokter_baru']))
        <table class="detail-table" style="margin-bottom:20px; opacity:0.7;">
            <tr>
                <td>Dokter Lama</td>
                <td style="text-decoration:line-through;">
                    {{ $data['detail_reservasi']['dokter_lama'] ?? '-' }}
                    @if(!empty($data['detail_reservasi']['spesialisasi_lama']))
                    <span style="color:#a8a29e; font-size:11px;">({{ $data['detail_reservasi']['spesialisasi_lama'] }})</span>
                    @endif
                </td>
            </tr>
        </table>
        @endif

        <!-- DETAIL JADWAL BARU -->
        <div class="section-title">
            Detail Jadwal Baru
        </div>

        <table class="detail-table">

            <tr>
                <td>Nama Pasien</td>
                <td>{{ $data['detail_reservasi']['pasien'] ?? '-' }}</td>
            </tr>

            <tr>
                <td>Tanggal</td>
                <td>
                    {{ \Carbon\Carbon::parse($data['detail_reservasi']['tanggal_baru'])->translatedFormat('d F Y') }}
                </td>
            </tr>

            <tr>
                <td>Jam</td>
                <td>
                    {{ $data['detail_reservasi']['jam_baru'] ?? '-' }} WIB
                </td>
            </tr>

            <tr class="changed">
                <td>Dokter Baru</td>
                <td>
                    {{ $data['detail_reservasi']['dokter_baru'] ?? '-' }}
                    @if(!empty($data['detail_reservasi']['spesialisasi']))
                    <span style="color:#78716c; font-size:11px;">({{ $data['detail_reservasi']['spesialisasi'] }})</span>
                    @endif
                </td>
            </tr>

            <tr>
                <td>Klinik</td>
                <td>{{ $data['detail_reservasi']['klinik'] ?? '-' }}</td>
            </tr>

            <tr>
                <td>Status Reservasi</td>
                <td class="status">
                    {{ $data['detail_reservasi']['status'] ?? '-' }}
                </td>
            </tr>

        </table>

        <!-- NOTE -->
        <div class="note">
            <p>
                • Mohon datang 30 menit sebelum jadwal pemeriksaan.<br>
                • Simpan email ini sebagai bukti perubahan jadwal reservasi.<br>
                • Jika memiliki pertanyaan, silakan hubungi admin klinik.
            </p>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p class="brand">
            Klinik Gigi Dokter Ndeso
        </p>

        <p>
            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
        </p>
    </div>

</div>

</body>
</html>