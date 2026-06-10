<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            margin: 40px 30px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.5;
        }

        .invoice-container {
            width: 100%;
        }

        .header {
            border-bottom: 2px solid #164f84;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #164f84;
            margin-bottom: 5px;
        }

        .company-info {
            color: #666;
            font-size: 12px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            color: #164f84;
            font-size: 32px;
            letter-spacing: 2px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            border: none;
            vertical-align: top;
        }

        .info-section {
            margin-bottom: 25px;
        }

        .info-box {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #164f84;
            text-transform: uppercase;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .details-table th {
            background: #164f84;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 12px;
        }

        .details-table td {
            border: 1px solid #ddd;
            padding: 12px;
        }

        .details-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            width: 320px;
            margin-left: auto;
            margin-top: 25px;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 8px 12px;
            border: none;
        }

        .totals .total-row {
            background: #164f84;
            color: white;
            font-weight: bold;
            font-size: 15px;
        }

        .totals .border-row td {
            border-top: 1px solid #ddd;
        }

        .status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .paid {
            background: #d1fae5;
            color: #065f46;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .overdue {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            text-align: center;
            color: #777;
            font-size: 11px;
        }
    </style>
</head>

<body>

    @php
        $status = strtolower($invoice->status ?? 'pending');
        $statusClass = in_array($status, ['paid', 'pending', 'overdue']) ? $status : 'pending';
    @endphp

    <div class="invoice-container">

        <!-- Header -->
        <table class="header-table">
            <tr>
                <td width="60%">
                    <div class="company-name">
                        {{ siteSetting()->site_title ?? 'My Company' }}
                    </div>

                    <div class="company-info">
                        Email:
                        {{ auth()->user()->user_type === 'company'
                            ? auth()->user()->company->company_email ?? 'N/A'
                            : siteSetting()->site_email ?? 'N/A' }}
                        <br>

                        Phone:
                        {{ auth()->user()->user_type === 'company'
                            ? auth()->user()->company->company_mobile ?? 'N/A'
                            : siteSetting()->site_phone_number ?? 'N/A' }}
                    </div>
                </td>

                <td width="40%"
                    class="invoice-title">
                    <h1>INVOICE</h1>

                    <strong>#{{ $invoice->invoice_number }}</strong><br>

                    Date:
                    {{ $invoice->created_at->format('d M, Y') }}
                </td>
            </tr>
        </table>

        <!-- Invoice Info -->
        <div class="info-section">

            <div class="info-box">
                <div class="label">Billing Period</div>

                {{ $invoice->billing_period_start->format('d M, Y') }}
                -
                {{ $invoice->billing_period_end->format('d M, Y') }}
            </div>

            <div class="info-box"
                 style="text-align:right;">
                <div class="label">Status</div>

                <span class="status {{ $statusClass }}">
                    {{ ucfirst($status) }}
                </span>
            </div>

        </div>

        <!-- Details Table -->
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Employees</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Employee Fee</td>

                    <td class="text-center">
                        {{ $invoice->total_employees_billed }}
                    </td>

                    <td class="text-right">
                        £{{ number_format($invoice->employee_fee, 2) }}
                    </td>

                    <td class="text-right">
                        £{{ number_format($invoice->subtotal, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">
                        £{{ number_format($invoice->subtotal, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>VAT</td>
                    <td class="text-right">
                        £{{ number_format($invoice->vat, 2) }}
                    </td>
                </tr>

                <tr class="border-row total-row">
                    <td>Total Due</td>
                    <td class="text-right">
                        £{{ number_format($invoice->total, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            {{ siteSetting()->copyright_text ?? '© ' . now()->year . ' My Company' }}
        </div>

    </div>

</body>

</html>
