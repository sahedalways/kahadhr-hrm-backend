<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Back to Employees List -->
        <div class="text-end mb-2">
            <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
               style="background-color:#f8f9fa; border:1px solid #0d6efd; padding:6px 12px; font-size:0.875rem;"
               href="{{ route('super-admin.employees') }}">
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
                                                   class="status-toggle">
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
                            <h5 class="mb-4 fw-bold text-dark">Emergency contacts</h4>



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
                                                                         onmouseout="this.style.zIndex='{{ $zIndex }}';this.style.transform='translateY(0) scale(1)';this.style.marginRight='-70px';">

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

                    </div>
                </div>



            </div>
        </div>
    </div>


</div>


<script src="{{ asset('js/company/document.js') }}"></script>
