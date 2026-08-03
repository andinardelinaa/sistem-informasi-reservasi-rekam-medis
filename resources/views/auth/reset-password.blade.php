<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Klinik Gigi Dokter Ndeso</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto+Flex:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            min-height: 100dvh; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .background {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #134e4a 0%, #128174 50%, #54cab8 100%);
            z-index: -2;
        }

        .bg-circle-1 {
            position: fixed;
            top: -200px;
            left: -200px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
            animation: float 15s ease-in-out infinite;
        }

        .bg-circle-2 {
            position: fixed;
            bottom: -150px;
            right: -150px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            z-index: -1;
            animation: float 12s ease-in-out infinite reverse;
        }

        .bg-circle-3 {
            position: fixed;
            top: 50%;
            left: 10%;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.06);
            z-index: -1;
            animation: float 10s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(20px, -20px) rotate(10deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }

        .reset-container {
            width: 100%;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 28px;
            box-shadow: 0 25px 60px rgba(19, 78, 74, 0.25),
                        0 0 0 1px rgba(255, 255, 255, 0.3) inset;
            padding: 25px 35px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin: auto;
        }

        .reset-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 80px rgba(19, 78, 74, 0.28),
                        0 0 0 1px rgba(255, 255, 255, 0.4) inset;
        }

        .reset-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #134e4a, #14b8a6, #5eead4);
        }

        .logo-container {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 15px 35px rgba(19, 78, 74, 0.25);
            padding: 10px;
            border: 5px solid #f0fdfa;
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
            box-shadow: 0 20px 40px rgba(19, 78, 74, 0.3);
        }

        .logo-container img {
            width: 78%;
            height: 78%;
            object-fit: contain;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-family: 'Roboto Flex', sans-serif;
            font-weight: 800;
            font-size: 28px;
            color: #134e4a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header h2 {
            font-size: 17px;
            font-weight: 700;
            color: #14b8a6;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            color: #5f9ea0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #134e4a;
            font-size: 18px;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus + i {
            color: #14b8a6;
            transform: translateY(-50%) scale(1.1);
        }

        input {
            width: 100%;
            height: 48px;
            padding: 0 50px;
            background: rgba(255, 255, 255, 0.85);
            border: 1.5px solid rgba(19, 78, 74, 0.25);
            border-radius: 14px;
            font-size: 15px;
            color: #134e4a;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: #5f9ea0;
            opacity: 0.75;
        }

        input:focus {
            outline: none;
            border-color: #14b8a6;
            background: white;
            box-shadow: 0 0 0 5px rgba(20, 184, 166, 0.15);
        }

        .form-options {
            text-align: center;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-options a {
            color: #134e4a;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-options a::after {
            content: "";
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #14b8a6;
            transition: width 0.3s ease;
        }

        .form-options a:hover {
            color: #14b8a6;
        }

        .form-options a:hover::after {
            width: 100%;
        }

        .btn-reset {
            height: 48px;
            background: linear-gradient(135deg, #134e4a, #14b8a6);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(19, 78, 74, 0.35);
            margin-top: 5px;
            position: relative;
            overflow: hidden;
        }

        .btn-reset::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-reset:hover::before {
            left: 100%;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #14b8a6, #5eead4);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(19, 78, 74, 0.45);
        }

        .btn-reset:active {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(19, 78, 74, 0.35);
        }

        .notification {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .notification i {
            margin-right: 10px;
            margin-top: 2px;
        }

        .notification.success {
            background: rgba(236, 253, 245, 0.95);
            color: #006d40;
            border-left: 4px solid #10b981;
        }

        .notification.error {
            background: rgba(254, 242, 242, 0.95);
            color: #c62828;
            border-left: 4px solid #ef4444;
        }

        /* responsive */
        @media (max-width: 576px) {
            body {
                padding: 16px;
            }

            .bg-circle-1, .bg-circle-2, .bg-circle-3 {
                display: none;
            }

            .reset-container {
                width: 100%;
                max-width: 100%;
                padding: 22px 25px;
                border-radius: 22px;
            }

            .logo-container {
                width: 90px;
                height: 90px;
                margin: 0 auto 16px;
                padding: 8px;
            }

            .header {
                margin-bottom: 20px;
            }

            .header h1 {
                font-size: 24px;
                margin-bottom: 5px;
            }

            .header h2 {
                font-size: 15px;
                margin-bottom: 5px;
            }

            .header p {
                font-size: 13px;
            }

            form {
                gap: 14px;
            }

            .input-group i {
                left: 16px;
                font-size: 16px;
            }

            input {
                height: 46px;
                padding: 0 45px;
                font-size: 14px;
                border-radius: 12px;
            }

            .btn-reset {
                height: 46px;
                font-size: 14px;
                border-radius: 12px;
            }

            .notification {
                padding: 10px 14px;
                font-size: 13px;
                margin-bottom: 12px;
            }
        }

        @media (max-width: 380px) {
            body {
                padding: 12px;
            }

            .reset-container {
                padding: 18px 20px;
            }

            .logo-container {
                width: 80px;
                height: 80px;
                margin: 0 auto 12px;
                padding: 6px;
            }

            .header {
                margin-bottom: 18px;
            }

            .header h1 {
                font-size: 22px;
            }

            .header h2 {
                font-size: 14px;
            }

            .header p {
                font-size: 12px;
            }

            input {
                height: 42px;
                font-size: 13px;
            }

            .btn-reset {
                height: 42px;
                font-size: 13px;
            }
        }

        @media (max-height: 600px) {
            body {
                align-items: flex-start;
                padding-top: 20px;
                padding-bottom: 20px;
            }

            .reset-container:hover {
                transform: none;
            }

            .logo-container {
                width: 70px;
                height: 70px;
                margin: 0 auto 12px;
            }

            .header {
                margin-bottom: 16px;
            }

            .header h1 {
                font-size: 22px;
            }

            .header h2 {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="bg-circle-1"></div>
    <div class="bg-circle-2"></div>
    <div class="bg-circle-3"></div>
    <!-- container -->
    <div class="reset-container">
        <div class="logo-container">
            <!-- logo klinik -->
            <img src="{{ asset('images/logo-klinik.png') }}" alt="Dokter Ndeso">
        </div>

        <div class="header">
            <h1>RESET PASSWORD</h1>
            <h2>Amankan Akun Anda</h2>
            <p>Klinik Gigi Dokter Ndeso</p>
        </div>
        <!-- notif sukses -->
        @if (session('status'))
            <div class="notification success">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('status') }}
            </div>
        @endif
        <!-- notif error -->
        @if ($errors->any())
            <div class="notification error">
                <i class="fa-solid fa-exclamation-circle"></i>
                <ul style="margin: 0; padding-left: 20px; list-style-type: disc;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <!-- input email -->
            <div class="input-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda" required autofocus>
            </div>

            <div class="form-options">
                <a href="{{ route('login') }}">Kembali ke Login</a>
            </div>
            <!-- button link reset -->
            <button type="submit" class="btn-reset">KIRIM LINK RESET</button>
        </form>
    </div>
</body>
</html>