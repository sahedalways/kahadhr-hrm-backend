@push('styles')
    <link href="{{ asset('assets/css/reporting-duties.css') }}"
          rel="stylesheet" />
@endpush

<div>
    <div class="container-fluid px-0">
        <div class="col-md-3 mb-5 mt-2">
            <h5 class="fw-bold text-primary mb-0">
                <i class="fas fa-file-contract me-2"></i>Company Policies
            </h5>
            <p class="text-muted small mb-0">
                Access, review, and download all company policy
            </p>
        </div>


        <div class="mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-8">
                    <div class="position-relative">
                        <i
                           class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Search policies by title or description..."
                               class="form-control form-control-lg ps-5 border-0 shadow-sm rounded-pill" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-end gap-2">
                        @if ($policies->count() > 0)
                            @if ($policies->count() > 0)
                                <span class="badge bg-primary d-inline-flex align-items-center justify-content-center"
                                      style="height: 30px; padding: 0 15px; border-radius: 50%; gap: 4px; min-width: 30px;">
                                    <i class="fas fa-file-alt"
                                       style="font-size: 14px;"></i>
                                    <span style="font-size: 12px;">{{ $policies->count() }}</span>
                                </span>
                            @endif
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
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm hover-card rounded-4 overflow-hidden">
                        {{-- Card Header with Gradient --}}
                        <div class="card-header bg-gradient-primary text-white border-0 p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="icon-wrapper bg-white bg-opacity-20 rounded-circle p-2">
                                        <i class="fas fa-file-alt text-dark fs-5"></i>
                                    </div>
                                    <span
                                          class="badge bg-white bg-opacity-20 text-dark rounded-pill px-3 py-1 shadow-sm">
                                        <i class="fas fa-file-pdf me-1"></i>
                                        Policy ({{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }})
                                    </span>
                                </div>
                                <small class="text-white-50">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $policy->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold text-dark mb-3 line-clamp-2">
                                {{ $policy->title }}
                            </h5>

                            @php
                                $descriptionText = strip_tags($policy->description);
                                $descriptionLength = strlen($descriptionText);
                                $needsTruncation = $descriptionLength > 120;
                            @endphp

                            <div class="description-wrapper mb-3">
                                <p class="card-text text-muted small mb-2 line-clamp-3"
                                   id="description-{{ $policy->id }}"
                                   style="display: -webkit-box; line-height: 1.5;">
                                    {{ Str::limit($descriptionText, 120) }}
                                </p>
                                <p class="card-text text-muted small mb-2"
                                   id="full-description-{{ $policy->id }}"
                                   style="display: none; line-height: 1.5;">
                                    {{ $descriptionText }}
                                </p>
                                @if ($needsTruncation)
                                    <a href="javascript:void(0)"
                                       class="text-primary small text-decoration-none fw-semibold"
                                       onclick="toggleDescription({{ $policy->id }})">
                                        <span id="see-more-btn-{{ $policy->id }}">
                                            <i class="fas fa-chevron-down me-1"></i>See More
                                        </span>
                                        <span id="see-less-btn-{{ $policy->id }}"
                                              style="display: none;">
                                            <i class="fas fa-chevron-up me-1"></i>See Less
                                        </span>
                                    </a>
                                @endif
                            </div>

                            {{-- File Preview if exists --}}
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
                                                 class="img-fluid rounded-3 cursor-pointer preview-image w-100"
                                                 style="max-height: 180px; object-fit: cover;"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#imageModal"
                                                 data-image="{{ $fileUrl }}">
                                            <div class="image-overlay">
                                                <i class="fas fa-search-plus text-white fs-4"></i>
                                            </div>
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
                                                    <small class="text-dark fw-semibold d-block">Policy Document</small>
                                                    <small class="text-muted">PDF • {{ $fileSize }}</small>
                                                </div>
                                            </div>
                                            <a href="{{ $fileUrl }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                <i class="fas fa-external-link-alt me-1"></i> View
                                            </a>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="file-icon-wrapper">
                                                    <i class="fas fa-file-alt text-secondary fs-1"></i>
                                                </div>
                                                <div>
                                                    <small class="text-dark fw-semibold d-block">Document File</small>
                                                    <small class="text-muted">{{ strtoupper($ext) }} •
                                                        {{ $fileSize }}</small>
                                                </div>
                                            </div>
                                            <button wire:click="downloadPolicy({{ $policy->id }})"
                                                    class="btn btn-sm btn-outline-primary rounded-pill px-3">
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


    </div>




    <script>
        function toggleDescription(id) {
            const truncatedDesc = document.getElementById(`description-${id}`);
            const fullDesc = document.getElementById(`full-description-${id}`);
            const seeMoreBtn = document.getElementById(`see-more-btn-${id}`);
            const seeLessBtn = document.getElementById(`see-less-btn-${id}`);

            if (truncatedDesc.style.display === 'none') {
                truncatedDesc.style.display = '-webkit-box';
                fullDesc.style.display = 'none';
                seeMoreBtn.style.display = 'inline';
                seeLessBtn.style.display = 'none';
            } else {
                truncatedDesc.style.display = 'none';
                fullDesc.style.display = 'block';
                seeMoreBtn.style.display = 'none';
                seeLessBtn.style.display = 'inline';
            }
        }
    </script>
</div>
