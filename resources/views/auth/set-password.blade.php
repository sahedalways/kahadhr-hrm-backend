@extends('components.layouts.login_layout')

@section('title', 'Set Password | ' . siteSetting()->site_title)

@section('content')

    <div>
        <main class="main-content main-content-bg mt-0">
            <div class="page-header min-vh-100"
                style="background-image: url('{{ asset('assets/img/set-password-bg.jpg') }}'); background-size: cover; background-position: center;">

                <span class="mask bg-gradient-dark opacity-6"></span>

                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-7">

                            <div class="card border-0 mb-0">
                                <div class="card-header bg-transparent text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center mt-2 mb-4">
                                        <img src="{{ siteSetting()->logo_url }}" alt="Logo" class="login-logo mb-2"
                                            style="width: 100px; height: auto;">
                                        <h6 class="text-primary">Set Your Password</h6>
                                    </div>
                                </div>

                                <div class="card-body px-lg-5 pt-0" x-transition.fade>
                                    <form method="POST" action="{{ route('employee.auth.set-password', $token) }}">
                                        @csrf

                                        <div class="mb-3">

                                            <input type="email" name="email" class="form-control"
                                                value="{{ $employee->email }}" readonly>
                                        </div>

                                        <!-- New Password -->
                                        <div class="mb-3 position-relative">
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="New Password">
                                            <span id="togglePassword"
                                                class="position-absolute top-50 end-3 translate-middle-y cursor-pointer">
                                                <i class="fas fa-eye"></i>
                                            </span>

                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mb-3 position-relative">
                                            <input type="password" name="password_confirmation" id="password_confirmation"
                                                class="form-control" placeholder="Confirm Password">
                                            <span id="togglePasswordConfirm"
                                                class="position-absolute top-50 end-3 translate-middle-y cursor-pointer">
                                                <i class="fas fa-eye"></i>
                                            </span>

                                            @error('password_confirmation')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>



                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success w-100 my-4">
                                                Set Password
                                            </button>
                                        </div>

                                        <div class="mb-2 position-relative text-center">
                                            <p
                                                class="text-sm fw-500 mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">
                                                Powered by <a href="{{ url('/') }}" class="text-dark fw-600">
                                                    {{ siteSetting()->site_title }}
                                                </a>
                                            </p>
                                        </div>

                                    </form>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle New Password
        const togglePassword = document.querySelector('#togglePassword i');
        const password = document.querySelector('#password');

        togglePassword.parentElement.addEventListener('click', function() {
            if (password.type === 'password') {
                password.type = 'text';
                togglePassword.classList.remove('fa-eye');
                togglePassword.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                togglePassword.classList.remove('fa-eye-slash');
                togglePassword.classList.add('fa-eye');
            }
        });

        // Toggle Confirm Password
        const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm i');
        const passwordConfirm = document.querySelector('#password_confirmation');

        togglePasswordConfirm.parentElement.addEventListener('click', function() {
            if (passwordConfirm.type === 'password') {
                passwordConfirm.type = 'text';
                togglePasswordConfirm.classList.remove('fa-eye');
                togglePasswordConfirm.classList.add('fa-eye-slash');
            } else {
                passwordConfirm.type = 'password';
                togglePasswordConfirm.classList.remove('fa-eye-slash');
                togglePasswordConfirm.classList.add('fa-eye');
            }
        });
    });
</script>
