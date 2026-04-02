<div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-500 text-primary">Trial Settings</h5>
                    <hr>
                    <form class="row g-3"
                          wire:submit.prevent="save">

                        <!-- Trial Days -->
                        <div class="col-md-4">
                            <label class="form-label">Trial Days <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   step="1"
                                   min="1"
                                   max="600"
                                   wire:model.defer="trial_days"
                                   placeholder="Enter number of trial days"
                                   oninput="this.value = this.value.replace(/[^0-9]/g,'');">

                            <small class="text-muted d-block">
                                Number of days for the trial period (max 600 days)
                            </small>

                            @error('trial_days')
                                <span class="text-danger d-block mt-1">{{ $message }}</span>
                            @enderror
                            <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                                <button type="submit"
                                        class="btn btn-success"
                                        wire:loading.attr="disabled"
                                        wire:target="save">
                                    <span wire:loading
                                          wire:target="save">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                    </span>
                                    <span wire:loading.remove
                                          wire:target="save">
                                        Save
                                    </span>
                                </button>
                            </div>
                        </div>



                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
