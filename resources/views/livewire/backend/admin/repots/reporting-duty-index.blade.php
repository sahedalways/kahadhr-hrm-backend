@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
          rel="stylesheet">
@endpush

<style>
    [x-cloak] {
        display: none !important;
    }

    .description-cell {
        max-width: 300px;
        word-wrap: break-word;
    }

    .full-desc {
        max-height: 400px;
        overflow-y: auto;
    }

    /* Ensure Quill content displays properly */
    .description-cell p {
        margin-bottom: 0.5rem;
    }

    .description-cell ul,
    .description-cell ol {
        padding-left: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .description-cell strong,
    .description-cell b {
        font-weight: 600;
        color: #333;
    }

    .description-cell a {
        color: #0d6efd;
        text-decoration: none;
    }

    .description-cell a:hover {
        text-decoration: underline;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Fix for truncated text */
    .truncated-desc {
        word-break: break-word;
    }
</style>

@php
    $id = request('id');
@endphp

<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Reporting Duty Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportDuties('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportDuties('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportDuties('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#addDuty"
               wire:click="resetInputFields"
               class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Duty
            </a>
        </div>
    </div>

    <!-- Search + Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                       class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text"
                                       class="form-control border-start-0"
                                       placeholder="Search by title..."
                                       wire:model.live.debounce.300ms="search" />
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-6">
                            <select class="form-select"
                                    wire:model.live="visibility">
                                <option value="">All Visibility</option>
                                <option value="both">Both</option>
                                <option value="company">Company Only</option>
                                <option value="employee">Employee Only</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3 col-6">
                            <select class="form-select"
                                    wire:model.live="sortOrder">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for: <strong>{{ $search ?: 'All Duties' }}</strong>
                        </p>
                        <div wire:loading
                             wire:target="search">
                            <span class="spinner-border spinner-border-sm text-primary"></span>
                            <span class="text-primary small">Searching...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Duty Table -->
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Visibility</th>
                                <th>File</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1; @endphp

                            @forelse($duties as $duty)
                                @php
                                    // Description handling
                                    $plainText = strip_tags($duty->description);
                                    $needsTruncation = strlen($plainText) > 50;

                                    $truncatedPlainText =
                                        mb_substr($plainText, 0, 50) . (strlen($plainText) > 50 ? '...' : '');
                                    $truncatedHtml = nl2br(e($truncatedPlainText));

                                    // Visibility handling
                                    $visibility = strtolower(trim($duty->visibility ?? ''));

                                    $badgeClass = match ($visibility) {
                                        'both' => 'bg-info',
                                        'company' => 'bg-primary',
                                        'employee' => 'bg-success',
                                        default => 'bg-secondary',
                                    };

                                    $visibilityText = match ($visibility) {
                                        'both' => 'Both',
                                        'company' => 'Company Only',
                                        'employee' => 'Employee Only',
                                        default => 'Not Set',
                                    };
                                @endphp

                                <tr>
                                    <td>{{ $i++ }}</td>

                                    <td class="text-start">
                                        <strong>{{ $duty->title }}</strong>
                                    </td>

                                    {{-- Description --}}
                                    <td class="text-start"
                                        style="max-width: 300px;">
                                        <div class="description-cell">

                                            <div id="truncated-{{ $duty->id }}">
                                                {!! $truncatedHtml !!}
                                            </div>

                                            <div id="full-{{ $duty->id }}"
                                                 style="display:none;">
                                                {!! $duty->description !!}
                                            </div>

                                            @if ($needsTruncation)
                                                <a href="javascript:void(0)"
                                                   onclick="toggleDescription({{ $duty->id }})"
                                                   class="text-primary small fw-semibold">
                                                    <span id="toggle-text-{{ $duty->id }}">See More</span>
                                                </a>
                                            @endif

                                        </div>
                                    </td>

                                    {{-- Visibility --}}
                                    <td>
                                        <span class="badge rounded-pill {{ $badgeClass }}">
                                            {{ $visibilityText }}
                                        </span>
                                    </td>

                                    {{-- File --}}
                                    <td>
                                        @if ($duty->file_path)
                                            <a href="{{ asset('storage/' . $duty->file_path) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        @else
                                            <span class="text-muted">No file</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <button type="button"
                                                class="btn btn-sm btn-danger px-3"
                                                wire:click="deleteDuty({{ $duty->id }})"
                                                onclick="return confirm('Are you sure you want to delete this duty?')">
                                            <i class="fa fa-trash me-1"></i> Delete
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center p-5">
                                        <i class="fa fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted mb-0">No reporting duties found</p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>

                    {{-- Load More --}}
                    @if ($hasMore)
                        <div class="text-center mt-4 mb-3">
                            <button wire:click="loadMore"
                                    class="btn btn-outline-primary rounded-pill px-4 py-2">
                                <i class="fa fa-arrow-down me-2"></i> Load More
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Add Duty Modal -->
    <div wire:ignore.self
         class="modal fade"
         id="addDuty"
         data-bs-backdrop="static"
         tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fa fa-plus-circle me-2"></i>Add Reporting Duty
                    </h5>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveDuty">
                    <div class="modal-body p-4">

                        <label class="fw-bold mb-2">Title <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               wire:model="title"
                               placeholder="Enter reporting duty title">

                        @error('title')
                            <div class="text-danger mt-1"
                                 style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">

                        <label class="fw-bold mb-2">Description (Optional)</label>
                        <div wire:ignore>
                            <div id="dutyDescriptionEditor"
                                 style="height: 130px; background: #fff;">{!! $description !!}
                            </div>
                        </div>

                        <hr class="my-4">

                        <label class="fw-bold mb-2">Visibility <span class="text-danger">*</span></label>
                        <select class="form-select"
                                wire:model="visibility">
                            <option value="both">Both</option>
                            <option value="company">Company Only</option>
                            <option value="employee">Employee Only</option>
                        </select>

                        @error('visibility')
                            <div class="text-danger mt-1"
                                 style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">

                        <label class="fw-bold mb-2">Attach Duty File (PDF, Image, or Document) <span
                                  class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control"
                               wire:model="file"
                               accept="application/pdf,image/jpeg,image/png,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">

                        <div class="mt-2 small fst-italic text-secondary">
                            <i class="bi bi-cloud-upload me-1 text-primary"></i> 3 MB max •
                            <i class="bi bi-file-earmark-plus me-1 text-success"></i> 1 file at a time •
                            <i class="bi bi-filetype-pdf me-1 text-purple"></i> PDF, JPG, JPEG, PNG, WebP,
                            HEIC, HEIF, DOC or DOCX formats only
                        </div>

                        @error('file')
                            <span class="text-danger mt-1 d-block"
                                  style="font-size: 0.875rem;">
                                @if (str_contains($message, 'failed to upload'))
                                    <strong>File size is too large!</strong> Maximum allowed is 3 MB.
                                @else
                                    {{ $message }}
                                @endif
                            </span>
                        @enderror

                        <div wire:loading
                             wire:target="file"
                             class="small text-primary mt-1">
                            <i class="fas fa-spinner fa-spin"></i> Uploading...
                        </div>

                        @if ($file)
                            @php
                                $isObject = is_object($file);
                                $extension = $isObject
                                    ? $file->getClientOriginalExtension()
                                    : pathinfo($file, PATHINFO_EXTENSION);
                                $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                $fileName = $isObject ? $file->getClientOriginalName() : basename($file);

                                $shortName = strlen($fileName) > 30 ? substr($fileName, 0, 27) . '...' : $fileName;
                                $isImage = in_array(strtolower($extension), [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'webp',
                                    'heic',
                                    'heif',
                                ]);
                                $isPdf = strtolower($extension) === 'pdf';
                            @endphp

                            <div class="border rounded p-2 position-relative mt-2"
                                 style="width: 180px; text-align:center;">
                                @if ($isImage)
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <img src="{{ $fileUrl }}"
                                             alt="{{ $fileName }}"
                                             style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                        <p class="small mb-0 mt-1">{{ $shortName }}</p>
                                    </a>
                                @elseif ($isPdf)
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        <p class="small mb-0 mt-1">{{ $shortName }}</p>
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <i class="fas fa-file-alt fa-3x text-primary"></i>
                                        <p class="small mb-0 mt-1">{{ $shortName }}</p>
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="modal-footer mt-4">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="saveDuty">
                                <span wire:loading
                                      wire:target="saveDuty">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove
                                      wire:target="saveDuty">Save Duty</span>
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
    // Toggle function for See More/Less
    function toggleDescription(id) {
        const truncatedDiv = document.getElementById(`truncated-${id}`);
        const fullDiv = document.getElementById(`full-${id}`);
        const toggleText = document.getElementById(`toggle-text-${id}`);
        const chevron = document.getElementById(`chevron-${id}`);

        if (truncatedDiv.style.display === 'none') {
            // Switch to truncated view
            truncatedDiv.style.display = 'block';
            fullDiv.style.display = 'none';
            if (toggleText) toggleText.textContent = 'See More';
            if (chevron) {
                chevron.classList.remove('fa-chevron-up');
                chevron.classList.add('fa-chevron-down');
            }
        } else {
            // Switch to full view
            truncatedDiv.style.display = 'none';
            fullDiv.style.display = 'block';
            if (toggleText) toggleText.textContent = 'See Less';
            if (chevron) {
                chevron.classList.remove('fa-chevron-down');
                chevron.classList.add('fa-chevron-up');
            }
        }
    }

    // Initialize Quill editor
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('dutyDescriptionEditor')) {
            const quillDetails = new Quill('#dutyDescriptionEditor', {
                theme: 'snow'
            });

            quillDetails.on('text-change', function() {
                @this.set('description', quillDetails.root.innerHTML);
            });

            Livewire.on('reset-editor', () => {
                if (quillDetails) {
                    quillDetails.root.innerHTML = '';
                }
            });

            Livewire.on('load-description-add', (description) => {
                if (quillDetails && description) {
                    quillDetails.root.innerHTML = description;
                }
            });
        }
    });

    // Re-initialize after Livewire updates
    document.addEventListener('livewire:navigated', function() {
        console.log('Livewire navigation completed');
    });
</script>
