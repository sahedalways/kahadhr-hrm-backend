<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Invoice Paid</title>
</head>

<body style="font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #f5f7fa; margin: 0; padding: 40px 0;">

    {{-- Main Container --}}
    <div
         style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden;">

        {{-- Header with Gradient --}}
        <div
             style="background: linear-gradient(135deg, #0b4f6c 0%, #1e88e5 100%); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Invoice Paid</h1>
            <p style="margin: 10px 0 0; opacity: 0.9; font-size: 16px;">Thank you for your payment</p>
        </div>

        {{-- Body --}}
        <div style="padding: 40px 35px; color: #2c3e50;">

            {{-- Greeting --}}
            <p style="font-size: 18px; margin-top: 0;">Hello <strong>{{ $company->company_name }}</strong>,</p>

            <p style="line-height: 1.6; color: #34495e;">
                We have successfully received your subscription payment. Please find your invoice details below.
            </p>

            {{-- Invoice Details Card --}}
            <div
                 style="background: #f8fafd; border-radius: 12px; padding: 25px; margin: 25px 0; border: 1px solid #e9ecef;">

                {{-- Invoice Number --}}
                <div
                     style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px dashed #ddd; padding-bottom: 15px; margin-bottom: 15px;">
                    <span style="font-size: 16px; color: #7f8c8d;">Invoice Number</span>
                    <span
                          style="font-size: 22px; font-weight: 700; color: #0b4f6c;">{{ $invoice->invoice_number }}</span>
                </div>

                {{-- Billing Period --}}
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: #7f8c8d;">Billing Period</span>
                    <span style="font-weight: 500;">
                        {{ \Carbon\Carbon::parse($invoice->billing_period_start)->format('d M Y') }}
                        –
                        {{ \Carbon\Carbon::parse($invoice->billing_period_end)->format('d M Y') }}
                    </span>
                </div>

                {{-- Invoice Date --}}
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: #7f8c8d;">Invoice Date</span>
                    <span
                          style="font-weight: 500;">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d M Y') }}</span>
                </div>

                {{-- Employees Billed --}}
                <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                    <span style="color: #7f8c8d;">Employees Billed</span>
                    <span style="font-weight: 500;">{{ $invoice->total_employees_billed }} ×
                        {{ number_format($invoice->employee_fee, 2) }} {{ $invoice->currency }}</span>
                </div>

                {{-- Divider --}}
                <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>

                {{-- Subtotal --}}
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #7f8c8d;">Subtotal</span>
                    <span style="font-weight: 500;">{{ number_format($invoice->subtotal, 2) }}
                        {{ $invoice->currency }}</span>
                </div>

                {{-- VAT --}}
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span style="color: #7f8c8d;">VAT</span>
                    <span style="font-weight: 500;">{{ number_format($invoice->vat, 2) }}
                        {{ $invoice->currency }}</span>
                </div>

                {{-- Total - Highlighted --}}
                <div
                     style="display: flex; justify-content: space-between; margin-top: 15px; padding-top: 15px; border-top: 2px solid #0b4f6c; font-size: 20px;">
                    <span style="font-weight: 600; color: #0b4f6c;">Total Paid</span>
                    <span style="font-weight: 700; color: #1e88e5;">{{ number_format($invoice->total, 2) }}
                        {{ $invoice->currency }}</span>
                </div>
            </div>

            {{-- Next Billing Date --}}
            <div
                 style="background: #e3f2fd; border-radius: 8px; padding: 15px 20px; margin: 20px 0; border-left: 4px solid #1e88e5;">
                <p style="margin: 0; font-size: 15px;">
                    <strong style="color: #0b4f6c;">Next Billing Date:</strong>
                    {{ \Carbon\Carbon::parse($company->subscription_end)->format('d M Y') }}
                </p>
            </div>

            {{-- Button --}}
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.frontend_url') }}/company/invoices/{{ $invoice->id }}"
                   style="background: #1e88e5; color: white; padding: 14px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; display: inline-block; box-shadow: 0 4px 10px rgba(30,136,229,0.3);">
                    View Invoice
                </a>
            </div>

            <p style="font-size: 14px; color: #7f8c8d; text-align: center; margin-top: 20px;">
                If you have any questions, please contact our support team.
            </p>

        </div>

        {{-- Footer --}}
        <div
             style="background: #f8fafd; text-align: center; padding: 25px; font-size: 13px; color: #7f8c8d; border-top: 1px solid #e9ecef;">
            <p style="margin: 0 0 10px;">
                <strong>{{ siteSetting()->site_title ?? 'Company Portal' }}</strong>
            </p>
            <p style="margin: 0 0 5px;">
                Need help?
                <a href="mailto:{{ getSiteEmail() ?? 'support@company.com' }}"
                   style="color: #1e88e5; text-decoration: none;">
                    {{ getSiteEmail() ?? 'support@company.com' }}
                </a>
            </p>
            <p style="margin: 10px 0 0; font-size: 12px;">
                {{ siteSetting()->copyright_text ?? '© 2026 All rights reserved.' }}
            </p>
            <p style="margin: 5px 0 0;">
                <a href="{{ config('app.frontend_url') }}"
                   target="_blank"
                   style="color: #7f8c8d; text-decoration: none;">
                    {{ config('app.frontend_url') }}
                </a>
            </p>
        </div>

    </div>
</body>

</html>
