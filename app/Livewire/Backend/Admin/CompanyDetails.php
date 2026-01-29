<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Models\User;
use App\Services\API\VerificationService;
use App\Services\CompanyService;
use App\Traits\ToastTrait;
use Carbon\Carbon;
use Livewire\WithPagination;
use Stripe\StripeClient;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class CompanyDetails extends BaseComponent
{
    use ToastTrait, WithPagination;
    use WithFileUploads;

    public  $company, $company_id, $company_name, $business_type, $company_house_number, $address_contact_info, $company_email, $company_mobile, $company_logo, $company_logo_preview, $registered_domain, $calendar_year = 'english', $billing_plan_id, $subscription_status, $subscription_start, $subscription_end, $status;


    public $details;
    public $stripeCard;
    public $search = '';
    public $start_date;
    public $end_date;

    public $activeCount = 0;
    public $formerCount = 0;

    public $editMode = false;

    public $otp = [],  $showOtpModal = false;
    public $updating_field;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $new_email;
    public $new_mobile;
    public $verification_code;
    public $activeTab = 'overview';
    protected $companyService;

    public $countries = [];
    public $citySearch = '';
    public $cities = [];
    public $allCities = [];

    public $stateSearch = '';
    public $states = [];
    public $allStates = [];

    public $address, $street, $city, $state, $postcode, $country;

    public $filteredCountries = [];

    protected $listeners = ['deleteCompany', 'openModal', 'tick'];

    public function boot(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }






    public function updatedCountrySearch($value)
    {
        $this->filteredCountries = collect($this->countries)
            ->filter(function ($c) use ($value) {
                return str_contains(strtolower($c['name']), strtolower($value));
            })
            ->values()
            ->toArray();
    }

    public function updatedCitySearch($value)
    {
        if ($value === '') {
            $this->cities = $this->allCities;
            return;
        }

        $this->cities = collect($this->allCities)
            ->filter(
                fn($city) =>
                str_contains(strtolower($city), strtolower($value))
            )
            ->values()
            ->toArray();
    }


    public function updatedStateSearch($value)
    {
        if ($value === '') {
            $this->states = $this->allStates;
            return;
        }

        $this->states = collect($this->allStates)
            ->filter(
                fn($s) =>
                str_contains(strtolower($s['name']), strtolower($value))
            )
            ->values()
            ->toArray();
    }




    public function updatedCountry($value)
    {
        $this->state = null;
        $this->city = null;

        $this->stateSearch = '';
        $this->citySearch  = '';

        $this->states = [];
        $this->allStates = [];
        $this->cities = [];
        $this->allCities = [];

        $this->loadStates($value);
    }




    public function loadStates($country)
    {
        if (!$country) {
            return;
        }

        $cacheKey = 'states.' . md5($country);

        $this->allStates = Cache::remember(
            $cacheKey,
            now()->addDays(7),
            function () use ($country) {

                $response = Http::post(
                    'https://countriesnow.space/api/v0.1/countries/states',
                    ['country' => $country]
                );

                if ($response->successful()) {
                    return $response->json()['data']['states'] ?? [];
                }

                return [];
            }
        );


        $this->states = $this->allStates;
    }



    public function updatedState($state)
    {
        $this->city = null;
        $this->citySearch = '';

        $cacheKey = 'cities.' . md5($this->country . '_' . $state);

        $this->allCities = Cache::remember(
            $cacheKey,
            now()->addDays(7),
            function () use ($state) {
                $response = Http::post(
                    'https://countriesnow.space/api/v0.1/countries/state/cities',
                    [
                        'country' => $this->country,
                        'state'   => $state,
                    ]
                );

                return $response->successful()
                    ? $response->json()['data']
                    : [];
            }
        );


        $this->cities = $this->allCities;
    }





    public function mount($id)
    {
        $this->company_id = $id;
        $this->loadCompanyDetails();
    }

    public function loadCompanyDetails()
    {
        $this->details = Company::with(['bankInfos', 'employees', 'billingPlan', 'calendarYearSetting'])
            ->findOrFail($this->company_id);



        $paymentMethodId = $this->details->bankInfos?->first()?->stripe_payment_method_id;

        $this->stripeCard = $paymentMethodId ? $this->fetchStripeCardInfo($paymentMethodId) : null;

        $this->activeCount = $this->details->employees->where('is_active', 1)->count();
        $this->formerCount = $this->details->employees->where('is_active', 0)->count();


        $this->country = 'United Kingdom';

        $this->countries = Cache::remember(
            'countries.list',
            now()->addDays(7),
            function () {
                $response = Http::get(
                    'https://countriesnow.space/api/v0.1/countries/flag/images'
                );

                return $response->successful()
                    ? $response->json()['data']
                    : [];
            }
        );

        $this->loadStates($this->country);



        $this->filteredCountries = $this->countries;
    }




    protected function fetchStripeCardInfo($paymentMethodId)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentMethod = $stripe->paymentMethods->retrieve($paymentMethodId);

        $card = $paymentMethod->card;

        return [
            'brand' => $card->brand,
            'last4' => $card->last4,
            'exp_month' => $card->exp_month,
            'exp_year' => $card->exp_year,
            'holder_name' => $paymentMethod->billing_details->name,
        ];
    }



    public function resetInputFields()
    {
        $this->company = null;
        $this->company_name = '';
        $this->business_type = '';
        $this->address_contact_info = '';
        $this->company_email = '';
        $this->company_mobile = '';
        $this->calendar_year = 'english';
        $this->company_logo = null;
        $this->company_logo_preview = null;
        $this->address = '';
        $this->street = '';
        $this->city = '';
        $this->state = '';
        $this->postcode = '';

        $this->country = 'United Kingdom';
        $this->country = '';
        $this->resetErrorBag();
    }





    public function manageCompanyProfile($id)
    {
        $this->editMode = true;
        $this->company = $this->companyService->getCompany($id);

        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }


        $this->company->load('calendarYearSetting');

        $this->company_name = $this->company->company_name;
        $this->company_house_number = $this->company->company_house_number;
        $this->country = $this->company->country ?? 'United Kingdom';
        $this->state = $this->company->state ?? null;
        $this->city = $this->company->city ?? null;
        $this->address = $this->company->address ?? null;
        $this->postcode = $this->company->postcode ?? null;
        $this->street = $this->company->street ?? null;
        $this->company_email = $this->company->company_email;
        $this->company_mobile = $this->company->company_mobile;
        $this->business_type = $this->company->business_type;
        $this->address_contact_info = $this->company->address_contact_info;
        $this->registered_domain = $this->company->registered_domain;
        $this->calendar_year = $this->company->calendarYearSetting->calendar_year ?? null;
        $this->subscription_status = $this->company->subscription_status;
        $this->subscription_start = $this->company->subscription_start
            ? Carbon::parse($this->company->subscription_start)->format('Y-m-d')
            : null;

        $this->subscription_end = $this->company->subscription_end
            ? Carbon::parse($this->company->subscription_end)->format('Y-m-d')
            : null;
        $this->status = $this->company->status == 'Active';
        $this->company_logo_preview = $this->company->company_logo_url;
    }




    public function update()
    {
        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }


        $this->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_house_number' => 'required|string|max:255',
            'company_email' => ['required', 'email', Rule::unique('companies', 'company_email')->ignore($this->company->id)],
            'company_mobile' => ['required', 'string', 'max:20', Rule::unique('companies', 'company_mobile')->ignore($this->company->id)],
            'business_type' => 'required|string|max:255',
            'registered_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,6}$/',
                Rule::unique('companies', 'registered_domain')->ignore($this->company->id),
            ],
            'company_logo' => 'nullable|image|max:2048',

            'address' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
        ]);



        $data = [
            'company_name' => $this->company_name,
            'company_house_number' => $this->company_house_number,
            'company_email' => $this->company_email,
            'company_mobile' => $this->company_mobile,
            'business_type' => $this->business_type,
            'registered_domain' => $this->registered_domain,
            'address' => $this->address,
            'street' => $this->street,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'state' => $this->state,
            'country' => $this->country ?: 'United Kingdom',
        ];

        if ($this->company_logo) {
            $data['company_logo'] = $this->company_logo;
        }

        $this->companyService->updateCompany($this->company, $data);




        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('closemodal');
        $this->toast('Company updated successfully!', 'success');
        $this->details = Company::with(['bankInfos', 'employees', 'billingPlan', 'calendarYearSetting'])
            ->findOrFail($this->company_id);
    }


    /* Delete company */
    public function deleteCompany($id)
    {
        $company = Company::findOrFail($id);

        if ($company->user) {
            $company->user->delete();
        }

        $company->delete();


        $this->toast('Company deleted successfully!', 'success');
        return redirect()->route('super-admin.companies');
    }


    public function getInvoicesProperty()
    {
        $query = $this->details->invoices()->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%');
        }

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        return $query->get();
    }


    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }



    public function requestVerification($field, VerificationService $verificationService)
    {
        $this->updating_field = $field;

        if ($field === 'email') {
            $this->validate([
                'new_email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('companies', 'company_email')->ignore($this->company->id),
                ],
            ]);

            $emailExists =
                User::where('email', $this->new_email)->where('id', '!=', $this->company->user_id)->exists();

            if ($emailExists) {
                $this->toast('This email is already in use.', 'error');
                return;
            }

            $target = $this->new_email;
        } else {


            $this->validate([
                'new_mobile' => [
                    'required',
                    'string',
                    'min:10',
                    'max:20',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users', 'phone_no')->ignore($this->company->user_id),
                ],
            ]);


            $mobileExists =
                Company::where('company_mobile', $this->new_mobile)
                ->where('id', '!=', $this->company->id)
                ->exists();

            if ($mobileExists) {
                $this->toast('This phone number is already in use.', 'error');
                return;
            }

            $target = $this->new_mobile;
        }




        $sent = false;
        if ($field === 'email') {
            $sent = $verificationService->sendEmailOtp($target, null);
        } else {
            $sent = $verificationService->sendPhoneOtp($target, null);
        }

        if ($sent) {
            $this->toast("Verification code sent to your {$field}.", 'info');

            $this->code_sent = true;
            $this->startOtpCooldown();
        } else {
            $this->toast("Failed to send OTP", 'error');
        }
    }


    public function startOtpCooldown()
    {
        $this->otpCooldown = 120;

        $this->dispatch('start-otp-countdown');
    }


    public function canResendOtp()
    {
        return $this->otpCooldown <= 0;
    }

    public function tick()
    {
        if ($this->otpCooldown > 0) {
            $this->otpCooldown--;
        }
    }




    public function verifyAndUpdate(VerificationService $verificationService)
    {

        $code = implode('', $this->otp);
        $this->verification_code = $code;


        $this->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $target = $this->updating_field === 'email' ? $this->new_email : $this->new_mobile;

        try {

            $verificationService->verifyOtp($target, $this->verification_code);


            if ($this->updating_field === 'email') {
                $this->company_email = $this->new_email;
                $this->company->update(['company_email' => $this->new_email]);
            } else {
                $this->company_mobile = $this->new_mobile;
                $this->company->user->update(['phone_no' => $this->new_mobile]);
                $this->company->update(['company_mobile' => $this->new_mobile]);
            }


            $this->toast(ucfirst($this->updating_field) . " has been changed successfully.", 'success');
            $this->resetVerificationFields();
            $this->dispatch('closemodal');
            $this->details = Company::with(['bankInfos', 'employees', 'billingPlan', 'calendarYearSetting'])
                ->findOrFail($this->company_id);
        } catch (\Exception $e) {

            $this->toast("OTP does not match.", 'error');
        }
    }


    public function resetVerificationFields()
    {
        $this->new_email = null;
        $this->new_mobile = null;
        $this->otp = [];
        $this->verification_code = null;

        $this->updating_field = null;
        $this->code_sent = false;
        $this->otpCooldown = 0;
    }

    public function toggleStatus($id)
    {
        $company = Company::find($id);

        if (!$company) {
            $this->toast('Company not found!', 'error');
            return;
        }

        $company->status = $company->status == 'Active' ? "Inactive" : "Active";
        $company->save();

        $this->toast('Status updated successfully!', 'success');

        $this->details = Company::with(['bankInfos', 'employees', 'billingPlan', 'calendarYearSetting'])
            ->findOrFail($this->company_id);
    }


    public function render()
    {
        return view('livewire.backend.admin.company-details', [
            'invoices' => $this->invoices,
        ]);
    }
}
