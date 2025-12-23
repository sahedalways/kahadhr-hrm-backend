@php
    $summary = $this->calcCalendarSummary();
@endphp

<div class="unique-summary-wrapper mt-auto border-top bg-white py-4 px-3 rounded-bottom">

    <div class="d-flex align-items-center mb-3 px-1">
        <div class="flex-grow-1 border-bottom me-3" style="opacity: 0.1;"></div>
        <h6 class="text-muted fw-bold text-uppercase mb-0 small" style="letter-spacing: 1px;">
            {{ $viewMode == 'monthly' ? 'Monthly Overview' : 'Weekly Overview' }}
        </h6>
        <div class="flex-grow-1 border-bottom ms-3" style="opacity: 0.1;"></div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="unique-summary-card d-flex align-items-center p-3 shadow-sm border rounded">
                <div
                    class="unique-summary-icon-box bg-primary-subtle text-primary me-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-calendar-check fa-lg"></i>
                </div>
                <div>
                    <div class="unique-summary-value fw-bold fs-4 text-dark">{{ $summary['shifts'] }}</div>
                    <div class="unique-summary-label text-muted small text-uppercase fw-semibold">Total Shifts</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="unique-summary-card d-flex align-items-center p-3 shadow-sm border rounded">
                <div
                    class="unique-summary-icon-box bg-info-subtle text-info me-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-stopwatch fa-lg"></i>
                </div>
                <div>
                    <div class="unique-summary-value fw-bold fs-4 text-dark">{{ $summary['hours'] }}</div>
                    <div class="unique-summary-label text-muted small text-uppercase fw-semibold">Total Hours</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="unique-summary-card d-flex align-items-center p-3 shadow-sm border rounded">
                <div
                    class="unique-summary-icon-box bg-success-subtle text-success me-3 rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <div>
                    <div class="unique-summary-value fw-bold fs-4 text-dark">{{ $summary['users'] }}</div>
                    <div class="unique-summary-label text-muted small text-uppercase fw-semibold">Active Users</div>
                </div>
            </div>
        </div>
    </div>
</div>
