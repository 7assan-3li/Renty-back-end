<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renty Admin - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #008b96;
            --primary-dark: #006064;
            --dark-bg: #1a1a1a;
            --text-color: #333;
            --light-gray: #f4f7f6;
            --bg-color: #eef2f5;
            --card-bg: #fff;
            --input-bg: #f9f9f9;
            --input-border: #eee;
        }

        [data-theme="dark"] {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #f0f0f0;
            --light-gray: #2d2d2d;
            --input-bg: #2d2d2d;
            --input-border: #444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        body {
            background-color: var(--bg-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-color);
        }

        /* Login Container */
        .login-container {
            background: var(--card-bg);
            width: 1000px;
            max-width: 90%;
            height: 600px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            display: flex;
            overflow: hidden;
            animation: slideUp 0.8s ease-out;
            position: relative;
        }

        /* Right Side (Visual) */
        .info-side {
            flex: 1;
            background: linear-gradient(135deg, var(--dark-bg), var(--primary-dark));
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .info-side::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: var(--primary-color);
            opacity: 0.1;
            border-radius: 50%;
            z-index: -1;
        }

        .info-side::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: var(--primary-color);
            opacity: 0.1;
            border-radius: 50%;
            z-index: -1;
        }

        .brand-logo {
            font-size: 50px;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            display: inline-block;
            z-index: 20;
        }

        html[dir="rtl"] .brand-logo i {
            transform: scaleX(1);
        }

        html[dir="ltr"] .brand-logo i {
            transform: scaleX(-1);
        }

        /* Animation Keyframes remain same but ensure they work */
        html[dir="rtl"] .car-drive-action {
            animation: driveRight 3.2s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        html[dir="ltr"] .car-drive-action {
            animation: driveLeft 3.2s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
        }

        @keyframes driveRight {
            0% {
                transform: translateX(0) scaleX(1);
                opacity: 1;
            }

            20% {
                transform: translateX(-40px) scaleX(1) scale(0.95);
                opacity: 1;
            }

            30% {
                transform: translateX(0) scaleX(1) scale(1);
                opacity: 1;
            }

            60% {
                opacity: 1;
            }

            100% {
                transform: translateX(800px) scaleX(1) scale(1.1);
                opacity: 0;
            }
        }

        @keyframes driveLeft {
            0% {
                transform: translateX(0) scaleX(-1);
                opacity: 1;
            }

            20% {
                transform: translateX(40px) scaleX(-1) scale(0.95);
                opacity: 1;
            }

            30% {
                transform: translateX(0) scaleX(-1) scale(1);
                opacity: 1;
            }

            60% {
                opacity: 1;
            }

            100% {
                transform: translateX(-800px) scaleX(-1) scale(1.1);
                opacity: 0;
            }
        }

        .info-text h2 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 800;
        }

        .info-text p {
            font-size: 16px;
            opacity: 0.8;
            line-height: 1.6;
            max-width: 300px;
            margin: 0 auto;
        }

        /* Left Side (Form) */
        .form-side {
            flex: 1;
            background: var(--card-bg);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 5;
        }

        .top-controls {
            position: absolute;
            top: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        html[dir="rtl"] .top-controls {
            left: 20px;
        }

        html[dir="ltr"] .top-controls {
            right: 20px;
        }


        .control-btn {
            background: var(--light-gray);
            color: var(--text-color);
            font-weight: bold;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .control-btn:hover {
            background: var(--input-border);
        }

        .form-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .form-header h3 {
            font-size: 28px;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .form-header p {
            color: #888;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            border: 2px solid var(--input-border);
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
            background: var(--input-bg);
            color: var(--text-color);
        }

        html[dir="rtl"] .form-control {
            padding: 15px 50px 15px 15px;
        }

        html[dir="ltr"] .form-control {
            padding: 15px 15px 15px 50px;
        }

        .input-group i.input-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: 0.3s;
        }

        html[dir="rtl"] .input-group i.input-icon {
            right: 15px;
        }

        html[dir="ltr"] .input-group i.input-icon {
            left: 15px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(0, 139, 150, 0.1);
        }

        .form-control:focus+i.input-icon {
            color: var(--primary-color);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc;
        }

        html[dir="rtl"] .toggle-password {
            left: 15px;
        }

        html[dir="ltr"] .toggle-password {
            right: 15px;
        }

        .toggle-password:hover {
            color: var(--text-color);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: var(--text-color);
            opacity: 0.8;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .checkbox-container input {
            accent-color: var(--primary-color);
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0, 139, 150, 0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 139, 150, 0.3);
        }

        html[dir="rtl"] .login-btn i {
            transform: rotate(0deg);
        }

        html[dir="ltr"] .login-btn i {
            transform: rotate(180deg);
        }

        .text-danger {
            color: #dc3545;
            font-size: 12px;
            display: block;
            margin-top: 5px;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: auto;
            }

            .info-side {
                padding: 30px;
            }

            .brand-logo {
                font-size: 40px;
            }

            .form-side {
                padding: 30px;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <div class="info-side">
            <div class="brand-logo"><i id="anim-car" class="fa-solid fa-car-side"></i></div>
            <div class="info-text">
                <h2>Renty Admin</h2>
                <p>{{ __('The most advanced and smart fleet management system. Control every detail from one place.') }}
                </p>
            </div>
        </div>

        <div class="form-side">
            <div class="top-controls">
                <button class="control-btn" onclick="toggleTheme()" title="Toggle Theme">
                    <i class="fa-solid fa-moon" id="theme-icon"></i>
                </button>
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="control-btn">
                    {{ app()->getLocale() == 'ar' ? 'ENG' : 'عربي' }}
                </a>
            </div>

            <div class="form-header">
                <h3>{{ __('Welcome Back!') }}</h3>
                <p>{{ __('Please enter your details to access the dashboard') }}</p>
            </div>

            <form id="loginForm" method="POST" action="{{ route('admin.login.submit') }}"
                onsubmit="event.preventDefault(); login();">
                @csrf

                @if ($errors->any())
                    <div style="color: red; margin-bottom: 20px; font-size: 14px;">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="input-group">
                    <input type="email" name="email" class="form-control" id="emailInput"
                        placeholder="{{ __('Email Address') }}" required value="{{ old('email') }}">
                    <i class="fa-solid fa-envelope input-icon"></i>
                </div>

                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="passwordInput"
                        placeholder="{{ __('Password') }}" required>
                    <i class="fa-solid fa-lock input-icon"></i>
                    <i class="fa-solid fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>

                <div class="form-options">
                    <label class="checkbox-container">
                        <input type="checkbox" name="remember"> <span>{{ __('Remember me') }}</span>
                    </label>
                </div>

                <button type="submit" class="login-btn">
                    <span id="loginBtnText">{{ __('Login') }}</span>
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </form>

            <div style="margin-top: 30px; text-align: center; font-size: 13px; color: #999;">
                &copy; {{ date('Y') }} Renty Car Rental System. <span>{{ __('All rights reserved.') }}</span>
            </div>
        </div>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            const icon = document.getElementById('theme-icon');
            if (icon) {
                icon.className = newTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
            }
        }

        // Initialize Theme
        (function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Wait for DOM to set icon
            document.addEventListener('DOMContentLoaded', () => {
                const icon = document.getElementById('theme-icon');
                if (icon) {
                    icon.className = savedTheme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
                }
            });
        })();

        function togglePassword() {
            const passwordField = document.getElementById('passwordInput');
            const icon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function login() {
            const btn = document.querySelector('.login-btn');
            const btnText = document.getElementById('loginBtnText');
            const carIcon = document.getElementById('anim-car');
            const form = document.getElementById('loginForm');

            // UI Feedback
            btnText.innerHTML = "{{ __('Logging in...') }}";
            btn.style.opacity = '0.8';
            btn.disabled = true;

            // Animate
            carIcon.classList.add('car-drive-action');

            // Submit form after animation
            setTimeout(() => {
                form.submit();
            }, 2500);
        }
    </script>

</body>

</html>