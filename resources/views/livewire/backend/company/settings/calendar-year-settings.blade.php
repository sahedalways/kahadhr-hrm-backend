<div class="card">
    <div class="card-body p-3">
        <form class="row g-3" wire:submit.prevent="save">

            <h5 class="fw-bold mb-2 text-primary">Calendar Year Settings</h5>
            <hr>

            <!-- Calendar Year -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Select Calendar Year <span class="text-danger">*</span></label>
                <select class="form-select shadow-sm" wire:model="calendar_year">
                    <option value="english">English Calendar (Jan - Dec)</option>
                    <option value="hmrc">HMRC Calendar (Apr - Mar)</option>
                </select>
                @error('calendar_year')
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
