<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0" />
    <title>Demo Request Confirmation</title>

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
        }

        .email-body {
            padding: 30px 25px;
            color: #333333;
        }

        h2 {
            color: #2998ff;
            font-size: 1.4rem;
            margin-bottom: 15px;
            text-align: center;
        }

        .details-box {
            background-color: #f0f4f8;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 20px 0;
        }

        .label {
            font-weight: 600;
            color: #2998ff;
            min-width: 100px;
            display: inline-block;
        }

        .footer {
            background-color: #f9fafc;
            text-align: center;
            padding: 20px;
            font-size: 0.8rem;
            color: #777777;
            border-top: 1px solid #eaeaea;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #2998ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-header">
            🎯 Demo Request Received – {{ siteSetting()->site_title ?? 'Kahadhr' }}
        </div>

        <div class="email-body">
            <h2>Thank You, {{ $data['full_name'] ?? 'there' }}!</h2>

            <p>
                We've received your request for a demo of <strong>{{ siteSetting()->site_title ?? 'Kahadhr' }}</strong>.
            </p>

            <div class="details-box">
                <p><span class="label">Reference:</span> {{ $data['request_id'] ?? 'DEMO-' . uniqid() }}</p>
                <p><span class="label">Demo Date:</span>
                    {{ isset($data['demo_date']) ? \Carbon\Carbon::parse($data['demo_date'])->format('l, F j, Y') : 'N/A' }}
                </p>
                <p><span class="label">Demo Time:</span> {{ $data['demo_time'] ?? 'N/A' }}</p>
            </div>

            <h3>What Happens Next?</h3>
            <ol>
                <li>Our team will review your request</li>
                <li>You'll receive a confirmation email within 24 hours</li>
                <li>We'll send a calendar invitation with meeting details</li>
            </ol>

            <div style="text-align: center;">
                <a href="{{ config('app.frontend_url') ?? '#' }}"
                   class="button">
                    Visit Our Website
                </a>
            </div>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} {{ siteSetting()->site_title ?? 'Kahadhr' }}</p>
        </div>
    </div>
</body>

</html>
