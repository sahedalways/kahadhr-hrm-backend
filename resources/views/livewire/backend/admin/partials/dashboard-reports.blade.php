@push('styles')
    <link href="{{ asset('assets/css/superAdmin-dashboard.css') }}"
          rel="stylesheet" />
@endpush


<div class="container-fluid py-4"
     wire:poll.60s>
    <div class="row g-3 mb-4">

        <div class="d-flex"
             id="wrapper">
            <div class="container-fluid py-4 px-4">

                {{-- ================= TOP KPI CARDS ================= --}}
                <div class="row g-3 mb-4">

                    {{-- Total Companies --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Total Companies</small>
                                <i class="bi bi-building text-primary"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0">{{ number_format($totalCompanies) }}</h3>
                            <small class="text-muted">Today: <strong
                                        class="text-dark">+{{ $companiesToday ?? 0 }}</strong></small>
                        </div>
                    </div>

                    {{-- Total Employees --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Total Employees</small>
                                <i class="bi bi-people text-info"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0">{{ number_format($totalEmployees) }}</h3>
                            <small class="text-muted">Global headcount</small>
                        </div>
                    </div>

                    {{-- Expired Subscriptions --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white h-100 border-start border-4 border-danger">
                            <div class="d-flex justify-content-between">
                                <small class="text-danger text-uppercase fw-bold">Expired Subs</small>
                                <i class="bi bi-exclamation-octagon text-danger"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0 text-danger">{{ $expiredCompanies }}</h3>
                            <small class="text-muted">Requires immediate action</small>
                        </div>
                    </div>

                    {{-- Active Servers / System Health --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white h-100 border-start border-4 border-success">
                            <div class="d-flex justify-content-between">
                                <small class="text-success text-uppercase fw-bold">Active Servers</small>
                                <i class="bi bi-hdd-network text-success"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0 text-success">{{ $activeServers }}</h3>
                            <small class="text-muted">● Active Client Instances</small>
                        </div>
                    </div>

                </div>

                {{-- ================= REVENUE SECTION ================= --}}
                <div class="row g-3 mb-4">

                    {{-- Revenue Today --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Revenue Today</small>
                                <i class="bi bi-calendar-day text-success"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0">£{{ number_format($todayRevenue, 0) }}</h3>
                            <small class="text-muted">Last 24 hours</small>
                        </div>
                    </div>

                    {{-- Revenue This Month --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Revenue (MTD)</small>
                                <i class="bi bi-calendar-month text-primary"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0">£{{ number_format($monthlyRevenue, 0) }}</h3>
                            <small class="text-muted">This month</small>
                        </div>
                    </div>

                    {{-- Revenue This Year --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Revenue (YTD)</small>
                                <i class="bi bi-calendar-range text-info"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0">£{{ number_format($yearlyRevenue, 0) }}</h3>
                            <small class="text-muted">This year</small>
                        </div>
                    </div>

                    {{-- Lifetime Revenue --}}
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white h-100 border-start border-4 border-success">
                            <div class="d-flex justify-content-between">
                                <small class="text-success text-uppercase fw-bold">Lifetime Revenue</small>
                                <i class="bi bi-trophy-fill text-success"></i>
                            </div>
                            <h3 class="fw-bold mt-2 mb-0 text-success">
                                £{{ number_format($lifetimeRevenue, 0) }}
                            </h3>
                            <small class="text-muted">All-time earnings</small>
                        </div>
                    </div>

                </div>

                {{-- ================= CHART + INFRA ================= --}}
                <div class="row g-4 mb-4"
                     wire:ignore>

                    {{-- Chart --}}
                    <div class="col-lg-8"
                         wire:poll.1s="updateHealthData">
                        <div class="card border-0 shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold text-muted mb-0">SYSTEM HEALTH TRENDS</h6>

                            </div>
                            <canvas id="healthChart"
                                    height="280"></canvas>
                        </div>
                    </div>

                    {{-- Infrastructure --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm p-4 h-100">
                            <h6 class="fw-bold text-muted mb-4 text-uppercase">Infrastructure</h6>

                            {{-- Disk --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">Disk Usage</small>
                                    <small class="text-muted">
                                        {{ $diskPercent }}%
                                        ({{ $diskUsedTB }}TB / {{ $diskTotalTB }}TB)
                                    </small>
                                </div>

                                <div class="progress"
                                     style="height:8px;">
                                    <div class="progress-bar bg-warning"
                                         role="progressbar"
                                         style="width: {{ $diskPercent }}%"
                                         aria-valuenow="{{ $diskPercent }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>


                            {{-- RAM --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">RAM Consumption</small>
                                    <small class="text-muted">
                                        {{ $ramPercent }}%
                                        ({{ $usedRam }}GB / {{ $TotalRam }}GB)
                                    </small>
                                </div>

                                <div class="progress"
                                     style="height:8px;">
                                    <div class="progress-bar bg-success"
                                         role="progressbar"
                                         style="width: {{ $ramPercent }}%"
                                         aria-valuenow="{{ $ramPercent }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>


                            {{-- API Latency --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">API Latency</small>
                                    <small class="text-muted">
                                        {{ $latency }} ms
                                    </small>
                                </div>

                                <div class="progress"
                                     style="height:8px;">
                                    <div class="progress-bar bg-info"
                                         role="progressbar"
                                         style="width: {{ min(100, round($latency / 5)) }}%"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>


                            <hr>

                            {{-- Logs --}}
                            <h6 class="small fw-bold mb-3">Live System Logs</h6>
                            @if ($latestBackup)
                                <p class="small text-muted mb-1">🟢 {{ $latestBackup->message }} at
                                    {{ \Carbon\Carbon::parse($latestBackup->created_at)->format('h:i A') }}</p>
                            @else
                                <p class="small text-muted mb-1">🟢 No backup recorded yet</p>
                            @endif

                            @if (!empty($trafficSpike))
                                <p class="small text-muted mb-0">🟡 {{ $trafficSpike }}</p>
                            @endif

                        </div>
                    </div>

                </div>

            </div>
        </div>




    </div>


    <div class="row g-4 mb-4">
        <div class="col-lg-8">

            {{-- Recent Companies --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-muted mb-0 text-uppercase">Recent Registered Companies</h6>
                    <button class="btn btn-sm btn-outline-primary"
                            onclick="window.location='{{ route('super-admin.companies') }}'">
                        View All
                    </button>

                </div>
                <div class="table-responsive px-3 pb-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th>Company</th>
                                <th>Plan / Status</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentCompanies as $company)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <img src="{{ $company->company_logo_url ?? asset('assets/img/default-avatar.png') }}"
                                                     alt="{{ $company->company_name }}"
                                                     class="rounded-circle"
                                                     style="width: 35px; height: 35px; object-fit: cover;">
                                            </div>

                                            <div>
                                                <p class="mb-0 fw-bold small">{{ $company->company_name }}</p>
                                                <small class="text-muted">{{ $company->company_email }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span
                                              class="badge bg-soft-warning text-warning text-uppercase">{{ $company->billing_plan_id ?? 'Trial' }}</span>
                                        <span class="badge bg-soft-success text-success">{{ $company->status }}</span>
                                    </td>
                                    <td>
                                        <p class="mb-0 small fw-bold text-danger">
                                            {{ \Carbon\Carbon::parse($company->subscription_end)->format('M d, Y') }}
                                        </p>
                                        @php
                                            $daysLeft = \Carbon\Carbon::parse($company->subscription_end)
                                                ->startOfDay()
                                                ->diffInDays(\Carbon\Carbon::now()->startOfDay());
                                        @endphp

                                        <small class="text-muted">{{ $daysLeft }} days left</small>


                                    </td>


                                    <td>
                                        <button class="btn btn-sm btn-light border"
                                                style="cursor: pointer;"
                                                onclick="window.location='{{ route('super-admin.company.details.show', $company->id) }}'">
                                            <i class="fa fa-eye"></i>
                                        </button>


                                    </td>



                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Employees --}}
            <div class="card border-0 shadow-sm mt-5">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-muted mb-0 text-uppercase">New Registered Employees (Global)</h6>

                    <button class="btn btn-sm btn-outline-primary"
                            onclick="window.location='{{ route('super-admin.employees') }}'">
                        View All
                    </button>

                </div>
                <div class="table-responsive px-3 pb-3">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="small text-muted border-bottom">
                                <th>Employee Name</th>
                                <th>Assigned Company</th>
                                <th>Join Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentEmployees as $employee)
                                <tr>
                                    <td>
                                        <strong>{{ $employee->f_name }} {{ $employee->l_name }}</strong><br>
                                        <small class="text-muted">{{ $employee->email }}</small>
                                    </td>
                                    <td><span
                                              class="text-primary fw-bold small">{{ $employee->company->company_name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="small">
                                        {{ \Carbon\Carbon::parse($employee->start_date)->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                            {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>

                                        <button class="btn btn-sm btn-light border"
                                                style="cursor: pointer;"
                                                onclick="window.location='{{ route('super-admin.dashboard.employees.details', $employee->id) }}'">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-muted mb-0 text-uppercase">Subscription Condition</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="card border-0 shadow-sm"
                         style="border-radius: 12px; transition: transform 0.2s;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-uppercase fw-bold text-muted mb-1"
                                       style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        Expiring Soon
                                    </p>
                                    <h3 class="fw-bold mb-0 text-dark">{{ $expiringCount }}</h3>
                                    <small class="text-muted">
                                        <i class="fa fa-calendar-alt me-1"></i> Next 7 Days
                                    </small>
                                </div>

                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="fa fa-exclamation-triangle text-light fs-6"></i>


                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top border-light">
                                <p class="small text-muted mb-0">
                                    <strong>{{ $expiringCount }} companies</strong> reaching end of cycle this week.
                                </p>
                            </div>
                        </div>
                    </div>


                    <div class="p-3 border rounded mb-3"
                         style="background-color: #fdf2f2; border-color: #f8d7da !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-danger small">
                                Payment Failed ({{ $failedCount }})
                            </span>
                            <small class="text-muted fw-bold">Critical</small>
                        </div>
                        <p class="small mb-0 text-dark">
                            Recent payment attempts failed for {{ $failedCount }}
                            {{ Str::plural('company', $failedCount) }}.
                        </p>

                    </div>


                    <div class="mt-4">
                        <h6 class="small fw-bold text-muted text-uppercase mb-3">Plan Distribution</h6>

                        @php
                            $colors = [
                                'trial' => 'warning',
                                'active' => 'primary',
                                'expired' => 'danger',
                                'suspended' => 'secondary',
                            ];
                        @endphp

                        @foreach ($planStats as $plan => $percent)
                            <div class="d-flex align-items-center mb-2">
                                <small class="w-25 text-capitalize">{{ $plan }}</small>
                                <div class="progress w-75"
                                     style="height: 6px;">
                                    <div class="progress-bar bg-{{ $colors[$plan] }}"
                                         style="width: {{ $percent }}%"></div>
                                </div>
                                <small class="ms-2">{{ $percent }}%</small>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>


</div>


<script>
    const ctx = document.getElementById('healthChart').getContext('2d');
    const data = {
        labels: @json(array_column($healthData, 'time')),
        datasets: [{
                label: 'Disk Usage (%)',
                data: @json(array_column($healthData, 'disk')),
                borderColor: 'rgb(255, 205, 86)',
                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                tension: 0.3
            },
            {
                label: 'RAM Usage (%)',
                data: @json(array_column($healthData, 'ram')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3
            },
            {
                label: 'API Latency (ms)',
                data: @json(array_column($healthData, 'latency')),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.3,
                yAxisID: 'latencyAxis'
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Usage (%)'
                    }
                },
                latencyAxis: {
                    type: 'linear',
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Latency (ms)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    };

    new Chart(ctx, config);
</script>
