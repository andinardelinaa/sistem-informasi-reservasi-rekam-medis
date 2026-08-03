<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Klinik Gigi Dokter Ndeso</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .background {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #134e4a 0%, #128174 50%, #54cab8 100%);
            z-index: -2;
        }

        .bg-circle-1 {
            position: absolute;
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
            position: absolute;
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
            position: absolute;
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

        .welcome-text {
            position: absolute;
            left: 240px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            z-index: 1;
        }

        .welcome-text h1 {
            font-family: 'Roboto Flex', sans-serif;
            font-weight: 800;
            font-size: 72px;
            margin-bottom: 20px;
            letter-spacing: -2px;
            text-transform: uppercase;
            line-height: 1;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .welcome-text h2 {
            font-size: 24px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 15px;
        }

        .welcome-text p {
            font-size: 16px;
            opacity: 0.8;
            max-width: 400px;
            line-height: 1.6;
        }

        .login-container {
            width: 100%;
            max-width: 350px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(19, 78, 74, 0.2),
                        0 0 0 1px rgba(255, 255, 255, 0.3) inset;
            padding: 30px 25px;
            position: absolute;
            right: 300px;
            top: 50%;
            transform: translateY(-50%);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.4);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .login-container:hover {
            box-shadow: 0 20px 50px rgba(19, 78, 74, 0.25),
                        0 0 0 1px rgba(255, 255, 255, 0.4) inset;
            transform: translateY(-52%);
        }

        .login-container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #134e4a, #14b8a6, #5eead4);
        }

        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(19, 78, 74, 0.2);
            padding: 8px;
            border: 3px solid #f0fdfa;
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 30px rgba(19, 78, 74, 0.25);
        }

        .logo-container img {
            width: 78%;
            height: 78%;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-family: 'Roboto Flex', sans-serif;
            font-weight: 800;
            font-size: 24px;
            color: #134e4a;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: 600;
            color: #14b8a6;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 14px;
            color: #5f9ea0;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group {
            position: relative;
        }

        .input-group i.icon-left {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #134e4a;
            font-size: 20px;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus + .icon-left {
            color: #14b8a6;
            transform: translateY(-50%) scale(1.1);
        }

        .input-group .toggle-eye {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #5f9ea0;
            font-size: 20px;
            opacity: 0.75;
            transition: all 0.2s ease;
        }

        .input-group .toggle-eye:hover {
            opacity: 1;
            color: #14b8a6;
            transform: translateY(-50%) scale(1.1);
        }

        input {
            width: 100%;
            height: 44px;
            padding: 0 50px;
            background: rgba(255, 255, 255, 0.85);
            border: 1.5px solid rgba(19, 78, 74, 0.25);
            border-radius: 12px;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin: 5px 0 15px;
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

        .btn-login {
            height: 44px;
            background: linear-gradient(135deg, #134e4a, #14b8a6);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(19, 78, 74, 0.3);
            letter-spacing: 0.4px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #14b8a6, #5eead4);
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(19, 78, 74, 0.45);
        }

        .btn-login:active {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(19, 78, 74, 0.35);
        }

        .notification {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14.8px;
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
            margin-right: 12px;
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

        /* Responsive Design */
        @media (max-width: 1200px) {
            .welcome-text {
                left: 60px;
            }
            
            .welcome-text h1 {
                font-size: 56px;
            }
            
            .login-container {
                right: 258px;
            }
        }

        @media (max-width: 992px) {
            /* Welcome text untuk tablet - tampil di atas card */
            .welcome-text {
                position: relative;
                left: auto;
                top: auto;
                transform: none;
                text-align: center;
                margin-bottom: 20px;
                padding: 0 20px;
            }
            
            .welcome-text h1 {
                font-size: 48px;
                margin-bottom: 15px;
            }
            
            .welcome-text h2 {
                font-size: 20px;
                margin-bottom: 10px;
            }
            
            .welcome-text p {
                font-size: 15px;
                max-width: 100%;
            }
            
            /* Login container untuk tablet */
            .login-container {
                position: relative;
                right: auto;
                top: auto;
                transform: none;
                margin: 0 auto 20px;
                max-width: 420px;
            }
            
            .login-container:hover {
                transform: translateY(-8px);
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 15px;
                align-items: flex-start;
                padding-top: 40px;
            }
            
            .bg-circle-1, .bg-circle-2, .bg-circle-3 {
                display: none;
            }
            
            .welcome-text {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(10px);
                border-radius: 16px;
                padding: 20px;
                margin-bottom: 25px;
                width: 100%;
                max-width: 380px;
            }
            
            .welcome-text h1 {
                font-size: 36px;
                margin-bottom: 12px;
            }
            
            .welcome-text h2 {
                font-size: 18px;
                margin-bottom: 8px;
            }
            
            .welcome-text p {
                font-size: 14px;
            }
            
            .login-container {
                width: 100%;
                max-width: 380px;
                padding: 35px 25px;
                margin: 0;
                border-radius: 20px;
            }
            
            .logo-container {
                width: 90px;
                height: 90px;
                margin: 0 auto 25px;
                padding: 10px;
                border: 4px solid #f0fdfa;
            }
            
            .header {
                margin-bottom: 30px;
            }
            
            .header h1 {
                font-size: 28px;
                margin-bottom: 6px;
            }
            
            .header h2 {
                font-size: 16px;
                margin-bottom: 6px;
            }
            
            .header p {
                font-size: 14px;
            }
            
            form {
                gap: 18px;
            }
            
            .input-group i.icon-left {
                left: 18px;
                font-size: 18px;
            }
            
            .input-group .toggle-eye {
                right: 18px;
                font-size: 18px;
            }
            
            input {
                height: 52px;
                padding: 0 48px;
                font-size: 15px;
                border-radius: 14px;
            }
            
            .form-options {
                flex-direction: column;
                gap: 12px;
                margin: 5px 0 20px;
                font-size: 14px;
            }
            
            .btn-login {
                height: 52px;
                font-size: 15px;
                border-radius: 14px;
            }
            
            .notification {
                padding: 12px 15px;
                font-size: 14px;
                margin-bottom: 20px;
            }
            
            .notification i {
                margin-right: 10px;
                font-size: 16px;
            }
            .tagline {
                margin-top: 14px;
                font-size: 16px;
                font-weight: 700;
                letter-spacing: 1px;
                color: #ccfbf1;
                opacity: 0;
                transform: translateY(10px);
                animation: fadeUp 0.8s ease forwards;
                animation-delay: 0.3s;
            }

            @keyframes fadeUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        @media (max-width: 380px) {
            body {
                padding: 10px;
                padding-top: 30px;
            }
            
            .welcome-text {
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .welcome-text h1 {
                font-size: 32px;
            }
            
            .welcome-text h2 {
                font-size: 16px;
            }
            
            .welcome-text p {
                font-size: 13px;
            }
            
            .login-container {
                padding: 30px 20px;
                border-radius: 16px;
            }
            
            .logo-container {
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
                padding: 8px;
            }
            
            .header {
                margin-bottom: 25px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .header h2 {
                font-size: 15px;
            }
            
            .header p {
                font-size: 13px;
            }
            
            input {
                height: 48px;
                font-size: 14px;
            }
            
            .btn-login {
                height: 48px;
                font-size: 14px;
            }
        }

        @media (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
            }
            
            .welcome-text {
                padding: 10px 15px;
                margin-bottom: 15px;
            }
            
            .welcome-text h1 {
                font-size: 28px;
                margin-bottom: 8px;
            }
            
            .welcome-text h2 {
                font-size: 16px;
                margin-bottom: 5px;
            }
            
            .welcome-text p {
                font-size: 13px;
            }
            
            .login-container {
                padding: 25px 20px;
                margin: 0 auto 10px;
            }
            
            .logo-container {
                width: 70px;
                height: 70px;
                margin: 0 auto 15px;
            }
            
            .header {
                margin-bottom: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .header h2 {
                font-size: 15px;
            }
            
            .header p {
                font-size: 13px;
            }
            
            form {
                gap: 15px;
            }
            
            input {
                height: 45px;
            }
            
            .btn-login {
                height: 45px;
            }
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="bg-circle-1"></div>
    <div class="bg-circle-2"></div>
    <div class="bg-circle-3"></div>
    
    <!-- welcome text -->
    <div class="welcome-text">
        <h1>WELCOME!</h1>
        <h2>Klinik Gigi Dokter Ndeso</h2>
        <p>Sistem Reservasi & Rekam Medis Terpercaya untuk Kesehatan Gigi Anda</p>
        <p class="tagline">
            Cozy • Friendly • Worth It
        </p>
    </div>
    
    <!-- container login -->
    <div class="login-container">
        <div class="logo-container">
            <img src="{{ asset('images/logo-klinik.png') }}" alt="Dokter Ndeso">
        </div>

        <div class="header">
            <h1>LOGIN</h1>
            <h2>Sistem Reservasi & Rekam Medis</h2>
            <p>Klinik Gigi Dokter Ndeso</p>
        </div>
        <!-- notif suskes -->
        @if (session('success'))
            <div class="notification success">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('success') }}
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
        <!-- form untuk isi email dan password -->
        <form method="POST" action="{{ route('login.proses') }}">
            @csrf 
            <div class="input-group">
                <i class="fa-solid fa-envelope icon-left"></i>
                <input type="email" name="email" value="{{ old('email', request('email')) }}" placeholder="Email anda" required autofocus>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-key icon-left"></i>
                <input type="password" name="password" id="passwordField" placeholder="Password" required>
                <i class="fa-solid fa-eye-slash toggle-eye" id="toggleEye"></i>
            </div>

            <div class="form-options">
                <a href="{{ route('register') }}">Daftar akun baru</a>
                <a href="{{ route('password.request') }}">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login">MASUK</button>
        </form>
    </div>

    <script>
        // ambil elemen html
        const toggleEye = document.getElementById('toggleEye'); //ikon mata
        const passwordField = document.getElementById('passwordField'); //input pw
        // ketika ikon mata di klik jalankan fungsi ini
        toggleEye.addEventListener('click', function() {
            if (passwordField.type === 'password') { //cek tipe input
                passwordField.type = 'text'; //pw masih tersembunyi
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                passwordField.type = 'password'; //pw terlihat
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    </script>
</body>
</html>