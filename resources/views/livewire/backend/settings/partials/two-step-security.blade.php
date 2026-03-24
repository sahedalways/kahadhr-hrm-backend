<div class="accordion-item"
     wire:ignore>
    <h6 class="accordion-header">
        <button class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseTwoStep">
            🔒 Two-Step Security
            <span class="arrow">
                <i class="fas fa-chevron-right"></i>
            </span>
        </button>
    </h6>

    <div id="collapseTwoStep"
         class="accordion-collapse collapse"
         data-bs-parent="#securityAccordion">
        <div class="accordion-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="fw-semibold">Enable Two-Step Verification</div>
                    <small class="text-muted">Adds an extra layer of security using OTP</small>
                </div>
                <label class="pro-switch">
                    <input type="checkbox"
                           wire:model.live="twoStepEnabled">
                    <span class="pro-slider"></span>
                </label>
            </div>

            <div x-data="{ enabled: @entangle('twoStepEnabled') }">
                <div x-show="enabled"
                     x-transition>
                    <hr class="my-3">
                    <div>
                        <label class="form-label fw-semibold mb-1">Verification Method</label>
                        <select class="form-select shadow-sm"
                                wire:model.live="verificationMethod">
                            <option value="mobile">📱 Mobile OTP</option>
                            <option value="email">📧 Email OTP</option>
                        </select>
                        <small class="text-muted">Choose how you want to receive verification codes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
