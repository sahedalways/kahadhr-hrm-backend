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
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a class="list-group-item list-group-item-action active py-3 fw-semibold"
                                data-bs-toggle="tab" href="#overview">
                                <i class="bi bi-person-lines-fill me-2"></i> Employee Overview
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#personalInfo">
                                <i class="bi bi-person-badge me-2"></i> Personal Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#employment">
                                <i class="bi bi-briefcase me-2"></i> Employment Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#documentsSection">
                                <i class="bi bi-folder me-2"></i> Documents
                            </a>


                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="tab-content">

                    <!-- Employee Overview -->
                    <div class="tab-pane fade show active" id="overview">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Employee Overview</h4>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 text-center">
                                        <img src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                            class="img-fluid rounded-3 shadow-sm mb-3 clickable-image"
                                            style="max-height: 160px; object-fit: cover; cursor: pointer; transition:transform 0.25s ease;"
                                            data-src="{{ $details->avatar_url ?? asset('assets/default-user.jpg') }}"
                                            alt="Avatar" onmouseover="this.style.transform='scale(1.1)'"
                                            onmouseout="this.style.transform='scale(1)'">
                                    </div>

                                    <div class="col-md-8">
                                        <h2 class="fw-bold mb-1">{{ $details->full_name }}</h2>
                                        <p class="text-muted mb-2 fs-6">{{ $details->job_title ?: 'N/A' }}</p>
                                        <span
                                            class="badge px-3 py-2 fs-6 rounded-pill 
                                              {{ $details->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $details->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <hr>
                                        <p class="mb-1"><strong>Work Email:</strong> {{ $details->email }}</p>
                                        <p class="mb-1"><strong>Company:</strong>
                                            {{ $details->company->company_name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Work Phone No:</strong>
                                            {{ $details->user->phone_no ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Department:</strong>
                                            {{ $details->department?->name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>Team:</strong> {{ $details->team?->name ?? 'N/A' }}
                                        </p>
                                        <p class="mb-1"><strong>Role:</strong> {{ ucfirst($details->role) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment Info -->
                    <div class="tab-pane fade" id="employment">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Employment Information</h4>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Salary Type:</strong>
                                    {{ ucfirst($details->salary_type ?? 'N/A') }}</p>

                                @if (($details->salary_type ?? '') === 'hourly')
                                    <p class="mb-1"><strong>Contract Hours:</strong>
                                        {{ $details->contract_hours ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Employment Type:</strong> Part Time</p>
                                @else
                                    <p class="mb-1"><strong>Employment Type:</strong> Full Time</p>
                                @endif

                                <p class="mb-1"><strong>Start Date:</strong>
                                    {{ $details->start_date?->format('d M Y') ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>End Date:</strong>
                                    {{ $details->end_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>

                        </div>
                    </div>


                    <!-- Documents -->
                    <div class="tab-pane fade" id="documentsSection">
                        <div class="row g-4">

                            @php
                                $hasDocs = $details->documents->isNotEmpty();
                            @endphp

                            @if (!$hasDocs)
                                <div class="col-12">
                                    <div class="alert alert-info text-center text-white">
                                        No documents found for this employee.
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

                                    <div class="col-md-3">
                                        <div class="card shadow-sm border-0 rounded-3 h-100">

                                            <div
                                                class="card-header bg-light text-primary fw-semibold d-flex align-items-center">
                                                <i class="fas fa-folder me-2"></i> {{ $type->name }}
                                            </div>

                                            <div class="card-body d-flex flex-column">

                                                <div class="mb-3 flex-grow-1">

                                                    @foreach ($docsForType as $doc)
                                                        <div class="shadow-sm rounded p-3 mb-2 border position-relative"
                                                            data-doc-id="{{ $doc->id }}"
                                                            onclick="openDocumentModal({{ $type->id }}, {{ $doc->id }}, '{{ $doc->document_url }}', '{{ $doc->expires_at }}', '{{ $doc->comment }}')"
                                                            style="cursor:pointer; background-color:#f9f9f9; transition: .3s;"
                                                            data-bs-toggle="modal" data-bs-target="#openDocumentModal"
                                                            onmouseover="this.style.backgroundColor='#e6f0ff';"
                                                            onmouseout="this.style.backgroundColor='#f9f9f9';">

                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-file-pdf text-primary me-2"
                                                                    style="font-size:28px;"></i>

                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold text-truncate"
                                                                        style="max-width: 200px;">
                                                                        {{ $doc->name ?? 'Document' }}
                                                                    </div>

                                                                    <div class="small text-muted mt-1">
                                                                        Expiry:
                                                                        <span
                                                                            class="{{ $doc->expires_at && \Carbon\Carbon::parse($doc->expires_at)->isPast() ? 'text-danger' : '' }}">
                                                                            {{ $doc->expires_at ? \Carbon\Carbon::parse($doc->expires_at)->format('d M, Y') : 'No Expiry' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if ($doc->expires_at && \Carbon\Carbon::parse($doc->expires_at)->isPast())
                                                                <span
                                                                    class="badge bg-danger position-absolute top-0 end-0 m-2">Expired</span>
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


                    <!-- Personal Info -->
                    <div class="tab-pane fade" id="personalInfo">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Personal Information</h4>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Date of Birth:</strong>
                                            {{ $details->profile->date_of_birth
                                                ? \Carbon\Carbon::parse($details->profile->date_of_birth)->format('d F, Y')
                                                : 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Gender:</strong>
                                            {{ $details->profile->gender ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Marital Status:</strong>
                                            {{ $details->profile->marital_status ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Nationality:</strong>
                                            {{ $details->profile->nationality ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Street 1:</strong>
                                            {{ $details->profile->street_1 ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Street 2:</strong>
                                            {{ $details->profile->street_2 ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>City:</strong>
                                            {{ $details->profile->city ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>State:</strong>
                                            {{ $details->profile->state ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Postcode:</strong>
                                            {{ $details->profile->postcode ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Country:</strong>
                                            {{ $details->profile->country ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Home Phone:</strong>
                                            {{ $details->profile->home_phone ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Mobile Phone:</strong>
                                            {{ $details->profile->mobile_phone ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Personal Email:</strong>
                                            {{ $details->profile->personal_email ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tax Reference No:</strong>
                                            {{ $details->profile->tax_reference_number ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Immigration Status / Visa Type:</strong>
                                            {{ $details->profile->immigration_status ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>BRP Number:</strong>
                                            {{ $details->profile->brp_number ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>BRP Expiry Date:</strong>
                                            {{ $details->profile->brp_expiry_date
                                                ? \Carbon\Carbon::parse($details->profile->brp_expiry_date)->format('d F, Y')
                                                : 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Right to Work Expiry:</strong>
                                            {{ $details->profile->right_to_work_expiry
                                                ? \Carbon\Carbon::parse($details->profile->right_to_work_expiry)->format('d F, Y')
                                                : 'N/A' }}
                                        </p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Passport Number:</strong>
                                            {{ $details->profile->passport_number ?? 'N/A' }}</p>
                                    </div>

                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Passport Expiry:</strong>
                                            {{ $details->profile->passport_expiry
                                                ? \Carbon\Carbon::parse($details->profile->passport_expiry)->format('d F, Y')
                                                : 'N/A' }}
                                        </p>
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
