<div class="modal fade"
     wire:ignore.self
     id="forgotPasswordModal"
     tabindex="-1"
     data-bs-backdrop="static"
     style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <style>
        /* Step Progress Styles */
        #forgotPasswordModal .step-dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        /* Selection Card Styles */
        #forgotPasswordModal .method-card {
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        #forgotPasswordModal .method-card:hover {
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }

        #forgotPasswordModal .method-card.active {
            background-color: #eef2ff;
            border-color: #4f46e5;
        }

        /* OTP Input Styles */
        #forgotPasswordModal .otp-field {
            width: 45px;
            height: 55px;
            font-size: 1.25rem;
            font-weight: 700;
            border-radius: 10px;
            border: 2px solid #e2e8f0;
        }

        #forgotPasswordModal .otp-field:focus {
            border-color: #4f46e5;
            box-shadow: none;
            outline: none;
        }
    </style>

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg"
             style="border-radius: 20px;">

            <div class="modal-header border-0 pt-4 px-4">
                <div>
                    <h5 class="fw-bold mb-0"
                        style="color: #1e293b;">Reset Password</h5>
                    <p class="text-muted small mb-0">Follow the steps to recover your account</p>
                </div>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    @php $steps = [1 => 'Send', 2 => 'Verify', 3 => 'Reset']; @endphp
                    @foreach ($steps as $num => $label)
                        <div class="text-center"
                             style="flex: 1;">
                            <div class="step-dot mx-auto mb-1 {{ $currentStep >= $num ? 'bg-primary text-white' : 'bg-light text-muted' }}"
                                 style="{{ $currentStep > $num ? 'background-color: #10b981 !important;' : '' }}">
                                @if ($currentStep > $num)
                                    ✓
                                @else
                                    {{ $num }}
                                @endif
                            </div>
                            <span
                                  class="small fw-bold {{ $currentStep >= $num ? 'text-dark' : 'text-muted' }}">{{ $label }}</span>
                        </div>
                        @if ($num < 3)
                            <div style="flex: 1; height: 2px; background: #f1f5f9; margin-top: -20px;"></div>
                        @endif
                    @endforeach
                </div>

                @if ($currentStep === 1)
                    <div class="mb-3">
                        <label class="method-card {{ $resetMethod === 'email' ? 'active' : '' }}">
                            <input type="radio"
                                   wire:model.live="resetMethod"
                                   value="email"
                                   class="d-none">
                            <div
                                 style="width: 45px; height: 45px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                📧
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Email Address</div>
                                <div class="text-muted small">Send code to your inbox</div>
                            </div>
                        </label>

                        <label class="method-card {{ $resetMethod === 'phone' ? 'active' : '' }}">
                            <input type="radio"
                                   wire:model.live="resetMethod"
                                   value="phone"
                                   class="d-none">
                            <div
                                 style="width: 45px; height: 45px; background: #f0fdf4; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                📱
                            </div>
                            <div>
                                <div class="fw-bold text-dark">Phone Number</div>
                                <div class="text-muted small">Send code via SMS</div>
                            </div>
                        </label>
                    </div>

                    @if ($resetMethod)
                        <div class="animate__animated animate__fadeIn">
                            <label class="small fw-bold text-muted mb-1">{{ ucfirst($resetMethod) }} <span
                                      class="text-danger">*</span></label>

                            @if ($resetMethod === 'email')
                                <input type="email"
                                       class="form-control py-2"
                                       style="border-radius: 10px;"
                                       wire:model.defer="resetEmail"
                                       placeholder="Enter your email">
                            @else
                                <input type="text"
                                       inputmode="numeric"
                                       maxlength="15"
                                       class="form-control py-2"
                                       style="border-radius: 10px;"
                                       wire:model.defer="resetPhone"
                                       placeholder="Enter your phone"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @endif
                        </div>
                    @endif
                @endif

                @if ($currentStep === 2)
                    <div class="text-center mb-4">
                        <h6 class="fw-bold">Check your {{ $resetMethod }}</h6>
                        <p class="text-muted small">We've sent a 6-digit code to your account.</p>
                        <div class="d-flex gap-2 justify-content-center mt-3">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text"
                                       wire:model="changePasswordOtp.{{ $i }}"
                                       class="form-control text-center otp-field"
                                       maxlength="1"
                                       placeholder="-"
                                       inputmode="numeric"
                                       autocomplete="one-time-code"
                                       @if ($i === 0) autofocus @endif
                                       oninput="handleOtpInputTwo(this, {{ $i }})"
                                       onkeydown="handleOtpBackspaceForChangePassword(event, {{ $i }})">
                            @endfor


                        </div>
                    </div>
                @endif

                @if ($currentStep === 3)
                    <div class="mb-3 position-relative">
                        <label class="small fw-bold text-muted mb-1">New Password <span
                                  class="text-danger">*</span></label>
                        <input type="password"
                               class="form-control py-2"
                               style="border-radius: 10px;"
                               id="newPassword"
                               wire:model.defer="newPassword"
                               placeholder="Enter new password">

                        <span id="togglePassword"
                              class="position-absolute"
                              style="top: 70%; right: 12px; transform: translateY(-50%); cursor: pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <div class="mb-3 position-relative">
                        <label class="small fw-bold text-muted mb-1">Confirm New Password <span
                                  class="text-danger">*</span></label>
                        <input type="password"
                               class="form-control py-2"
                               style="border-radius: 10px;"
                               id="confirmPassword"
                               wire:model.defer="confirmPassword"
                               placeholder="Enter confirm password">

                        <span id="togglePasswordConfirm"
                              class="position-absolute"
                              style="top: 70%; right: 12px; transform: translateY(-50%); cursor: pointer;">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                @endif

                @if ($currentStep === 2)
                    @if ($resetOtpCooldown > 0)
                        <div class="text-center p-2 mt-3"
                             style="background: #fffbeb; border-radius: 10px; color: #b45309; font-size: 0.85rem;">
                            <span wire:poll.1000ms="tickResetOtp">
                                Code expires in:
                                <strong>{{ floor($resetOtpCooldown / 60) }}:{{ str_pad($resetOtpCooldown % 60, 2, '0', STR_PAD_LEFT) }}</strong>
                            </span>
                        </div>
                    @else
                        <div class="text-center mt-3">
                            <button class="btn btn-primary btn-sm"
                                    wire:click="sendResetOtp"
                                    wire:loading.attr="disabled"
                                    wire:target="sendResetOtp">

                                <span wire:loading
                                      wire:target="sendResetOtp">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Sending...
                                </span>


                                <span wire:loading.remove
                                      wire:target="sendResetOtp">
                                    Resend Code
                                </span>

                            </button>
                        </div>
                    @endif
                @endif


            </div>

            <div class="modal-footer border-0 pb-4 px-4 mt-2">
                <button type="button"
                        class="btn btn-link text-muted text-decoration-none fw-bold border"
                        data-bs-dismiss="modal">Cancel</button>

                @if ($currentStep === 1 && $resetMethod)
                    <button class="btn btn-primary px-4 fw-bold shadow-sm"
                            style="border-radius: 10px;"
                            wire:click="sendResetOtp"
                            wire:loading.attr="disabled"
                            wire:target="sendResetOtp">

                        <span wire:loading
                              wire:target="sendResetOtp">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Sending...
                        </span>

                        <span wire:loading.remove
                              wire:target="sendResetOtp">
                            Send Code
                        </span>
                    </button>
                @endif

                @if ($currentStep === 2)
                    <button class="btn btn-primary px-4 fw-bold shadow-sm"
                            style="border-radius: 10px;"
                            wire:click="verifyResetOtp"
                            wire:loading.attr="disabled"
                            wire:target="verifyResetOtp">
                        <span wire:loading
                              wire:target="verifyResetOtp">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Verifying...
                        </span>

                        <span wire:loading.remove
                              wire:target="verifyResetOtp">
                            Verify & Continue
                        </span>
                    </button>
                @endif

                @if ($currentStep === 3)
                    <button class="btn btn-success px-4 fw-bold shadow-sm"
                            style="border-radius: 10px;"
                            wire:click="updatePassword"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword">

                        <span wire:loading
                              wire:target="updatePassword">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Updating...
                        </span>

                        <span wire:loading.remove
                              wire:target="updatePassword">
                            Update Password
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('click', function(e) {
        if (e.target.closest('#togglePassword')) {
            const newPassword = document.querySelector('#newPassword');
            const type = newPassword.type === 'password' ? 'text' : 'password';
            newPassword.type = type;

            e.target.closest('#togglePassword').innerHTML =
                type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }

        if (e.target.closest('#togglePasswordConfirm')) {
            const confirmPassword = document.querySelector('#confirmPassword');
            const type = confirmPassword.type === 'password' ? 'text' : 'password';
            confirmPassword.type = type;

            e.target.closest('#togglePasswordConfirm').innerHTML =
                type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        }
    });
