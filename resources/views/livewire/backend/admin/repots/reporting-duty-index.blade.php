@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
          rel="stylesheet">
@endpush

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
                                <option value="both">Both </option>
                                <option value="company">Company Only</option>
                                <option value="employee">Employee Only</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3 col-6">
                            <select class="form-select"
                                    wire:change="handleSort($event.target.value)">
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
                    <table class="table table-bordered mt-0 text-center align-middle">
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
                                <tr class="{{ $id == $duty->id ? 'table-primary' : '' }}">
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        <strong>{{ $duty->title }}</strong>
                                    </td>
                                    <td>
                                        {!! Str::limit($duty->description, 100) !!}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match ($duty->visibility) {
                                                'both' => 'bg-info',
                                                'company' => 'bg-primary',
                                                'employee' => 'bg-success',
                                                default => 'bg-secondary',
                                            };
                                            $visibilityText = match ($duty->visibility) {
                                                'both' => 'Both',
                                                'company' => 'Company Only',
                                                'employee' => 'Employee Only',
                                                default => ucfirst($duty->visibility),
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $visibilityText }}
                                        </span>
                                    </td>
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
                                    <td>
                                        <a href="#"
                                           class="badge bg-danger text-white cursor-pointer"
                                           wire:click.prevent="$dispatch('confirmDelete', {{ $duty->id }})">
                                            <i class="fa fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center p-5">
                                        <i class="fa fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No reporting duties found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($hasMore)
                        <div class="text-center mt-4 mb-3">
                            <button wire:click="loadMore"
                                    class="btn btn-outline-primary rounded-pill px-4 py-2">
                                <i class="fa fa-arrow-down me-2"></i>Load More
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
                            <option value="both">Both </option>
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

                        @error('file')
                            <span class="text-danger mt-1 d-block"
                                  style="font-size: 0.875rem;">

                                @if (str_contains($message, 'failed to upload'))
                                    <strong>File size is too large!</strong> Maximum allowed is 3MB.
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

                                $shortName = shortFileName($fileName);
                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
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
                            <button class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="saveDuty">
                                <span wire:loading
                                      wire:target="saveDuty"><i class="fas fa-spinner fa-spin me-2"></i>
                                    Saving...</span>
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
    document.addEventListener('DOMContentLoaded', function() {

        const quillDetails = new Quill('#dutyDescriptionEditor', {
            theme: 'snow'
        });

        quillDetails.on('text-change', function() {
            @this.set('description', quillDetails.root.innerHTML);
        });

        Livewire.on('reset-editor', () => {
            quillDetails.root.innerHTML = '';
        });

        Livewire.on('load-description-add', (description) => {
            quillDetails.root.innerHTML = description || '';
        });

    });
</script>

<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Are you sure you want to delete this reporting duty?")) {
            Livewire.dispatch('deleteDuty', {
                id: id
            });
        }
    });
</script>
