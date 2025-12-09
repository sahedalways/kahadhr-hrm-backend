<div>


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-500 mb-0 text-primary">Mail Settings</h5>
                    <hr>
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Mailer -->
                        <div class="col-md-4">
                            <label class="form-label">Mailer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_mailer"
                                placeholder="e.g. smtp" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_mailer')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Host -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Host <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_host"
                                placeholder="e.g. smtp.mailtrap.io"
                                oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_host')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Port -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Port <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_port"
                                placeholder="587" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_port')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="col-md-4">
                            <label class="form-label">Mail Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_username"
                                placeholder="Your mail username" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Mail Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding shadow-sm"
                                    wire:model.defer="mail_password" id="mail_password" placeholder="Your mail password"
                                    oninput="this.value = this.value.replace(/\s/g, '')">
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
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_encryption"
                                placeholder="tls/ssl" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_encryption')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- From Address -->
                        <div class="col-md-6">
                            <label class="form-label">From Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control shadow-sm" wire:model.defer="mail_from_address"
                                placeholder="noreply@example.com" oninput="this.value = this.value.replace(/\s/g, '')">
                            @error('mail_from_address')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- From Name -->
                        <div class="col-md-6">
                            <label class="form-label">From Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="mail_from_name"
                                placeholder="App Name" oninput="this.value = this.value.replace(/\s/g, '')">
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
