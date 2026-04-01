<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0" />
    <title>New Company Policy</title>

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

        .policy-link {
            display: inline-block;
            background-color: #164f84;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: 500;
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
            New Company Policy: {{ $policy->title }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Hello {{ $employee->full_name }},</h2>

            <p>
                A new company policy has been published at
                <strong>{{ $policy->company->company_name ?? 'Our Company' }}</strong>.
            </p>

            <div class="details-box">
                <div class="details-row">
                    <span class="label">Policy Title:</span> {{ $policy->title }}
                </div>
                @if ($policy->description)
                    <div class="details-row">
                        <span class="label">Description:</span> {!! $policy->description !!}
                    </div>
                @endif
                <div class="details-row">
                    <span class="label">Published Date:</span>
                    {{ \Carbon\Carbon::parse($policy->created_at)->format('d M, Y') }}
                </div>
            </div>

            @if ($policy->file_path)
                <p>
                    You can view or download the policy document here:
                </p>
                <div style="text-align: center;">
                    <a href="{{ asset('storage/' . $policy->file_path) }}"
                       class="policy-link"
                       target="_blank">
                        View Policy Document
                    </a>
                </div>
            @endif

            <p style="margin-top: 20px;">
                Please review this policy carefully. If you have any questions, contact your manager or HR department.
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
                <a href="{{ config('app.frontend_url') }}"
                   target="_blank">
                    {{ config('app.frontend_url') }}
                </a>
            </p>
        </div>

    </div>

</body>

</html>
