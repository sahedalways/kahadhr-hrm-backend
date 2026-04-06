@php

    if (empty($dates) || count($dates) == 0) {
        $dates = $this->dates;
    }

    $firstDate = $dates[0]['date'] ?? null;
    $expectedFirstDate = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';

    if ($firstDate != $expectedFirstDate) {
        $dates = $this->dates;
    }
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0">
            {{ Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F Y') }}
        </h5>
        <div class="btn-group">
            <button wire:click="changeMonth('prev')"
                    class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button wire:click="changeMonth('next')"
                    class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="timeline-container">
            <div class="timeline-grid timeline-header">
                <div class="input-group pb-4 shadow-sm p-2 sticky-col rounded-0">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0 ps-2"
                           placeholder="Search by name"
                           wire:model.live.debounce.300ms="search">
                </div>

                @foreach ($dates as $d)
                    <div
                         class="day-cell header-cell border-start border-bottom text-center {{ $d['is_weekend'] ? 'weekend-bg' : '' }}">
                        <div class="day-letter text-muted small">{{ $d['letter'] }}</div>
                    </div>
                @endforeach
            </div>
            @forelse($employees as $emp)
                @if ($filterEmployeeId && $filterEmployeeId !== $emp->user_id)
                    @continue
                @endif

                <div class="timeline-grid timeline-row"
                     wire:key="emp-{{ $emp->user_id }}">
                    <div class="employee-cell sticky-col border-bottom bg-white">
                        <div class="d-flex align-items-center p-2 gap-2 employee-clickable
                {{ $filterEmployeeId === $emp->user_id ? 'employee-active' : '' }}"
                             wire:click="filterByEmployee({{ $emp->user_id }})">
                            <img src="{{ $emp->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $emp->full_name }}"
                                 class="rounded-circle border border-2 border-primary-subtle"
                                 style="width: 40px; height: 40px; object-fit: cover;"
                                 alt="{{ $emp->full_name }}">

                            <div class="d-flex gap-2 flex-column">
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold text-dark lh-sm mb-0"
                                        style="font-size: 12px;">
                                        {{ $emp->full_name }}
                                    </h6>
                                    <small class="text-secondary d-block"
                                           style="font-size: 10px;">{{ $emp->job_title ?? '' }}</small>
                                </div>

                                <div class="text-end ms-auto d-flex align-items-center gap-2">
                                    @php
                                        $totalLeaveDays = 0;

                                        $leavesCollection =
                                            $filterEmployeeId === $emp->user_id
                                                ? $approvedLeavesCollection->where('user_id', $emp->user_id)
                                                : $emp->leaves;

                                        $totalLeaveDays = $leavesCollection->sum(function ($leave) use (
                                            $currentYear,
                                            $currentMonth,
                                        ) {
                                            $start = \Carbon\Carbon::parse($leave->start_date)->startOfDay();
                                            $end = \Carbon\Carbon::parse($leave->end_date)->startOfDay();

                                            $currentStart = \Carbon\Carbon::create(
                                                $currentYear,
                                                $currentMonth,
                                                1,
                                            )->startOfMonth();
                                            $currentEnd = \Carbon\Carbon::create(
                                                $currentYear,
                                                $currentMonth,
                                                1,
                                            )->endOfMonth();

                                            $effectiveStart = $start->lt($currentStart) ? $currentStart : $start;
                                            $effectiveEnd = $end->gt($currentEnd) ? $currentEnd : $end;

                                            if ($effectiveStart->gt($effectiveEnd)) {
                                                return 0;
                                            }

                                            $days = $effectiveStart->diffInDays($effectiveEnd->copy()->startOfDay());
                                            return round($days) + 1;
                                        });
                                    @endphp

                                    @if ($totalLeaveDays > 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="fas fa-plane-departure me-1"></i>
                                            {{ number_format($totalLeaveDays, 0) }}
                                            {{ Str::plural('Day', $totalLeaveDays) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach ($dates as $d)
                        @php

                            $employeeLeaves = $this->approvedLeavesCollection->where('user_id', $emp->user_id);
                            $cellDate = \Carbon\Carbon::parse($d['date'])->startOfDay();

                            $leave = $employeeLeaves->first(function ($l) use ($cellDate, $emp) {
                                $startDate = \Carbon\Carbon::parse($l->start_date)->startOfDay();
                                $endDate = \Carbon\Carbon::parse($l->end_date)->endOfDay();

                                $isInRange = $cellDate->gte($startDate) && $cellDate->lte($endDate);

                                return $isInRange;
                            });

                            $isWeekend = $d['is_weekend'];
                            $hasLeave = !is_null($leave);

                            $cellClass = 'day-cell border-start border-bottom position-relative transition-all ';
                            $cellClass .= $isWeekend ? 'weekend-bg ' : '';
                            $cellClass .= $hasLeave ? 'has-leave ' : '';

                            $tooltipTitle = $hasLeave
                                ? ($leave->leaveType->name ?? 'Leave') . ' (' . ($leave->leaveType->emoji ?? '🌴') . ')'
                                : '';
                        @endphp


                        <div class="{{ $cellClass }}"
                             style="cursor: pointer; min-width: 45px;"
                             @if ($hasLeave) data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             title="{{ $tooltipTitle }}"
                             onclick="Livewire.dispatch('showLeaveRequestInfo', { id: {{ $leave->id }} })" @endif
                             wire:key="emp-{{ $emp->user_id }}-date-{{ $d['date'] }}">

                            @if ($hasLeave)
                                <div class="leave-indicator position-relative w-100 h-100 d-flex align-items-center justify-content-center"
                                     style="background-color: {{ $leave->leaveType->color ?? '#f8b195' }}30;
                                       min-height: 45px;">
                                    <span class="leave-emoji"
                                          style="font-size: 18px;">
                                        {{ $leave->leaveType->emoji ?? '🌴' }}
                                    </span>
                                    @if ($leave->is_half_day ?? false)
                                        <span
                                              class="half-day-badge position-absolute top-0 end-0 small text-muted">½</span>
                                    @endif
                                </div>
                            @else
                                <div class="d-flex align-items-center justify-content-center"
                                     style="min-height: 45px;">
                                    <span class="date-number text-muted small fw-medium">{{ $d['day'] }}</span>
                                </div>
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

            {{-- Load More Button --}}
            @if ($employees->hasMorePages())
                <div class="text-center py-3">
                    <button wire:click="loadMore"
                            wire:loading.attr="disabled"
                            class="btn btn-outline-primary">
                        <span wire:loading.remove>
                            <i class="fas fa-plus-circle me-1"></i> Load More Employees
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i> Loading...
                        </span>
                    </button>
                </div>
            @endif

            {{-- Optional: Show pagination info --}}
            @if ($employees->total() > 0)
                <div class="text-center text-muted small py-2 border-top">
                    Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }}
                    of {{ $employees->total() }} employees
                </div>
            @endif
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
                        <div class="leave-list-container"
                             style="max-height: 350px; overflow-y: auto;">
                            <li class="list-group-item d-flex align-items-center justify-content-between px-0 leave-request-item"
                                wire:click="viewRequestInfo({{ $leave->id }})">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $leave->user->employee->avatar_url }}"
                                         class="rounded-circle me-3 border border-2 border-light-subtle"
                                         style="width: 45px; height: 45px; object-fit: cover;"
                                         alt="{{ $leave->user->full_name }}">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $leave->user->full_name }}</div>

                                    </div>
                                </div>

                                <div class="text-end">
                                    <span class="badge bg-info-subtle text-info fw-bold p-2">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->diffInDays(\Carbon\Carbon::parse($leave->end_date)) + 1 }}
                                        Days
                                    </span>
                                    <small class="d-block text-muted mt-1"
                                           style="font-size: 0.75rem;">
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
                        <label for="employeeSelect"
                               class="form-label fw-medium">Select Employee <span class="text-danger">*</span></label>
                        <div class="dropdown">
                            <button class="btn btn-light form-select text-start w-100"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                {{ $selectedEmployeeName ?? '-- Select Employee --' }}
                            </button>

                            <ul class="dropdown-menu w-100"
                                style="max-height:200px; overflow-y:auto;">
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
                        <select class="form-select"
                                wire:model.live="leave_type_id"
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
                            <input type="text"
                                   class="form-control"
                                   wire:model="other_leave_reason"
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

                            <select class="form-select"
                                    wire:model.live="paidStatus">
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
                                <input type="number"
                                       step="0.01"
                                       class="form-control"
                                       wire:model="paidHours"
                                       placeholder="Enter hours">

                                @error('paidHours')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                    @endif



                    {{-- Date Range --}}
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="start_date"
                                   class="form-label fw-medium">From Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="start_date"
                                   wire:model="start_date">
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date"
                                   class="form-label fw-medium">End Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   id="end_date"
                                   wire:model="end_date">
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    @error('remaining')
                        <div class="text-danger mb-2">{{ $message }}</div>
                    @enderror






                    <button type="submit"
                            class="btn btn-primary btn-lg w-100 mt-3 shadow-sm"
                            wire:loading.attr="disabled"
                            wire:target="saveRequest">
                        <span wire:loading
                              wire:target="saveRequest">
                            <i class="fas fa-spinner fa-spin me-2"></i> Submitting ...
                        </span>
                        <span wire:loading.remove
                              wire:target="saveRequest"> <i class="fas fa-plus-circle me-2"></i> Add Leave
                            Manually</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


</div>
</div>




@include('livewire.backend.company.leaves.partials.modals')