</script>


<script>
    function handleOtpInputForChangePassword(el, index) {
        el.value = el.value.replace(/[^0-9]/g, '');
        if (el.value.length === 1) {
            const next = el.nextElementSibling;
            if (next) next.focus();
        }
    }

    function handleOtpBackspaceForChangePassword(e, index) {
        if (e.key !== "Backspace") return;

        const el = e.target;


        if (el.value === "") {
            const prev = el.previousElementSibling;
            if (prev) {
                prev.focus();
                prev.value = "";
                prev.dispatchEvent(new Event('input'));
            }
        } else {

            el.value = "";
            el.dispatchEvent(new Event('input'));
        }
    }
</script>


<script>
    window.addEventListener('closeModal', () => {
        const modalEl = document.getElementById('forgotPasswordModal');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
        modalInstance.hide();
    });

    window.addEventListener('redirectAfterDelay', (event) => {
        setTimeout(() => {
            window.location.href = event.detail.url;
        }, 1000);
    });
</script>


<script>
    function handleOtpInputTwo(el, index) {
        el.value = el.value.replace(/[^0-9]/g, '');

        if (!el.value) return;

        const inputs = document.querySelectorAll('.otp-field');

        if (index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    }

    function handleOtpBackspace(e, index) {
        if (e.key !== 'Backspace') return;

        const inputs = document.querySelectorAll('.otp-field');

        if (!inputs[index].value && index > 0) {
            inputs[index - 1].focus();
        }
    }
</script>
