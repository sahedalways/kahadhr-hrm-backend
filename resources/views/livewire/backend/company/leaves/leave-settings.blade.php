<div class="card">
    <div class="card-body p-3">
        <form class="row g-3" wire:submit.prevent="save">

            <h5 class="fw-bold mb-2">Leave Settings</h5>
            <hr>

            <!-- Full-Time Hours -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full-Time Annual Leave Hours <span
                        class="text-danger">*</span></label>
                <input type="number" class="form-control shadow-sm" wire:model="full_time_hours" min="0"
                    step="0.01" placeholder="Enter full-time hours">
                @error('full_time_hours')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="d-flex justify-content-start mt-3">
                <button type="submit" class="btn btn-success px-4 py-2 shadow-sm" wire:loading.attr="disabled"
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
