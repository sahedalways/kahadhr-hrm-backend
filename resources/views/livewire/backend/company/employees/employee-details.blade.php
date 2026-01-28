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
            <div class="card shadow-sm"
                 style="border-radius:14px; border:none; overflow:hidden; background:#fff;">

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

                <div class="list-group list-group-flush"
                     role="tablist">

                    <a class="list-group-item list-group-item-action {{ $activeTab === 'overview' ? 'active' : '' }}"
                       href="#overview"
                       role="tab"
                       data-bs-toggle="tab"
                       wire:click.prevent="$set('activeTab', 'overview')"
                       style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
               {{ $activeTab === 'overview' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                        <i class="bi bi-person-lines-fill"
                           style="font-size:1.1rem; {{ $activeTab === 'overview' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                        Employee Overview
                    </a>

                    <a class="list-group-item list-group-item-action {{ $activeTab === 'personalInfo' ? 'active' : '' }}"
                       href="#personalInfo"
                       role="tab"
                       data-bs-toggle="tab"
                       wire:click.prevent="$set('activeTab', 'personalInfo')"
                       style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
               {{ $activeTab === 'personalInfo' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                        <i class="bi bi-person-badge"
                           style="font-size:1.1rem; {{ $activeTab === 'personalInfo' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                        Personal Information
                    </a>

                    <a class="list-group-item list-group-item-action {{ $activeTab === 'emeregeny' ? 'active' : '' }}"
                       href="#emeregeny"
                       role="tab"
                       data-bs-toggle="tab"
                       wire:click.prevent="$set('activeTab', 'emeregeny')"
                       style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
               {{ $activeTab === 'emeregeny' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                        <i class="bi bi-briefcase"
                           style="font-size:1.1rem; {{ $activeTab === 'emeregeny' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                        Emergency Contacts
                    </a>

                    <a class="list-group-item list-group-item-action {{ $activeTab === 'documentsSection' ? 'active' : '' }}"
                       href="#documentsSection"
                       role="tab"
                       data-bs-toggle="tab"
                       wire:click.prevent="$set('activeTab', 'documentsSection')"
                       style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
               {{ $activeTab === 'documentsSection' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                        <i class="bi bi-folder"
                           style="font-size:1.1rem; {{ $activeTab === 'documentsSection' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                        Documents
                    </a>

                </div>
            </div>
        </div>



        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="tab-content">

                <!-- Employee Overview -->
                <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }}"
                     id="overview"
                     role="tabpanel"
                     aria-labelledby="overview-tab">
                    <div class="card border-0 shadow-lg"
                         style="border-radius: 1rem;">
                        <div class="card-header bg-white py-4 border-bottom-0 d-flex align-items-center justify-content-between"
                             style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">

                            <!-- Title -->
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="fas fa-user-circle me-3 text-info"></i>
                                Employee Overview
                                </h4>

                                <!-- Menu Button -->
                                <div class="dropdown"
                                     wire:ignore.self>
                                    <button class="btn btn-sm btn-light border-0 px-md-3 px-2"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            wire:loading.attr="disabled"
                                            wire:target="sendVerificationLink, sendPasswordResetLink, toggleStatus">

                                        <!-- Normal Icon -->
                                        <span wire:loading.remove
                                              wire:target="sendVerificationLink, sendPasswordResetLink, toggleStatus">
                                            <i class="fas fa-ellipsis-v text-muted"></i>
                                        </span>

                                        <!-- Loading Icon -->
                                        <span wire:loading
                                              wire:target="sendVerificationLink, sendPasswordResetLink, toggleStatus">
                                            <span class="spinner-border spinner-border-sm text-muted"></span>
                                        </span>

                                    </button>


                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li>
                                            <a class="dropdown-item"
                                               href="#"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editProfile"
                                               wire:click="editProfile({{ $employee->id }})">
                                                <i class="fas fa-edit me-2 text-muted"></i> Edit Profile
                                            </a>
                                        </li>
                                        @if (!$employee->verified && !$employee->user)
                                            <li>
                                                <a class="dropdown-item"
                                                   href="#"
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


                                        @if ($employee->user)
                                            <li>
                                                <a class="dropdown-item"
                                                   href="#"
                                                   wire:click.prevent="sendPasswordResetLink({{ $employee->id }})"
                                                   wire:loading.attr="disabled"
                                                   wire:target="sendPasswordResetLink({{ $employee->id }})">

                                                    <i class="fas fa-user-lock me-2 text-muted"></i>

                                                    <span wire:loading.remove
                                                          wire:target="sendPasswordResetLink({{ $employee->id }})">
                                                        Password Reset Link
                                                    </span>

                                                    <span wire:loading
                                                          wire:target="sendPasswordResetLink({{ $employee->id }})">
                                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                                        Sending...
                                                    </span>
                                                </a>
                                            </li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item"
                                               href="#"
                                               wire:click.prevent="toggleStatus({{ $employee->id }})"
                                               wire:loading.attr="disabled"
                                               wire:target="toggleStatus">

                                                <span wire:loading.remove
                                                      wire:target="toggleStatus">
                                                    @if ($employee->is_active == 1)
                                                        <i class="fas fa-user-minus me-2 text-muted"></i> Change to
                                                        Former
                                                    @else
                                                        <i class="fas fa-user-check me-2 text-muted"></i> Change to
                                                        Active
                                                    @endif
                                                </span>

                                                <span wire:loading
                                                      wire:target="toggleStatus">
                                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                                    Updating...
                                                </span>
                                            </a>
                                        </li>


                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>
                                            <a class="dropdown-item text-danger"
                                               href="#"
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

                                            <span class="badge rounded-pill position-absolute p-2
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

                                        <h6 class="mb-1 fw-bold">{{ $employee->full_name }}</h5>

                                            <!-- Verified / Unverified Badge -->
                                            @if ($employee->user)
                                                @if ($employee->user->email_verified_at && $this->isProfileComplete())
                                                    <span class="badge bg-success mb-2">Verified</span>
                                                @else
                                                    <span class="badge bg-light text-dark mb-2">Not Verified</span>
                                                @endif

                                            @endif





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

                                            <style>
                                                @keyframes blinkRed {
                                                    0% {
                                                        color: #dc3545;
                                                    }

                                                    50% {
                                                        color: transparent;
                                                    }

                                                    100% {
                                                        color: #dc3545;
                                                    }
                                                }

                                                .blink-red {
                                                    animation: blinkRed 1s infinite;
                                                }

                                                #countryDropdownButton,
                                                #stateDropdownButton,
                                                #cityDropdownButton,
                                                #countryDropdownMenu,
                                                #stateDropdownMenu,
                                                #cityDropdownMenu {
                                                    box-shadow: none !important;
                                                }

                                                #countryDropdownMenu {
                                                    overflow-y: auto;

                                                    overflow-x: hidden;

                                                }
                                            </style>



                                            @if ($employee->nationality === 'British')
                                                <td>
                                                    <div class="text-center mt-3">
                                                        <span
                                                              style="color: #6c757d; font-size: 13px; display: block;">Right
                                                            to Work Expiry</span>
                                                        <span class="badge mt-1"
                                                              style="background:#f8f9fa; color:#6c757d; font-weight:600; border: 1px solid #dee2e6;">
                                                            Not Required (British)
                                                        </span>
                                                    </div>
                                                </td>
                                            @else
                                                @if ($latestShareDoc && $latestShareDoc->expires_at)
                                                    <td>
                                                        <div class="text-center mt-3">
                                                            <span
                                                                  style="color: #6c757d; font-size: 14px; display: block; margin-bottom: 2px;">
                                                                Right to Work Expiry
                                                            </span>

                                                            <strong class="{{ $daysLeft !== null && $daysLeft <= 60 ? 'blink-red' : '' }}"
                                                                    style="color: {{ $daysLeft !== null && $daysLeft <= 60 ? '#dc3545' : '#198754' }};
                       font-size: 17px; display: block;">
                                                                {{ \Carbon\Carbon::parse($latestShareDoc->expires_at)->format('d F Y') }}
                                                            </strong>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td>
                                                        <div class="text-center mt-3">
                                                            <span
                                                                  style="color: #6c757d; font-size: 13px; display: block;">Right
                                                                to Work Expiry</span>
                                                            <span class="badge bg-light text-muted mt-1"
                                                                  style="border: 1px dashed #ced4da;">
                                                                Not Verified
                                                            </span>
                                                        </div>
                                                    </td>
                                                @endif
                                            @endif


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

                <div class="tab-pane fade {{ $activeTab === 'emeregeny' ? 'show active' : '' }}"
                     id="emeregeny"
                     role="tabpanel"
                     aria-labelledby="emeregeny-tab">
                    <div class="card border-0 shadow-sm"
                         style="border-radius:1rem; background-color: #f8f9fa;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                {{-- Left : Title --}}
                                <h5 class="fw-bold text-dark mb-0">
                                    Emergency contacts
                                </h5>


                                @if ($employee->profile?->addressHistory)
                                    @php
                                        $addr = $employee->profile->addressHistory;
                                    @endphp

                                    <div class="text-end"
                                         style="max-width: 420px;">
                                        <div class="fw-semibold text-muted mb-1"
                                             style="font-size: 13px;">
                                            Previous Address
                                        </div>

                                        <div class="small text-dark"
                                             style="line-height: 1.4;">
                                            {{ $addr->house_no }},
                                            {{ $addr->street }},
                                            {{ $addr->city }},
                                            {{ $addr->state }},
                                            {{ $addr->postcode }}<br>
                                            <span class="text-muted">{{ $addr->country }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>


                            @if ($employee->emergencyContacts->count() < 2)
                                <button class="btn mb-4 d-flex align-items-center px-3 py-2"
                                        style="background-color: #2f6dca; color: white; border-radius: 8px; font-weight: 500;"
                                        wire:click="openEmergencyContactModal">

                                    <i class="fas fa-user-plus me-2"></i> Add emergency contact
                                </button>
                            @else
                                <div class="alert alert-warning mb-4">
                                    <strong>Limit Reached:</strong> You can only add <strong>2 emergency
                                        contacts</strong>.
                                </div>
                            @endif


                            <div class="table-responsive bg-white shadow-sm border"
                                 style="border-radius: 12px;">
                                <table class="table mb-0 align-middle">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 ps-4 py-3 text-muted fw-bold"
                                                style="font-size: 14px;">Name</th>
                                            <th class="border-0 py-3 text-muted fw-bold"
                                                style="font-size: 14px;">Relationship</th>
                                            <th class="border-0 py-3 text-muted fw-bold"
                                                style="font-size: 14px;">Mobile</th>
                                            <th class="border-0 py-3 text-muted fw-bold"
                                                style="font-size: 14px;">E-mail</th>
                                            <th class="border-0 py-3 text-muted fw-bold"
                                                style="font-size: 14px;">Address</th>
                                            <th class="border-0 py-3 text-muted fw-bold text-center"
                                                style="font-size: 14px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->emergencyContacts as $contact)
                                            <tr>
                                                <td class="ps-4 py-4 fw-bold text-dark">{{ $contact->name }}</td>
                                                <td class="text-muted">{{ $contact->relationship }}</td>
                                                <td class="text-muted">
                                                    {{ $contact->mobile }}<br>

                                                </td>
                                                <td class="text-muted">{{ $contact->email ?? '' }}</td>
                                                <td class="text-muted"
                                                    style="max-width: 200px;">
                                                    {!! nl2br(e($contact->address)) !!}
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-3">
                                                        <a href="#"
                                                           class="text-dark"
                                                           wire:click.prevent="openEditEmergencyContactModal({{ $contact->id }})">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>

                                                        <a href="#"
                                                           class="text-danger"
                                                           onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                                           wire:click="deleteEmergencyContact({{ $contact->id }})">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if ($employee->emergencyContacts->isEmpty())
                                            <tr>
                                                <td colspan="6"
                                                    class="text-center py-5 text-muted">
                                                    No emergency contacts found.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Documents -->
                <div class="tab-pane fade {{ $activeTab === 'documentsSection' ? 'show active' : '' }}"
                     id="documentsSection"
                     role="tabpanel"
                     aria-labelledby="documents-tab">

                    <div class="card border-0 shadow-lg"
                         style="border-radius: 1rem;">
                        <div class="card-header bg-white py-4 border-bottom-0"
                             style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
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
                                            <h5 class="mt-2 mb-0 fw-semibold text-white">No documents found for this
                                                employee.</h5>
                                        </div>
                                    </div>
                                @else
                                    @php
                                        $grouped = $employee->documents
                                            ->sortByDesc('created_at')
                                            ->groupBy(function ($doc) {
                                                return $doc->documentType->name ?? 'Unknown Type';
                                            });
                                    @endphp

                                    @foreach ($grouped as $type => $docs)
                                        @php
                                            $latestDocs = $docs->sortByDesc('created_at')->take(3)->values();

                                            $latestDoc = $docs->sortByDesc('created_at')->first();

                                            $latestExpiresAt =
                                                $latestDoc && $latestDoc->expires_at
                                                    ? \Carbon\Carbon::parse($latestDoc->expires_at)
                                                    : null;

                                            $showTypeNotify = false;
                                            $notificationType = null;

                                            if ($latestExpiresAt) {
                                                if ($latestExpiresAt->isPast()) {
                                                    $showTypeNotify = true;
                                                    $notificationType = 'expired';
                                                } elseif (
                                                    now()->diffInDays($latestExpiresAt, false) > 0 &&
                                                    now()->diffInDays($latestExpiresAt, false) <= 60
                                                ) {
                                                    $showTypeNotify = true;
                                                    $notificationType = 'soon';
                                                }
                                            }
                                        @endphp

                                        <div class="col-md-6 col-sm-12">
                                            <div class="card border-0 shadow-sm rounded-4 h-100 employee-doc-card"
                                                 style="background-color: #fcfcfc;">

                                                <div class="card-header bg-info text-white fw-bold d-flex align-items-center py-3"
                                                     style="border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                                                    <i class="bi bi-folder me-2 fs-5"></i> {{ $type }}
                                                </div>

                                                <div class="card-body d-flex flex-column p-3">

                                                    <div class="mb-3 flex-grow-1">

                                                        <div class="border rounded p-2 mb-2"
                                                             style="max-width:240px; position:relative;">

                                                            @if ($showTypeNotify)
                                                                <span wire:click.stop="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')"
                                                                      wire:loading.attr="disabled"
                                                                      title="Notify employee"
                                                                      style="position:absolute; top:6px; right:6px; background:#dc3545; color:#fff; width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; cursor:pointer; transition:all 0.25s ease; box-shadow:0 3px 8px rgba(0,0,0,0.25);"
                                                                      onmouseover="this.style.transform='scale(1.2)'; this.style.background='#b02a37';"
                                                                      onmouseout="this.style.transform='scale(1)'; this.style.background='#dc3545';">

                                                                    <i class="bi bi-bell-fill"
                                                                       wire:loading.remove
                                                                       wire:target="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')">
                                                                    </i>

                                                                    <i class="bi bi-arrow-repeat"
                                                                       wire:loading
                                                                       wire:target="notifyEmployee({{ $latestDoc->doc_type_id }}, {{ $employee->id }}, '{{ $notificationType }}')"
                                                                       style="animation:spin 1s linear infinite;">
                                                                    </i>

                                                                </span>
                                                            @endif



                                                            <div class="d-flex align-items-center"
                                                                 style="overflow-x:auto; overflow-y:hidden; padding:16px 8px; min-height:105px; max-width:230px; scrollbar-width:none;">

                                                                @foreach ($latestDocs as $index => $doc)
                                                                    @php
                                                                        $colors = [
                                                                            '#4e73df',
                                                                            '#1cc88a',
                                                                            '#36b9cc',
                                                                            '#f6c23e',
                                                                            '#e74a3b',
                                                                        ];
                                                                        $currentColor =
                                                                            $colors[$index % count($colors)];
                                                                        $extension = pathinfo(
                                                                            $doc->file_path,
                                                                            PATHINFO_EXTENSION,
                                                                        );
                                                                        $expiresAt = $doc->expires_at
                                                                            ? \Carbon\Carbon::parse($doc->expires_at)
                                                                            : null;

                                                                        $isExpired = $expiresAt && $expiresAt->isPast();

                                                                        $isExpiringSoon =
                                                                            $expiresAt &&
                                                                            !$isExpired &&
                                                                            $expiresAt->isFuture() &&
                                                                            $expiresAt->lte(now()->addDays(60));

                                                                        $zIndex = count($latestDocs) - $index;
                                                                    @endphp

                                                                    <div class="doc-card-wrapper"
                                                                         style="position:relative; min-width:90px; margin-right:-70px; z-index:{{ $zIndex }}; transition:all 0.4s cubic-bezier(0.165,0.84,0.44,1); cursor:pointer;"
                                                                         onmouseover="this.style.zIndex='999';this.style.transform='translateY(-12px) scale(1.08)';this.style.marginRight='10px';"
                                                                         onmouseout="this.style.zIndex='{{ $zIndex }}';this.style.transform='translateY(0) scale(1)';this.style.marginRight='-70px';"
                                                                         data-bs-toggle="modal"
                                                                         data-bs-target="#documentModal"
                                                                         wire:click="openDocModal({{ $doc->id }}, {{ $index + 1 }})">

                                                                        <div
                                                                             style="background:white; border-radius:10px; padding:10px; box-shadow:-5px 0 12px rgba(0,0,0,0.08); border-top:3px solid {{ $currentColor }}; text-align:center; border:1px solid #eee;">
                                                                            <div
                                                                                 style="width:36px; height:36px; background:{{ $currentColor }}15; color:{{ $currentColor }}; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 6px; font-size:1.1rem;">
                                                                                @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                                                    <i class="bi bi-image"></i>
                                                                                @elseif($extension === 'pdf')
                                                                                    <i
                                                                                       class="bi bi-file-earmark-pdf"></i>
                                                                                @else
                                                                                    <i
                                                                                       class="bi bi-file-earmark-text"></i>
                                                                                @endif
                                                                            </div>

                                                                            <div
                                                                                 style="font-weight:700;font-size:0.65rem;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                                                File-{{ $index + 1 }}.{{ $extension }}
                                                                            </div>

                                                                            <div
                                                                                 style="font-size:0.6rem;margin-top:4px;">
                                                                                <div
                                                                                     style="color:{{ $isExpired ? '#dc3545' : ($isExpiringSoon ? '#fd7e14' : '#999') }};">
                                                                                    {{ $doc->expires_at ? date('d M, Y', strtotime($doc->expires_at)) : 'No Expiry' }}
                                                                                </div>

                                                                                @if ($isExpired)
                                                                                    <span class="badge bg-danger mt-1"
                                                                                          style="font-size:0.55rem;">Expired</span>
                                                                                @elseif($isExpiringSoon)
                                                                                    <span class="badge bg-warning text-dark mt-1"
                                                                                          style="font-size:0.55rem;">Expires
                                                                                        Soon</span>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <style>
                                        .d-flex::-webkit-scrollbar {
                                            display: none;
                                        }
                                    </style>

                                    <link rel="stylesheet"
                                          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
                                @endif

                            </div>
                        </div>

                    </div>
                </div>



                <div class="tab-pane fade {{ $activeTab === 'personalInfo' ? 'show active' : '' }}"
                     id="personalInfo"
                     role="tabpanel">

                    @php
                        $profile = $employee->profile;
                        $emp = $employee;
                    @endphp


                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fas fa-id-card-alt text-dark me-2"></i>Employee Profile Summary
                            </h4>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                Last Updated:
                                {{ $profile && $profile->updated_at ? $profile->updated_at->format('d M, Y') : 'N/A' }}
                            </span>
                    </div>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius: 20px;">
                                <div class="card-header bg-info bg-opacity-10 border-0 pt-4 px-4">
                                    <h6 class="fw-bold text-dark text-uppercase mb-0"><i
                                           class="fas fa-user-circle me-2"></i>Identity & Contact</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-12 d-flex align-items-center mb-2">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 45px; height: 45px;">
                                                <span
                                                      class="fw-bold">{{ substr($emp->f_name, 0, 1) }}{{ substr($emp->l_name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-muted small mb-0">Full Name</p>
                                                <h6 class="fw-bold mb-0">{{ $profile->title ?? '' }}
                                                    {{ $emp->f_name ?? '' }} {{ $emp->l_name ?? '' }}</h6>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Work Email</label>
                                            <span class="fw-semibold text-dark">{{ $emp->email ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Personal Email</label>
                                            <span
                                                  class="fw-semibold text-dark">{{ $profile->personal_email ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Work Mobile</label>
                                            <span class="fw-semibold text-dark">{{ $emp->phone_no ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Personal Mobile</label>
                                            <span
                                                  class="fw-semibold text-dark">{{ $profile->mobile_phone ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius: 20px;">
                                <div class="card-header bg-info bg-opacity-10 border-0 pt-4 px-4">
                                    <h6 class="fw-bold text-dark text-uppercase mb-0"><i
                                           class="fas fa-briefcase me-2"></i>Employment Details</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Job Title</label>
                                            <span class="fw-bold text-dark">{{ $emp->job_title ?? 'N/A' }}</span>
                                        </div>



                                        <div class="col-sm-6">
                                            <div class="text-muted fw-bold small mb-1">Departments</div>

                                            @php

                                                $max = 5;
                                            @endphp

                                            @if ($departments->isEmpty())
                                                <span class="text-muted ">N/A</span>
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

                                        <div class="col-sm-6">
                                            <div class="text-muted small d-block mb-1">Team</div>

                                            @php
                                                $assignedTeams = $employee->user ? $employee->user->teams : collect();
                                                $max = 5;
                                            @endphp

                                            @if ($assignedTeams->isEmpty())
                                                <span class="text-muted ">N/A</span>
                                            @else
                                                <div class="d-flex flex-wrap align-items-center gap-2">
                                                    @foreach ($assignedTeams->take($showAllTeams ? $assignedTeams->count() : $max) as $team)
                                                        @php
                                                            $isLead = $team->team_lead_id === $employee->user_id;
                                                        @endphp

                                                        <span
                                                              class="badge rounded-pill px-3 py-2 {{ $isLead ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                                                            {{ $team->name }}
                                                            @if ($isLead)
                                                                <span class="ms-1 fw-semibold">⭐ Leader</span>
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

                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Contract Hours (Weekly)</label>
                                            <span
                                                  class="badge bg-success bg-opacity-10 text-white fw-bold">{{ $employee->contract_hours ?? '0' }}
                                                Hours</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Start Date</label>
                                            <span
                                                  class="fw-semibold">{{ !empty($emp->start_date) ? \Carbon\Carbon::parse($emp->start_date)->format('d M, Y') : 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Employment Status</label>
                                            <span
                                                  class="badge bg-soft-primary text-primary border border-primary border-opacity-25">{{ ucfirst($emp->employment_status ?? 'N/A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        @if ($profile)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm"
                                     style="border-radius: 20px; background: linear-gradient(to right, #f8f9fa, #ffffff);">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-md-6 border-end-md">
                                                <h6 class="fw-bold text-primary text-uppercase mb-3">
                                                    <i class="fas fa-home me-2"></i>Current Address
                                                </h6>

                                                <p class="fw-semibold text-dark mb-1">
                                                    {{ $profile->address ?? 'N/A' }}
                                                </p>

                                                <span class="text-muted small">
                                                    {{ $profile->city ?? 'N/A' }},
                                                    {{ $profile->state ?? 'N/A' }},
                                                    {{ $profile->postcode ?? 'N/A' }},
                                                    {{ $profile->country ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                <div class="card border-0 shadow-sm"
                                     style="border-radius: 20px; background: linear-gradient(to right, #f8f9fa, #ffffff);">
                                    <div class="card-body p-4">
                                        <div class="row">
                                            <div class="col-md-6 border-end-md">
                                                <h6 class="fw-bold text-primary text-uppercase mb-3">
                                                    <i class="fas fa-home me-2"></i>Current Address
                                                </h6>

                                                <p class="fw-semibold text-dark mb-1">
                                                    N/A
                                                </p>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius: 20px;">
                                <div class="card-header bg-info bg-opacity-10 border-0 pt-4 px-4">
                                    <h6 class="fw-bold text-dark text-uppercase mb-0">
                                        <i class="fas fa-user-tag me-2"></i>Demographics
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    <dl class="row mb-0">

                                        <dt class="col-12 text-muted fw-normal small">Date of Birth</dt>
                                        <dd class="col-12 fw-bold mb-3">
                                            {{ !empty($profile->date_of_birth) ? \Carbon\Carbon::parse($profile->date_of_birth)->format('d F, Y') : 'N/A' }}
                                        </dd>

                                        <dt class="col-12 text-muted fw-normal small">Nationality</dt>
                                        <dd class="col-12 fw-bold mb-3">
                                            {{ $emp->nationality ?? 'N/A' }}
                                        </dd>

                                        <dt class="col-12 text-muted fw-normal small">Gender</dt>
                                        <dd class="col-12 fw-bold text-capitalize mb-3">
                                            {{ $profile->gender ?? 'N/A' }}
                                        </dd>

                                        <dt class="col-12 text-muted fw-normal small">Marital Status</dt>
                                        <dd class="col-12 fw-bold text-capitalize mb-0">
                                            {{ $profile->marital_status ?? 'N/A' }}
                                        </dd>

                                    </dl>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm h-100"
                                 style="border-radius: 20px;">
                                <div class="card-header bg-info bg-opacity-10 border-0 pt-4 px-4">
                                    <h6 class="fw-bold text-dark text-uppercase mb-0"><i
                                           class="fas fa-shield-alt me-2"></i>Compliance & ID</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Immigration Status</label>
                                            <span
                                                  class="fw-bold text-dark">{{ $profile->immigration_status ?? 'N/A' }}</span>
                                        </div>


                                        <div class="col-sm-6">
                                            @php
                                                $shareCodeType = $documentTypes->firstWhere('name', 'Share Code');

                                                $latestShareDoc = null;
                                                $daysLeft = null;
                                                $rtwExp = null;

                                                if ($shareCodeType) {
                                                    $latestShareDoc = $employee
                                                        ->documents()
                                                        ->where('doc_type_id', $shareCodeType->id)
                                                        ->latest('created_at')
                                                        ->first();

                                                    if ($latestShareDoc && $latestShareDoc->expires_at) {
                                                        $rtwExp = \Carbon\Carbon::parse($latestShareDoc->expires_at);
                                                        $daysLeft = now()->diffInDays($rtwExp, false);
                                                    }
                                                }
                                            @endphp

                                            <label class="text-muted small d-block">Right to Work Expiry</label>

                                            @if ($employee->nationality === 'British')
                                                <span class="badge bg-light text-muted mt-1"
                                                      style="border: 1px solid #dee2e6;">
                                                    Not Required (British)
                                                </span>
                                            @elseif ($rtwExp)
                                                <span class="fw-bold {{ $daysLeft !== null && $daysLeft <= 60 ? 'text-danger blink-red' : 'text-dark' }}"
                                                      style="font-size: 15px;">
                                                    {{ $rtwExp->format('d F Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted mt-1"
                                                      style="border: 1px dashed #ced4da;">
                                                    Not Verified
                                                </span>
                                            @endif
                                        </div>




                                        <div class="col-sm-12">
                                            <label class="text-muted small d-block">National Insurance / Tax
                                                Ref</label>
                                            <span
                                                  class="fw-mono fw-bold text-dark">{{ $profile->tax_reference_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="text-muted small d-block">Passport Number</label>
                                            <span
                                                  class="fw-bold text-dark">{{ $profile->passport_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-sm-6">
                                            @php $passExp = !empty($profile->passport_expiry_date) ? \Carbon\Carbon::parse($profile->passport_expiry_date) : null; @endphp
                                            <label class="text-muted small d-block">Passport Expiry</label>
                                            <span
                                                  class="fw-bold {{ $passExp && $passExp->isPast() ? 'text-danger' : 'text-dark' }}">
                                                {{ $passExp ? $passExp->format('d M, Y') : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($customFields->isNotEmpty())
                            <div class="col-12">
                                <div class="card border-0 shadow-sm"
                                     style="border-radius: 20px; background: linear-gradient(to right, #f8f9fa, #ffffff);">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-primary text-uppercase mb-3">
                                            <i class="fas fa-clipboard-list me-2"></i>More Information
                                        </h6>

                                        <div class="row g-3">
                                            @foreach ($customFields as $field)
                                                <div class="col-md-6">
                                                    <label
                                                           class="text-muted small d-block">{{ $field->name }}</label>
                                                    <span class="fw-bold text-dark">
                                                        {{ $customValues[$field->id] ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>



            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    @include('livewire.backend.company.components.document-view')





    <div wire:ignore.self
         class="modal fade"
         id="verifyModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="verifyModal"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-lg"
             role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Verification Centre</h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="verifyAndUpdate">
                    <div class="modal-body">

                        @if (!$passwordVerified)
                            <div class="mb-3"
                                 wire:ignore>
                                <label>Enter Password <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password"
                                           class="form-control extra-padding shadow-sm"
                                           id="password"
                                           wire:model.defer="passwordInput">
                                    <span class="icon-position "
                                          style="cursor:pointer;"
                                          onclick="togglePassword('password', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>


                            </div>
                            <div class="text-end">
                                <button type="button"
                                        class="btn btn-primary"
                                        wire:click="verifyPassword"
                                        wire:loading.attr="disabled"
                                        wire:target="verifyPassword">

                                    <span wire:loading
                                          wire:target="verifyPassword">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                    </span>

                                    <span wire:loading.remove
                                          wire:target="verifyPassword">
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
                                        <input type="email"
                                               class="form-control form-control-sm shadow-sm"
                                               wire:model="new_email"
                                               placeholder="Enter new email"
                                               style="height: 38px;">

                                        <button class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                                type="button"
                                                style="height: 38px;"
                                                wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="requestVerification"
                                                @if ($otpCooldown > 0) disabled @endif>
                                            <span wire:loading
                                                  wire:target="requestVerification">
                                                <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                            </span>
                                            <span wire:loading.remove
                                                  wire:target="requestVerification">
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
                                        <input type="text"
                                               class="form-control shadow-sm form-control-sm"
                                               wire:model="new_mobile"
                                               placeholder="Enter new mobile no."
                                               style="height: 38px;">
                                        <button class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                                type="button"
                                                style="height: 38px;"
                                                wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                                wire:loading.attr="disabled"
                                                wire:target="requestVerification"
                                                @if ($otpCooldown > 0) disabled @endif>
                                            <span wire:loading
                                                  wire:target="requestVerification">
                                                <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                            </span>
                                            <span wire:loading.remove
                                                  wire:target="requestVerification">
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
                                            <input type="text"
                                                   wire:model="otp.{{ $i }}"
                                                   class="form-control text-center otp-field"
                                                   maxlength="1"
                                                   placeholder="-"
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
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="verifyOtp">
                                <span wire:loading
                                      wire:target="verifyOtp">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                </span>
                                <span wire:loading.remove
                                      wire:target="verifyOtp">Verify</span>
                            </button>
                        @endif
                        @endif

                    </div>

                </form>
            </div>
        </div>
    </div>


    <div wire:ignore.self
         class="modal fade"
         id="editProfile"
         tabindex="-1"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg"
                 style="border-radius: 1.25rem; overflow: hidden;">

                <div class="modal-header bg-white border-bottom-0 pt-4 px-4">
                    <div class="d-flex align-items-center">

                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Edit Employee Profile</h5>
                            <small class="text-muted">Update personal and employment details</small>
                        </div>
                    </div>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="updateProfile">
                    <div class="modal-body px-4 pt-2 pb-4">
                        <div class="row g-3">

                            <div class="col-12 mt-4 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-uppercase small text-primary letter-spacing-1">Basic
                                        Information</span>
                                    <div class="flex-grow-1 ms-3 border-bottom border-light"></div>
                                </div>
                            </div>

                            <!-- 1. Title -->
                            <div class="col-md-2">
                                <label class="form-label small fw-semibold text-secondary">Title <span
                                          class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model="title">
                                    <option value="">Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                </select>
                                @error('title')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 2. First Name -->
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">First Name <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="f_name"
                                       placeholder="Enter First Name">
                                @error('f_name')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 3. Last Name -->
                            <div class="col-md-5">
                                <label class="form-label small fw-semibold text-secondary">Last Name <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="l_name"
                                       placeholder="Enter Last Name">
                                @error('l_name')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-uppercase small text-primary letter-spacing-1">Contact
                                        Details</span>
                                    <div class="flex-grow-1 ms-3 border-bottom border-light"></div>
                                </div>
                            </div>

                            <!-- 4. Email -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Email <span
                                          class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i
                                           class="fas fa-envelope text-muted"></i></span>
                                    <input type="email"
                                           class="form-control border-light-subtle bg-light shadow-none"
                                           wire:model="email"
                                           readonly>
                                    <button class="btn btn-outline-primary"
                                            type="button"
                                            wire:click="openModal('email')"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyModal">Change</button>
                                </div>
                            </div>

                            <!-- 5. Personal Email -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Personal Email</label>
                                <input type="email"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="personal_email"
                                       placeholder="personal@example.com">
                            </div>

                            <!-- 6. Mobile -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Mobile <span
                                          class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-light-subtle"><i
                                           class="fas fa-phone-alt text-muted"></i></span>
                                    <input type="text"
                                           class="form-control border-light-subtle bg-light shadow-none"
                                           wire:model="phone_no"
                                           readonly>
                                    <button class="btn btn-outline-primary"
                                            type="button"
                                            wire:click="openModal('mobile')"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyModal">Change</button>
                                </div>
                            </div>

                            <!-- 7. Personal Mobile -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Personal Mobile</label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="home_phone"
                                       placeholder="+44 ..."
                                       pattern="[0-9]+"
                                       inputmode="numeric"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                @error('home_phone')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-uppercase small text-primary letter-spacing-1">Employment
                                        Details</span>
                                    <div class="flex-grow-1 ms-3 border-bottom border-light"></div>
                                </div>


                            </div>

                            <!-- 8. Job Title -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Job Title <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="job_title"
                                       placeholder="Enter Job Title">

                                @error('job_title')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>



                            <!-- 11. Contract Hours -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Contract Hours (Weekly)
                                    <span class="text-danger">*</span></label>
                                <input type="number"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="contract_hours"
                                       placeholder="e.g. 40">

                                @error('contract_hours')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 12. Employment Start Date -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Employment Start Date <span
                                          class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="start_date">

                                @error('start_date')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 13. Employment Status -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Employment Status <span
                                          class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model="employment_status">
                                    <option value="">Select</option>
                                    <option value="full-time">Full time</option>
                                    <option value="part-time">Part time</option>
                                </select>
                                @error('employment_status')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 mb-1">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-uppercase small text-primary letter-spacing-1">Residency
                                        &
                                        Demographics</span>
                                    <div class="flex-grow-1 ms-3 border-bottom border-light"></div>
                                </div>
                            </div>


                            <div class="col-12 mt-4">
                                <div class="p-4 rounded-4  border border-light-subtle shadow-sm">

                                    <div class="d-flex align-items-center mb-4">

                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Current Address Details</h5>

                                        </div>
                                    </div>

                                    <div class="row g-3">

                                        <div class="col-12 mb-2"
                                             x-data="addressAutocomplete()">
                                            <label class="form-label small fw-semibold text-secondary">
                                                Address
                                                <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text"
                                                       class="form-control border-light-subtle py-2 shadow-none"
                                                       wire:model="address"
                                                       placeholder="Type to search your address..."
                                                       autocomplete="off">
                                            </div>
                                            @error('address')
                                                <span class="text-danger x-small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <hr class="my-3 opacity-25">



                                        <div class="col-md-6"
                                             id="countryDropdownContainer">
                                            <label class="form-label">Country <span
                                                      class="text-danger">*</span></label>
                                            <div style="position:relative;">
                                                <button class="btn btn-sm w-100 text-start"
                                                        type="button"
                                                        id="countryDropdownButton"
                                                        style="border:1px solid #ccc; background:#fff;">
                                                    {{ $country ?? 'Select Country' }}
                                                </button>

                                                <div id="countryDropdownMenu"
                                                     wire:ignore.self
                                                     style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                                    <input type="text"
                                                           class="form-control mb-2"
                                                           placeholder="Search country..."
                                                           wire:model.live="countrySearch">

                                                    @foreach ($filteredCountries as $c)
                                                        <a href="#"
                                                           class="dropdown-item d-flex align-items-center"
                                                           wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">
                                                            <img src="{{ $c['flag'] }}"
                                                                 alt="{{ $c['name'] }}"
                                                                 style="width:20px; height:15px; margin-right:8px;">
                                                            {{ $c['name'] }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @error('country')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6"
                                             id="stateDropdownContainer">
                                            <label class="form-label">State </label>
                                            <div style="position:relative;">
                                                <button class="btn btn-sm w-100 text-start"
                                                        type="button"
                                                        id="stateDropdownButton"
                                                        style="border:1px solid #ccc; background:#fff;"
                                                        @if (empty($states)) disabled @endif>
                                                    {{ $state ?? 'Select State' }}
                                                </button>

                                                <div id="stateDropdownMenu"
                                                     wire:ignore.self
                                                     style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                                    <input type="text"
                                                           class="form-control mb-2"
                                                           placeholder="Search state..."
                                                           wire:model.live="stateSearch">

                                                    @forelse ($states as $s)
                                                        <a href="#"
                                                           class="dropdown-item"
                                                           wire:click.prevent="$set('state', '{{ $s['name'] }}'); $set('city', null); closeDropdown()">
                                                            {{ $s['name'] }}
                                                        </a>
                                                    @empty
                                                        <span class="dropdown-item text-muted small">Select country
                                                            first</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                            @error('state')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6"
                                             id="cityDropdownContainer">
                                            <label class="form-label">City </label>
                                            <div style="position:relative;">
                                                <button class="btn btn-sm w-100 text-start"
                                                        type="button"
                                                        id="cityDropdownButton"
                                                        style="border:1px solid #ccc; background:#fff;"
                                                        @if (empty($cities)) disabled @endif>
                                                    {{ $city ?? 'Select City' }}
                                                </button>

                                                <div id="cityDropdownMenu"
                                                     wire:ignore.self
                                                     style="display:none; position:absolute; z-index:1000; width:100%; max-height:200px; overflow-y:auto; background:#fff; border:1px solid #ccc; border-radius:4px; padding: 8px;">
                                                    <input type="text"
                                                           class="form-control mb-2"
                                                           placeholder="Search city..."
                                                           wire:model.live="citySearch">

                                                    @forelse ($cities as $c)
                                                        <a href="#"
                                                           class="dropdown-item"
                                                           wire:click.prevent="$set('city', '{{ $c }}'); closeDropdown()">
                                                            {{ $c }}
                                                        </a>
                                                    @empty
                                                        <span class="dropdown-item text-muted small">Select state
                                                            first</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                            @error('city')
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>








                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Zip / Postal
                                                Code
                                                <span class="text-danger">*</span></label>

                                            <input type="text"
                                                   class="form-control border-light-subtle shadow-none"
                                                   wire:model="postcode"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                   placeholder="e.g. 1234">
                                            @error('postcode')
                                                <span class="text-danger x-small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                    </div>


                                    <div class="row g-3 mt-1">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">House Number
                                                <span class="text-danger">*</span></label>


                                            <input type="text"
                                                   class="form-control border-light-subtle shadow-none"
                                                   wire:model="house_no"
                                                   placeholder="Enter House Number">

                                            @error('house_no')
                                                <span class="text-danger x-small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Street <span
                                                      class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control border-light-subtle shadow-none"
                                                   wire:model="street"
                                                   placeholder="Enter House Number">

                                            @error('street')
                                                <span class="text-danger x-small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <!-- 16. Date of Birth -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Date of Birth <span
                                          class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="date_of_birth">


                                @error('date_of_birth')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 17. Nationality -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Nationality <span
                                          class="text-danger">*</span></label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model.live="nationality">
                                    <option value="">Select</option>
                                    @foreach ($nationalities as $nation)
                                        <option value="{{ $nation }}">{{ $nation }}</option>
                                    @endforeach
                                </select>

                                @error('nationality')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>



                            @if ($nationality && $nationality !== 'British')
                                <div class="col-md-6 ">
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


                            <!-- 18. Immigration Status / Visa Type -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Immigration Status / Visa
                                    Type</label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model="immigration_status">
                                    <option value="">Select</option>
                                    @foreach ($immigrationOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>


                                @error('immigration_status')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 20. National Insurance / Tax Reference -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">National Insurance / Tax
                                    Reference <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="tax_reference_number"
                                       placeholder="Enter NI / Tax Reference">

                                @error('tax_reference_number')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 21. Passport Number -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Passport Number <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="passport_number"
                                       placeholder="Enter Passport Number">

                                @error('passport_number')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 22. Passport Expiry -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Passport Expiry <span
                                          class="text-danger">*</span></label>
                                <input type="date"
                                       class="form-control border-light-subtle shadow-none"
                                       wire:model="passport_expiry_date">

                                @error('passport_expiry_date')
                                    <span class="text-danger x-small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- 23. Gender -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Gender</label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model="gender">
                                    <option value="">Select</option>
                                    @foreach ($genderOptions as $g)
                                        <option value="{{ $g }}">{{ ucfirst($g) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 24. Marital Status -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-secondary">Marital Status</label>
                                <select class="form-select border-light-subtle shadow-none"
                                        wire:model="marital_status">
                                    <option value="">Select</option>
                                    @foreach ($maritalOptions as $m)
                                        <option value="{{ $m }}">{{ ucfirst($m) }}</option>
                                    @endforeach
                                </select>
                            </div>

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
                                        <input type="text"
                                               class="form-control"
                                               placeholder="Enter {{ $field->name }}"
                                               wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'number')
                                        <input type="number"
                                               class="form-control"
                                               placeholder="Enter {{ $field->name }}"
                                               wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'date')
                                        <input type="date"
                                               class="form-control"
                                               placeholder="{{ $field->name }}"
                                               wire:model.defer="customValues.{{ $field->id }}">
                                    @elseif($field->type === 'textarea')
                                        <textarea class="form-control"
                                                  placeholder="Enter {{ $field->name }}"
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

                                    @error('customValues.' . $field->id)
                                        <span class="text-danger x-small mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="modal-footer bg-light bg-opacity-50 border-0 py-3 px-4">
                        <button type="button"
                                class="btn btn-link text-secondary text-decoration-none fw-semibold"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-primary px-4 fw-bold text-white shadow-sm"
                                style="border-radius: 8px;"
                                wire:loading.attr="disabled">
                            <span wire:loading
                                  wire:target="updateProfile">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove
                                  wire:target="updateProfile">
                                Update Profile
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <div wire:ignore.self
         class="modal fade"
         id="documentModal"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="d-flex align-items-start flex-column">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="modal-title"
                                style="font-weight: 800; color: #2d3748; letter-spacing: -0.5px; margin: 0;">
                                File-{{ $modalFileIndex ?? '' }}
                            </h5>

                            <span
                                  style="background: #edf2f7; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; color: #4a5568; text-transform: uppercase;">
                                DOC
                            </span>
                        </div>

                        <div class="d-flex align-items-center">
                            @if ($modalDocument && $modalDocument->expires_at)
                                @php
                                    $expiry = \Carbon\Carbon::parse($modalDocument->expires_at);
                                    $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                                @endphp

                                <div
                                     style="
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                font-weight: 600;
                @if ($daysLeft < 0) background: #fff5f5; color: #c53030; border: 1px solid #feb2b2;
                @elseif($daysLeft === 0) background: #fffaf0; color: #975a16; border: 1px solid #fbd38d;
                @else background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; @endif
            ">
                                    <span
                                          style="
                    height: 8px; width: 8px; border-radius: 50%;
                    @if ($daysLeft < 0) background: #c53030;
                    @elseif($daysLeft === 0) background: #975a16;
                    @else background: #276749; @endif
                "></span>

                                    @if ($daysLeft < 0)
                                        Expired ({{ abs($daysLeft) }} days ago)
                                    @elseif ($daysLeft === 0)
                                        Expires Today
                                    @else
                                        Expires in {{ $daysLeft }} days
                                    @endif
                                </div>
                            @else
                                <div
                                     style="display: flex; align-items: center; gap: 5px; color: #718096; font-size: 0.75rem; font-weight: 500;">
                                    <i class="bi bi-calendar-x"
                                       style="font-size: 0.85rem;"></i>
                                    No Expiry Set
                                </div>
                            @endif
                        </div>
                    </div>


                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div class="container-fluid">
                        <div class="row g-0"
                             style="min-height: 70vh;">

                            {{-- LEFT SIDE : Fields --}}
                            <div class="col-md-4 p-3 border-end"
                                 style="background:#f8fafc;">

                                <h6 class="fw-bold mb-3 text-uppercase text-muted"
                                    style="font-size:12px;">
                                    Document Details
                                </h6>

                                {{-- Document Type --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Type <span
                                              class="text-danger">*</span></label>
                                    <select class="form-select form-select-sm"
                                            wire:model.live="doc_type_id">
                                        <option value="">-- Select --</option>
                                        @foreach ($docTypes as $type)
                                            <option value="{{ $type->id }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>



                                {{-- Expiry --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Expires At <span
                                              class="text-danger">*</span></label>
                                    <input type="date"
                                           class="form-control form-control-sm"
                                           wire:model.live="expires_at">
                                </div>


                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Change File (PDF)</label>

                                    <input type="file"
                                           class="form-control form-control-sm"
                                           wire:model="new_file"
                                           accept="application/pdf">

                                    @error('new_file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    @if ($new_file)
                                        <div class="small text-success mt-1">
                                            New file selected: {{ $new_file->getClientOriginalName() }}
                                        </div>
                                    @endif
                                </div>

                            </div>

                            {{-- RIGHT SIDE : Document Preview --}}
                            <div class="col-md-8 p-0">

                                @if ($modalDocument && $modalDocument->file_path)
                                    <iframe src="{{ asset('storage/' . $modalDocument->file_path) }}"
                                            style="width:100%; height:100%; min-height:70vh; border:0;">
                                    </iframe>
                                @else
                                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                        No file found
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>


                <div class="modal-footer d-flex justify-content-between align-items-center">


                    <div class="d-flex gap-2 position-relative">

                        @if ($modalDocument)
                            <button wire:click="updateDocument({{ $modalDocument->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="updateDocument"
                                    class="btn btn-sm btn-primary">


                                <span wire:loading
                                      wire:target="updateDocument">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Updating...
                                </span>

                                <span wire:loading.remove
                                      wire:target="updateDocument">
                                    <i class="fa fa-edit me-1"></i> Update
                                </span>
                            </button>
                        @endif



                        @if ($modalDocument && $confirmDeleteId === $modalDocument->id)
                            <div style="position:absolute; bottom:45px; left:0; z-index:999;">
                                <div
                                     style="background:#fff; border:1px solid #ddd; padding:8px 10px;
                            border-radius:8px; box-shadow:0 3px 10px rgba(0,0,0,0.15);">
                                    <div style="font-size:12px; margin-bottom:6px;">
                                        Are you sure?
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button wire:click="deleteDocument({{ $modalDocument->id }})"
                                                wire:loading.attr="disabled"
                                                class="btn btn-sm btn-danger">
                                            Yes
                                        </button>
                                        <button wire:click="$set('confirmDeleteId', null)"
                                                class="btn btn-sm btn-secondary">
                                            No
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @elseif ($modalDocument)
                            <button wire:click="confirmDelete({{ $modalDocument->id }})"
                                    class="btn btn-sm btn-danger">
                                <i class="fa fa-trash me-1"></i> Delete
                            </button>
                        @endif

                    </div>

                    {{-- Right side: Close --}}
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Close
                    </button>

                </div>


            </div>
        </div>
    </div>





    <div wire:ignore.self
         class="modal fade"
         id="addEmergencyContact"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">
                        {{ $mode == 'edit' ? 'Edit Emergency Contact' : 'Add Emergency Contact' }}
                    </h6>

                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveContact">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="name"
                                   placeholder="Enter name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="mobile"
                                   placeholder="Enter mobile number"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   class="form-control"
                                   wire:model="email"
                                   placeholder="Enter email (optional)">
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      wire:model="address"
                                      placeholder="Enter address"></textarea>
                            @error('address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Relationship <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="relationship"
                                   placeholder="Enter relationship">
                            @error('relationship')
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
                                wire:target="saveContact">
                            <span wire:loading
                                  wire:target="saveContact">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Saving...
                            </span>
                            <span wire:loading.remove
                                  wire:target="saveContact">
                                {{ $mode == 'edit' ? 'Update' : 'Save' }}
                            </span>
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
        ['country', 'state', 'city', 'immigration'].forEach(type => {
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
    document.addEventListener('livewire:load', function() {
        Livewire.on('documentModalOpened', () => {
            var modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();
        });
    });
</script>


<script>
    window.addEventListener('show-emergency-modal', () => {
        new bootstrap.Modal(document.getElementById('addEmergencyContact')).show();
    });

    window.addEventListener('hide-emergency-modal', () => {
        bootstrap.Modal.getInstance(document.getElementById('addEmergencyContact')).hide();
    });
</script>
