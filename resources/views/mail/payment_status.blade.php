<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
</head>

<body style="font-family:Segoe UI, Roboto, Arial; background:#f5f7fa; padding:40px 0;">
    <div
        style="max-width:520px;margin:auto;background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden;">

        {{-- Header --}}
        <div
            style="background:linear-gradient(90deg,#164f84,#0083bb);color:#fff;text-align:center;padding:25px;font-size:18px;font-weight:600;">
            {{ siteSetting()->site_title ?? 'Company Portal' }}
        </div>

        {{-- Body --}}
        <div style="padding:30px 25px;color:#333;">

            {{-- 🔴 Subscription Suspended --}}
            @if ($type === 'subscription_suspended')
                <h2 style="text-align:center;color:#c0392b;">Subscription Suspended</h2>

                <p>Hello {{ $company->company_name }},</p>

                <p>
                    Your subscription has been <strong>suspended</strong> due to multiple failed
                    payment attempts.
                </p>

                <p>
                    Please update your card to restore service immediately.
                </p>

                {{-- 🔔 Card Reminder --}}
            @elseif($type === 'card_reminder')
                <h2 style="text-align:center;color:#2980b9;">Subscription Ending Soon</h2>

                <p>Hello {{ $company->company_name }},</p>

                <p>
                    Your subscription will expire soon, but no payment method is currently on file.
                </p>

                <p>
                    Please add your card before the expiry date to avoid any service interruption.
                </p>

                {{-- ⚠️ Payment Failed --}}
            @else
                <h2 style="text-align:center;color:#f39c12;">Payment Failed</h2>

                <p>Hello {{ $company->company_name }},</p>

                <p>
                    Your recent subscription payment attempt has failed.
                </p>

                <p>
                    Please update your card to avoid service interruption.
                </p>
            @endif


        </div>

        {{-- Footer --}}
        <div
            style="background:#f9fafc;text-align:center;padding:20px;font-size:12px;color:#777;border-top:1px solid #eee;">
            <p>
                Need help?
                <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}">
                    {{ getSiteEmail() ?? 'support@company.com' }}
                </a>
            </p>
            <p>{{ siteSetting()->copyright_text ?? '' }}</p>

            <p><a href="{{ config('app.frontend_url') }}" target="_blank">
                    {{ config('app.frontend_url') }}
                </a></p>
        </div>

    </div>
</body>

</html>
