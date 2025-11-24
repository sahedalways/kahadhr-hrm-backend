<x-layouts.app>
    <div class="container-fluid py-4">
        <div class="row g-4">
            <a class="list-group-item list-group-item-action py-3 fw-semibold "
                href="{{ route('super-admin.companies') }}">
                <i class="bi bi-list-ul me-2"></i> Company List
            </a>
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-0">

                        <div class="list-group list-group-flush">


                            <a class="list-group-item list-group-item-action active py-3 fw-semibold"
                                data-bs-toggle="tab" href="#overview">
                                <i class="bi bi-buildings me-2"></i> Company Overview
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#billing">
                                <i class="bi bi-receipt me-2"></i> Billing / Subscription
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#bankinfo">
                                <i class="bi bi-credit-card-2-front me-2"></i> Bank Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#employees">
                                <i class="bi bi-people me-2"></i> Employees
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#settingsItem">
                                <i class="bi bi-gear me-2"></i> Settings
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
                    <div class="tab-pane fade show active" id="overview">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold text-dark">Company Overview</h4>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 text-center">
                                        <img src="{{ $details->company_logo_url ?? asset('assets/default-image.jpg') }}"
                                            class="img-fluid rounded-3 shadow-sm mb-3"
                                            style="max-height: 160px; object-fit: contain;">
                                    </div>
                                    <div class="col-md-8">
                                        <h2 class="fw-bold mb-1">{{ $details->company_name }}</h2>
                                        <p class="text-muted mb-2 fs-6">{{ $details->business_type ?: 'N/A' }}</p>
                                        <span
                                            class="badge px-3 py-2 fs-6 rounded-pill 
                                            {{ $details->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $details->status }}
                                        </span>
                                        <hr>
                                        <p class="mb-1"><strong>Email:</strong> {{ $details->company_email }}</p>
                                        <p class="mb-1"><strong>Phone:</strong> {{ $details->company_mobile }}</p>
                                        <p class="mb-1"><strong>House Number:</strong>
                                            {{ $details->company_house_number }}</p>
                                        <p class="mb-1"><strong>Contact Address:</strong>
                                            {{ $details->address_contact_info ?: 'Not Provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================================
                         BILLING / SUBSCRIPTION
                    ===================================== -->
                    <div class="tab-pane fade" id="billing">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Billing / Subscription</h4>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Registered Domain:</strong>
                                    {{ $details->registered_domain ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Calendar Year:</strong>
                                    {{ ucfirst($details->calendar_year) }}</p>
                                <p class="mb-1"><strong>Billing Plan:</strong>
                                    {{ $details->billingPlan->name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Subscription Status:</strong>
                                    {{ ucfirst($details->subscription_status) }}</p>
                                <p class="mb-1"><strong>Subscription Start:</strong>
                                    {{ $details->subscription_start?->format('d M Y') ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Subscription End:</strong>
                                    {{ $details->subscription_end?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- ================================
                         BANK INFORMATION
                    ===================================== -->
                    <div class="tab-pane fade" id="bankinfo">
                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Bank Information</h4>
                            </div>
                            <div class="card-body">
                                @forelse ($details->bankInfos as $bank)
                                    <div class="border rounded-3 p-3 shadow-sm mb-3 bg-light">
                                        <p class="mb-1"><strong>Bank Name:</strong> {{ $bank->bank_name }}</p>
                                        <p class="mb-1"><strong>Card Number:</strong> {{ $bank->card_number }}</p>
                                        <p class="mb-1"><strong>Expiry Date:</strong> {{ $bank->expiry_date }}</p>
                                        <p class="mb-1"><strong>CVV:</strong> {{ $bank->cvv }}</p>
                                    </div>
                                @empty
                                    <p class="text-muted">No bank information found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- ================================
                         EMPLOYEES
                    ===================================== -->
                    <div class="tab-pane fade" id="employees">
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
                                                        <td>{{ $emp->full_name }}</td>
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
                    <div class="tab-pane fade" id="settingsItem">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Settings</h4>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-semibold mb-3">Change Password</h5>
                                <form id="changePasswordForm">
                                    <input type="hidden" id="companyId" value="{{ $details->id }}">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">New Password</label>
                                        <input type="password" id="new_password" class="form-control"
                                            placeholder="Enter new password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Confirm Password</label>
                                        <input type="password" id="confirm_password" class="form-control"
                                            placeholder="Confirm new password" required>
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
    </div>

    <script src="{{ asset('js/admin/changePassword.js') }}"></script>
</x-layouts.app>
