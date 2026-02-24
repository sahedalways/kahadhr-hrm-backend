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
    </style>
</head>

<body>
    <h3>Schedule for {{ mb_convert_encoding($employee->full_name, 'UTF-8', 'UTF-8') }}</h3>

    @if ($viewMode === 'weekly')
        <table>
            <thead>
                <tr>
                    @foreach ($weekDays as $day)
                        <th>{{ mb_convert_encoding($day['day'], 'UTF-8', 'UTF-8') }}<br>{{ mb_convert_encoding($day['date'], 'UTF-8', 'UTF-8') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($weekDays as $day)
                        @php
                            $dateKey = $day['full_date'];
                            $shifts = $calendarShifts[$dateKey] ?? [];
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
    @else
        <table>
            <thead>
                <tr>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                    <th>Sunday</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($weeks as $week)
                    <tr>
                        @foreach ($week as $day)
                            @php
                                $dateKey = $day->format('Y-m-d');
                                $shifts = $calendarShifts[$dateKey] ?? [];
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
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
