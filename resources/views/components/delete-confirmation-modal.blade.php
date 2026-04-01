@props([
    'id' => 'deleteConfirmationModal',
    'title' => 'Confirm Delete',
    'message' => 'Are you sure you want to delete this item?',
    'warning' => 'This action cannot be undone!',
    'showDataCount' => false,
    'dataCount' => 0,
    'dataCountMessage' => 'This will delete :count record(s) associated with this item!',
    'wireMethod' => 'delete',
    'wireParams' => [],
    'deleteButtonText' => 'Yes, Delete',
    'deleteButtonClass' => 'btn-danger',
])

<div wire:ignore.self
     class="modal fade"
     id="{{ $id }}"
     tabindex="-1"
     data-bs-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">{{ $title }}</h6>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <p>{{ $message }}</p>

                    @if ($showDataCount && $dataCount > 0)
                        <p class="text-danger fw-bold">
                            {{ str_replace(':count', $dataCount, $dataCountMessage) }}
                        </p>
                    @endif

                    <p class="text-muted small mt-2">
                        <strong class="text-danger">{{ $warning }}</strong>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button"
                        class="btn {{ $deleteButtonClass }}"
                        wire:click="{{ $wireMethod }}({{ implode(',', $wireParams) }})"
                        wire:loading.attr="disabled">
                    <span wire:loading
                          wire:target="{{ $wireMethod }}">
                        <i class="fas fa-spinner fa-spin me-2"></i> Deleting...
                    </span>
                    <span wire:loading.remove
                          wire:target="{{ $wireMethod }}">
                        {{ $deleteButtonText }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
