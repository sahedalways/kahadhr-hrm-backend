@push('styles')
    <link href="{{ asset('assets/css/company-dashboard.css') }}"
          rel="stylesheet" />
@endpush

<div class="container-fluid py-4"
     wire:poll.60s>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-sky">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Today's Absent</small>
                    <i class="fas fa-user-times text-danger"></i>
                </div>
                <h3>{{ $todayAbsent ?? 0 }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-green">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">On Leave Today</small>
                    <i class="fas fa-plane-departure text-success"></i>
                </div>
                <h3>{{ $onLeaveToday ?? 0 }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-pink">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Upcoming Holiday</small>
                    <i class="fas fa-calendar-day text-pink"></i>
                </div>
                <h5 class="mb-0">Summer Bank Holiday</h5>
            </div>
        </div>

        <div class="col-md-3">
            <div class="dashboard-card stat-card stat-orange">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="fw-bold opacity-75">Pending Leave Requests</small>
                    <i class="fas fa-clock text-warning"></i>
                </div>
                <h3>{{ $pendingRequests ?? 0 }}</h3>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <div class="col-lg-8">
            <div class="dashboard-section mb-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="calendar-nav d-flex align-items-center gap-1 bg-light rounded-pill p-1 border">
                        <button class="btn btn-link text-dark p-0 border-0 d-flex align-items-center justify-content-center nav-arrow"
                                style="width: 28px; height: 28px;">
                            <i class="fas fa-chevron-left"
                               style="font-size: 12px;"></i>
                        </button>

                        <span class="fw-bold text-center px-2 mb-0"
                              style="min-width: 130px; font-size: 0.95rem; letter-spacing: -0.2px;">
                            September 2024
                        </span>

                        <button class="btn btn-link text-dark p-0 border-0 d-flex align-items-center justify-content-center nav-arrow"
                                style="width: 28px; height: 28px;">
                            <i class="fas fa-chevron-right"
                               style="font-size: 12px;"></i>
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="badge badge-green px-3 py-2">Leaves (Green)</span>
                        <span class="badge badge-pink px-3 py-2">Birthdays</span>
                        <span class="badge badge-danger px-3 py-2">UK Holidays</span>
                        <span class="badge badge-orange px-3 py-2">Doc Expiry</span>

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered calendar-table m-0">
                        <thead>
                            <tr class="text-center bg-light">
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Sun</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Mon</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Tue</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Wed</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Thu</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Fri</th>
                                <th class="py-3 text-muted fw-normal"
                                    style="width: 14.28%">Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="calendar-day text-muted">31</td>
                                <td class="calendar-day">1</td>
                                <td class="calendar-day">
                                    2 <div class="event-bar badge-green">John Doe - Annual Leave</div>
                                </td>
                                <td class="calendar-day">3</td>
                                <td class="calendar-day">
                                    4 <div class="event-bar badge-pink">Mike Brown's Birthday</div>
                                </td>
                                <td class="calendar-day">5</td>
                                <td class="calendar-day">
                                    6 <div class="event-bar badge-green">John Doe - Annual...</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="calendar-day">
                                    7 <div class="event-bar badge-green">John Doe - Annual Leave</div>
                                </td>
                                <td class="calendar-day">8</td>
                                <td class="calendar-day">9</td>
                                <td class="calendar-day">10</td>
                                <td class="calendar-day">11</td>
                                <td class="calendar-day">
                                    12 <div class="event-bar bg-danger text-white">Bank Holiday - Summer Bank Holiday
                                    </div>
                                </td>
                                <td class="calendar-day">13</td>
                            </tr>
                            <tr>
                                <td class="calendar-day">
                                    14 <div class="event-bar badge-green-light text-success">Sarah Smith - Sick Leave
                                    </div>
                                </td>
                                <td class="calendar-day">15</td>
                                <td class="calendar-day">16</td>
                                <td class="calendar-day">17</td>
                                <td class="calendar-day">18</td>
                                <td class="calendar-day">
                                    19 <div class="event-bar badge-green-light text-success">Sarah Smith - Sick Leave
                                    </div>
                                </td>
                                <td class="calendar-day">20</td>
                            </tr>
                            <tr>
                                <td class="calendar-day">
                                    21 <div class="event-bar badge-green">John Doe - Annual Leave</div>
                                </td>
                                <td class="calendar-day">22</td>
                                <td class="calendar-day">23</td>
                                <td class="calendar-day">
                                    24 <div class="event-bar badge-green">John Doe - Annual Leave</div>
                                </td>
                                <td class="calendar-day">
                                    25 <div class="event-bar badge-orange">Doc Expiry: Visa (A. Khan)</div>
                                </td>
                                <td class="calendar-day">26</td>
                                <td class="calendar-day">27</td>
                            </tr>
                            <tr>
                                <td class="calendar-day text-white bg-danger opacity-75">
                                    28 <div class="event-bar">Bank Holiday - Summer</div>
                                </td>
                                <td class="calendar-day">29</td>
                                <td class="calendar-day">30</td>
                                <td class="calendar-day text-muted">1</td>
                                <td class="calendar-day text-muted">2</td>
                                <td class="calendar-day text-muted">3</td>
                                <td class="calendar-day text-muted">4</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-section">
                <h5 class="fw-bold mb-5">Attendance Anomalies</h5>
                <table class="table align-middle">
                    <tbody>
                        <tr>
                            <td><strong>Chris Evans</strong></td>
                            <td><span class="badge badge-red rounded-pill px-3">Late In - Red</span></td>
                            <td class="fw-bold">9:45 AM</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dashboard-section mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-secondary mb-0">Live Office Status</h6>
                    <select wire:change="handleFilter($event.target.value)"
                            class="form-select form-select-sm"
                            style="width: 90px;"> <!-- adjust this value as needed -->
                        <option value="day"
                                @if ($statusFilter == 'day') selected @endif>Today</option>
                        <option value="month"
                                @if ($statusFilter == 'month') selected @endif>Month</option>
                        <option value="year"
                                @if ($statusFilter == 'year') selected @endif>Year</option>
                    </select>
                </div>



                <div class="d-flex align-items-center">
                    <div style="width: 145px; height: 130px; position: relative;"
                         class="me-4">
                        <canvas id="statusChart"></canvas>
                    </div>

                    <div class="chart-legend">
                        <div class="legend-item">
                            <span class="dot bg-present"></span>
                            <span>Present: <strong>{{ $liveStatus['present'] ?? 0 }}</strong></span>
                        </div>
                        <div class="legend-item">
                            <span class="dot bg-leave"></span>
                            <span>On Leave: <strong>{{ $liveStatus['leave'] ?? 0 }}</strong></span>
                        </div>
                        <div class="legend-item">
                            <span class="dot bg-absent"></span>
                            <span>Absent/Late: <strong>{{ $liveStatus['absent'] ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-section mb-4">
                <h5 class="fw-bold mb-3">Action Center</h5>
                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 py-2 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-bold">Leave Req: David Lee</span>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-success">Approve</button>
                                <button class="btn btn-sm btn-outline-danger">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-section">
                <h5 class="fw-bold mb-3">Documents Expiring Soon (60 Days)</h5>

                @forelse ($expiringDocs as $doc)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-bold">
                                {{ $doc->employee->f_name }} - {{ $doc->documentType->name }}
                            </span>
                            <span class="text-danger fw-bold">
                                {{ \Carbon\Carbon::now()->diffInDays($doc->expires_at) }} days
                            </span>
                        </div>

                        <div class="progress"
                             style="height: 6px;">
                            <div class="progress-bar bg-danger"
                                 style="width: 40%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-shield mb-2 d-block"
                           style="font-size: 28px;"></i>
                        <span class="fw-bold fst-italic">
                            No documents expiring in the next 60 days
                        </span>
                    </div>
                @endforelse
            </div>


            <div class="dashboard-section shadow-sm border-0 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold m-0 text-dark">Recent Employees</h5>
                        <small class="text-muted">Total Employees: <span
                                  class="fw-bold text-primary">{{ $totalEmployees ?? 0 }}</span></small>
                    </div>
                    <a href="{{ route('company.dashboard.employees.index', ['company' => app('authUser')->company->sub_domain]) }}"
                       class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        View All
                    </a>

                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead class="table-light">
                            <tr class="text-muted smaller fw-bold text-uppercase">
                                <th class="border-0 ps-3">Name</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Mobile</th>
                                <th class="border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEmployees as $emp)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-soft-primary text-primary d-flex align-items-center justify-content-center fw-bold me-2"
                                                 style="width: 32px; height: 32px; font-size: 12px;">
                                                {{ strtoupper(substr($emp->f_name, 0, 1) . substr($emp->l_name, 0, 1)) }}
                                            </div>
                                            <span class="fw-bold">{{ $emp->f_name }} {{ $emp->l_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $emp->email }}</td>
                                    <td class="text-muted small">{{ $emp->phone_no ?? 'N/A' }}</td>
                                    <td>
                                        <span
                                              class="badge {{ $emp->is_active ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} rounded-pill px-3">
                                            {{ $emp->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center text-muted py-3">
                                        No employees found
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


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'On Leave', 'Absent'],
            datasets: [{
                data: [40, 5, 5],
                backgroundColor: [
                    '#28a745',
                    '#007bff',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            cutout: '65%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            }
        }
    });
</script>
