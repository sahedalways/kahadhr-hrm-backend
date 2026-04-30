<x-layouts.app>
    <div class="d-flex align-items-center justify-content-center"
         style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg border-0 rounded-4"
                         style="background: #2b0a0a; backdrop-filter: blur(10px);">
                        <div class="card-body text-center p-5 text-white">

                            <i class="bi bi-x-circle-fill text-danger mb-3"
                               style="font-size: 3rem;"></i>

                            <h3 class="fw-bold mb-3 text-white">
                                Subscription Suspended
                            </h3>

                            <p class="mb-4"
                               style="font-size: 1.1rem; color: rgba(255,255,255,0.8);">
                                Your subscription has been suspended due to multiple failed payment attempts.
                                Please update your payment method to restore access.
                            </p>

                            <a href="{{ route('company.dashboard.settings.bank-info', ['company' => auth()->user()->company->sub_domain]) }}"
                               class="btn btn-lg fw-bold shadow-sm"
                               style="border-radius: 50px;
                                      padding: 0.75rem 2rem;
                                      font-size: 1.1rem;
                                      color: #ffffff;
                                      background: linear-gradient(135deg, #ff4d4d, #cc0000);
                                      transition: all 0.3s ease;">

                                <i class="bi bi-credit-card-2-front-fill me-2"></i>
                                Update Payment Method
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-white-50">
                            Your access will remain restricted until payment is successful.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
