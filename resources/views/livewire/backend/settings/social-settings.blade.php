<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Social Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3 align-items-center" wire:submit.prevent="save">

                        <!-- Facebook -->
                        <div class="col-md-4">
                            <label class="form-label">Facebook URL</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter Facebook URL"
                                wire:model="facebook">
                        </div>

                        <!-- Twitter -->
                        <div class="col-md-4">
                            <label class="form-label">Twitter URL</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter Twitter URL"
                                wire:model="twitter">
                        </div>

                        <!-- Instagram -->
                        <div class="col-md-4">
                            <label class="form-label">Instagram URL</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter Instagram URL"
                                wire:model="instagram">
                        </div>

                        <!-- LinkedIn -->
                        <div class="col-md-4">
                            <label class="form-label">LinkedIn URL</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter LinkedIn URL"
                                wire:model="linkedin">
                        </div>

                        <!-- YouTube -->
                        <div class="col-md-4">
                            <label class="form-label">YouTube URL</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter YouTube URL"
                                wire:model="youtube">
                        </div>

                        <!-- Save Button -->
                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="save">
                                    Save
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
