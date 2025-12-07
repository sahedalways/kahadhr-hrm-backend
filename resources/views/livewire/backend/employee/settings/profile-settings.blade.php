<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-dark">Employee Profile</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-lg-5 p-4 shadow-sm border rounded-3 bg-white">
                    <form class="row g-4 align-items-center"
                        wire:submit.prevent="save">

                        <!-- PERSONAL INFORMATION -->
                        <h5 class="fw-bold mb-0">Personal Information</h5>
                        <hr class="mt-2">

                        <!-- First Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="f_name"
                                placeholder="Enter First Name">
                            @error('f_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="l_name"
                                placeholder="Enter Last Name">
                            @error('l_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Avatar</label>
                            <input type="file" class="form-control shadow-sm" wire:model="avatar" accept="image/*">
                            @error('avatar')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror

                            @if ($avatar)
                                <img src="{{ $avatar->temporaryUrl() }}" class="img-thumbnail mt-2 shadow-sm rounded"
                                    width="90">
                                <div wire:loading wire:target="avatar">
                                    <span class="text-muted small">Uploading...</span>
                                </div>
                            @elseif ($old_avatar)
                                <img src="{{ $old_avatar }}" class="img-thumbnail mt-2 shadow-sm rounded"
                                    width="90">
                            @endif
                        </div>

                        <!-- JOB & DEPARTMENT INFO -->
                        <hr class="mt-4">
                        <h5 class="fw-bold mb-0">Job & Department Info</h5>
                        <hr class="mt-2">

                        <div class="col-md-4">
                            <p><strong>Job Title:</strong> {{ $job_title }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Department:</strong> {{ $departments[$department_id] ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Team:</strong> {{ $teams[$team_id] ?? 'N/A' }}</p>
                        </div>

                        <!-- CONTRACT & SALARY -->
                        <hr class="mt-4">
                        <h5 class="fw-bold mb-0">Contract & Salary</h5>
                        <hr class="mt-2">

                        <div class="col-md-4">
                            <p><strong>Salary Type:</strong> {{ ucfirst($salary_type) }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>Employment Type:</strong>
                                {{ $salary_type === 'monthly' ? 'Full-time' : 'Part-time' }}</p>
                        </div>

                        @if ($salary_type === 'hourly')
                            <div class="col-md-4">
                                <p><strong>Contract Hours:</strong> {{ $contract_hours }}</p>
                            </div>
                        @endif

                        <div class="col-md-4">
                            <p><strong>Start Date:</strong> {{ $start_date ?? 'N/A' }}</p>
                        </div>

                        <div class="col-md-4">
                            <p><strong>End Date:</strong> {{ $end_date ?? 'N/A' }}</p>
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

                        <!-- Gender -->
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

                        <!-- Address -->
                        <div class="col-md-6">
                            <label class="form-label">Street 1</label>
                            <input type="text" class="form-control" wire:model="street_1">
                            @error('street_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Street 2</label>
                            <input type="text" class="form-control" wire:model="street_2">
                            @error('street_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" wire:model="city">
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" wire:model="state">
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Postcode</label>
                            <input type="text" class="form-control" wire:model="postcode"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('postcode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

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

                        <!-- Phones & Email -->
                        <div class="col-md-6">
                            <label class="form-label">Home Phone</label>
                            <input type="text" class="form-control" wire:model="home_phone"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('home_phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Personal Email</label>
                            <input type="email" class="form-control" wire:model="personal_email">
                            @error('personal_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tax, Immigration, BRP & Passport -->
                        <div class="col-md-6">
                            <label class="form-label">Tax Reference Number</label>
                            <input type="text" class="form-control" wire:model="tax_reference_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('tax_reference_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Immigration Status / Visa Type</label>
                            <select class="form-select" wire:model="immigration_status">
                                <option value="">Select Immigration Status / Visa Type</option>
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

                        <div class="col-md-6">
                            <label class="form-label">BRP Number</label>
                            <input type="text" class="form-control" wire:model="brp_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('brp_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">BRP Expiry Date</label>
                            <input type="date" class="form-control" wire:model="brp_expiry_date">
                            @error('brp_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Right to Work Expiry Date</label>
                            <input type="date" class="form-control" wire:model="right_to_work_expiry_date">
                            @error('right_to_work_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Passport Number</label>
                            <input type="text" class="form-control" wire:model="passport_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('passport_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Passport Expiry Date</label>
                            <input type="date" class="form-control" wire:model="passport_expiry_date">
                            @error('passport_expiry_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- SUBMIT -->
                        <hr class="mt-4">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4 py-2 shadow-sm"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="save">Save</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
