<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500 text-white">Company Profile Settings</h5>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body p-3">
                    <form class="row g-3 align-items-center" wire:submit.prevent="save">

                        <hr>

                        <!-- Company Name -->
                        <div class="col-md-4">
                            <label class="form-label">Company Name<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="company_name"
                                placeholder="Enter Company Name">
                            @error('company_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Subdomain -->
                        <div class="col-md-4">
                            <label class="form-label">Subdomain</label>
                            <input type="text" class="form-control" wire:model="sub_domain"
                                placeholder="example.yourdomain.com" readonly>
                            @error('sub_domain')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- House Number -->
                        <div class="col-md-4">
                            <label class="form-label">Company House Number<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="company_house_number"
                                placeholder="House No. / Flat No.">
                            @error('company_house_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-4">
                            <label class="form-label">Company Mobile<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="company_mobile"
                                placeholder="Enter Mobile Number" readonly>
                            @error('company_mobile')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-4">
                            <label class="form-label">Company Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" wire:model="company_email"
                                placeholder="Enter Email" readonly>
                            @error('company_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Business Type -->
                        <div class="col-md-4">
                            <label class="form-label">Business Type</label>
                            <input type="text" class="form-control" wire:model="business_type"
                                placeholder="Business Type">
                            @error('business_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address Contact Info -->
                        <div class="col-md-12">
                            <label class="form-label">Address / Contact Info</label>
                            <textarea class="form-control" rows="3" wire:model="address_contact_info" placeholder="Enter Company Address"></textarea>
                            @error('address_contact_info')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        <div class="row">
                            <!-- Company Logo -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" wire:model="company_logo" accept="image/*">
                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror

                                @if ($company_logo)
                                    <img src="{{ $company_logo->temporaryUrl() }}" class="img-thumbnail mt-2"
                                        width="80">
                                    <div wire:loading wire:target="company_logo">
                                        <span class="text-muted">Uploading...</span>
                                    </div>
                                @elseif ($old_company_logo)
                                    <img src="{{ $old_company_logo }}" class="img-thumbnail mt-2" width="80">
                                @endif
                            </div>

                            <!-- Registered Domain -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Registered Domain</label>
                                <input type="text" class="form-control" wire:model="registered_domain"
                                    placeholder="yourdomain.com">
                                @error('registered_domain')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Calendar Year -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Calendar Year</label>
                                <select class="form-select" wire:model="calendar_year">
                                    <option value="">Select</option>
                                    <option value="english">English</option>
                                    <option value="hmrc">HMRC</option>
                                </select>
                                @error('calendar_year')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <hr>

                        <div class="modal-footer">

                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
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

        </div>
    </div>
</div>
