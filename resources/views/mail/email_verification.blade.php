<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Contact Message</title>

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
            New Contact Message – {{ siteSetting()->site_title ?? 'Kahadhr' }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>You've Received a New Contact Message</h2>

            <p>
                Someone has submitted a message through the contact form on
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr' }}</strong>.
            </p>

            <!-- Contact Details -->
            <div class="details-box">
                <div class="details-row">
                    <span class="label">Name:</span>
                    {{ $contact->first_name }} {{ $contact->last_name }}
                </div>

                <div class="details-row">
                    <span class="label">Email:</span>
                    {{ $contact->email }}
                </div>

                <div class="details-row">
                    <span class="label">Phone:</span>
                    {{ $contact->phone ?? 'N/A' }}
                </div>

                <div class="details-row">
                    <span class="label">Topic:</span>
                    {{ $contact->topic }}
                </div>

                <div class="details-row">
                    <span class="label">Message:</span><br>
                    {{ $contact->description }}
                </div>
            </div>

            <p>
                Please reply to the user if support is needed.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Need system help? Contact support:
                <a href="mailto:{{ getSiteEmail() ?? 'support@kahadhr.com' }}">
                    {{ getSiteEmail() ?? 'support@kahadhr.com' }}
                </a>
            </p>

            <p>
                &copy; {{ date('Y') }}
                <strong>{{ siteSetting()->site_title ?? 'Kahadhr HRM' }}</strong>.
                All rights reserved.
            </p>

            <p>
                <a href="https://www.kahadhr.com" target="_blank">www.kahadhr.com</a>
            </p>
        </div>

    </div>

</body>

</html>
