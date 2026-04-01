<div wire:ignore.self
     class="modal fade"
     id="editLeaveModal"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="editLeaveModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold"
                    id="editLeaveModalLabel">Edit Leave</h5>
                <button type="button"
                        class="btn btn-light rounded-pill"
                        data-bs-dismiss="modal"
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
                        <div class="me-3 fs-5"
                             style="min-width: 25px;">
                            {!! $calendarLeaveInfo->leaveType->emoji !!}
                        </div>
                        <div class="fw-medium text-dark flex-grow-1">
                            {{ $calendarLeaveInfo->leaveType->name ?? 'N/A' }}
                        </div>
                    </div>

                    <!-- 3. Reason/Description -->


                    <div
                         class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                        <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon"
                           style="min-width: 25px;"></i>
                        <div class="fw-normal text-dark flex-grow-1">
                            {{ $calendarLeaveInfo->other_reason ?: ($calendarLeaveInfo->reason ?: '-') }}
                        </div>
                    </div>

                    <!-- 4. Editable Date Range -->
                    <div class="row g-2 mb-4 px-2">
                        <div class="col-12">
                            <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   wire:model="editStartDate"
                                   min="{{ date('Y-m-d') }}">
                            @error('editStartDate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   class="form-control"
                                   wire:model="editEndDate"
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
                <button type="button"
                        class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Cancel</button>

                <button type="button"
                        class="btn btn-primary rounded-pill px-4"
                        wire:click="updateLeave({{ $calendarLeaveInfo->id ?? 0 }})"
                        wire:loading.attr="disabled"
                        wire:target="updateLeave">
                    <span wire:loading.remove
                          wire:target="updateLeave">
                        <i class="fas fa-save me-1"></i> Save Changes
                    </span>
                    <span wire:loading
                          wire:target="updateLeave">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        Saving...
                    </span>
                </button>
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
                    id="viewRequestInfoLabel">Leave Request</h5>
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

                    <!-- 2. Leave Type -->
                    <div class="d-flex align-items-center mb-3 bg-light p-3 rounded-3 shadow-sm info-card border">
                        <div class="me-3 fs-5"
                             style="min-width: 25px;">
                            {!! $requestDetails->leaveType->emoji !!}
                        </div>
                        <div class="fw-medium text-dark flex-grow-1">
                            {{ $requestDetails->leaveType->name ?? 'N/A' }}
                        </div>
                    </div>


                    <!-- 3. Reason/Description -->
                    <div
                         class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                        <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon"
                           style="min-width: 25px;"></i>
                        <div class="fw-normal text-dark flex-grow-1">
                            {{ $requestDetails->other_reason ?: ($requestDetails->reason ?: '-') }}
                        </div>
                    </div>

                    <!-- 4. Date Range -->
                    <div class="row g-2 mb-4 px-2">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <div class="text-muted fw-medium me-3"
                                     style="min-width: 50px;">From</div>
                                <div class="fw-bold text-dark flex-grow-1">
                                    {{ \Carbon\Carbon::parse($requestDetails->start_date)->format('D, d M') }}
                                </div>
                                <div class="badge bg-success-subtle text-success fw-bold p-2">Morning</div>
                            </div>
                        </div>
                        <hr class="text-light-subtle my-0">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <div class="text-muted fw-medium me-3"
                                     style="min-width: 50px;">To</div>
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
                            <select class="form-select"
                                    wire:model.live="paidStatus"
                                    @if ($requestDetails->leave_type_id == 4) disabled @endif>
                                <option value="">Select Status</option>
                                <option value="paid">Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>

                        @if ($paidStatus === 'paid')
                            <div class="mb-3">
                                <label class="form-label fw-bold">Hours <span class="text-danger">*</span></label>
                                <input type="number"
                                       class="form-control"
                                       min="0"
                                       step="0.25"
                                       wire:model.defer="paidHours"
                                       placeholder="Enter leave hours">
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
                <button type="button"
                        class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <div>
                    @if ($requestDetails)

                        @if ($requestDetails->status === 'pending')
                            <div>
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
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Rejecting...
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
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        Approving...
                                    </span>
                                </button>
                            </div>
                        @else
                            <span
                                  class="badge
                @if ($requestDetails->status === 'approved') bg-success
                @elseif ($requestDetails->status === 'rejected') bg-danger
                @elseif ($requestDetails->status === 'cancelled') bg-secondary
                @else bg-info @endif
                px-3 py-2 fs-6">
                                {{ ucfirst($requestDetails->status) }}
                            </span>
                        @endif

                    @endif
                </div>

            </div>
        </div>
    </div>
</div>



<div wire:ignore.self
     class="modal fade"
     id="viewRequestInfoFromCalendar"
     data-bs-backdrop="static"
     tabindex="-1"
     aria-labelledby="viewRequestInfoFromCalendar"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold"
                    id="viewRequestInfoFromCalendar">Leave Request</h5>
                <button type="button"
                        class="btn btn-light rounded-pill"
                        data-bs-dismiss="modal"
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
                        <div class="me-3 fs-5"
                             style="min-width: 25px;">
                            {!! $calendarLeaveInfo->leaveType->emoji !!}
                        </div>
                        <div class="fw-medium text-dark flex-grow-1">
                            {{ $calendarLeaveInfo->leaveType->name ?? 'N/A' }}
                        </div>
                    </div>


                    <!-- 3. Reason/Description -->

                    <div
                         class="d-flex align-items-center mb-4 bg-primary-subtle p-3 rounded-3 shadow-sm border border-primary-subtle">
                        <i class="fas fa-comment-alt me-3 fs-5 text-primary-icon"
                           style="min-width: 25px;"></i>
                        <div class="fw-normal text-dark flex-grow-1">
                            {{ $calendarLeaveInfo->other_reason ?: ($calendarLeaveInfo->reason ?: '-') }}
                        </div>
                    </div>

                    <!-- 4. Date Range -->
                    <div class="row g-2 mb-4 px-2">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <div class="text-muted fw-medium me-3"
                                     style="min-width: 50px;">From</div>
                                <div class="fw-bold text-dark flex-grow-1">
                                    {{ \Carbon\Carbon::parse($calendarLeaveInfo->start_date)->format('D, d M') }}
                                </div>
                                <div class="badge bg-success-subtle text-success fw-bold p-2">Morning</div>
                            </div>
                        </div>
                        <hr class="text-light-subtle my-0">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-2">
                                <div class="text-muted fw-medium me-3"
                                     style="min-width: 50px;">To</div>
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
                    <button type="button"
                            class="btn btn-outline-danger rounded-pill px-4"
                            onclick="confirmCancel({{ $calendarLeaveInfo->id }})"
                            wire:loading.attr="disabled"
                            wire:target="cancelLeave">

                        <span wire:loading.remove
                              wire:target="cancelLeave">
                            <i class="fas fa-ban me-1"></i> Cancel Leave
                        </span>

                        <span wire:loading
                              wire:target="cancelLeave">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Cancelling...
                        </span>
                    </button>

                    <!-- Right: Edit Leave -->
                    <button type="button"
                            class="btn btn-primary rounded-pill px-4"
                            wire:click="editLeave({{ $calendarLeaveInfo->id }})"
                            data-bs-toggle="modal"
                            data-bs-target="#editLeaveModal">

                        <i class="fas fa-edit me-1"></i> Edit Leave
                    </button>

                </div>
            @endif

        </div>
    </div>
</div>
