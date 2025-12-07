@push('styles')
    <link href="{{ asset('assets/css/training.css') }}" rel="stylesheet" />
@endpush



<div>
    <div class="row align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-white m-0">Training Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportTrainings('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportTrainings('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportTrainings('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#addTraining" wire:click="resetInputFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Training
            </a>
        </div>
    </div>

    <!-- Search + Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control shadow-sm form-control-lg border-start-0"
                                    placeholder="Search by course or title..." wire:model="search"
                                    wire:keyup="set('search', $event.target.value)" />
                            </div>
                        </div>

                        <div class="col-md-4 d-flex gap-2">
                            <select class="form-select form-select-lg" wire:change="handleSort($event.target.value)">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for: <strong>{{ $search ?: 'All Trainings' }}</strong>
                        </p>
                        <div wire:loading wire:target="search">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="text-primary small">Searching...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Table -->
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-bordered mt-0 text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Expiry</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($infos as $training)
                                <tr>
                                    <td>{{ $i++ }}</td>

                                    <td>
                                        <strong>{{ $training->course_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {!! Str::limit($training->description, 100) !!}
                                        </small>

                                    </td>

                                    <td>
                                        @if ($training->content_type == 'video')
                                            <span class="badge bg-danger">Video</span>
                                        @elseif($training->content_type == 'pdf')
                                            <span class="badge bg-info">PDF</span>
                                        @elseif($training->content_type == 'image')
                                            <span class="badge bg-warning">Image</span>
                                        @else
                                            <span class="badge bg-secondary">Text</span>
                                        @endif

                                        @if ($training->required_proof)
                                            <br><span class="badge bg-success mt-1">Proof Required</span>
                                        @endif
                                    </td>

                                    <td>
                                        From: <strong>{{ $training->from_date }}</strong><br>
                                        To: <strong>{{ $training->to_date }}</strong>
                                    </td>

                                    <td>
                                        <span
                                            class="{{ now() > $training->expiry_date ? 'text-danger' : 'text-dark' }}">
                                            {{ $training->expiry_date ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $totalAssigned = $training->assignments->count();
                                            $completedCount = $training->assignments
                                                ->where('status', 'completed')
                                                ->count();
                                            $progress = $totalAssigned ? ($completedCount / $totalAssigned) * 100 : 0;
                                        @endphp

                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted small">Completed</span>
                                                <span class="fw-bold small">{{ $completedCount }} /
                                                    {{ $totalAssigned }}</span>
                                            </div>
                                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width: {{ $progress }}%;"
                                                    aria-valuenow="{{ $completedCount }}" aria-valuemin="0"
                                                    aria-valuemax="{{ $totalAssigned }}"></div>
                                            </div>
                                        </div>
                                    </td>



                                    <td>
                                        <a data-bs-toggle="modal" data-bs-target="#editTraining"
                                            wire:click="edit({{ $training->id }})"
                                            class="badge bg-info text-white cursor-pointer">
                                            Edit
                                        </a>

                                        <a href="#" class="badge bg-danger text-white cursor-pointer"
                                            wire:click.prevent="$dispatch('confirmDelete', {{ $training->id }})">
                                            Delete
                                        </a>


                                        <a data-bs-toggle="modal" data-bs-target="#viewReport"
                                            wire:click="viewReport({{ $training->id }})"
                                            class="badge text-white custom-report-badge">
                                            Report
                                        </a>

                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="10" class="text-center p-3">No trainings found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($hasMore)
                        <div class="text-center mt-4 mb-3">
                            <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>




    <div wire:ignore.self class="modal fade" id="addTraining" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addTraining" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Training</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveTraining">
                    <div class="modal-body p-4">


                        <label class="fw-bold mb-2">Select Employees <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="selectedEmployee" wire:change="addEmployee"
                            style="overflow-y:auto; max-height:150px;">
                            <option value="">-- Choose Employee --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>




                        @if (count($selectedEmployees) > 0)
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <span class="fw-bold">Selected :</span>

                                @foreach ($selectedEmployees as $emp)
                                    <span class="badge bg-primary d-flex align-items-center px-3 py-2">
                                        {{ $emp['name'] }}
                                        <i class="fas fa-times ms-2 cursor-pointer"
                                            wire:click="removeEmployee({{ $emp['id'] }})"></i>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @error('selectedEmployees')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror


                        <hr class="my-4">


                        <label class="fw-bold mb-2">Course Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="course_name"
                            placeholder="Enter course title">

                        @error('course_name')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">


                        <label class="fw-bold mb-2">Training Details (Optional)</label>


                        <div wire:ignore>
                            <div id="editorDetails" style="height: 130px; background: #fff;">{!! $description !!}
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-4">

                                <label class="fw-bold mb-2">From Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="from_date">

                                @error('from_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">

                                <label class="fw-bold mb-2">To Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="to_date">

                                @error('to_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">

                                <label class="fw-bold mb-2">Expiry Date (Optional)</label>
                                <input type="date" class="form-control" wire:model="expiry_date">

                                @error('expiry_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">


                        <label class="fw-bold mb-2">Attach Instruction <span class="text-danger">*</span></label>

                        <div class="mb-2">
                            <select class="form-select" style="width:300px" wire:model.live="content_type"
                                wire:key="content_type">
                                <option value="video">Video</option>
                                <option value="file">File (PDF)</option>
                            </select>

                            @error('content_type')
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($content_type === 'video')
                            <input type="file" class="form-control" wire:model="instruction_file"
                                accept="video/*">
                        @elseif ($content_type === 'file')
                            <input type="file" class="form-control" wire:model="instruction_file"
                                accept="application/pdf">
                        @endif

                        @error('instruction_file')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">


                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="require_proof"
                                id="proofCheck">
                            <label class="form-check-label fw-bold" for="proofCheck">
                                Require proof of completion
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="send_email" id="emailCheck">
                            <label class="form-check-label fw-bold" for="emailCheck">
                                Send employee an email notification
                            </label>
                        </div>


                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="saveTraining">
                                <span wire:loading wire:target="saveTraining">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="saveTraining">Save Training</span>
                            </button>

                        </div>

                    </div>
                </form>



            </div>
        </div>
    </div>



    <div wire:ignore.self class="modal fade" id="editTraining" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="editTraining" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Training</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="updateTraining">
                    <div class="modal-body p-4">


                        <label class="fw-bold mb-2">Select Employees <span class="text-danger">*</span></label>
                        <select class="form-select" wire:model="selectedEmployee" wire:change="addEmployee"
                            style="overflow-y:auto; max-height:150px;">
                            <option value="">-- Choose Employee --</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>




                        @if (count($selectedEmployees) > 0)
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <span class="fw-bold">Selected :</span>

                                @foreach ($selectedEmployees as $emp)
                                    <span class="badge bg-primary d-flex align-items-center px-3 py-2">
                                        {{ $emp['name'] }}
                                        <i class="fas fa-times ms-2 cursor-pointer"
                                            wire:click="removeEmployee({{ $emp['id'] }})"></i>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        @error('selectedEmployees')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror


                        <hr class="my-4">


                        <label class="fw-bold mb-2">Course Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model="course_name"
                            placeholder="Enter course title">

                        @error('course_name')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">


                        <label class="fw-bold mb-2">Training Details (Optional)</label>


                        <div wire:ignore>
                            <div id="editorDetails2" style="height: 130px; background: #fff;">{!! $description !!}
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-4">

                                <label class="fw-bold mb-2">From Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="from_date">

                                @error('from_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">

                                <label class="fw-bold mb-2">To Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" wire:model="to_date">

                                @error('to_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">

                                <label class="fw-bold mb-2">Expiry Date (Optional)</label>
                                <input type="date" class="form-control" wire:model="expiry_date">

                                @error('expiry_date')
                                    <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">




                        <div class="mb-2">
                            <label class="fw-bold mb-1">Attach Instruction <span class="text-danger">*</span></label>
                            <select class="form-select" style="width:300px" wire:model.live="content_type"
                                wire:key="content_type">
                                <option value="video">Video</option>
                                <option value="file">File (PDF)</option>
                            </select>

                            @error('content_type')
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Existing File / Video Preview --}}
                        @if ($training && $training->file_path)
                            <div class="mb-3 mt-2 p-3 border rounded bg-light">
                                <span class="fw-bold">Existing File:</span>
                                @if ($content_type === 'video')
                                    <video width="100%" height="200" controls class="mt-2 rounded">
                                        <source src="{{ asset('storage/' . $training->file_path) }}"
                                            type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @elseif($content_type === 'file')
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        @php
                                            $extension = pathinfo($training->file_path, PATHINFO_EXTENSION);
                                        @endphp

                                        @if (in_array($extension, ['mp4', 'mov', 'avi', 'wmv']))
                                            <i class="fas fa-video text-primary fs-4"></i>
                                        @elseif($extension === 'pdf')
                                            <i class="fas fa-file-pdf text-danger fs-4"></i>
                                        @else
                                            <i class="fas fa-file text-secondary fs-4"></i>
                                        @endif

                                        <a href="{{ asset('storage/' . $training->file_path) }}" target="_blank"
                                            class="fw-bold text-decoration-none">
                                            View {{ strtoupper($extension) }}
                                        </a>
                                    </div>

                                @endif
                            </div>
                        @endif

                        {{-- Upload New File --}}
                        @if ($content_type === 'video')
                            <input type="file" class="form-control" wire:model="instruction_file"
                                accept="video/*">
                        @elseif ($content_type === 'file')
                            <input type="file" class="form-control" wire:model="instruction_file"
                                accept="application/pdf">
                        @endif

                        @error('instruction_file')
                            <div class="text-danger mt-1" style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">


                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" wire:model="require_proof"
                                id="proofCheck">
                            <label class="form-check-label fw-bold" for="proofCheck">
                                Require proof of completion
                            </label>
                        </div>




                        <div class="modal-footer">


                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="updateTraining">
                                <span wire:loading wire:target="updateTraining">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="updateTraining">Save Training</span>
                            </button>

                        </div>

                    </div>
                </form>



            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="viewReport" tabindex="-1" aria-labelledby="viewReportLabel" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="viewReportLabel">
                        Training Report: {{ $training->course_name ?? '' }}
                    </h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Training Details -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Training Details</h6>
                        <ul class="list-group list-group-flush rounded shadow-sm">
                            <li class="list-group-item d-flex justify-content-between"><strong>Course Name:</strong>
                                {{ $training->course_name ?? '-' }}</li>
                            <li class="list-group-item d-flex justify-content-between"><strong>From Date:</strong>
                                {{ $training->from_date ?? '-' }}</li>
                            <li class="list-group-item d-flex justify-content-between"><strong>To Date:</strong>
                                {{ $training->to_date ?? '-' }}</li>
                            <li class="list-group-item d-flex justify-content-between"><strong>Expiry Date:</strong>
                                {{ $training->expiry_date ?? '-' }}</li>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong>Required Proof:</strong>
                                {{ $training ? ($training->required_proof ? 'Yes' : 'No') : 'N/A' }}
                            </li>

                            <li class="list-group-item d-flex justify-content-between"><strong>Content Type:</strong>
                                {{ ucfirst($training->content_type ?? '-') }}</li>
                        </ul>
                    </div>

                    @if ($training && $training->description)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Training Description</h6>
                            <div class="p-3 bg-light rounded shadow-sm" style="max-height: 150px; overflow-y:auto;">
                                {!! $training->description !!}
                            </div>
                        </div>
                    @endif


                    <!-- Summary Boxes -->
                    @php
                        if ($training) {
                            $totalAssigned = $training->assignments->count();
                            $completedCount = $training->assignments->where('status', 'completed')->count();
                            $incompleteCount = $totalAssigned - $completedCount;
                        } else {
                            $totalAssigned = 0;
                            $completedCount = 0;
                            $incompleteCount = 0;
                        }
                    @endphp
                    <div class="row text-center mb-4 g-3">
                        <div class="col-md-4">
                            <div class="p-3 bg-info text-white rounded shadow">
                                <h6 class="mb-1">Assigned</h6>
                                <h4>{{ $totalAssigned }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-success text-white rounded shadow">
                                <h6 class="mb-1">Completed</h6>
                                <h4>{{ $completedCount }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-warning text-white rounded shadow">
                                <h6 class="mb-1">Incomplete</h6>
                                <h4>{{ $incompleteCount }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        @php
                            $progress = $totalAssigned ? ($completedCount / $totalAssigned) * 100 : 0;
                        @endphp
                        <h6 class="text-muted mb-2">Completion Progress</h6>
                        <div class="progress rounded-pill" style="height: 12px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $progress }}%;" aria-valuenow="{{ $completedCount }}"
                                aria-valuemin="0" aria-valuemax="{{ $totalAssigned }}"></div>
                        </div>
                    </div>

                    <!-- Employee List with Status -->
                    <div>
                        <h6 class="text-muted mb-2">Employee Status</h6>

                        <!-- Scrollable table container -->
                        <div
                            style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem;">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Proof</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($training?->assignments ?? [] as $assignment)
                                        <tr>
                                            <td>{{ $assignment->user->full_name ?? '-' }}</td>
                                            <td>{{ $assignment->user->email ?? '-' }}</td>
                                            <td>
                                                @if ($assignment->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-secondary">Assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($assignment->proof_file)
                                                    <a href="{{ asset('storage/' . $assignment->proof_file) }}"
                                                        target="_blank" class="badge bg-info text-white">View</a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $fromDate = \Carbon\Carbon::parse($training->from_date);
                                                    $toDate = \Carbon\Carbon::parse($training->to_date);
                                                    $today = \Carbon\Carbon::today();
                                                @endphp
                                                @if ($assignment->status !== 'completed' && $today->between($fromDate, $toDate))
                                                    <button wire:click="sendReminder({{ $assignment->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="sendReminder({{ $assignment->id }})"
                                                        class="btn btn-sm btn-warning d-flex align-items-center">

                                                        <span wire:loading
                                                            wire:target="sendReminder({{ $assignment->id }})"
                                                            class="spinner-border spinner-border-sm me-2"></span>
                                                        Send Reminder
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>



</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
    const quillDetails = new Quill('#editorDetails', {
        theme: 'snow'
    });

    const quillDetails2 = new Quill('#editorDetails2', {
        theme: 'snow'
    });


    quillDetails.root.innerHTML = @json($description ?? '');
    quillDetails2.root.innerHTML = @json($description2 ?? '');



    quillDetails.on('text-change', function() {
        @this.set('description', quillDetails.root.innerHTML);
    });

    quillDetails2.on('text-change', function() {
        @this.set('description', quillDetails2.root.innerHTML);
    });
</script>



<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Are you sure you want to delete this training?")) {
            Livewire.dispatch('deleteTraining', {
                id: id
            });
        }
    });
</script>
