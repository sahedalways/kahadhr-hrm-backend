<!DOCTYPE html>
<html>

<head>
    <title>{{ $title ?? 'Export Data' }}</title>
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
            text-align: left;
            margin-bottom: 10px;
            color: #164f84;
            font-size: 1.2rem;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            font-size: 0.9rem;
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


        .export-title {
            text-align: center;
            font-size: 1.2rem;
            color: #164f84;
            font-weight: 600;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <h1>{{ siteSetting()->site_title ?? 'My Site' }}</h1>
        <p>Email: {{ siteSetting()->site_email ?? 'support@example.com' }} | Phone:
            {{ siteSetting()->site_phone_number ?? 'N/A' }}</p>
        <p>Print Date: {{ now()->format('d M Y, H:i') }}</p>
    </header>

    <!-- Table -->
    <h2 class="export-title">{{ $title ?? 'Export Data' }}</h2>
    <table>
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th>{{ $column }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    @foreach ($keys as $key)
                        <td>{{ data_get($item, $key, 'N/A') }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        <p>
            {{ siteSetting()->copyright_text }}
        </p>
    </footer>
</body>

</html>
