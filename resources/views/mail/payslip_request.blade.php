<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payslip Request Notification</title>

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

        .document-info {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            font-size: 1rem;
            color: #164f84;
        }

        .document-info p {
            margin-bottom: 6px;
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
    </style>
</head>

<body>

    <div class="email-wrapper">

        <div class="email-header">
            Payslip Request – {{ siteSetting()->site_title ?? 'Company Portal' }}
        </div>

        <div class="email-body">
            <h2>New Payslip Request Submitted</h2>

            <p>Hello {{ $company->company_name ?? 'Company' }},</p>

            <p>
                A new payslip request has been submitted by an employee.
                Below are the details:
            </p>

            <div class="document-info">
                <p><strong>Requested By:</strong> {{ $user->full_name ?? 'N/A' }}</p>
                <p><strong>Employee Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                <p><strong>Period:</strong> {{ $request->period }}</p>
                <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
                <p><strong>Requested On:</strong> {{ $request->created_at->format('d M Y, H:i') }}</p>
            </div>

            <p>Please review the request and upload the payslip from the company portal.</p>

        </div>

        <div class="footer">
            <p>
                Need help? Contact support:
                <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}">
                    {{ getSiteEmail() ?? 'support@company.com' }}
                </a>
            </p>

            <p>{{ siteSetting()->copyright_text ?? '' }}</p>

            <p>
            <p><a href="{{ config('app.frontend_url') }}" target="_blank">
                    {{ config('app.frontend_url') }}
                </a></p>
            </p>
        </div>

    </div>

</body>

</html>
