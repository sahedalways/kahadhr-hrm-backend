<div>
    <div class="row align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Employees Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportEmployees('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>
            <button wire:click="exportEmployees('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <button wire:click="exportEmployees('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>



        </div>



    </div>

    <div class="row mb-3">
        <div class="col-md-8">
            <input type="text" class="form-control shadow-sm" placeholder="Search by name, email, job title"
                wire:model="search" wire:keyup="set('search', $event.target.value)">
        </div>

        <div class="col-md-4 d-flex gap-2">
            <select class="form-select" wire:change="handleSort($event.target.value)">
                <option value="desc">Newest First</option>
                <option value="asc">Oldest First</option>
            </select>
            <select class="form-select" wire:change="handleFilter($event.target.value)">
                <option value="">All Status</option>
                <option value="active">Active Member</option>
                <option value="former">Former Member</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body p-0 table-responsive">
                    <table class="table table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Is Verified</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($infos as $employee)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td style="cursor:pointer; transition: background-color 0.3s;"
                                        onclick="window.location='{{ route('super-admin.company.details.show', $employee->company->id) }}'"
                                        onmouseover="this.style.backgroundColor='#f0f8ff';"
                                        onmouseout="this.style.backgroundColor=''">
                                        {{ $employee->company->company_name ?? 'N/A' }}
                                    </td>



                                    <td>{{ $employee->f_name ?? 'N/A' }}</td>
                                    <td>{{ $employee->l_name ?? 'N/A' }}</td>
                                    <td>
                                        <span onclick="copyToClipboard('{{ $employee->email ?? '' }}')"
                                            style="cursor:pointer; padding:2px 4px; border-radius:4px;"
                                            onmouseover="this.style.backgroundColor='#f0f0f0';"
                                            onmouseout="this.style.backgroundColor='transparent';"
                                            style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Click to copy">
                                            {{ $employee->email ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $employee->job_title ?? 'N/A' }}</td>
                                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->team->name ?? 'N/A' }}</td>


                                    <td>
                                        <a href="#" onmouseover="this.style.backgroundColor='#f0f0f0';"
                                            onmouseout="this.style.backgroundColor='transparent';"
                                            style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;">
                                            {!! statusBadgeTwo($employee->is_active) !!}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($employee->verified)
                                            <span class="badge badge-success badge-xs">Verified</span>
                                        @else
                                            <span class="badge badge-danger badge-xs">Unverified</span>
                                        @endif
                                    </td>


                                    <td>
                                        <a href="{{ route('super-admin.dashboard.employees.details', $employee->id) }}"
                                            class="badge badge-xs text-white"
                                            style="background-color:#5acaa3; color:#ffffff; transition: all 0.3s ease;"
                                            onmouseover="this.style.setProperty('background-color', '#4ebf9f', 'important'); this.style.setProperty('color', '#000000', 'important');"
                                            onmouseout="this.style.setProperty('background-color', '#5acaa3', 'important'); this.style.setProperty('color', '#ffffff', 'important');">
                                            View Details
                                        </a>


                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No employees found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($hasMore)
                        <div class="text-center my-3">
                            <button wire:click="loadMore" class="btn btn-outline-primary">Load More</button>
                        </div>
                    @endif
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
