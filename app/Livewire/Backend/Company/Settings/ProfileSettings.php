<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class ProfileSettings extends BaseComponent
{
    use WithFileUploads;

    public $company_name, $sub_domain, $company_house_number;
    public $company_mobile, $company_email, $business_type;
    public  $registered_domain;
    public $company_logo, $old_company_logo;

    public $countries = [];
    public $citySearch = '';
    public $cities = [];
    public $allCities = [];

    public $stateSearch = '';
    public $states = [];
    public $allStates = [];

    public $address, $street, $city, $state, $postcode, $country;

    public $filteredCountries = [];


    public $company;



    /* Load company info from middleware authenticated company */
    public function mount()
    {
        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found for this user.');
        }


        $this->company_name          = $this->company->company_name;
        $this->sub_domain            = $this->company->sub_domain;
        $this->company_house_number  = $this->company->company_house_number;
        $this->company_email         = $this->company->company_email;
        $this->business_type         = $this->company->business_type;

        $this->registered_domain     = $this->company->registered_domain;
        $this->address     = $this->company->address;
        $this->street     = $this->company->street;
        $this->postcode     = $this->company->postcode;
        $this->country     = $this->company->country ?: 'United Kingdom';
        $this->state     = $this->company->state ?? null;
        $this->city     = $this->company->city ?? null;


        $this->old_company_logo = $this->company->company_logo_url;



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






    /* Save company profile settings */
    public function save()
    {
        $validatedData = $this->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_house_number' => 'required|string|max:255',
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

        $company = $this->company;




        if ($this->company_logo instanceof UploadedFile) {
            $company->company_logo = uploadImage(
                $this->company_logo,
                'image/company/logo',
                $company->company_logo
            );
        }


        $company->update([
            'company_name'          => $this->company_name,
            'company_house_number'  => $this->company_house_number,
            'business_type'         => $this->business_type,

            'registered_domain'     => $this->registered_domain,
            'company_logo'          => $company->company_logo,
            'city'          => $this->city,
            'state'          => $this->state,
            'country'          => $this->country,
            'postcode'          => $this->postcode,
            'street'          => $this->street,
            'address'          => $this->address,

        ]);




        $this->toast('Company Profile Updated Successfully!', 'success');
    }
}
