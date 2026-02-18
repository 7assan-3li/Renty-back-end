<!DOCTYPE html>
<html>

<head>
    <title>Reset Password</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div
        style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #008b96; text-align: center;">Reset Your Password</h2>
        <p style="color: #666; font-size: 16px; line-height: 1.5;">
            You requested to reset your password for your Renty account. Use the code below to proceed:
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <span
                style="background-color: #f8f9fa; border: 2px dashed #008b96; color: #008b96; font-size: 24px; font-weight: bold; padding: 15px 30px; border-radius: 8px; letter-spacing: 5px;">
                {{ $otp }}
            </span>
        </div>

        <p style="color: #666; font-size: 14px;">
            This code will expire in 10 minutes.<br>
            If you did not request a password reset, please ignore this email.
        </p>

        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">

        <p style="color: #999; font-size: 12px; text-align: center;">
            &copy; {{ date('Y') }} Renty Car Rental. All rights reserved.
        </p>
    </div>
</body>

</html>