<?php

namespace App\Livewire\Backend\Employee\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CustomEmployeeProfileField;
use App\Models\CustomEmployeeProfileFieldValue;
use Livewire\WithFileUploads;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ProfileSettings extends BaseComponent
{
    use WithFileUploads;
    public $countries = [];
    public $citySearch = '';
    public $cities = [];
    public $allCities = [];

    public $stateSearch = '';
    public $states = [];
    public $allStates = [];

    public $documentTypes;
    public $locations = [];

    public $filteredCountries = [];

    public $nationality = 'British';
    public $share_code;
    public $nationalities = [];

    public $customFields = [];
    public $customValues = [];
    public $countrySearch = '';

    public $f_name, $l_name, $employment_status, $avatar, $old_avatar, $title;
    public $job_title, $department_id, $team_id;
    public $contract_hours, $salary_type, $start_date, $end_date;

    public $date_of_birth, $house_no, $address, $street, $city, $state, $postcode, $country,
        $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status,
        $right_to_work_expiry_date, $passport_number, $passport_expiry_date;

    public $employee;
    public $departments = [];
    public $teams = [];

    public $genderOptions = ['male', 'female', 'other'];

    public $maritalOptions = ['single', 'married'];
    public $immigrationOptions = [
        "British Citizen",
        "Indefinite Leave to Remain (ILR)",
        "Pre-Settled Status",
        "Settled Status",
        "Skilled Worker Visa",
        "Student Visa (Tier 4)",
        "Graduate Visa",
        "Health and Care Worker Visa",
        "Family Visa",
        "Spouse Visa",
        "Start-up Visa",
        "Innovator Visa",
        "Temporary Work Visa",
        "Youth Mobility Scheme Visa",
        "Asylum Seeker",
        "Refugee Status",
        "Other",
    ];



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




    /* Load employee info */
    public function mount()
    {
        $this->documentTypes = DocumentType::query()
            ->where('company_id', auth()->user()->employee->company_id)
            ->orderBy('name')
            ->get();



        $this->employee = auth()->user()->employee;

        if (!$this->employee) {
            abort(403, 'Employee profile not found.');
        }

        $this->f_name        = $this->employee->f_name;
        $this->l_name        = $this->employee->l_name;
        $this->job_title     = $this->employee->job_title;
        $this->department_id = $this->employee->department_id;
        $this->team_id       = $this->employee->team_id;
        $this->contract_hours = $this->employee->contract_hours;
        $this->employment_status = $this->employee->employment_status;
        $this->salary_type    = $this->employee->salary_type;
        $this->start_date     = optional($this->employee->start_date)->format('Y-m-d');
        $this->end_date       = optional($this->employee->end_date)->format('Y-m-d');
        $this->old_avatar     = $this->employee->avatar_url;
        $this->nationality = $this->employee->nationality;
        $this->date_of_birth = $this->employee->date_of_birth ?? null;
        $this->share_code = $this->employee->share_code ?? null;



        $this->house_no                  = $this->employee->profile?->house_no;
        $this->street                  = $this->employee->profile?->street;
        $this->city                       = $this->employee->profile?->city ?: null;
        $this->state                      = $this->employee->profile?->state ?: null;
        $this->postcode                   = $this->employee->profile?->postcode;
        $this->country                    = $this->employee->profile?->country ?: null;
        $this->home_phone                 = $this->employee->profile?->home_phone;
        $this->title                 = $this->employee->title;
        $this->personal_email             = $this->employee->profile?->personal_email;
        $this->gender                     = $this->employee->profile?->gender;
        $this->marital_status             = $this->employee->profile?->marital_status;
        $this->tax_reference_number       = $this->employee->profile?->tax_reference_number;
        $this->tax_reference_number       = $this->employee->profile?->tax_reference_number;
        $this->address         = $this->employee->profile?->address ?: null;

        $this->passport_number            = $this->employee->profile?->passport_number;
        $this->passport_expiry_date       = optional($this->employee->profile?->passport_expiry_date)->format('Y-m-d');


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


        $this->customFields = CustomEmployeeProfileField::where('company_id', $this->employee->company_id)
            ->orderBy('id')
            ->get();

        $this->customValues = $this->employee->customFieldValues
            ->pluck('value', 'field_id')
            ->toArray();


        $this->nationalities = [
            "British",
            "Afghan",
            "Albanian",
            "Algerian",
            "American",
            "Andorran",
            "Angolan",
            "Antiguans",
            "Argentinean",
            "Armenian",
            "Australian",
            "Austrian",
            "Azerbaijani",
            "Bahamian",
            "Bahraini",
            "Bangladeshi",
            "Barbadian",
            "Belarusian",
            "Belgian",
            "Belizean",
            "Beninese",
            "Bhutanese",
            "Bolivian",
            "Bosnian",
            "Botswanan",
            "Brazilian",
            "Bruneian",
            "Bulgarian",
            "Burkinabe",
            "Burmese",
            "Burundian",
            "Cambodian",
            "Cameroonian",
            "Canadian",
            "Cape Verdean",
            "Central African",
            "Chadian",
            "Chilean",
            "Chinese",
            "Colombian",
            "Comoran",
            "Congolese",
            "Costa Rican",
            "Croatian",
            "Cuban",
            "Cypriot",
            "Czech",
            "Danish",
            "Djiboutian",
            "Dominican",
            "Dutch",
            "East Timorese",
            "Ecuadorean",
            "Egyptian",
            "Emirati",
            "Equatorial Guinean",
            "Eritrean",
            "Estonian",
            "Ethiopian",
            "Fijian",
            "Finnish",
            "French",
            "Gabonese",
            "Gambian",
            "Georgian",
            "German",
            "Ghanaian",
            "Greek",
            "Grenadian",
            "Guatemalan",
            "Guinean",
            "Guinea-Bissauan",
            "Guyanese",
            "Haitian",
            "Honduran",
            "Hungarian",
            "Icelander",
            "Indian",
            "Indonesian",
            "Iranian",
            "Iraqi",
            "Irish",
            "Israeli",
            "Italian",
            "Ivorian",
            "Jamaican",
            "Japanese",
            "Jordanian",
            "Kazakhstani",
            "Kenyan",
            "Kittian and Nevisian",
            "Kuwaiti",
            "Kyrgyz",
            "Laotian",
            "Latvian",
            "Lebanese",
            "Liberian",
            "Libyan",
            "Liechtensteiner",
            "Lithuanian",
            "Luxembourger",
            "Macedonian",
            "Malagasy",
            "Malawian",
            "Malaysian",
            "Maldivian",
            "Malian",
            "Maltese",
            "Marshallese",
            "Mauritanian",
            "Mauritian",
            "Mexican",
            "Micronesian",
            "Moldovan",
            "Monacan",
            "Mongolian",
            "Moroccan",
            "Mozambican",
            "Namibian",
            "Nauruan",
            "Nepalese",
            "New Zealander",
            "Nicaraguan",
            "Nigerian",
            "Nigerien",
            "North Korean",
            "Northern Irish",
            "Norwegian",
            "Omani",
            "Pakistani",
            "Palauan",
            "Panamanian",
            "Papua New Guinean",
            "Paraguayan",
            "Peruvian",
            "Polish",
            "Portuguese",
            "Qatari",
            "Romanian",
            "Russian",
            "Rwandan",
            "Saint Lucian",
            "Salvadoran",
            "Samoan",
            "San Marinese",
            "Sao Tomean",
            "Saudi",
            "Scottish",
            "Senegalese",
            "Serbian",
            "Seychellois",
            "Sierra Leonean",
            "Singaporean",
            "Slovakian",
            "Slovenian",
            "Solomon Islander",
            "Somali",
            "South African",
            "South Korean",
            "South Sudanese",
            "Spanish",
            "Sri Lankan",
            "Sudanese",
            "Surinamer",
            "Swazi",
            "Swedish",
            "Swiss",
            "Syrian",
            "Taiwanese",
            "Tajik",
            "Tanzanian",
            "Thai",
            "Togolese",
            "Tongan",
            "Trinidadian or Tobagonian",
            "Tunisian",
            "Turkish",
            "Turkmen",
            "Tuvaluan",
            "Ugandan",
            "Ukrainian",
            "Uruguayan",
            "Uzbekistani",
            "Vanuatuan",
            "Venezuelan",
            "Vietnamese",
            "Welsh",
            "Yemenite",
            "Zambian",
            "Zimbabwean",
        ];
    }

    public function getRightToWorkStatusProperty()
    {
        $shareCodeType = $this->documentTypes->firstWhere('name', 'Share Code');

        $latestShareDoc = null;
        $daysLeft = null;

        if ($shareCodeType) {
            $latestShareDoc = $this->employee
                ->documents()
                ->where('doc_type_id', $shareCodeType->id)
                ->latest('created_at')
                ->first();

            if ($latestShareDoc && $latestShareDoc->expires_at) {
                $expiresAt = \Carbon\Carbon::parse($latestShareDoc->expires_at);
                $daysLeft = now()->diffInDays($expiresAt, false);
            }
        }

        return [
            'doc' => $latestShareDoc,
            'daysLeft' => $daysLeft,
        ];
    }



    public function updatedShareCode($value)
    {
        $this->share_code = strtoupper($value);
    }




    /* Save employee profile */
    public function save()
    {

        if ($this->nationality == '') {
            $this->nationality = 'British';
        }
        $rules = [
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'title' => 'required|in:Mr,Mrs',
            'address' => 'required|string|max:255',
            'house_no' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'postcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'nationality' => 'required|string',
            'date_of_birth' => 'required|date',
            'home_phone' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married',
            'tax_reference_number' => 'required|string|max:100',
            'immigration_status' => 'nullable|string|max:255',
            'passport_number' => 'required|string|max:100',
            'passport_expiry_date' => 'required|date',
        ];


        if ($this->nationality !== 'British') {
            $rules['share_code'] = 'nullable|string|max:20';
        } else {
            $this->share_code = null;
        }


        $attributes = [
            'email'                    => 'Email Address',
            'phone_no'                 => 'Phone Number',
            'f_name'                   => 'First Name',
            'l_name'                   => 'Last Name',
            'title'                    => 'Title',
            'job_title'                => 'Job Title',
            'contract_hours'           => 'Contract Hours (Weekly)',
            'start_date'               => 'Employment Start Date',
            'house_no'                 => 'House Number',
            'street'                 => 'Street',
            'city'                     => 'City',
            'state'                    => 'State',
            'address'                    => 'Current Address',
            'postcode'                 => 'Postcode',
            'country'                  => 'Country',
            'nationality'              => 'Nationality',
            'date_of_birth'            => 'Date of Birth',
            'home_phone'               => 'Home Phone',
            'personal_email'           => 'Personal Email',
            'gender'                   => 'Gender',
            'marital_status'           => 'Marital Status',
            'tax_reference_number'     => 'Tax Reference Number',
            'immigration_status'       => 'Immigration Status / Visa Type',
            'passport_number'          => 'Passport Number',
            'passport_expiry_date'     => 'Passport Expiry Date',
            'employment_status'        => 'Employment Status',
            'share_code'               => 'Share Code',
        ];


        $customRules = [];
        $customAttributes = [];

        foreach ($this->customFields as $field) {
            if ($field->required) {
                $customRules["customValues.{$field->id}"] = 'required';
                $customAttributes["customValues.{$field->id}"] = $field->name;
            }
        }

        $validatedData = $this->validate(
            array_merge($rules, $customRules),
            [],
            array_merge($attributes, $customAttributes)
        );


        $this->employee->update([
            'f_name'         => $validatedData['f_name'],
            'l_name'         => $validatedData['l_name'],
            'nationality'    => $validatedData['nationality'],
            'share_code'     => $validatedData['share_code'] ?? null,
            'date_of_birth'  => $validatedData['date_of_birth'] ?? null,
        ]);



        $this->employee->profile()->updateOrCreate(
            ['emp_id' => $this->employee->id],
            [
                'house_no' => $validatedData['house_no'],
                'address' => $validatedData['address'],
                'street' => $validatedData['street'],
                'city' => $validatedData['city'] ?? null,
                'state' => $validatedData['state'],
                'postcode' => $validatedData['postcode'],
                'country' => $validatedData['country'],
                'home_phone' => $validatedData['home_phone'],
                'mobile_phone' => $validatedData['mobile_phone'] ?? null,
                'personal_email' => $validatedData['personal_email'],
                'gender' => $validatedData['gender'],
                'marital_status' => $validatedData['marital_status'],
                'tax_reference_number' => $validatedData['tax_reference_number'],
                'immigration_status' => $validatedData['immigration_status'],
                'passport_number' => $validatedData['passport_number'],

                'passport_expiry_date' => $validatedData['passport_expiry_date'] ?: null,
            ]
        );


        if (!empty($this->customValues)) {
            foreach ($this->customValues as $fieldId => $value) {
                $field = CustomEmployeeProfileField::find($fieldId);
                if (!$field) continue;


                CustomEmployeeProfileFieldValue::updateOrCreate(
                    [
                        'employee_id' => auth()->user()->employee->id,
                        'field_id' => $fieldId,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }



        $this->toast('Employee Profile Updated Successfully!', 'success');
    }
}
