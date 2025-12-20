{{-- ================= FOOTER STATS ================= --}}
<div class="mt-4">
    <div class="row g-3">

        {{-- Total Users --}}
        @if ($viewMode !== 'monthly')
            <div class="col-md-3">
                <div class="card shadow-sm border-0 text-center p-3 h-100">
                    <div class="card-body">
                        <div class="fw-bold text-muted small">Total Users</div>
                        <div class="h4 fw-bold">
                            {{ collect($employees)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif


        {{-- Total Absents --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Total Absents</div>
                    <div class="h4 fw-bold text-danger">{{ $totalAbsents ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Total Leaves --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Total Leaves</div>
                    <div class="h4 fw-bold text-warning">{{ $totalLeaves ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Pending Requests --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Pending Requests</div>
                    <div class="h4 fw-bold text-primary">{{ $totalPending ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Approved</div>
                    <div class="h4 fw-bold text-success">{{ $totalApproved ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Rejected --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Rejected</div>
                    <div class="h4 fw-bold text-danger">{{ $totalRejected ?? 0 }}</div>
                </div>
            </div>
        </div>

        {{-- Total Hours --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0 text-center p-3 h-100">
                <div class="card-body">
                    <div class="fw-bold text-muted small">Total Hours</div>
                    <div class="h4 fw-bold text-secondary">{{ $totalHours ?? '0h 0m' }}</div>
                </div>
            </div>
        </div>

    </div>
</div>
