<div class="modal fade"
     id="loadWeekTemplateModal"
     tabindex="-1"
     wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-download me-2"></i> Load Weekly Template
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if (count($weeklyTemplates) > 0)
                    <div class="list-group">
                        @foreach ($weeklyTemplates as $template)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $template->name }}</h6>
                                        <small class="text-muted">
                                            Created: {{ $template->created_at->format('M d, Y') }}
                                            @if ($template->description)
                                                <br>{{ $template->description }}
                                            @endif
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <!-- Apply Button -->
                                        <button class="btn btn-sm btn-primary"
                                                onclick="return confirmApply('{{ $template->name }}', '{{ $template->id }}')"
                                                wire:click="applyWeeklyTemplate({{ $template->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="applyWeeklyTemplate({{ $template->id }})"
                                                data-bs-dismiss="modal">

                                            <span wire:loading.remove
                                                  wire:target="applyWeeklyTemplate({{ $template->id }})">
                                                <i class="fas fa-check me-1"></i> Apply
                                            </span>

                                            <span wire:loading
                                                  wire:target="applyWeeklyTemplate({{ $template->id }})">
                                                <i class="fas fa-spinner fa-spin me-1"></i> Applying...
                                            </span>
                                        </button>


                                        <!-- Delete Button -->
                                        <button class="btn btn-sm btn-danger"
                                                wire:click="deleteWeeklyTemplate({{ $template->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteWeeklyTemplate({{ $template->id }})"
                                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">

                                            <span wire:loading.remove
                                                  wire:target="deleteWeeklyTemplate({{ $template->id }})">
                                                <i class="fas fa-trash"></i>
                                            </span>

                                            <span wire:loading
                                                  wire:target="deleteWeeklyTemplate({{ $template->id }})">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>

                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No weekly templates saved yet.</p>

                    </div>
                @endif


                @if ($loaded >= $perPage)
                    <div class="text-center mt-3">
                        <button wire:click="loadMoreWeeklyTemplates"
                                wire:loading.attr="disabled"
                                class="btn btn-outline-primary btn-sm">

                            <span wire:loading.remove>Load More</span>
                            <span wire:loading>Loading...</span>

                        </button>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script>
    function confirmApply(templateName, templateId) {

        @this.call('hasCurrentWeekShifts').then(hasShifts => {
            if (hasShifts) {
                return confirm(
                    "⚠️ Warning: Applying this template will replace ALL existing shifts for the current week.\n\nThis action cannot be undone.\n\nDo you want to continue?"
                );
            } else {
                return confirm("Apply this template to the current week?");
            }
        });
        return false;
    }
</script>
