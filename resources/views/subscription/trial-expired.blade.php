<x-layouts.app>
    <div class="d-flex align-items-center justify-content-center"
         style="min-height: 100vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-lg border-0 rounded-4"
                         style="background: #002147; backdrop-filter: blur(10px);">
                        <div class="card-body text-center p-5 text-white">
                            <i class="bi bi-exclamation-triangle-fill text-warning mb-3"
                               style="font-size: 3rem;"></i>

                            <h3 class="fw-bold mb-3 text-white">Trial Period Ended</h3>

                            <p class="mb-4"
                               style="font-size: 1.1rem; color: rgba(255,255,255,0.8);">
                                Your trial period has ended. To continue using all features, please subscribe.
                            </p>

                            <a href="{{ route('company.dashboard.settings.bank-info', ['company' => auth()->user()->company->sub_domain]) }}"
                               class="btn btn-lg fw-bold shadow-sm"
                               style="border-radius: 50px;
          padding: 0.75rem 2rem;
          font-size: 1.1rem;
          color: #ffffff;
          background: linear-gradient(135deg, #2998ff, #1f6ed4);
          transition: all 0.3s ease;
          box-shadow: 0 10px 25px rgba(41, 152, 255, 0.3);"
                               onmouseover="this.style.transform='translateY(-3px) scale(1.03)';
                this.style.boxShadow='0 15px 35px rgba(41,152,255,0.5)';
                this.style.background='linear-gradient(135deg, #1f6ed4, #2998ff)';"
                               onmouseout="this.style.transform='translateY(0) scale(1)';
               this.style.boxShadow='0 10px 25px rgba(41,152,255,0.3)';
               this.style.background='linear-gradient(135deg, #2998ff, #1f6ed4)';">

                                <i class="bi bi-credit-card-2-front-fill me-2"></i> Subscribe Now
                            </a>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-white-50">
                            If you do not subscribe, your access will remain restricted.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
