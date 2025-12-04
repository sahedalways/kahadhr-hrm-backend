@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
    <link href="{{ asset('assets/css/manage-leave.css') }}" rel="stylesheet" />
@endpush

<div>

    <div class="container-fluid my-4">
        <div class="row g-3">

            <!-- Left Column: Employee List + Search -->
            <div class="col-lg-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Employees</h5>
                    </div>
                    <div class="card shadow-lg border-0 h-100">
                        <div class="card-header bg-white border-0 pt-4 px-4">
                            <h5 class="fw-bold text-primary mb-2">👥 Employee Directory</h5>
                            <p class="text-muted small mb-3">Quickly find and view leave details.</p>
                        </div>
                        <div class="card-body p-4">

                            <div class="input-group mb-4 shadow-sm">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i
                                        class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0 ps-0"
                                    placeholder="Search by name, department, or title..."
                                    wire:model.debounce.300ms="searchQuery">
                            </div>

                            <div class="list-container" style="max-height: 400px; overflow-y: auto;">
                                <ul class="list-group list-group-flush">
                                    @forelse ($employees as $employee)
                                        <li class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom-0"
                                            style="cursor: pointer;" wire:click="selectEmployee({{ $employee->id }})">


                                            <img src="{{ $employee->avatar_url }}"
                                                class="rounded-circle me-3 border border-2 border-primary-subtle"
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                alt="{{ $employee->full_name }}">

                                            <div class="flex-grow-1">

                                                <div class="fw-bold text-dark">{{ $employee->full_name }}</div>
                                                <small
                                                    class="text-secondary d-block">{{ $employee->job_title ?? null }}</small>
                                            </div>

                                            <div class="text-end ms-auto">

                                                <span
                                                    class="badge bg-light text-primary border border-primary-subtle fw-medium text-uppercase"
                                                    style="font-size: 0.75rem;">
                                                    <i class="fas fa-building me-1"></i>
                                                    {{ $employee->department->name ?? 'Unknown' }}
                                                </span>


                                                <i class="fas fa-chevron-right text-muted ms-3"></i>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center text-muted py-5">
                                            <i class="fas fa-info-circle me-1"></i> No employees found matching your
                                            criteria.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column: Calendar -->
            <div class="col-lg-5" wire:ignore>
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Leaves Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar" style="min-height: 500px;"></div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Leave Requests</h5>
                    </div>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white border-0 py-3">
                            <h5 class="mb-0">⏳ Pending Leave Requests</h5>
                        </div>
                        <div class="card-body p-4">

                            <ul class="list-group list-group-flush mb-4">
                                @forelse ($leaveRequests as $leave)
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
                                @empty
                                    <li class="list-group-item text-center text-muted py-4">
                                        <i class="fas fa-check-circle me-1"></i> No pending leave requests.
                                    </li>
                                @endforelse
                            </ul>

                            <hr class="my-4">

                            <h5 class="fw-bold mb-3 text-primary">✍️ Manual Leave Entry</h5>
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
                                                        ({{ $employee->department->name ?? 'N/A' }})
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
                                    </div>
                                    @error('end_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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
                                style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="fw-bold text-dark flex-grow-1">{{ $requestDetails->user->full_name }}</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>

                        <!-- 2. Leave Type -->
                        <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                            <i class="fas fa-umbrella-beach me-3 fs-5 text-warning-icon" style="min-width: 25px;"></i>
                            <div class="fw-medium text-dark flex-grow-1">
                                {{ $requestDetails->leaveType->name ?? 'N/A' }}</div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>

                        <!-- 3. Reason/Description -->
                        <div
                            class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                            <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon" style="min-width: 25px;"></i>
                            <div class="fw-normal text-dark flex-grow-1">{{ $requestDetails->reason ?? '-' }}</div>
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
                                wire:loading.attr="disabled"
                                wire:target="rejectRequest({{ $requestDetails->id ?? 0 }})">
                                <span wire:loading.remove wire:target="rejectRequest({{ $requestDetails->id ?? 0 }})">
                                    Reject
                                </span>
                                <span wire:loading wire:target="rejectRequest({{ $requestDetails->id ?? 0 }})">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    Rejecting...
                                </span>
                            </button>

                            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm"
                                wire:click="approveRequest({{ $requestDetails->id ?? 0 }})"
                                wire:loading.attr="disabled"
                                wire:target="approveRequest({{ $requestDetails->id ?? 0 }})">
                                <span wire:loading.remove
                                    wire:target="approveRequest({{ $requestDetails->id ?? 0 }})">
                                    Approve
                                </span>
                                <span wire:loading wire:target="approveRequest({{ $requestDetails->id ?? 0 }})">
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>
                                    Approving...
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


</div>


<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="{{ asset('js/company/manage-leave.js') }}"></script>
