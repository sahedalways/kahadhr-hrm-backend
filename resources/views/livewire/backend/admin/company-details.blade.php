@push('styles')
    <link href="{{ asset('assets/css/company/company-profile.css') }}"
          rel="stylesheet" />
@endpush


<div>

    <div class="container-fluid py-4">
        <div class="row g-4">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <!-- Back Button -->


                <!-- Company Info with Avatar -->
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-circle"
                         style="width: 60px; height: 60px; background: linear-gradient(135deg, #0dcaf0, #0b9ed0); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(13, 202, 240, 0.3);">
                        <span style="color: white; font-weight: 600; font-size: 1.4rem;">
                            {{ strtoupper(substr($details->company_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0"
                            style="font-size: 1.5rem; color: #1a2c3e; letter-spacing: -0.3px;">
                            {{ $details->company_name }}
                        </h2>
                        <div class="d-flex gap-2 mt-1 flex-wrap">

                            @php
                                $statusColors = [
                                    'active' => [
                                        'bg' => '#d1fae5',
                                        'color' => '#065f46',
                                        'dot' => '#10b981',
                                        'icon' => 'fa-credit-card',
                                    ],
                                    'trial' => [
                                        'bg' => '#dbeafe',
                                        'color' => '#1e40af',
                                        'dot' => '#3b82f6',
                                        'icon' => 'fa-credit-card',
                                    ],
                                    'expired' => [
                                        'bg' => '#fee2e2',
                                        'color' => '#991b1b',
                                        'dot' => '#dc2626',
                                        'icon' => 'fa-credit-card',
                                    ],
                                    'suspended' => [
                                        'bg' => '#fef3c7',
                                        'color' => '#92400e',
                                        'dot' => '#f59e0b',
                                        'icon' => 'fa-credit-card',
                                    ],
                                    'inactive' => [
                                        'bg' => '#f3f4f6',
                                        'color' => '#374151',
                                        'dot' => '#6b7280',
                                        'icon' => 'fa-credit-card',
                                    ],
                                ];
                                $statusKey = strtolower($details->subscription_status ?? 'inactive');
                                $statusStyle = $statusColors[$statusKey] ?? $statusColors['inactive'];
                            @endphp
                            <span class="badge"
                                  style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['color'] }}; font-weight: 500; padding: 4px 10px; border-radius: 20px;">
                                <i class="fas {{ $statusStyle['icon'] }} me-1"
                                   style="font-size: 0.7rem;"></i>
                                {{ ucfirst($details->subscription_status ?? 'Inactive') }}
                            </span>
                            @if (isset($details->subscription_end) && $details->subscription_end)
                                <span class="badge"
                                      style="background: #e9ecef; color: #495057; font-weight: 500; padding: 4px 10px; border-radius: 20px;">
                                    <i class="fas fa-calendar-alt me-1"
                                       style="font-size: 0.7rem;"></i>
                                    Expires: {{ $details->subscription_end->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <a class="btn btn-outline-primary d-inline-flex align-items-center rounded-pill px-3 py-2"
                       style="background-color: #f8f9fa; border: 1px solid #0dcaf0; color: #0dcaf0; transition: all 0.3s ease;"
                       href="{{ route('super-admin.companies') }}"
                       onmouseover="this.style.backgroundColor='#0dcaf0'; this.style.color='white';"
                       onmouseout="this.style.backgroundColor='#f8f9fa'; this.style.color='#0dcaf0';">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Back to Companies
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-lg"
                     style="border-radius: 1rem;">
                    <div class="card-body p-0">

                        <div class="list-group list-group-flush"
                             role="tablist">

                            <div class="list-group-item list-group-item-light text-uppercase fw-bold small py-3"
                                 style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
                                Company Navigation
                            </div>

                            <!-- Sidebar Tabs -->
                            <a class="list-group-item list-group-item-action"
                               href="#overview"
                               role="tab"
                               wire:click.prevent="$set('activeTab', 'overview')"
                               style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
          {{ $activeTab === 'overview' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                                <i class="bi bi-person-lines-fill"
                                   style="font-size:1.1rem; {{ $activeTab === 'overview' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                                Company Overview
                            </a>

                            <a class="list-group-item list-group-item-action"
                               href="#companyInfo"
                               role="tab"
                               wire:click.prevent="$set('activeTab', 'companyInfo')"
                               style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
          {{ $activeTab === 'companyInfo' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                                <i class="bi bi-receipt"
                                   style="font-size:1.1rem; {{ $activeTab === 'companyInfo' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                                Company Information
                            </a>

                            <a class="list-group-item list-group-item-action"
                               href="#billing"
                               role="tab"
                               wire:click.prevent="$set('activeTab', 'billing')"
                               style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
          {{ $activeTab === 'billing' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                                <i class="bi bi-card-list"
                                   style="font-size:1.1rem; {{ $activeTab === 'billing' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                                Billing / Subscription
                            </a>

                            <a class="list-group-item list-group-item-action"
                               href="#emp"
                               role="tab"
                               wire:click.prevent="$set('activeTab', 'emp')"
                               style="border:none; padding:14px 20px; display:flex; align-items:center; gap:12px;
          {{ $activeTab === 'emp' ? 'background:#e9f7fc; color:#0dcaf0; font-weight:600; border-left:4px solid #0dcaf0;' : 'font-weight:500; color:#444;' }}">
                                <i class="bi bi-people"
                                   style="font-size:1.1rem; {{ $activeTab === 'emp' ? 'color:#0dcaf0;' : 'color:#6c757d;' }}"></i>
                                Employees
                            </a>



                        </div>

                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="tab-content">

                    <!-- ================================
                         COMPANY OVERVIEW
                    ===================================== -->

                    <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }}"
                         id="overview"
                         role="tabpanel">

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <!-- Header Section with Custom Cyan Color -->
                            <div class="card-header text-white py-4 px-4 border-0"
                                 style="background-color: #3d75a8;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-building fa-lg text-dark"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-white">Company Overview</h4>
                                            <p class="mb-0 text-white"
                                               style="opacity: 0.9;">Company details and contact information</p>
                                        </div>
                                    </div>

                                    <!-- Actions Dropdown -->
                                    <div class="dropdown"
                                         wire:ignore.self>
                                        <button class="btn btn-light btn-sm rounded-pill px-3"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-cog me-1"></i> Actions
                                            <i class="fas fa-chevron-down ms-1 fs-10"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">

                                            <li>
                                                <a class="dropdown-item py-2"
                                                   href="#"
                                                   wire:click.prevent="toggleStatus({{ $details->id }})">
                                                    @if ($details->status == 'Active')
                                                        <i class="fas fa-user-minus me-2 text-warning"></i> Change to
                                                        Inactive
                                                    @else
                                                        <i class="fas fa-user-check me-2 text-success"></i> Change to
                                                        Active
                                                    @endif
                                                    <span wire:loading
                                                          wire:target="toggleStatus"
                                                          class="spinner-border spinner-border-sm ms-2"></span>
                                                </a>
                                            </li>

                                            @if ($details->subscription_status == 'trial')
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                       data-bs-toggle="modal"
                                                       data-bs-target="#forceActiveModal">
                                                        <i class="fas fa-bolt me-2 text-primary"></i> Force to Active
                                                    </a>
                                                </li>
                                            @endif


                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 text-danger"
                                                   href="#"
                                                   wire:click.prevent="$dispatch('confirmDelete', {{ $details->id }})">
                                                    <i class="fas fa-trash-alt me-2"></i> Delete Company
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Body Section -->
                            <div class="card-body p-0">
                                <!-- Company Header Section -->
                                <div class="p-4 border-bottom bg-light">
                                    <div class="row align-items-center g-4">
                                        <!-- Logo -->
                                        <div class="col-md-3 col-lg-2 text-center">
                                            <div class="bg-white rounded-3 p-3 shadow-sm d-inline-block w-100">
                                                <img src="{{ $details->company_logo_url ?? asset('assets/img/default-avatar.png') }}"
                                                     class="img-fluid rounded-2 clickable-image"
                                                     style="max-height: 100px; width: auto; cursor: pointer;"
                                                     data-src="{{ $details->company_logo_url ?? asset('assets/img/default-avatar.png') }}"
                                                     alt="Company Logo">
                                            </div>
                                        </div>

                                        <!-- Company Info -->
                                        <div class="col-md-9 col-lg-10">
                                            <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                                                <h4 class=" mb-0 fw-bold text-dark">{{ $details->company_name }}
                                                    </h5>
                                                    @php
                                                        $statusConfig = [
                                                            'Active' => [
                                                                'class' => 'bg-success',
                                                                'icon' => 'fa-check-circle',
                                                            ],
                                                            'Inactive' => [
                                                                'class' => 'bg-secondary',
                                                                'icon' => 'fa-circle',
                                                            ],
                                                        ];
                                                        $status = $statusConfig[$details->status] ?? [
                                                            'class' => 'bg-secondary',
                                                            'icon' => 'fa-circle',
                                                        ];
                                                    @endphp
                                                    <span class="badge {{ $status['class'] }} px-3 py-2 rounded-pill">
                                                        <i class="fas {{ $status['icon'] }} me-1 fs-10"></i>
                                                        {{ $details->status }}
                                                    </span>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center gap-2 text-muted">
                                                        <i class="fas fa-tag"
                                                           style="color: #0dcaf0;"></i>
                                                        <span class="fw-semibold">Business Type:</span>
                                                        <span>{{ $details->business_type ?: 'Not specified' }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center gap-2 text-muted">
                                                        <i class="fas fa-calendar-alt"
                                                           style="color: #0dcaf0;"></i>
                                                        <span class="fw-semibold">Joined:</span>
                                                        <span>{{ $details->created_at ? $details->created_at->format('d M, Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Cards -->
                                <div class="p-4">
                                    <div class="row g-4">
                                        <!-- Contact Details Card -->
                                        <div class="col-lg-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div
                                                         class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                             style="width: 50px; height: 50px; background-color: rgba(13, 202, 240, 0.1);">

                                                            <i class="fas fa-address-card"
                                                               style="color: #0dcaf0;"></i>
                                                        </div>
                                                        <h5 class="mb-0 fw-semibold">Contact Details</h5>
                                                    </div>

                                                    <div class="vstack gap-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                                 style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">

                                                                <i class="fas fa-envelope"
                                                                   style="color: #0dcaf0;"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small mb-1">Email Address</div>
                                                                <div class="fw-medium">
                                                                    {{ $details->company_email ?: 'Not provided' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                                 style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">

                                                                <i class="fas fa-phone-alt"
                                                                   style="color: #0dcaf0;"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small mb-1">Phone Number</div>
                                                                <div class="fw-medium">
                                                                    {{ $details->company_mobile ?: 'Not provided' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($details->company_website ?? false)
                                                            <div class="d-flex align-items-start gap-3">
                                                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                                     style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">

                                                                    <i class="fas fa-globe"
                                                                       style="color: #0dcaf0;"></i>
                                                                </div>
                                                                <div>
                                                                    <div class="text-muted small mb-1">Website</div>
                                                                    <div class="fw-medium">
                                                                        <a href="{{ $details->company_website }}"
                                                                           target="_blank"
                                                                           class="text-decoration-none"
                                                                           style="color: #0dcaf0;">
                                                                            {{ $details->company_website }}
                                                                            <i
                                                                               class="fas fa-external-link-alt ms-1 fs-10"></i>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address Card -->
                                        <div class="col-lg-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body">
                                                    <div
                                                         class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                             style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">
                                                            <i class="fas fa-map-marker-alt"
                                                               style="color: #0dcaf0;"></i>
                                                        </div>
                                                        <h5 class="mb-0 fw-semibold">Address Information</h5>
                                                    </div>

                                                    <div class="vstack gap-3">
                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                                 style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">
                                                                <i class="fas fa-home"
                                                                   style="color: #0dcaf0;"></i>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted small mb-1">House/Office No
                                                                </div>
                                                                <div class="fw-medium">
                                                                    {{ $details->company_house_number ?: 'Not provided' }}
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex align-items-start gap-3">
                                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                                 style="width: 36px; height: 36px; background-color: rgba(13, 202, 240, 0.1);">
                                                                <i class="fas fa-location-dot"
                                                                   style="color: #0dcaf0;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="text-muted small mb-1">Full Address</div>
                                                                <div class="fw-medium">
                                                                    {{ $details->street ? $details->street . ', ' : '' }}
                                                                    {{ $details->city ? $details->city . ', ' : '' }}
                                                                    {{ $details->state ? $details->state . ', ' : '' }}
                                                                    {{ $details->postcode ? $details->postcode . ', ' : '' }}
                                                                    <span
                                                                          class="fw-bold">{{ $details->country ?? 'Not specified' }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional Info if needed -->
                                    @if (($details->tax_number ?? false) || ($details->registration_number ?? false))
                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body">
                                                        <div
                                                             class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                                            <div class="rounded-circle p-2"
                                                                 style="background-color: rgba(13, 202, 240, 0.1);">
                                                                <i class="fas fa-info-circle"
                                                                   style="color: #0dcaf0;"></i>
                                                            </div>
                                                            <h5 class="mb-0 fw-semibold">Additional Information</h5>
                                                        </div>
                                                        <div class="row g-3">
                                                            @if ($details->registration_number)
                                                                <div class="col-md-6">
                                                                    <div class="text-muted small mb-1">Registration
                                                                        Number</div>
                                                                    <div class="fw-medium">
                                                                        {{ $details->registration_number }}</div>
                                                                </div>
                                                            @endif
                                                            @if ($details->tax_number)
                                                                <div class="col-md-6">
                                                                    <div class="text-muted small mb-1">Tax Number</div>
                                                                    <div class="fw-medium">{{ $details->tax_number }}
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- ================================
                         BILLING / SUBSCRIPTION
                    ===================================== -->
                    <div class="tab-pane fade {{ $activeTab === 'billing' ? 'show active' : '' }}"
                         id="billing"
                         role="tabpanel">

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <!-- Header Section -->
                            <div class="card-header text-white py-4 px-4 border-0"
                                 style="background-color: #3d75a8;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-credit-card fa-lg text-dark"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fw-bold text-white">Billing & Subscription</h4>
                                        <p class="mb-0 text-white"
                                           style="opacity: 0.9;">Manage payment methods and subscription details</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <!-- Payment Method Section -->
                                <div class="mb-4">
                                    <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-1"
                                             style="background-color: rgba(13, 202, 240, 0.1);">
                                            <i class="fas fa-credit-card"
                                               style="color: #0dcaf0; font-size: 14px;"></i>
                                        </div>
                                        Payment Method
                                    </h5>

                                    @if ($stripeCard)
                                        @php
                                            $brandColors = [
                                                'visa' => 'linear-gradient(135deg, #1a1f71, #3a6ad8)',
                                                'mastercard' => 'linear-gradient(135deg, #ff5f00, #eb001b)',
                                                'amex' => 'linear-gradient(135deg, #2e77bb, #4fa3dd)',
                                                'discover' => 'linear-gradient(135deg, #f76b1c, #ffcc00)',
                                                'default' => 'linear-gradient(135deg, #2c3e50, #34495e)',
                                            ];
                                            $cardBg =
                                                $brandColors[strtolower($stripeCard['brand'] ?? 'default')] ??
                                                $brandColors['default'];
                                        @endphp

                                        <div class="card text-white p-3"
                                             style="background: {{ $cardBg }}; border-radius: 16px; max-width: 320px;">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="fw-bold text-uppercase">{{ $stripeCard['brand'] }}</span>
                                                <i class="fas fa-credit-card fa-2x"></i>
                                            </div>
                                            <div class="mb-3"
                                                 style="font-family: 'Courier New', monospace; font-size: 1.1rem; letter-spacing: 2px;">
                                                •••• •••• •••• <strong>{{ $stripeCard['last4'] }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <small class="text-light opacity-75 d-block">Card Holder</small>
                                                    <strong
                                                            class="text-uppercase">{{ $stripeCard['holder_name'] }}</strong>
                                                </div>
                                                <div class="text-end">
                                                    <small class="text-light opacity-75 d-block">Valid Thru</small>
                                                    <strong>{{ $stripeCard['exp_month'] }}/{{ $stripeCard['exp_year'] }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="border border-dashed rounded-4 text-center p-4"
                                             style="max-width: 320px; border-color: #dee2e6;">
                                            <i class="fas fa-credit-card fa-3x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No card saved yet</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Subscription Info Cards -->
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-globe"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Registered
                                                    Domain</small>
                                            </div>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ $details->registered_domain ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-calendar"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Calendar
                                                    Year</small>
                                            </div>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ ucfirst($details->calendarYearSetting->calendar_year ?? 'N/A') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: {{ isset($details->subscription_status) && $details->subscription_status === 'active' ? 'linear-gradient(135deg, #e8f5e9, #ffffff)' : 'linear-gradient(135deg, #fff3e0, #ffffff)' }};">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-chart-line"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Subscription
                                                    Status</small>
                                            </div>
                                            @php
                                                $statusColors = [
                                                    'active' => ['bg' => '#28a745', 'icon' => 'fa-check-circle'],
                                                    'trial' => ['bg' => '#17a2b8', 'icon' => 'fa-hourglass-half'],
                                                    'expired' => ['bg' => '#dc3545', 'icon' => 'fa-times-circle'],
                                                    'suspended' => ['bg' => '#ffc107', 'icon' => 'fa-ban'],
                                                ];
                                                $currentStatus = $details->subscription_status ?? 'expired';
                                                $statusColor = $statusColors[$currentStatus] ?? [
                                                    'bg' => '#6c757d',
                                                    'icon' => 'fa-question-circle',
                                                ];
                                            @endphp
                                            <span class="badge px-3 py-2 rounded-pill text-white"
                                                  style="background-color: {{ $statusColor['bg'] }};">
                                                <i class="fas {{ $statusColor['icon'] }} me-1"></i>
                                                {{ ucfirst($currentStatus) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-play-circle"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Subscription
                                                    Start</small>
                                            </div>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ $details->subscription_start?->format('d M Y') ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-stop-circle"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Subscription
                                                    End</small>
                                            </div>
                                            <p
                                               class="fs-6 fw-bold mb-0 {{ isset($details->subscription_end) && $details->subscription_end->isPast() ? 'text-danger' : 'text-dark' }}">
                                                {{ $details->subscription_end?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-lg-4">
                                        <div class="p-3 rounded-3 border"
                                             style="background: linear-gradient(135deg, #f8f9fa, #ffffff);">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="fas fa-pound-sign"
                                                   style="color: #0dcaf0;"></i>
                                                <small class="text-muted text-uppercase fw-semibold">Payment
                                                    Status</small>
                                            </div>
                                            @php
                                                // Payment status colors for enum: paid, unpaid, pending, failed
                                                $paymentStatusColors = [
                                                    'paid' => [
                                                        'bg' => '#d1fae5',
                                                        'color' => '#065f46',
                                                        'icon' => 'fa-check-circle',
                                                        'label' => 'Paid',
                                                        'border' => '#10b981',
                                                    ],
                                                    'unpaid' => [
                                                        'bg' => '#fee2e2',
                                                        'color' => '#991b1b',
                                                        'icon' => 'fa-exclamation-circle',
                                                        'label' => 'Unpaid',
                                                        'border' => '#dc2626',
                                                    ],
                                                    'pending' => [
                                                        'bg' => '#fef3c7',
                                                        'color' => '#92400e',
                                                        'icon' => 'fa-clock',
                                                        'label' => 'Pending',
                                                        'border' => '#f59e0b',
                                                    ],
                                                    'failed' => [
                                                        'bg' => '#f3f4f6',
                                                        'color' => '#374151',
                                                        'icon' => 'fa-times-circle',
                                                        'label' => 'Failed',
                                                        'border' => '#6b7280',
                                                    ],
                                                ];

                                                // Get payment status from company model
                                                $paymentStatus = strtolower($details->payment_status ?? 'pending');
                                                $paymentStyle =
                                                    $paymentStatusColors[$paymentStatus] ??
                                                    $paymentStatusColors['pending'];
                                            @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1"
                                                      style="background: {{ $paymentStyle['bg'] }}; color: {{ $paymentStyle['color'] }}; font-weight: 500; border: 1px solid {{ $paymentStyle['border'] }};">
                                                    <i class="fas {{ $paymentStyle['icon'] }} me-1"></i>
                                                    {{ $paymentStyle['label'] }}
                                                </span>

                                                @if ($paymentStatus == 'failed')
                                                    <i class="fas fa-exclamation-triangle"
                                                       style="color: #dc3545; font-size: 14px;"
                                                       title="Payment failed. Please update payment method."></i>
                                                @endif

                                                @if ($paymentStatus == 'unpaid')
                                                    <i class="fas fa-bell"
                                                       style="color: #f59e0b; font-size: 14px;"
                                                       title="Payment is due"></i>
                                                @endif
                                            </div>

                                            @if ($paymentStatus == 'unpaid' && isset($details->payment_due_date))
                                                <small class="text-muted d-block mt-2">
                                                    <i class="fas fa-calendar-alt me-1 fs-10"></i>
                                                    Due Date:
                                                    {{ \Carbon\Carbon::parse($details->payment_due_date)->format('d M Y') }}
                                                </small>
                                            @endif

                                            @if ($paymentStatus == 'failed')
                                                <small class="text-danger d-block mt-2">
                                                    <i class="fas fa-exclamation-circle me-1 fs-10"></i>
                                                    Please update your payment method to continue service
                                                </small>
                                            @endif
                                        </div>

                                    </div>
                                </div>

                                <!-- Invoices Section -->
                                <div class="mt-4">
                                    <h5 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                                        <div class="rounded-circle p-1"
                                             style="background-color: rgba(13, 202, 240, 0.1);">
                                            <i class="fas fa-file-invoice"
                                               style="color: #0dcaf0; font-size: 14px;"></i>
                                        </div>
                                        Invoice History
                                    </h5>

                                    <!-- Filter Form -->
                                    <form id="invoiceFilterForm"
                                          method="GET"
                                          class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <div class="input-group">
                                                <span class="input-group-text bg-light border-0"><i
                                                       class="fas fa-search"
                                                       style="color: #0dcaf0;"></i></span>
                                                <input type="text"
                                                       name="search"
                                                       value="{{ request('search') }}"
                                                       class="form-control border-0 bg-light"
                                                       placeholder="Search by Invoice #">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date"
                                                   name="start_date"
                                                   value="{{ request('start_date') }}"
                                                   class="form-control bg-light border-0"
                                                   placeholder="Start Date">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date"
                                                   name="end_date"
                                                   value="{{ request('end_date') }}"
                                                   class="form-control bg-light border-0"
                                                   placeholder="End Date">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit"
                                                    class="btn text-white w-100 rounded-pill"
                                                    style="background-color: #0dcaf0;">
                                                <i class="fas fa-filter me-1"></i> Filter
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Invoices Table -->
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead style="background-color: #f8f9fa;">
                                                <tr>
                                                    <th class="py-3">#</th>
                                                    <th class="py-3">Invoice #</th>
                                                    <th class="py-3">Billing Period</th>
                                                    <th class="py-3">Subtotal</th>
                                                    <th class="py-3">VAT</th>
                                                    <th class="py-3">Total</th>
                                                    <th class="py-3">Status</th>
                                                    <th class="py-3">Invoice Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($invoices as $invoice)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="fw-semibold">#{{ $invoice->invoice_number }}</td>
                                                        <td>{{ $invoice->billing_period_start->format('d M, Y') }} -
                                                            {{ $invoice->billing_period_end->format('d M, Y') }}</td>
                                                        <td>£{{ number_format($invoice->subtotal, 2) }}</td>
                                                        <td>£{{ number_format($invoice->vat, 2) }}</td>
                                                        <td class="fw-bold">£{{ number_format($invoice->total, 2) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge rounded-pill px-3 py-1 bg-success">
                                                                <i class="fas fa-check-circle me-1 fs-10"></i>
                                                                {{ ucfirst($invoice->status) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8"
                                                            class="text-center py-4 text-muted">
                                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                            No invoices found
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade {{ $activeTab === 'emp' ? 'show active' : '' }}"
                         id="emp"
                         role="tabpanel">

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <!-- Header Section -->
                            <div class="card-header text-white py-4 px-4 border-0"
                                 style="background-color: #3d75a8;">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-users fa-lg text-dark"></i>
                                        </div>
                                        <div>
                                            <h4 class="mb-0 fw-bold text-white">Employees</h4>
                                            <p class="mb-0 text-white"
                                               style="opacity: 0.9;">Manage and view all company employees</p>
                                        </div>
                                    </div>

                                    <!-- Employee Stats -->
                                    <div class="d-flex gap-2">
                                        <div class="bg-white bg-opacity-20 rounded-circle d-flex flex-column align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; text-align: center;">

                                            <small class="d-block text-dark"
                                                   style="font-size: 11px;">Total</small>
                                            <strong class="text-dark">{{ $details->employees->count() }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-0">
                                @if ($details->employees->count())
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead style="background-color: #f8f9fa;">
                                                <tr>
                                                    <th class="py-3 px-4">#</th>
                                                    <th class="py-3">Name</th>
                                                    <th class="py-3">Email</th>
                                                    <th class="py-3">Register Date</th>
                                                    <th class="py-3 px-4">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($details->employees as $emp)
                                                    <tr>
                                                        <td class="px-4">{{ $loop->iteration }}</td>
                                                        <td>
                                                            <a href="{{ route('super-admin.dashboard.employees.details', $emp->id) }}"
                                                               class="text-decoration-none fw-semibold"
                                                               style="color: #0dcaf0;">
                                                                <i class="fas fa-user-circle me-2"></i>
                                                                {{ $emp->full_name ?? 'N/A' }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $emp->email }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($emp->created_at)->format('d M, Y') }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                  class="badge rounded-pill px-3 py-2 {{ $emp->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                                <i
                                                                   class="fas {{ $emp->is_active ? 'fa-check-circle' : 'fa-circle' }} me-1 fs-10"></i>
                                                                {{ $emp->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Stats Cards -->
                                    <div class="p-4 border-top"
                                         style="background-color: #f8f9fa;">
                                        <div class="row g-3 justify-content-center">
                                            <div class="col-auto">
                                                <div class="rounded-pill px-4 py-2 shadow-sm"
                                                     style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                                                    <i class="fas fa-user-check me-2"
                                                       style="color: #2e7d32;"></i>
                                                    <span class="fw-semibold"
                                                          style="color: #1b5e20;">Active: {{ $activeCount }}</span>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <div class="rounded-pill px-4 py-2 shadow-sm"
                                                     style="background: linear-gradient(135deg, #ffebee, #ffcdd2);">
                                                    <i class="fas fa-user-slash me-2"
                                                       style="color: #c62828;"></i>
                                                    <span class="fw-semibold"
                                                          style="color: #c62828;">Inactive: {{ $formerCount }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No employees found</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeTab === 'companyInfo' ? 'show active' : '' }}"
                         id="companyInfo"
                         role="tabpanel">

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <!-- Header Section -->
                            <div class="card-header text-white py-4 px-4 border-0"
                                 style="background-color: #3d75a8;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-building fa-lg text-dark"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 fw-bold text-white">Company Information</h4>
                                        <p class="mb-0 text-white"
                                           style="opacity: 0.9;">Detailed company profile and contact information</p>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Logo Section -->
                                    <div class="col-md-3 text-center">
                                        <div class="bg-light rounded-4 p-4 text-center">
                                            <img src="{{ $details->company_logo ? asset('storage/' . $details->company_logo) : asset('assets/img/default-avatar.png') }}"
                                                 class="img-fluid rounded-3 mb-3"
                                                 style="max-height: 120px; width: auto;">
                                            <div>
                                                <span class="badge px-3 py-2 rounded-pill"
                                                      style="background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0;">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $details->business_type ?? 'Business' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Company Details -->
                                    <div class="col-md-9">
                                        <div class="row g-4">
                                            <!-- Company Name -->
                                            <div class="col-12 pb-3 border-bottom">
                                                <label class="text-muted text-uppercase fw-semibold mb-1 d-block"
                                                       style="font-size: 11px; letter-spacing: 1px;">
                                                    <i class="fas fa-building me-1"
                                                       style="color: #0dcaf0;"></i> Company Name
                                                </label>
                                                <h5 class="mb-0 fw-bold"
                                                    style="color: #1e293b;">{{ $details->company_name ?? 'N/A' }}</h3>
                                            </div>

                                            <!-- Contact Info Grid -->
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="rounded-circle p-2"
                                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                                        <i class="fas fa-home"
                                                           style="color: #0dcaf0;"></i>
                                                    </div>
                                                    <div>
                                                        <label class="text-muted small d-block">House Number</label>
                                                        <span
                                                              class="fw-medium">{{ $details->company_house_number ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="rounded-circle p-2"
                                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                                        <i class="fas fa-globe"
                                                           style="color: #0dcaf0;"></i>
                                                    </div>
                                                    <div>
                                                        <label class="text-muted small d-block">Website</label>
                                                        @if ($details->registered_domain)
                                                            <a href="https://{{ $details->registered_domain }}"
                                                               target="_blank"
                                                               class="text-decoration-none"
                                                               style="color: #0dcaf0;">
                                                                {{ $details->registered_domain }}
                                                                <i class="fas fa-external-link-alt ms-1 fs-10"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="rounded-circle p-2"
                                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                                        <i class="fas fa-phone-alt"
                                                           style="color: #0dcaf0;"></i>
                                                    </div>
                                                    <div>
                                                        <label class="text-muted small d-block">Phone Number</label>
                                                        <span
                                                              class="fw-medium">{{ $details->company_mobile ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="d-flex align-items-start gap-3">
                                                    <div class="rounded-circle p-2"
                                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                                        <i class="fas fa-envelope"
                                                           style="color: #0dcaf0;"></i>
                                                    </div>
                                                    <div>
                                                        <label class="text-muted small d-block">Email Address</label>
                                                        <span
                                                              class="fw-medium">{{ $details->company_email ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Address Section -->
                                            <div class="col-12 mt-2">
                                                <div class="p-3 rounded-3"
                                                     style="background-color: rgba(13, 202, 240, 0.05); border: 1px solid rgba(13, 202, 240, 0.1);">
                                                    <div class="d-flex align-items-start gap-3">
                                                        <div class="rounded-circle p-2"
                                                             style="background-color: rgba(13, 202, 240, 0.1);">
                                                            <i class="fas fa-map-marker-alt"
                                                               style="color: #0dcaf0;"></i>
                                                        </div>
                                                        <div>
                                                            <label class="text-muted small d-block mb-1">Registered
                                                                Office Address</label>
                                                            <div class="fw-medium"
                                                                 style="color: #334155; line-height: 1.6;">
                                                                {{ $details->street ? $details->street . ', ' : '' }}
                                                                {{ $details->city ? $details->city . ', ' : '' }}
                                                                {{ $details->state ? $details->state . ', ' : '' }}
                                                                {{ $details->postcode ? $details->postcode . ', ' : '' }}
                                                                <span
                                                                      class="fw-bold">{{ $details->country ?? 'N/A' }}</span>
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


                </div>




            </div>
        </div>
    </div>

    <div wire:ignore.self
         class="modal fade"
         id="manageCompanyProfile"
         tabindex="-1"
         role="dialog"
         aria-labelledby="manageCompanyProfile"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-lg"
             role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Manage Company Info</h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">

                            {{-- Company Name --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control shadow-sm"
                                       wire:model="company_name"
                                       required>
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- House Number --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Company House Number <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control shadow-sm"
                                       wire:model="company_house_number">
                                @error('company_house_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>





                            <div class="col-md-6 d-flex align-items-end mb-2">
                                <div class="flex-grow-1">
                                    <label class="form-label"> Company Mobile<span
                                              class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control shadow-sm"
                                           wire:model="company_mobile"
                                           readonly
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('company_mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button"
                                        class="btn btn-primary ms-2 mb-0"
                                        wire:click="openModal('mobile')"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal">
                                    Change
                                </button>
                            </div>

                            <div class="col-md-6 d-flex align-items-end mb-2">
                                <div class="flex-grow-1">
                                    <label class="form-label">Company Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           class="form-control"
                                           wire:model="company_email"
                                           readonly>
                                    @error('company_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <button type="button"
                                        class="btn btn-primary ms-2 mb-0"
                                        wire:click="openModal('email')"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal">
                                    Change
                                </button>
                            </div>


                            <div class="col-12 mt-4 mb-4">
                                <div class="p-4 rounded-4  border border-light-subtle shadow-sm">

                                    <div class="d-flex align-items-center mb-4">

                                        <div>
                                            <h6 class="fw-bold text-dark mb-0">Company Address Details</h5>

                                        </div>
                                    </div>

                                    <div class="row g-3">

                                        <div class="col-12 mb-2"
                                             x-data="addressAutocomplete()">
                                            <label class="form-label small fw-semibold text-secondary">Current
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
                                            <label class="form-label small fw-semibold text-secondary">Street <span
                                                      class="text-danger">*</span></label>
                                            <input type="text"
                                                   class="form-control border-light-subtle shadow-none"
                                                   wire:model="street"
                                                   placeholder="Enter Street">

                                            @error('street')
                                                <span class="text-danger x-small">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>



                            {{-- Business Type --}}
                            <div class="col-md-6 mb-2 mt-2">
                                <label class="form-label">Business Type <span class="text-danger">*</span></label>
                                <select class="form-control shadow-sm"
                                        wire:model="business_type">
                                    <option value="">-- Select Business Type --</option>
                                    <option value="Sole Trader">Sole Trader</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Limited Company">Limited Company</option>
                                    <option value="Community Interest Company">Community Interest Company</option>
                                    <option value="Charity">Charity</option>
                                </select>
                                @error('business_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>




                            <div class="col-md-6 mb-2 mt-2">
                                <label class="form-label">Company Logo</label>
                                <input type="file"
                                       class="form-control"
                                       wire:model="company_logo"
                                       accept="image/*">


                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}"
                                         class="img-thumbnail mt-2"
                                         width="80">

                                    <div wire:loading
                                         wire:target="company_logo">
                                        <span class="text-muted">Uploading...</span>
                                    </div>
                                @elseif ($company_logo_preview)
                                    <img src="{{ $company_logo_preview }}"
                                         class="img-thumbnail mt-2"
                                         width="80">
                                @endif

                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                            {{-- Registered Domain --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Company Website</label>
                                <input type="text"
                                       class="form-control shadow-sm"
                                       wire:model="registered_domain"
                                       pattern="^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,11}?$"
                                       placeholder="Enter a valid domain, e.g., example.com"
                                       title="Enter a valid domain, e.g., example.com"
                                       oninput="this.value = this.value.replace(/[^a-zA-Z0-9\.\-]/g,'')">
                                @error('registered_domain')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="update">
                            <span wire:loading
                                  wire:target="update">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove
                                  wire:target="update">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>







    <!-- Force Active Modal -->
    <div wire:ignore.self
         class="modal fade"
         id="forceActiveModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="forceActiveModal"
         aria-hidden="true"
         data-bs-backdrop="static"
         data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered"
             role="document">
            <div class="modal-content">
                <div class="modal-header"
                     style="background: linear-gradient(135deg, #0dcaf0, #0b9ed0);">
                    <h6 class="modal-title fw-600 text-white">
                        <i class="fas fa-bolt me-2"></i> Force Company to Active
                    </h6>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3 mb-3">
                            <i class="fas fa-exclamation-triangle fa-2x text-dark"></i>
                        </div>
                        <h5 class="fw-bold">Force Subscription to Active</h5>
                        <p class="text-muted small">
                            This will change the company's subscription status from <strong
                                    class="text-info">Trial</strong>
                            to <strong class="text-success">Active</strong> and set the subscription period.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-alt me-1 text-primary"></i>
                            Subscription Duration (Days)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-clock text-primary"></i>
                            </span>
                            <input type="number"
                                   class="form-control form-control-lg text-center fw-bold"
                                   wire:model="forceActiveDays"
                                   min="1"
                                   max="600"
                                   step="1"
                                   placeholder="Enter days (1-600)"
                                   style="font-size: 1.1rem;">
                            <span class="input-group-text bg-light border-start-0">Days</span>
                        </div>
                        @error('forceActiveDays')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Select between 1 to 600 days for the subscription period
                        </small>
                    </div>

                    <div class="alert alert-info small p-3 rounded-3"
                         style="background: #e3f2fd;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What will happen?</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            <li>Subscription status will change to <strong class="text-success">Active</strong></li>
                            <li>Subscription start date: <strong>{{ now()->format('d M Y') }}</strong></li>
                            <li>Subscription end date: Based on selected days</li>
                            <li>Trial ends at: <strong>{{ now()->format('d M Y') }}</strong></li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="button"
                            class="btn btn-success rounded-pill px-4"
                            wire:click="forceToActive"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="fas fa-bolt me-1"></i> Force to Active
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin me-1"></i> Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>


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

                        <!-- Email Input -->
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

                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
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
</div>


<script src="{{ asset('js/admin/changePassword.js') }}"></script>

<script>
    document.querySelectorAll('.toggle-password').forEach(function(element) {
        element.addEventListener('click', function() {
            const input = document.querySelector(this.getAttribute('toggle'));
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);

            // Change icon
            this.innerHTML = type === 'password' ?
                '<i class="fa fa-eye"></i>' :
                '<i class="fa fa-eye-slash"></i>';
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if the page has any GET parameters (i.e., filter applied)
        if (window.location.search.includes('search') ||
            window.location.search.includes('status') ||
            window.location.search.includes('start_date') ||
            window.location.search.includes('end_date')) {

            // Remove "show active" from all tabs
            document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('show', 'active'));
            document.querySelectorAll('.list-group-item').forEach(link => link.classList.remove('active'));

            // Activate Billing tab
            const billingTab = document.getElementById('billing');
            if (billingTab) billingTab.classList.add('show', 'active');

            const billingLink = document.querySelector('a[href="#billing"]');
            if (billingLink) billingLink.classList.add('active');
        }
    });
</script>


<script>
    Livewire.on('confirmDelete', companyId => {
        if (confirm("Are you sure you want to delete this company? This action cannot be undone.")) {
            Livewire.dispatch('deleteCompany', {
                id: companyId
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


<script>
    document.addEventListener('click', function(e) {
        ['country', 'state', 'city'].forEach(type => {
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
