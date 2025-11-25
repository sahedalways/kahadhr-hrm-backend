<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Contact Info Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3 align-items-center" wire:submit.prevent="save">

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Enter Email" wire:model="email">
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control shadow-sm" placeholder="Enter Phone Number"
                                wire:model="phone">
                        </div>

                        <!-- Dhaka Office Address -->
                        <div class="col-12">
                            <label class="form-label">Dhaka Office Address</label>
                            <textarea class="form-control" placeholder="Enter Dhaka Office Address" wire:model="dhaka_office_address"
                                rows="2"></textarea>
                        </div>

                        <!-- Gazipur Office Address -->
                        <div class="col-12">
                            <label class="form-label">Gazipur Office Address</label>
                            <textarea class="form-control" placeholder="Enter Gazipur Office Address" wire:model="gazipur_office_address"
                                rows="2"></textarea>
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
