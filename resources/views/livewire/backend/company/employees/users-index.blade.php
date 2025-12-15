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

        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#add" wire:click="resetInputFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Employee
            </a>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
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
                                            <td>{{ $employee->f_name ?? 'N/A' }}</td>
                                            <td>{{ $employee->l_name ?? 'N/A' }}</td>
                                            <td>
                                                <span onclick="copyToClipboard('{{ $employee->email ?? '' }}')"
                                                    style="cursor:pointer; padding:2px 4px; border-radius:4px;"
                                                    onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                    onmouseout="this.style.backgroundColor='transparent';"
                                                    style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Click to copy">
                                                    {{ $employee->email ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $employee->job_title ?? 'N/A' }}</td>
                                            <td>{{ $employee->department->name ?? 'N/A' }}</td>
                                            <td>{{ $employee->team->name ?? 'N/A' }}</td>


                                            <td>
                                                <a href="#" wire:click.prevent="toggleStatus({{ $employee->id }})"
                                                    onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                    onmouseout="this.style.backgroundColor='transparent';"
                                                    style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Click to change status">
                                                    {!! statusBadgeTwo($employee->is_active) !!}
                                                </a>
                                            </td>
                                            <td>
                                                @if (!$employee->verified && !$employee->invite_token)
                                                    <button class="badge badge-primary badge-xs border-0"
                                                        wire:click="sendVerificationLink({{ $employee->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="sendVerificationLink({{ $employee->id }})">
                                                        <span wire:loading.remove
                                                            wire:target="sendVerificationLink({{ $employee->id }})">
                                                            Send Verification Link
                                                        </span>
                                                        <span wire:loading
                                                            wire:target="sendVerificationLink({{ $employee->id }})">
                                                            Sending...
                                                        </span>
                                                    </button>
                                                @elseif (!$employee->verified && $employee->invite_token)
                                                    <span class="badge badge-warning badge-xs">Link Sent</span>
                                                @else
                                                    <span class="badge badge-success badge-xs">Verified</span>
                                                @endif
                                            </td>



                                            <td>




                                                <a href="{{ route('company.dashboard.employees.details', [
                                                    'company' => app('authUser')->company->sub_domain,
                                                    'id' => $employee->id,
                                                ]) }}"
                                                    class="badge badge-xs text-white"
                                                    style="background-color:#5acaa3; color:#ffffff; transition: all 0.3s ease;"
                                                    onmouseover="this.style.setProperty('background-color', '#4ebf9f', 'important'); this.style.setProperty('color', '#000000', 'important');"
                                                    onmouseout="this.style.setProperty('background-color', '#5acaa3', 'important'); this.style.setProperty('color', '#ffffff', 'important');">
                                                    View Details
                                                </a>


                                                <a data-bs-toggle="modal" data-bs-target="#editProfile"
                                                    wire:click="editProfile({{ $employee->id }})"
                                                    class="badge badge-info badge-xs text-white"
                                                    style="background-color:#4ba3f7 !important; color:#ffffff !important; cursor:pointer; transition: all 0.3s ease;"
                                                    onmouseover="this.style.setProperty('background-color', '#5ca6f9', 'important'); this.style.setProperty('color', '#000000', 'important');"
                                                    onmouseout="this.style.setProperty('background-color', '#4ba3f7', 'important'); this.style.setProperty('color', '#ffffff', 'important');">
                                                    Edit Profile
                                                </a>




                                                <a href="#" class="badge badge-warning badge-xs"
                                                    wire:click.prevent="assignAAdmin({{ $employee->id }})">
                                                    A-Admin
                                                </a>

                                                <a href="#" class="badge badge-danger badge-xs"
                                                    wire:click.prevent="$dispatch('confirmDelete', {{ $employee->id }})">
                                                    Delete
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
    </div>



    <div wire:ignore.self class="modal fade" id="add" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add Employee</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">


                    <!-- Select Add Method -->
                    <div class="mb-3">
                        <label class="form-label">Add Employee Via</label>
                        <select class="form-select" wire:model.live="addMethod" wire:key="addMethod">
                            <option value="manual">Manual Entry</option>
                            <option value="csv">Import CSV</option>
                        </select>
                    </div>

                    <!-- Conditional: CSV Import -->
                    @if ($addMethod === 'csv')
                        <div class="mb-3" wire:key="csv-field">
                            <label class="form-label">Upload CSV File <span class="text-danger">*</span></label>
                            <input type="file" wire:model="csv_file" accept=".csv" class="form-control">
                            @error('csv_file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">CSV must include headers: f_name, l_name, email,
                                department, role</small>
                        </div>
                        <button class="btn btn-primary" wire:click="importCsv" wire:loading.attr="disabled">
                            <span wire:loading wire:target="importCsv"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Importing...</span>
                            <span wire:loading.remove wire:target="importCsv">Import CSV</span>
                        </button>
                    @endif

                    <!-- Conditional: Manual Entry -->
                    @if ($addMethod === 'manual')
                        <form wire:submit.prevent="submitEmployee" wire:key="manual-field">
                            <div class="row g-2">

                                <div class="col-md-6">
                                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="f_name">
                                    @error('f_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="l_name">
                                    @error('l_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


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



                                <!-- Team -->
                                <div class="col-md-6">
                                    <label class="form-label">Team <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model="team_id">
                                        <option value="">Select Team </option>
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
                                        <option value="" selected>Select a role</option>
                                        @foreach (config('roles') as $role)
                                            <option value="{{ $role }}">
                                                {{ ucfirst(preg_replace('/([a-z])([A-Z])/', '$1 $2', $role)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Salary Type -->
                                <div class="col-md-6">
                                    <label class="form-label">Salary Type <span class="text-danger">*</span></label>
                                    <select class="form-select" wire:model.live="salary_type" wire:key="salary_type">
                                        <option value="" selected disabled>Select Salary Type</option>
                                        <option value="hourly">Hourly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                    @error('salary_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($salary_type === 'hourly')
                                    <div class="col-md-6" wire:key="contract-hours-field">
                                        <label class="form-label">Contract Hours (Weekly)<span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control"
                                            wire:model="contract_hours">
                                        @error('contract_hours')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                    wire:target="submitEmployee">
                                    <span wire:loading wire:target="submitEmployee"><i
                                            class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                                    <span wire:loading.remove wire:target="submitEmployee">Save</span>
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>





    <div wire:ignore.self class="modal fade" id="editProfile" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Edit Employee</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form wire:submit.prevent="updateProfile">
                    <div class="modal-body row g-2">
                        <h6 class="fw-bold">Basic Information</h6>

                        <div class="col-md-6">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="title">
                                <option value="" selected>Select Title</option>
                                <option value="Mr">Mr</option>
                                <option value="Mrs">Mrs</option>
                            </select>
                            @error('title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="f_name">
                            @error('f_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="l_name">
                            @error('l_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label"> Mobile No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="phone_no" readonly
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('phone_no')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary ms-2 mb-0" wire:click="openModal('mobile')"
                                data-bs-toggle="modal" data-bs-target="#verifyModal">
                                Change
                            </button>
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label"> Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model="email" readonly>
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary ms-2 mb-0" wire:click="openModal('email')"
                                data-bs-toggle="modal" data-bs-target="#verifyModal">
                                Change
                            </button>
                        </div>


                        <!-- Job Title -->
                        <div class="col-md-6">
                            <label class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="job_title">

                            @error('job_title')
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

                            <select class="form-select" wire:model.live="salary_type" wire:key="salary_type">
                                <option value="" selected disabled>Select Salary Type</option>
                                <option value="hourly">Hourly</option>
                                <option value="monthly">Monthly</option>
                            </select>

                            @error('salary_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($salary_type === 'hourly')
                            <div class="col-md-6" wire:key="contract-hours-field">
                                <label class="form-label">Contract Hours (Weekly) <span
                                        class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control"
                                    wire:model="contract_hours">

                                @error('contract_hours')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Start Date -->
                        <div class="col-md-6">
                            <label class="form-label">Start Date </label>
                            <input type="date" class="form-control" wire:model="start_date" required readonly>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" wire:model="end_date">
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-12 mb-2">
                            <label class="form-label">Profile Image</label>
                            <input type="file" class="form-control" wire:model="avatar" accept="image/*">


                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="img-thumbnail mt-2" width="80">

                                <div wire:loading wire:target="avatar">
                                    <span class="text-muted">Uploading...</span>
                                </div>
                            @elseif ($avatar_preview)
                                <img src="{{ $avatar_preview }}" class="img-thumbnail mt-2" width="80">
                            @endif

                            @error('avatar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- PROFILE INFORMATION -->
                        <hr class="my-3">
                        <h6 class="fw-bold">Profile Information</h6>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" wire:model="date_of_birth">
                            @error('date_of_birth')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Street 1 -->
                        <div class="col-md-6">
                            <label class="form-label">Street 1</label>
                            <input type="text" class="form-control" wire:model="street_1">
                            @error('street_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Street 2 -->
                        <div class="col-md-6">
                            <label class="form-label">Street 2</label>
                            <input type="text" class="form-control" wire:model="street_2">
                            @error('street_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" wire:model="city">
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- State -->
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" wire:model="state">
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Postcode -->
                        <div class="col-md-6">
                            <label class="form-label">Postcode</label>
                            <input type="text" class="form-control" wire:model="postcode"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('postcode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-control" wire:model="country">
                            @error('country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control" wire:model="nationality">
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Home Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Home Phone</label>
                            <input type="text" class="form-control" wire:model="home_phone"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('home_phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                        <!-- Personal Email -->
                        <div class="col-md-6">
                            <label class="form-label">Personal Email</label>
                            <input type="email" class="form-control" wire:model="personal_email">
                            @error('personal_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" wire:model="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Marital Status -->
                        <div class="col-md-6">
                            <label class="form-label">Marital Status</label>
                            <select class="form-select" wire:model="marital_status">
                                <option value="">Select Status</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                            </select>
                            @error('marital_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tax Reference Number -->
                        <div class="col-md-6">
                            <label class="form-label">Tax Reference Number</label>
                            <input type="text" class="form-control" wire:model="tax_reference_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('tax_reference_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Immigration Status -->
                        <div class="col-md-6">
                            <label class="form-label">Immigration Status / Visa Type</label>
                            <select class="form-select" wire:model="immigration_status">
                                <option value="" selected>Select Immigration Status / Visa Type</option>

                                <!-- Common UK Visa & Immigration Status Options -->
                                <option value="British Citizen">British Citizen</option>
                                <option value="Indefinite Leave to Remain (ILR)">Indefinite Leave to Remain (ILR)
                                </option>
                                <option value="Pre-Settled Status">Pre-Settled Status</option>
                                <option value="Settled Status">Settled Status</option>
                                <option value="Skilled Worker Visa">Skilled Worker Visa</option>
                                <option value="Student Visa (Tier 4)">Student Visa (Tier 4)</option>
                                <option value="Graduate Visa">Graduate Visa</option>
                                <option value="Health and Care Worker Visa">Health & Care Worker Visa</option>
                                <option value="Family Visa">Family Visa</option>
                                <option value="Spouse Visa">Spouse Visa</option>
                                <option value="Start-up Visa">Start-up Visa</option>
                                <option value="Innovator Visa">Innovator Visa</option>
                                <option value="Temporary Work Visa">Temporary Work Visa</option>
                                <option value="Youth Mobility Scheme Visa">Youth Mobility Scheme Visa</option>
                                <option value="Asylum Seeker">Asylum Seeker</option>
                                <option value="Refugee Status">Refugee Status</option>
                                <option value="Other">Other</option>
                            </select>

                            @error('immigration_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- BRP Number -->
                        <div class="col-md-6">
                            <label class="form-label">BRP Number</label>
                            <input type="text" class="form-control" wire:model="brp_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('brp_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- BRP Expiry -->
                        <div class="col-md-6">
                            <label class="form-label">BRP Expiry Date</label>
                            <input type="date" class="form-control" wire:model="brp_expiry_date">
                            @error('brp_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Right to Work Expiry -->
                        <div class="col-md-6">
                            <label class="form-label">Right to Work Expiry Date</label>
                            <input type="date" class="form-control" wire:model="right_to_work_expiry_date">
                            @error('right_to_work_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Passport Number -->
                        <div class="col-md-6">
                            <label class="form-label">Passport Number</label>
                            <input type="text" class="form-control" wire:model="passport_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Passport Expiry -->
                        <div class="col-md-6">
                            <label class="form-label">Passport Expiry Date</label>
                            <input type="date" class="form-control" wire:model="passport_expiry_date">
                            @error('passport_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>









                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="updateProfile">
                            <span wire:loading wire:target="updateProfile">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove wire:target="updateProfile">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="verifyModal" tabindex="-1" role="dialog"
        aria-labelledby="verifyModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Verification Centre</h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="verifyAndUpdate">
                    <div class="modal-body">

                        <!-- Email Input -->
                        @if ($updating_field === 'email')
                            <div class="mb-3">
                                <label>New Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control form-control-sm shadow-sm"
                                        wire:model="new_email" placeholder="Enter new email" style="height: 38px;">

                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>





                                </div>

                                <!-- Livewire polling for countdown -->
                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Mobile Input -->
                        @if ($updating_field === 'mobile')
                            <div class="mb-3">
                                <label>New Mobile No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control shadow-sm form-control-sm"
                                        wire:model="new_mobile" placeholder="Enter new mobile no."
                                        style="height: 38px;">
                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>
                                </div>


                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Verification Code Input -->
                        @if ($code_sent)
                            <div class="mb-3">
                                <label>Verification Code <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text" wire:model="otp.{{ $i }}"
                                            class="form-control text-center otp-field" maxlength="1" placeholder="-"
                                            oninput="handleOtpInput(this)"
                                            onkeydown="handleOtpBackspace(event, this)">
                                    @endfor
                                </div>
                                @error('verification_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif




                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        @if ($code_sent)
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="verifyOtp">
                                <span wire:loading wire:target="verifyOtp">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                </span>
                                <span wire:loading.remove wire:target="verifyOtp">Verify</span>
                            </button>
                        @endif

                    </div>
                </form>
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

    Livewire.on('confirmDelete', employeeId => {
        if (confirm("Are you sure you want to delete this employee?")) {
            Livewire.dispatch('deleteEmployee', {
                id: employeeId
            });
        }
    });
</script>

<script>
    function handleOtpInput(el) {

        el.value = el.value.replace(/[^0-9]/g, '');


        if (el.value.length === 1) {
            const next = el.nextElementSibling;
            if (next && next.classList.contains('otp-field')) {
                next.focus();
            }
        }
    }

    function handleOtpBackspace(e, el) {

        if (e.key === 'Backspace') {
            if (el.value) {

                el.value = '';
            } else {

                const prev = el.previousElementSibling;
                if (prev && prev.classList.contains('otp-field')) {
                    prev.focus();
                    prev.value = '';
                }
            }

            e.preventDefault();
        }
    }
</script>
