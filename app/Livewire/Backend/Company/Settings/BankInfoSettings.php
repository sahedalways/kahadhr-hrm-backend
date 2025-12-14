<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyBankInfo;
use Stripe\StripeClient;

class BankInfoSettings extends BaseComponent
{
    public $bank_name;
    public $stripe_payment_method_id;
    public $card_brand;
    public $card_last4;
    public $card_exp_month;
    public $card_exp_year;
    public $card_holder_name;

    public $companyBankInfo;
    public $company;

    protected $listeners = [
        'stripePaymentMethodCreated' => 'save'
    ];

    public function mount()
    {
        $this->company = app('authUser')->company;
        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        $this->companyBankInfo = CompanyBankInfo::where('company_id', $this->company->id)->first();

        if ($this->companyBankInfo) {
            $this->bank_name = $this->companyBankInfo->bank_name;
            $this->stripe_payment_method_id = $this->companyBankInfo->stripe_payment_method_id;


            if ($this->stripe_payment_method_id) {
                $this->fetchStripeCardInfo();
            }
        }
    }

    public function save($paymentMethodId)
    {
        $validatedData = [
            'company_id' => $this->company->id,
            'stripe_payment_method_id' => $paymentMethodId,
        ];

        if ($this->companyBankInfo) {
            $this->companyBankInfo->update($validatedData);
        } else {
            $this->companyBankInfo = CompanyBankInfo::create($validatedData);
        }

        $this->stripe_payment_method_id = $paymentMethodId;
        $this->fetchStripeCardInfo();
        $this->dispatch('closemodal');
        $this->toast('Card Payment Info Saved Successfully!', 'success');
    }

    public function fetchStripeCardInfo()
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentMethod = $stripe->paymentMethods->retrieve($this->stripe_payment_method_id);

        $card = $paymentMethod->card;

        $this->card_brand = $card->brand;
        $this->card_last4 = $card->last4;
        $this->card_exp_month = $card->exp_month;
        $this->card_exp_year = $card->exp_year;
        $this->card_holder_name = $paymentMethod->billing_details->name;
    }

    public function render()
    {
        return view('livewire.backend.company.settings.bank-info-settings');
    }
}
