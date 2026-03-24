@php
    $user = auth()->user();
    $isPrivileged = in_array($user->user_type, ['superAdmin', 'company']);
@endphp
@push('styles')
    <link href="{{ asset('assets/css/security-settings.css') }}"
          rel="stylesheet" />
@endpush


<div class="card border-0 shadow-sm">
    <div class="card-body">

        <h5 class="fw-bold mb-4 text-primary">
            Security Settings
        </h5>

        <div class="accordion"
             id="securityAccordion">


            <div class="accordion-item">

                @if ($isPrivileged)
                    <h6 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapsePassword">

                            <span class="acc-title">
                                🔐 Change Password
                            </span>

                            <span class="arrow">
                                <i class="fas fa-chevron-right"></i>
                            </span>

                        </button>
                    </h6>

                    <div id="collapsePassword"
                         class="accordion-collapse collapse"
                         data-bs-parent="#securityAccordion">
                    @else
                        <h6 class="px-3 pt-3 pb-2 mb-0 fw-semibold text-dark">
                            🔐 Change Password
                        </h6>

                        <div class="accordion-collapse">
                @endif


                <div class="accordion-body">

                    <form class="row g-3"
                          wire:submit.prevent="save">

                        <!-- Old Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Old Password <span class="text-danger">*</span></label>

                            <div class="position-relative">
                                <input type="password"
                                       class="form-control extra-padding shadow-sm"
                                       wire:model.defer="old_password"
                                       id="old_password">
                                <span class="icon-position"
                                      style="cursor:pointer;"
                                      onclick="togglePassword('old_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>

                            @error('old_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>

                            <div class="position-relative">
                                <input type="password"
                                       class="form-control extra-padding shadow-sm"
                                       wire:model.defer="new_password"
                                       id="new_password">
                                <span class="icon-position"
                                      style="cursor:pointer;"
                                      onclick="togglePassword('new_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>

                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="position-relative">


                                <input type="password"
                                       class="form-control extra-padding shadow-sm"
                                       wire:model.defer="confirm_new_password"
                                       id="confirm_new_password">
                                <span class="icon-position"
                                      style="cursor:pointer;"
                                      onclick="togglePassword('confirm_new_password', this)">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            @error('confirm_new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="save">
                                <span wire:loading
                                      wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove
                                      wire:target="save">
                                    Save
                                </span>
                            </button>
                        </div>


                    </form>

                </div>
            </div>
        </div>



        @if ($isPrivileged)
            <livewire:backend.settings.partials.two-step-security />
            <livewire:backend.settings.partials.sessions />
        @endif




    </div>

</div>
</div>


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
