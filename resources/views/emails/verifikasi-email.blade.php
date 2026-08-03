<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        body { margin: 0; padding: 0; background: #f5f5f4; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 16px; border: 1px solid #e7e5e4; overflow: hidden; }
        .header { background: linear-gradient(135deg, #134e4a, #0f766e); padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 13px; }
        .body { padding: 32px 28px; text-align: center; }
        .welcome { color: #1c1917; font-size: 16px; font-weight: 600; margin-bottom: 8px; }
        .desc { color: #78716c; font-size: 14px; line-height: 1.7; margin-bottom: 28px; }
        .btn-verify { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #134e4a, #0f766e); color: #fff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(19,78,74,0.25); }
        .btn-verify:hover { background: linear-gradient(135deg, #0f766e, #115e59); }
        .expiry { margin-top: 24px; padding: 12px 16px; background: #fef3c7; border-radius: 8px; font-size: 12px; color: #92400e; }
        .footer { background: #fafaf9; padding: 20px 28px; text-align: center; border-top: 1px solid #e7e5e4; }
        .footer p { margin: 0; color: #a8a29e; font-size: 12px; }
        .brand { color: #134e4a; font-weight: 700; font-size: 13px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>📧 Verifikasi Email</h1>
            <p>Klinik Gigi Dokter Ndeso</p>
        </div>
        <div class="body">
            <p class="welcome">Halo, {{ $data['nama'] ?? 'Pengguna' }}!</p>
            <p class="desc">Terima kasih telah mendaftar di Klinik Gigi Dokter Ndeso. Silakan klik tombol di bawah untuk memverifikasi alamat email Anda.</p>
            <a href="{{ $data['url'] }}" class="btn-verify">Verifikasi Email</a>
            <div class="expiry">
                ⏰ Link ini berlaku hingga {{ $data['expiry'] ?? '24 jam' }}. Jika tidak meminta verifikasi, abaikan email ini.
            </div>
        </div>
        <div class="footer">
            <p class="brand">Klinik Gigi Dokter Ndeso</p>
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>