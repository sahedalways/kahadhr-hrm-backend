<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Employees Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportEmployees('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>
            <button wire:click="exportEmployees('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <button wire:click="exportEmployees('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    toastr.success(@json(session('success')), '', {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 5000,
                        positionClass: "toast-top-right"
                    });
                });
            </script>
        @endif


        <div class="col-auto">
            <button class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#customFieldModal">
                <i class="fa fa-sliders-h me-1"></i> Custom Fields
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#add"
               wire:click="resetInputFields"
               class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Employee
            </a>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-8 col-md-6 col-12">
                    <input type="text"
                           class="form-control shadow-sm"
                           placeholder="Search by name, email, job title"
                           wire:model="search"
                           wire:keyup="set('search', $event.target.value)">
                </div>

                <div class="col-lg-4 col-md-6 col-12 d-flex gap-2">
                    <select class="form-select"
                            wire:change="handleSort($event.target.value)">
                        <option value="desc">Newest First</option>
                        <option value="asc">Oldest First</option>
                    </select>
                    <select class="form-select"
                            wire:change="handleFilter($event.target.value)">
                        <option value="">All Status</option>
                        <option value="active"
                                selected>Active Member</option>
                        <option value="former">Former Member</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>

                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Job Title</th>
                                        <th>Right to Work Expires</th>


                                    </tr>
                                </thead>
                                @php
                                    $activeCount = $infos->where('is_active', 1)->count();
                                    $formerCount = $infos->where('is_active', 0)->count();
                                @endphp
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($infos as $employee)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>
                                                <a href="{{ route('company.dashboard.employees.details', [
                                                    'company' => app('authUser')->company->sub_domain,
                                                    'employee' => $employee->id,
                                                ]) }}"
                                                   class="badge badge-xs text-primary"
                                                   style="
        background: transparent;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
   "
                                                   onmouseover="this.style.textDecoration='underline'"
                                                   onmouseout="this.style.textDecoration='none'">
                                                    {{ $employee->full_name ?? 'N/A' }}
                                                </a>


                                            </td>

                                            <td>
                                                <span onclick="copyToClipboard('{{ $employee->email ?? '' }}')"
                                                      style="cursor:pointer; padding:2px 4px; border-radius:4px;"
                                                      onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                      onmouseout="this.style.backgroundColor='transparent';"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="top"
                                                      title="Click to copy"
                                                      aria-label="copy email">
                                                    {{ $employee->email ?? 'N/A' }}
                                                </span>
                                            </td>

                                            <td>
                                                <span onclick="copyToClipboard('{{ $employee->phone_no ?? '' }}')"
                                                      style="cursor:pointer; padding:2px 4px; border-radius:4px;"
                                                      onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                      onmouseout="this.style.backgroundColor='transparent';"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="top"
                                                      title="Click to copy"
                                                      aria-label="copy phone number">
                                                    {{ $employee->phone_no ?? 'N/A' }}
                                                </span>
                                            </td>

                                            <td>{{ $employee->job_title ?? 'N/A' }}</td>



                                            @php
                                                $shareCodeType = $documentTypes->firstWhere('name', 'Share Code');

                                                $latestShareDoc = null;
                                                $statusLabel = null;
                                                $statusColor = null;

                                                if ($shareCodeType) {
                                                    $latestShareDoc = $employee
                                                        ->documents()
                                                        ->where('doc_type_id', $shareCodeType->id)
                                                        ->latest('created_at')
                                                        ->first();

                                                    if ($latestShareDoc && $latestShareDoc->expires_at) {
                                                        $expiresAt = \Carbon\Carbon::parse($latestShareDoc->expires_at);
                                                        $daysLeft = now()->diffInDays($expiresAt, false);

                                                        if ($daysLeft < 0) {
                                                            $statusLabel = 'Expired';
                                                            $statusColor = '#dc3545';
                                                        } elseif ($daysLeft <= 60) {
                                                            $statusLabel = 'Expires Soon';
                                                            $statusColor = '#fd7e14';
                                                        } else {
                                                            $statusLabel = 'Valid';
                                                            $statusColor = '#198754';
                                                        }
                                                    }
                                                }
                                            @endphp




                                            @if ($employee->nationality === 'British')
                                                <td>
                                                    <span class="badge"
                                                          style="background:#0d6efd; color:#fff; font-weight:600;">
                                                        Permanent
                                                    </span>
                                                </td>
                                            @else
                                                @if ($latestShareDoc && $latestShareDoc->expires_at)
                                                    <td>
                                                        <div style="display:flex; flex-direction:column; gap:4px;">
                                                            <span style="font-size:12px; color:#6c757d;">

                                                                <strong>{{ \Carbon\Carbon::parse($latestShareDoc->expires_at)->format('d M, Y') }}</strong>
                                                            </span>

                                                            <div
                                                                 style="display:flex; align-items:center; justify-content:center;">
                                                                <span class="badge text-center"
                                                                      style="
              background: {{ $statusColor }};
              color:#fff;
              font-weight:600;
              min-width:110px;
          ">
                                                                    {{ $statusLabel }}
                                                                </span>
                                                            </div>

                                                        </div>
                                                    </td>
                                                @else
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            Not Verified
                                                        </span>
                                                    </td>
                                                @endif
                                            @endif

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9"
                                                class="text-center">No employees found</td>
                                        </tr>

                                    @endforelse
                                </tbody>


                            </table>



                            @if ($hasMore)
                                <div class="text-center my-3">
                                    <button wire:click="loadMore"
                                            class="btn btn-outline-primary">Load More</button>
                                </div>
                            @endif
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 d-flex gap-3 justify-content-center">
                                @if ($statusFilter == 'active')
                                    <div class="px-4 py-2 rounded-pill shadow-sm small"
                                         style="background-color: #e9f5ee; color: #1b5e20; font-weight: 600; border: 1px solid #d1e7dd;">
                                        Total Active Employees: {{ $activeCount }}
                                    </div>
                                @else
                                    <div class="px-4 py-2 rounded-pill shadow-sm small"
                                         style="background-color: #fce8e8; color: #c62828; font-weight: 600; border: 1px solid #f8d7da;">
                                        Total Former Employees: {{ $formerCount }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <script>
                            function copyToClipboard(text) {
                                navigator.clipboard.writeText(text).then(function() {
                                    // Success feedback here
                                }, function(err) {
                                    console.error('Could not copy text: ', err);
                                });
                            }

                            // Initialize tooltips (requires Bootstrap JS)
                            document.addEventListener('DOMContentLoaded', function() {
                                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl)
                                })
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div wire:ignore.self
         class="modal fade"
         id="add"
         tabindex="-1"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add Employee</h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">


                    <!-- Select Add Method -->
                    <div class="mb-3">
                        <label class="form-label">Add Employee Via</label>
                        <select class="form-select"
                                wire:model.live="addMethod"
                                wire:key="addMethod">
                            <option value="manual">Add an employee</option>
                            <option value="csv">Add
                                multiple employees</option>
                        </select>
                    </div>

                    <!-- Conditional: CSV Import -->
                    @if ($addMethod === 'csv')
                        <div class="card shadow-sm border-0 mb-4"
                             wire:key="csv-field">
                            <div class="card-body">

                                <h6 class="fw-semibold mb-3">
                                    <i class="fas fa-file-csv text-success me-2"></i>
                                    Bulk Employee Upload
                                </h6>

                                <div class="mb-3">
                                    <label class="form-label fw-medium">
                                        Upload CSV File <span class="text-danger">*</span>
                                    </label>

                                    <input type="file"
                                           wire:model="csv_file"
                                           accept=".csv"
                                           class="form-control">

                                    @error('csv_file')
                                        <div class="text-danger small mt-1">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="bg-light border rounded p-3 mb-3">
                                    <small class="text-muted d-block mb-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        CSV file must contain the following headers:
                                    </small>

                                    <code class="d-block text-dark">
                                        f_name, l_name, email, phone_no, nationality, date_of_birth,
                                        employment_status,contract_hours
                                    </code>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <a href="{{ route('employees.csv.template') }}"
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-download me-1"></i>
                                        Download CSV Template
                                    </a>

                                    @if ($csv_file)
                                        <span class="text-success small">
                                            <i class="fas fa-check-circle me-1"></i>
                                            File selected successfully
                                        </span>
                                    @endif
                                </div>

                            </div>
                        </div>



                        <button class="btn btn-primary"
                                wire:click="importCsv"
                                wire:loading.attr="disabled">
                            <span wire:loading
                                  wire:target="importCsv"><i
                                   class="fas fa-spinner fa-spin me-2"></i>Importing...</span>
                            <span wire:loading.remove
                                  wire:target="importCsv">Import CSV</span>
                        </button>
                    @endif

                    <!-- Conditional: Manual Entry -->
                    @if ($addMethod === 'manual')
                        <form wire:submit.prevent="submitEmployee"
                              wire:key="manual-field">
                            <div class="row g-2">

                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           wire:model="f_name"
                                           placeholder="Enter first name">
                                    @error('f_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           wire:model="l_name"
                                           placeholder="Enter last name">
                                    @error('l_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control"
                                           wire:model="email"
                                           placeholder="Enter email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="col-md-6">
                                    <label class="form-label">Mobile <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control "
                                           wire:model="phone_no"
                                           placeholder="Enter mobile no."
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('phone_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="col-md-6 mt-2">
                                    <label class="form-label">
                                        Date of Birth <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           class="form-control"
                                           wire:model="date_of_birth"
                                           max="{{ date('Y-m-d') }}">

                                    @error('date_of_birth')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>





                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nationality <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                            wire:model.live="nationality">
                                        @foreach ($nationalities as $nation)
                                            <option value="{{ $nation }}">{{ $nation }}</option>
                                        @endforeach
                                    </select>

                                    @error('nationality')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                @if ($nationality && $nationality !== 'British')
                                    <div class="col-md-6 mt-2">
                                        <label class="form-label">
                                            Share Code
                                        </label>
                                        <input type="text"
                                               class="form-control"
                                               wire:model.live="share_code"
                                               placeholder="Example: WLE JFZ 6FT">

                                        @error('share_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif


                                <div class="col-md-6">
                                    <label class="form-label">
                                        Employment Status <span class="text-danger">*</span>
                                    </label>

                                    <select class="form-select"
                                            wire:model.live="employment_status">
                                        <option value="full-time">Full-time</option>
                                        <option value="part-time">Part-time</option>
                                    </select>

                                    @error('employment_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>



                                <div class="col-md-6"
                                     wire:key="contract-hours-field">
                                    <label class="form-label">Contract Hours (Weekly)</label>
                                    <input type="number"
                                           step="0.01"
                                           class="form-control"
                                           wire:model="contract_hours"
                                           placeholder="Enter contract hours">
                                    @error('contract_hours')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                <button type="submit"
                                        class="btn btn-success"
                                        wire:loading.attr="disabled"
                                        wire:target="submitEmployee">
                                    <span wire:loading
                                          wire:target="submitEmployee"><i
                                           class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                                    <span wire:loading.remove
                                          wire:target="submitEmployee">Save</span>
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>




    <div wire:ignore.self
         class="modal fade"
         id="customFieldModal"
         tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add Custom Employee Field</h6>
                    <button class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Field Label</label>
                        <input type="text"
                               class="form-control"
                               wire:model="customField.label"
                               placeholder="e.g. Emergency Contact Name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Field Type</label>
                        <select class="form-select"
                                wire:model="customField.type">
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Dropdown</option>
                        </select>
                    </div>

                    @if ($customField['type'] === 'select')
                        <div class="mb-3">
                            <label class="form-label">Options (comma separated)</label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="customField.options"
                                   placeholder="A+, B+, O+">
                        </div>
                    @endif
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               id="requiredField"
                               wire:model="customField.required">
                        <label class="form-check-label"
                               for="requiredField">
                            Required Field
                        </label>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <div class="modal-footer">


                        <button class="btn btn-primary"
                                wire:click="saveCustomField"
                                wire:loading.attr="disabled"
                                wire:target="saveCustomField">


                            <span wire:loading
                                  wire:target="saveCustomField">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>


                            <span wire:loading.remove
                                  wire:target="saveCustomField">
                                Save Field
                            </span>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>

<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.error(err));
    }
</script>
