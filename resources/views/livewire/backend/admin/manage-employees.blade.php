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

                        <option value="active"
                                selected>Active </option>
                        <option value="former">Former </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 shadow-sm border-0">
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered align-middle text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        <th>Company Name</th>


                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Job Title</th>


                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1; @endphp
                                    @forelse($infos as $employee)
                                        <tr>
                                            <td>{{ $i++ }}</td>


                                            <td>
                                                <a href="{{ route('super-admin.dashboard.employees.details', $employee->id) }}"
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


                                            <td style="cursor:pointer; transition: background-color 0.3s;"
                                                onclick="window.location='{{ route('super-admin.company.details.show', $employee->company->id) }}'"
                                                onmouseover="this.style.backgroundColor='#f0f8ff';"
                                                onmouseout="this.style.backgroundColor=''">
                                                {{ $employee->company->company_name ?? 'N/A' }}
                                            </td>




                                            <td>
                                                <span onclick="copyToClipboard('{{ $employee->email ?? '' }}')"
                                                      style="cursor:pointer; padding:2px 4px; border-radius:4px;"
                                                      onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                      onmouseout="this.style.backgroundColor='transparent';"
                                                      style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-placement="top"
                                                      class="tooltip-btn"
                                                      data-tooltip="Click to copy"
                                                      aria-label="copy email">
                                                    {{ $employee->email ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $employee->phone_no ?? 'N/A' }}</td>
                                            <td>{{ $employee->job_title ?? 'N/A' }}</td>

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


                    </div>
                    @php
                        $activeCount = $infos->where('is_active', 1)->count();
                        $formerCount = $infos->where('is_active', 0)->count();
                    @endphp

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
                                    Total Inactive Employees: {{ $formerCount }}
                                </div>
                            @endif
                        </div>
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
