<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $data['title'] ?? 'Verify Your Email Address' }}</title>
    <style>
        body {
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 40px 0;
        }

        .email-wrapper {
            max-width: 480px;
            background-color: #ffffff;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(90deg, #164f84 0%, #0083bb 100%);
            color: #ffffff;
            text-align: center;
            padding: 20px;
            font-size: 1.1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .email-body {
            padding: 30px 25px;
            color: #333333;
        }

        h2 {
            text-align: center;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        p {
            line-height: 1.6;
            font-size: 0.95rem;
            color: #555555;
            margin-bottom: 14px;
        }

        .otp-box {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }

        .otp-label {
            color: #555555;
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        .otp-code {
            font-size: 2rem;
            font-weight: bold;
            color: #164f84;
            letter-spacing: 4px;
        }

        .footer {
            background-color: #f9fafc;
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #777777;
            border-top: 1px solid #eaeaea;
        }

        .footer a {
            color: #164f84;
            text-decoration: none;
        }

        .footer strong {
            color: #164f84;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            Email Verification – {{ siteSetting()->site_title ?? 'Kahadhr' }}
        </div>

        <div class="email-body">
            <h2>Hello {{ $data['companyName'] ?? 'User' }},</h2>
            <p>
                Thank you for joining
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr' }}</strong>!
            </p>
            <p>
                {{ $data['body'] ?? 'Please use the following verification code to confirm your email address:' }}
            </p>

            <div class="otp-box">
                <p class="otp-label">Your 6-digit verification code:</p>
                <div class="otp-code">{{ $data['otp'] ?? '******' }}</div>
            </div>

            <p>
                For your security, this code will expire in
                <strong>2 minutes</strong>. If you didn’t request this, please ignore
                this message.
            </p>
        </div>

        <div class="footer">
            <p>
                Need help? Contact us at
                <a href="mailto:{{ getSiteEmail() ?? 'support@kahadhr.com' }}">
                    {{ getSiteEmail() ?? 'support@Kahadhr.com' }}
                </a>
            </p>
            <p>
                &copy; {{ date('Y') }}
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr HRM' }}</strong>. All
                rights reserved.
            </p>
            <p>
                <a href="https://www.Kahadhr.com" target="_blank">www.Kahadhr.com</a>
            </p>
        </div>
    </div>
</body>

</html>
