<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0" />
    <title>New Demo Request</title>

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
            background: linear-gradient(90deg, #2998ff 0%, #205a97 100%);
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
            color: #2998ff;
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
            padding-bottom: 8px;
            border-bottom: 1px dashed #d1d5db;
        }

        .details-row:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #2998ff;
            display: inline-block;
            min-width: 120px;
        }

        .value {
            color: #333333;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            background: #2998ff20;
            color: #2998ff;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .highlight-box {
            background: linear-gradient(135deg, #2998ff10, #205a9710);
            border-left: 4px solid #2998ff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #2998ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 5px;
            transition: background 0.3s;
        }

        .button:hover {
            background: #205a97;
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
            color: #2998ff;
            text-decoration: none;
        }

        .footer strong {
            color: #2998ff;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="email-wrapper">

        <!-- Header -->
        <div class="email-header">
            🎯 New Demo Request – {{ siteSetting()->site_title ?? 'Kahadhr' }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>A Potential Client Requested a Demo</h2>

            <p>
                A user has requested a product demonstration on
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr' }}</strong>.
                Please review the details below and contact them promptly.
            </p>

            <!-- Request Reference -->
            <div class="highlight-box">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="label">Request Reference:</span>
                    <span class="badge">{{ $data['request_id'] ?? 'DEMO-' . uniqid() }}</span>
                </div>
                <div style="margin-top: 10px;">
                    <span class="label">Submitted:</span>
                    <span class="value">{{ $data['submitted_at'] ?? now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <!-- Company Details -->
            <h3 style="color: #2998ff; margin: 20px 0 10px;">🏢 Company Information</h3>
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Company Name:</span>
                    <span class="value">{{ $data['company_name'] ?? 'N/A' }}</span>
                </div>

                <div class="details-row">
                    <span class="label">Employee Count:</span>
                    <span class="value">{{ $data['employee_count'] ?? 'N/A' }}</span>
                </div>

                <div class="details-row">
                    <span class="label">Source:</span>
                    <span class="value">{{ $data['source'] ?? 'Website' }}</span>
                </div>
            </div>

            <!-- Contact Details -->
            <h3 style="color: #2998ff; margin: 20px 0 10px;">👤 Contact Person</h3>
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Full Name:</span>
                    <span class="value">{{ $data['full_name'] ?? 'N/A' }}</span>
                </div>

                <div class="details-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $data['email'] ?? 'N/A' }}</span>
                </div>

                <div class="details-row">
                    <span class="label">Mobile:</span>
                    <span class="value">{{ $data['mobile'] ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Demo Schedule -->
            <h3 style="color: #2998ff; margin: 20px 0 10px;">📅 Demo Schedule</h3>
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Demo Date:</span>
                    <span class="value">
                        {{ isset($data['demo_date']) ? \Carbon\Carbon::parse($data['demo_date'])->format('l, F j, Y') : 'N/A' }}
                    </span>
                </div>

                <div class="details-row">
                    <span class="label">Demo Time:</span>
                    <span class="value">{{ $data['demo_time'] ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Status -->
            <div style="text-align: center; margin: 20px 0;">
                <span class="status-pending">⏳ Status: Pending Response</span>
            </div>

            <!-- Quick Actions -->
            <div style="text-align: center; margin: 25px 0;">
                <p style="color: #666; margin-bottom: 15px;">
                    ⏱️ Please respond within 24 hours
                </p>
                <a href="mailto:{{ $data['email'] ?? '#' }}?subject=Demo Request Confirmation - {{ $data['company_name'] ?? '' }}"
                   class="button">
                    ✉️ Reply via Email
                </a>
                <a href="tel:{{ $data['mobile'] ?? '#' }}"
                   class="button"
                   style="background: #205a97;">
                    📞 Call Now
                </a>
            </div>

            <p style="font-size: 0.9rem; color: #666; text-align: center;">
                Make sure to confirm the demo timing and send calendar invitation.
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
                {{ siteSetting()->copyright_text ?? '© ' . date('Y') . ' Kahadhr. All rights reserved.' }}
            </p>

            <p>
                <a href="{{ config('app.frontend_url') ?? '#' }}"
                   target="_blank">
                    {{ config('app.frontend_url') ?? 'Kahadhr' }}
                </a>
            </p>

            <p style="margin-top: 10px; font-size: 0.7rem;">
                This is an automated message from your HRM system.
            </p>
        </div>

    </div>

</body>

</html>
