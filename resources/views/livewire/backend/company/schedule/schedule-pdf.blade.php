<?php
// Set headers at the very beginning
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type"
          content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language"
          content="en">
    <style>
        body {
            font-family: 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }

        .emp-column {
            width: 120px;
            text-align: left;
            background-color: #f9f9f9;
            font-weight: bold;
        }

        thead th {
            background-color: #f1f4f9;
            color: #3366ff;
            padding: 10px 5px;
        }

        .shift-box {
            background-color: #000;
            color: #fff;
            padding: 4px;
            border-radius: 4px;
            font-size: 8px;
            margin-bottom: 2px;
            text-align: left;
        }

        .shift-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }

        /* Ensure proper character rendering */
        .utf8-safe {
            unicode-bidi: embed;
            direction: ltr;
        }

        .schedule-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .schedule-header h2 {
            margin-bottom: 5px;
            color: #3366ff;
        }

        .schedule-header .date-range {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="schedule-header">
        <h2>Employee Schedule</h2>
        <div class="date-range">
            <strong>Schedule Period:</strong>
            {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}
            to
            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="emp-column">EMPLOYEES</th>
                @foreach ($weekDays as $day)
                    <th>
                        {{ $day['day'] ?? '' }}<br>
                        <span style="font-weight: normal; color: #666;">{{ $day['date'] ?? '' }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $employee)
                <tr>
                    <td class="emp-column utf8-safe">
                        {{ $employee->full_name ?? 'N/A' }}<br>
                        <small style="font-weight: normal; color: #888;">Employee</small>
                    </td>

                    @foreach ($weekDays as $day)
                        @php
                            $dateKey = $day['full_date'] ?? '';
                            $shifts = isset($calendarShifts[$employee->id][$dateKey])
                                ? $calendarShifts[$employee->id][$dateKey]
                                : [];
                        @endphp
                        <td class="utf8-safe">
                            @if (!empty($shifts))
                                @foreach ($shifts as $shift)
                                    @php
                                        $shiftTitle = isset($shift['shift']['title'])
                                            ? htmlspecialchars($shift['shift']['title'], ENT_QUOTES, 'UTF-8')
                                            : 'Shift';
                                        $startTime = isset($shift['start_time'])
                                            ? \Carbon\Carbon::parse($shift['start_time'])->format('g:i A')
                                            : '';
                                        $endTime = isset($shift['end_time'])
                                            ? \Carbon\Carbon::parse($shift['end_time'])->format('g:i A')
                                            : '';
                                    @endphp
                                    <div class="shift-box">
                                        <span class="shift-title">{{ $shiftTitle }}</span>
                                        @if ($startTime && $endTime)
                                            {{ $startTime }} - {{ $endTime }}
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
