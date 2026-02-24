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
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
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
        }

        .page-break {
            page-break-after: always;
        }

        h3 {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    @foreach ($employees as $employee)
        <h3>Schedule for {{ mb_convert_encoding($employee->full_name, 'UTF-8', 'UTF-8') }}</h3>

        @php
            // Get all the dates to display for this employee
            if ($viewMode === 'weekly') {
                $displayDates = $weekDays;
            } else {
                // monthly: flatten weeks to a single list of dates
                $displayDates = [];
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

            // employee's shifts
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
                                    {{ mb_convert_encoding($shift['shift']['title'], 'UTF-8', 'UTF-8') }}<br>
                                    {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }} -
                                    {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                </div>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
    @endforeach
</body>

</html>
