<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #1e293b; /* كحلي أنيق يتماشى مع تصميمك */
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            letter-spacing: 1px;
        }
        .body {
            padding: 40px 30px;
            color: #334155;
            line-height: 1.6;
        }
        .body p {
            margin-bottom: 20px;
            font-size: 16px;
        }
        .otp-container {
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            display: inline-block;
            font-size: 32px;
            font-weight: bold;
            color: #1e293b;
            background-color: #f1f5f9;
            padding: 15px 30px;
            border-radius: 8px;
            letter-spacing: 5px;
            border: 1px solid #e2e8f0;
        }
        .warning {
            font-size: 14px;
            color: #64748b;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Forensic AI System</h1>
        </div>
        <div class="body">
            <p>Hello,</p>
            <p>We received a request to reset the password for your Forensic AI account. Please use the verification code below to securely set up a new password.</p>

            <div class="otp-container">
                <span class="otp-code">{{ $otp }}</span>
            </div>

            <p class="warning">This code is valid for <strong>60 seconds</strong>. If you did not request a password reset, please ignore this email to ensure your account remains secure.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Forensic AI. All rights reserved.<br>
            Secure access to forensic tools and case management.
        </div>
    </div>
</body>
</html>
