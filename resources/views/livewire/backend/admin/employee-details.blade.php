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
                <div class="card border-0 shadow-lg" style="border-radius: 0.75rem; overflow: hidden;">
                    <div class="card-body p-0">

                        <div class="list-group list-group-flush" role="tablist">

                            <div class="list-group-item bg-info text-white text-uppercase fw-bold small py-3 px-4"
                                style="border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
                                <i class="bi bi-person-circle me-2"></i> Employee Profile
                            </div>

                            <a class="list-group-item list-group-item-action active py-3 px-4 fw-bold border-0"
                                data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview"
                                aria-selected="true"
                                style="border-left: 5px solid var(--bs-info) !important; color: var(--bs-info) !important; background-color: #ebf6ff; transition: all 0.25s ease;">
                                <i class="bi bi-person-lines-fill me-3 fs-5 align-middle"></i> Employee Overview
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0 text-dark"
                                data-bs-toggle="tab" href="#personalInfo" role="tab" aria-controls="personalInfo"
                                aria-selected="false"
                                style="transition: background-color 0.25s ease; border-left: 5px solid transparent;">
                                <i class="bi bi-person-badge me-3 fs-5 align-middle"></i> Personal Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0 text-dark"
                                data-bs-toggle="tab" href="#employment" role="tab" aria-controls="employment"
                                aria-selected="false"
                                style="transition: background-color 0.25s ease; border-left: 5px solid transparent;">
                                <i class="bi bi-briefcase me-3 fs-5 align-middle"></i> Employment Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0 text-dark"
                                data-bs-toggle="tab" href="#documentsSection" role="tab"
                                aria-controls="documentsSection" aria-selected="false"
                                style="border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem; background-color: #f7f7f7; transition: background-color 0.25s ease; border-left: 5px solid transparent;">
                                <i class="bi bi-folder me-3 fs-5 align-middle"></i> Documents
                            </a>

                        </div>

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
                                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bi bi-person-circle me-3 text-info"></i> Employee Overview
                                </h3>
                            </div>

                            <div class="card-body p-4 pt-0">
                                <div class="row g-4">

                                    <div class="col-lg-3 col-md-4 text-center border-end">
                                        <div class="d-flex flex-column align-items-center p-3">
                                            <div class="position-relative mb-3">
                                                <img src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                    class="img-fluid shadow-sm clickable-image"
                                                    style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid var(--bs-info); cursor: pointer;"
                                                    data-src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                                    alt="Employee Avatar">

                                                <span
                                                    class="badge rounded-pill position-absolute bottom-0 end-0 p-2 
                                  {{ $details->is_active ? 'bg-success border border-white' : 'bg-secondary border border-white' }}"
                                                    style="transform: translate(25%, 25%);">
                                                    <i
                                                        class="bi {{ $details->is_active ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </span>
                                            </div>

                                            <h4 class="mb-1 fw-bold">{{ $details->full_name }}</h4>
                                            <p class="text-muted small mb-0">{{ $details->job_title ?: 'N/A' }}</p>


                                        </div>
                                    </div>

                                    <div class="col-lg-9 col-md-8">
                                        <h5 class="fw-bold mb-3 text-info">Employment & Contact</h5>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <dl class="row mb-0 small">
                                                    <dt class="col-sm-4 text-muted">Work Email:</dt>
                                                    <dd class="col-sm-8 text-dark fw-medium mb-2 text-truncate">
                                                        {{ $details->email }}</dd>

                                                    <dt class="col-sm-4 text-muted">Work Phone:</dt>
                                                    <dd class="col-sm-8 text-dark fw-medium mb-2">
                                                        {{ $details->user->phone_no ?? 'N/A' }}</dd>

                                                    <dt class="col-sm-4 text-muted">Role:</dt>
                                                    <dd class="col-sm-8 text-dark fw-medium mb-2">
                                                        {{ ucfirst($details->role) }}</dd>
                                                </dl>
                                            </div>

                                            <div class="col-md-6 border-start">
                                                <dl class="row mb-0 small">
                                                    <dt class="col-sm-5 text-muted">Company:</dt>
                                                    <dd class="col-sm-7 text-dark fw-medium mb-2">
                                                        {{ $details->company->company_name ?? 'N/A' }}</dd>

                                                    <dt class="col-sm-5 text-muted">Department:</dt>
                                                    <dd class="col-sm-7 text-dark fw-medium mb-2">
                                                        {{ $details->department?->name ?? 'N/A' }}</dd>

                                                    <dt class="col-sm-5 text-muted">Team:</dt>
                                                    <dd class="col-sm-7 text-dark fw-medium mb-2">
                                                        {{ $details->team?->name ?? 'N/A' }}</dd>
                                                </dl>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Info -->
                    <div class="tab-pane fade" id="employment" role="tabpanel" aria-labelledby="employment-tab">
                        <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                            <div class="card-header bg-white py-4 border-bottom-0"
                                style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bi bi-briefcase me-3 text-info"></i> Employment Information
                                </h3>
                            </div>

                            <div class="card-body p-4">
                                <div class="row g-4">

                                    <div class="col-md-6">
                                        <h5 class="fw-bold text-secondary mb-3">Contract Structure</h5>

                                        <div class="p-3 bg-light rounded-3 mb-3 border-start border-3 border-info">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1">Salary
                                                Type</small>
                                            <p class="fs-5 fw-bold mb-0 text-dark">
                                                {{ ucfirst($details->salary_type ?? 'N/A') }}
                                            </p>
                                        </div>

                                        <div
                                            class="p-3 bg-light rounded-3 mb-3 border-start border-3 
                        @if (($details->salary_type ?? '') === 'hourly') border-warning @else border-success @endif">
                                            <small
                                                class="text-muted text-uppercase fw-semibold d-block mb-1">Employment
                                                Type</small>
                                            <p
                                                class="fs-5 fw-bold mb-0 
                            @if (($details->salary_type ?? '') === 'hourly') text-warning @else text-success @endif">
                                                @if (($details->salary_type ?? '') === 'hourly')
                                                    Part Time
                                                @else
                                                    Full Time
                                                @endif
                                            </p>
                                        </div>

                                        @if (($details->salary_type ?? '') === 'hourly')
                                            <div class="p-3 bg-light rounded-3">
                                                <small
                                                    class="text-muted text-uppercase fw-semibold d-block mb-1">Contract
                                                    Hours</small>
                                                <p class="fs-5 fw-bold mb-0 text-dark">
                                                    {{ $details->contract_hours ?? 'N/A' }} hours/week
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6 border-start ps-4">
                                        <h5 class="fw-bold text-secondary mb-3">Duration & History</h5>

                                        <div class="p-3 bg-light rounded-3 mb-3">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1">Start
                                                Date</small>
                                            <p class="fs-4 fw-bolder mb-0 text-info">
                                                {{ $details->start_date?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>

                                        <div
                                            class="p-3 rounded-3 
                        @if ($details->end_date ?? null) bg-danger-subtle border border-danger @else bg-success-subtle border border-success @endif">
                                            <small class="text-muted text-uppercase fw-semibold d-block mb-1">End
                                                Date</small>
                                            <p
                                                class="fs-4 fw-bolder mb-0 
                            @if ($details->end_date ?? null) text-danger @else text-success @endif">
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
                                <h3 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="bi bi-folder-fill me-3 text-info"></i> Employee Documents
                                </h3>
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
                                                                            class="badge bg-danger position-absolute top-0 end-0 m-2">EXPIRED</span>
                                                                    @elseif ($isSoon)
                                                                        <span
                                                                            class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Expires
                                                                            Soon</span>
                                                                    @endif
                                                                </div>
                                                            @endforeach

                                                        </div>

                                                        <div class="mt-auto pt-2">
                                                            <button class="btn btn-sm btn-outline-secondary w-100">
                                                                <i class="bi bi-cloud-arrow-up me-1"></i> Upload New
                                                            </button>
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

                        <h3 class="mb-4 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-person-badge me-3 text-info"></i> Personal Information
                        </h3>

                        <div class="row g-4">

                            <div class="col-xl-6">
                                <div class="card border-0 shadow-lg h-100" style="border-radius: 1rem;">
                                    <div class="card-header bg-info text-white fw-bold py-3"
                                        style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                        <i class="bi bi-person me-2"></i> Core Details & Contact
                                    </div>
                                    <div class="card-body p-4">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5 text-muted">Date of Birth:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->date_of_birth
                                                    ? \Carbon\Carbon::parse(optional($details->profile)->date_of_birth)->format('d F, Y')
                                                    : 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Gender:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->gender ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Marital Status:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->marital_status ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Nationality:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->nationality ?? 'N/A' }}
                                            </dd>

                                            <hr class="my-3">

                                            <dt class="col-sm-5 text-muted">Personal Email:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3 text-truncate">
                                                {{ optional($details->profile)->personal_email ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Mobile Phone:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->mobile_phone ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Home Phone:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->home_phone ?? 'N/A' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="card border-0 shadow-lg mb-4" style="border-radius: 1rem;">
                                    <div class="card-header bg-secondary text-white fw-bold py-3"
                                        style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                        <i class="bi bi-geo-alt me-2"></i> Permanent Address
                                    </div>
                                    <div class="card-body p-4">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-4 text-muted">Street 1 / 2:</dt>
                                            <dd class="col-sm-8 fw-medium mb-3">
                                                {{ optional($details->profile)->street_1 ?? 'N/A' }}<br>
                                                <span
                                                    class="small text-secondary">{{ optional($details->profile)->street_2 ?? '' }}</span>
                                            </dd>

                                            <dt class="col-sm-4 text-muted">City, State:</dt>
                                            <dd class="col-sm-8 fw-medium mb-3">
                                                {{ optional($details->profile)->city ?? 'N/A' }},
                                                {{ optional($details->profile)->state ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-4 text-muted">Postcode:</dt>
                                            <dd class="col-sm-8 fw-medium mb-3">
                                                {{ optional($details->profile)->postcode ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-4 text-muted">Country:</dt>
                                            <dd class="col-sm-8 fw-medium mb-3">
                                                {{ optional($details->profile)->country ?? 'N/A' }}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-lg" style="border-radius: 1rem;">
                                    <div class="card-header bg-warning text-dark fw-bold py-3"
                                        style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                        <i class="bi bi-passport me-2"></i> ID & Compliance
                                    </div>
                                    <div class="card-body p-4">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5 text-muted">Tax Ref No:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->tax_reference_number ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Visa/Status:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->immigration_status ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">RTW Expiry:</dt>
                                            <dd
                                                class="col-sm-7 fw-bold mb-3 
                            @if (\Carbon\Carbon::parse(optional($details->profile)->right_to_work_expiry)->isPast()) text-danger @endif">
                                                {{ optional($details->profile)->right_to_work_expiry
                                                    ? \Carbon\Carbon::parse(optional($details->profile)->right_to_work_expiry)->format('d F, Y')
                                                    : 'N/A' }}
                                            </dd>

                                            <hr class="my-3">

                                            <dt class="col-sm-5 text-muted">Passport No:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->passport_number ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">Passport Expiry:</dt>
                                            <dd
                                                class="col-sm-7 fw-bold mb-3 
                            @if (\Carbon\Carbon::parse(optional($details->profile)->passport_expiry)->isPast()) text-danger @endif">
                                                {{ optional($details->profile)->passport_expiry
                                                    ? \Carbon\Carbon::parse(optional($details->profile)->passport_expiry)->format('d F, Y')
                                                    : 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">BRP Number:</dt>
                                            <dd class="col-sm-7 fw-medium mb-3">
                                                {{ optional($details->profile)->brp_number ?? 'N/A' }}
                                            </dd>

                                            <dt class="col-sm-5 text-muted">BRP Expiry:</dt>
                                            <dd
                                                class="col-sm-7 fw-bold mb-3 
                            @if (\Carbon\Carbon::parse(optional($details->profile)->brp_expiry_date)->isPast()) text-danger @endif">
                                                {{ optional($details->profile)->brp_expiry_date
                                                    ? \Carbon\Carbon::parse(optional($details->profile)->brp_expiry_date)->format('d F, Y')
                                                    : 'N/A' }}
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

        <!-- Image Preview Modal -->
        @include('livewire.backend.company.components.document-view')

    </div>


    <script src="{{ asset('js/company/document.js') }}"></script>

</x-layouts.app>
