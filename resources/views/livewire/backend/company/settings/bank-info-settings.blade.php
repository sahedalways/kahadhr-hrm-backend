<div class="card">
    <div class="card-body">
        <form class="row g-3" wire:submit.prevent="save">

            <h5 class="fw-bold mb-0 text-primary">Bank Information</h5>
            <hr>

            <!-- Bank Name -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Bank Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-sm" wire:model="bank_name" placeholder="Enter Bank Name">
                @error('bank_name')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- Card Number -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Card Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-sm" wire:model="card_number"
                    placeholder="Enter Card Number" maxlength="20" oninput="this.value = this.value.replace(/\D/g, '')">
                @error('card_number')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>


            <!-- Expiry Date -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-sm" wire:model="expiry_date" placeholder="MM/YY"
                    maxlength="5" oninput="formatExpiry(this)">
                @error('expiry_date')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <!-- CVV -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">CVV <span class="text-danger">*</span></label>
                <input type="text" class="form-control shadow-sm" wire:model="cvv" placeholder="CVV" maxlength="4"
                    oninput="this.value = this.value.replace(/\D/g, '')">
                @error('cvv')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>



            <div class="d-flex justify-content-end">
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


<script>
    function formatExpiry(input) {
        let value = input.value.replace(/\D/g, '');


        if (value.length > 4) value = value.slice(0, 4);


        if (value.length > 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }

        input.value = value;

        @this.set('expiry_date', value);
    }
</script>
