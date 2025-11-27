<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Employee Profile Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-4 align-items-center p-3 shadow-sm border rounded bg-white"
                        wire:submit.prevent="save">

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

                        <hr class="mt-4">



                        <!-- Job Title -->
                        <div class="row mt-3">

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

                        </div>


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
