<!DOCTYPE html>
<html>

<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 50px 30px;
        }

        body {
            font-family: Arial, sans-serif;
            position: relative;
        }

        header {
            text-align: center;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f2f6fa;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #164f84;
        }

        header p {
            margin: 4px 0;
            font-size: 0.9rem;
            color: #555;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #164f84;
            font-size: 1.4rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            font-size: 0.95rem;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #e0ebf7;
            color: #164f84;
        }

        tbody tr:nth-child(even) {
            background-color: #f7f9fc;
        }

        tbody tr:hover {
            background-color: #e8f0fc;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
            color: #777;
            border-top: 1px solid #ccc;
            padding: 8px 0;
        }

        footer p {
            margin: 0;
        }
    </style>
</head>

<body>

    <header>
        <h1>{{ siteSetting()->site_title ?? 'My Company' }}</h1>
        <p>Email: {{ siteSetting()->site_email ?? 'support@example.com' }} | Phone:
            {{ siteSetting()->site_phone_number ?? 'N/A' }}</p>
        <p>Invoice #: {{ $invoice->invoice_number }}</p>
        <p>Invoice Date: {{ $invoice->created_at->format('d M, Y') }}</p>
    </header>

    <h2>Invoice Details</h2>

    <p><strong>Billing Period:</strong> {{ $invoice->billing_period_start->format('d M, Y') }} -
        {{ $invoice->billing_period_end->format('d M, Y') }}</p>
    <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>

    <table>
        <thead>
            <tr>
                <th>Employee Fee</th>
                <th>Total Employees</th>
                <th>Subtotal</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>£{{ number_format($invoice->employee_fee, 2) }}</td>
                <td>{{ $invoice->total_employees_billed }}</td>
                <td>£{{ number_format($invoice->subtotal, 2) }}</td>
                <td>£{{ number_format($invoice->vat, 2) }}</td>
                <td>£{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <footer>
        <p>{{ siteSetting()->copyright_text ?? '© ' . now()->year . ' My Company' }}</p>
    </footer>

</body>

</html>
