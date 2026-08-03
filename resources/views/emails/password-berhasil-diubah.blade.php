<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Berhasil Diubah</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f4; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #e7e5e4; overflow: hidden; }
        .header { background: linear-gradient(135deg, #134e4a, #0f766e); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 13px; }
        .body { padding: 32px 28px; text-align: center; }
        .icon { width: 64px; height: 64px; margin: 0 auto 20px; background: #f0fdfa; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; }
        .welcome { color: #1c1917; font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .desc { color: #78716c; font-size: 14px; line-height: 1.7; margin-bottom: 24px; }
        .detail-table { width: 100%; max-width: 320px; margin: 0 auto; border-collapse: collapse; text-align: left; }
        .detail-table td { padding: 10px 0; font-size: 13px; border-bottom: 1px solid #f5f5f4; }
        .detail-table td:first-child { color: #78716c; width: 45%; }
        .detail-table td:last-child { color: #1c1917; font-weight: 600; }
        .alert { margin-top: 24px; padding: 14px 18px; background: #fef2f2; border-radius: 8px; border-left: 4px solid #ef4444; text-align: left; }
        .alert p { margin: 0; color: #991b1b; font-size: 12px; line-height: 1.6; }
        .footer { background: #fafaf9; padding: 20px 28px; text-align: center; border-top: 1px solid #e7e5e4; }
        .footer p { margin: 0; color: #a8a29e; font-size: 12px; }
        .brand { color: #134e4a; font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>✅ Password Diperbarui</h1>
            <p>Klinik Gigi Dokter Ndeso</p>
        </div>
        <div class="body">
            <div class="icon">🔒</div>
            <p class="welcome">Password Berhasil Diubah!</p>
            <p class="desc">Halo, {{ $data['nama'] ?? 'Pengguna' }}. Password akun Anda telah berhasil diperbarui.</p>
            <table class="detail-table">
                <tr><td>Waktu</td><td>{{ $data['waktu'] ?? now()->format('d M Y H:i') }}</td></tr>
                <tr><td>Perangkat</td><td>{{ $data['device'] ?? 'Tidak diketahui' }}</td></tr>
            </table>
            <div class="alert">
                <p>⚠️ Jika Anda tidak merasa mengubah password, segera hubungi admin atau reset password kembali.</p>
            </div>
        </div>
        <div class="footer">
            <p class="brand">Klinik Gigi Dokter Ndeso</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>