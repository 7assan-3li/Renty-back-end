<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Renty</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #008b96;
            --secondary-color: #006064;
            --dark-bg: #1a1a1a;
            --light-bg: #f4f7f6;
            --text-color: #333;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --danger: #c62828;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-image: linear-gradient(135deg, #e0f7fa 0%, #ffffff 100%);
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .card-header {
            background: white;
            padding: 25px 25px 10px;
            text-align: center;
        }

        .card-header h3 {
            margin: 0;
            font-weight: 700;
            color: var(--secondary-color);
            font-size: 24px;
        }

        .card-header p {
            color: #777;
            font-size: 14px;
            margin-top: 5px;
        }

        .card-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #eee;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
            background: #fcfcfc;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 139, 150, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 139, 150, 0.2);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 139, 150, 0.3);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .input-icon input {
            padding-left: 40px;
        }

        .alert-danger {
            background: #ffebee;
            color: var(--danger);
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #ffcdd2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #888;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="brand-logo">
            <i class="fa-solid fa-car-side"></i> Renty Admin
        </div>
        @yield('content')
        <div class="footer-text">
            &copy; {{ date('Y') }} Renty Car Rental System
        </div>
    </div>
</body>

</html>