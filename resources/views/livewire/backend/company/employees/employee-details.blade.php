<x-layouts.app>
    <div class="container-fluid py-4">
        <div class="row g-4">
            <!-- Back to Employees List -->
            <a class="list-group-item list-group-item-action py-3 fw-semibold"
                href="{{ route('company.dashboard.employees') }}">
                <i class="bi bi-arrow-left me-2"></i> Back to Employees
            </a>

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
                                href="#employment">
                                <i class="bi bi-briefcase me-2"></i> Employment Info
                            </a>

                            <a class="list-group-item list-group-item-action py-3 fw-semibold" data-bs-toggle="tab"
                                href="#settings">
                                <i class="bi bi-gear me-2"></i> Settings
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
                                        <img src="{{ $details->profile_photo_url ?? asset('assets/default-user.jpg') }}"
                                            class="img-fluid rounded-3 shadow-sm mb-3"
                                            style="max-height: 160px; object-fit: cover;">
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
                                        <p class="mb-1"><strong>Email:</strong> {{ $details->email }}</p>
                                        <p class="mb-1"><strong>Phone:</strong> {{ $details->phone_no ?? 'N/A' }}</p>
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
                                <p class="mb-1"><strong>Contract Hours:</strong>
                                    {{ $details->contract_hours ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Start Date:</strong>
                                    {{ $details->start_date?->format('d M Y') ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>End Date:</strong>
                                    {{ $details->end_date?->format('d M Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="tab-pane fade" id="settings">
                        <div class="card border-0 shadow-sm rounded-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h4 class="mb-0 fw-bold">Settings</h4>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-semibold mb-3">Change Password</h5>
                                <form id="changePasswordForm">
                                    <input type="hidden" id="employeeId" value="{{ $details->id }}">
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
