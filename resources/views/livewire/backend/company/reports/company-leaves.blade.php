<div>
    <div class="row">
        <div class="card border-0 shadow-sm p-4">
            <div class="row g-4">

                <div class="col-auto">
                    <h5 class="fw-500 text-primary m-0">Leaves Report</h5>
                </div>

                <div class="col-md-12 mb-4">
                    <label class="form-label small fw-bolder text-uppercase text-muted tracking-wider mb-2 d-block">
                        Employee Status
                    </label>
                    <div class="d-inline-flex p-1 bg-light rounded-3 border">
                        <div class="me-1">
                            <input type="radio"
                                   class="btn-check"
                                   wire:model.live="status"
                                   id="active"
                                   value="active"
                                   autocomplete="off">
                            <label class="btn btn-sm px-4 py-2 rounded-2 border-0 shadow-none transition-all status-toggle-label"
                                   for="active">
                                <i class="fas fa-circle shadow-sm me-2 text-success small"></i>Active
                            </label>
                        </div>

                        <div>
                            <input type="radio"
                                   class="btn-check"
                                   wire:model.live="status"
                                   id="former"
                                   value="former"
                                   autocomplete="off">
                            <label class="btn btn-sm px-4 py-2 rounded-2 border-0 shadow-none transition-all status-toggle-label"
                                   for="former">
                                <i class="fas fa-circle shadow-sm me-2 text-secondary small"></i>Former
                            </label>
                        </div>
                    </div>
                </div>

                <style>
                    .btn-check:checked+.status-toggle-label {
                        background-color: white !important;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08) !important;
                        color: #0d6efd !important;
                        font-weight: 600;
                    }

                    .status-toggle-label {
                        color: #6c757d;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        white-space: nowrap;
                    }

                    .status-toggle-label:hover {
                        color: #333;
                        background-color: rgba(255, 255, 255, 0.5);
                    }

                    .transition-all {
                        transition: all 0.2s ease-in-out;
                    }
                </style>

                {{-- Employees --}}
                <div class="col-md-6"
                     wire:ignore.self>
                    <label class="form-label fw-bold text-secondary">Select Employees</label>

                    <div class="dropdown">
                        <button class="btn btn-white border w-100 d-flex justify-content-between align-items-center py-2"
                                type="button"
                                data-bs-toggle="dropdown"
                                data-bs-auto-close="outside">

                            <span class="text-truncate"
                                  style="max-width:85%;">
                                @if (count($selectedEmployees) === 0)
                                    <span class="text-muted">Select employees...</span>
                                @elseif (count($selectedEmployees) <= 2)
                                    {{ $employees->whereIn('id', $selectedEmployees)->pluck('full_name')->join(', ') }}
                                @else
                                    {{ $employees->whereIn('id', $selectedEmployees)->pluck('full_name')->take(2)->join(', ') }}
                                    <span class="text-muted">
                                        +{{ count($selectedEmployees) - 2 }} more
                                    </span>
                                @endif
                            </span>

                            <i class="fas fa-chevron-down small text-muted"></i>
                        </button>

                        <div class="dropdown-menu p-3 w-100 shadow-lg border-0"
                             style="min-width:300px;">
                            @if ($employees->count() > 0)
                                <div class="form-check mb-2 pb-2 border-bottom">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:model.live="selectAllUsers"
                                           id="emp-all">
                                    <label class="form-check-label fw-bold"
                                           for="emp-all">
                                        Select All
                                    </label>
                                </div>
                            @endif

                            <div class="overflow-auto no-scrollbar"
                                 style="max-height:200px; scrollbar-width:none; -ms-overflow-style:none;">

                                @if ($employees->count() == 0)
                                    <div class="dropdown-item text-muted">
                                        No employees found
                                    </div>
                                @else
                                    @foreach ($employees as $emp)
                                        <div class="form-check dropdown-item rounded-2 py-1 ms-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   value="{{ $emp->id }}"
                                                   wire:model.live="selectedEmployees"
                                                   id="emp-{{ $emp->id }}">
                                            <label class="form-check-label w-100"
                                                   for="emp-{{ $emp->id }}">
                                                {{ $emp->full_name }}
                                            </label>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-bolder text-uppercase text-muted tracking-wider mb-2">
                        <i class="fas fa-tag me-1"></i> Leave Category
                    </label>

                    <div class="d-flex gap-2">
                        {{-- Paid Leave Toggle --}}
                        <div class="flex-fill">
                            <input type="checkbox"
                                   class="btn-check"
                                   id="paid"
                                   value="paid"
                                   wire:model.live="leaveCategory"
                                   autocomplete="off">

                            <label for="paid"
                                   class="w-100 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2"
                                   onmouseover="this.style.border='2px dashed #198754'; this.style.background='#f0fff7'; this.style.color='#198754';"
                                   onmouseout="this.style.border='2px dashed {{ in_array('paid', $leaveCategory ?? []) ? '#198754' : '#ced4da' }}'; this.style.background='{{ in_array('paid', $leaveCategory ?? []) ? '#f0fff7' : '#fff' }}'; this.style.color='{{ in_array('paid', $leaveCategory ?? []) ? '#198754' : '#6c757d' }}';"
                                   style="
                        cursor:pointer;
                        border:2px dashed {{ in_array('paid', $leaveCategory ?? []) ? '#198754' : '#ced4da' }};
                        background: {{ in_array('paid', $leaveCategory ?? []) ? '#f0fff7' : '#fff' }};
                        color: {{ in_array('paid', $leaveCategory ?? []) ? '#198754' : '#6c757d' }};
                        font-weight:600;
                        transition:.2s;
                   ">
                                <i class="fas fa-check-circle"></i>
                                Paid
                            </label>
                        </div>

                        {{-- Unpaid Leave Toggle --}}
                        <div class="flex-fill">
                            <input type="checkbox"
                                   class="btn-check"
                                   id="unpaid"
                                   value="unpaid"
                                   wire:model.live="leaveCategory"
                                   autocomplete="off">

                            <label for="unpaid"
                                   class="w-100 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center gap-2"
                                   onmouseover="this.style.border='2px dashed #dc3545'; this.style.background='#fff5f5'; this.style.color='#dc3545';"
                                   onmouseout="this.style.border='2px dashed {{ in_array('unpaid', $leaveCategory ?? []) ? '#dc3545' : '#ced4da' }}'; this.style.background='{{ in_array('unpaid', $leaveCategory ?? []) ? '#fff5f5' : '#fff' }}'; this.style.color='{{ in_array('unpaid', $leaveCategory ?? []) ? '#dc3545' : '#6c757d' }}';"
                                   style="
                        cursor:pointer;
                        border:2px dashed {{ in_array('unpaid', $leaveCategory ?? []) ? '#dc3545' : '#ced4da' }};
                        background: {{ in_array('unpaid', $leaveCategory ?? []) ? '#fff5f5' : '#fff' }};
                        color: {{ in_array('unpaid', $leaveCategory ?? []) ? '#dc3545' : '#6c757d' }};
                        font-weight:600;
                        transition:.2s;
                   ">
                                <i class="fas fa-times-circle"></i>
                                Unpaid
                            </label>
                        </div>
                    </div>
                </div>



                {{-- Time Period Section --}}

                <div class="col-md-12">
                    <label class="form-label small fw-bolder text-uppercase text-muted tracking-wider mb-2 d-block">
                        <i class="fas fa-calendar-alt me-1"></i> Time Period
                    </label>

                    <div class="bg-light p-3 rounded-3 border border-dashed">
                        {{-- Modern Segmented Control --}}
                        <div class="d-inline-flex p-1 bg-white border rounded-pill mb-3 shadow-sm">
                            @foreach (['custom', 'week', 'month', 'year'] as $type)
                                <button type="button"
                                        class="btn btn-sm px-4 py-1 rounded-pill border-0 transition-all ms-2 {{ $dateRangeType == $type ? 'btn-primary shadow-sm fw-bold' : 'btn-white text-muted hover-bg-light' }}"
                                        wire:click="$set('dateRangeType','{{ $type }}')">
                                    {{ ucfirst($type) }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Smoothly Swap Inputs based on selection --}}
                        <div class="row g-2 align-items-center">
                            @if ($dateRangeType == 'custom')
                                <div class="col-md-5">
                                    <div class="input-group shadow-sm">
                                        <span
                                              class="input-group-text bg-white border-end-0 text-muted small">From</span>
                                        <input type="date"
                                               class="form-control border-start-0 ps-0 fw-medium"
                                               wire:model.live="startDate">
                                    </div>
                                </div>
                                <div class="col-md-1 text-center text-muted">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted small">To</span>
                                        <input type="date"
                                               class="form-control border-start-0 ps-0 fw-medium"
                                               wire:model.live="endDate">
                                    </div>
                                </div>
                            @elseif($dateRangeType == 'month')
                                <div class="col-md-7">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0 text-muted">
                                            <i class="far fa-calendar"></i>
                                        </span>
                                        <select class="form-select border-start-0 ps-0 fw-medium"
                                                wire:model.live="selectedMonth">
                                            @foreach (range(1, 12) as $m)
                                                <option value="{{ $m }}">
                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <select class="form-select shadow-sm fw-medium"
                                            wire:model.live="selectedYear">
                                        @foreach (range(now()->year - 2, now()->year + 4) as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($dateRangeType == 'year')
                                <div class="col-md-6">
                                    <select class="form-select shadow-sm fw-medium"
                                            wire:model.live="selectedYear">
                                        @foreach (range(now()->year - 2, now()->year + 4) as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <style>
                    /* Styling to match your previous professional elements */
                    .hover-bg-light:hover {
                        background-color: #f8f9fa !important;
                        color: #333 !important;
                    }

                    .border-dashed {
                        border-style: dashed !important;
                        border-width: 2px !important;
                        border-color: #dee2e6 !important;
                    }

                    .form-control,
                    .form-select {
                        border-color: #dee2e6;
                        padding: 0.6rem 0.75rem;
                    }

                    .form-control:focus,
                    .form-select:focus {
                        border-color: #0d6efd;
                        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.05);
                    }

                    .input-group-text {
                        border-color: #dee2e6;
                        font-weight: 500;
                    }

                    .transition-all {
                        transition: all 0.2s ease-in-out;
                    }
                </style>


                @php
                    $canGenerate =
                        count($selectedEmployees) > 0 &&
                        count($leaveCategory) > 0 &&
                        (($dateRangeType === 'custom' && $startDate && $endDate) || $dateRangeType !== 'custom');
                @endphp

                <div class="col-md-12 text-end border-top pt-4">
                    <button class="btn btn-primary btn-sm px-4 py-2 shadow-sm"
                            type="button"
                            {{ $canGenerate ? '' : 'disabled' }}
                            data-bs-toggle="modal"
                            data-bs-target="#generateReportModal"
                            style="opacity: {{ $canGenerate ? '1' : '.5' }};
                   cursor: {{ $canGenerate ? 'pointer' : 'not-allowed' }};">
                        <i class="fas fa-file-export me-2"></i>
                        Generate Report
                    </button>
                </div>

            </div>
        </div>
    </div>

    @include('livewire.backend.company.components.file-type-view')
</div>
