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
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Total Companies</small>
                                <i class="bi bi-building text-primary"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">1,240</h3>
                            <small class="text-success small"><i class="bi bi-arrow-up"></i> 12%</small>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Total Employees</small>
                                <i class="bi bi-people text-info"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">15,600</h3>
                            <small class="text-muted small">Across all units</small>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Revenue (MTD)</small>
                                <i class="bi bi-currency-dollar text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">$45,200</h3>
                            <small class="text-primary small">Target: $50k</small>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div
                             class="stat-card p-3 shadow-sm bg-white border-0 h-100 border-start border-danger border-4">
                            <div class="d-flex justify-content-between">
                                <small class="text-danger text-uppercase fw-bold">Expired Subs</small>
                                <i class="bi bi-exclamation-octagon text-danger"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">12</h3>
                            <small class="text-danger small fw-bold">Needs Attention</small>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Active Servers</small>
                                <i class="bi bi-hdd-network text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">08</h3>
                            <small class="text-success small">● All Healthy</small>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 col-sm-6">
                        <div class="stat-card p-3 shadow-sm bg-white border-0 h-100">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted text-uppercase fw-bold">Pending Inquiries</small>
                                <i class="bi bi-chat-dots text-warning"></i>
                            </div>
                            <h3 class="fw-bold mb-0 mt-1">24</h3>
                            <small class="text-warning small">8 Urgent</small>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-bold text-muted mb-0">SYSTEM HEALTH TRENDS</h6>
                                <select class="form-select form-select-sm w-25">
                                    <option>Last 24 Hours</option>
                                    <option>Last 7 Days</option>
                                </select>
                            </div>
                            <canvas id="healthChart"
                                    height="280"></canvas>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm p-4 h-100">
                            <h6 class="fw-bold text-muted mb-4 text-uppercase">Infrastructure</h6>

                            <div class="storage-item mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">Disk Usage</small>
                                    <small class="text-muted">75% (1.5TB / 2TB)</small>
                                </div>
                                <div class="progress"
                                     style="height: 8px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: 75%"></div>
                                </div>
                            </div>

                            <div class="storage-item mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">RAM Consumption</small>
                                    <small class="text-muted">42% (26GB / 64GB)</small>
                                </div>
                                <div class="progress"
                                     style="height: 8px;">
                                    <div class="progress-bar bg-success"
                                         style="width: 42%"></div>
                                </div>
                            </div>

                            <div class="storage-item mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="fw-bold">API Latency</small>
                                    <small class="text-muted">120ms</small>
                                </div>
                                <div class="progress"
                                     style="height: 8px;">
                                    <div class="progress-bar bg-info"
                                         style="width: 25%"></div>
                                </div>
                            </div>

                            <hr>
                            <div class="system-logs mt-auto">
                                <h6 class="small fw-bold mb-3">Live System Logs</h6>
                                <div class="d-flex mb-2">
                                    <div class="bg-success rounded-circle mt-1 me-2"
                                         style="width: 8px; height: 8px;"></div>
                                    <p class="small text-muted mb-0">Backup completed successfully at 02:00 AM</p>
                                </div>
                                <div class="d-flex mb-2">
                                    <div class="bg-warning rounded-circle mt-1 me-2"
                                         style="width: 8px; height: 8px;"></div>
                                    <p class="small text-muted mb-0">Traffic spike detected from UK-Region-1</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>


    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-muted mb-0 text-uppercase">Recent Registered Companies</h6>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="table-responsive px-3 pb-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th>Company</th>
                                <th>Domain</th>
                                <th>Plan / Status</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary-soft text-primary rounded me-2 d-flex align-items-center justify-content-center"
                                             style="width: 35px; height: 35px; background: #eef2ff;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold small">XYZ IT Solutions Ltd.</p>
                                            <small class="text-muted">company@company.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">xyz.hr.com</span></td>
                                <td>
                                    <span class="badge bg-soft-warning text-warning text-uppercase">Trial</span>
                                    <span class="badge bg-soft-success text-success">Active</span>
                                </td>
                                <td>
                                    <p class="mb-0 small fw-bold text-danger">Feb 16, 2026</p>
                                    <small class="text-muted">14 days left</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info-soft text-info rounded me-2 d-flex align-items-center justify-content-center"
                                             style="width: 35px; height: 35px; background: #e0f2fe;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold small">ABC Tech Solutions Ltd.</p>
                                            <small class="text-muted">abc@company.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">abc.hr.com</span></td>
                                <td>
                                    <span class="badge bg-soft-warning text-warning text-uppercase">Trial</span>
                                    <span class="badge bg-soft-success text-success">Active</span>
                                </td>
                                <td>
                                    <p class="mb-0 small fw-bold text-danger">Feb 16, 2026</p>
                                    <small class="text-muted">14 days left</small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-5">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold text-muted mb-0 text-uppercase">New Registered Employees (Global)</h6>
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
                            <tr>
                                <td><strong>John Doe</strong><br><small class="text-muted">john@example.com</small>
                                </td>
                                <td><span class="text-primary fw-bold small">XYZ IT Solutions</span></td>
                                <td class="small">Feb 02, 2026</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td><i class="bi bi-three-dots"></i></td>
                            </tr>
                            <tr>
                                <td><strong>Sarah Smith</strong><br><small class="text-muted">sarah@abc.com</small>
                                </td>
                                <td><span class="text-primary fw-bold small">ABC Tech Solutions</span></td>
                                <td class="small">Feb 01, 2026</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td><i class="bi bi-three-dots"></i></td>
                            </tr>
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
                    <div class="p-3 border rounded mb-3 bg-light-warning"
                         style="background-color: #fff9eb; border-color: #ffeeba !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-warning text-dark small">Expiring Soon (5)</span>
                            <small class="text-muted fw-bold">Next 7 Days</small>
                        </div>
                        <p class="small mb-0 text-dark">5 companies will end their trial/subscription this week.</p>
                        <a href="#"
                           class="small text-warning fw-bold text-decoration-none mt-2 d-block">View Details →</a>
                    </div>

                    <div class="p-3 border rounded mb-3"
                         style="background-color: #fdf2f2; border-color: #f8d7da !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-danger small">Payment Failed (2)</span>
                            <small class="text-muted fw-bold">Critical</small>
                        </div>
                        <p class="small mb-0 text-dark">Recent payment attempts failed for 2 companies.</p>
                        <a href="#"
                           class="small text-danger fw-bold text-decoration-none mt-2 d-block">Check Billing →</a>
                    </div>

                    <div class="mt-4">
                        <h6 class="small fw-bold text-muted text-uppercase mb-3">Plan Distribution</h6>
                        <div class="d-flex align-items-center mb-2">
                            <small class="w-25">Trial</small>
                            <div class="progress w-75"
                                 style="height: 6px;">
                                <div class="progress-bar bg-warning"
                                     style="width: 60%"></div>
                            </div>
                            <small class="ms-2">60%</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <small class="w-25">Standard</small>
                            <div class="progress w-75"
                                 style="height: 6px;">
                                <div class="progress-bar bg-primary"
                                     style="width: 30%"></div>
                            </div>
                            <small class="ms-2">30%</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <small class="w-25">Business</small>
                            <div class="progress w-75"
                                 style="height: 6px;">
                                <div class="progress-bar bg-success"
                                     style="width: 10%"></div>
                            </div>
                            <small class="ms-2">10%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
