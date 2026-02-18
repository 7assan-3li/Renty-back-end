<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background-color: #008b96;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 28px;
            letter-spacing: 2px;
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .greeting {
            font-size: 20px;
            color: #006064;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .otp-box {
            background: #e0f7fa;
            color: #008b96;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 30px;
            border: 1px dashed #008b96;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
        }

        .footer a {
            color: #008b96;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>RENTY</h1>
        </div>
        <div class="content">
            <div class="greeting">Hello!</div>
            <p class="message">Thank you for registering with Renty. Please use the verification code below to activate
                your account.</p>

            <div class="otp-box">{{ $otp }}</div>

            <p class="message" style="margin-bottom: 0; font-size: 14px;">This code will expire in 10 minutes.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Renty. All rights reserved.<br>
            <a href="#">Privacy Policy</a> | <a href="#">Support</a>
        </div>
    </div>
</body>

</html>