@push('styles')
    <link href="{{ asset('assets/css/timesheet.css') }}"
          rel="stylesheet">
    <link href="{{ asset('assets/css/company-schedule.css') }}"
          rel="stylesheet" />
@endpush

@php
    $highlightId = request('id');
@endphp


@php
    use Carbon\Carbon;
@endphp


<div class="container-fluid dashboard-container">
    <div class="row h-100">

        {{-- LEFT PANEL --}}
        <div class="col-md-5 left-panel py-5 px-4">

            {{-- CURRENT TIME --}}
            <div class="time-display text-white mb-5">
                <h1 class="current-time text-white"
                    id="current-time"
                    wire:ignore>10:00:00 AM</h1>
                <div class="current-date"
                     id="current-date"
                     wire:ignore>Monday, Oct 26, 2024</div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="action-card mb-3">
                <a href="#"
                   class="action-link"
                   data-bs-toggle="modal"
                   data-bs-target="#manualEntryModal">
                    <i class="fas fa-user-clock"></i>
                    <span>Submit Manual Entry</span>
                </a>
            </div>

            <div class="action-card">
                <a href="#"
                   class="action-link"
                   data-bs-toggle="modal"
                   data-bs-target="#timeSheetModal">
                    <i class="fas fa-list-alt"></i>
                    <span>View Time Records</span>
                </a>
            </div>

        </div>

        {{-- RIGHT PANEL --}}
        <div class="col-md-7 right-panel py-5 px-4">

            {{-- FILTERS --}}
            <div class="filter-section mb-5 text-white">
                <div class="row g-2">

                    <div class="col position-relative dropdown"
                         wire:ignore>

                        <!-- Dropdown Button -->
                        <button class="btn btn-secondary shadow-none w-100 min-width-fit d-flex align-items-center justify-content-between dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            Filter by Employees
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="dropdown-menu w-100 p-2"
                             data-bs-auto-close="outside">

                            <!-- All Employees -->
                            <div class="form-check mb-2 border-bottom pb-2">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model.live="selectAllUsers"
                                       id="emp-all">
                                <label class="form-check-label fw-semibold"
                                       for="emp-all">
                                    All Employees
                                </label>
                            </div>

                            <div class="fw-semibold text-muted small mb-2">
                                Select Employees
                            </div>

                            <!-- Employees List -->
                            @foreach ($employees as $emp)
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           value="{{ $emp->user_id }}"
                                           wire:model.live="filterUsers"
                                           id="emp{{ $emp->user_id }}">
                                    <label class="form-check-label"
                                           for="emp{{ $emp->user_id }}">
                                        {{ $emp->full_name }}
                                    </label>
                                </div>
                            @endforeach

                        </div>
                    </div>





                </div>

                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <input type="date"
                               wire:model.live="dateFrom"
                               class="form-control">
                    </div>
                    <div class="col-md-6">
                        <input type="date"
                               wire:model.live="dateTo"
                               class="form-control">
                    </div>
                </div>
            </div>

            {{-- PENDING REQUESTS --}}
            <div class="pending-requests-section mb-4 mt-3">
                <h3 class="requests-header text-white mb-3">
                    Pending Requests ({{ $records->sum(fn($r) => $r->requests->where('status', 'pending')->count()) }})
                </h3>

                @foreach ($records as $record)
                    @php
                        $pendingRequests = $record->requests->where('status', 'pending');
                    @endphp

                    @foreach ($pendingRequests as $req)
                        <div class="request-item mb-2 d-flex justify-content-between align-items-center p-3 border"
                             wire:click="viewRequest({{ $req->id }})"
                             style="
                    cursor: pointer;
                    border-radius: 8px;
                    background: {{ $highlightId === $req->id ? '#fff3cd' : '#ffffff10' }};
                    transition: all 0.3s ease;
                 "
                             onmouseover="this.style.background='{{ $highlightId === $req->id ? '#fff3cd' : '#ffffff20' }}'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)';"
                             onmouseout="this.style.background='{{ $highlightId === $req->id ? '#fff3cd' : '#ffffff10' }}'; this.style.boxShadow='none';">

                            <div>
                                <p class="mb-1 fw-bold text-white">{{ $record->user->full_name }}</p>
                                <small class="text-light">
                                    {{ ucfirst(str_replace('_', ' ', $req->type)) }} - Pending
                                </small>
                            </div>

                            <div>
                                <i class="fas fa-eye text-primary fs-5"></i>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>





        </div>

    </div>

    <div wire:ignore.self
         class="modal fade"
         id="timeSheetModal"
         tabindex="-1"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">

        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Time Sheet</h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- date navigator --}}
                <div class="bg-white d-flex justify-content-center align-items-center py-2 ">
                    @include('livewire.backend.company.timesheet.partials.header_nav', [
                        'startDate' => $displayDateRange,
                        'endDate' => '',
                    ])
                </div>

                {{-- ================= MONTHLY VIEW ================= --}}
                @if ($viewMode === 'monthly')
                    <div class="table-responsive">
                        <table class="table table-bordered schedule-table m-0">
                            <thead>
                                <tr>
                                    <th>Mon</th>
                                    <th>Tue</th>
                                    <th>Wed</th>
                                    <th>Thu</th>
                                    <th>Fri</th>
                                    <th>Sat</th>
                                    <th>Sun</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($weeks as $week)
                                    <tr>
                                        @foreach ($week as $day)
                                            @php
                                                $isCurrent = $day->month == $this->startDate->month;
                                                $dateKey = $day->format('Y-m-d');
                                                $hasAtt = !empty($this->attendanceCalendar[$dateKey]);
                                                $employees = $this->shiftMap[$dateKey] ?? [];

                                            @endphp

                                            <td class="schedule-cell month-cell {{ $day->equalTo(today()) ? 'bg-primary-light-cell' : '' }}"
                                                style="height:120px; width:14.28%; position:relative; vertical-align:top; padding:4px;">

                                                {{-- Date number --}}
                                                <span class="date-number fw-bold">{{ $day->day }}</span>
                                                @php
                                                    $isCurrentMonth = $day->month === $this->startDate->month;
                                                    $isPastOrToday = $day->lessThanOrEqualTo(today());

                                                    $attendances = collect($this->attendanceCalendar[$dateKey] ?? []);
                                                    $totalAtt = $attendances->count();
                                                    $visibleAtt = $attendances->take(2); // সর্বোচ্চ ২ টা দেখাবে
                                                    $moreCount = $totalAtt - 2;

                                                    $shiftEmployees = $this->shiftMap[$dateKey] ?? [];
                                                    $attendanceUserIds = $attendances->pluck('user_id')->unique();
                                                    $absentCount = collect($shiftEmployees)
                                                        ->diff($attendanceUserIds)
                                                        ->count();
                                                @endphp

                                                {{-- Attendance Section --}}
                                                @if ($isCurrentMonth && $totalAtt > 0)
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach ($visibleAtt as $att)
                                                            <div class="attendance-wrapper"
                                                                 style="transform: scale(0.95); margin-left: -5px; margin-right: -5px;">
                                                                @include(
                                                                    'livewire.backend.company.timesheet.partials._attendance-block',
                                                                    ['att' => $att]
                                                                )
                                                            </div>
                                                        @endforeach



                                                        {{-- More Indicator --}}
                                                        @if ($moreCount > 0)
                                                            <div class="text-center py-1 rounded"
                                                                 style="font-size: 10px; cursor: pointer; background: #f8f9fa; color: #007bff; border: 1px dashed #007bff; font-weight: 600;"
                                                                 wire:click="openAttendanceMoreModal('{{ $dateKey }}')">
                                                                +{{ $moreCount }} others
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif


                                                {{-- Absent indicator --}}
                                                @if ($isCurrentMonth && $isPastOrToday && $absentCount > 0)
                                                    <div class="position-absolute bottom-0 start-0 w-100 px-1 pb-1">
                                                        <div class="badge bg-danger d-flex align-items-center justify-content-center gap-1 w-100 shadow-sm"
                                                             style="font-size: 10px; height: 22px; cursor: pointer; border-radius: 4px;"
                                                             wire:click="showAbsentDetails('{{ $dateKey }}')">
                                                            <i class="fas fa-user-times"
                                                               style="font-size: 9px;"></i>
                                                            <span>{{ $absentCount }} Absent</span>
                                                        </div>
                                                    </div>
                                                @endif


                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- ================= WEEKLY VIEW ================= --}}
                @else
                    <div class="d-flex">


                        <div class="mt-n3">
                            <div class="schedule-sidebar p-2 border-end"
                                 style="width: clamp(250px, 18vw, 280px); flex-shrink: 0;">
                                <input type="text"
                                       class="form-control form-control-sm mb-3"
                                       placeholder="Search employees..."
                                       wire:model.live="employeeSearch">

                                <h6 class="fw-bold text-muted text-uppercase mb-2">Employees</h6>

                                <div class="employee-list ">
                                    @if ($employees->isEmpty())
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                                            <div>No employees found.</div>
                                        </div>
                                    @else
                                        @foreach ($employees as $emp)
                                            {{-- single loop --}}
                                            <div class="d-flex align-items-center py-4 px-2 employee-row shadow-sm rounded mb-2"
                                                 title="{{ $emp->full_name }}">

                                                {{-- avatar (optional) --}}
                                                <div class="position-relative me-3">
                                                    <img src="{{ $emp->avatar_url ?? asset('assets/img/default-avatar.png') }}"
                                                         alt="{{ $emp->full_name }}"
                                                         class="rounded-circle employee-avatar">
                                                </div>

                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold">{{ $emp->full_name }}</span>
                                                    <small
                                                           class="text-muted">{{ ucfirst($emp->role ?? 'Employee') }}</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- grid --}}
                        <div class="flex-grow-1 mt-4 table-responsive">
                            <table class="table table-bordered schedule-table m-0">
                                <thead>
                                    <tr class="text-center">
                                        @foreach ($weekDays as $day)
                                            <th
                                                class="{{ $day['highlight'] ? 'bg-primary-light text-primary' : 'bg-light' }}">
                                                <div class="fw-bold">{{ $day['day'] }}</div>
                                                <small>{{ $day['date'] }}</small>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($employees as $emp)
                                        <tr>
                                            @foreach ($weekDays as $day)
                                                @php
                                                    $dateKey = $day['full_date'];

                                                    // Conditions
                                                    $onLeave = hasLeave($emp->id, $dateKey);

                                                    $empAttendance = collect(
                                                        $this->attendanceCalendar[$dateKey] ?? [],
                                                    )->where('user_id', $emp->user_id);

                                                    $hasShift = in_array($emp->id, $this->shiftMap[$dateKey] ?? []);

                                                    $isPastDate = Carbon::parse($dateKey)->lt(today()); // strictly past
                                                    $isToday = Carbon::parse($dateKey)->isToday();
                                                @endphp

                                                <td
                                                    class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}">

                                                    {{-- Leave --}}
                                                    @if ($onLeave)
                                                        <div class="unavailable-box">
                                                            Unavailable
                                                        </div>

                                                        {{-- Attendance exists --}}
                                                    @elseif ($empAttendance->isNotEmpty())
                                                        @foreach ($empAttendance as $att)
                                                            @include(
                                                                'livewire.backend.company.timesheet.partials._attendance-block',
                                                                ['att' => $att]
                                                            )
                                                        @endforeach

                                                        {{-- Absent: Shift exists + past date + no attendance --}}
                                                    @elseif ($hasShift && ($isPastDate || $isToday))
                                                        <div
                                                             class="absent-badge d-flex justify-content-center align-items-center py-1">
                                                            <span
                                                                  class="badge bg-danger d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                                                <i class="fas fa-user-times"></i>
                                                                Absent
                                                            </span>
                                                        </div>
                                                    @endif

                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
                @include('livewire.backend.company.timesheet.partials.footer_summary')
            </div>
        </div>


    </div>




    <div wire:ignore.self
         class="modal fade"
         id="attendanceDetailModal"
         tabindex="-1"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">

                {{-- Header --}}
                <div class="modal-header bg-light rounded-top-4">
                    <div>
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            Attendance Details
                        </h6>
                        @if ($selectedAttendance)
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($selectedAttendance->clock_in)->format('l, d M Y') }}
                            </small>
                        @endif
                    </div>

                    <button class="btn btn-sm btn-outline-secondary rounded-circle"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4 py-3">
                    @if ($selectedAttendance)
                        @php
                            $hours = $this->getShiftHours($selectedAttendance);
                        @endphp

                        {{-- Employee Info --}}
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ $selectedAttendance->user->employee->avatar_url ?? asset('assets/img/default-avatar.png') }}"
                                 class="rounded-circle me-3"
                                 style="width:45px;height:45px;object-fit:cover;">

                            <div>
                                <div class="fw-semibold">
                                    {{ $selectedAttendance->user->full_name }}
                                </div>
                                <small class="text-muted">Employee</small>
                            </div>

                            <div class="ms-auto">
                                <div class="ms-auto d-flex align-items-center gap-2">

                                    @php
                                        $statusColor = match ($selectedAttendance->status) {
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp

                                    {{-- Status Badge --}}
                                    <span class="badge bg-{{ $statusColor }} px-3 py-2">
                                        {{ ucfirst($selectedAttendance->status) }}
                                    </span>

                                    {{-- Action Dropdown (Only Pending) --}}
                                    @if ($selectedAttendance->status === 'pending')
                                        <div class="dropdown">

                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown"
                                                    wire:loading.attr="disabled"
                                                    wire:target="approveAttendance,rejectAttendance">
                                                Actions
                                            </button>

                                            <div class="dropdown-menu dropdown-menu-end shadow-sm p-2"
                                                 style="min-width: 220px;">

                                                {{-- Approve --}}
                                                <button type="button"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-success rounded"
                                                        wire:click="approveAttendance({{ $selectedAttendance->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="approveAttendance">

                                                    <span wire:loading.remove
                                                          wire:target="approveAttendance">
                                                        <i class="fas fa-check-circle"></i>
                                                    </span>

                                                    <span wire:loading
                                                          wire:target="approveAttendance">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>

                                                    <span>
                                                        Approve Attendance
                                                        <small class="d-block text-muted">
                                                            Confirm this attendance
                                                        </small>
                                                    </span>
                                                </button>

                                                <div class="dropdown-divider"></div>

                                                {{-- Reject --}}
                                                <button type="button"
                                                        class="dropdown-item d-flex align-items-center gap-2 text-danger rounded"
                                                        wire:click="rejectAttendance({{ $selectedAttendance->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="rejectAttendance">

                                                    <span wire:loading.remove
                                                          wire:target="rejectAttendance">
                                                        <i class="fas fa-times-circle"></i>
                                                    </span>

                                                    <span wire:loading
                                                          wire:target="rejectAttendance">
                                                        <i class="fas fa-spinner fa-spin"></i>
                                                    </span>

                                                    <span>
                                                        Reject Attendance
                                                        <small class="d-block text-muted">
                                                            Mark as rejected
                                                        </small>
                                                    </span>
                                                </button>

                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- Time Info --}}
                        <div class="row text-center mb-3">

                            <div class="col-6">
                                <div
                                     class="border rounded-3 p-2 d-flex align-items-center justify-content-center gap-1">
                                    <div class="flex-grow-1 text-start">
                                        <small class="text-muted d-block">Clock In</small>

                                        @if ($editingClockIn)
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   wire:model.defer="clockInTime"
                                                   wire:change="updateClockIn">
                                        @else
                                            <span class="fw-bold">
                                                {{ \Carbon\Carbon::parse($clockInTime)->format('h:i A') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Small edit icon with tooltip --}}
                                    <button class="btn btn-sm btn-outline-secondary p-1"
                                            wire:click="toggleClockInEdit"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Edit Clock In Time">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Clock Out --}}
                            <div class="col-6">
                                <div
                                     class="border rounded-3 p-2 d-flex align-items-center justify-content-center gap-1">
                                    <div class="flex-grow-1 text-start">
                                        <small class="text-muted d-block">Clock Out</small>

                                        @if ($editingClockOut)
                                            <input type="time"
                                                   class="form-control form-control-sm"
                                                   wire:model.defer="clockOutTime"
                                                   wire:change="updateClockOut">
                                        @else
                                            <span class="fw-bold">
                                                {{ $clockOutTime ? \Carbon\Carbon::parse($clockOutTime)->format('h:i A') : '---' }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Small edit icon with tooltip --}}
                                    <button class="btn btn-sm btn-outline-secondary p-1"
                                            wire:click="toggleClockOutEdit"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Edit Clock Out Time">
                                        <i class="fas fa-edit fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Initialize tooltips --}}
                        <script>
                            document.addEventListener('livewire:load', function() {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl)
                                });
                            });
                        </script>
                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Shift Hours</small>
                                    <span class="fw-bold">{{ $hours['shift_hours'] }}</span>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Worked Hours</small>
                                    <span class="fw-bold">{{ $hours['worked_hours'] }}</span>
                                </div>
                            </div>

                            <div class="col-4">
                                <div class="border rounded-3 p-2">
                                    <small class="text-muted d-block">Break Hours</small>
                                    <span class="fw-bold">{{ $hours['break_hours'] }}</span>
                                </div>
                            </div>
                        </div>


                        {{-- Location --}}
                        @if ($selectedAttendance->clock_in_location || $selectedAttendance->clock_out_location)
                            <div class="border rounded-3 p-3 mb-3 bg-light">

                                {{-- Clock In Location --}}
                                @if ($selectedAttendance->clock_in_location)
                                    <div class="d-flex align-items-start mb-2">
                                        <i class="fas fa-sign-in-alt text-success me-2 mt-1"></i>
                                        <div>
                                            <small class="text-muted d-block">Clock In Location</small>
                                            <span class="fw-semibold">
                                                {{ $selectedAttendance->clock_in_location }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Clock Out Location --}}
                                @if ($selectedAttendance->clock_out_location)
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-sign-out-alt text-danger me-2 mt-1"></i>
                                        <div>
                                            <small class="text-muted d-block">Clock Out Location</small>
                                            <span class="fw-semibold">
                                                {{ $selectedAttendance->clock_out_location }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        @endif


                        {{-- Requests --}}
                        <h6 class="fw-bold mt-4 mb-3">
                            <i class="fas fa-file-alt me-2 text-primary"></i>
                            Attendance Requests
                        </h6>

                        @forelse($selectedAttendance->requests as $req)
                            @php
                                $reqColor = match ($req->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp

                            <div class="border rounded-3 p-3 mb-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold">
                                        {{ ucfirst(str_replace('_', ' ', $req->type)) }}
                                    </span>
                                    <span class="badge bg-{{ $reqColor }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </div>

                                <small class="text-muted">
                                    {{ $req->reason }}
                                </small>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-1"></i>
                                No requests found
                            </div>
                        @endforelse

                    @endif
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if ($selectedAttendance)
                        <button type="button"
                                class="btn btn-sm btn-danger"
                                onclick="if(confirm('Are you sure you want to delete this attendance?')) { @this.deleteAttendance({{ $selectedAttendance->id }}); }">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>

                        {{-- Close Button --}}
                        <button type="button"
                                class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">
                            Close
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div wire:ignore.self
         class="modal fade"
         id="viewRequestInfo"
         data-bs-backdrop="static"
         tabindex="-1"
         aria-labelledby="viewRequestInfoLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"
                        id="viewRequestInfoLabel">
                        Pending Request
                    </h5>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    @if ($requestDetails)
                        <!-- 1. Employee Card -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <img src="{{ $requestDetails->user->employee->avatar_url ?? 'https://via.placeholder.com/40' }}"
                                 class="rounded-circle me-3 border border-1"
                                 alt="{{ $requestDetails->user->full_name }}"
                                 style="min-width: 40px; width: 40px; height: 40px; object-fit: cover;">
                            <div class="fw-bold text-dark flex-grow-1">{{ $requestDetails->user->full_name }}</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                        <!-- Request Type -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <div class="me-3 fs-5"
                                 style="min-width: 25px;">
                                <span>{{ $requestDetails->typeEmoji }}</span>
                            </div>
                            <div class="fw-medium text-dark flex-grow-1">
                                {{ $requestDetails->typeName }}
                            </div>
                        </div>

                        <!-- Time & Location -->
                        <div
                             class="d-flex align-items-center mb-3 bg-secondary-subtle p-3 rounded-3 shadow-sm border border-secondary-subtle">
                            <i class="fas fa-map-marker-alt me-3 fs-5 text-secondary-icon"
                               style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">
                                <div><strong>Time:</strong>
                                    {{ \Carbon\Carbon::parse($requestDetails->start_date)->format('h:i A') }}</div>
                                <div><strong>Location:</strong> {{ $requestDetails->clock_in_location }}</div>
                            </div>
                        </div>

                        <!-- 4. Reason -->
                        <div
                             class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                            <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon"
                               style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">
                                {{ $requestDetails->reason ?: '-' }}
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No request selected.
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <div>
                        @if ($requestDetails && $requestDetails->status === 'pending')
                            <button type="button"
                                    class="btn btn-danger rounded-pill px-4 shadow-sm me-2"
                                    wire:click="rejectRequest({{ $requestDetails->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="rejectRequest">
                                <span wire:loading.remove
                                      wire:target="rejectRequest">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </span>
                                <span wire:loading.delay
                                      wire:target="rejectRequest">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Rejecting...
                                </span>
                            </button>

                            <button type="button"
                                    class="btn btn-primary rounded-pill px-4 shadow-sm"
                                    wire:click="approveRequest({{ $requestDetails->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="approveRequest">
                                <span wire:loading.remove
                                      wire:target="approveRequest">
                                    <i class="fas fa-check-circle me-1"></i> Approve
                                </span>
                                <span wire:loading.delay
                                      wire:target="approveRequest">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Approving...
                                </span>
                            </button>
                        @elseif ($requestDetails)
                            <span
                                  class="badge
                            @if ($requestDetails->status === 'approved') bg-success
                            @elseif($requestDetails->status === 'rejected') bg-danger
                            @else bg-info @endif
                            px-3 py-2 fs-6">
                                {{ ucfirst($requestDetails->status) }}
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div wire:ignore.self
         class="modal fade"
         id="moreAttendanceDetailModal"
         tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title">More Attendances ({{ $selectedDate }})</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        @foreach ($selectedDateAttendances as $att)
                            <li class="list-group-item attendance-item"
                                style="cursor:pointer;"
                                data-bs-dismiss="modal">
                                @include('livewire.backend.company.timesheet.partials._attendance-block', [
                                    'att' => $att,
                                ])
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self
         class="modal fade"
         id="manualEntryModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="manualEntryModal"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-md"
             role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Submit Manual Attendance</h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitManualEntry">
                    <div class="modal-body">
                        <div class="row g-2">

                            {{-- Employee Dropdown --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Employee <span class="text-danger">*</span></label>
                                <select class="form-select shadow-sm"
                                        wire:model="employeeId"
                                        required>
                                    <option value=""
                                            selected>Select Employee</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('employeeId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Date --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control shadow-sm"
                                       wire:model="manualDate"
                                       required>
                                @error('manualDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Clock In --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Clock In <span class="text-danger">*</span></label>
                                <input type="time"
                                       class="form-control shadow-sm"
                                       wire:model="clockInTime"
                                       required>
                                @error('clockInTime')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Clock Out --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Clock Out</label>
                                <input type="time"
                                       class="form-control shadow-sm"
                                       wire:model="clockOutTime">
                                @error('clockOutTime')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Reason --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Reason</label>
                                <textarea class="form-control shadow-sm"
                                          wire:model="reason"
                                          placeholder="Optional reason"></textarea>
                                @error('reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="submitManualEntry">
                            <span wire:loading
                                  wire:target="submitManualEntry">
                                <i class="fas fa-spinner fa-spin me-2"></i> Submitting...
                            </span>
                            <span wire:loading.remove
                                  wire:target="submitManualEntry">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>







    <script>
        window.addEventListener('showAbsentModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('absentModal'));
            modal.show();
        });
    </script>


    <div wire:ignore.self
         class="modal fade"
         id="absentModal"
         tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Absent Details - {{ $absentDate }}</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    @forelse($absentDetails as $name)
                        <div class="p-2 mb-2 border rounded d-flex align-items-center gap-2">
                            <i class="fas fa-user-times text-danger"></i>
                            <span>{{ $name }}</span>
                        </div>
                    @empty
                        <p class="text-muted">No absent users.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('open-attendance-modal', () => {
            const modal = new bootstrap.Modal(
                document.getElementById('attendanceDetailModal')
            );
            modal.show();
        });
    </script>




    {{-- TIME UPDATE --}}
    <script>
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').innerText = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            document.getElementById('current-date').innerText = now.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
        setInterval(updateTime, 1000);
        updateTime();
    </script>


    <script>
        window.addEventListener('showAbsentModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('absentModal'));
            modal.show();
        });
    </script>



    <script>
        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll('.dropdown-menu').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>


    <script>
        window.addEventListener('show-request-modal', () => {
            var modalEl = document.getElementById('viewRequestInfo');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    </script>
    <script>
        window.addEventListener('show-more-attendance-modal', event => {
            var myModal = new bootstrap.Modal(document.getElementById('moreAttendanceDetailModal'));
            myModal.show();
        });
    </script>
