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
                                    <img src="{{ siteSetting()->logo_url }}" alt="Logo" class="login-logo mb-2"
                                        style="width: 100px; height: auto;">
                                    <h6 class="text-primary">Set Your Password</h6>
                                </div>
                            </div>

                            <div class="card-body px-lg-5 pt-0" x-transition.fade>
                                <form method="POST" action="{{ route('employee.auth.set-password', $token) }}">
                                    @csrf
                                    <!-- Email (readonly) -->
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" value="{{ $employee->email }}"
                                            readonly>
                                    </div>

                                    <!-- New Password -->
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            required>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success w-100 my-4">
                                            Set Password
                                        </button>
                                    </div>

                                    <div class="mb-2 position-relative text-center">
                                        <p
                                            class="text-sm fw-500 mb-2 text-secondary text-border d-inline z-index-2 bg-white px-3">
                                            Powered by <a href="{{ url('/') }}" class="text-dark fw-600"
                                                target="_blank">
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
