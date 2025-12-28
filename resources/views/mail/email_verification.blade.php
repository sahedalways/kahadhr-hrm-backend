<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Email Verification</title>

    <style>
        body {
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 40px 0;
        }

        .email-wrapper {
            max-width: 520px;
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
            padding: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .email-body {
            padding: 30px 25px;
            color: #333333;
        }

        h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            text-align: center;
        }

        p {
            line-height: 1.65;
            font-size: 0.95rem;
            color: #555555;
            margin-bottom: 14px;
        }

        .otp-box {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
            font-size: 1.3rem;
            letter-spacing: 3px;
            font-weight: bold;
            color: #164f84;
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

        <!-- Header -->
        <div class="email-header">
            {{ $data['title'] ?? 'Email Verification' }} – {{ siteSetting()->site_title }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Email Verification Code</h2>

            <p>Hello {{ $data['name'] ?? null }},</p>
            <p>We received a request to verify your email <strong>{{ $data['email'] }}</strong> of
                <strong>{{ siteSetting()->site_title ?? null }}</strong>.
            </p>

            <div class="otp-box">
                {{ $data['otp'] }}
            </div>

            <p>
                Enter this code in the app or website to complete your email verification.
                <br>
                This code will expire in 2 minutes.
            </p>

            <p>
                If you did not request this verification, please ignore this email.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Need help? Contact support:
                <a href="mailto:{{ getSiteEmail() ?? 'support@kahadhr.com' }}">
                    {{ getSiteEmail() ?? 'support@kahadhr.com' }}
                </a>
            </p>

            <p>
                {{ siteSetting()->copyright_text ?? '' }}
            </p>

            <p>
                <a href="{{ config('app.frontend_url') }}" target="_blank">
                    {{ config('app.frontend_url') }}
                </a>
            </p>
        </div>

    </div>

</body>

</html>
