<x-layouts.app>
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Back to Employees List -->
            <div class="text-end mb-2">
                <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                    style="background-color:#f8f9fa; border:1px solid #0d6efd; padding:6px 12px; font-size:0.875rem;"
                    href="{{ route('super-admin.companies') }}">
                    <i class="bi bi-arrow-left me-2"></i>
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



                    </div>
                </div>
            </div>


            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="tab-content">

                    <!-- Employee Overview -->
                    <div class="tab-pane fade show active" id="overview" role="tabpanel"
                        aria-labelledby="overview-tab">
                        <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                            <div class="card-header bg-white py-4 border-bottom-0"
                                style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center">
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
                                                <img src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                    class="img-fluid shadow-sm clickable-image"
                                                    style="min-width: 120px; width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid var(--bs-info); cursor: pointer;"
                                                    data-src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                    alt="Employee Avatar">

                                                <span
                                                    class="badge rounded-pill position-absolute p-2 
                                {{ $details->is_active ? 'bg-success border border-white' : 'bg-secondary border border-white' }}"
                                                    style="transform: translate(25%, 25%); bottom: 12px;
                                    right: 8px ">
                                                    <i
                                                        class="bi {{ $details->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </span>
                                            </div>

                                            <h5 class="mb-1 fw-bold">{{ $details->full_name }}</h5>
                                            <p class="text-muted small fw-bold mb-0">{{ $details->job_title ?: 'N/A' }}
                                            </p>

                                        </div>
                                    </div>



                                    <!-- Details -->
                                    <div class="col-lg-9 col-md-8">
                                        <h5 class="fw-bold mb-3 text-info text-center text-md-start">
                                            Employment & Contact
                                        </h5>

                                        <div class="row">

                                            <!-- Left Info -->
                                            <div class="col-md-6">
                                                <div class="p-3 pb-md-3 pb-0 rounded bg-light-subtle h-100">

                                                    <div class="mb-3">
                                                        <div class="text-muted fw-bold small">Work Email</div>
                                                        <div class="fw-medium text-truncate">
                                                            {{ $details->email }}
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <div class="text-muted fw-bold small">Work Phone</div>
                                                        <div>
                                                            {{ $details->user->phone_no ?? 'N/A' }}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-muted fw-bold small">Role</div>
                                                        <div>
                                                            {{ ucfirst($details->role) }}
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>





                                            <!-- Right Info -->
                                            <div class="col-md-6">
                                                <div class="p-3 rounded bg-light-subtle h-100">

                                                    <div class="mb-3">
                                                        <div class="text-muted small fw-bold">Company</div>
                                                        <div>
                                                            {{ $details->company->company_name ?? 'N/A' }}
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
                                                                @foreach ($departments->take($max) as $department)
                                                                    <span
                                                                        class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                                                        {{ $department->name }}
                                                                    </span>
                                                                @endforeach


                                                            </div>
                                                        @endif
                                                    </div>



                                                    <div>
                                                        <div class="text-muted fw-bold small mb-1">Teams</div>

                                                        @php
                                                            $assignedTeams = $details->user
                                                                ? $details->user->teams
                                                                : collect();
                                                            $max = 5;
                                                        @endphp

                                                        @if ($assignedTeams->isEmpty())
                                                            <span class="text-muted fst-italic">N/A</span>
                                                        @else
                                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                                @foreach ($assignedTeams->take($max) as $team)
                                                                    @php
                                                                        $isLead =
                                                                            $team->team_lead_id === $details->user_id;
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
                                                {{ ucfirst($details->salary_type ?? 'N/A') }}
                                            </p>
                                        </div>

                                        <div
                                            class="p-3 bg-light rounded-3 mb-3 border-start border-3
                        @if (($details->salary_type ?? '') === 'hourly') border-warning @else border-success @endif">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                                style="font-size:0.7rem;">
                                                Employment Type
                                            </small>
                                            <p class="fw-bold mb-0
                            @if (($details->salary_type ?? '') === 'hourly') text-warning @else text-success @endif"
                                                style="font-size:0.9rem;">
                                                @if (($details->salary_type ?? '') === 'hourly')
                                                    Part Time
                                                @else
                                                    Full Time
                                                @endif
                                            </p>
                                        </div>

                                        @if (($details->salary_type ?? '') === 'hourly')
                                            <div class="p-3 bg-light rounded-3">
                                                <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                                    style="font-size:0.7rem;">
                                                    Contract Hours
                                                </small>
                                                <p class="fw-bold mb-0 text-dark" style="font-size:0.9rem;">
                                                    {{ $details->contract_hours ?? 'N/A' }} hours/week
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
                                                {{ $details->start_date?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>

                                        <div
                                            class="p-3 rounded-3
                        @if ($details->end_date ?? null) bg-danger-subtle border border-danger @else bg-success-subtle border border-success @endif">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1"
                                                style="font-size:0.7rem;">
                                                End Date
                                            </small>
                                            <p class="fw-bolder mb-0
                            @if ($details->end_date ?? null) text-danger @else text-success @endif"
                                                style="font-size:1rem;">
                                                @if ($details->end_date ?? null)
                                                    {{ $details->end_date->format('d M Y') }}
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
                    <div class="tab-pane fade" id="documentsSection" role="tabpanel"
                        aria-labelledby="documents-tab">
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
                                        $hasDocs = $details->documents->isNotEmpty();

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
                                                $docsForType = $details->documents->where('doc_type_id', $type->id);
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
                                                                    onclick="openDocumentModal({{ $type->id }}, {{ $doc->id }}, '{{ $doc->document_url }}', '{{ $doc->expires_at }}', '{{ $doc->comment }}')"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#openDocumentModal"
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
                                        <i class="bi bi-person" style="margin-right:0.5rem;"></i> Core Details &
                                        Contact
                                    </div>
                                    <div style="padding:1rem;">
                                        <dl style="margin:0;">
                                            @php $profile = $details->profile; @endphp

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
                                                $rtw = $profile->right_to_work_expiry;
                                                $rtwExpired = $rtw && \Carbon\Carbon::parse($rtw)->isPast();
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
                                                $passport = $profile->passport_expiry;
                                                $passportExpired =
                                                    $passport && \Carbon\Carbon::parse($passport)->isPast();
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
                                                $brp = $profile->brp_expiry_date;
                                                $brpExpired = $brp && \Carbon\Carbon::parse($brp)->isPast();
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
                        </div>
                    </div>



                </div>
            </div>
        </div>


        @include('livewire.backend.company.components.document-view')

    </div>


    <script src="{{ asset('js/company/document.js') }}"></script>

</x-layouts.app>
