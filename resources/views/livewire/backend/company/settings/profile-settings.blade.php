<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500">Company Profile Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body p-lg-5 p-4 shadow-sm border rounded-3 bg-white">
                    <form class="row g-4 align-items-center" wire:submit.prevent="save">

                        <h5 class="fw-bold mb-0">Company Information</h5>
                        <hr class="mt-2">

                        <!-- Company Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="company_name"
                                placeholder="Enter Company Name">
                            @error('company_name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Subdomain -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subdomain</label>

                            <div class="input-group shadow-sm" style="height: 34px;">

                                <input type="text" class="form-control" id="fullDomain"
                                    style="height: 100%; padding: 2px 6px; font-size: 13px;"
                                    value="{{ $sub_domain ? $sub_domain . '.' . config('app.base_domain') : '' }}"
                                    readonly>

                                <button class="border btn btn-outline-secondary btn-sm btn-no-hover shadow-none"
                                    type="button" onclick="copyFullDomain()"
                                    style="height: 100%; padding: 2px 10px; font-size: 13px;">
                                    Copy
                                </button>


                            </div>

                            @error('sub_domain')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- House Number -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Company House Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control shadow-sm" wire:model="company_house_number"
                                placeholder="House No. / Flat No.">
                            @error('company_house_number')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Business Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Business Type</label>
                            <input type="text" class="form-control shadow-sm" wire:model="business_type"
                                placeholder="Business Type">
                            @error('business_type')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address Contact Info -->
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Address / Contact Info</label>
                            <textarea class="form-control shadow-sm" rows="3" wire:model="address_contact_info"
                                placeholder="Enter Company Address"></textarea>
                            @error('address_contact_info')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr class="mt-4">

                        <h5 class="fw-bold mb-0">Brand & Company Settings</h5>
                        <hr class="mt-2">

                        <div class="row mt-2">

                            <!-- Company Logo -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Company Logo</label>
                                <input type="file" class="form-control shadow-sm" wire:model="company_logo"
                                    accept="image/*">
                                @error('company_logo')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}"
                                        class="img-thumbnail mt-2 shadow-sm rounded" width="90">
                                    <div wire:loading wire:target="company_logo">
                                        <span class="text-muted small">Uploading...</span>
                                    </div>
                                @elseif ($old_company_logo)
                                    <img src="{{ $old_company_logo }}" class="img-thumbnail mt-2 shadow-sm rounded"
                                        width="90">
                                @endif
                            </div>

                            <!-- Registered Domain -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Website URL</label>
                                <input type="text" class="form-control shadow-sm" wire:model="registered_domain"
                                    placeholder="yourdomain.com">
                                @error('registered_domain')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

                        <hr class="mt-4">

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4 py-2 shadow-sm"
                                wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="save">Save</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>



</div>
<script>
    function copyFullDomain() {
        const input = document.getElementById('fullDomain');
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Copied: ' + input.value);
        });
    }
</script>
