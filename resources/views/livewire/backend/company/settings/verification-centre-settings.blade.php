<div>

    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="col">
                        <h5 class="fw-500 text-primary">Verification Centre</h5>
                    </div>
                    <hr>
                    <form class="row g-3 align-items-center">


                        <!-- Mobile -->
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="flex-grow-1">
                                <label class="form-label">Company Mobile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="company_mobile"
                                    readonly oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="verifyAndUpdate">
                    <div class="modal-body">

                        @if (!$passwordVerified)
                            <div class="mb-3" wire:ignore>
                                <label>Enter Password <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="form-control extra-padding shadow-sm" id="password"
                                    wire:model.defer="passwordInput">
                                <span class="icon-position " style="cursor:pointer;"
                                    onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                                </div>


                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" wire:click="verifyPassword"
                                    wire:loading.attr="disabled" wire:target="verifyPassword">

                                    <span wire:loading wire:target="verifyPassword">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                    </span>

                                    <span wire:loading.remove wire:target="verifyPassword">
                                        Verify Password
                                    </span>
                                </button>

                            </div>
                        @endif

                        <!-- Email Input -->
                        @if ($passwordVerified)
                            @if ($updating_field === 'email')
                                <div class="mb-3">
                                    <label>New Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="email" class="form-control form-control-sm shadow-sm"
                                            wire:model="new_email" placeholder="Enter new email" style="height: 38px;">

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
                                        <input type="text" class="form-control shadow-sm form-control-sm"
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
                                                class="form-control text-center otp-field" maxlength="1"
                                                placeholder="-" oninput="handleOtpInput(this)"
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
                    @endif
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
