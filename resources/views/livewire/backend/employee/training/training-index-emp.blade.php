@push('styles')
    <link href="{{ asset('assets/css/training.css') }}" rel="stylesheet" />
@endpush


<div>
    <div class="row align-items-center justify-content-between mb-4">


        <div class="col-auto">
            <h5 class="fw-500 m-0">My Trainings</h5>
        </div>


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


    </div>

    <!-- Search + Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control form-control-lg"
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
              <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered m-0 text-center align-middle">
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
                                @php
                                    $assignment = $training->assignments->where('user_id', auth()->id())->first();
                                @endphp

                                @if ($assignment)
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
                                            @elseif($training->content_type == 'file')
                                                <span class="badge bg-info">PDF</span>
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
                                            @if ($assignment->status === 'completed')
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a data-bs-toggle="modal" data-bs-target="#viewReport"
                                                wire:click="viewReport({{ $training->id }})"
                                                class="badge text-white custom-report-badge">
                                                View Details
                                            </a>
                                        </td>

                                        <td>

                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center p-3">No trainings assigned to you</td>
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
    </div>


    <div class="modal fade" id="viewReport" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Training Report: {{ $training->course_name ?? '' }}</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    {{-- Training Details --}}
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-muted">Training Details</h6>
                            <ul class="list-group list-group-flush mt-2">
                                <li class="list-group-item d-flex justify-content-between">
                                    <strong>Status:</strong>
                                   <span class="badge {{ $assignment?->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
    {{ $assignment?->status === 'assigned' ? 'Pending' : ucfirst($assignment?->status ?? 'Pending') }}
</span>


                                </li>
                                <li class="list-group-item d-flex justify-content-between"><strong>Course Name:</strong>
                                    {{ $training->course_name ?? '-' }}</li>
                                <li class="list-group-item d-flex justify-content-between"><strong>From:</strong>
                                    {{ $training->from_date ?? '-' }}</li>
                                <li class="list-group-item d-flex justify-content-between"><strong>To:</strong>
                                    {{ $training->to_date ?? '-' }}</li>
                                <li class="list-group-item d-flex justify-content-between"><strong>Expiry:</strong>
                                    {{ $training->expiry_date ?? '-' }}</li>
                               <li class="list-group-item d-flex justify-content-between">
    <strong>Required Proof:</strong> {{ $training?->required_proof ? 'Yes' : 'No' }}
</li>

                                <li class="list-group-item d-flex justify-content-between"><strong>Content
                                        Type:</strong> {{ ucfirst($training->content_type ?? '-') }}</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Description --}}
                @if ($training?->description)
    <div class="card mb-3 shadow-sm">
        <div class="card-body p-3" style="max-height: 150px; overflow-y:auto;">
            <h6 class="text-muted mb-2">Description</h6>
            {!! $training->description !!}
        </div>
    </div>
@endif



                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                   @php
    $today = \Carbon\Carbon::today();

    $fromDate = $training?->from_date ? \Carbon\Carbon::parse($training->from_date) : null;
    $toDate = $training?->to_date ? \Carbon\Carbon::parse($training->to_date) : null;
@endphp


                    @if ($assignment && $assignment->status === 'assigned' && $today->between($fromDate, $toDate))
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#trainingContentModal">
                            <i class="fas fa-play me-1"></i> Start Training
                        </button>
                    @endif


                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="trainingContentModal" tabindex="-1" role="dialog"
        aria-labelledby="trainingContentModal" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">


        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Training: {{ $training->course_name ?? '' }}</h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

            @if ($training?->content_type === 'video' && $training?->file_path)
    <div class="mb-3 shadow-sm rounded">
        <video id="trainingVideo" class="w-100 rounded" controls>
            <source src="{{ asset('storage/' . $training->file_path) }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
@elseif ($training?->content_type === 'file' && $training?->file_path)
    <div id="pdfContainer" class="mb-3 shadow-sm rounded" style="height:600px; overflow-y:auto;">
        <iframe src="{{ asset('storage/' . $training->file_path) }}"
            class="w-100 h-100 rounded"></iframe>
    </div>
@endif


                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    @if ($assignment && $assignment->status !== 'completed')
                        <div id="markCompletedBtn" style="display: none;">
                            <button wire:click="markCompleted" wire:loading.attr="disabled" class="btn btn-success">


                                <span wire:loading wire:target="markCompleted"
                                    class="spinner-border spinner-border-sm me-2"></span>

                                <i class="fas fa-check-circle me-1"></i> Mark as Completed
                            </button>
                        </div>
                    @endif


                </div>

            </div>
        </div>
    </div>



    <div wire:ignore.self class="modal fade" id="proofModal" tabindex="-1" role="dialog"
        aria-labelledby="proofModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title text-white" id="proofModalLabel">
    Upload Proof for {{ $training?->course_name ?? 'Training' }}
</h5>

                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Upload Proof File <span class="text-danger">*</span></label>
                    <input type="file" wire:model="proofFile" class="form-control" accept=".pdf, image/*">

                    @error('proofFile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-footer">
                    <button wire:click="submitProof" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submitProof"
                            class="spinner-border spinner-border-sm me-2"></span>
                        Submit Proof
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var trainingModal = document.getElementById('trainingContentModal');
            const markButton = document.getElementById('markCompletedBtn');

            trainingModal.addEventListener('shown.bs.modal', function() {

                const video = document.getElementById('trainingVideo');
                if (video) {
                    video.addEventListener('ended', () => {
                        if (markButton) markButton.style.display = 'block';
                    });
                }

                const pdfContainer = document.getElementById('pdfContainer');
                if (pdfContainer) {
                    pdfContainer.addEventListener('scroll', function() {
                        const scrollTop = pdfContainer.scrollTop;
                        const scrollHeight = pdfContainer.scrollHeight - pdfContainer.clientHeight;

                        if (scrollTop >= scrollHeight - 5) {
                            if (markButton) markButton.style.display = 'block';
                        }
                    });
                }

            });

        });
    </script>


    <script>
        window.addEventListener('showProofModal', event => {
            const proofModal = new bootstrap.Modal(document.getElementById('proofModal'));
            proofModal.show();
        });
    </script>
