<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reservasi Dibatalkan</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f4; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #e7e5e4; overflow: hidden; }
        .header { background: linear-gradient(135deg, #dc2626, #b91c1c); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .body { padding: 32px 28px; }
        .cancel { background: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 16px 20px; margin-bottom: 24px; }
        .cancel p { margin: 0; color: #991b1b; font-size: 14px; line-height: 1.7; }
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table td { padding: 12px 4px; font-size: 13px; border-bottom: 1px solid #f5f5f4; }
        .detail-table td:first-child { color: #78716c; width: 40%; }
        .detail-table td:last-child { color: #1c1917; font-weight: 600; text-align: right; }
        .footer { background: #fafaf9; padding: 20px 28px; text-align: center; border-top: 1px solid #e7e5e4; }
        .footer p { margin: 0; color: #a8a29e; font-size: 12px; }
        .brand { color: #134e4a; font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>❌ Reservasi Dibatalkan</h1>
            <p>Klinik Gigi Dokter Ndeso</p>
        </div>
        <div class="body">
            <div class="cancel">
                <p><strong>Halo, {{ $data['detail_reservasi']['pasien'] ?? 'Pasien' }}.</strong></p>
                <p style="margin-top: 8px;">Reservasi Anda telah dibatalkan karena perubahan jadwal dokter. Silakan buat reservasi baru.</p>
            </div>
            <table class="detail-table">
                <tr><td>Nama Pasien</td><td>{{ $data['detail_reservasi']['pasien'] ?? '-' }}</td></tr>
                <tr><td>Tanggal</td><td>{{ $data['detail_reservasi']['tanggal'] ?? '-' }}</td></tr>
                <tr><td>Jam</td><td>{{ $data['detail_reservasi']['jam'] ?? '-' }}</td></tr>
                <tr><td>Status Akhir</td><td style="color: #dc2626;">{{ $data['detail_reservasi']['status_akhir'] ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="footer">
            <p class="brand">Klinik Gigi Dokter Ndeso</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>