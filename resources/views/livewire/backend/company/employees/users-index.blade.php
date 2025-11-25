<div>
    <div class="row align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-white m-0">Employees Management</h5>
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

        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#add" wire:click="resetInputFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Employee
            </a>
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
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($infos as $employee)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $employee->f_name }}</td>
                                    <td>{{ $employee->l_name }}</td>
                                    <td>
                                        <span onclick="copyToClipboard('{{ $employee->user->email ?? '' }}')"
                                            style="cursor:pointer; padding:2px 4px; border-radius:4px;">
                                            {{ $employee->user->email ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>{{ $employee->job_title ?? 'N/A' }}</td>
                                    <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                    <td>{{ $employee->team->name ?? 'N/A' }}</td>
                                    <td>
                                        <a href="#" wire:click.prevent="toggleStatus({{ $employee->id }})">
                                            {!! statusBadgeTwo($employee->is_active) !!}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('company.dashboard.employee.details', [
                                            'company' => app('authUser')->company->sub_domain,
                                            'id' => $employee->id,
                                        ]) }}"
                                            class="badge badge-xs text-white" style="background-color:#5acaa3;">
                                            View Details
                                        </a>


                                        <a data-bs-toggle="modal" data-bs-target="#editProfile"
                                            wire:click="editProfile({{ $employee->id }})"
                                            class="badge badge-info badge-xs text-white"
                                            style="background-color:#4ba3f7;">Edit Profile</a>

                                        <a href="#" class="badge badge-danger badge-xs"
                                            wire:click.prevent="$dispatch('confirmDelete', {{ $employee->id }})">
                                            Delete
                                        </a>

                                        <a href="#" class="badge badge-warning badge-xs"
                                            wire:click.prevent="assignAAdmin({{ $employee->id }})">
                                            A-Admin
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



    <div wire:ignore.self class="modal fade" id="add" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add Employee</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="submitEmployee">
                    <div class="modal-body row g-2">

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" wire:model="email" required>

                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Job Title -->
                        <div class="col-md-6">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="job_title">

                            @error('job_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div class="col-md-6">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="department_id">
                                <option value="">Select Department</option>
                                @foreach ($departments as $dep)
                                    <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                @endforeach
                            </select>

                            @error('department_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Team -->
                        <div class="col-md-6">
                            <label class="form-label">Team <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="team_id">
                                <option value="">Select Team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>


                            @error('team_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="role">
                                <option value="" selected disabled>Select a role</option>
                                @foreach (config('roles') as $role)
                                    <option value="{{ $role }}">
                                        {{ ucfirst(preg_replace('/([a-z])([A-Z])/', '$1 $2', $role)) }}</option>
                                @endforeach
                            </select>

                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Salary Type -->
                        <div class="col-md-6">
                            <label class="form-label">Salary Type <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="salary_type">
                                <option value="" selected disabled>Select Salary Type</option>
                                <option value="hourly">Hourly</option>
                                <option value="monthly">Monthly</option>
                            </select>


                            @error('salary_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Contract Hours (show only if hourly) -->
                        @if ($salary_type === 'hourly')
                            <div class="col-md-6">
                                <label class="form-label">Contract Hours <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control"
                                    wire:model="contract_hours">
                            </div>

                            @error('contract_hours')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="submitEmployee">
                            <span wire:loading wire:target="submitEmployee">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove wire:target="submitEmployee">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    {{-- edit Employee Modal --}}
    {{-- <div wire:ignore.self class="modal fade" id="editEmployee" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Employee</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="updateEmployee">
                    <div class="modal-body row g-2">

                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" wire:model="f_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" wire:model="l_name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-control" wire:model="job_title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select class="form-select" wire:model="department_id">
                                <option value="">Select Department</option>
                                @foreach ($departments as $dep)
                                    <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Team</label>
                            <select class="form-select" wire:model="team_id">
                                <option value="">Select Team</option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select" wire:model="role">
                                <option value="" selected disabled>Select a role</option>
                                @foreach (config('roles') as $role)
                                    <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Contract Hours</label>
                            <input type="number" class="form-control" wire:model="contract_hours">
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

</div>

<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.error(err));
    }

    Livewire.on('confirmDelete', employeeId => {
        if (confirm("Are you sure you want to delete this employee?")) {
            Livewire.dispatch('deleteEmployee', {
                id: employeeId
            });
        }
    });
</script>
