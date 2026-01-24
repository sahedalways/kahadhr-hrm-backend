@section('title', 'Company Login | ' . siteSetting()->site_title)

<div>
    @include('components.reset-password-modal')


    <main class="main-content main-content-bg mt-0">
        <div class="page-header min-vh-100"
             style="background-image: url('assets/img/login-bg.jpg');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                <div class="row justify-content-center py-5">

                    <div class="col-xl-4 col-md-5 col-md-7">
                        <div class="card border-0 mb-0">

                            <div class="position-absolute top-0 end-0 p-2">
                                <a href="{{ route('employee.auth.empLogin', ['company' => $company]) }}"
                                   class="btn btn-sm btn-secondary">
                                    Employee Login
                                </a>
                            </div>

                            <div class="card-header bg-transparent text-center mt-5">
                                <div class="d-flex flex-column align-items-center justify-content-center mt-2 mb-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <img src="{{ getCompanyLogoUrl() }}"
                                             alt="Logo"
                                             class="login-logo mb-2"
                                             style="width: 100px; height: auto;">
                                    </div>



                                    <h6 class="text-primary">Welcome to Company Panel</h6>
                                </div>

                            </div>
                            <div class="card-body px-lg-5 pt-0"
                                 x-transition.fade>
                                <div class="">
                                    <div class="text-muted mb-3">
                                        <small class="fw-bold">Login to Continue</small>
                                    </div>
                                    <form role="form"
                                          class="text-start"
                                          wire:submit.prevent="login">
                                        <div class="mb-3">
                                            <input type="email"
                                                   class="form-control"
                                                   placeholder="Email"
                                                   wire:model="email">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <div class="position-relative">
                                                <input type="password"
                                                       id="password"
                                                       class="form-control"
                                                       placeholder="Password"
                                                       wire:model="password">

                                                <span class="d-flex position-absolute top-50 end-3 translate-middle-y cursor-pointer"
                                                      onclick="togglePassword()">
                                                    <i id="passwordEye"
                                                       class="fas fa-eye"></i>
                                                </span>
                                            </div>

                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @if ($errors->has('login_error'))
                                            <span
                                                  class="text-center text-danger">{{ $errors->first('login_error') }}</span>
                                        @endif

                                        <div class="text-end mt-2">
                                            <a href="javascript:void(0)"
                                               class="text-sm text-primary fw-bold"
                                               data-bs-toggle="modal"
                                               data-bs-target="#forgotPasswordModal"
                                               wire:click="cleaResetPasswordFields">
                                                Forgot password?
                                            </a>
                                        </div>


                                        <div class="form-check form-switch">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="rememberMe"
                                                   wire:model="rememberMe">
                                            <label class="form-check-label"
                                                   for="rememberMe">Remember me</label>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit"
                                                    class="btn btn-primary w-100 my-4 mb-4"
                                                    wire:loading.attr="disabled"
                                                    wire:target="login">


                                                <span wire:loading
                                                      wire:target="login">
                                                    <i class="fas fa-spinner fa-spin me-2"></i> Logging In ...
                                                </span>


                                                <span wire:loading.remove
                                                      wire:target="login">
                                                    Login
                                                </span>
                                            </button>
                                        </div>


                                        <div class="mb-2 position-relative text-center">
                                            <p
                                               class="text-sm fw-500 mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">
                                                Powered by <a href="{{ url('/') }}"
                                                   class="text-dark fw-600"
                                                   target="_blank"> {{ siteSetting()->site_title }}</a>
                                            </p>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($showOtpModal)
            <div class="modal fade show d-block"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered"> <!-- Centered -->
                    <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title">Enter OTP</h5>
                        </div>

                        <!-- Modal Body -->
                        <form role="form"
                              class="text-start"
                              wire:submit.prevent="verifyOtp">
                            <div class="modal-body">

                                <!-- OTP Fields -->
                                <div class="d-flex justify-content-between gap-2 mb-3">
                                    @for ($i = 0; $i < 6; $i++)
                                        <input type="text"
                                               wire:model="otp.{{ $i }}"
                                               class="form-control text-center otp-field"
                                               maxlength="1"
                                               placeholder="-"
                                               inputmode="numeric"
                                               autocomplete="one-time-code"
                                               @if ($i === 0) autofocus @endif
                                               oninput="handleOtpInput(this, {{ $i }})"
                                               onkeydown="handleOtpBackspace(event, {{ $i }})">
                                    @endfor
                                </div>


                                <!-- Countdown Polling -->
                                @if ($otpCooldown > 0)
                                    <div wire:poll.1000ms="tick"></div>
                                @endif
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer justify-content-between">

                                <!-- Send/Resend OTP Button -->
                                <button type="button"
                                        class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                                        wire:click.prevent.stop="login('{{ $updating_field }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="login"
                                        style="height: 38px; min-width: 120px;"
                                        @if ($otpCooldown > 0) disabled @endif>
                                    <span wire:loading
                                          wire:target="login">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Sending...
                                    </span>
                                    <span wire:loading.remove
                                          wire:target="login">
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

                                <!-- Verify Button -->
                                @if ($code_sent)
                                    <div class="ms-auto d-flex gap-2">
                                        <button type="submit"
                                                class="btn btn-success"
                                                wire:loading.attr="disabled"
                                                wire:target="verifyOtp">
                                            <span wire:loading
                                                  wire:target="verifyOtp">
                                                <i class="fas fa-spinner fa-spin me-2"></i> Verifying...
                                            </span>
                                            <span wire:loading.remove
                                                  wire:target="verifyOtp">Verify</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </form>

                    </div>
                </div>


            </div>


            <div class="modal-backdrop-custom fade show"></div>

            <style>
                /* Custom Dark + Blur Backdrop */
                .modal-backdrop-custom {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);

                    backdrop-filter: blur(5px);

                    -webkit-backdrop-filter: blur(5px);

                    z-index: 1040;

                }

                .modal-dialog {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
            </style>
        @endif



    </main>



</div>

<script>
    function handleOtpInput(el, index) {
        // Allow only numbers
        el.value = el.value.replace(/[^0-9]/g, '');

        // Move to next field if input exists
        if (el.value.length === 1 && index < 5) {
            const nextField = document.querySelector(`[wire\\:model="otp.${index + 1}"]`);
            if (nextField) nextField.focus();
        }
    }

    function handleOtpBackspace(event, index) {
        const el = event.target;

        // If backspace pressed and current field is empty, move to previous
        if (event.key === 'Backspace') {
            if (el.value === '' && index > 0) {
                const prevField = document.querySelector(`[wire\\:model="otp.${index - 1}"]`);
                if (prevField) {
                    prevField.focus();
                    prevField.value = ''; // Clear previous field
                }
            } else {
                // Clear current field
                el.value = '';
            }
            event.preventDefault();
        }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        const type = urlParams.get('type') || 'info';

        if (message) {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-center",
                timeOut: 5000
            };
            toastr[type](message);
        }
    });
</script>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('passwordEye');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>

<script>
    function handleOtpInput(el, index) {
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
