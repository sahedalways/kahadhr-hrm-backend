<x-layouts.app>
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

                            <a class="list-group-item list-group-item-action active py-3 px-4 fw-semibold border-0"
                               data-bs-toggle="tab"
                               href="#overview"
                               role="tab"
                               aria-controls="overview"
                               aria-selected="true"
                               style="border-left: 4px solid var(--bs-primary) !important;">
                                <i class="bi bi-buildings me-3 fs-5"></i> Company Overview
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0 "
                               data-bs-toggle="tab"
                               href="#billing"
                               role="tab"
                               aria-controls="billing"
                               aria-selected="false">
                                <i class="bi bi-receipt me-3 fs-5"></i> Billing / Subscription
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0"
                               data-bs-toggle="tab"
                               href="#bankinfo"
                               role="tab"
                               aria-controls="bankinfo"
                               aria-selected="false">
                                <i class="bi bi-credit-card-2-front me-3 fs-5"></i> Payment Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 px-4 fw-semibold border-0"
                               data-bs-toggle="tab"
                               href="#employees"
                               role="tab"
                               aria-controls="employees"
                               aria-selected="false">
                                <i class="bi bi-people me-3 fs-5"></i> Employees
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
                    <div class="tab-pane fade show active"
                         id="overview">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold text-dark">Company Overview</h4>
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
                                                    House / Suite
                                                </dt>
                                                <dd class="col-7 col-sm-8 fw-medium mb-1">
                                                    {{ $details->company_house_number ?: 'N/A' }}
                                                </dd>

                                                <dt class="col-5 col-sm-4 text-muted">
                                                    <i class="fa-solid fa-map-location-dot me-1"></i>
                                                    Address
                                                </dt>
                                                <dd class="col-7 col-sm-8 fw-medium">
                                                    {{ $details->address_contact_info ?: 'Not Provided' }}
                                                </dd>
                                            </dl>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ================================
                         BILLING / SUBSCRIPTION
                    ===================================== -->
                    <div class="tab-pane fade"
                         id="billing">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Billing / Subscription</h4>
                            </div>
                            <div class="card-body p-4">

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

                    <!-- ================================
                         BANK INFORMATION
                    ===================================== -->
                    <div class="tab-pane fade"
                         id="bankinfo">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Payment Information</h4>
                            </div>
                            <div class="card-body d-flex flex-wrap gap-3">
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
                                        <!-- Brand & Icon -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-uppercase">{{ $stripeCard['brand'] }}</span>
                                            <i class="bi bi-credit-card-2-front-fill"></i>
                                        </div>

                                        <!-- Card Number -->
                                        <div class="mb-2"
                                             style="font-family: 'Courier New', monospace; font-size: 1rem; letter-spacing: 1.5px;">
                                            **** **** **** <strong>{{ $stripeCard['last4'] }}</strong>
                                        </div>

                                        <!-- Labels & Info -->
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
                                        <p class="text-muted mb-2"><i class="bi bi-info-circle-fill me-1"></i>No card
                                            saved yet.</p>

                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ================================
                         EMPLOYEES
                    ===================================== -->
                    <div class="tab-pane fade"
                         id="employees">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Employees</h4>
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
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($details->employees as $emp)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td style="cursor:pointer; transition: background-color 0.3s;"
                                                            onclick="window.location='{{ route('super-admin.dashboard.employees.details', $emp->id) }}'"
                                                            onmouseover="this.style.backgroundColor='#f0f8ff';"
                                                            onmouseout="this.style.backgroundColor=''">
                                                            {{ $emp->full_name }}
                                                        </td>

                                                        <td>{{ $emp->email }}</td>
                                                        <td>{{ ucfirst($emp->role) }}</td>
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
                                    </div>
                                @else
                                    <p class="text-muted">No employees found.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ================================
                         SETTINGS
                    ===================================== -->
                    <div class="tab-pane fade"
                         id="settingsItem">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Settings</h4>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">Change Password</h6>
                                <form id="changePasswordForm">
                                    <input type="hidden"
                                           id="companyId"
                                           value="{{ $details->id }}">
                                    <div class="mb-3 position-relative">
                                        <label class="form-label fw-semibold">New Password <span
                                                  class="text-danger">*</span></label>
                                        <input type="password"
                                               id="new_password"
                                               class="form-control"
                                               placeholder="Enter new password"
                                               required>
                                        <span class="toggle-password"
                                              toggle="#new_password"
                                              style="position:absolute; right:10px; top:40px; cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>

                                    <div class="mb-3 position-relative">
                                        <label class="form-label fw-semibold">Confirm Password <span
                                                  class="text-danger">*</span></label>
                                        <input type="password"
                                               id="confirm_password"
                                               class="form-control"
                                               placeholder="Confirm new password"
                                               required>
                                        <span class="toggle-password"
                                              toggle="#confirm_password"
                                              style="position:absolute; right:10px; top:40px; cursor:pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-primary px-4 py-2 fw-semibold"
                                            id="changePasswordBtn">
                                        <span id="btnText">Save Password</span>
                                        <span id="btnLoader"
                                              class="spinner-border spinner-border-sm ms-2 d-none"
                                              role="status"></span>
                                    </button>
                                    <div id="passwordMessage"
                                         class="mt-2 text-danger fw-semibold"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
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


</x-layouts.app>
