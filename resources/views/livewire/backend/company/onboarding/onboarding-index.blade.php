@push('styles')
    <link href="{{ asset('assets/css/onboarding.css') }}" rel="stylesheet" />
@endpush

<div>


    <div class="container py-5">

        <div class="card">
            <div class="card-body">
                {{-- 🔍 Search + Sort + Add Section --}}
                <div class="row g-3 align-items-center mb-4">

                    {{-- Search --}}
                    <div class="col-xl-8 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" placeholder="Search by title"
                                wire:model="search" wire:keyup="set('search', $event.target.value)">
                        </div>
                    </div>

                    {{-- Sort + Add --}}
                    <div
                        class="col-xl-4 col-md-6 d-flex justify-content-end gap-3 align-items-center flex-column flex-md-row">


                        <select class="form-select ps-4" wire:change="handleSort($event.target.value)">
                            <option value="desc">Newest First</option>
                            <option value="asc">Oldest First</option>
                        </select>



                        <button data-bs-toggle="modal" data-bs-target="#addAnnouncement" wire:click="resetInputFields"
                            class="btn btn-primary d-flex align-items-center w-100 justify-content-center">
                            <i class="fa fa-plus me-2"></i>
                            <span class="text-nowrap"> Add Onboarding</span>
                        </button>
                    </div>
                </div>


                <div class="d-flex justify-content-between align-items-center mt-2">
                    <p class="text-muted small mb-0">
                        Showing results for: <strong>{{ $search ?: 'All Onboardings' }}</strong>
                    </p>

                    <div wire:loading wire:target="search">
                        <span class="spinner-border spinner-border-sm text-primary"></span>
                        <span class="text-primary small ms-1">Searching...</span>
                    </div>
                </div>
            </div>
        </div>



        <div class="card mt-4">
            <div class="card-body">
                <div class="text-center ">
                    <h1 class="display-5 fw-bold ">📢 Onboarding Announcements</h1>
                    <p class="lead ">All onboarding-related media and instructions are listed below.</p>
                </div>


                <div class="row g-4">

            @forelse ($infos as $item)
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="card onboarding-step shadow-sm w-100 position-relative">

                        {{-- 🔵 Hover View Button --}}
                        <div class="view-overlay">
                            <a href="{{ route('company.dashboard.onboarding.view', [
                                'id' => $item->id,
                                'company' => app('authUser')->company->sub_domain,
                            ]) }}"
                                class="btn btn-primary btn-sm">
                                View
                            </a>
                        </div>


                        {{-- Media --}}
                        <div class="media-container text-center p-3">

                            @php
                                $media = strtolower($item->media ?? '');
                                $isImage = Str::endsWith($media, ['.jpg', '.jpeg', '.png', '.gif', '.webp']);
                                $isVideo = Str::endsWith($media, ['.mp4', '.mov', '.avi']);
                                $isAudio = Str::endsWith($media, ['.mp3', '.wav']);
                            @endphp

                            @if ($item->media && $isImage)
                                <img src="{{ asset('storage/' . $item->media) }}" class="card-img-top rounded"
                                    alt="media">
                            @elseif ($item->media && $isVideo)
                                <video class="w-100 rounded" controls>
                                    <source src="{{ asset('storage/' . $item->media) }}" type="video/mp4">
                                </video>
                            @elseif ($item->media && $isAudio)
                                <audio controls class="w-100">
                                    <source src="{{ asset('storage/' . $item->media) }}">
                                </audio>
                            @else
                                <div class="media-placeholder">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <p class="small text-muted">No media uploaded</p>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $item->title }}</h5>
                            <p class="card-text text-muted">
                                {!! \Illuminate\Support\Str::limit($item->description, 120) !!}
                            </p>
                        </div>

                        <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-secondary">
                                    @if (isset($item->creator) && $item->creator->user_type === 'company')
                                        Company Admin
                                    @else
                                        {{ $item->creator->full_name ?? 'Admin' }}
                                    @endif
                                </span>

                                <small class="text-muted ms-2">
                                    {{ $item->created_at->format('d M Y') }}
                                </small>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" wire:click="edit({{ $item->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                    wire:click.prevent="$dispatch('confirmDelete', {{ $item->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>


                    </div>
                </div>

            @empty
                <div class="col-12 text-center ">
                    <h4>No announcements found.</h4>
                </div>
            @endforelse
        </div>
            </div>
        </div>

        


        {{-- Load More --}}
        @if ($hasMore)
            <div class="text-center mt-4">
                <button class="btn btn-outline-light px-4 py-2" wire:click="loadMore">
                    Load More
                    <span wire:loading wire:target="loadMore" class="spinner-border spinner-border-sm ms-2"></span>
                </button>
            </div>
        @endif

    </div>


    <div wire:ignore.self class="modal fade" id="addAnnouncement" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                {{-- Modal Header --}}
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Add New Announcement</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-2">
                            {{-- Title --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="title">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <div wire:ignore>
                                    <div id="editorDetails" style="height: 130px; background: #fff;">

                                    </div>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Media --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Media (Image / Video / Audio)</label>
                                <input type="file" class="form-control" wire:model="mediaFile"
                                    accept="image/*,video/*,audio/*">
                                @error('mediaFile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div wire:loading wire:target="mediaFile" class="small text-primary mt-1">
                                    <i class="fas fa-spinner fa-spin"></i> Uploading...
                                </div>

                                @if ($mediaFile)
                                    @php
                                        $isObject = is_object($mediaFile);
                                        $extension = $isObject
                                            ? $mediaFile->getClientOriginalExtension()
                                            : pathinfo($mediaFile, PATHINFO_EXTENSION);
                                        $fileUrl = $isObject
                                            ? $mediaFile->temporaryUrl()
                                            : asset('storage/' . $mediaFile);
                                        $fileName = $isObject
                                            ? $mediaFile->getClientOriginalName()
                                            : basename($mediaFile);

                                        $shortName = shortFileName($fileName);

                                    @endphp

                                    <div class="border rounded p-2 position-relative mt-2"
                                        style="width: 180px; text-align:center;">
                                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ $fileUrl }}" class="img-fluid rounded"
                                                style="height: 100px; object-fit: cover;">
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @elseif (strtolower($extension) === 'pdf')
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="d-block text-decoration-none">
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                <p class="small mb-0">{{ $shortName }}</p>
                                            </a>
                                        @elseif (in_array(strtolower($extension), ['mp4', 'mov', 'webm', 'ogg']))
                                            <video width="100%" height="100" controls>
                                                <source src="{{ $fileUrl }}"
                                                    type="video/{{ strtolower($extension) }}">
                                                Your browser does not support the video tag.
                                            </video>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @elseif (in_array(strtolower($extension), ['mp3', 'wav', 'ogg']))
                                            <audio controls class="w-100 mt-1">
                                                <source src="{{ $fileUrl }}"
                                                    type="audio/{{ strtolower($extension) }}">
                                                Your browser does not support the audio element.
                                            </audio>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @else
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>



                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>



                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove wire:target="save">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>





    <div wire:ignore.self class="modal fade" id="editAnnouncement" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Edit Announcement</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" aria-label="close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">
                            {{-- Title --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="title">
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <div wire:ignore>
                                    <div id="editorDetailsEdit" style="height: 130px; background: #fff;">
                                        {!! $description !!}
                                    </div>
                                </div>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Media --}}
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Media (Image / Video / Audio)</label>
                                <input type="file" class="form-control" wire:model="mediaFile"
                                    accept="image/*,video/*,audio/*">
                                @error('mediaFile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <div wire:loading wire:target="mediaFile" class="small text-primary mt-1">
                                    <i class="fas fa-spinner fa-spin"></i> Uploading...
                                </div>



                                @if ($mediaFile)
                                    @php
                                        $isObject = is_object($mediaFile);
                                        $extension = $isObject
                                            ? $mediaFile->getClientOriginalExtension()
                                            : pathinfo($mediaFile, PATHINFO_EXTENSION);
                                        $fileUrl = $isObject
                                            ? $mediaFile->temporaryUrl()
                                            : asset('storage/' . $mediaFile);
                                        $fileName = $isObject
                                            ? $mediaFile->getClientOriginalName()
                                            : basename($mediaFile);

                                        $shortName = shortFileName($fileName);
                                    @endphp

                                    <div class="border rounded p-2 position-relative mt-2"
                                        style="width: 180px; text-align:center;">
                                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                            <img src="{{ $fileUrl }}" class="img-fluid rounded"
                                                style="height: 100px; object-fit: cover;">
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @elseif (strtolower($extension) === 'pdf')
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="d-block text-decoration-none">
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                <p class="small mb-0">{{ $shortName }}</p>
                                            </a>
                                        @elseif (in_array(strtolower($extension), ['mp4', 'mov', 'webm', 'ogg']))
                                            <video width="100%" height="100" controls>
                                                <source src="{{ $fileUrl }}"
                                                    type="video/{{ strtolower($extension) }}">
                                                Your browser does not support the video tag.
                                            </video>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @elseif (in_array(strtolower($extension), ['mp3', 'wav', 'ogg']))
                                            <audio controls class="w-100 mt-1">
                                                <source src="{{ $fileUrl }}"
                                                    type="audio/{{ strtolower($extension) }}">
                                                Your browser does not support the audio element.
                                            </audio>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @else
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-sm" wire:loading.attr="disabled"
                            wire:target="update,media">
                            <span wire:loading wire:target="update"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Updating...</span>
                            <span wire:loading.remove wire:target="update">Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

<script>
    // Initialize Quill Editors
    const quillDetails = new Quill('#editorDetails', {
        theme: 'snow'
    });

    const editorDetailsEdit = new Quill('#editorDetailsEdit', {
        theme: 'snow'
    });


    Livewire.on('load-description-add', data => {
        quillDetails.root.innerHTML = data.description ?? '';
    });


    Livewire.on('load-description-edit', data => {
        editorDetailsEdit.root.innerHTML = data.description ?? '';
    });

    // ---------- SYNC ADD MODAL EDITOR ----------
    quillDetails.on('text-change', function() {
        @this.set('description', quillDetails.root.innerHTML);
    });

    // ---------- SYNC EDIT MODAL EDITOR ----------
    editorDetailsEdit.on('text-change', function() {
        @this.set('description', editorDetailsEdit.root.innerHTML);
    });

    // ---------- RESET ADD MODAL WHEN OPEN ----------
    Livewire.on('reset-editor', () => {
        quillDetails.root.innerHTML = '';
    });

    Livewire.on('load-description-edit', data => {

        if (Array.isArray(data) && data.length > 0) {
            editorDetailsEdit.root.innerHTML = data[0].description ?? '';
        } else {
            editorDetailsEdit.root.innerHTML = '';
        }

        const editModal = new bootstrap.Modal(document.getElementById('editAnnouncement'));
        editModal.show();
    });
</script>



<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Are you sure you want to delete this?")) {
            Livewire.dispatch('deleteAnnouncement', {
                id: id
            });
        }
    });
</script>
