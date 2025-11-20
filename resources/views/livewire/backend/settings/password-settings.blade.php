<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Password Settings</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Old Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Old Password <span class="text-danger">*</span></label>

                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding"
                                    wire:model.defer="old_password" id="old_password">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('old_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>

                            @error('old_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>

                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding"
                                    wire:model.defer="new_password" id="new_password">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('new_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>

                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="position-relative">


                                <input type="password" class="form-control extra-padding"
                                    wire:model.defer="confirm_new_password" id="confirm_new_password">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('confirm_new_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('confirm_new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

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

<script>
    function togglePassword(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = "password";
            icon.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
</script>
