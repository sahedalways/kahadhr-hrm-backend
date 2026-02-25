@section('title', 'Employee Login | ' . siteSetting()->site_title)

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

                                <a href="{{ route('company.auth.login', ['company' => $company]) }}"
                                   class="btn btn-sm btn-secondary">
                                    Company Login
                                </a>


                            </div>
                            <div class="card-header bg-transparent text-center mt-5">
                                <div class="d-flex flex-column align-items-center justify-content-center mt-2 mb-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <img src="{{ getCompanyLogoUrl() }}"
                                             alt="Logo"
                                             class="login-logo mb-3"
                                             style="width: 100px; height: auto;">
                                    </div>



                                    <h6 class="text-primary">Welcome to Employee Panel</h6>
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



    </main>


</div>



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
