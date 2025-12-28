<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Back to Employees List -->
        <div class="text-end mb-2">
            <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                style="background-color:#f8f9fa; border:1px solid #0d6efd; padding:6px 12px; font-size:0.875rem;"
                href="{{ route('company.dashboard.employees.index', ['company' => app('authUser')->company->sub_domain]) }}">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Back to Employees
            </a>
        </div>




        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow-sm" style="border-radius:14px;border:none;overflow:hidden;background:#fff;">

                <!-- Header -->
                <div
                    style="background:linear-gradient(135deg,#0dcaf0,#0b9ed0);
                   color:#fff;
                   padding:14px 20px;
                   font-weight:600;
                   font-size:0.85rem;
                   text-transform:uppercase;
                   letter-spacing:.5px;">
                    <i class="bi bi-person-circle me-2"></i>
                    Employee Profile
                </div>

                <!-- Menu -->
                <div class="list-group list-group-flush" role="tablist">

                    <!-- Active -->
                    <a class="list-group-item list-group-item-action active" data-bs-toggle="tab" href="#overview"
                        role="tab"
                        style="border:none;
                      padding:14px 20px;
                      display:flex;
                      align-items:center;
                      gap:12px;
                      background:#e9f7fc;
                      color:#0dcaf0;
                      font-weight:600;
                      border-left:4px solid #0dcaf0;">
                        <i class="bi bi-person-lines-fill" style="font-size:1.1rem;"></i>
                        Employee Overview
                    </a>

                    <a class="list-group-item list-group-item-action" data-bs-toggle="tab" href="#personalInfo"
                        role="tab"
                        style="border:none;
                      padding:14px 20px;
                      display:flex;
                      align-items:center;
                      gap:12px;
                      font-weight:500;
                      color:#444;">
                        <i class="bi bi-person-badge" style="font-size:1.1rem;color:#6c757d;"></i>
                        Personal Info
                    </a>

                    <a class="list-group-item list-group-item-action" data-bs-toggle="tab" href="#employment"
                        role="tab"
                        style="border:none;
                      padding:14px 20px;
                      display:flex;
                      align-items:center;
                      gap:12px;
                      font-weight:500;
                      color:#444;">
                        <i class="bi bi-briefcase" style="font-size:1.1rem;color:#6c757d;"></i>
                        Employment Info
                    </a>

                    <a class="list-group-item list-group-item-action" data-bs-toggle="tab" href="#documentsSection"
                        role="tab"
                        style="border:none;
                      padding:14px 20px;
                      display:flex;
                      align-items:center;
                      gap:12px;
                      font-weight:500;
                      color:#444;">
                        <i class="bi bi-folder" style="font-size:1.1rem;color:#6c757d;"></i>
                        Documents
                    </a>

                    <a class="list-group-item list-group-item-action" data-bs-toggle="tab" href="#settingsEmp"
                        role="tab"
                        style="border:none;
                      padding:14px 20px;
                      display:flex;
                      align-items:center;
                      gap:12px;
                      font-weight:500;
                      color:#444;">
                        <i class="bi bi-gear" style="font-size:1.1rem;color:#6c757d;"></i>
                        Settings
                    </a>

                </div>
            </div>
        </div>



        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="tab-content">

                <!-- Employee Overview -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                    <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                        <div class="card-header bg-white py-4 border-bottom-0 d-flex align-items-center justify-content-between"
                            style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">

                            <!-- Title -->
                            <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-user-circle me-3 text-info"></i>
                                Employee Overview
                            </h4>

                            <!-- Menu Button -->
                            <div class="dropdown" wire:ignore>
                                <button class="btn btn-sm btn-light border-0 px-md-3 px-2" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target="#editProfile" wire:click="editProfile({{ $employee->id }})">
                                            <i class="fas fa-edit me-2 text-muted"></i> Edit Profile
                                        </a>
                                    </li>
                                    @if (!$employee->verified && !$employee->user)
                                        <li>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="sendVerificationLink({{ $employee->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="sendVerificationLink({{ $employee->id }})">

                                                <i class="fas fa-envelope me-2 text-muted"></i>

                                                <span wire:loading.remove
                                                    wire:target="sendVerificationLink({{ $employee->id }})">
                                                    Send Verification Link
                                                </span>



                                                <span wire:loading
                                                    wire:target="sendVerificationLink({{ $employee->id }})">
                                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                                    Sending...
                                                </span>
                                            </a>
                                        </li>
                                    @endif

                                    <li>
                                        <a class="dropdown-item" href="#"
                                            wire:click.prevent="toggleStatus({{ $employee->id }})"
                                            wire:loading.attr="disabled" wire:target="toggleStatus">

                                            <span wire:loading.remove wire:target="toggleStatus">
                                                <i class="fas fa-user-lock me-2 text-muted"></i> Change Status
                                            </span>

                                            <span wire:loading wire:target="toggleStatus">
                                                <span class="spinner-border spinner-border-sm me-2"></span>
                                                Changing...
                                            </span>
                                        </a>
                                    </li>


                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li>
                                        <a class="dropdown-item text-danger" href="#"
                                            wire:click.prevent="$dispatch('confirmDelete', {{ $employee->id }})">
                                            <i class="fas fa-trash me-2"></i> Remove Employee
                                        </a>
                                    </li>

                                </ul>
                            </div>

                        </div>


                        <div class="card-body p-4 pt-0">
                            <div class="row g-4 align-items-start">

                                <!-- Profile -->
                                <div class="col-lg-3 col-md-4 text-center position-relative">

                                    <!-- responsive border -->
                                    <div class="d-none d-md-block position-absolute top-0 end-0 h-100 border-end">
                                    </div>

                                    <div class="d-flex flex-column align-items-center p-3">

                                        <div class="position-relative mb-3">
                                            <img src="{{ $employee->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                class="img-fluid shadow-sm clickable-image"
                                                style="min-width: 120px; width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid var(--bs-info); cursor: pointer;"
                                                data-src="{{ $employee->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                alt="Employee Avatar">

                                            <span
                                                class="badge rounded-pill position-absolute p-2 
            {{ $employee->is_active ? 'bg-success border border-white' : 'bg-secondary border border-white' }}"
                                                style="transform: translate(25%, 25%); bottom: 12px;
            right: 8px ">
                                                <a href="#"
                                                    wire:click.prevent="toggleStatus({{ $employee->id }})"
                                                    class="status-toggle tooltip-btn"
                                                    data-tooltip="click to change status"
                                                    aria-label="Toggle employee status">

                                                    <span
                                                        class="status-dot {{ $employee->is_active ? 'active' : 'inactive' }}"></span>

                                                </a>
                                            </span>
                                        </div>

                                        <h5 class="mb-1 fw-bold">{{ $employee->full_name }}</h5>

                                        <!-- Verified / Unverified Badge -->
                                        @if ($employee->user)
                                            @if ($employee->user->email_verified_at)
                                                <span class="badge bg-success mb-2">Verified</span>
                                            @else
                                                <span class="badge bg-warning text-dark mb-2">Unverified</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary mb-2">Link Sent</span>
                                        @endif

                                        <p class="text-muted small fw-bold mb-0">{{ $employee->job_title ?: 'N/A' }}
                                        </p>

                                    </div>

                                </div>



                                <!-- Details -->
                                <div class="col-lg-9 col-md-8">
                                    <h5 class="fw-bold mb-3 text-info text-center">
                                        Employment & Contact
                                    </h5>

                                    <div class="row">

                                        <!-- Left Info -->
                                        <div class="col-md-6">
                                            <div class="p-3 pb-md-3 pb-0 rounded bg-light-subtle h-100">

                                                <div class="mb-3">
                                                    <div class="text-muted fw-bold small">Work Email</div>
                                                    <div class="fw-medium text-truncate">
                                                        {{ $employee->email }}
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="text-muted fw-bold small">Work Phone</div>
                                                    <div>
                                                        {{ $employee->user->phone_no ?? 'N/A' }}
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="text-muted fw-bold small">Role</div>
                                                    <div>
                                                        {{ ucfirst($employee->role) }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- Right Info -->
                                        <div class="col-md-6">
                                            <div class="p-3 rounded bg-light-subtle h-100">

                                                <div class="mb-3">
                                                    <div class="text-muted fw-bold small">Company</div>
                                                    <div>
                                                        {{ $employee->company->company_name ?? 'N/A' }}
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="text-muted fw-bold small mb-1">Departments</div>

                                                    @php

                                                        $max = 5;
                                                    @endphp

                                                    @if ($departments->isEmpty())
                                                        <span class="text-muted fst-italic">N/A</span>
                                                    @else
                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                            @foreach ($departments->take($showAllDepartments ? $departments->count() : $max) as $department)
                                                                <span
                                                                    class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                                    {{ $department->name }}
                                                                </span>
                                                            @endforeach

                                                            @if ($departments->count() > $max)
                                                                <button wire:click="toggleDepartments"
                                                                    class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                                    {{ $showAllDepartments ? 'See less' : 'View more' }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <div class="text-muted fw-bold small mb-1">Teams</div>

                                                    @php
                                                        $assignedTeams = $employee->user
                                                            ? $employee->user->teams
                                                            : collect();
                                                        $max = 5;
                                                    @endphp

                                                    @if ($assignedTeams->isEmpty())
                                                        <span class="text-muted fst-italic">N/A</span>
                                                    @else
                                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                                            @foreach ($assignedTeams->take($showAllTeams ? $assignedTeams->count() : $max) as $team)
                                                                @php
                                                                    $isLead =
                                                                        $team->team_lead_id === $employee->user_id;
                                                                @endphp

                                                                <span
                                                                    class="badge rounded-pill px-3 py-2
                    {{ $isLead ? 'bg-primary text-white' : 'bg-light text-dark border' }}">

                                                                    {{ $team->name }}

                                                                    @if ($isLead)
                                                                        <span class="ms-1 fw-semibold">
                                                                            ⭐ Leader
                                                                        </span>
                                                                    @endif
                                                                </span>
                                                            @endforeach

                                                            @if ($assignedTeams->count() > $max)
                                                                <button wire:click="toggleTeams"
                                                                    class="btn btn-sm btn-link text-decoration-none fw-semibold ms-1">
                                                                    {{ $showAllTeams ? 'See less' : 'View more' }}
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>




                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Employment Info -->
                <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                    <div class="card border-0 shadow-lg" style="border-radius:1rem;">

                        <!-- Header -->
                        <div class="card-header bg-white py-3 border-bottom-0"
                            style="border-top-left-radius:1rem;border-top-right-radius:1rem;">
                            <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-briefcase me-3 text-info"></i>

                                Employment Information
                            </h4>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">

                                <!-- Left -->
                                <div class="col-md-6">
                                    <h5 class="fw-bold text-secondary mb-3" style="font-size:0.95rem;">
                                        Contract Structure
                                    </h5>

                                    <div class="p-3 bg-light rounded-3 mb-3 border-start border-3 border-info">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                            style="font-size:0.7rem;">
                                            Salary Type
                                        </small>
                                        <p class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">
                                            {{ ucfirst($employee->salary_type ?? 'N/A') }}
                                        </p>
                                    </div>

                                    <div
                                        class="p-3 bg-light rounded-3 mb-3 border-start border-3
                        @if (($employee->salary_type ?? '') === 'hourly') border-warning @else border-success @endif">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                            style="font-size:0.7rem;">
                                            Employment Type
                                        </small>
                                        <p class="fw-bold mb-0
                            @if (($employee->salary_type ?? '') === 'hourly') text-warning @else text-success @endif"
                                            style="font-size:0.9rem;">
                                            @if (($employee->salary_type ?? '') === 'hourly')
                                                Part Time
                                            @else
                                                Full Time
                                            @endif
                                        </p>
                                    </div>

                                    @if (($employee->salary_type ?? '') === 'hourly')
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                                style="font-size:0.7rem;">
                                                Contract Hours
                                            </small>
                                            <p class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">
                                                {{ $employee->contract_hours ?? 'N/A' }} hours/week
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Right -->
                                <div class="col-md-6 border-start ps-4">
                                    <h5 class="fw-bold text-secondary mb-3" style="font-size:0.95rem;">
                                        Duration & History
                                    </h5>

                                    <div class="p-3 bg-light rounded-3 mb-3">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                            style="font-size:0.7rem;">
                                            Start Date
                                        </small>
                                        <p class="fw-bolder mb-0 text-info" style="font-size:1rem;">
                                            {{ $employee->start_date?->format('d M Y') ?? 'N/A' }}
                                        </p>
                                    </div>

                                    <div
                                        class="p-3 rounded-3
                        @if ($employee->end_date ?? null) bg-danger-subtle border border-danger @else bg-success-subtle border border-success @endif">
                                        <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                            style="font-size:0.7rem;">
                                            End Date
                                        </small>
                                        <p class="fw-bolder mb-0
                            @if ($employee->end_date ?? null) text-danger @else text-success @endif"
                                            style="font-size:1rem;">
                                            @if ($employee->end_date ?? null)
                                                {{ $employee->end_date->format('d M Y') }}
                                            @else
                                                Ongoing
                                            @endif
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <!-- Documents -->
                <div class="tab-pane fade" id="documentsSection" role="tabpanel" aria-labelledby="documents-tab">
                    <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                        <div class="card-header bg-white py-4 border-bottom-0"
                            style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                            <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-folder me-3 text-info"></i>
                                Employee Documents
                            </h4>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">

                                @php
                                    $hasDocs = $employee->documents->isNotEmpty();
                                @endphp

                                @if (!$hasDocs)
                                    <div class="col-12">
                                        <div class="alert alert-info border-0 text-center shadow-sm d-flex flex-column justify-content-center align-items-center"
                                            style="background-color: #eaf6ff; border-radius: 0.75rem; height: 200px;">
                                            <i class="bi bi-file-earmark-lock fs-3 me-2 text-info"></i>
                                            <h5 class="mt-2 mb-0 fw-semibold text-white">No documents found for
                                                this
                                                employee.</h5>
                                        </div>
                                    </div>
                                @else
                                    @foreach ($types as $type)
                                        @php
                                            $docsForType = $employee->documents->where('doc_type_id', $type->id);
                                        @endphp

                                        @if ($docsForType->isEmpty())
                                            @continue
                                        @endif

                                        <div class="col-xl-3 col-md-6 col-sm-12">
                                            <div class="card border-0 shadow-sm rounded-4 h-100"
                                                style="background-color: #fcfcfc;">

                                                <div class="card-header bg-info text-white fw-bold d-flex align-items-center py-3"
                                                    style="border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                                                    <i class="bi bi-folder me-2 fs-5"></i> {{ $type->name }}
                                                </div>

                                                <div class="card-body d-flex flex-column p-3">

                                                    <div class="mb-3 flex-grow-1">

                                                        @foreach ($docsForType as $doc)
                                                            <div class="rounded p-3 mb-2 border border-light position-relative doc-item"
                                                                data-doc-id="{{ $doc->id }}"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#openDocumentModal"
                                                                wire:click.prevent="$dispatch('openDocumentModal', {{ json_encode(['docId' => $doc->id]) }})"
                                                                style="cursor:pointer; background-color:white; transition: all .2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">

                                                                <div class="d-flex align-items-center">
                                                                    <i class="bi bi-file-earmark-pdf-fill text-danger me-3"
                                                                        style="font-size: 32px;"></i>

                                                                    <div class="flex-grow-1">
                                                                        <div class="fw-semibold text-truncate"
                                                                            style="max-width: 100%;">
                                                                            {{ $doc->name ?? 'Document File' }}
                                                                        </div>

                                                                        <div class="small text-muted mt-1">
                                                                            Expiry:
                                                                            @php
                                                                                $expiryDate = $doc->expires_at
                                                                                    ? \Carbon\Carbon::parse(
                                                                                        $doc->expires_at,
                                                                                    )
                                                                                    : null;
                                                                                $isExpired =
                                                                                    $expiryDate &&
                                                                                    $expiryDate->isPast();
                                                                                $isSoon =
                                                                                    $expiryDate &&
                                                                                    $expiryDate->diffInDays(now()) <
                                                                                        30 &&
                                                                                    !$isExpired;
                                                                            @endphp
                                                                            <span
                                                                                class="fw-medium 
                                                                @if ($isExpired) text-danger @elseif($isSoon) text-warning @else text-dark @endif">
                                                                                {{ $expiryDate ? $expiryDate->format('d M, Y') : 'No Expiry' }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if ($isExpired)
                                                                    <span
                                                                        class="badge bg-danger position-absolute top-0 end-3 m-2">EXPIRED</span>
                                                                @elseif ($isSoon)
                                                                    <span
                                                                        class="badge bg-warning text-dark position-absolute top-0 end-3 m-2">Expires
                                                                        Soon</span>
                                                                @endif
                                                            </div>
                                                        @endforeach

                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </div>


                    </div>
                </div>


                <!-- Personal Info -->
                <div class="tab-pane fade" id="personalInfo" role="tabpanel" aria-labelledby="personalInfo-tab">

                    <h4 class="mb-0 fw-bold text-dark d-flex align-items-center mb-3">
                        <i class="fas fa-id-badge" style="margin-right:0.75rem; color:#0dcaf0;"></i>
                        Personal Information
                    </h4>

                    <div style="display:flex; flex-wrap:wrap; gap:1rem;">

                        {{-- ================= CORE DETAILS ================= --}}
                        <div style="flex:1 1 48%;">
                            <div
                                style="border-radius:16px; box-shadow:0 0.25rem 1rem rgba(0,0,0,0.1); border:0; height:100%;">
                                <div
                                    style="background:#0dcaf0; color:#fff; font-weight:700; padding:0.75rem 1rem; border-radius:16px 16px 0 0;">
                                    <i class="bi bi-person" style="margin-right:0.5rem;"></i> Core Details & Contact
                                </div>
                                <div style="padding:1rem;">
                                    <dl style="margin:0;">
                                        @php $profile = $employee->profile ?? new \stdClass(); @endphp

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Date of Birth</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->date_of_birth) ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d F, Y') : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Gender</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->gender) ? $profile->gender : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Marital Status</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->marital_status) ? $profile->marital_status : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Nationality</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->nationality) ? $profile->nationality : 'N/A' }}
                                        </dd>

                                        <hr style="margin:0.75rem 0; border-color:#dee2e6;">

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Personal Email</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem; word-break:break-word;">
                                            {{ !empty($profile->personal_email) ? $profile->personal_email : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Mobile Phone</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->mobile_phone) ? $profile->mobile_phone : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Home Phone</dt>
                                        <dd style="font-weight:600;">
                                            {{ !empty($profile->home_phone) ? $profile->home_phone : 'N/A' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        {{-- ================= ADDRESS + ID ================= --}}
                        <div style="flex:1 1 48%; display:flex; flex-direction:column; gap:1rem;">

                            {{-- ADDRESS --}}
                            <div style="border-radius:16px; box-shadow:0 0.25rem 1rem rgba(0,0,0,0.1); border:0;">
                                <div
                                    style="background:#6c757d; color:#fff; font-weight:700; padding:0.75rem 1rem; border-radius:16px 16px 0 0;">
                                    <i class="bi bi-geo-alt" style="margin-right:0.5rem;"></i> Permanent Address
                                </div>
                                <div style="padding:1rem;">
                                    <dl style="margin:0;">
                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Street</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->street_1) ? $profile->street_1 : 'N/A' }}<br>
                                            <span style="font-size:0.85rem; color:#868e96;">
                                                {{ !empty($profile->street_2) ? $profile->street_2 : '' }}
                                            </span>
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">City / State</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->city) ? $profile->city : 'N/A' }},
                                            {{ !empty($profile->state) ? $profile->state : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Postcode</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->postcode) ? $profile->postcode : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Country</dt>
                                        <dd style="font-weight:600;">
                                            {{ !empty($profile->country) ? $profile->country : 'N/A' }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>

                            {{-- ID & COMPLIANCE --}}
                            <div style="border-radius:16px; box-shadow:0 0.25rem 1rem rgba(0,0,0,0.1); border:0;">
                                <div
                                    style="background:#ffc107; color:#212529; font-weight:700; padding:0.75rem 1rem; border-radius:16px 16px 0 0;">
                                    <i class="bi bi-passport" style="margin-right:0.5rem;"></i> ID & Compliance
                                </div>
                                <div style="padding:1rem;">
                                    <dl style="margin:0;">

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Tax Ref No</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->tax_reference_number) ? $profile->tax_reference_number : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Visa / Status</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->immigration_status) ? $profile->immigration_status : 'N/A' }}
                                        </dd>

                                        @php
                                            $rtw = $profile->right_to_work_expiry ?? null;
                                            $rtwExpired = $rtw ? \Carbon\Carbon::parse($rtw)->isPast() : false;
                                        @endphp
                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">RTW Expiry</dt>
                                        <dd
                                            style="font-weight:600; margin-bottom:0.75rem; color: {{ $rtwExpired ? '#dc3545' : '#212529' }};">
                                            {{ $rtw ? \Carbon\Carbon::parse($rtw)->format('d F, Y') : 'N/A' }}
                                        </dd>

                                        <hr style="margin:0.75rem 0; border-color:#dee2e6;">

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Passport No</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->passport_number) ? $profile->passport_number : 'N/A' }}
                                        </dd>

                                        @php
                                            $passport = $profile->passport_expiry ?? null;
                                            $passportExpired = $passport
                                                ? \Carbon\Carbon::parse($passport)->isPast()
                                                : false;
                                        @endphp
                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">Passport Expiry</dt>
                                        <dd
                                            style="font-weight:600; margin-bottom:0.75rem; color: {{ $passportExpired ? '#dc3545' : '#212529' }};">
                                            {{ $passport ? \Carbon\Carbon::parse($passport)->format('d F, Y') : 'N/A' }}
                                        </dd>

                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">BRP Number</dt>
                                        <dd style="font-weight:600; margin-bottom:0.75rem;">
                                            {{ !empty($profile->brp_number) ? $profile->brp_number : 'N/A' }}
                                        </dd>

                                        @php
                                            $brp = $profile->brp_expiry_date ?? null;
                                            $brpExpired = $brp ? \Carbon\Carbon::parse($brp)->isPast() : false;
                                        @endphp
                                        <dt style="color:#6c757d; margin-bottom:0.25rem;">BRP Expiry</dt>
                                        <dd
                                            style="font-weight:600; color: {{ $brpExpired ? '#dc3545' : '#212529' }};">
                                            {{ $brp ? \Carbon\Carbon::parse($brp)->format('d F, Y') : 'N/A' }}
                                        </dd>

                                    </dl>
                                </div>
                            </div>

                        </div>

                        @if ($customFields->isNotEmpty())
                            <div style="flex:1 1 100%; margin-top:1rem;">
                                <div style="border-radius:16px; box-shadow:0 0.25rem 1rem rgba(0,0,0,0.1); border:0;">
                                    <div
                                        style="background:#198754; color:#fff; font-weight:700; padding:0.75rem 1rem; border-radius:16px 16px 0 0;">
                                        <i class="bi bi-sliders" style="margin-right:0.5rem;"></i> More Information
                                    </div>

                                    <div style="padding:1rem;">
                                        <div class="row">
                                            @foreach ($customFields as $field)
                                                @php
                                                    $value = $customValues[$field->id] ?? null;
                                                @endphp

                                                <div class="col-md-6 mb-3">
                                                    <dt style="color:#6c757d; margin-bottom:0.25rem;">
                                                        {{ $field->name }}
                                                    </dt>

                                                    <dd style="font-weight:600;">
                                                        @if ($value === null || $value === '')
                                                            <span class="text-muted">N/A</span>
                                                        @else
                                                            {{-- Format by type --}}
                                                            @if ($field->type === 'date')
                                                                {{ \Carbon\Carbon::parse($value)->format('d F, Y') }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        @endif
                                                    </dd>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>




                <!-- Settings -->
                <div class="tab-pane fade" id="settingsEmp">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-white py-3 border-0"
                            style="display:flex; align-items:center; gap:10px;">
                            <i class="fas fa-gear text-info"></i>
                            <h4 class="mb-0 fw-bold">Settings</h4>
                        </div>

                        <div class="card-body">
                            <h5 class="fw-semibold mb-3">Change Password</h5>
                            <form id="changePasswordForm">
                                <input type="hidden" id="employeeId" value="{{ $employee->id }}">
                                <input type="hidden" id="companySubdomain"
                                    value="{{ app('authUser')->company->sub_domain }}">
                                <div class="mb-3 position-relative">
                                    <label class="form-label fw-semibold">New Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" id="new_password" class="form-control"
                                        placeholder="Enter new password" required>
                                    <span class="toggle-password" toggle="#new_password"
                                        style="position:absolute; right:10px; top:40px; cursor:pointer;">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>

                                <div class="mb-3 position-relative">
                                    <label class="form-label fw-semibold">Confirm Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" id="confirm_password" class="form-control"
                                        placeholder="Confirm new password" required>
                                    <span class="toggle-password" toggle="#confirm_password"
                                        style="position:absolute; right:10px; top:40px; cursor:pointer;">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>

                                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold"
                                    id="changePasswordBtn">
                                    <span id="btnText">Save Password</span>
                                    <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none"
                                        role="status"></span>
                                </button>
                                <div id="passwordMessage" class="mt-2 text-danger fw-semibold"></div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    @include('livewire.backend.company.components.document-view')





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

                        @if (!$passwordVerified)
                            <div class="mb-3" wire:ignore>
                                <label>Enter Password <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="form-control extra-padding shadow-sm"
                                        id="password" wire:model.defer="passwordInput">
                                    <span class="icon-position " style="cursor:pointer;"
                                        onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>


                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" wire:click="verifyPassword"
                                    wire:loading.attr="disabled" wire:target="verifyPassword">

                                    <span wire:loading wire:target="verifyPassword">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                    </span>

                                    <span wire:loading.remove wire:target="verifyPassword">
                                        Verify Password
                                    </span>
                                </button>

                            </div>
                        @endif

                        <!-- Email Input -->
                        @if ($passwordVerified)
                            @if ($updating_field === 'email')
                                <div class="mb-3">
                                    <label>New Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="email" class="form-control form-control-sm shadow-sm"
                                            wire:model="new_email" placeholder="Enter new email"
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
                                                class="form-control text-center otp-field" maxlength="1"
                                                placeholder="-" oninput="handleOtpInput(this)"
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
                        @endif

                    </div>

                </form>
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
                            <input type="text" class="form-control" wire:model="f_name"
                                placeholder="Enter first name">
                            @error('f_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="l_name"
                                placeholder="Enter last name">
                            @error('l_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label"> Mobile No.<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="phone_no" readonly
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    placeholder="Enter phone no.">
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
                                <input type="email" class="form-control" wire:model="email" readonly
                                    placeholder="Enter email address">
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
                            <input type="text" class="form-control" wire:model="job_title"
                                placeholder="Enter job title">

                            @error('job_title')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>




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
                                    wire:model="contract_hours" placeholder="Enter contract hours">

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
                            <input type="text" class="form-control" wire:model="street_1"
                                placeholder="Enter street 1">
                            @error('street_1')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Street 2 -->
                        <div class="col-md-6">
                            <label class="form-label">Street 2</label>
                            <input type="text" class="form-control" wire:model="street_2"
                                placeholder="Enter street 2">
                            @error('street_2')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- State Dropdown -->
                        <div class="col-md-6" id="stateDropdownContainer">
                            <label class="form-label">State</label>
                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="stateDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ !empty($state) ? $state : 'Select State' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="stateDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($locations as $loc)
                                        @if (str_contains(strtolower($loc['state']), strtolower($stateSearch ?? '')))
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                wire:click.prevent="$set('state', '{{ $loc['state'] }}'); selectState('{{ $loc['state'] }}'); closeDropdown('state')">
                                                {{ $loc['state'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('state')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- City Dropdown -->
                        <div class="col-md-6" id="cityDropdownContainer">
                            <label class="form-label">City</label>
                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="cityDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;"
                                    @if (!$cities) disabled @endif>

                                    {{ !empty($city) ? $city : 'Select City' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="cityDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($cities as $c)
                                        @if (str_contains(strtolower($c), strtolower($citySearch ?? '')))
                                            <a href="#" class="dropdown-item d-flex align-items-center"
                                                wire:click.prevent="$set('city', '{{ $c }}'); closeDropdown('city')">
                                                {{ $c }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('city')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Postcode -->
                        <div class="col-md-6">
                            <label class="form-label">Postcode</label>
                            <input type="text" class="form-control" wire:model="postcode"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                placeholder="Enter postal code">
                            @error('postcode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-6" id="countryDropdownContainer">
                            <label class="form-label">Country</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="countryDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">

                                    {{ !empty($country) ? $country : 'Select Country' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="countryDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">
                                    <input type="text" class="form-control mb-2" placeholder="Search country..."
                                        wire:model.live="countrySearch">

                                    @foreach ($filteredCountries as $c)
                                        <a href="#" class="dropdown-item d-flex align-items-center"
                                            wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">

                                            <!-- Flag Image -->
                                            <img src="{{ $c['image'] }}" alt="{{ $c['name'] }}"
                                                style="width:20px; height:15px; margin-right:8px;">

                                            {{ $c['name'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            @error('country')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nationality -->
                        <div class="col-md-6">
                            <label class="form-label">Nationality</label>
                            <input type="text" class="form-control" wire:model="nationality"
                                placeholder="Enter nationality">
                            @error('nationality')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Home Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Home Phone</label>
                            <input type="text" class="form-control" wire:model="home_phone"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                placeholder="Enter home phone no.">
                            @error('home_phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>



                        <!-- Personal Email -->
                        <div class="col-md-6">
                            <label class="form-label">Personal Email</label>
                            <input type="email" class="form-control" wire:model="personal_email"
                                placeholder="Enter personal email address">
                            @error('personal_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Gender -->
                        <div class="col-md-6" id="genderDropdownContainer">
                            <label class="form-label">Gender</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="genderDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $gender ? ucfirst($gender) : 'Select Gender' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="genderDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($genderOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($genderSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('gender', '{{ $option }}'); closeDropdown('gender')">
                                                {{ ucfirst($option) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('gender')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Marital Status -->
                        <div class="col-md-6" id="maritalDropdownContainer">
                            <label class="form-label">Marital Status</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button" id="maritalDropdownButton"
                                    style="border:1px solid #ccc; background:#fff;">
                                    {{ $marital_status ? ucfirst($marital_status) : 'Select Status' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="maritalDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($maritalOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($maritalSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('marital_status', '{{ $option }}'); closeDropdown('marital')">
                                                {{ ucfirst($option) }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('marital_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Tax Reference Number -->
                        <div class="col-md-6">
                            <label class="form-label">Tax Reference Number</label>
                            <input type="text" class="form-control" wire:model="tax_reference_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                placeholder="Enter tax reference no.">
                            @error('tax_reference_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Immigration Status -->
                        <div class="col-md-6" id="immigrationDropdownContainer">
                            <label class="form-label">Immigration Status / Visa Type</label>

                            <div style="position:relative;">
                                <!-- Button -->
                                <button class="btn btn-sm w-100 text-start" type="button"
                                    id="immigrationDropdownButton" style="border:1px solid #ccc; background:#fff;">

                                    {{ !empty($immigration_status) ? $immigration_status : 'Select Immigration Status / Visa Type' }}
                                </button>

                                <!-- Dropdown -->
                                <div id="immigrationDropdownMenu" wire:ignore.self
                                    style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px;">


                                    @foreach ($immigrationOptions as $option)
                                        @if (str_contains(strtolower($option), strtolower($immigrationSearch ?? '')))
                                            <a href="#" class="dropdown-item"
                                                wire:click.prevent="$set('immigration_status', '{{ $option }}'); closeDropdown('immigration')">
                                                {{ $option }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            @error('immigration_status')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- BRP Number -->
                        <div class="col-md-6">
                            <label class="form-label">BRP Number</label>
                            <input type="text" class="form-control" wire:model="brp_number"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Enter BRP no.">
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
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                placeholder="Enter passport no.">
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

                        <hr>
                        @if (!empty($customFields) && $customFields->count())
                            @foreach ($customFields as $field)
                                <div class="col-md-6 mb-2">
                                    <label class="form-label">
                                        {{ $field->name }}
                                        @if ($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($field->type === 'text')
                                        <input type="text" class="form-control"
                                            placeholder="Enter {{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'number')
                                        <input type="number" class="form-control"
                                            placeholder="Enter {{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'date')
                                        <input type="date" class="form-control"
                                            placeholder="{{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'textarea')
                                        <textarea class="form-control" placeholder="Enter {{ $field->name }}"
                                            wire:model.defer="customValues.{{ $field->id }}"></textarea>
                                    @elseif($field->type === 'select')
                                        <select class="form-select"
                                            wire:model.defer="customValues.{{ $field->id }}">
                                            <option value="">{{ $field->name }}</option>
                                            @foreach ($field->options ?? [] as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        @endif


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
</div>

<script src="{{ asset('js/company/changePassword.js') }}"></script>
<script src="{{ asset('js/company/document.js') }}"></script>


<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.error(err));
    }


    Livewire.on('confirmDelete', employeeId => {
        if (confirm("Are you sure you want to delete this employee?")) {
            @this.call('handleDelation', employeeId);
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


<script>
    document.addEventListener('click', function(e) {
        ['country', 'state', 'city', 'immigration', 'gender', 'marital'].forEach(type => {
            const btn = document.getElementById(type + 'DropdownButton');
            const menu = document.getElementById(type + 'DropdownMenu');
            if (btn && menu) {
                if (btn.contains(e.target)) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                } else if (!menu.contains(e.target)) {
                    menu.style.display = 'none';
                }
            }
        });
    });
</script>


<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = "password";
            icon.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
</script>
