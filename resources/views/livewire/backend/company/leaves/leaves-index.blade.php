@push('styles')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
@endpush

<div> {{-- Root element for Livewire --}}

    <div class="container-fluid my-4">
        <div class="row g-3">

            <!-- Left Column: Employee List + Search -->
            <div class="col-lg-3">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Employees</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search Bar -->
                        <input type="text" class="form-control mb-3" placeholder="Search employees">

                        <!-- Employee List -->
                        <ul class="list-group">
                            <li class="list-group-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" class="rounded-circle me-2"
                                    alt="Employee Photo">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">John Doe</div>
                                    <small class="text-muted">Marketing</small>
                                </div>
                            </li>

                            <li class="list-group-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" class="rounded-circle me-2"
                                    alt="Employee Photo">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Jane Smith</div>
                                    <small class="text-muted">HR</small>
                                </div>
                            </li>

                            <li class="list-group-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/40" class="rounded-circle me-2"
                                    alt="Employee Photo">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">Michael Lee</div>
                                    <small class="text-muted">Finance</small>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Middle Column: Calendar -->
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Leaves Calendar</h5>
                    </div>
                    <div class="card-body">
                        <div id="calendar" style="min-height: 500px;"></div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Leave Requests</h5>
                    </div>
                    <div class="card-body">

                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2"
                                        alt="John Doe">
                                    <div>
                                        <div class="fw-bold">John Doe</div>
                                        <small class="text-muted">Marketing</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill">3 days</span>
                            </li>

                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2"
                                        alt="Jane Smith">
                                    <div>
                                        <div class="fw-bold">Jane Smith</div>
                                        <small class="text-muted">HR</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill">1 day</span>
                            </li>
                        </ul>

                        <h5 class="fw-bold mb-3">Manual Entry</h5>
                        <form>
                            <div class="mb-2">
                                <label for="employeeSelect" class="form-label">Select Employee</label>
                                <select class="form-select" id="employeeSelect">
                                    <option value="">-- Select Employee --</option>
                                    <option value="1">John Doe - Marketing</option>
                                    <option value="2">Jane Smith - HR</option>
                                    <option value="3">Michael Lee - Finance</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="leaveType" class="form-label">Leave Type</label>
                                <select class="form-select" id="leaveType">
                                    <option value="">-- Select Leave Type --</option>
                                    <option value="vacation">Vacation</option>
                                    <option value="sick">Sick Leave</option>
                                    <option value="personal">Personal Day</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="fromDate" class="form-label">From</label>
                                <input type="date" class="form-control" id="fromDate" min="{{ date('Y-m-d') }}">
                            </div>

                            <div class="mb-2">
                                <label for="toDate" class="form-label">To</label>
                                <input type="date" class="form-control" id="toDate" min="{{ date('Y-m-d') }}">
                            </div>


                            <button type="submit" class="btn btn-success w-100 mt-2">
                                <i class="fas fa-plus"></i> Add Leave
                            </button>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>

</div>


<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script src="{{ asset('js/company/manage-leave.js') }}"></script>
