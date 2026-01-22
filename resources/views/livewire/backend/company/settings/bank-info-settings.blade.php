@php
    $brandColors = [
        'visa' => 'linear-gradient(135deg, #1a1f71, #3a6ad8)',
        'mastercard' => 'linear-gradient(135deg, #ff5f00, #eb001b)',
        'amex' => 'linear-gradient(135deg, #2e77bb, #4fa3dd)',
        'discover' => 'linear-gradient(135deg, #f76b1c, #ffcc00)',
        'default' => 'linear-gradient(135deg, #2c3e50, #34495e)',
    ];

    $cardBg = $brandColors[strtolower($card_brand ?? 'default')] ?? $brandColors['default'];
@endphp

<div>
    <div class="card mt-4 shadow-sm border-0"
         style="border-radius: 20px;">
        <div class="card-body p-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Subscription Payment Method
                </h5>
                @if ($stripe_payment_method_id)
                    <button class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editCardModal">
                        <i class="bi bi-pencil-fill me-1"></i> Update
                    </button>
                @endif
            </div>
            <hr class="my-2">

            <!-- Card Info -->
            @if ($stripe_payment_method_id)
                <div class="card shadow-sm text-white p-4"
                     style="background: {{ $cardBg }}; border-radius: 15px; max-width: 400px;">

                    <!-- Brand & Icon -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold text-uppercase"
                              style="font-size: 1rem;">{{ $card_brand }}</span>
                        <i class="bi bi-credit-card-2-front-fill"
                           style="font-size: 1.5rem; opacity: 0.8;"></i>
                    </div>

                    <!-- Card Number -->
                    <div class="mb-3"
                         style="font-family: 'Courier New', monospace; font-size: 1.4rem; letter-spacing: 2px;">
                        <span>**** **** **** </span>
                        <strong class="text-warning">{{ $card_last4 }}</strong>
                    </div>

                    <!-- Card Holder & Expiry -->
                    <div class="d-flex justify-content-between align-items-end"
                         style="font-size: 0.85rem;">
                        <div>
                            <small class="text-light opacity-75 d-block">Card Holder</small>
                            <strong class="text-uppercase">{{ $card_holder_name ?? 'N/A' }}</strong>
                        </div>

                        <div class="text-end">
                            <small class="text-light opacity-75 d-block">Valid Thru</small>
                            <strong>{{ $card_exp_month }}/{{ $card_exp_year }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Secure Note -->
                <p class="mt-2 mb-0 text-success small text-end">
                    <i class="bi bi-lock-fill me-1"></i> Payment information is secure.
                </p>
            @else
                <!-- No Card -->
                <div class="card border border-dashed rounded text-center p-4 shadow-sm"
                     style="max-width: 400px;">
                    <p class="text-muted mb-3"><i class="bi bi-info-circle-fill me-1"></i>No card saved yet.</p>
                    <button class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editCardModal">
                        <i class="bi bi-plus-circle-fill me-1"></i> Add New Card
                    </button>
                </div>
            @endif

        </div>
    </div>

    <input type="hidden"
           id="stripe-key"
           value="{{ config('services.stripe.key') }}">

    <!-- Edit/Add Card Modal -->
    <div class="modal fade"
         id="editCardModal"
         tabindex="-1"
         aria-labelledby="editCardModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="editCardModalLabel">
                        @if ($stripe_payment_method_id)
                            Edit Card
                        @else
                            Add Card
                        @endif
                    </h5>
                    <button type="button"
                            class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                            data-bs-dismiss="modal"
                            style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-times"
                           style="font-size: 14px; color: #000;"></i>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="bank-form"
                          class="row g-3">


                        <hr>

                        <!-- Bank Name -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Card Holder Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="card-holder-name"
                                   placeholder="Enter Card Holder Name">
                        </div>


                        <!-- Stripe Card Element -->
                        <div class="col-md-6"
                             wire:ignore>
                            <label class="form-label fw-semibold">
                                Card Details <span class="text-danger">*</span>
                            </label>
                            <div id="card-element"
                                 class="form-control p-2"></div>
                            <small id="card-error"
                                   class="text-danger"></small>
                        </div>



                        <div class="d-flex justify-content-end">
                            <button type="button"
                                    id="save-card-btn"
                                    class="btn btn-success">
                                <span id="btn-text">Save Card</span>
                                <span id="btn-loading"
                                      class="d-none">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Submitting...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    <div>
        <div class="row g-3 align-items-center justify-content-between mb-4 mt-5">

            {{-- LEFT: Title --}}
            <div class="col-auto">
                <h5 class="fw-500 text-primary m-0">Invoices</h5>
            </div>

            {{-- RIGHT: Export --}}
            <div class="col-auto d-flex gap-2">
                <button wire:click="exportInvoices('pdf')"
                        class="btn btn-sm btn-white text-primary">
                    <i class="fa fa-file-pdf me-1"></i> PDF
                </button>
                <button wire:click="exportInvoices('excel')"
                        class="btn btn-sm btn-white text-success">
                    <i class="fa fa-file-excel me-1"></i> Excel
                </button>
                <button wire:click="exportInvoices('csv')"
                        class="btn btn-sm btn-white text-info">
                    <i class="fa fa-file-csv me-1"></i> CSV
                </button>
            </div>



        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3 g-4">

                    <div class="col-md-3">
                        <select class="form-select"
                                wire:change="handleDateFilter($event.target.value)">
                            <option value="">All Dates</option>
                            <option value="day">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    @if ($filterDate === 'custom')
                        <div class="col-md-2">
                            <input type="date"
                                   class="form-control"
                                   wire:change="handleDateFrom($event.target.value)"
                                   wire:model="date_from">
                        </div>
                        <div class="col-md-2">
                            <input type="date"
                                   class="form-control"
                                   wire:change="handleDateTo($event.target.value)"
                                   wire:model="date_to">
                        </div>
                    @endif

                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i
                                   class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   placeholder="Search by invoice number"
                                   wire:model="search"
                                   wire:keyup="set('search', $event.target.value)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Invoice #</th>
                                    <th>Billing Period</th>
                                    <th>Employee Fee (£)</th>
                                    <th>Subtotal (£)</th>
                                    <th>VAT (£)</th>
                                    <th>Total (£)</th>
                                    <th>Status</th>
                                    <th>Invoice Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->billing_period_start->format('d M, Y') }} -
                                            {{ $invoice->billing_period_end->format('d M, Y') }}</td>
                                        <td>{{ number_format($invoice->employee_fee, 2) }}</td>
                                        <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                        <td>{{ number_format($invoice->vat, 2) }}</td>
                                        <td>{{ number_format($invoice->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-success text-white">Paid</span>
                                        </td>

                                        <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <a href="#"
                                               wire:click.prevent="downloadInvoice({{ $invoice->id }})"
                                               class="badge bg-primary text-white">
                                                PDF
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10"
                                            class="text-center">No invoices found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Load More --}}
                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore"
                                        class="btn btn-outline-primary rounded-pill px-4 py-2">Load
                                    More</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>




    </div>


</div>



<script src="https://js.stripe.com/v3/"></script>



<script>
    function formatExpiry(input) {
        let value = input.value.replace(/\D/g, '');


        if (value.length > 4) value = value.slice(0, 4);


        if (value.length > 2) {
            value = value.slice(0, 2) + '/' + value.slice(2);
        }

        input.value = value;

        @this.set('expiry_date', value);
    }
</script>


<script>
    document.addEventListener('livewire:init', () => {
        const stripeKey = document.getElementById('stripe-key').value;
        const stripe = Stripe(stripeKey);
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        const btn = document.getElementById('save-card-btn');
        const btnText = document.getElementById('btn-text');
        const btnLoading = document.getElementById('btn-loading');

        btn.addEventListener('click', async () => {

            const cardHolderName = document.getElementById('card-holder-name').value;
            btn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');

            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod({
                type: 'card',
                card: card,
                billing_details: {
                    name: cardHolderName,
                }
            });

            if (error) {
                document.getElementById('card-error').innerText = error.message;


                btn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                return;
            }

            Livewire.dispatch("stripePaymentMethodCreated", {
                paymentMethodId: paymentMethod.id
            });


            setTimeout(() => {
                btn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }, 1000);
        });
    });
</script>
