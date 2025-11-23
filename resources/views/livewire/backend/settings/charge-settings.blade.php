<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Charge Rate Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Per Employee Charge -->
                        <div class="col-md-4">
                            <label class="form-label">Rate (£) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" step="0.01" min="0"
                                wire:model.defer="rate" placeholder="Enter company charge rate"
                                oninput="this.value = this.value.replace(/[^0-9.]/g,'');">
                            @error('rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
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
                        </div>

                        <!-- Submit Button -->


                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
