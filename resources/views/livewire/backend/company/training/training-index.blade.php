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
                                <th>Assigned</th>
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
                                        <small class="text-muted">{{ Str::limit($training->description, 40) }}</small>
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
                                            {{ $training->expiry_date }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $training->assigned_count }} employees
                                    </td>

                                    <td>
                                        <span class="badge bg-success">
                                            {{ $training->completed_count }} completed
                                        </span>
                                    </td>

                                    <td>
                                        <a data-bs-toggle="modal" data-bs-target="#editTraining"
                                            wire:click="edit({{ $training->id }})" class="badge bg-info text-white">
                                            Edit
                                        </a>

                                        <a href="#" class="badge bg-danger text-white"
                                            wire:click.prevent="$dispatch('confirmDelete', {{ $training->id }})">
                                            Delete
                                        </a>

                                        <a href="{{ route('training.report', $training->id) }}"
                                            class="badge bg-secondary text-white">
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


                <div class="modal-body p-4">


                    <label class="fw-bold mb-2">Select Employees <span class="text-danger">*</span></label>
                    <select class="form-select" wire:model="selectedEmployee" wire:change="addEmployee">
                        <option value="">-- Choose Employee --</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>


                    <div class="mt-3 d-flex flex-wrap gap-2">
                        @foreach ($selectedEmployees as $emp)
                            <span class="badge bg-primary d-flex align-items-center px-3 py-2">
                                {{ $emp['name'] }}
                                <i class="fas fa-times ms-2 cursor-pointer"
                                    wire:click="removeEmployee({{ $emp['id'] }})"></i>
                            </span>
                        @endforeach
                    </div>

                    <hr class="my-4">


                    <label class="fw-bold mb-2">Course Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="course_name"
                        placeholder="Enter course title">

                    <hr class="my-4">


                    <label class="fw-bold mb-2">Training Details (Optional)</label>


                    <div wire:ignore>
                        <div id="editorDetails" style="height: 130px; background: #fff;"></div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-md-4">

                            <label class="fw-bold mb-2">From Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="from_date">
                        </div>

                        <div class="col-md-4">

                            <label class="fw-bold mb-2">To Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" wire:model="to_date">
                        </div>

                        <div class="col-md-4">

                            <label class="fw-bold mb-2">Expiry Date (Optional)</label>
                            <input type="date" class="form-control" wire:model="expiry_date">
                        </div>
                    </div>

                    <hr class="my-4">


                    <label class="fw-bold mb-2">Attach Instruction <span class="text-danger">*</span></label>

                    <div class="mb-2">
                        <select class="form-select" style="width:300px" wire:model.live="content_type"
                            wire:key="content_type">
                            <option value="video">Video</option>
                            <option value="file">File (PDF)</option>
                            <option value="text">Text</option>
                        </select>
                    </div>

                    @if ($content_type === 'text')
                        <div wire:ignore>
                            <div id="editorInstruction"
                                style="height: 130px; background: #fff; display: {{ $content_type === 'text' ? 'block' : 'none' }}">
                            </div>
                        </div>
                    @elseif ($content_type === 'video')
                        <input type="file" class="form-control" wire:model="instruction_file" accept="video/*">
                    @elseif ($content_type === 'file')
                        <input type="file" class="form-control" wire:model="instruction_file"
                            accept="application/pdf">
                    @endif



                    <hr class="my-4">


                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" wire:model="require_proof" id="proofCheck">
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

                </div>


                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" wire:click="saveTraining">Save Training</button>
                </div>

            </div>
        </div>
    </div>


    <!-- Edit Training Modal -->
    {{-- @include('livewire.backend.training.edit') --}}

</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
    // Initialize Quill editors
    const quillDetails = new Quill('#editorDetails', {
        theme: 'snow'
    });
    const quillInstruction = new Quill('#editorInstruction', {
        theme: 'snow'
    });

    quillDetails.root.innerHTML = @this.get('description') || '';
    quillInstruction.root.innerHTML = @this.get('instruction_text') || '';


    quillDetails.on('text-change', function() {
        @this.set('description', quillDetails.root.innerHTML);
    });

    quillInstruction.on('text-change', function() {
        @this.set('instruction_text', quillInstruction.root.innerHTML);
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
