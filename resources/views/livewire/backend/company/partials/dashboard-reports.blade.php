@push('styles')
    <link href="{{ asset('assets/css/company-dashboard.css') }}"
          rel="stylesheet" />
@endpush
@php
    $authUser = app('authUser');
    $companySubDomain =
        $authUser->user_type === 'company' ? $authUser->company->sub_domain : $authUser->employee->company->sub_domain;
@endphp

<div class="container-fluid py-4"
     wire:poll.60s>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-sky">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Today's Absent</small>
                    <i class="fas fa-user-times text-danger"></i>
                </div>
                <h3>{{ $todayAbsent ?? 0 }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">On Leave Today</small>
                    <i class="fas fa-plane-departure text-success"></i>
                </div>
                <h3>{{ $onLeaveToday ?? 0 }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-pink">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Upcoming Holiday</small>
                    <i class="fas fa-calendar-day text-pink"></i>
                </div>
                <h5 class="mb-0">{{ $upcomingHoliday ?? null }}</h5>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Pending Leave Requests</small>
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <h3>{{ $pendingRequests ?? 0 }}</h3>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-section mb-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="calendar-nav d-flex align-items-center gap-1 bg-light rounded-pill p-1 border">


                        <button wire:click="previousMonth"
                                class="btn btn-link ...">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <span class="fw-bold">
                            {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
                        </span>

                        <button wire:click="nextMonth"
                                class="btn btn-link ...">
                            <i class="fas fa-chevron-right"></i>
                        </button>


                    </div>

                    <div class="d-flex gap-2">
                        <span wire:click="toggleCalendarFilter('leave')"
                              class="badge badge-green px-3 py-2 clickable
          {{ !$calendarFilters['leave'] ? 'opacity-50 text-decoration-line-through' : '' }}">
                            Leaves
                        </span>

                        <span wire:click="toggleCalendarFilter('birthday')"
                              class="badge badge-pink px-3 py-2 clickable
          {{ !$calendarFilters['birthday'] ? 'opacity-50 text-decoration-line-through' : '' }}">
                            Birthdays
                        </span>

                        <span wire:click="toggleCalendarFilter('uk_holiday')"
                              class="badge badge-danger px-3 py-2 clickable
          {{ !$calendarFilters['uk_holiday'] ? 'opacity-50 text-decoration-line-through' : '' }}">
                            UK Holidays
                        </span>

                        <span wire:click="toggleCalendarFilter('doc_expiry')"
                              class="badge badge-orange px-3 py-2 clickable
          {{ !$calendarFilters['doc_expiry'] ? 'opacity-50 text-decoration-line-through' : '' }}">
                            Doc Expiry
                        </span>
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
                            @endphp
                            @foreach (array_chunk($days, 7) as $week)
                                <tr>
                                    @foreach ($week as $date)
                                        <td class="calendar-day">
                                            {{ $date->day }}
                                            @if (isset($calendarEvents[$date->toDateString()]))
                                                @foreach ($calendarEvents[$date->toDateString()] as $event)
                                                    @php
                                                        $badgeClass = match ($event['type']) {
                                                            'leave' => 'badge-green',
                                                            'birthday' => 'badge-pink',
                                                            'uk_holiday' => 'bg-danger text-white',
                                                            'uk_weekend' => 'bg-danger text-white',
                                                            'doc_expiry' => 'badge-orange',
                                                            default => 'badge-secondary',
                                                        };
                                                    @endphp
                                                    <div class="event-bar {{ $badgeClass }}">
                                                        {{ $event['text'] }}
                                                    </div>
                                                @endforeach
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>



            <div class="dashboard-section">
                <h5 class="fw-bold mb-5">Attendance Anomalies</h5>
                <table class="table align-middle">
                    <tbody>
                        @forelse($attendanceAnomalies as $row)
                            <tr>
                                <td><strong>{{ $row['name'] }}</strong></td>
                                <td>
                                    <span
                                          class="badge bg-{{ $row['badge'] }}-subtle text-{{ $row['badge'] }} rounded-pill px-3">
                                        {{ $row['type'] }}
                                    </span>
                                </td>
                                <td class="fw-bold text-end">{{ $row['time'] }}</td>
                            </tr>
                        @empty
                            <div class="text-center py-5 rounded-4 shadow-sm border-0"
                                 style="background: #ffffff;">

                                <div class="mb-3">
                                    <i class="fas fa-user-shield text-light-emphasis"
                                       style="font-size: 3rem; opacity: 0.3;"></i>
                                </div>
                                <h6 class="text-muted fw-normal">No anomalies found today</h6>
                                <small class="text-secondary">Everything looks good! No unusual activity detected in the
                                    last 24 hours.</small>
                            </div>
                        @endforelse
                    </tbody>
                </table>
            </div>


            <div class="dashboard-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"
                        style="font-size: 1.1rem; color: #2d3748;">
                        Recent Expenses
                    </h5>
                    @if (!$expenses->isEmpty())
                        <a href="{{ route('company.dashboard.reports.expenses', ['company' => app('authUser')->company->sub_domain]) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           style="font-size: 0.8rem;">View All</a>
                    @endif
                </div>

                @if ($expenses->isEmpty())
                    <div class="text-center py-5 rounded-4 shadow-sm border-0"
                         style="background: #ffffff;">
                        <div class="mb-3">
                            <i class="fas fa-receipt text-light-emphasis"
                               style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                        <h6 class="text-muted fw-normal">No recent expenses found</h6>
                        <small class="text-secondary">Expenses submitted by employees will appear here.</small>
                    </div>
                @else
                    <div class="list-group list-group-flush"
                         style="max-height: 400px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin;">
                        @foreach ($expenses as $expense)
                            @php
                                $route = route('company.dashboard.reports.expenses', [
                                    'company' => $companySubDomain,
                                    'id' => $expense->id ?? null,
                                ]);

                            @endphp


                            <div class="list-group-item mb-3 p-3 border-0 rounded-4 shadow-sm"
                                 style="background: #ffffff; border: 1px solid #eef2f7; cursor: pointer; transition: all 0.25s ease-in-out;"
                                 onclick="window.location='{{ $route }}'"
                                 onmouseover="this.style.backgroundColor='#f1f7ff'; this.style.borderColor='#cfe2ff'; this.style.transform='translateY(-1px)';"
                                 onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#eef2f7'; this.style.transform='translateY(0)';">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                                             style="width: 45px; height: 45px; background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2;">
                                            <i class="fas fa-hand-holding-usd fs-5"></i>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark text-truncate"
                                                    style="font-size: 0.95rem;">
                                                    {{ $expense->user->full_name ?? 'N/A' }}
                                                </h6>
                                                <small class="text-muted"
                                                       style="font-size: 0.8rem;">
                                                    <i class="fas fa-tag me-1"
                                                       style="font-size: 0.7rem;"></i>
                                                    {{ ucfirst($expense->category) }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge rounded-pill bg-danger text-white px-3 py-2 shadow-sm"
                                                      style="font-size: 0.85rem; font-weight: 700;">
                                                    €{{ number_format($expense->amount, 2) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center"
                                             style="border-color: #fceaea !important;">
                                            <small class="text-muted"
                                                   style="font-size: 0.75rem;">
                                                <i class="far fa-clock me-1"></i>
                                                {{ \Carbon\Carbon::parse($expense->submitted_at)->format('M d, Y | h:i A') }}
                                            </small>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="dashboard-section mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"
                        style="font-size: 1.1rem; color: #2d3748;">
                        Recent e-Sign Documents
                    </h5>
                    @if (!$recentDocuments->isEmpty())
                        <a href="{{ route('company.dashboard.document-manage.index', ['company' => app('authUser')->company->sub_domain]) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           style="font-size: 0.8rem;">View All</a>
                    @endif
                </div>



                @if ($recentDocuments->isEmpty())
                    <div class="text-center py-5 rounded-4 shadow-sm border-0"
                         style="background: #ffffff;">
                        <div class="mb-3">
                            <i class="fas fa-folder-open text-light-emphasis"
                               style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                        <h6 class="text-muted fw-normal">No recent documents found</h6>
                        <small class="text-secondary">All clear! No new e-Sign Docs to review.</small>
                    </div>
                @else
                    <div class="list-group list-group-flush"
                         style="max-height: 400px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin;">
                        @foreach ($recentDocuments as $doc)
                            @php
                                $route = route('company.dashboard.document-manage.index', [
                                    'company' => $companySubDomain,
                                    'id' => $doc->id ?? null,
                                ]);

                            @endphp
                            <div onclick="window.location='{{ $route }}'"
                                 class="list-group-item mb-3 p-3 border-0 rounded-4 shadow-sm"
                                 style="background: #ffffff; border: 1px solid #eef2f7; cursor: pointer; transition: all 0.25s ease-in-out;"
                                 onclick="window.location='{{ $route }}'"
                                 onmouseover="this.style.backgroundColor='#f1f7ff'; this.style.borderColor='#cfe2ff'; this.style.transform='translateY(-1px)';"
                                 onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#eef2f7'; this.style.transform='translateY(0)';">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                                             style="width: 45px; height: 45px; background: #f0f4f8; color: #4a5568; border: 1px solid #e2e8f0;">
                                            <i class="fas fa-file-pdf fs-5"></i>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1 fw-bold text-dark text-truncate"
                                                style="font-size: 0.95rem;">
                                                {{ $doc->name }}
                                            </h6>
                                            <span class="text-muted fw-medium"
                                                  style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($doc->created_at)->format('h:i A') }}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle"
                                                  style="font-size: 0.65rem; padding: 4px 10px;">
                                                <i class="fas fa-check-circle me-1"></i>{{ ucfirst($doc->status) }}
                                            </span>

                                            @if ($doc->expires_at)
                                                <span class="text-warning fw-medium"
                                                      style="font-size: 0.75rem;">
                                                    <i class="fas fa-hourglass-half me-1"></i>Exp:
                                                    {{ \Carbon\Carbon::parse($doc->expires_at)->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted"
                                                      style="font-size: 0.75rem;">
                                                    <i class="fas fa-infinity me-1"></i>No expiry
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>


                            </div>
                        @endforeach
                    </div>
                @endif
            </div>


        </div>

        <div class="col-lg-4">
            <div class="dashboard-section mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-secondary mb-0">Live Office Status</h6>
                    <select wire:change="handleFilter($event.target.value)"
                            class="form-select form-select-sm"
                            style="width: 90px;"> <!-- adjust this value as needed -->
                        <option value="day"
                                @if ($statusFilter == 'day') selected @endif>Today</option>
                        <option value="month"
                                @if ($statusFilter == 'month') selected @endif>Month</option>
                        <option value="year"
                                @if ($statusFilter == 'year') selected @endif>Year</option>
                    </select>
                </div>



                <div class="d-flex align-items-center ">

                    @php
                        $hasData =
                            ($liveStatus['present'] ?? 0) > 0 ||
                            ($liveStatus['leave'] ?? 0) > 0 ||
                            ($liveStatus['absent'] ?? 0) > 0;
                    @endphp

                    @if ($hasData)
                        <div style="width: 145px; height: 130px; position: relative;"
                             class="me-4">
                            <canvas id="statusChart"></canvas>
                        </div>
                    @else
                        <div style="width: 145px; height: 130px; position: relative;"
                             class="me-4 d-flex align-items-center justify-content-center bg-light rounded">
                            <span class="text-muted fw-bold small text-center">
                                <i class="fas fa-info-circle me-1"></i> No data available
                            </span>
                        </div>
                    @endif



                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="dot bg-present"></span>
                            <span>Present: <strong>{{ $liveStatus['present'] ?? 0 }}</strong></span>
                        </div>
                        <div class="legend-item">
                            <span class="dot bg-leave"></span>
                            <span>On Leave: <strong>{{ $liveStatus['leave'] ?? 0 }}</strong></span>
                        </div>
                        <div class="legend-item">
                            <span class="dot bg-absent"></span>
                            <span>Absent: <strong>{{ $liveStatus['absent'] ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-section mb-4">
                <h5 class="fw-bold mb-3">Request Center</h5>



                <ul class="nav border-bottom d-flex"
                    id="requestTabs"
                    role="tablist"
                    style="border-color: #e5e7eb !important; width: 100%;">

                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link active position-relative w-100 py-3 border-0 bg-transparent fw-semibold text-muted"
                                id="leave-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#leave-requests"
                                type="button"
                                role="tab">
                            Leave
                        </button>
                    </li>

                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link position-relative w-100 py-3 border-0 bg-transparent fw-medium text-muted"
                                id="attendance-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#attendance-requests"
                                type="button"
                                role="tab">
                            Attendance
                        </button>
                    </li>

                    <li class="nav-item flex-fill text-center"
                        role="presentation">
                        <button class="nav-link position-relative w-100 py-3 border-0 bg-transparent fw-medium text-muted"
                                id="payslip-tab"
                                data-bs-toggle="pill"
                                data-bs-target="#payslip-requests"
                                type="button"
                                role="tab">
                            Payslip
                        </button>
                    </li>
                </ul>


                <div class="tab-content"
                     id="requestTabsContent">
                    <div class="tab-pane fade show active"
                         id="leave-requests"
                         role="tabpanel">
                        {{-- Leave Requests Section --}}
                        <div class="mt-4">

                            @if ($leaveRequests->isEmpty())
                                <div class="list-group list-group-flush mb-2">
                                    <li class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-check-circle me-1"></i> No leave requests found.
                                    </li>
                                </div>
                            @else
                                <ul class="list-group list-group-flush mb-2"
                                    style="max-height: 400px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin;">

                                    @foreach ($leaveRequests as $leave)
                                        @php
                                            $duration =
                                                \Carbon\Carbon::parse($leave->start_date)->diffInDays(
                                                    \Carbon\Carbon::parse($leave->end_date),
                                                ) + 1;

                                            $route = route('company.dashboard.leaves.index', [
                                                'company' => $companySubDomain,
                                                'leave' => $leave->id ?? null,
                                            ]);
                                        @endphp

                                        <li class="list-group-item mb-2 p-2 border-0 rounded-4 shadow-sm leave-item"
                                            style="background: #ffffff; border: 1px solid #eef2f7; cursor: pointer; transition: all 0.25s ease-in-out;"
                                            onclick="window.location='{{ $route }}'"
                                            onmouseover="this.style.backgroundColor='#f1f7ff'; this.style.borderColor='#cfe2ff'; this.style.transform='translateY(-1px)';"
                                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#eef2f7'; this.style.transform='translateY(0)';">

                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <!-- Left Side: Name + Date -->
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark"
                                                        style="font-size: 0.9rem;">
                                                        {{ $leave->user->full_name }}
                                                    </h6>
                                                    <small class="text-muted"
                                                           style="font-size: 0.75rem;">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                                    </small>
                                                </div>

                                                <!-- Right Side: Type + Emoji + Days below -->
                                                <div class="d-flex flex-column align-items-end text-end">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span style="font-size: 1rem;">{!! $leave->leaveType->emoji !!}</span>
                                                        <span class="fw-medium text-dark small">
                                                            {{ $leave->leaveType->name ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <small class="text-muted"
                                                           style="font-size: 0.7rem;">
                                                        ({{ $duration }} {{ Str::plural('Day', $duration) }})
                                                    </small>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>




                    <div class="tab-pane fade"
                         id="attendance-requests"
                         role="tabpanel">
                        {{-- Attendance Requests Section --}}
                        <div class="mt-4">


                            @if ($attendanceRequests->isEmpty())
                                <div class="list-group list-group-flush mb-2">
                                    <li class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-check-circle me-1"></i> No attendance requests found.
                                    </li>
                                </div>
                            @else
                                <ul class="list-group list-group-flush mb-2"
                                    style="max-height: 400px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin;">

                                    @foreach ($attendanceRequests as $record)
                                        @php
                                            $pendingRequests = $record->requests->where('status', 'pending');
                                        @endphp

                                        @foreach ($pendingRequests as $req)
                                            @php
                                                $route = route('company.dashboard.timesheet.index', [
                                                    'company' => $companySubDomain,
                                                    'id' => $req->id ?? null,
                                                ]);

                                                $period =
                                                    $req->type === 'late_clock_in'
                                                        ? \Carbon\Carbon::parse($record->clock_in)->format('h:i A')
                                                        : \Carbon\Carbon::parse($record->clock_out)->format('h:i A');
                                            @endphp

                                            <li class="list-group-item mb-2 p-2 border-0 rounded-4 shadow-sm"
                                                style="background: #ffffff; border: 1px solid #eef2f7; cursor: pointer; transition: all 0.25s ease-in-out;"
                                                onclick="window.location='{{ $route }}'"
                                                onmouseover="this.style.backgroundColor='#f4faff'; this.style.borderColor='#ffeeba'; this.style.transform='translateY(-1px)';"
                                                onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#eef2f7'; this.style.transform='translateY(0)';">

                                                <div class="d-flex justify-content-between align-items-center">
                                                    <!-- Left Side: Employee Name -->
                                                    <div>
                                                        <h6 class="mb-0 fw-bold text-dark"
                                                            style="font-size: 0.9rem;">
                                                            {{ $record->user->full_name }}
                                                        </h6>
                                                        <small class="text-muted"
                                                               style="font-size: 0.75rem;">
                                                            {{ ucfirst(str_replace('_', ' ', $req->type)) }} Request
                                                        </small>
                                                    </div>

                                                    <!-- Right Side: Period -->
                                                    <small class="text-muted"
                                                           style="font-size: 0.75rem;">
                                                        <i class="fas fa-calendar-check me-1 text-success"></i>
                                                        {{ $period }}
                                                    </small>
                                                </div>
                                            </li>
                                        @endforeach
                                    @endforeach

                                </ul>
                            @endif
                        </div>

                    </div>


                    <div class="tab-pane fade"
                         id="payslip-requests"
                         role="tabpanel">
                        <div class="mt-4">

                            @if ($payslipRequests->isEmpty())
                                <div class="list-group list-group-flush mb-2">
                                    <li class="list-group-item text-center text-muted py-3">
                                        <i class="fas fa-check-circle me-1"></i> No payslip requests found.
                                    </li>
                                </div>
                            @else
                                <ul class="list-group list-group-flush mb-2"
                                    style="max-height: 400px; overflow-y: auto; padding-right: 5px; scrollbar-width: thin;">

                                    @foreach ($payslipRequests as $request)
                                        @php
                                            $route = route('company.dashboard.reports.payslips', [
                                                'company' => $companySubDomain,
                                                'id' => $request->id,
                                            ]);

                                            $period = \Carbon\Carbon::create($request->year, $request->month)->format(
                                                'F Y',
                                            );
                                        @endphp

                                        <li class="list-group-item mb-2 p-2 border-0 rounded-4 shadow-sm"
                                            style="background: #ffffff; border: 1px solid #eef2f7; cursor: pointer; transition: all 0.25s ease-in-out;"
                                            onclick="window.location='{{ $route }}'"
                                            onmouseover="this.style.backgroundColor='#f4faff'; this.style.borderColor='#b3d7ff'; this.style.transform='translateY(-1px)';"
                                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.borderColor='#eef2f7'; this.style.transform='translateY(0)';">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <!-- Left: User Name + Requested Date -->
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark"
                                                        style="font-size: 0.9rem;">
                                                        {{ $request->user->full_name }}
                                                    </h6>
                                                    <small class="text-muted"
                                                           style="font-size: 0.75rem;">
                                                        <i class="fas fa-history me-1"></i>
                                                        Requested:
                                                        {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}
                                                    </small>
                                                </div>

                                                <!-- Right: Period -->
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="me-1 text-success">
                                                        <i class="fas fa-file-invoice-dollar"
                                                           style="font-size: 0.9rem;"></i>
                                                    </div>
                                                    <small class="text-dark fw-bold"
                                                           style="font-size: 0.75rem;">
                                                        {{ $period }}
                                                    </small>
                                                </div>
                                            </div>

                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                    </div>



                </div>
            </div>




            <div class="dashboard-section">
                <h5 class="fw-bold mb-3">Documents Expiring Soon (60 Days)</h5>

                @forelse ($expiringDocs as $doc)
                    @php
                        $totalDays = 60;
                        $remainingDays = max(floor(\Carbon\Carbon::now()->diffInDays($doc->expires_at)), 0);
                        $progress = (($totalDays - $remainingDays) / $totalDays) * 100;
                    @endphp

                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-bold">
                                {{ $doc->employee->f_name }} - {{ $doc->documentType->name ?? 'eSigned Docs' }}
                            </span>

                            <span class="fw-bold {{ $remainingDays == 0 ? 'text-danger' : 'text-warning' }}">
                                @if ($remainingDays == 0)
                                    Expired
                                @else
                                    {{ $remainingDays }} days
                                @endif
                            </span>

                        </div>

                        <div class="progress"
                             style="height: 6px;">
                            <div class="progress-bar bg-danger"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-shield mb-2 d-block"
                           style="font-size: 28px;"></i>
                        <span class="fw-bold fst-italic">
                            No documents expiring.
                        </span>
                    </div>
                @endforelse
            </div>


            <div class="dashboard-section shadow-sm border-0 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">Recent Employees</h5>
                        <small class="text-muted">Total Employees: <span
                                  class="fw-bold text-primary">{{ $totalEmployees ?? 0 }}</span></small>
                    </div>
                    <a href="{{ route('company.dashboard.employees.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        View All
                    </a>

                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead class="table-light">
                            <tr class="text-muted smaller fw-bold text-uppercase">
                                <th class="border-0 ps-3">Name</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Mobile</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEmployees as $emp)
                                <tr class="clickable-row cursor-pointer"
                                    data-href="{{ route('company.dashboard.employees.details', [
                                        'company' => app('authUser')->company->sub_domain,
                                        'employee' => $emp->id,
                                    ]) }}">
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold me-2"
                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ strtoupper(substr($emp->f_name, 0, 1) . substr($emp->l_name, 0, 1)) }}
                                            </div>
                                            <span class="fw-bold">{{ $emp->f_name }} {{ $emp->l_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $emp->email }}</td>
                                    <td class="text-muted small">{{ $emp->phone_no ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                              class="badge {{ $emp->is_active ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} rounded-pill px-3">
                                            {{ $emp->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted py-3">
                                        No employees found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
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

    if (window.Livewire) {
        Livewire.hook('message.processed', renderStatusChart);
    }
</script>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('tr.clickable-row').forEach(function(row) {
            row.addEventListener('click', function() {
                window.location.href = this.dataset.href;
            });
        });
    });
</script>
