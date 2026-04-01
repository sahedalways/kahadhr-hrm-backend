<div>
    <div class="container-fluid px-0">
        {{-- Search Section --}}
        <div class="mb-4">
            <div class="position-relative">
                <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search policies by title or description..."
                       class="form-control form-control-lg ps-5 border-0 shadow-sm rounded-pill" />
            </div>
        </div>

        {{-- Stats Section --}}
        @if ($policies->count() > 0)
            <div class="mb-3 text-end">
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    <i class="fas fa-file-alt me-1"></i> {{ $policies->count() }} Policy(s) Loaded
                </span>
            </div>
        @endif

        {{-- Policies Grid --}}
        <div class="row g-4">
            @forelse ($policies as $policy)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all rounded-3 overflow-hidden">
                        {{-- Card Header with Icon --}}
                        <div class="card-header bg-white border-0 pt-4 pb-0">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center"
                                     style="width:50px; height:50px;">
                                    <i class="fas fa-file-pdf text-white fs-4"></i>
                                </div>

                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body pt-0">
                            <h5 class="card-title text-dark fw-bold mb-3 line-clamp-2">
                                {{ $policy->title }}
                            </h5>

                            <p class="card-text text-muted small mb-3 line-clamp-3">
                                {{ Str::limit(strip_tags($policy->description), 120) }}
                            </p>

                            {{-- File Preview if exists --}}
                            @if ($policy->file_path)
                                <div class="file-preview bg-light rounded p-2 mb-3">
                                    @php
                                        $ext = pathinfo($policy->file_path, PATHINFO_EXTENSION);
                                        $fileUrl = asset('storage/' . $policy->file_path);
                                    @endphp

                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <div class="text-center">
                                            <img src="{{ $fileUrl }}"
                                                 alt="{{ $policy->title }}"
                                                 class="img-fluid rounded cursor-pointer preview-image"
                                                 style="max-height: 150px; object-fit: cover;"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#imageModal"
                                                 data-image="{{ $fileUrl }}">
                                        </div>
                                    @elseif(in_array($ext, ['pdf']))
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-pdf text-danger fs-3 me-2"></i>
                                                <div>
                                                    <small class="text-dark fw-semibold d-block">Policy Document</small>
                                                    <small class="text-muted">{{ strtoupper($ext) }} File</small>
                                                </div>
                                            </div>
                                            <a href="{{ $fileUrl }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file text-primary fs-2 me-2"></i>
                                            <div class="flex-grow-1">
                                                <small class="text-dark d-block">Document File</small>
                                                <small class="text-muted">{{ strtoupper($ext) }} File</small>
                                            </div>
                                            <a href="{{ $fileUrl }}"
                                               download
                                               class="btn btn-sm btn-outline-secondary rounded-pill">
                                                <i class="fas fa-download me-1"></i> Download
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Card Footer --}}
                        <div class="card-footer bg-white border-0 pb-4 pt-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fas fa-building me-1"></i>
                                    {{ $policy->company->company_name ?? 'N/A' }}
                                </div>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $policy->created_at->format('d M, Y') }}
                                </div>
                            </div>
                            @if (
                                $policy->file_path &&
                                    !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'mp4', 'mov', 'avi', 'mkv', 'mp3', 'wav', 'ogg']))
                                <div class="mt-2">
                                    <a href="{{ $fileUrl }}"
                                       download
                                       class="btn btn-sm btn-primary w-100 rounded-pill">
                                        <i class="fas fa-download me-1"></i> Download File
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-file-alt text-muted fs-1"></i>
                        </div>
                        <h5 class="text-muted">No Policies Found</h5>
                        <p class="text-muted small">No company policies match your search criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Load More Button --}}
        @if ($hasMore)
            <div class="text-center mt-4">
                <button wire:click="loadMore"
                        wire:loading.attr="disabled"
                        class="btn btn-outline-primary rounded-pill px-4 py-2">
                    <span wire:loading.remove>
                        <i class="fas fa-arrow-down me-2"></i> Load More
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-2"></i> Loading...
                    </span>
                </button>
            </div>
        @endif

        {{-- Image Modal for Fullscreen View --}}
        @if (
            $policies->contains(function ($policy) {
                return $policy->file_path &&
                    in_array(pathinfo($policy->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            }))
            <div class="modal fade"
                 id="imageModal"
                 tabindex="-1"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body p-0 text-center">
                            <img id="modalImage"
                                 src=""
                                 alt="Full size image"
                                 class="img-fluid rounded shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Additional CSS Styles --}}
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .file-preview {
            transition: all 0.2s ease;
        }

        .file-preview:hover {
            background-color: #f8f9fa !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>

    {{-- JavaScript for Image Modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image modal handler
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                imageModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const imageUrl = button.getAttribute('data-image');
                    const modalImage = document.getElementById('modalImage');
                    if (modalImage) {
                        modalImage.src = imageUrl;
                    }
                });
            }
        });
    </script>

</div>
