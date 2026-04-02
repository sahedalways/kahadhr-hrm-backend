@push('styles')
    <link href="{{ asset('assets/css/company/profile-settings.css') }}"
          rel="stylesheet" />
@endpush


<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500">Company Profile Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="col-12 mt-4">
                <div class="p-4 rounded-4 border border-light-subtle shadow-sm"
                     style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-circle p-2"
                             style="background-color: rgba(13, 202, 240, 0.1);">
                            <i class="fas fa-credit-card"
                               style="color: #0dcaf0; font-size: 18px;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Payment Information</h6>
                            <p class="text-muted small mb-0">Manage subscription and payment details</p>
                        </div>
                    </div>

                    @php
                        // Get subscription info from helper
                        $subInfo = getSubscriptionInfo(
                            $subscription_status ?? 'expired',
                            $trial_ends_at ?? null,
                            $subscription_start ?? null,
                            $subscription_end ?? null,
                        );

                        // Status colors configuration
                        $subStatusColors = [
                            'active' => [
                                'bg' => '#d1fae5',
                                'color' => '#065f46',
                                'icon' => 'fa-check-circle',
                            ],
                            'trial' => [
                                'bg' => '#dbeafe',
                                'color' => '#1e40af',
                                'icon' => 'fa-hourglass-half',
                            ],
                            'expired' => [
                                'bg' => '#fee2e2',
                                'color' => '#991b1b',
                                'icon' => 'fa-times-circle',
                            ],
                            'suspended' => [
                                'bg' => '#fef3c7',
                                'color' => '#92400e',
                                'icon' => 'fa-ban',
                            ],
                        ];
                        $subStatus = strtolower($subscription_status ?? 'expired');
                        $subStyle = $subStatusColors[$subStatus] ?? $subStatusColors['expired'];

                        // Payment status colors
                        $payStatusColors = [
                            'paid' => [
                                'bg' => '#d1fae5',
                                'color' => '#065f46',
                                'icon' => 'fa-check-circle',
                            ],
                            'unpaid' => [
                                'bg' => '#fee2e2',
                                'color' => '#991b1b',
                                'icon' => 'fa-exclamation-circle',
                            ],
                            'pending' => [
                                'bg' => '#fef3c7',
                                'color' => '#92400e',
                                'icon' => 'fa-clock',
                            ],
                            'failed' => [
                                'bg' => '#f3f4f6',
                                'color' => '#374151',
                                'icon' => 'fa-times-circle',
                            ],
                        ];
                        $payStatus = strtolower($payment_status ?? 'pending');
                        $payStyle = $payStatusColors[$payStatus] ?? $payStatusColors['pending'];
                    @endphp

                    <div class="row g-4">
                        <!-- Subscription Status -->
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-white border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-chart-line"
                                       style="color: #0dcaf0; font-size: 14px;"></i>
                                    <small class="text-muted text-uppercase fw-semibold">Subscription
                                        Status</small>
                                </div>
                                <span class="badge px-3 py-2 rounded-pill"
                                      style="background: {{ $subStyle['bg'] }}; color: {{ $subStyle['color'] }}; font-weight: 500;">
                                    <i class="fas {{ $subStyle['icon'] }} me-1"></i>
                                    {{ ucfirst($subscription_status ?? 'Expired') }}
                                </span>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-white border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-dollar-sign"
                                       style="color: #0dcaf0; font-size: 14px;"></i>
                                    <small class="text-muted text-uppercase fw-semibold">Payment
                                        Status</small>
                                </div>
                                <span class="badge px-3 py-2 rounded-pill"
                                      style="background: {{ $payStyle['bg'] }}; color: {{ $payStyle['color'] }}; font-weight: 500;">
                                    <i class="fas {{ $payStyle['icon'] }} me-1"></i>
                                    {{ ucfirst($payment_status ?? 'Pending') }}
                                </span>
                            </div>
                        </div>

                        <!-- Subscription Start -->
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-white border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-play-circle"
                                       style="color: #0dcaf0; font-size: 14px;"></i>
                                    <small class="text-muted text-uppercase fw-semibold">Start Date</small>
                                </div>
                                <p class="fw-bold mb-0 text-dark"
                                   style="font-size: 0.95rem;">
                                    {{ $subscription_start ? \Carbon\Carbon::parse($subscription_start)->format('d M Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- Subscription End / Next Payment -->
                        <div class="col-md-6 col-lg-3">
                            <div class="p-3 rounded-3 bg-white border">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-stop-circle"
                                       style="color: #0dcaf0; font-size: 14px;"></i>
                                    <small class="text-muted text-uppercase fw-semibold">
                                        @if ($subscription_status == 'trial')
                                            Trial End Date
                                        @else
                                            End Date
                                        @endif
                                    </small>
                                </div>
                                <p class="fw-bold mb-0 {{ isset($subscription_end) && \Carbon\Carbon::parse($subscription_end)->isPast() ? 'text-danger' : 'text-dark' }}"
                                   style="font-size: 0.95rem;">
                                    @if ($subscription_status == 'trial' && isset($trial_ends_at))
                                        {{ \Carbon\Carbon::parse($trial_ends_at)->format('d M Y') }}
                                    @elseif(isset($subscription_end))
                                        {{ \Carbon\Carbon::parse($subscription_end)->format('d M Y') }}
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Information from Helper -->
                    @if ($subInfo['html'] && $subscription_status != 'active' && $subscription_status != 'expired')
                        <div class="mt-3 p-3 rounded-3"
                             style="background: linear-gradient(135deg, #e0f2fe, #f0f9ff); border-left: 4px solid #0dcaf0;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle"
                                   style="color: #0dcaf0;"></i>
                                <span class="small">Subscription Info:</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small">{!! $subInfo['html'] !!}</span>
                            </div>
                        </div>
                    @endif



                    <!-- Active Subscription Next Payment Info -->
                    @if (
                        $subscription_status == 'active' &&
                            isset($subscription_end) &&
                            \Carbon\Carbon::parse($subscription_end)->isFuture())
                        <div class="mt-3 p-3 rounded-3"
                             style="background: linear-gradient(135deg, #d1fae5, #ecfdf5); border-left: 4px solid #10b981;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-credit-card"
                                   style="color: #10b981;"></i>
                                <span class="small fw-semibold"
                                      style="color: #065f46;">Next Payment</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small">Your next payment will be charged on
                                    <strong>{{ \Carbon\Carbon::parse($subscription_end)->format('d M Y') }}</strong></span>
                                @php
                                    $daysUntilPayment = (int) ceil(
                                        \Carbon\Carbon::now()->diffInDays(
                                            \Carbon\Carbon::parse($subscription_end),
                                            false,
                                        ),
                                    );
                                @endphp
                                @if ($daysUntilPayment <= 7)
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background: #f59e0b; color: white; font-size: 10px;">
                                        {{ $daysUntilPayment }} days left
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Alert for Failed Payment -->
                    @if (isset($payment_status) && strtolower($payment_status) == 'failed')
                        <div class="mt-3 p-3 rounded-3"
                             style="background: #fee2e2; border-left: 4px solid #dc2626;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-exclamation-triangle"
                                   style="color: #dc2626;"></i>
                                <span class="small fw-semibold"
                                      style="color: #991b1b;">Payment Failed</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small text-danger">Please update your payment method to
                                    continue service</span>
                            </div>
                        </div>
                    @endif

                    <!-- Alert for Unpaid Payment -->
                    @if (isset($payment_status) && strtolower($payment_status) == 'unpaid')
                        <div class="mt-3 p-3 rounded-3"
                             style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-bell"
                                   style="color: #f59e0b;"></i>
                                <span class="small fw-semibold"
                                      style="color: #92400e;">Payment Due</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small">Please complete your payment to avoid service
                                    interruption</span>
                            </div>
                        </div>
                    @endif

                    <!-- Expired Subscription Alert -->
                    @if ($subscription_status == 'expired')
                        <div class="mt-3 p-3 rounded-3"
                             style="background: #fee2e2; border-left: 4px solid #dc2626;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-exclamation-circle"
                                   style="color: #dc2626;"></i>
                                <span class="small fw-semibold"
                                      style="color: #991b1b;">Subscription Expired</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small">Please renew your subscription to continue using our
                                    services</span>
                                <a href="{{ route('subscription.renew') }}"
                                   class="btn btn-sm btn-danger ms-auto"
                                   style="font-size: 11px; padding: 4px 12px;">
                                    Renew Now
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Suspended Subscription Alert -->
                    @if ($subscription_status == 'suspended')
                        <div class="mt-3 p-3 rounded-3"
                             style="background: #fef3c7; border-left: 4px solid #f59e0b;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-ban"
                                   style="color: #f59e0b;"></i>
                                <span class="small fw-semibold"
                                      style="color: #92400e;">Account Suspended</span>
                                <span class="small text-muted mx-2">|</span>
                                <span class="small">Please contact support to resolve the issue</span>
                                <a href="{{ route('contact.support') }}"
                                   class="btn btn-sm btn-warning ms-auto"
                                   style="font-size: 11px; padding: 4px 12px;">
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>






            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <form class="row g-0"
                          wire:submit.prevent="save">



                        <!-- Form Content -->
                        <div class="p-4 p-lg-5">

                            <!-- Company Information Section -->
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                                    <div class="rounded-circle p-1"
                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                        <i class="fas fa-info-circle"
                                           style="color: #0dcaf0; font-size: 18px;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0"
                                        style="color: #1a2c3e;">Company Information</h5>
                                    <span class="badge bg-light text-muted ms-2">Basic Details</span>
                                </div>

                                <div class="row g-4">
                                    <!-- Company Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-building me-1"></i> Company Name <span
                                                  class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-lg border-0 bg-light rounded-3"
                                               style="padding: 12px 16px;"
                                               wire:model="company_name"
                                               placeholder="Enter company name">
                                        @error('company_name')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- House Number -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-home me-1"></i> Company House Number <span
                                                  class="text-danger">*</span>
                                        </label>
                                        <input type="number"
                                               class="form-control form-control-lg border-0 bg-light rounded-3"
                                               style="padding: 12px 16px;"
                                               wire:model="company_house_number"
                                               placeholder="House No. / Flat No.">
                                        @error('company_house_number')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Business Type -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-briefcase me-1"></i> Business Type
                                        </label>
                                        <select class="form-select form-select-lg border-0 bg-light rounded-3"
                                                style="padding: 12px 16px;"
                                                wire:model="business_type">
                                            <option value="">-- Select Business Type --</option>
                                            <option value="Sole Trader">Sole Trader</option>
                                            <option value="Partnership">Partnership</option>
                                            <option value="Limited Company">Limited Company</option>
                                            <option value="Community Interest Company">Community Interest Company
                                            </option>
                                            <option value="Charity">Charity</option>
                                        </select>
                                        @error('business_type')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                                    <div class="rounded-circle p-1"
                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                        <i class="fas fa-location-dot"
                                           style="color: #0dcaf0; font-size: 18px;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0"
                                        style="color: #1a2c3e;">Address Details</h5>
                                    <span class="badge bg-light text-muted ms-2">Location Information</span>
                                </div>

                                <div class="row g-4">
                                    <!-- Country -->
                                    <div class="col-md-6"
                                         id="countryDropdownContainer">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-globe me-1"></i> Country <span
                                                  class="text-danger">*</span>
                                        </label>
                                        <div style="position:relative;">
                                            <button class="btn w-100 text-start d-flex align-items-center justify-content-between border-0 bg-light rounded-3"
                                                    type="button"
                                                    id="countryDropdownButton"
                                                    style="padding: 12px 16px; background: #f8f9fa !important;">
                                                <span>
                                                    @if ($country)
                                                        <img src="{{ collect($filteredCountries)->firstWhere('name', $country)['flag'] ?? '' }}"
                                                             style="width: 20px; height: 15px; margin-right: 8px;">
                                                    @endif
                                                    {{ $country ?? 'Select Country' }}
                                                </span>
                                                <i class="fas fa-chevron-down text-muted"></i>
                                            </button>

                                            <div id="countryDropdownMenu"
                                                 wire:ignore.self
                                                 style="display:none; position:absolute; z-index:1000; width:100%; max-height:250px; overflow-y:auto; background:#fff; border:1px solid #e9ecef; border-radius:12px; padding: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                                                <input type="text"
                                                       class="form-control mb-2 rounded-3"
                                                       placeholder="Search country..."
                                                       wire:model.live="countrySearch"
                                                       style="border: 1px solid #e9ecef;">
                                                @foreach ($filteredCountries as $c)
                                                    <a href="#"
                                                       class="dropdown-item d-flex align-items-center py-2 rounded-2"
                                                       style="gap: 8px; transition: all 0.2s;"
                                                       wire:click.prevent="$set('country', '{{ $c['name'] }}'); closeDropdown()">
                                                        <img src="{{ $c['flag'] }}"
                                                             alt="{{ $c['name'] }}"
                                                             style="width:20px; height:15px;">
                                                        <span>{{ $c['name'] }}</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                        @error('country')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div class="col-md-6"
                                         id="stateDropdownContainer">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> State / Province
                                        </label>
                                        <div style="position:relative;">
                                            <button class="btn w-100 text-start d-flex align-items-center justify-content-between border-0 bg-light rounded-3"
                                                    type="button"
                                                    id="stateDropdownButton"
                                                    style="padding: 12px 16px; background: #f8f9fa !important;"
                                                    @if (empty($states)) disabled @endif>
                                                <span>{{ $state ?? 'Select State' }}</span>
                                                <i class="fas fa-chevron-down text-muted"></i>
                                            </button>

                                            <div id="stateDropdownMenu"
                                                 wire:ignore.self
                                                 style="display:none; position:absolute; z-index:1000; width:100%; max-height:250px; overflow-y:auto; background:#fff; border:1px solid #e9ecef; border-radius:12px; padding: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                                                <input type="text"
                                                       class="form-control mb-2 rounded-3"
                                                       placeholder="Search state..."
                                                       wire:model.live="stateSearch"
                                                       style="border: 1px solid #e9ecef;">
                                                @forelse ($states as $s)
                                                    <a href="#"
                                                       class="dropdown-item py-2 rounded-2"
                                                       style="transition: all 0.2s;"
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
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- City -->
                                    <div class="col-md-6"
                                         id="cityDropdownContainer">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-city me-1"></i> City
                                        </label>
                                        <div style="position:relative;">
                                            <button class="btn w-100 text-start d-flex align-items-center justify-content-between border-0 bg-light rounded-3"
                                                    type="button"
                                                    id="cityDropdownButton"
                                                    style="padding: 12px 16px; background: #f8f9fa !important;"
                                                    @if (empty($cities)) disabled @endif>
                                                <span>{{ $city ?? 'Select City' }}</span>
                                                <i class="fas fa-chevron-down text-muted"></i>
                                            </button>

                                            <div id="cityDropdownMenu"
                                                 wire:ignore.self
                                                 style="display:none; position:absolute; z-index:1000; width:100%; max-height:250px; overflow-y:auto; background:#fff; border:1px solid #e9ecef; border-radius:12px; padding: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                                                <input type="text"
                                                       class="form-control mb-2 rounded-3"
                                                       placeholder="Search city..."
                                                       wire:model.live="citySearch"
                                                       style="border: 1px solid #e9ecef;">
                                                @forelse ($cities as $c)
                                                    <a href="#"
                                                       class="dropdown-item py-2 rounded-2"
                                                       style="transition: all 0.2s;"
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
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Post Code -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-mail-bulk me-1"></i> Post Code <span
                                                  class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-lg border-0 bg-light rounded-3"
                                               style="padding: 12px 16px; text-transform: uppercase;"
                                               wire:model="postcode"
                                               oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase()"
                                               placeholder="e.g. SW1A 1AA">
                                        @error('postcode')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Street -->
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-road me-1"></i> Street Address <span
                                                  class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control form-control-lg border-0 bg-light rounded-3"
                                               style="padding: 12px 16px;"
                                               wire:model="street"
                                               placeholder="Enter street name">
                                        @error('street')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Brand Settings Section -->
                            <div class="mb-5">
                                <div class="d-flex align-items-center gap-2 mb-4 pb-2 border-bottom">
                                    <div class="rounded-circle p-1"
                                         style="background-color: rgba(13, 202, 240, 0.1);">
                                        <i class="fas fa-palette"
                                           style="color: #0dcaf0; font-size: 18px;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0"
                                        style="color: #1a2c3e;">Brand & Company Settings</h5>
                                    <span class="badge bg-light text-muted ms-2">Visual & Web</span>
                                </div>

                                <div class="row g-4">
                                    <!-- Company Logo -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-image me-1"></i> Company Logo
                                        </label>
                                        <div class="border-0 bg-light rounded-3 p-3"
                                             style="background: #f8f9fa !important;">
                                            <input type="file"
                                                   class="form-control"
                                                   wire:model="company_logo"
                                                   accept="image/*"
                                                   style="border: none; background: transparent;">
                                            @error('company_logo')
                                                <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                            @enderror

                                            @if ($company_logo)
                                                <div class="mt-3 text-center">
                                                    <img src="{{ $company_logo->temporaryUrl() }}"
                                                         class="img-thumbnail shadow-sm rounded-3"
                                                         style="max-width: 120px; border: none;">
                                                    <div wire:loading
                                                         wire:target="company_logo"
                                                         class="mt-2">
                                                        <span class="text-muted small"><i
                                                               class="fas fa-spinner fa-spin"></i> Uploading...</span>
                                                    </div>
                                                </div>
                                            @elseif ($old_company_logo)
                                                <div class="mt-3 text-center">
                                                    <img src="{{ $old_company_logo }}"
                                                         class="img-thumbnail shadow-sm rounded-3"
                                                         style="max-width: 120px; border: none;">
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Registered Domain -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-uppercase text-muted mb-2">
                                            <i class="fas fa-globe me-1"></i> Company Website
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text border-0 bg-light"
                                                  style="color: #0dcaf0;">
                                                <i class="fas fa-link"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control form-control-lg border-0 bg-light rounded-3"
                                                   style="padding: 12px 16px;"
                                                   wire:model="registered_domain"
                                                   placeholder="yourdomain.com">
                                        </div>
                                        @error('registered_domain')
                                            <span class="text-danger small d-block mt-1">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted d-block mt-1">Enter without https:// (e.g.,
                                            example.com)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end gap-3 pt-3 border-top">

                                <div class="d-flex justify-content-end">
                                    <button type="submit"
                                            class="btn btn-success px-4 py-2 shadow-sm"
                                            wire:loading.attr="disabled"
                                            wire:target="save">
                                        <span wire:loading
                                              wire:target="save">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                        </span>
                                        <span wire:loading.remove
                                              wire:target="save">Save Changes</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>


</div>

<script>
    function copyFullDomain() {
        const input = document.getElementById('fullDomain');
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Copied: ' + input.value);
        });
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
