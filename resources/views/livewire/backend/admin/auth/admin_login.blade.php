<div>
    <main class="main-content main-content-bg mt-0">
        <div class="page-header min-vh-100" style="background-image: url('assets/img/login-bg.jpg');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-7">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-transparent text-center">
                                <div class="d-flex flex-column align-items-center justify-content-center mt-2 mb-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <img src="{{ siteSetting()->logo_url }}" alt="Logo" class="login-logo mb-2"
                                            style="width: 100px; height: auto;">
                                    </div>



                                    <h6 class="text-primary">Welcome to Admin Panel</h6>
                                </div>

                            </div>
                            <div class="card-body px-lg-5 pt-0" x-transition.fade>
                                <div class="">
                                    <div class="text-muted mb-4">
                                        <small>Login to Continue</small>
                                    </div>
                                    <form role="form" class="text-start" wire:submit.prevent="login">
                                        <div class="mb-3">
                                            <input type="email" class="form-control" placeholder="Email"
                                                wire:model="email">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" class="form-control" placeholder="Password"
                                                wire:model="password">
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        @if ($errors->has('login_error'))
                                            <span
                                                class="text-center text-danger">{{ $errors->first('login_error') }}</span>
                                        @endif

                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="rememberMe"
                                                wire:model="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary w-100 my-4 mb-4"
                                                wire:loading.attr="disabled" wire:target="login">
                                                <span wire:loading wire:target="login">
                                                    <i class="fas fa-spinner fa-spin me-2"></i> Logging In ...
                                                </span>
                                                <span wire:loading.remove wire:target="login">
                                                    Login
                                                </span>
                                            </button>
                                        </div>

                                        <div class="mb-2 position-relative text-center">
                                            <p
                                                class="text-sm fw-500 mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">
                                                Powered by <a href="{{ url('/') }}" class="text-dark fw-600"
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
            <form role="form" class="text-start" wire:submit.prevent="verifyOtp">
                <div class="modal fade show d-block" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Enter OTP</h5>
                            </div>

                            <div class="modal-body d-flex justify-content-between">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" wire:model="otp.{{ $i }}"
                                        class="form-control text-center mx-1 otp-field" maxlength="1" placeholder="-"
                                        style="width: 50px; font-size: 1.5rem;" oninput="handleOtpInput(this)"
                                        onkeydown="handleOtpBackspace(event, this)">
                                @endfor
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary w-25 my-4 mb-4"
                                    wire:loading.attr="disabled" wire:target="verifyOtp">
                                    <span wire:loading wire:target="verifyOtp">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Verifying ...
                                    </span>
                                    <span wire:loading.remove wire:target="verifyOtp">
                                        Verify OTP
                                    </span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-backdrop fade show"></div>
        @endif
    </main>


</div>

<script>
    function handleOtpInput(el) {
        el.value = el.value.replace(/[^0-9]/g, '');
        if (el.value) {
            const next = el.nextElementSibling;
            if (next && next.classList.contains('otp-field')) {
                next.focus();
            }
        }
    }

    function handleOtpBackspace(e, el) {
        if (e.key === 'Backspace' && !el.value) {
            const prev = el.previousElementSibling;
            if (prev && prev.classList.contains('otp-field')) {
                prev.focus();
            }
        }
    }
</script>
