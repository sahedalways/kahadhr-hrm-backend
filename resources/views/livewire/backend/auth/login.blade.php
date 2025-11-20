<div>
    <main class="main-content main-content-bg mt-0">
        <div class="page-header min-vh-100" style="background-image: url('assets/img/login-bg.jpg');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-7">
                        <div class="card border-0 mb-0">
                            <div class="card-header bg-transparent text-center">
                                <div class="d-flex flex-column align-items-center justify-content-center mt-4 mb-4">
                                    <div class="d-flex flex-column align-items-center justify-content-center mb-2">
                                        <img src="{{ siteSetting()->logo_url }}" alt="Logo" class="login-logo mb-4">
                                        <h4 class="text-dark mb-0">{{ siteSetting()->site_title }}</h4>
                                    </div>


                                    <h6 class="text-primary">Welcome to Admin Panel</h6>
                                </div>

                            </div>
                            <div class="card-body px-lg-5 pt-0" x-transition.fade>
                                <div class="">
                                    <div class="text-muted mb-4">
                                        <small>Login to Continue</small>
                                    </div>
                                    <form role="form" class="text-start">
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
                                            <input class="form-check-input" type="checkbox" id="rememberMe">
                                            <label class="form-check-label" for="rememberMe">Remember me</label>
                                        </div>
                                        <div class="text-center">
                                            <button type="button" wire:click="login"
                                                class="btn btn-primary w-100 my-4 mb-4">Login</button>
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
    </main>
</div>
