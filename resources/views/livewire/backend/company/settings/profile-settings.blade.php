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
                            <label class="form-label">Company Name <span class="text-danger">*</span></label>
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
                            <label class="form-label">Company House Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model="company_house_number"
                                placeholder="House No. / Flat No.">
                            @error('company_house_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Mobile -->
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label">Company Mobile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="company_mobile" readonly>
                                @error('company_mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary ms-2 mb-0" wire:click="openModal('mobile')"
                                data-bs-toggle="modal" data-bs-target="#verifyModal">
                                Change
                            </button>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label">Company Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model="company_email" readonly>
                                @error('company_email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="button" class="btn btn-primary ms-2 mb-0" wire:click="openModal('email')"
                                data-bs-toggle="modal" data-bs-target="#verifyModal">
                                Change
                            </button>
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

    <div wire:ignore.self class="modal fade" id="verifyModal" tabindex="-1" role="dialog"
        aria-labelledby="verifyModal" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Verification Centre</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:none;">
                        <i class="fas fa-times" style="color:black;"></i>
                    </button>
                </div>

                <form wire:submit.prevent="verifyAndUpdate">
                    <div class="modal-body">

                        <!-- Email Input -->
                        @if ($updating_field === 'email')
                            <div class="mb-3">
                                <label>New Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control form-control-sm" wire:model="new_email"
                                        placeholder="Enter new email" style="height: 38px;">

                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>





                                </div>

                                <!-- Livewire polling for countdown -->
                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Mobile Input -->
                        @if ($updating_field === 'mobile')
                            <div class="mb-3">
                                <label>New Mobile No. <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model="new_mobile" placeholder="Enter new mobile no."
                                        style="height: 38px;">
                                    <button
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        type="button" style="height: 38px;"
                                        wire:click.prevent.stop="requestVerification('{{ $updating_field }}')"
                                        wire:loading.attr="disabled" wire:target="requestVerification"
                                        @if ($otpCooldown > 0) disabled @endif>
                                        <span wire:loading wire:target="requestVerification">
                                            <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                        </span>
                                        <span wire:loading.remove wire:target="requestVerification">
                                            @if ($otpCooldown > 0)
                                                Resend In
                                                {{ floor($otpCooldown / 60) }}:{{ str_pad($otpCooldown % 60, 2, '0', STR_PAD_LEFT) }}
                                            @elseif($code_sent)
                                                Resend OTP
                                            @else
                                                Send OTP
                                            @endif
                                        </span>
                                    </button>





                                </div>


                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif

                                @error('new_mobile')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif

                        <!-- Verification Code Input -->
                        @if ($code_sent)
                            <div class="mb-3">
                                <label>Verification Code <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text" wire:model="otp.{{ $i }}"
                                            class="form-control text-center otp-field" maxlength="1" placeholder="-"
                                            style="width: 50px; font-size: 1.5rem; height: 50px;"
                                            oninput="handleOtpInput(this)"
                                            onkeydown="handleOtpBackspace(event, this)">
                                    @endfor
                                </div>
                                @error('verification_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif




                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        @if ($code_sent)
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="verifyOtp">
                                <span wire:loading wire:target="verifyOtp">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                </span>
                                <span wire:loading.remove wire:target="verifyOtp">Verify</span>
                            </button>
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>


</div>


<script>
    function handleOtpInput(el) {

        el.value = el.value.replace(/[^0-9]/g, '');


        if (el.value.length === 1) {
            const next = el.nextElementSibling;
            if (next && next.classList.contains('otp-field')) {
                next.focus();
            }
        }
    }

    function handleOtpBackspace(e, el) {

        if (e.key === 'Backspace') {
            if (el.value) {

                el.value = '';
            } else {

                const prev = el.previousElementSibling;
                if (prev && prev.classList.contains('otp-field')) {
                    prev.focus();
                    prev.value = '';
                }
            }

            e.preventDefault();
        }
    }
</script>
