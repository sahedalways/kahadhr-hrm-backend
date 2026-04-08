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
            <h5 class="fw-500 text-primary m-0">Company Policy Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportPolicies('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportPolicies('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportPolicies('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#addPolicy"
               wire:click="resetInputFields"
               class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Policy
            </a>
        </div>
    </div>

    <!-- Search + Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                       class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text"
                                       class="form-control border-start-0"
                                       placeholder="Search by title or type..."
                                       wire:model="search"
                                       wire:keyup="set('search', $event.target.value)" />
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12 d-flex gap-2">
                            <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for: <strong>{{ $search ?: 'All Policies' }}</strong>
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

    <!-- Policy Table -->
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

                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($policies as $policy)
                                <tr class="{{ $id == $policy->id ? 'table-primary' : '' }}">
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        <strong>{{ $policy->title }}</strong>
                                    </td>


                                    <td>
                                        {!! Str::limit($policy->description, 100) !!}
                                    </td>


                                    <td>
                                        <a data-bs-toggle="modal"
                                           data-bs-target="#editPolicy"
                                           wire:click="edit({{ $policy->id }})"
                                           class="badge bg-info text-white cursor-pointer">
                                            Edit
                                        </a>

                                        <a href="#"
                                           class="badge bg-danger text-white cursor-pointer"
                                           wire:click.prevent="$dispatch('confirmDelete', {{ $policy->id }})">
                                            Delete
                                        </a>

                                        @if ($policy->file_path)
                                            <a href="{{ asset('storage/' . $policy->file_path) }}"
                                               target="_blank"
                                               class="badge bg-primary text-white cursor-pointer">
                                                View File
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="text-center p-3">No policies found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($hasMore)
                        <div class="text-center mt-4 mb-3">
                            <button wire:click="loadMore"
                                    class="btn btn-outline-primary rounded-pill px-4 py-2">
                                Load More
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add Policy Modal -->
    <div wire:ignore.self
         class="modal fade"
         id="addPolicy"
         data-bs-backdrop="static"
         tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Policy</h5>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="savePolicy">
                    <div class="modal-body p-4">

                        <label class="fw-bold mb-2">Title <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               wire:model="title"
                               placeholder="Enter policy title">

                        @error('title')
                            <div class="text-danger mt-1"
                                 style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">

                        <label class="fw-bold mb-2">Description (Optional)</label>
                        <div wire:ignore>
                            <div id="policyDescriptionEditor"
                                 style="height: 130px; background: #fff;">{!! $description !!}
                            </div>
                        </div>





                        <hr class="my-4">

                        <label class="fw-bold mb-2">Attach Policy File (PDF or Image) <span
                                  class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control"
                               wire:model="file"
                               accept="application/pdf,image/jpeg,image/png">

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
                            @endphp

                            <div class="border rounded p-2 position-relative mt-2"
                                 style="width: 180px; text-align:center;">
                                @if (strtolower($extension) === 'pdf')
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        <p class="small mb-0">{{ $shortName }}</p>
                                    </a>
                                @else
                                    <p class="small mb-0">{{ $shortName }}</p>
                                @endif


                            </div>
                        @endif


                        <div class="form-check mb-1 mt-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   wire:model.live="send_email"
                                   id="emailCheck">

                            <label class="form-check-label fw-bold"
                                   for="emailCheck">
                                Send employees an email notification
                            </label>
                        </div>


                        @if ($emailGatewayMissing)
                            <div class="small text-danger mt-1">
                                ⚠️ Email notification is not configured yet.
                                Please set up your email gateway from
                                <a href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}"
                                   class="text-decoration-underline fw-semibold">
                                    Settings → Mail Settings
                                </a>
                            </div>
                        @endif

                        <div class="modal-footer">
                            <button class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancel</button>
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="savePolicy">
                                <span wire:loading
                                      wire:target="savePolicy"><i class="fas fa-spinner fa-spin me-2"></i>
                                    Saving...</span>
                                <span wire:loading.remove
                                      wire:target="savePolicy">Save Policy</span>
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Edit Policy Modal (Similar to Add) -->
    <div wire:ignore.self
         class="modal fade"
         id="editPolicy"
         data-bs-backdrop="static"
         tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Policy</h5>
                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="updatePolicy">
                    <div class="modal-body p-4">
                        <label class="fw-bold mb-2">Title <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               wire:model="title"
                               placeholder="Enter policy title">

                        @error('title')
                            <div class="text-danger mt-1"
                                 style="font-size: 0.875rem;">{{ $message }}</div>
                        @enderror

                        <hr class="my-4">

                        <label class="fw-bold mb-2">Description (Optional)</label>
                        <div wire:ignore>
                            <div id="policyDescriptionEditor2"
                                 style="height: 130px; background: #fff;">{!! $description !!}
                            </div>
                        </div>





                        <hr class="my-4">

                        <label class="fw-bold mb-2">Attach Policy File (PDF or Image) <span
                                  class="text-danger">*</span></label>
                        <input type="file"
                               class="form-control"
                               wire:model="file"
                               accept="application/pdf,image/jpeg,image/png">

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
                                $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                $fileName = $isObject ? $file->getClientOriginalName() : basename($file);
                                $fileExtension = $isObject
                                    ? $file->getClientOriginalExtension()
                                    : pathinfo($fileName, PATHINFO_EXTENSION);
                                $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png']);
                            @endphp

                            <div class="border rounded p-2 mt-2"
                                 style="width:180px; text-align:center;">
                                @if ($isImage)
                                    {{-- Display image preview with link --}}
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <img src="{{ $fileUrl }}"
                                             alt="{{ $fileName }}"
                                             style="max-width: 100%; max-height: 100px; object-fit: contain;">
                                        <p class="small mb-0 mt-1">{{ $fileName }}</p>
                                    </a>
                                @else
                                    {{-- Display PDF with link --}}
                                    <a href="{{ $fileUrl }}"
                                       target="_blank"
                                       class="d-block text-decoration-none">
                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                        <p class="small mb-0 mt-1">{{ $fileName }}</p>
                                    </a>
                                @endif
                            </div>
                        @endif


                        <div class="form-check mb-1 mt-3">
                            <input class="form-check-input"
                                   type="checkbox"
                                   wire:model.live="send_email"
                                   id="emailCheck">

                            <label class="form-check-label fw-bold"
                                   for="emailCheck">
                                Send employees an email notification
                            </label>
                        </div>


                        @if ($emailGatewayMissing)
                            <div class="small text-danger mt-1">
                                ⚠️ Email notification is not configured yet.
                                Please set up your email gateway from
                                <a href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}"
                                   class="text-decoration-underline fw-semibold">
                                    Settings → Mail Settings
                                </a>
                            </div>
                        @endif

                        <div class="modal-footer">
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="updatePolicy">
                                <span wire:loading
                                      wire:target="updatePolicy"><i class="fas fa-spinner fa-spin me-2"></i>
                                    Saving...</span>
                                <span wire:loading.remove
                                      wire:target="updatePolicy">Save Policy</span>
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

        const quillDetails = new Quill('#policyDescriptionEditor', {
            theme: 'snow'
        });

        const policyDescriptionEditor2 = new Quill('#policyDescriptionEditor2', {
            theme: 'snow'
        });

        quillDetails.on('text-change', function() {
            @this.set('description', quillDetails.root.innerHTML);
        });

        policyDescriptionEditor2.on('text-change', function() {
            @this.set('description', policyDescriptionEditor2.root.innerHTML);
        });

        Livewire.on('reset-editor', () => {
            quillDetails.root.innerHTML = '';
        });

        Livewire.on('load-description-add', (description) => {
            quillDetails.root.innerHTML = description || '';
        });

        Livewire.on('load-description-edit', (data) => {

            const descriptionText = data.description || data || '';
            policyDescriptionEditor2.root.innerHTML = descriptionText;

        });

    });
</script>




<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Are you sure you want to delete this policy?")) {
            Livewire.dispatch('deletePolicy', {
                id: id
            });
        }
    });
</script>
