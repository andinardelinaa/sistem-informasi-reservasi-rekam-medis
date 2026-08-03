<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reminder Reservasi</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f4; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #e7e5e4; overflow: hidden; }
        .header { background: linear-gradient(135deg, #4f46e5, #4338ca); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p { color: #e0e7ff; margin: 8px 0 0; font-size: 14px; }
        .body { padding: 32px 28px; }
        .greeting { margin-bottom: 20px; color: #44403c; font-size: 14px; line-height: 1.7; }
        .greeting strong { color: #1c1917; }
        .alert-box { background: #fffbeb; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .alert-box p { margin: 0; color: #92400e; font-size: 13px; line-height: 1.7; }
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table td { padding: 12px 4px; font-size: 13px; border-bottom: 1px solid #f5f5f4; }
        .detail-table td:first-child { color: #78716c; width: 40%; }
        .detail-table td:last-child { color: #1c1917; font-weight: 600; text-align: right; }
        .footer { background: #fafaf9; padding: 20px 28px; text-align: center; border-top: 1px solid #e7e5e4; }
        .footer p { margin: 0; color: #a8a29e; font-size: 12px; }
        .brand { color: #312e81; font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>🔔 Reminder Reservasi</h1>
            <p>{{ now()->format('d F Y') }}</p>
        </div>
        <div class="body">
            <div class="greeting">
                <p><strong>Halo, {{ $pasien->nama ?? 'Pasien' }}.</strong></p>
                <p style="margin-top: 8px;">Ini adalah pengingat bahwa Anda memiliki jadwal kunjungan hari ini. Berikut detail reservasi Anda:</p>
            </div>

            <table class="detail-table">
                <tr><td>Nama Pasien</td><td>{{ $pasien->nama ?? '-' }}</td></tr>
                <tr><td>Klinik</td><td>{{ $klinik->nama_klinik ?? 'Klinik Gigi' }}</td></tr>
                <tr><td>Dokter</td><td>{{ $dokter->nama ?? '-' }}</td></tr>
                <tr><td>Spesialisasi</td><td>{{ $dokter->spesialisasi ?? '-' }}</td></tr>
                <tr><td>Tanggal</td><td>{{ \Carbon\Carbon::parse($reservasi->tgl_reservasi)->format('d F Y') ?? '-' }}</td></tr>
                <tr><td>Jam</td><td>{{ $reservasi->jam_reservasi ?? '-' }}</td></tr>
                <tr><td>No. Antrean</td><td>#{{ $reservasi->antrean ?? '-' }}</td></tr>
                <tr><td>Keluhan</td><td>{{ $reservasi->keluhan ?? '-' }}</td></tr>
            </table>

            <div class="alert-box">
                <p><strong>⚠️ Penting:</strong> Harap datang tepat waktu. Reservasi akan otomatis dibatalkan jika Anda tidak hadir dalam 30 menit dari jadwal kunjungan.</p>
            </div>
        </div>
        <div class="footer">
            <p class="brand">{{ $klinik->nama_klinik ?? 'Klinik Gigi' }}</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>Jika ada pertanyaan, hubungi kami di {{ $klinik->telepon ?? '(021) 1234-5678' }}</p>
        </div>
    </div>
</body>
</html>