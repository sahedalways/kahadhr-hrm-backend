<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Payment Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form class="row g-3" wire:submit.prevent="save">

                        <!-- Gateway -->
                        <div class="col-md-4">
                            <label class="form-label">Gateway <span class="text-danger">*</span></label>
                            <select class="form-control" wire:model.defer="gateway">
                                <option value="bkash">Bkash</option>
                            </select>
                            @error('gateway')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- App Key -->
                        <div class="col-md-4">
                            <label class="form-label">App Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="app_key">
                            @error('app_key')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- App Secret -->
                        <div class="col-md-4">
                            <label class="form-label">App Secret <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="app_secret">
                            @error('app_secret')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Username -->
                        <div class="col-md-4">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="username">
                            @error('username')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="password" class="form-control extra-padding" wire:model.defer="password"
                                    id="password">
                                <span class="icon-position" style="cursor:pointer;"
                                    onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Base URL -->
                        <div class="col-md-4">
                            <label class="form-label">Base URL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model.defer="base_url">
                            @error('base_url')
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


<script>
    function togglePassword(id, el) {
        let input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = "password";
            el.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
</script>
