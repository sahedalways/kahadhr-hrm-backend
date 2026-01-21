@php
    $id = request('id');
@endphp



<div>
    <!-- Header -->
    <div class="row mb-3 align-items-center justify-content-between">
        <div class="col-auto">
            <h5 class="fw-500">My Clock In / Out</h5>
        </div>

        <div class="col-auto d-flex gap-2">
            <!-- Export buttons -->
            <button wire:click="exportAttendance('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>
            <button wire:click="exportAttendance('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <button wire:click="exportAttendance('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>
    </div>

    <!-- Search + Sort -->
    <div class="row mb-3 align-items-end mt-4 g-3">
        <div class="col-md-4">
            <label for="startDate"
                   class="form-label fw-bold">Start Date</label>
            <input type="date"
                   id="startDate"
                   class="form-control"
                   wire:model="startDate"
                   wire:change="handleStartDate($event.target.value)" />

        </div>

        <div class="col-md-4">
            <label for="endDate"
                   class="form-label fw-bold">End Date</label>
            <input type="date"
                   id="endDate"
                   class="form-control"
                   wire:model="endDate"
                   wire:change="handleEndDate($event.target.value)" />

        </div>

        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-outline-secondary w-100"
                    wire:click="resetFilters">
                Reset Dates
            </button>
        </div>

        <div class="col-md-2 d-flex justify-content-end">
            <div class="w-100">
                <label for="sortOrder"
                       class="form-label fw-bold">Sort</label>
                <select id="sortOrder"
                        class="form-select form-select-lg"
                        wire:change="handleSort($event.target.value)">
                    <option value="desc">Newest First</option>
                    <option value="asc">Oldest First</option>
                </select>
            </div>
        </div>
    </div>


    <!-- Attendance Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-0 table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Clock In</th>
                            <th>Clock Out</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Late In Request</th>
                            <th>Late In Reason</th>

                            <th>Early Out Request</th>
                            <th>Early Out Reason</th>

                            <th>Late Out Request</th>
                            <th>Late Out Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($infos as $i => $attendance)
                            @php
                                $clockInRequest = \App\Models\AttendanceRequest::where(
                                    'attendance_id',
                                    $attendance['id'],
                                )
                                    ->where('type', 'late_clock_in')
                                    ->latest()
                                    ->first();

                                $earlyClockOutRequest = \App\Models\AttendanceRequest::where(
                                    'attendance_id',
                                    $attendance['id'],
                                )
                                    ->where('type', 'early_clock_out')
                                    ->latest()
                                    ->first();

                                $lateClockOutRequest = \App\Models\AttendanceRequest::where(
                                    'attendance_id',
                                    $attendance['id'],
                                )
                                    ->where('type', 'late_clock_out')
                                    ->latest()
                                    ->first();
                            @endphp

                            <tr class="{{ $id == $attendance['id'] ? 'table-primary' : '' }}">
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $attendance['date'] }}</td>
                                <td>{{ $attendance['clock_in'] }}</td>
                                <td>{{ $attendance['clock_out'] }}</td>
                                <td>{{ $attendance['location'] ?? 'Unknown' }}</td>

                                <!-- Attendance Status -->
                                <td>
                                    @php
                                        $status = strtolower($attendance['status']);
                                        $bgColor =
                                            $status == 'approved'
                                                ? 'success'
                                                : ($status == 'pending'
                                                    ? 'warning'
                                                    : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $bgColor }}">{{ ucfirst($status) }}</span>
                                </td>

                                <!-- Late Clock In -->
                                <td>
                                    @if (optional($clockInRequest)->type)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ optional($clockInRequest)->reason ?? 'N/A' }}</td>

                                <!-- Early Clock Out -->
                                <td>
                                    @if (optional($earlyClockOutRequest)->type)
                                        <span class="badge bg-warning text-dark">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ optional($earlyClockOutRequest)->reason ?? 'N/A' }}</td>

                                <!-- Late Clock Out -->
                                <td>
                                    @if (optional($lateClockOutRequest)->type)
                                        <span class="badge bg-danger">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ optional($lateClockOutRequest)->reason ?? 'N/A' }}</td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="12"
                                    class="text-center">No attendance records found</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            @if ($hasMore)
                <div class="text-center mt-4">
                    <button wire:click="loadMore"
                            class="btn btn-outline-primary rounded-pill px-4 py-2">
                        Load More
                    </button>
                </div>
            @endif
        </div>
    </div>


</div>
