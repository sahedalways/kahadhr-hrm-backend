<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0" />
    <title>Document Notification</title>
    <style>
        body {
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 40px 0;
        }

        .email-wrapper {
            max-width: 520px;
            background-color: #fff;
            margin: 0 auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(90deg, #164f84 0%, #0083bb 100%);
            color: #fff;
            text-align: center;
            padding: 25px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .email-body {
            padding: 30px 25px;
            color: #333;
        }

        h2 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            text-align: center;
        }

        p {
            line-height: 1.65;
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 14px;
        }

        .footer {
            background-color: #f9fafc;
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #777;
            border-top: 1px solid #eaeaea;
        }

        .footer a {
            color: #164f84;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            Document Notification – {{ $company->company_name ?? 'Company Portal' }}
        </div>
        <div class="email-body">
            <h2>Document Notification</h2>
            <p>Hello {{ $employee->full_name }},</p>
            <p>{{ $messageText }}</p>

            <div class="document-info">
                <p><strong>Document Type:</strong> {{ $documentType->name }}</p>
                <p><strong>Company:</strong> {{ $company->company_name }}</p>
            </div>

            <p>
                <a href="{{ config('app.frontend_url') }}"
                   style="color:#fff; background-color:#164f84; padding:10px 20px; border-radius:5px; text-decoration:none;">
                    View Portal
                </a>
            </p>
        </div>
        <div class="footer">
            <p>Need help? Contact support: <a
                   href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}">{{ getSiteEmail() ?? 'support@company.com' }}</a>
            </p>
            <p>{{ siteSetting()->copyright_text ?? '' }}</p>
            <p><a href="{{ config('app.frontend_url') }}"
                   target="_blank">{{ config('app.frontend_url') }}</a></p>
        </div>
    </div>
</body>

</html>
