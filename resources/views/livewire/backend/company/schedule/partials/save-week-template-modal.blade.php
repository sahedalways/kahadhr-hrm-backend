<div class="modal fade"
     id="saveWeekTemplateModal"
     tabindex="-1"
     wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-save me-2"></i> Save Week as Template
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Template Name <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control"
                           wire:model="newTemplateName"
                           placeholder="e.g., Standard Week, Night Shift Week">
                    @error('newTemplateName')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Description (Optional)</label>
                    <textarea class="form-control"
                              wire:model="newTemplateDescription"
                              rows="2"
                              placeholder="Add notes about this template..."></textarea>
                </div>
                <div class="alert alert-info text-white py-1 px-2 mb-2"
                     style="font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-1"></i>
                    This will save all shifts for the current week ({{ $displayDateRange }})
                </div>
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                <button type="button"
                        class="btn btn-primary"
                        wire:click="saveWeekAsTemplate"
                        wire:loading.attr="disabled"
                        wire:target="saveWeekAsTemplate">


                    <span wire:loading.remove
                          wire:target="saveWeekAsTemplate">
                        <i class="fas fa-save me-1"></i> Save Template
                    </span>


                    <span wire:loading
                          wire:target="saveWeekAsTemplate">
                        <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                    </span>

                </button>
            </div>
        </div>
    </div>
</div>
