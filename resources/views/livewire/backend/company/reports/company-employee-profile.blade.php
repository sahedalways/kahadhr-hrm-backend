<div class="row">

    <div class="card border-0 shadow-sm p-4">
        <div class="row g-4">

            <div class="col-md-12 mb-4">
                <label class="form-label small fw-bolder text-uppercase text-muted tracking-wider mb-2 d-block">
                    Employee Status
                </label>
                <div class="d-inline-flex p-1 bg-light rounded-3 border">
                    <div class="me-1"> <input type="radio"
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
                               class="btn-check "
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
                /* Premium Selection Style */
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

                .no-scrollbar::-webkit-scrollbar {
                    display: none;
                }
            </style>

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
                <label class="form-label fw-bold text-secondary">Export Fields</label>

                <div class="dropdown">
                    <button class="btn btn-white border w-100 d-flex justify-content-between align-items-center py-2"
                            type="button"
                            data-bs-toggle="dropdown"
                            data-bs-auto-close="outside">

                        <span class="d-flex flex-wrap gap-1 text-truncate"
                              style="max-width:85%;">

                            {{-- When no field selected --}}
                            @if (count($selectedFields) === 0)
                                <span class="text-muted">Choose columns...</span>
                            @else
                                @foreach (collect($selectedFields)->take(6) as $field)
                                    <span class="badge bg-success-subtle text-success fw-normal">
                                        {{ $profileFields[$field] ?? ucwords(str_replace('_', ' ', $field)) }}
                                    </span>
                                @endforeach

                                {{-- Show +x more --}}
                                @if (count($selectedFields) > 6)
                                    <span class="badge bg-light text-muted">
                                        +{{ count($selectedFields) - 6 }} more
                                    </span>
                                @endif
                            @endif

                        </span>



                        <i class="fas fa-columns small text-muted"></i>
                    </button>

                    <div class="dropdown-menu p-3 w-100 shadow-lg border-0"
                         style="min-width:300px;">
                        <div class="form-check mb-2 pb-2 border-bottom">
                            <input class="form-check-input"
                                   type="checkbox"
                                   wire:model.live="selectAllFields"
                                   id="fields-all">
                            <label class="form-check-label fw-bold"
                                   for="fields-all">
                                All Fields
                            </label>
                        </div>

                        <div class="overflow-auto no-scrollbar"
                             style="max-height:200px; scrollbar-width:none; -ms-overflow-style:none;">

                            @foreach ($profileFields as $field => $label)
                                <div class="form-check dropdown-item rounded-2 py-1 ms-2">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           value="{{ $field }}"
                                           wire:model.live="selectedFields"
                                           id="field-{{ $field }}">
                                    <label class="form-check-label w-100"
                                           for="field-{{ $field }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach

                        </div>

                    </div>
                </div>
            </div>




            <div class="col-md-12 border-top pt-4 text-end">
                <button class="btn btn-primary btn-lg shadow-sm px-5"
                        type="button"
                        @if (empty($selectedEmployees) || empty($selectedFields)) disabled @endif
                        data-bs-toggle="modal"
                        data-bs-target="#generateReportModal">
                    <i class="fas fa-file-export me-2"></i>Generate Report
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade"
         id="generateReportModal"
         tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h6 class="modal-title fw-bold">Choose Format</h6>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-light-soft">
                    <div class="row g-3">
                        <div class="col-12">
                            <button wire:click="exportFile('pdf')"
                                    wire:loading.attr="disabled"
                                    wire:target="exportFile('pdf')"
                                    class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between group">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger-soft p-2 rounded-3 me-3 text-danger">
                                        <i class="far fa-file-pdf fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">PDF Document</div>
                                        <small class="text-muted">High-quality print ready</small>
                                    </div>
                                </div>
                                <div wire:loading
                                     wire:target="exportFile('pdf')">
                                    <span class="spinner-border spinner-border-sm text-danger"
                                          role="status"></span>
                                </div>
                                <i class="fas fa-chevron-right text-light group-hover-text-muted d-none d-sm-block"
                                   wire:loading.remove
                                   wire:target="exportFile('pdf')"></i>
                            </button>
                        </div>

                        <div class="col-12">
                            <button wire:click="exportFile('excel')"
                                    wire:loading.attr="disabled"
                                    wire:target="exportFile('excel')"
                                    class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success-soft p-2 rounded-3 me-3 text-success">
                                        <i class="far fa-file-excel fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">Excel Spreadsheet</div>
                                        <small class="text-muted">Detailed data analysis</small>
                                    </div>
                                </div>
                                <div wire:loading
                                     wire:target="exportFile('excel')">
                                    <span class="spinner-border spinner-border-sm text-success"
                                          role="status"></span>
                                </div>
                                <i class="fas fa-chevron-right text-light d-none d-sm-block"
                                   wire:loading.remove
                                   wire:target="exportFile('excel')"></i>
                            </button>
                        </div>

                        <div class="col-12">
                            <button wire:click="exportFile('csv')"
                                    wire:loading.attr="disabled"
                                    wire:target="exportFile('csv')"
                                    class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info-soft p-2 rounded-3 me-3 text-info">
                                        <i class="far fa-file-alt fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0">CSV File</div>
                                        <small class="text-muted">Plain text raw data</small>
                                    </div>
                                </div>
                                <div wire:loading
                                     wire:target="exportFile('csv')">
                                    <span class="spinner-border spinner-border-sm text-info"
                                          role="status"></span>
                                </div>
                                <i class="fas fa-chevron-right text-light d-none d-sm-block"
                                   wire:loading.remove
                                   wire:target="exportFile('csv')"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    window.addEventListener('keep-employee-dropdown-open', function() {
        const empBtn = document.getElementById('employeeDropdownBtn');
        const empDropdown = new bootstrap.Dropdown(empBtn);
        empDropdown.show();
    });

    window.addEventListener('keep-field-dropdown-open', function() {
        const fieldBtn = document.getElementById('fieldDropdownBtn');
        const fieldDropdown = new bootstrap.Dropdown(fieldBtn);
        fieldDropdown.show();
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.dropdown-menu').forEach(function(dropdown) {
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
</script>
