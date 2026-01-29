<div>

    <div class="container-fluid py-4">
        <div class="row g-4">


            <div class="text-end mb-2">
                <a class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                   style="background-color:#f8f9fa; border:1px solid #0d6efd; padding:6px 12px; font-size:0.875rem;"
                   href="{{ route('super-admin.companies') }}">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Back to Companies
                </a>
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



                        <div class="card-header bg-white py-4 border-bottom-0 d-flex align-items-center justify-content-between"
                             style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">

                            <!-- Title -->
                            <h5 class="mb-0  text-dark d-flex align-items-center">

                                Company Overview
                                </h4>

                                <!-- Menu Button -->
                                <div class="dropdown"
                                     wire:ignore.self>
                                    <!-- Dropdown Button -->
                                    <button class="btn btn-sm btn-light border-0 px-md-3 px-2"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            wire:ignore>
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                        wire:ignore.self>
                                        <li>
                                            <a class="dropdown-item"
                                               href="#"
                                               data-bs-toggle="modal"
                                               data-bs-target="#manageCompanyProfile"
                                               wire:click="manageCompanyProfile({{ $details->id }})">
                                                <i class="fas fa-edit me-2 text-muted"></i> Manage Profile
                                                <span wire:loading
                                                      wire:target="manageCompanyProfile"
                                                      class="spinner-border spinner-border-sm ms-2"></span>
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item"
                                               href="#"
                                               wire:click.prevent="toggleStatus({{ $details->id }})">
                                                @if ($details->status == 'Active')
                                                    <i class="fas fa-user-minus me-2 text-muted"></i> Change to Inactive
                                                @else
                                                    <i class="fas fa-user-check me-2 text-muted"></i> Change to Active
                                                @endif
                                                <span wire:loading
                                                      wire:target="toggleStatus"
                                                      class="spinner-border spinner-border-sm ms-2"></span>
                                            </a>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <li>
                                            <a class="dropdown-item text-danger"
                                               href="#"
                                               wire:click.prevent="$dispatch('confirmDelete', {{ $details->id }})">
                                                <i class="fas fa-trash me-2"></i> Delete Company
                                            </a>
                                        </li>
                                    </ul>
                                </div>


                        </div>



                        <div class="card-body p-3 p-md-4">
                            <div class="row g-4 align-items-start">

                                <!-- Logo -->
                                <div class="col-lg-3 col-md-4 col-12 text-center">
                                    <div class="border rounded-4 p-3 bg-light shadow-sm mx-auto"
                                         style="max-width:180px;">
                                        <img src="{{ $details->company_logo_url ?? asset('assets/img/default-avatar.png') }}"
                                             class="img-fluid rounded-3 clickable-image"
                                             style="max-height:120px; object-fit:contain; cursor:pointer;"
                                             data-src="{{ $details->company_logo_url ?? asset('assets/img/default-avatar.png') }}">
                                    </div>
                                </div>

                                <!-- Info + Contact -->
                                <div class="col-lg-9 col-md-8 col-12">

                                    <!-- Company Info -->
                                    <div class="mb-4">
                                        <h4 class="fw-bold mb-1 d-flex flex-wrap align-items-center gap-2">
                                            {{ $details->company_name }}

                                            <span
                                                  class="badge px-3 py-2 fw-semibold
                        {{ $details->status == 'Active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                                {{ $details->status }}
                                            </span>
                                        </h4>

                                        <p class="text-muted mb-3 small">
                                            <i class="fa-solid fa-briefcase me-1"></i>
                                            {{ $details->business_type ?: 'N/A' }}
                                        </p>

                                        <dl class="row mb-0 small">
                                            <dt class="col-4 col-sm-3 fw-semibold text-dark">
                                                <i class="fa-regular fa-envelope me-1 text-muted"></i>
                                                Email
                                            </dt>
                                            <dd class="col-8 col-sm-9 text-truncate mb-1">
                                                {{ $details->company_email ?: 'N/A' }}
                                            </dd>

                                            <dt class="col-4 col-sm-3 fw-semibold text-dark">
                                                <i class="fa-solid fa-phone me-1 text-muted"></i>
                                                Phone
                                            </dt>
                                            <dd class="col-8 col-sm-9 mb-0">
                                                {{ $details->company_mobile ?: 'N/A' }}
                                            </dd>
                                        </dl>
                                    </div>

                                    <hr class="my-3">

                                    <!-- Contact / Location -->
                                    <div>
                                        <h6 class="fw-semibold text-secondary mb-2">
                                            <i class="fa-solid fa-location-dot me-1"></i>
                                            Contact Location
                                        </h6>

                                        <dl class="row mb-0 small">
                                            <dt class="col-5 col-sm-4 text-muted">
                                                <i class="fa-solid fa-house me-1"></i>
                                                House Number
                                            </dt>
                                            <dd class="col-7 col-sm-8 fw-medium mb-1">
                                                {{ $details->company_house_number ?: 'N/A' }}
                                            </dd>

                                            <dt class="col-5 col-sm-4 text-muted">
                                                <i class="fa-solid fa-map-location-dot me-1"></i>
                                                Address
                                            </dt>

                                            <dd class="col-7 col-sm-8 fw-medium">


                                                <div style="color: #334155; line-height: 1.6;">
                                                    {{ $details->street ? $details->street . ', ' : '' }}
                                                    {{ $details->city ? $details->city . ', ' : '' }}
                                                    {{ $details->state ? $details->state . ', ' : '' }}
                                                    {{ $details->postcode ? $details->postcode . ', ' : '' }}
                                                    <span
                                                          style="font-weight: 700;">{{ $details->country ?? 'N/A' }}</span>

                                                </div>
                                            </dd>

                                        </dl>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- ================================
                         BILLING / SUBSCRIPTION
                    ===================================== -->
                    <div class="tab-pane fade {{ $activeTab === 'billing' ? 'show active' : '' }}"
                         id="billing"
                         role="tabpanel"
                         aria-labelledby="billing-tab">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="mb-0 fw-bold">Billing / Subscription</h4>
                            </div>
                            <div class="card-body p-4">

                                <div class="mb-4">

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
                                             style="background: {{ $cardBg }}; border-radius: 12px; width: 250px; font-size: 0.85rem;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold text-uppercase">{{ $stripeCard['brand'] }}</span>
                                                <i class="bi bi-credit-card-2-front-fill"></i>
                                            </div>
                                            <div class="mb-2"
                                                 style="font-family: 'Courier New', monospace; font-size: 1rem; letter-spacing: 1.5px;">
                                                **** **** **** <strong>{{ $stripeCard['last4'] }}</strong>
                                            </div>
                                            <div class="d-flex justify-content-between"
                                                 style="font-size: 0.75rem;">
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
                                        <div class="card border border-dashed rounded text-center p-3 shadow-sm"
                                             style="width: 250px; font-size: 0.85rem;">
                                            <p class="text-muted mb-2"><i class="bi bi-info-circle-fill me-1"></i>No
                                                card saved yet.</p>
                                        </div>
                                    @endif
                                </div>




                                <!-- Subscription Info Cards -->
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small
                                                   class="text-muted text-uppercase fw-semibold d-block mb-1 fs-7">Registered
                                                Domain</small>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ $details->registered_domain ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small
                                                   class="text-muted text-uppercase fw-semibold d-block mb-1 fs-7">Calendar
                                                Year</small>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ ucfirst($details->calendarYearSetting->calendar_year ?? 'N/A') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div
                                             class="p-3 rounded-3
                        @if (isset($details->subscription_status) && $details->subscription_status === 'active') bg-success-subtle border border-success @else bg-warning-subtle border border-warning @endif">
                                            <small
                                                   class="text-muted text-uppercase fw-semibold d-block mb-1 fs-7">Subscription
                                                Status</small>
                                            <span
                                                  class="badge
                            @if (isset($details->subscription_status) && $details->subscription_status === 'active') bg-success @else bg-warning text-dark @endif
                            fs-7 py-1 px-2 fw-bold">
                                                {{ isset($details->subscription_status) ? ucfirst($details->subscription_status) : 'N/A' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small
                                                   class="text-muted text-uppercase fw-semibold d-block mb-1 fs-7">Subscription
                                                Start</small>
                                            <p class="fs-6 fw-bold mb-0 text-dark">
                                                {{ $details->subscription_start?->format('d M Y') ?? 'N/A' }}</p>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small
                                                   class="text-muted text-uppercase fw-semibold d-block mb-1 fs-7">Subscription
                                                End</small>
                                            <p
                                               class="fs-6 fw-bold mb-0 @if (isset($details->subscription_end) && $details->subscription_end->isPast()) text-danger @else text-dark @endif">
                                                {{ $details->subscription_end?->format('d M Y') ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Search & Filter for Invoices -->
                                <form id="invoiceFilterForm"
                                      method="GET"
                                      class="row g-2 mb-3">
                                    <div class="col-md-4">
                                        <input type="text"
                                               name="search"
                                               value="{{ request('search') }}"
                                               class="form-control form-control-sm"
                                               placeholder="Search by Invoice #">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date"
                                               name="start_date"
                                               value="{{ request('start_date') }}"
                                               class="form-control form-control-sm"
                                               placeholder="Start Date">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date"
                                               name="end_date"
                                               value="{{ request('end_date') }}"
                                               class="form-control form-control-sm"
                                               placeholder="End Date">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary w-100">Filter</button>
                                    </div>
                                </form>



                                <!-- Invoices Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped align-middle text-center mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Invoice #</th>
                                                <th>Billing Period</th>
                                                <th>Subtotal</th>
                                                <th>VAT</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Invoice Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($invoices as $invoice)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $invoice->invoice_number }}</td>
                                                    <td>{{ $invoice->billing_period_start->format('d M, Y') }} -
                                                        {{ $invoice->billing_period_end->format('d M, Y') }}</td>
                                                    <td>£{{ number_format($invoice->subtotal, 2) }}</td>
                                                    <td>£{{ number_format($invoice->vat, 2) }}</td>
                                                    <td>£{{ number_format($invoice->total, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            {{ ucfirst($invoice->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8"
                                                        class="text-muted">No invoices found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>



                    <div class="tab-pane fade {{ $activeTab === 'emp' ? 'show active' : '' }}"
                         id="emp"
                         role="tabpanel"
                         aria-labelledby="emp-tab">


                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="mb-0 fw-bold">Employees</h4>
                            </div>
                            <div class="card-body">

                                @if ($details->employees->count())
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped text-center align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Register</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($details->employees as $emp)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>

                                                        <td>
                                                            <a href="{{ route('super-admin.dashboard.employees.details', $emp->id) }}"
                                                               class="badge badge-xs text-primary"
                                                               style="
        background: transparent;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
   "
                                                               onmouseover="this.style.textDecoration='underline'"
                                                               onmouseout="this.style.textDecoration='none'">
                                                                {{ $emp->full_name ?? 'N/A' }}
                                                            </a>


                                                        </td>


                                                        <td>{{ $emp->email }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($emp->created_at)->format('d/m/Y') }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                  class="badge rounded-pill px-3 py-2
                                    {{ $emp->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                                {{ $emp->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div> <!-- /.table-responsive -->


                                    <div class="row mt-4">
                                        <div class="col-12 d-flex gap-3 justify-content-center">
                                            <!-- Active Employees -->
                                            <div class="px-4 py-2 rounded-pill shadow-sm small"
                                                 style="background-color: #e9f5ee; color: #1b5e20; font-weight: 600; border: 1px solid #d1e7dd;">
                                                Total Active Employees: {{ $activeCount }}
                                            </div>

                                            <!-- Former/Inactive Employees -->
                                            <div class="px-4 py-2 rounded-pill shadow-sm small"
                                                 style="background-color: #fce8e8; color: #c62828; font-weight: 600; border: 1px solid #f8d7da;">
                                                Total Inactive Employees: {{ $formerCount }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">No employees found.</p>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="tab-pane fade {{ $activeTab === 'companyInfo' ? 'show active' : '' }}"
                         id="companyInfo"
                         role="tabpanel"
                         aria-labelledby="companyInfo-tab"
                         style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">

                        <div class="card border-0 rounded-4 mb-4"
                             style="box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid #f0f0f0 !important; overflow: hidden;">


                            <div class="card-header bg-white py-3 border-0">
                                <h5 class="mb-0 fw-bold">Company Information</h4>
                            </div>

                            <div class="card-body p-4">
                                <div class="row g-5 align-items-start">

                                    <div class="col-lg-3 col-md-4 text-center">
                                        <div class="p-4 rounded-4"
                                             style="background: linear-gradient(145deg, #ffffff, #f8f9fc); border: 1px solid #edf2f7; transition: transform 0.3s ease;">
                                            <img src="{{ $details->company_logo ? asset('storage/' . $details->company_logo) : asset('assets/img/default-avatar.png') }}"
                                                 class="img-fluid rounded-3"
                                                 style="max-height: 140px; width: 100%; object-fit: contain; filter: drop-shadow(0 5px 15px rgba(0,0,0,0.08));">

                                            <div class="mt-3">
                                                <span class="badge"
                                                      style="background: #eef2ff; color: #4e73df; font-weight: 600; padding: 6px 12px; border-radius: 8px;">
                                                    {{ $details->business_type ?? 'Business' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-9 col-md-8">
                                        <div class="row row-cols-1 row-cols-md-2 g-4">

                                            <div class="col-12 border-bottom pb-3 mb-2">
                                                <label
                                                       style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 4px; display: block;">Company
                                                    Name</label>
                                                <div style="font-size: 1.15rem; color: #1e293b; font-weight: 600;">
                                                    {{ $details->company_name ?? 'N/A' }}</div>
                                            </div>

                                            <div class="col">
                                                <label
                                                       style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 2px; display: block;">Company
                                                    House Number</label>
                                                <div style="color: #475569; font-weight: 500;">
                                                    {{ $details->company_house_number ?? 'N/A' }}</div>
                                            </div>

                                            <div class="col">
                                                <label
                                                       style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 2px; display: block;">Company
                                                    Website</label>
                                                <div>
                                                    @if ($details->registered_domain)
                                                        <a href="https://{{ $details->registered_domain }}"
                                                           target="_blank"
                                                           style="color: #4e73df; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center;">
                                                            {{ $details->registered_domain }}
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                 width="12"
                                                                 height="12"
                                                                 fill="currentColor"
                                                                 class="ms-1"
                                                                 viewBox="0 0 16 16">
                                                                <path
                                                                      d="M8.5 1.5A.5.5 0 0 1 9 1h5a1 1 0 0 1 1 1v5a.5.5 0 0 1-1 0V2.5L7.854 8.646a.5.5 0 1 1-.708-.708L13.293 2H9a.5.5 0 0 1-.5-.5z" />
                                                            </svg>
                                                        </a>
                                                    @else
                                                        <span style="color: #94a3b8;">N/A</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col">
                                                <label
                                                       style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 2px; display: block;">Company
                                                    Mobile</label>
                                                <div style="color: #475569; font-weight: 500;">
                                                    {{ $details->company_mobile ?? 'N/A' }}</div>
                                            </div>

                                            <div class="col">
                                                <label
                                                       style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; margin-bottom: 2px; display: block;">Company
                                                    Email</label>
                                                <div style="color: #475569; font-weight: 500;">
                                                    {{ $details->company_email ?? 'N/A' }}</div>
                                            </div>

                                            <div class="col-12 mt-4">
                                                <div class="p-3 rounded-3"
                                                     style="background-color: #f8fafc; border: 1px dashed #cbd5e1;">
                                                    <label
                                                           style="font-size: 0.75rem; color: #64748b; font-weight: 700; margin-bottom: 4px; display: block;">Registered
                                                        Office Address</label>
                                                    <div style="color: #334155; line-height: 1.6;">
                                                        {{ $details->street ? $details->street . ', ' : '' }}
                                                        {{ $details->city ? $details->city . ', ' : '' }}
                                                        {{ $details->state ? $details->state . ', ' : '' }}
                                                        {{ $details->postcode ? $details->postcode . ', ' : '' }}
                                                        <span
                                                              style="font-weight: 700;">{{ $details->country ?? 'N/A' }}</span>
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
                                                   placeholder="Enter House Number">

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
