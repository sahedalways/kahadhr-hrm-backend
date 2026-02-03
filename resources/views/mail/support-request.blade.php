<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>New Support Request</title>
    <style>
        body {
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            background: linear-gradient(90deg, #164f84 0%, #0083bb 100%);
            color: #ffffff;
            text-align: center;
            padding: 25px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .email-body {
            padding: 30px 25px;
            color: #333333;
        }

        .email-body h2 {
            font-size: 1.4rem;
            text-align: center;
            margin-bottom: 20px;
        }

        .email-body p {
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .details-box {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .details-row {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .details-row .label {
            font-weight: 600;
            color: #164f84;
            display: inline-block;
            width: 120px;
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

        @media only screen and (max-width: 600px) {
            .details-row .label {
                display: block;
                width: 100%;
                margin-bottom: 3px;
            }
        }
    </style>
</head>

<body>
    <div class="email-wrapper">

        <!-- Header -->
        <div class="email-header">
            New Support Request
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>You've Received a New Support Request</h2>
            <p>
                A user submitted a request from
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr' }}</strong>.
            </p>

            <!-- Contact Details -->
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Company Name:</span> {{ $contact['company_name'] }}
                </div>
                <div class="details-row">
                    <span class="label">House Number:</span> {{ $contact['house_number'] }}
                </div>
                <div class="details-row">
                    <span class="label">Email:</span> {{ $contact['email'] }}
                </div>
                <div class="details-row">
                    <span class="label">Mobile:</span> {{ $contact['mobile'] ?? 'N/A' }}
                </div>
                <div class="details-row">
                    <span class="label">Subject:</span> {{ $contact['subject'] }}
                </div>
                <div class="details-row">
                    <span class="label">Message:</span><br>
                    {{ $contact['description'] }}
                </div>

                @if (!empty($contact['attachment_path']))
                    <div class="details-row">
                        <span class="label">Attachment:</span>
                        <a href="{{ $contact['attachment_path'] }}"
                           target="_blank">View File</a>
                    </div>
                @endif
            </div>

            <p>Please reply to the user if support is needed.</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Need help? Contact support:
                <a href="mailto:{{ getSiteEmail() ?? 'support@kahadhr.com' }}">
                    {{ getSiteEmail() ?? 'support@kahadhr.com' }}
                </a>
            </p>
            <p>{{ siteSetting()->copyright_text ?? '' }}</p>
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
