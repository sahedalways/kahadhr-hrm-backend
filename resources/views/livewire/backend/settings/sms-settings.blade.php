<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">SMS Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Twilio SID -->
                        <div class="col-md-4">
                            <label class="form-label">Twilio SID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="twilio_sid"
                                placeholder="Your Twilio SID">
                            @error('twilio_sid')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Twilio Auth Token -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Twilio Auth Token <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding shadow-sm"
                                    wire:model.defer="twilio_auth_token" id="twilio_auth_token"
                                    placeholder="Your Twilio Auth Token">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('twilio_auth_token', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('twilio_auth_token')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Twilio From -->
                        <div class="col-md-4">
                            <label class="form-label">Twilio From <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="twilio_from"
                                placeholder="Twilio sender number">
                            @error('twilio_from')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
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

<!-- Eye toggle script -->
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
