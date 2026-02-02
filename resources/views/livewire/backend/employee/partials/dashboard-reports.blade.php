@push('styles')
    <link href="{{ asset('assets/css/employee-dashboard.css') }}"
          rel="stylesheet" />
@endpush
@php
    $hour = now()->setTimezone('Europe/London')->hour;

    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Good Morning';
        $emoji = '🌅';
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = 'Good Afternoon';
        $emoji = '☀️';
    } elseif ($hour >= 17 && $hour < 21) {
        $greeting = 'Good Evening';
        $emoji = '🌇';
    } else {
        $greeting = 'Good Night';
        $emoji = '🌙';
    }

@endphp



<div class="container-fluid py-4"
     wire:poll.60s>
    <header class="mb-4">
        <h2 class="fw-bold">
            {{ $greeting }}, {{ auth()->user()->full_name }}! {{ $emoji }}
        </h2>

        <p class="text-muted">
            @if ($todayShift)
                Today's Shift:
                {{ \Carbon\Carbon::parse($todayShift->start_time)->format('g:i A') }}
                -
                {{ \Carbon\Carbon::parse($todayShift->end_time)->format('g:i A') }}
            @else
                No shift scheduled for today
            @endif
        </p>

    </header>


    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="custom-card mb-4 text-center">
                <h6 class="text-start text-muted fw-bold">Time Clock</h6>
                <h1 class="display-4 fw-bold my-3"
                    wire:ignore>
                    <span id="liveTime">--:--</span> <small id="livePeriod">--</small>
                </h1>

                <div class="clock-wrapper mb-3">
                    <div
                         class="btn clock-btn w-100 py-3 shadow-sm d-flex align-items-center justify-content-center transition-all {{ $isRunning ? 'btn-active' : 'btn-inactive' }}">

                        @if ($isRunning)
                            <div class="pulse-indicator me-2"></div>
                            <span class="fw-bold text-uppercase letter-spacing-1">Clocked In</span>
                            <i class="bi bi-box-arrow-right ms-2 fs-5"></i>
                        @else
                            <i class="bi bi-play-circle-fill me-2 fs-5 text-success"></i>
                            <span class="fw-bold text-uppercase letter-spacing-1">Clocked Out</span>
                        @endif
                    </div>
                </div>

                <p class="small mt-2 mb-0">
                    @if ($isRunning && $currentAttendance)
                        Last clocked in at
                        {{ \Carbon\Carbon::parse($currentAttendance->clock_in, auth()->user()->timezone ?? 'Asia/Dhaka')->format('h:i A') }}
                    @elseif(!$isRunning && $currentAttendance)
                        Last clocked out at
                        {{ \Carbon\Carbon::parse($currentAttendance->clock_out, auth()->user()->timezone ?? 'Asia/Dhaka')->format('h:i A') }}
                    @else
                        No attendance today
                    @endif
                </p>

                <div class="mt-5">

                    {{-- Progress Label --}}
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">
                            Worked: {{ round($workedHours ?? 0, 2) }} hrs
                        </small>
                        <small class="text-muted">
                            Contract: {{ $contractHours ?? 0 }} hrs
                        </small>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="progress"
                         style="height: 8px;">
                        <div class="progress-bar bg-info"
                             role="progressbar"
                             style="width: {{ $weeklyProgress }}%;"
                             aria-valuenow="{{ $weeklyProgress }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>

                    {{-- Optional Percentage Label --}}
                    <p class="small mt-2 mb-0 text-end">
                        Weekly Hours Progress: {{ round($weeklyProgress, 1) }}%
                    </p>

                </div>



            </div>

            <div class="custom-card">
                <h6 class="text-muted fw-bold">Quick Actions</h6>

                <a href="{{ route('employee.dashboard.leaves.index', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                   class="btn btn-outline-secondary w-100 mb-2 text-start">
                    ✈️ Request Leave
                </a>


                <a href="{{ route('employee.dashboard.reports.expenses', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                   class="btn btn-outline-secondary w-100 mb-2 text-start">
                    💵 Submit Expense
                </a>


                <a href="{{ route('employee.dashboard.reports.payslips', ['company' => app('authUser')->employee->company->sub_domain]) }}"
                   class="btn btn-outline-secondary w-100 text-start">
                    📄 View Payslips
                </a>
            </div>

        </div>




        <div class="col-lg-5 col-md-6">
            <div class="custom-card mb-4">
                <h6 class="text-muted fw-bold mb-3">My Leave Balances</h6>

                <div class="d-flex justify-content-around text-center"
                     id="leaveChartsWrapper"
                     wire:ignore>

                </div>

                <input type="hidden"
                       id="leaveBalancesData"
                       value='{!! json_encode($leaveBalances) !!}'>



            </div>

            <div class="custom-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <!-- Title -->
                    <h6 class="text-muted fw-bold mb-0">My Personal Calendar</h6>

                    <!-- Month Navigation -->
                    <div class="d-flex align-items-center border rounded-2 shadow-sm bg-white"
                         style="font-size: 0.85rem;">
                        <!-- Previous Month -->
                        <button wire:click="previousMonth"
                                class="btn btn-light border-0 px-2 py-1 rounded-start"
                                title="Previous Month">
                            <i class="fas fa-chevron-left text-muted"></i>
                        </button>

                        <!-- Current Month & Year -->
                        <div class="px-2 py-1 text-center">
                            <span class="fw-semibold text-dark"
                                  style="min-width: 100px; display: inline-block;">
                                {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
                            </span>
                        </div>

                        <!-- Next Month -->
                        <button wire:click="nextMonth"
                                class="btn btn-light border-0 px-2 py-1 rounded-end"
                                title="Next Month">
                            <i class="fas fa-chevron-right text-muted"></i>
                        </button>
                    </div>
                </div>

                <div
                     class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 bg-white border-bottom">




                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold text-uppercase me-2">Filters:</span>

                        @php
                            $filters = [
                                'leave' => ['label' => 'Leaves', 'color' => 'success', 'icon' => 'fa-calendar-check'],
                                'birthday' => ['label' => 'Birthdays', 'color' => 'info', 'icon' => 'fa-birthday-cake'],
                                'uk_holiday' => ['label' => 'UK Holidays', 'color' => 'danger', 'icon' => 'fa-flag'],
                                'doc_expiry' => [
                                    'label' => 'Doc Expiry',
                                    'color' => 'warning',
                                    'icon' => 'fa-file-exclamation',
                                ],
                            ];
                        @endphp

                        @foreach ($filters as $key => $data)
                            <button wire:click="toggleCalendarFilter('{{ $key }}')"
                                    @class([
                                        'btn btn-sm d-flex align-items-center gap-2 px-3 border transition-all',
                                        'bg-light text-muted opacity-50' => !$calendarFilters[$key],
                                        "bg-{$data['color']}-subtle text-{$data['color']} border-{$data['color']}" => $calendarFilters[
                                            $key
                                        ],
                                    ])
                                    style="border-radius: 6px; font-weight: 500;">
                                <i class="fas {{ $data['icon'] }} small"></i>
                                {{ $data['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered calendar-table m-0">
                        <thead>
                            <tr class="text-center bg-light">
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Sun</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Mon</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Tue</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Wed</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Thu</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Fri</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $start = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
                                $end = $start->copy()->endOfMonth();

                                $days = [];
                                for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
                                    $days[] = $day->copy();
                                }

                                $firstDayOfWeek = $start->dayOfWeek;
                                if ($firstDayOfWeek > 0) {
                                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                                        array_unshift($days, null);
                                    }
                                }

                                $lastDayOfWeek = $end->dayOfWeek;
                                if ($lastDayOfWeek < 6) {
                                    for ($i = $lastDayOfWeek + 1; $i <= 6; $i++) {
                                        array_push($days, null);
                                    }
                                }
                            @endphp

                            @foreach (array_chunk($days, 7) as $week)
                                <tr>
                                    @foreach ($week as $date)
                                        <td class="calendar-day text-center p-2"
                                            style="vertical-align: top; height: 80px;">
                                            @if ($date)
                                                <div class="fw-bold">{{ $date->day }}</div>

                                                @if (isset($calendarEvents[$date->toDateString()]))
                                                    @foreach ($calendarEvents[$date->toDateString()] as $event)
                                                        @php
                                                            $badgeClass = match ($event['type']) {
                                                                'leave' => 'badge bg-success',
                                                                'birthday' => 'badge bg-info text-white',
                                                                'uk_holiday' => 'badge bg-danger text-white',
                                                                'doc_expiry' => 'badge bg-warning text-dark',
                                                                default => 'badge bg-secondary',
                                                            };
                                                        @endphp
                                                        <div class="event-bar d-block w-100 my-1 {{ $badgeClass }}"
                                                             style="font-size: 0.7rem;">
                                                            {{ $event['text'] }}
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="col-lg-4 col-md-12">
            <div class="custom-card mb-4">
                <h6 class="text-muted fw-bold mb-3">Recent Requests Status</h6>



                <ul class="nav nav-pills nav-pills-segmented mb-4 w-100 d-flex"
                    id="requestsTab"
                    role="tablist">
                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link active w-100"
                                id="leave-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#leave"
                                type="button"
                                role="tab">
                            Leave
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link w-100"
                                id="expense-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#expense"
                                type="button"
                                role="tab">
                            Expense
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link w-100"
                                id="payslip-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#payslip"
                                type="button"
                                role="tab">
                            Payslip
                        </button>
                    </li>
                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link w-100"
                                id="attendance-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#attendance"
                                type="button"
                                role="tab">
                            Attendance
                        </button>
                    </li>
                </ul>


                <div class="tab-content"
                     id="requestsTabContent">
                    <!-- Leave Requests -->
                    <div class="tab-pane fade show active"
                         id="leave"
                         role="tabpanel">
                        <ul class="list-group list-group-flush">
                            @forelse($leaveRequests as $request)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 flex-column">
                                    <div class="d-flex justify-content-between w-100 align-items-center">
                                        <span class="fw-semibold">{{ $request->leaveType->name ?? 'Leave' }}</span>
                                        <span
                                              class="badge rounded-pill
            {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-secondary') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($request->start_date)->format('M d, Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($request->end_date)->format('M d, Y') }}
                                    </small>

                                </li>
                                <hr class="my-2">

                            @empty
                                <li class="list-group-item border-0 px-0 text-center text-muted">
                                    No leave requests found.
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Expense Requests -->
                    <div class="tab-pane fade"
                         id="expense"
                         role="tabpanel">
                        <div class="d-flex flex-column gap-2 mt-2">
                            @forelse($expenses as $expense)
                                <div
                                     class="d-flex justify-content-between align-items-center p-2 bg-white border rounded-2">

                                    <!-- Left: Category and date -->
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $expense->category }}</h6>
                                        <small class="text-muted">Submitted:
                                            {{ $expense->created_at->format('d M, Y') }}</small>
                                    </div>

                                    <!-- Right: Amount and status -->
                                    <div class="text-end">
                                        <div class="fw-bold">€{{ number_format($expense->amount, 2) }}</div>
                                        <span class="badge bg-success-subtle text-success rounded-pill">
                                            Approved
                                        </span>
                                    </div>

                                </div>
                                <hr class="my-2">
                            @empty
                                <div class="text-center py-4 border rounded-2 bg-light">
                                    <i class="fas fa-folder-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No expenses found.</p>
                                </div>
                            @endforelse
                        </div>

                    </div>


                    <!-- Payslip Requests -->
                    <div class="tab-pane fade"
                         id="payslip"
                         role="tabpanel">
                        <ul class="list-group list-group-flush">
                            @forelse($payslipRequests as $request)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    {{ $request->period }}
                                    <span
                                          class="badge rounded-pill
                            {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-secondary') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </li>
                                <hr class="my-2">
                            @empty
                                <li class="list-group-item border-0 px-0 text-center text-muted">
                                    No payslip requests found.
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Attendance Requests -->
                    <div class="tab-pane fade"
                         id="attendance"
                         role="tabpanel">
                        <ul class="list-group list-group-flush">
                            @forelse($attendanceRequests as $attendance)
                                @foreach ($attendance->requests as $request)
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 flex-column">

                                        <!-- Top row: date and status -->
                                        <div class="d-flex justify-content-between w-100 align-items-center">
                                            <span class="fw-semibold">
                                                {{ \Carbon\Carbon::parse($request->clock_in)->format('M d, Y') }}
                                            </span>
                                            <span
                                                  class="badge rounded-pill
                        {{ $request->status == 'pending' ? 'bg-warning text-dark' : ($request->status == 'approved' ? 'bg-success' : 'bg-secondary') }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>

                                        <!-- Bottom row: Clock In / Clock Out with time and location if needed -->
                                        <div class="d-flex flex-column mt-1">
                                            <small class="text-muted">
                                                Clock In:
                                                {{ \Carbon\Carbon::parse($request->clock_in)->format('h:i A') }}
                                                @if ($request->clock_in_location)
                                                    | {{ $request->clock_in_location }}
                                                @endif
                                            </small>
                                            @if ($request->clock_out)
                                                <small class="text-muted">
                                                    Clock Out:
                                                    {{ \Carbon\Carbon::parse($request->clock_out)->format('h:i A') }}
                                                    @if ($request->clock_out_location)
                                                        | {{ $request->clock_out_location }}
                                                    @endif
                                                </small>
                                            @endif
                                        </div>
                                    </li>
                                    <hr class="my-2">
                                @endforeach
                            @empty
                                <li class="list-group-item border-0 px-0 text-center text-muted">
                                    No attendance requests found.
                                </li>
                            @endforelse
                        </ul>


                    </div>
                </div>
            </div>


            <div class="custom-card">
                <h6 class="text-muted fw-bold mb-3">My Documents & Payslips</h6>

                @if ($payslips->isEmpty() && $expiringDocs->isEmpty())
                    <div class="text-center py-5 text-muted"
                         style="font-size: 0.875rem;">
                        <i class="bi bi-folder-check fa-lg me-2"></i> No payslips or expiring documents available
                    </div>
                @else
                    {{-- Payslips --}}
                    @forelse($payslips as $payslip)
                        @php
                            $period = \Carbon\Carbon::parse($payslip->period)->format('F Y');
                            $fileUrl = asset('storage/' . $payslip->file_path);
                        @endphp

                        <a href="{{ $fileUrl }}"
                           download
                           class="alert alert-light border d-flex justify-content-between align-items-center mb-2 text-decoration-none text-dark hover-shadow transition-all">
                            <span>{{ $period }} Payslip</span>
                            <i class="bi bi-download"></i>
                        </a>
                    @empty
                        {{-- Optional: message if only payslips empty --}}
                    @endforelse

                    {{-- Expiring Documents --}}
                    @forelse ($expiringDocs as $doc)
                        @php
                            $totalDays = 60;
                            $remainingDays = max(floor(\Carbon\Carbon::now()->diffInDays($doc->expires_at)), 0);
                            $progress = (($totalDays - $remainingDays) / $totalDays) * 100;
                            $typeName = $doc->documentType->name ?? ($doc->name ?? 'Document');
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                       class="bi bi-exclamation-triangle-fill {{ $remainingDays == 0 ? 'text-danger' : 'text-warning' }}"></i>
                                    <span
                                          class="{{ $remainingDays == 0 ? 'text-danger fw-bold' : 'text-warning fw-bold' }}">
                                        {{ $typeName }}
                                        @if ($remainingDays > 0)
                                            - {{ $remainingDays }} {{ Str::plural('day', $remainingDays) }}
                                        @else
                                            - Expired
                                        @endif
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($doc->expires_at)->format('M d, Y') }}
                                </small>
                            </div>

                            <div class="progress"
                                 style="height: 6px; border-radius: 3px;">
                                <div class="progress-bar {{ $remainingDays == 0 ? 'bg-danger' : 'bg-warning' }}"
                                     style="width: {{ min(max($progress, 0), 100) }}%;"></div>
                            </div>
                        </div>
                    @empty
                    @endforelse
                @endif
            </div>


        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<script>
    function renderStatusChart() {
        const canvas = document.getElementById('statusChart');
        if (!canvas) return;

        const data = [
            {{ $liveStatus['present'] ?? 0 }},
            {{ $liveStatus['leave'] ?? 0 }},
            {{ $liveStatus['absent'] ?? 0 }}
        ];

        // if all zeros, show a tiny dummy value to force render
        const allZero = data.every(v => v === 0);
        const chartData = allZero ? [0, 0, 0] : data;

        new Chart(canvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Present', 'On Leave', 'Absent'],
                datasets: [{
                    data: chartData,
                    backgroundColor: ['#28a745', '#007bff', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                cutout: '65%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', renderStatusChart);
</script>

<script>
    function updateClock() {
        const now = new Date();

        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const period = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12 || 12;

        document.getElementById('liveTime').innerText = `${hours}:${minutes}`;
        document.getElementById('livePeriod').innerText = period;
    }

    updateClock();
    setInterval(updateClock, 1000);
</script>



<script>
    function renderLeaveCharts() {
        const wrapper = document.getElementById('leaveChartsWrapper');
        const hiddenInput = document.getElementById('leaveBalancesData');

        if (!wrapper || !hiddenInput) return;

        wrapper.innerHTML = ''; // clear previous charts

        let leaveBalances = [];
        try {
            leaveBalances = JSON.parse(hiddenInput.value);
        } catch (e) {
            console.error('Invalid leave balances JSON', e);
            return;
        }

        leaveBalances.forEach((balance, index) => {
            // Container for chart + labels
            const container = document.createElement('div');
            container.className = 'text-center d-flex flex-column align-items-center';
            container.style.width = '120px';
            container.style.margin = '0 10px';

            const used = parseFloat(balance.used) || 0;
            const remaining = parseFloat(balance.remaining) || 0;

            if (used === 0 && remaining === 0) {
                // Professional "No leave found" placeholder
                const placeholder = document.createElement('div');
                placeholder.style.width = '120px';
                placeholder.style.height = '120px';
                placeholder.style.borderRadius = '50%';
                placeholder.style.backgroundColor = '#f0f0f0';
                placeholder.style.display = 'flex';
                placeholder.style.alignItems = 'center';
                placeholder.style.justifyContent = 'center';
                placeholder.style.fontSize = '0.85rem';
                placeholder.style.color = '#6c757d';
                placeholder.style.textAlign = 'center';
                placeholder.innerText = 'No leave found';
                container.appendChild(placeholder);
            } else {
                // Canvas for actual chart
                const canvas = document.createElement('canvas');
                canvas.id = 'leaveChart' + index;
                canvas.width = 120;
                canvas.height = 120;
                container.appendChild(canvas);

                // Render doughnut chart
                new Chart(canvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Used', 'Remaining'],
                        datasets: [{
                            data: [used, remaining],
                            backgroundColor: ['#dc3545', '#28a745'],
                            borderWidth: 4,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        cutout: '60%',
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        }
                    }
                });

                // Remaining days text
                const subtext = document.createElement('small');
                subtext.className = 'text-muted text-center';
                subtext.style.whiteSpace = 'nowrap';
                subtext.innerText = `${balance.remaining} Days Remaining`;
                container.appendChild(subtext);
            }

            // Name label (always shown)
            const label = document.createElement('p');
            label.className = 'small mb-0 mt-2 text-center fw-bold';
            label.innerText = balance.name;
            container.appendChild(label);

            wrapper.appendChild(container);
        });
    }

    // Initial render
    document.addEventListener('DOMContentLoaded', renderLeaveCharts);

    // Livewire reactivity
    if (window.Livewire) {
        Livewire.hook('message.processed', renderLeaveCharts);
    }
</script>
