<x-layouts.app>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-0 rounded-4"
                     style="background: rgba(10, 25, 70, 0.9); backdrop-filter: blur(10px);">
                    <div class="card-body text-center p-5 text-white">
                        <i class="bi bi-exclamation-triangle-fill text-warning mb-3"
                           style="font-size: 3rem;"></i>

                        <h3 class="fw-bold mb-3 text-white">Trial Period Ended</h3>

                        <p class="mb-4"
                           style="font-size: 1.1rem; color: rgba(255,255,255,0.8);">
                            Your trial period has ended. To continue using all features, please subscribe.
                        </p>

                        <a href="{{ route('company.dashboard.settings.bank-info', ['company' => auth()->user()->company->sub_domain]) }}"
                           class="btn btn-light btn-lg fw-bold shadow-sm"
                           style="border-radius: 50px; padding: 0.75rem 2rem; font-size: 1.1rem; color: #bbc1d4; background: linear-gradient(135deg, #0058fc, #2a5298);">
                            <i class="bi bi-credit-card-2-front-fill me-2"></i> Subscribe Now
                        </a>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-white-50">If you do not subscribe, your access will remain
                        restricted.</small>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
