<div>
    @push('styles')
        <link href="{{ asset('assets/css/reporting-duties.css') }}"
              rel="stylesheet" />
    @endpush

    <div>
        <div class="container-fluid px-0">
            {{-- Header & Filters --}}
            <div class="mb-5">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas fa-file-contract me-2"></i>Company Policies
                        </h5>
                        <p class="text-muted small mb-0">Access, review, and download all company policy</p>
                    </div>
                    <div class="col-md-6">
                        <div class="position-relative">
                            <i
                               class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Search policies by title or description..."
                                   class="form-control form-control-lg ps-5 border-0 shadow-sm rounded-pill" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex justify-content-end gap-2 align-items-center">
                            @if ($policies->count() > 0)
                                <span class="badge bg-primary rounded-pill px-3 py-2">
                                    <i class="fas fa-file-alt me-1"></i> {{ $policies->count() }}
                                </span>
                            @endif
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle px-3"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-sort me-1"></i>
                                    {{ $sortOrder == 'desc' ? 'Newest' : 'Oldest' }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item"
                                           href="#"
                                           wire:click.prevent="handleSort('desc')">
                                            <i class="fas fa-arrow-down me-2"></i>Newest First
                                        </a></li>
                                    <li><a class="dropdown-item"
                                           href="#"
                                           wire:click.prevent="handleSort('asc')">
                                            <i class="fas fa-arrow-up me-2"></i>Oldest First
                                        </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Policies Grid --}}
            <div class="row g-4">
                @forelse ($policies as $policy)
                    @php
                        $plainText = strip_tags($policy->description);
                        $descriptionLength = strlen($plainText);
                        $needsTruncation = $descriptionLength > 50;
                        $fullDescription = $policy->description;
                        $truncatedDescription = Str::limit(strip_tags($policy->description), 120);
                    @endphp

                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-card rounded-4 overflow-hidden">
                            {{-- Card Header --}}
                            <div class="card-header bg-gradient-primary text-white border-0 p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="icon-wrapper bg-white bg-opacity-20 rounded-circle p-2">
                                            <i class="fas fa-file-contract text-dark fs-5"></i>
                                        </div>
                                        <span
                                              class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-1 shadow-sm">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Policy
                                            ({{ str_pad($policies->count() - $loop->iteration + 1, 4, '0', STR_PAD_LEFT) }})
                                        </span>
                                    </div>
                                    <small class="text-white-50">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $policy->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>

                            {{-- Card Body with Alpine toggle --}}
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2">
                                    {{ $policy->title }}
                                </h5>
                                <div class="description-wrapper mb-3"
                                     x-data="{ expanded: false }">

                                    <!-- Short Description (truncated) -->
                                    <div x-show="!expanded"
                                         x-transition.duration.300ms
                                         class="card-text text-muted small mb-2 description-cell">
                                        {{ Str::limit(strip_tags($policy->description), 20) }}
                                    </div>

                                    <!-- Full Description (with HTML) -->
                                    <div x-show="expanded"
                                         x-transition.duration.300ms
                                         class="card-text text-muted small mb-2 full-description-content description-cell"
                                         x-html="@js($policy->description)">
                                    </div>

                                    @php
                                        $needsTruncation = strlen(strip_tags($policy->description)) > 20;
                                    @endphp

                                    @if ($needsTruncation)
                                        <button @click="expanded = !expanded"
                                                type="button"
                                                class="btn btn-link text-primary small fw-semibold p-0 m-0 text-decoration-none mt-1"
                                                style="background: none; border: none; box-shadow: none;">

                                            <span x-show="!expanded">
                                                <i class="fas fa-chevron-down me-1"></i>See More
                                            </span>

                                            <span x-show="expanded">
                                                <i class="fas fa-chevron-up me-1"></i>See Less
                                            </span>

                                        </button>
                                    @endif

                                </div>

                                {{-- File Preview --}}
                                @if ($policy->file_path)
                                    <div class="file-preview-modern rounded-3 p-3 mb-3">
                                        @php
                                            $ext = pathinfo($policy->file_path, PATHINFO_EXTENSION);
                                            $fileUrl = asset('storage/' . $policy->file_path);
                                            $fileSize = Storage::disk('public')->exists($policy->file_path)
                                                ? number_format(
                                                        Storage::disk('public')->size($policy->file_path) / 1024,
                                                        1,
                                                    ) . ' KB'
                                                : 'Unknown size';
                                        @endphp

                                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <div class="image-preview-wrapper position-relative">
                                                <img src="{{ $fileUrl }}"
                                                     alt="{{ $policy->title }}"
                                                     class="img-fluid rounded-3 preview-image w-100"
                                                     style="max-height: 180px; object-fit: cover;"
                                                     data-src="{{ $fileUrl }}">
                                            </div>
                                            <div class="mt-2 text-center">
                                                <small class="text-muted">{{ $fileSize }}</small>
                                            </div>
                                        @elseif(in_array($ext, ['pdf']))
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="pdf-icon-wrapper">
                                                        <i class="fas fa-file-pdf text-danger fs-1"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-dark fw-semibold d-block">Policy
                                                            Document</small>
                                                        <small class="text-muted">PDF • {{ $fileSize }}</small>
                                                    </div>
                                                </div>
                                                <a href="{{ $fileUrl }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="fas fa-external-link-alt me-1"></i> View
                                                </a>
                                            </div>
                                        @elseif(in_array($ext, ['doc', 'docx']))
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="doc-icon-wrapper">
                                                        <i class="fas fa-file-word text-primary fs-1"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-dark fw-semibold d-block">Word
                                                            Document</small>
                                                        <small class="text-muted">DOCX • {{ $fileSize }}</small>
                                                    </div>
                                                </div>
                                                <button wire:click="downloadPolicy({{ $policy->id }})"
                                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </button>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="file-icon-wrapper">
                                                        <i class="fas fa-file-alt text-secondary fs-1"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-dark fw-semibold d-block">Document
                                                            File</small>
                                                        <small class="text-muted">{{ strtoupper($ext) }} •
                                                            {{ $fileSize }}</small>
                                                    </div>
                                                </div>
                                                <button wire:click="downloadPolicy({{ $policy->id }})"
                                                        class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                    <i class="fas fa-download me-1"></i> Download
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Card Footer --}}
                            <div class="card-footer bg-white border-0 pb-4 pt-0 px-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $policy->created_at->format('d M, Y') }}
                                    </div>
                                    @if ($policy->file_path)
                                        <button wire:click="downloadPolicy({{ $policy->id }})"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-download me-1"></i> Download
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-file-contract text-muted fs-1"></i>
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
                        <span wire:loading.remove><i class="fas fa-arrow-down me-2"></i> Load More</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i> Loading...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Prevent FOUC while Alpine initializes --}}
    <style>
        [x-cloak] {
            display: none !important;
        }

        .description-cell a {
            color: #0d6efd !important;
            text-decoration: underline;
        }

        .full-description-content {
            line-height: 1.5;
        }

        .full-description-content p {
            margin-bottom: 0.75rem;
        }

        .full-description-content ul,
        .full-description-content ol {
            margin-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .full-description-content a {
            color: #0d6efd;
            text-decoration: none;
        }

        .full-description-content a:hover {
            text-decoration: underline;
        }
    </style>
</div>
