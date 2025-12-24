<div>
    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-auto">
            <h5 class="fw-500">My Leave Requests</h5>
        </div>

        <div class="col-auto d-flex gap-2">
            <button wire:click="exportLeaveEmp('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportLeaveEmp('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportLeaveEmp('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>



        <div class="col-auto">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLeave"
                wire:click="resetInputFields">
                <i class="fa fa-plus me-1"></i> New Leave Request
            </button>


        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Remaining Hours Info -->


            <!-- Search + Sort -->
            <div class="row mb-3 align-items-start g-3">

                <!-- Search -->
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by leave type"
                        wire:model="search" wire:keyup="set('search', $event.target.value)" />
                </div>

                <!-- Sort -->
                <div class="col-md-3">
                    <select class="form-select form-select-lg" wire:change="handleSort($event.target.value)">
                        <option value="desc">Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                </div>

                <!-- Leave Badges -->
                <div class="col-md-6 d-flex gap-3 flex-row">

                    <!-- Annual Leave Card -->
                    <div class="p-3 rounded shadow-sm bg-light border flex-grow-1">
                        <h6 class="mb-2 text-uppercase fw-bold text-success">
                            <i class="fas fa-calendar-check me-1"></i> Annual Leave
                        </h6>
                        <div class="d-flex justify-content-start align-items-center flex-wrap gap-2">
                            <span class="badge bg-success">Total: {{ number_format($totalAnnualHours, 2) }}</span>
                            <span class="badge bg-warning text-dark">Used:
                                {{ number_format($usedAnnualHours, 2) }}</span>
                            <span class="badge bg-info text-dark">Remaining:
                                {{ number_format($remainingAnnualHours, 2) }}</span>
                        </div>
                    </div>

                    <!-- Leave in Liew Card -->
                    <div class="p-3 rounded shadow-sm bg-light border flex-grow-1">
                        <h6 class="mb-2 text-uppercase fw-bold text-primary">
                            <i class="fas fa-hourglass-half me-1"></i> Leave In Liew
                        </h6>
                        <div class="d-flex justify-content-start align-items-center flex-wrap gap-2">
                            <span class="badge bg-primary">Total: {{ number_format($totalLeaveInLiewHours, 2) }}</span>
                            <span class="badge bg-secondary">Used: {{ number_format($usedLeaveInLiewHours, 2) }}</span>
                            <span class="badge bg-dark">Remaining:
                                {{ number_format($remainingLeaveInLiewHours, 2) }}</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Leave Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Total Hours</th>
                            <th>Paid Status</th>
                            <th>Paid Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $i => $leave)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {!! ($leave->leaveType->emoji ?? '') . ' ' . ($leave->leaveType->name ?? 'N/A') !!}
                                </td>

                                <td>{{ $leave->start_date ? date('d M, Y', strtotime($leave->start_date)) : 'N/A' }}
                                </td>
                                <td>{{ $leave->end_date ? date('d M, Y', strtotime($leave->end_date)) : 'N/A' }}</td>

                                <td>{{ $leave->total_hours }}</td>
                                <td>
                                    @if ($leave->paid_status === 'paid')
                                        <span class="badge bg-primary">Paid</span>
                                    @elseif ($leave->paid_status === 'unpaid')
                                        <span class="badge bg-secondary">Unpaid</span>
                                    @else
                                        <span class="badge bg-light text-muted">N/A</span>
                                    @endif
                                </td>


                                <td>
                                    @if ($leave->paid_hours > 0)
                                        <span class="badge bg-info text-dark">{{ $leave->paid_hours }} hrs</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>


                                <td>
                                    @if ($leave->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($leave->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">No leave requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($hasMore)
            <div class="text-center mt-4">
                <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">
                    Load More
                </button>
            </div>
        @endif

    </div>

    <!-- Add Leave Modal -->
    <div wire:ignore.self class="modal fade" id="addLeave" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="save">
                    <div class="modal-header">
                        <h6 class="modal-title">New Leave Request</h6>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:none;">
                            <i class="fas fa-times" style="color:black;"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Leave Type <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.live="leave_type_id" wire:key="leave_type_id">
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


                        <div class="mb-2">
                            <label>Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="start_date">
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label>End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="end_date">
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @error('remaining')
                            <div class="text-danger mb-2">{{ $message }}</div>
                        @enderror
                    </div>




                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-2"></i> Submitting ...
                            </span>
                            <span wire:loading.remove wire:target="save">Submit Request</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
