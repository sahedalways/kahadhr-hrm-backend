@push('styles')
    <link href="{{ asset('assets/css/timesheet.css') }}" rel="stylesheet">
@endpush

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
                <a href="#" class="action-link">
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

    <div wire:ignore.self class="modal fade" id="manualEntryModal" tabindex="-1" role="dialog"
        aria-labelledby="manualEntryModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Submit Manual Attendance</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
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
                                <select class="form-select shadow-sm" wire:model="employeeId" required>
                                    <option value="" selected>Select Employee</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                                @error('employeeId')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Date --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control shadow-sm" wire:model="manualDate"
                                    required>
                                @error('manualDate')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Clock In --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Clock In <span class="text-danger">*</span></label>
                                <input type="time" class="form-control shadow-sm" wire:model="clockInTime"
                                    required>
                                @error('clockInTime')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Clock Out --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Clock Out</label>
                                <input type="time" class="form-control shadow-sm" wire:model="clockOutTime">
                                @error('clockOutTime')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Reason --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Reason</label>
                                <textarea class="form-control shadow-sm" wire:model="reason" placeholder="Optional reason"></textarea>
                                @error('reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="submitManualEntry">
                            <span wire:loading wire:target="submitManualEntry">
                                <i class="fas fa-spinner fa-spin me-2"></i> Submitting...
                            </span>
                            <span wire:loading.remove wire:target="submitManualEntry">Submit</span>
                        </button>
                    </div>
                </form>
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
