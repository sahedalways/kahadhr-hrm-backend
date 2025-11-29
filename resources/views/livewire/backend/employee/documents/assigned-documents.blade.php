<div>
    <!-- Search + Sort -->
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col-md-6 mb-2">
            <input type="text" class="form-control shadow-sm" placeholder="Search by document name" wire:model="search"
                wire:keyup="set('search', $event.target.value)" />
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:change="handleSort($event.target.value)">
                <option value="desc">Newest First</option>
                <option value="asc">Oldest First</option>
            </select>


        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:change="handleFilter($event.target.value)">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="signed">Signed</option>
                <option value="expired">Expired</option>
            </select>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="row g-3">
        @forelse($documents as $doc)
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 10px; transition: all 0.2s;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2" style="font-size: 1rem;">
                            <i class="fas fa-file-alt text-primary me-1"></i>
                            {{ $doc->name }}
                        </h6>
                        <p class="text-muted small mb-1">
                            <i class="fas fa-user me-1"></i>
                            {{ $doc->employee?->full_name ?? 'N/A' }}
                        </p>
                        @if ($doc->status !== 'signed')
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Expires:
                                {{ $doc->expires_at ? \Carbon\Carbon::parse($doc->expires_at)->format('d M Y') : 'N/A' }}
                            </p>
                        @endif

                        <p class="mb-2">
                            @if ($doc->status === 'pending')
                                <span
                                    style="background-color: #ffc107; color:#fff; padding:0.25rem 0.5rem; border-radius:0.25rem;">Pending</span>
                            @elseif ($doc->status === 'signed')
                                <span
                                    style="background-color: #28a745; color:#fff; padding:0.25rem 0.5rem; border-radius:0.25rem;">Signed</span>
                            @elseif ($doc->status === 'expired')
                                <span
                                    style="background-color: #dc3545; color:#fff; padding:0.25rem 0.5rem; border-radius:0.25rem;">Expired</span>
                            @else
                                <span
                                    style="background-color: #6c757d; color:#fff; padding:0.25rem 0.5rem; border-radius:0.25rem;">Unknown</span>
                            @endif
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 text-center pb-3">
                        @if ($doc->document_url)
                            <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal"
                                data-bs-target="#documentModal" wire:click="openDocumentModal({{ $doc->id }})">
                                <i class="fas fa-eye me-1"></i> View
                            </button>
                        @else
                            <span class="text-muted small">No file available</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center mt-4">
                <p class="text-white">No documents assigned.</p>
            </div>
        @endforelse
    </div>

    <!-- Load More -->
    <div class="mt-3 text-center">
        @if ($hasMore)
            <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">
                Load More
            </button>
        @endif
    </div>

    <div wire:ignore.self class="modal fade" id="documentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Document Preview</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    @if ($currentDocument)
                        <iframe src="{{ $currentDocument->document_url }}" style="width:100%; height:500px;"
                            frameborder="0"></iframe>
                    @else
                        <p class="text-muted">No document selected.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <div class="modal-footer">
                        @if ($currentDocument)
                            @php
                                $isSigned = $currentDocument->status === 'signed';
                                $isExpired =
                                    $currentDocument->expires_at &&
                                    \Carbon\Carbon::parse($currentDocument->expires_at)->isPast();
                            @endphp

                            @if (!$isSigned && !$isExpired)
                                <div class="modal-footer">
                                    <button class="btn btn-success" wire:click="addSignature"
                                        wire:loading.attr="disabled" wire:target="addSignature"
                                        onclick="return confirm('Are you sure you want to add your signature?');">

                                        <!-- Loading spinner -->
                                        <span wire:loading wire:target="addSignature">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Signing ...
                                        </span>

                                        <!-- Normal button text -->
                                        <span wire:loading.remove wire:target="addSignature">
                                            <i class="fas fa-signature me-1"></i> Add Signature
                                        </span>
                                    </button>

                                </div>
                            @endif

                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.card').forEach(card => {
        card.addEventListener('mouseover', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.boxShadow = '0 6px 16px rgba(0,0,0,0.12)';
        });
        card.addEventListener('mouseout', () => {
            card.style.transform = 'translateY(0)';
            card.style.boxShadow = '0 1px 6px rgba(0,0,0,0.08)';
        });
    });
</script>
