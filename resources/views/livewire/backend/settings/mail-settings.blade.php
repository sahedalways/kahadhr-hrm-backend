<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Mail Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Mailer -->
                        <div class="col-md-4">
                            <label class="form-label">Mailer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_mailer"
                                placeholder="e.g. smtp">
                            @error('mail_mailer')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Host -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Host <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_host"
                                placeholder="e.g. smtp.mailtrap.io">
                            @error('mail_host')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Port -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Port <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_port" placeholder="587">
                            @error('mail_port')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_username"
                                placeholder="Your mail username">
                            @error('mail_username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Mail Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding"
                                    wire:model.defer="mail_password" id="mail_password"
                                    placeholder="Your mail password">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('mail_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>

                            </div>
                            @error('mail_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Encryption -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Encryption <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_encryption"
                                placeholder="tls/ssl">
                            @error('mail_encryption')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- From Address -->
                        <div class="col-md-6">
                            <label class="form-label">From Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" wire:model.defer="mail_from_address"
                                placeholder="noreply@example.com">
                            @error('mail_from_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- From Name -->
                        <div class="col-md-6">
                            <label class="form-label">From Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="mail_from_name"
                                placeholder="App Name">
                            @error('mail_from_name')
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
