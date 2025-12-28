<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Invitation</title>

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

        .details-box {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 20px 0;
        }

        .details-row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: 600;
            color: #164f84;
        }

        .btn-link {
            display: inline-block;
            padding: 12px 20px;
            margin: 15px 0;
            background-color: #164f84;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
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
            Welcome to {{ siteSetting()->site_title ?? 'Our Company' }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Hello {{ $employee->email }},</h2>

            <p>
                You have been added as an employee at <strong>{{ siteSetting()->site_title ?? 'Our Company' }}</strong>.
            </p>

            <p>
                To get started, please set your account password by clicking the button below:
            </p>

            <!-- Invitation Link -->
            <div class="details-box" style="text-align: center;">
                <a href="{{ $inviteUrl }}" class="btn-link" target="_blank">Set Your Password</a>
            </div>

            <p>
                This link will expire in 48 hours. If you did not expect this email, please ignore it.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Need help? Contact support:
                <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}">
                    {{ getSiteEmail() ?? 'support@company.com' }}
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
