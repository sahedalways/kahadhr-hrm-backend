<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $isReminder ? 'Training Reminder' : 'New Training Assigned' }}</title>

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
            {{ $isReminder ? 'Training Reminder: ' : 'New Training Assigned: ' }}{{ $training->course_name }}
        </div>

        <!-- Body -->
        <div class="email-body">
            <h2>Hello {{ $user->full_name }},</h2>

            @if ($isReminder)
                <p>
                    This is a friendly reminder to complete your assigned training at
                    <strong>{{ siteSetting()->site_title ?? 'Our Company' }}</strong>.
                </p>
            @else
                <p>
                    You have been assigned a new training at
                    <strong>{{ siteSetting()->site_title ?? 'Our Company' }}</strong>.
                </p>
            @endif

            <div class="details-box">
                <div class="details-row">
                    <span class="label">Course Name:</span> {{ $training->course_name }}
                </div>
                <div class="details-row">
                    <span class="label">From Date:</span>
                    {{ \Carbon\Carbon::parse($training->from_date)->format('d M, Y') }}
                </div>
                <div class="details-row">
                    <span class="label">To Date:</span>
                    {{ \Carbon\Carbon::parse($training->to_date)->format('d M, Y') }}
                </div>
                @if ($training->description)
                    <div class="details-row">
                        <span class="label">Description:</span> {!! $training->description !!}
                    </div>
                @endif
            </div>

            @if ($training->file_path)
                <p>
                    You can access the training file/video here:
                    <a href="{{ asset('storage/' . $training->file_path) }}" target="_blank">View Training</a>
                </p>
            @endif

            <p>
                Please complete this training before the expiry date (if any). If you have any questions, contact your
                manager.
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
