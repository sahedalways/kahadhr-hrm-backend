@push('styles')
    <link href="{{ asset('assets/css/timesheet.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/company-schedule.css') }}" rel="stylesheet" />
@endpush
@php
    use Carbon\Carbon;
@endphp


<div class="container-fluid dashboard-container">
    <div class="row h-100">

        {{-- LEFT PANEL --}}
        <div class="col-md-5 left-panel py-5 px-4">

            {{-- CURRENT TIME --}}
            <div class="time-display text-white mb-5">
                <div class="current-time" id="current-time" wire:ignore>10:00:00 AM</div>
                <div class="current-date" id="current-date" wire:ignore>Monday, Oct 26, 2024</div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="action-card mb-3">
                <a href="#" class="action-link" data-bs-toggle="modal" data-bs-target="#manualEntryModal">
                    <i class="fas fa-user-clock"></i>
                    <span>Submit Manual Entry</span>
                </a>
            </div>

            <div class="action-card">
                <a href="#" class="action-link" data-bs-toggle="modal" data-bs-target="#timeSheetModal">
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

                    <div class="col-md-12">
                        <select class="form-control" wire:model.live="employeeId">
                            <option value="">All Employees</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <input type="date" wire:model.live="dateFrom" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <input type="date" wire:model.live="dateTo" class="form-control">
                    </div>
                </div>
            </div>

            {{-- PENDING REQUESTS --}}
            <div class="pending-requests-section mb-4 mt-3">
                <h3 class="requests-header text-white mb-3">
                    Pending Requests ({{ $records->sum(fn($r) => $r->requests->where('status', 'pending')->count()) }})
                </h3>

                @foreach ($records as $record)
                    @foreach ($record->requests->where('status', 'pending') as $req)
                        @php
                            $location =
                                $req->type === 'late_clock_in'
                                    ? $record->clock_in_location
                                    : $record->clock_out_location;
                        @endphp

                        <div class="request-item mb-2 border-bottom pb-2 gap-4">
                            {{-- Header (clickable) --}}
                            <div class="request-info d-flex justify-content-between align-items-center"
                                wire:click="toggleReason({{ $req->id }})" style="cursor: pointer;">
                                <div>
                                    <p class="mb-0 request-name">
                                        {{ $record->user->full_name }}
                                        ({{ \Carbon\Carbon::parse($record->clock_in)->format('h:i A') }})
                                    </p>

                                    <small class="text-muted d-block">
                                        {{ ucfirst(str_replace('_', ' ', $req->type)) }} Request -
                                        <b class="text-warning">Pending</b>
                                    </small>

                                    @if ($location)
                                        <small class="text-info d-block">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $location }}
                                        </small>
                                    @endif
                                </div>

                                <div class="toggle-icon">
                                    <i class="fas"
                                        :class="{ 'fa-chevron-down': {{ $expandedRequest === $req->id ? 'true' : 'false' }}, 'fa-chevron-right': {{ $expandedRequest === $req->id ? 'false' : 'true' }} }"></i>
                                </div>
                            </div>



                            {{-- Action Buttons --}}
                            <div class="request-actions mt-2">
                                <button class="btn btn-sm action-btn approve-btn"
                                    wire:click="approveRequest({{ $req->id }})"
                                    onclick="return confirm('Are you sure you want to approve this request?')"
                                    wire:loading.attr="disabled" wire:target="approveRequest({{ $req->id }})">
                                    <span wire:loading wire:target="approveRequest({{ $req->id }})">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="approveRequest({{ $req->id }})">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </button>

                                <button class="btn btn-sm action-btn reject-btn"
                                    wire:click="rejectRequest({{ $req->id }})"
                                    onclick="return confirm('Are you sure you want to reject this request?')"
                                    wire:loading.attr="disabled" wire:target="rejectRequest({{ $req->id }})">
                                    <span wire:loading wire:target="rejectRequest({{ $req->id }})">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="rejectRequest({{ $req->id }})">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </button>
                            </div>
                        </div>

                        {{-- Reason (FAQ style) --}}
                        <div class="request-reason mt-2 transition-all duration-300 overflow-hidden"
                            style="max-height: {{ $expandedRequest === $req->id ? '200px' : '0' }}">
                            <div class="card">
                                <div class="card-body py-2 px-3">
                                    <small class="d-block fw-bold">Reason: {{ $req->reason }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>





        </div>

    </div>

    <div wire:ignore.self class="modal fade" id="timeSheetModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Time Sheet</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
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
                                                style="height:80px;width:14.28%;position:relative;">


                                                @if ($day->month == $this->startDate->month)
                                                    <span class="date-number">{{ $day->day }}</span>
                                                @endif


                                                @if ($isCurrent && $hasAtt)
                                                    @foreach ($this->attendanceCalendar[$dateKey] as $att)
                                                        @include(
                                                            'livewire.backend.company.timesheet.partials._attendance-block',
                                                            ['att' => $att]
                                                        )
                                                    @endforeach
                                                @endif

                                                @php
                                                    $isPastOrToday = $day->lessThanOrEqualTo(today());
                                                    $shiftEmployees = $this->shiftMap[$dateKey] ?? [];

                                                @endphp



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

                        {{-- sidebar --}}
                        <div class="mt-n3">
                            <div class="schedule-sidebar p-2 border-end" style="width:280px;flex-shrink:0;">
                                <input type="text" class="form-control form-control-sm mb-3"
                                    placeholder="Search employees..." wire:model.live="employeeSearch">

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
                                            <div class="d-flex align-items-center py-2 px-2 employee-row shadow-sm rounded mb-2"
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
                        <div class="flex-grow-1 mt-4">
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
                                                    $onLeave = hasLeave($emp['id'], $dateKey);
                                                    $empAtt = collect($this->attendanceCalendar[$dateKey] ?? [])->where(
                                                        'user_id',
                                                        $emp->user_id,
                                                    );

                                                    $hasShift = in_array($emp['id'], $this->shiftMap[$dateKey] ?? []);
                                                    $isPastOrToday = Carbon::parse($dateKey)->lessThanOrEqualTo(
                                                        today(),
                                                    );
                                                @endphp

                                                <td
                                                    class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}">
                                                    @if ($onLeave)
                                                        <div class="unavailable-box">Unavailable</div>
                                                    @elseif ($empAtt->isNotEmpty())
                                                        @foreach ($empAtt as $att)
                                                            @include(
                                                                'livewire.backend.company.timesheet.partials._attendance-block',
                                                                ['att' => $att]
                                                            )
                                                        @endforeach
                                                    @elseif ($hasShift && $isPastOrToday)
                                                        <div class="absent-badge w-100 text-center">
                                                            <span class="badge bg-danger small">Absent -
                                                                {{ $emp->full_name }}</span>
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

            </div>
        </div>
    </div>





</div>




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
