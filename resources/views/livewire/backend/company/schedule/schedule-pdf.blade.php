<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Schedule PDF</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            page-break-inside: auto;
            /* allow table to break across pages */
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
            page-break-inside: avoid;
            /* keep cell content together */
            page-break-after: auto;
        }

        th {
            background-color: #f0f0f0;
        }

        .shift-block {
            color: #fff;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 10px;
            margin-bottom: 2px;
            display: inline-block;
            page-break-inside: avoid;
            /* keep shift together */
        }

        h3 {
            margin-bottom: 10px;
        }

        /* Removed page-break-after: always */
    </style>
</head>

<body>
    @foreach ($employees as $employee)
        <h3>Schedule for {{ $employee->full_name }}</h3>

        @php
            $displayDates = $viewMode === 'weekly' ? $weekDays : [];
            if ($viewMode !== 'weekly') {
                foreach ($weeks as $week) {
                    foreach ($week as $day) {
                        $displayDates[] = [
                            'full_date' => $day->format('Y-m-d'),
                            'day' => $day->format('D'),
                            'date' => $day->format('d/m'),
                        ];
                    }
                }
            }

            $empShifts = $calendarShifts[$employee->id] ?? [];
        @endphp

        <table>
            <thead>
                <tr>
                    @foreach ($displayDates as $day)
                        <th>{{ $day['day'] }}<br>{{ $day['date'] }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($displayDates as $day)
                        @php
                            $dateKey = $day['full_date'];
                            $shifts = $empShifts[$dateKey] ?? [];
                        @endphp
                        <td>
                            @foreach ($shifts as $shift)
                                <div class="shift-block"
                                     style="background-color: {{ $shift['shift']['color'] ?? '#6c757d' }}">
                                    {{ $shift['shift']['title'] }}<br>
                                    {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                </div>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach
</body>

</html>
