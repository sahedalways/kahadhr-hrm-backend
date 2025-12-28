@push('styles')
    <link href="{{ asset('assets/css/manage-leave.css') }}" rel="stylesheet" />
@endpush

<div>

    <div class="container-fluid my-4">
        <div class="row g-3">

            @php
                use Carbon\Carbon;

                $currentDate = Carbon::now();
                $year = request('year', $currentDate->year);
                $month = request('month', $currentDate->month);

                $currentDate = Carbon::create($year, $month, 1);
                $prevMonth = $currentDate->copy()->subMonth();
                $nextMonth = $currentDate->copy()->addMonth();

                $daysInMonth = $currentDate->daysInMonth;

                $dates = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $date = Carbon::create($year, $month, $d);
                    $dates[] = [
                        'day' => $d,
                        'letter' => $date->format('D')[0],
                        'date' => $date->format('Y-m-d'),
                        'is_weekend' => in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                    ];
                }
            @endphp


            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
                    <h5 class="mb-0">{{ $currentDate->format('F Y') }}</h5>
                    <div class="btn-group">
                        <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="timeline-container">
                        <div class="timeline-grid timeline-header">
                            <div class="input-group pb-4 shadow-sm p-2 sticky-col rounded-0">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i
                                        class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-2"
                                    placeholder="Search by name" wire:model="search"
                                    wire:keyup="set('search', $event.target.value)">
                            </div>


                            @foreach ($dates as $d)
                                <div
                                    class="day-cell header-cell border-start border-bottom text-center {{ $d['is_weekend'] ? 'weekend-bg' : '' }}">
                                    <div class="day-letter text-muted small">{{ $d['letter'] }}</div>
                                </div>
                            @endforeach


                        </div>

                        @forelse($employees as $emp)
                            @if ($filterEmployeeId && $filterEmployeeId !== $emp->id)
                                @continue
                            @endif




                            <div class="timeline-grid timeline-row" wire:key="emp-{{ $emp->id }}" wire:ignore>
                                <div class="employee-cell sticky-col border-bottom bg-white">
                                    <div class="d-flex align-items-center p-2 gap-2 employee-clickable
        {{ $filterEmployeeId === $emp->id ? 'employee-active' : '' }}"
                                        wire:click="filterByEmployee({{ $emp->id }})">
                                        <img src="{{ $emp->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $emp->full_name }}"
                                            class="rounded-circle border border-2 border-primary-subtle"
                                            style="width: 40px; height: 40px; object-fit: cover;"
                                            alt="{{ $emp->full_name }}">


                                        <div class="d-flex gap-2 flex-column">
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold text-dark lh-sm mb-0" style="font-size: 12px;">
                                                    {{ $emp->full_name }}</h6>
                                                <small class="text-secondary d-block"
                                                    style="font-size: 10px;">{{ $emp->job_title ?? '' }}</small>
                                            </div>


                                            <div class="text-end ms-auto d-flex align-items-center gap-2">

                                                @php
                                                    $totalLeaveDays = $emp->leaves->sum(function ($leave) {
                                                        $start = \Carbon\Carbon::parse($leave->start_date);
                                                        $end = \Carbon\Carbon::parse($leave->end_date);
                                                        return $start->diffInDays($end) + 1;
                                                    });
                                                @endphp

                                                @if ($totalLeaveDays > 0)
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        <i class="fas fa-plane-departure me-1"></i>
                                                        {{ $totalLeaveDays }} Days
                                                    </span>
                                                @endif


                                            </div>
                                        </div>


                                    </div>
                                </div>


                                @foreach ($dates as $d)
                                    @php
                                        $leave = $emp->leaves->first(function ($l) use ($d) {
                                            return $l->start_date <= $d['date'] && $l->end_date >= $d['date'];
                                        });
                                        $bgColor = $leave ? $leave->leaveType->color ?? '#fd7e14' : null;
                                        $emoji = $leave ? $leave->leaveType->emoji ?? '🌴' : '';
                                    @endphp

                                    <div class="day-cell border-start border-bottom position-relative {{ $d['is_weekend'] ? 'weekend-bg' : '' }}"
                                        style="cursor: pointer;"
                                        @if ($leave) onclick="Livewire.dispatch('showLeaveRequestInfo', { id: {{ $leave->id }} })" @endif
                                        wire:key="emp-{{ $emp->id }}-date-{{ $d['date'] }}">
                                        @if ($leave)
                                            <div class="leave-bar" style="background-color: {{ $bgColor }}"
                                                title="{{ $leave->leaveType->name }}">
                                                {{ $emoji }}
                                            </div>
                                        @else
                                            <span class="cell-date-number text-muted small">{{ $d['day'] }}</span>
                                        @endif
                                    </div>
                                @endforeach


                            </div>

                        @empty
                            <div class="text-center py-5 w-100">
                                <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                <div class="text-muted fw-bold">No Employees Found</div>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>




            <div class="col-lg-6 mx-auto">
                <div class="card shadow-sm mb-4">

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary  border-0 p-3">
                            <h5 class="mb-0 text-white">⏳ Pending Leave Requests</h5>
                        </div>
                        <div class="card-body p-4">

                            <ul class="list-group list-group-flush mb-4">
                                @forelse ($leaveRequests as $leave)
                                    <div class="leave-list-container" style="max-height: 350px; overflow-y: auto;">
                                        <li class="list-group-item d-flex align-items-center justify-content-between px-0 leave-request-item"
                                            data-bs-toggle="modal" data-bs-target="#viewRequestInfo"
                                            wire:click="viewRequestInfo({{ $leave->id }})">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $leave->user->employee->avatar_url }}"
                                                    class="rounded-circle me-3 border border-2 border-light-subtle"
                                                    style="width: 45px; height: 45px; object-fit: cover;"
                                                    alt="{{ $leave->user->full_name }}">
                                                <div>
                                                    <div class="fw-bold text-dark">{{ $leave->user->full_name }}</div>
                                                    <small class="text-muted text-uppercase fw-medium"
                                                        style="font-size: 0.75rem;">
                                                        {{ $leave->user->employee->department->name ?? 'N/A Department' }}
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <span class="badge bg-info-subtle text-info fw-bold p-2">
                                                    {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}
                                                    Days
                                                </span>
                                                <small class="d-block text-muted mt-1" style="font-size: 0.75rem;">
                                                    {{ \Carbon\Carbon::parse($leave->start_date)->format('M d') }} -
                                                    {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                                </small>
                                            </div>
                                        </li>
                                    </div>
                                @empty
                                    <li class="list-group-item text-center text-muted py-4">
                                        <i class="fas fa-check-circle me-1"></i> No pending leave requests.
                                    </li>
                                @endforelse
                            </ul>

                            <hr class="my-4">
                            <div class="card-header bg-primary  border-0 p-3 mb-2">
                                <h5 class="mb-0 text-white">✍️ Manual Leave Entry</h5>
                            </div>

                            <form wire:submit.prevent="saveRequest">
                                {{-- Employee Select --}}
                                <div class="mb-3">
                                    <label for="employeeSelect" class="form-label fw-medium">Select Employee <span
                                            class="text-danger">*</span></label>
                                    <div class="dropdown">
                                        <button class="btn btn-light form-select text-start w-100" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ $selectedEmployeeName ?? '-- Select Employee --' }}
                                        </button>

                                        <ul class="dropdown-menu w-100" style="max-height:200px; overflow-y:auto;">
                                            @foreach ($employees as $employee)
                                                <li>
                                                    <a class="dropdown-item"
                                                        wire:click="selectEmployee({{ $employee->user_id }})">
                                                        {{ $employee->full_name }}

                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @error('selectedEmployee')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>



                                </div>



                                <div class="mb-3">
                                    <label>Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model.live="leave_type_id"
                                        wire:key="leave_type_id">
                                        <option value="">-- Select --</option>
                                        @foreach ($leaveTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {!! $type->emoji !!} {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leave_type_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>



                                @if ($leave_type_id && optional($leaveTypes->firstWhere('id', $leave_type_id))->name === 'Others')
                                    <div class="mb-2">
                                        <label>Specify Other Leave Type <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model="other_leave_reason"
                                            placeholder="Enter reason">
                                        @error('other_leave_reason')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif



                                @php
                                    $showPaidBlock = in_array($leave_type_id, [2, 3, 6]);
                                @endphp

                                @if ($showPaidBlock)

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Leave Payment Status <span
                                                class="text-danger">*</span></label>

                                        <select class="form-select" wire:model.live="paidStatus">
                                            <option value="">Select Status</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                        </select>

                                        @error('paidStatus')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Show Hours only if PAID --}}
                                    @if ($paidStatus === 'paid')
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Paid Hours <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control"
                                                wire:model="paidHours" placeholder="Enter hours">

                                            @error('paidHours')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                @endif



                                {{-- Date Range --}}
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="start_date" class="form-label fw-medium">From Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="start_date"
                                            wire:model="start_date" min="{{ date('Y-m-d') }}">
                                        @error('start_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="end_date" class="form-label fw-medium">End Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="end_date"
                                            wire:model="end_date" min="{{ date('Y-m-d') }}">
                                        @error('end_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>


                                @error('remaining')
                                    <div class="text-danger mb-2">{{ $message }}</div>
                                @enderror






                                <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 shadow-sm"
                                    wire:loading.attr="disabled" wire:target="saveRequest">
                                    <span wire:loading wire:target="saveRequest">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Submitting ...
                                    </span>
                                    <span wire:loading.remove wire:target="saveRequest"> <i
                                            class="fas fa-plus-circle me-2"></i> Add Leave Manually</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>




    <div wire:ignore.self class="modal fade" id="editLeaveModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editLeaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editLeaveModalLabel">Edit Leave</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    @if ($calendarLeaveInfo)
                        <!-- 1. Employee Card -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <img src="{{ $calendarLeaveInfo->user->employee->avatar_url ?? 'https://via.placeholder.com/40' }}"
                                class="rounded-circle me-3 border border-1"
                                alt="{{ $calendarLeaveInfo->user->full_name }}"
                                style="min-width: 40px; width: 40px; height: 40px; object-fit: cover;">
                            <div class="fw-bold text-dark flex-grow-1">{{ $calendarLeaveInfo->user->full_name }}</div>
                        </div>

                        <!-- 2. Leave Type -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <div class="me-3 fs-5" style="min-width: 25px;">
                                {!! $calendarLeaveInfo->leaveType->emoji !!}
                            </div>
                            <div class="fw-medium text-dark flex-grow-1">
                                {{ $calendarLeaveInfo->leaveType->name ?? 'N/A' }}
                            </div>
                        </div>

                        <!-- 3. Reason/Description -->
                        <div
                            class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                            <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon" style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">
                                {{ $calendarLeaveInfo->other_reason ?? '-' }}
                            </div>
                        </div>

                        <!-- 4. Editable Date Range -->
                        <div class="row g-2 mb-4 px-2">
                            <div class="col-12">
                                <label class="form-label fw-bold">Start Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="editStartDate"
                                    min="{{ date('Y-m-d') }}">
                                @error('editStartDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="editEndDate"
                                    min="{{ date('Y-m-d') }}">
                                @error('editEndDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- 5. Existing Badges / Payment info (read-only) -->
                        @if (!in_array($calendarLeaveInfo->leave_type_id, [1, 5]))
                            <div class="mt-4 p-3 bg-light rounded-3 border shadow-sm">
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fas fa-wallet me-2 text-primary"></i> Leave Payment Details
                                </h6>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-muted">Type:</span>
                                    <span class="fw-bold text-dark text-uppercase">
                                        {{ $calendarLeaveInfo->paid_status ?? 'N/A' }}
                                    </span>
                                </div>
                                @if ($calendarLeaveInfo->paid_status === 'paid')
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fw-medium text-muted">Paid Hours:</span>
                                        <span class="fw-bold text-success">
                                            {{ number_format($calendarLeaveInfo->paid_hours, 2) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- 6. Deduction Summary -->
                        <div class="text-center mt-3 pt-3 border-top">
                            <h5 class="fw-bold text-secondary">Deduction:
                                {{ \Carbon\Carbon::parse($calendarLeaveInfo->start_date)->diffInDays(\Carbon\Carbon::parse($calendarLeaveInfo->end_date)) + 1 }}
                                days
                            </h5>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No request selected.
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-primary rounded-pill px-4"
                        wire:click="updateLeave({{ $calendarLeaveInfo->id ?? 0 }})" wire:loading.attr="disabled"
                        wire:target="updateLeave">
                        <span wire:loading.remove wire:target="updateLeave">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </span>
                        <span wire:loading wire:target="updateLeave">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>




    <div wire:ignore.self class="modal fade" id="viewRequestInfo" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="viewRequestInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewRequestInfoLabel">Leave Request</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
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

                        <!-- 2. Leave Type -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <div class="me-3 fs-5" style="min-width: 25px;">
                                {!! $requestDetails->leaveType->emoji !!}
                            </div>
                            <div class="fw-medium text-dark flex-grow-1">
                                {{ $requestDetails->leaveType->name ?? 'N/A' }}
                            </div>
                        </div>


                        <!-- 3. Reason/Description -->
                        <div
                            class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                            <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon" style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">{{ $requestDetails->other_reason ?? '-' }}
                            </div>
                        </div>

                        <!-- 4. Date Range -->
                        <div class="row g-2 mb-4 px-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <div class="text-muted fw-medium me-3" style="min-width: 50px;">From</div>
                                    <div class="fw-bold text-dark flex-grow-1">
                                        {{ \Carbon\Carbon::parse($requestDetails->start_date)->format('D, d M') }}
                                    </div>
                                    <div class="badge bg-success-subtle text-success fw-bold p-2">Morning</div>
                                </div>
                            </div>
                            <hr class="text-light-subtle my-0">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <div class="text-muted fw-medium me-3" style="min-width: 50px;">To</div>
                                    <div class="fw-bold text-dark flex-grow-1">
                                        {{ \Carbon\Carbon::parse($requestDetails->end_date)->format('D, d M, Y') }}
                                    </div>
                                    <div class="badge bg-danger-subtle text-danger fw-bold p-2">End of day</div>
                                </div>
                            </div>
                        </div>


                        @if (in_array($requestDetails->leave_type_id, [2, 3, 4, 6]))
                            <div class="mb-3">
                                <label class="form-label fw-bold">Leave Type Option <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" wire:model.live="paidStatus"
                                    @if ($requestDetails->leave_type_id == 4) disabled @endif>
                                    <option value="">Select Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                </select>
                            </div>

                            @if ($paidStatus === 'paid')
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Hours <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" min="0" step="0.25"
                                        wire:model.defer="paidHours" placeholder="Enter leave hours">
                                </div>
                            @endif
                        @endif


                        <!-- 5. Deduction Summary -->
                        <div class="text-center mt-3 pt-3 border-top">
                            <h5 class="fw-bold text-secondary">Deduction:
                                {{ \Carbon\Carbon::parse($requestDetails->start_date)->diffInDays(\Carbon\Carbon::parse($requestDetails->end_date)) + 1 }}
                                days</h5>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No request selected.
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <div>
                        <div>

                            <button type="button" class="btn btn-danger rounded-pill px-4 shadow-sm me-2"
                                wire:click="rejectRequest({{ $requestDetails->id ?? 0 }})"
                                wire:loading.attr="disabled" wire:target="rejectRequest">

                                <span wire:loading.remove wire:target="rejectRequest">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </span>

                                <span wire:loading.delay wire:target="rejectRequest">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Rejecting...
                                </span>
                            </button>




                            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm me-2"
                                wire:click="approveRequest({{ $requestDetails->id ?? 0 }})"
                                wire:loading.attr="disabled" wire:target="approveRequest">

                                <span wire:loading.remove wire:target="approveRequest">
                                    <i class="fas fa-check-circle me-1"></i> Approve
                                </span>

                                <span wire:loading.delay wire:target="approveRequest">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    Approving...
                                </span>
                            </button>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <div wire:ignore.self class="modal fade" id="viewRequestInfoFromCalendar" data-bs-backdrop="static"
        tabindex="-1" aria-labelledby="viewRequestInfoFromCalendar" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="viewRequestInfoFromCalendar">Leave Request</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    @if ($calendarLeaveInfo)
                        <!-- 1. Employee Card -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <img src="{{ $calendarLeaveInfo->user->employee->avatar_url ?? 'https://via.placeholder.com/40' }}"
                                class="rounded-circle me-3 border border-1"
                                alt="{{ $calendarLeaveInfo->user->full_name }}"
                                style="min-width: 40px; width: 40px; height: 40px; object-fit: cover;">
                            <div class="fw-bold text-dark flex-grow-1">{{ $calendarLeaveInfo->user->full_name }}</div>

                        </div>

                        <!-- 2. Leave Type -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <div class="me-3 fs-5" style="min-width: 25px;">
                                {!! $calendarLeaveInfo->leaveType->emoji !!}
                            </div>
                            <div class="fw-medium text-dark flex-grow-1">
                                {{ $calendarLeaveInfo->leaveType->name ?? 'N/A' }}
                            </div>
                        </div>


                        <!-- 3. Reason/Description -->
                        <div
                            class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                            <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon" style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">{{ $calendarLeaveInfo->other_reason ?? '-' }}
                            </div>
                        </div>

                        <!-- 4. Date Range -->
                        <div class="row g-2 mb-4 px-2">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <div class="text-muted fw-medium me-3" style="min-width: 50px;">From</div>
                                    <div class="fw-bold text-dark flex-grow-1">
                                        {{ \Carbon\Carbon::parse($calendarLeaveInfo->start_date)->format('D, d M') }}
                                    </div>
                                    <div class="badge bg-success-subtle text-success fw-bold p-2">Morning</div>
                                </div>
                            </div>
                            <hr class="text-light-subtle my-0">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <div class="text-muted fw-medium me-3" style="min-width: 50px;">To</div>
                                    <div class="fw-bold text-dark flex-grow-1">
                                        {{ \Carbon\Carbon::parse($calendarLeaveInfo->end_date)->format('D, d M, Y') }}
                                    </div>
                                    <div class="badge bg-danger-subtle text-danger fw-bold p-2">End of day</div>
                                </div>
                            </div>
                        </div>


                        @if ($calendarLeaveInfo && !in_array($calendarLeaveInfo->leave_type_id, [1, 5]))

                            <div class="mt-4 p-3 bg-light rounded-3 border shadow-sm">

                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="fas fa-wallet me-2 text-primary"></i> Leave Payment Details
                                </h6>

                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium text-muted">Type:</span>
                                    <span class="fw-bold text-dark text-uppercase">
                                        {{ $calendarLeaveInfo->paid_status ?? 'N/A' }}
                                    </span>
                                </div>

                                @if ($calendarLeaveInfo->paid_status === 'paid')
                                    <div class="d-flex justify-content-between mt-2">
                                        <span class="fw-medium text-muted">Paid Hours:</span>
                                        <span class="fw-bold text-success">
                                            {{ number_format($calendarLeaveInfo->paid_hours, 2) }}
                                        </span>
                                    </div>
                                @endif

                            </div>

                        @endif



                        <!-- 5. Deduction Summary -->
                        <div class="text-center mt-3 pt-3 border-top">
                            <h5 class="fw-bold text-secondary">Deduction:
                                {{ \Carbon\Carbon::parse($calendarLeaveInfo->start_date)->diffInDays(\Carbon\Carbon::parse($calendarLeaveInfo->end_date)) + 1 }}
                                days</h5>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-1"></i> No request selected.
                        </div>
                    @endif
                </div>


                @if ($calendarLeaveInfo)
                    <div class="modal-footer d-flex justify-content-between">

                        <!-- Left: Cancel Leave -->
                        <button type="button" class="btn btn-outline-danger rounded-pill px-4"
                            onclick="confirmCancel({{ $calendarLeaveInfo->id }})" wire:loading.attr="disabled"
                            wire:target="cancelLeave">

                            <span wire:loading.remove wire:target="cancelLeave">
                                <i class="fas fa-ban me-1"></i> Cancel Leave
                            </span>

                            <span wire:loading wire:target="cancelLeave">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                Cancelling...
                            </span>
                        </button>

                        <!-- Right: Edit Leave -->
                        <button type="button" class="btn btn-primary rounded-pill px-4"
                            wire:click="editLeave({{ $calendarLeaveInfo->id }})" data-bs-toggle="modal"
                            data-bs-target="#editLeaveModal">

                            <i class="fas fa-edit me-1"></i> Edit Leave
                        </button>

                    </div>
                @endif

            </div>
        </div>
    </div>


</div>



<script>
    window.addEventListener('show-leave-modal', event => {
        let modalEl = document.getElementById('viewRequestInfoFromCalendar');
        let modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
</script>


<script>
    function confirmCancel(id) {
        if (confirm('Are you sure you want to cancel this leave?')) {
            @this.cancelLeave(id);
        }
    }
</script>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reload-page', () => {
            window.location.reload();
        });
    });
</script>
