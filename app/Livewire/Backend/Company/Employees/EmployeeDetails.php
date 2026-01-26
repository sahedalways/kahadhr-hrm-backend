<?php

namespace App\Livewire\Backend\Company\Employees;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CustomEmployeeProfileField;
use App\Models\CustomEmployeeProfileFieldValue;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\EmpDocument;
use App\Models\Team;
use App\Traits\Exportable;
use App\Jobs\SendEmployeeInvitation;
use App\Models\Company;
use App\Models\EmergencyContact;
use App\Models\Notification;
use App\Models\User;
use App\Services\API\VerificationService;
use App\Traits\VerifyPassword;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class EmployeeDetails extends BaseComponent
{
    use WithFileUploads;
    use Exportable;
    use VerifyPassword;


    public $departments, $teams;
    public $showAllTeams = false;
    public $editDocId = null;
    public $employee;
    public $contactId;
    public $mode;

    public $documentTypes;

    public $employment_status = 'full-time';
    public $types;

    public $employees, $employee_id, $title;

    public $nationality = 'British';
    public $share_code;
    public $nationalities = [];

    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $avatar, $avatar_preview, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';


    public $date_of_birth, $house_no, $street, $city, $state, $postcode, $country,
        $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status, $brp_number, $brp_expiry_date,
        $right_to_work_expiry_date, $passport_number, $passport_expiry_date;

    public $statusFilter = '';

    public $otp = [],  $showOtpModal = false;
    public $updating_field;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $new_email;
    public $new_mobile;
    public $verification_code;

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

    public  $expires_at, $status, $file_path, $new_file, $emp_id, $doc_type_id, $existingDocument;

    public $confirmDeleteId = null;
    public $docTypes = [];
    public $send_email = false;

    public $modalDocument;

    public $selectedType;
    public $modalFileIndex;


    public $countries = [];
    public $citySearch = '';
    public $cities = [];
    public $allCities = [];

    public $stateSearch = '';
    public $states = [];
    public $allStates = [];

    public $filteredCountries = [];

    public $customFields = [];
    public $customValues = [];

    public $editMode = false;
    public $countrySearch = '';
    public bool $showAllDepartments = false;

    protected $listeners = ['handleDelation'];

    public $selectedDocTypeId;
    public $selectedFileUrl;
    public $selectedExpiresAt;
    public $selectedComment;
    public $selectedDocId;

    public $activeTab = 'overview';

    public $name;
    public $mobile;
    public $address;
    public $relationship;



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




    public function deleteEmergencyContact($contactId)
    {
        $contact = EmergencyContact::findOrFail($contactId);

        $contact->delete();

        // Refresh employee contacts
        $this->employee->refresh();

        $this->toast('Emergency contact deleted successfully.', 'success');
    }


    public function openEmergencyContactModal()
    {
        $this->activeTab = 'emeregeny';
        $this->resetFields();
        $this->dispatch('show-emergency-modal');
    }

    public function resetFields()
    {
        $this->contactId = null;
        $this->mode = 'create';

        $this->name = '';
        $this->mobile = '';
        $this->email = '';
        $this->address = '';
        $this->relationship = '';

        $this->resetErrorBag();
        $this->resetValidation();
    }




    public function openEditEmergencyContactModal($id)
    {
        $this->activeTab = 'emeregeny';
        $contact = EmergencyContact::findOrFail($id);

        $this->contactId = $contact->id;
        $this->name = $contact->name;
        $this->mobile = $contact->mobile;
        $this->email = $contact->email;
        $this->address = $contact->address;
        $this->relationship = $contact->relationship;

        $this->mode = 'edit';
        $this->dispatch('show-emergency-modal');
    }


    public function saveContact()
    {
        $rules = [
            'name'         => 'required|string|max:255',
            'mobile'       => 'required|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'required|string|max:500',
            'relationship' => 'required|string|max:255',
        ];

        $this->validate($rules);

        if ($this->contactId) {

            EmergencyContact::findOrFail($this->contactId)->update([
                'name'         => $this->name,
                'mobile'       => $this->mobile,
                'email'        => $this->email,
                'address'      => $this->address,
                'relationship' => $this->relationship,
            ]);

            $this->toast('Emergency contact updated successfully.', 'success');
        } else {
            // If adding, check limit (max 2)
            if ($this->employee->emergencyContacts()->count() >= 2) {
                $this->toast('You can add only 2 emergency contacts.', 'error');
                return;
            }

            EmergencyContact::create([
                'employee_id'  => $this->employee->id,
                'name'         => $this->name,
                'mobile'       => $this->mobile,
                'email'        => $this->email,
                'address'      => $this->address,
                'relationship' => $this->relationship,
            ]);

            $this->toast('Emergency contact added successfully.', 'success');
        }

        $this->dispatch('closemodal');
        $this->resetFields();
    }



    public function openDocumentModal($docId)
    {
        $this->activeTab = 'documentsSection';
        $this->selectedFileUrl = '';
        $this->selectedExpiresAt = '';
        $this->selectedComment = '';
        $this->selectedDocId = null;


        $this->selectedDocId = $docId;

        if ($docId) {
            $document = EmpDocument::find($docId);
            $this->selectedDocTypeId = $document->doc_type_id;
            $this->selectedFileUrl = $document->document_url;
            $this->selectedExpiresAt = $document->expires_at;
            $this->selectedComment = $document->comment;
        } else {
            $this->selectedFileUrl = '';
            $this->selectedExpiresAt = '';
            $this->selectedComment = '';
        }
    }



    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
        $this->passwordInput = null;
        $this->passwordVerified = false;
    }


    public function mount($employee)
    {
        $this->documentTypes = DocumentType::query()
            ->where('company_id', auth()->user()->company->id)
            ->orderBy('name')
            ->get();


        $this->employee = Employee::with(
            'documents',
            'documents.documentType',
            'profile',
            'user',
            'company',
            'department',
            'user.teams',
            'emergencyContacts'
        )->find($employee);

        $this->docTypes = DocumentType::where('company_id', auth()->user()->company->id)
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();



        if (!$this->employee) {
            sleep(2);
            return redirect()->route(
                'company.dashboard.employees.index',
                ['company' => app('authUser')->company->sub_domain]
            );
        }


        $this->types = DocumentType::all();

        $this->teams = Team::all();


        $jsonPath = resource_path('data/countries.json');
        if (file_exists($jsonPath)) {
            $this->countries = json_decode(file_get_contents($jsonPath), true);
        }



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

        $this->departments = $this->employee->user
            ? $this->employee->user->teams
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->values()
            : collect();


        $this->customFields = CustomEmployeeProfileField::where('company_id', auth()->user()->company->id)
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



    public function updatedShareCode($value)
    {
        $this->share_code = strtoupper($value);
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }


    /* 🔹 Delete document */
    public function deleteDocument($id)
    {
        $document = EmpDocument::find($id);

        if ($document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();
        }


        $this->confirmDeleteId = null;

        $this->toast('Document deleted successfully!', 'info');
        $this->dispatch('closemodal');
        $this->dispatch('reload-page');
        $this->resetInputFields();
    }





    /* Reset input fields */
    public function resetInputFields()
    {
        // Basic info
        $this->f_name = '';
        $this->l_name = '';
        $this->email = '';
        $this->phone_no = '';
        $this->job_title = '';
        $this->title = '';
        $this->department_id = '';
        $this->team_id = '';
        $this->role = '';
        $this->contract_hours = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = 1;


        // Avatar
        $this->avatar_preview = '';
        $this->avatar = '';

        // Profile info fields (ADD YOURS HERE)
        $this->date_of_birth = '';
        $this->house_no = '';
        $this->street = '';
        $this->city = '';
        $this->state = '';
        $this->postcode = '';
        $this->country = '';
        $this->nationality = '';
        $this->home_phone = '';
        $this->address = '';
        $this->mobile_phone = '';
        $this->personal_email = '';
        $this->gender = '';
        $this->marital_status = '';
        $this->tax_reference_number = '';
        $this->immigration_status = '';
        $this->passport_number = '';
        $this->passport_expiry_date = '';

        // Finally
        $this->resetErrorBag();
    }


    public function editProfile($id)
    {
        $this->resetInputFields();
        $this->editMode = true;

        $this->employee = Employee::with('user', 'profile', 'customFieldValues')->find($id);

        if (!$this->employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }


        // Load all relevant fields
        $this->f_name = $this->employee->f_name;
        $this->l_name = $this->employee->l_name;
        $this->title = $this->employee->title;
        $this->nationality = $this->employee->nationality;
        $this->date_of_birth = $this->employee->date_of_birth;
        $this->share_code = $this->employee->share_code ?? null;
        $this->email = $this->employee->email;
        $this->job_title = $this->employee->job_title;
        $this->department_id = $this->employee->department_id;
        $this->employment_status = $this->employee->employment_status;
        $this->team_id = $this->employee->team_id;
        $this->role = $this->employee->role;
        $this->contract_hours = $this->employee->contract_hours;
        $this->is_active = $this->employee->is_active;
        $this->start_date = $this->employee->start_date
            ? Carbon::parse($this->employee->start_date)->format('Y-m-d')
            : null;

        $this->end_date = $this->employee->end_date;
        $this->phone_no = $this->employee->user->phone_no ?? null;
        $this->avatar_preview = $this->employee->avatar_url;


        $profile = $this->employee->profile;

        if ($profile) {
            $this->house_no = $profile->house_no;
            $this->street = $profile->street;
            $this->city = $profile->city ?: null;
            $this->state = $profile->state ?: null;
            $this->address = $profile->address ?: null;
            $this->postcode = $profile->postcode;
            $this->country = $profile->country ?: 'United Kingdom';
            $this->home_phone = $profile->home_phone;
            $this->mobile_phone = $profile->mobile_phone;
            $this->personal_email = $profile->personal_email;
            $this->gender = $profile->gender;
            $this->marital_status = $profile->marital_status;
            $this->tax_reference_number = $profile->tax_reference_number;
            $this->immigration_status = $profile->immigration_status ?: null;
            $this->passport_number = $profile->passport_number;
            $this->passport_expiry_date = $profile->passport_expiry_date
                ? Carbon::parse($profile->passport_expiry_date)->format('Y-m-d')
                : null;
        }

        $this->customFields = CustomEmployeeProfileField::where('company_id', auth()->user()->company->id)
            ->orderBy('id')
            ->get();


        $this->customValues = $this->employee->customFieldValues
            ->pluck('value', 'field_id')
            ->toArray();
    }

    public function sendVerificationLink($employeeId)
    {
        // Find the employee
        $employee = Employee::find($employeeId);

        if (!$employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }


        if ($employee->user) {
            $this->toast('Employee already has an account!', 'info');
            return;
        }


        $employee->invite_token = Str::random(64);
        $employee->invite_token_expires_at = Carbon::now()->addHours(48);
        $employee->save();


        $inviteUrl = route(
            'employee.auth.set-password',
            [
                'company' => app('authUser')->company->sub_domain,
                'token' => $employee->invite_token,
            ]
        );


        SendEmployeeInvitation::dispatch($employee, $inviteUrl)
            ->onConnection('sync')
            ->onQueue('urgent');

        $this->toast('Verification link sent successfully!', 'success');
    }




    /* Toggle status active/former */
    public function toggleStatus($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }

        $employee->is_active = !$employee->is_active;
        $employee->save();
        $this->employee->refresh();

        $this->toast('Status updated successfully!', 'success');
    }

    /* Assign A-Admin */
    public function assignAAdmin($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }

        // Example: toggle A-Admin role
        $employee->is_aadmin = !$employee->is_aadmin;
        $employee->save();

        $this->employee->refresh();

        $this->toast('A-Admin role updated successfully!', 'success');
    }


    public function isProfileComplete()
    {
        $requiredFields = [
            'f_name',
            'l_name',
            'title',
            'job_title',
            'address',
            'house_no',
            'street',
            'start_date',
            'postcode',
            'country',
            'state',
            'nationality',
            'date_of_birth',
            'tax_reference_number',
            'passport_number',
            'passport_expiry_date',
            'employment_status',
            'contract_hours'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->{$field})) {
                return false;
            }
        }

        return true;
    }




    public function updateProfile()
    {
        if (!$this->employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }

        if ($this->nationality == '') {
            $this->nationality = 'British';
        }

        // Validation rules
        $rules = [
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'title' => 'required|in:Mr,Mrs',
            'job_title' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'house_no' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'start_date' => 'required|date',
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
            'employment_status' => 'required|in:part-time,full-time',
            'contract_hours' => 'required|numeric|min:0',
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

        $this->validate(
            array_merge($rules, $customRules),
            [],
            array_merge($attributes, $customAttributes)
        );



        // Update employee
        $this->employee->update([
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'job_title' => $this->job_title,
            'title' => $this->title,
            'street' => $this->street,
            'house_no' => $this->house_no,

            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth == '' ? null : $this->date_of_birth,
            'share_code' => $this->share_code ?? null,
            'contract_hours' => $this->contract_hours !== '' ? $this->contract_hours : null,
            'employment_status' =>  $this->employment_status ?? null,
            'is_active' => $this->is_active,
            'start_date' => $this->start_date == '' ? null : $this->start_date,
        ]);




        $this->employee->profile()->updateOrCreate(
            ['emp_id' => $this->employee->id],
            [
                'date_of_birth' => $this->date_of_birth == '' ? null : $this->date_of_birth,
                'street' => $this->street,
                'house_no' => $this->house_no,
                'city' => $this->city ?? null,
                'address' => $this->address,
                'state' => $this->state,
                'postcode' => $this->postcode,
                'country' => $this->country,
                'nationality' => $this->nationality,
                'home_phone' => $this->home_phone,
                'mobile_phone' => $this->employee->user ? $this->employee->user->phone_no : null,
                'personal_email' => $this->personal_email,
                'gender' => $this->gender,
                'marital_status' => $this->marital_status,
                'tax_reference_number' => $this->tax_reference_number,
                'immigration_status' => $this->immigration_status,
                'passport_expiry_date' => $this->passport_expiry_date == '' ? null : $this->passport_expiry_date,
                'passport_number' => $this->passport_number,
            ]
        );




        if (!empty($this->customValues)) {
            foreach ($this->customValues as $fieldId => $value) {
                $field = CustomEmployeeProfileField::find($fieldId);
                if (!$field) continue;


                CustomEmployeeProfileFieldValue::updateOrCreate(
                    [
                        'employee_id' => $this->employee->id,
                        'field_id' => $fieldId,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }


        $user = $this->employee->user;

        if ($user && $this->team_id) {
            $user->teams()->syncWithoutDetaching([
                $this->team_id => ['is_team_lead' => false]
            ]);
        }

        // Reset form and close modal
        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('closemodal');

        $this->employee->refresh();
        $this->toast('Employee updated successfully!', 'success');
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
                    Rule::unique('employees', 'email')->ignore($this->employee->id),
                ],
            ]);

            $emailExists =
                User::where('email', $this->new_email)->where('id', '!=', $this->employee->user_id)->exists() ||
                Company::where('company_email', $this->new_email)->where('id', '!=', $this->employee->company_id)->exists();

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
                    Rule::unique('users', 'phone_no')->ignore($this->employee->user->id),
                ],
            ]);


            $mobileExists =
                Company::where('company_mobile', $this->new_mobile)
                ->where('id', '!=', $this->employee->company_id)
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
                $this->email = $this->new_email;
                $this->employee->update(['email' => $this->new_email]);
            } else {
                $this->employee->update([
                    'phone_no' => $this->new_mobile,
                ]);
            }


            $this->toast(ucfirst($this->updating_field) . " has been changed successfully.", 'success');
            $this->resetVerificationFields();

            $this->employee->refresh();
            $this->dispatch('closemodal');
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



    public function handleDelation($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            $this->redirect(
                route(
                    'company.dashboard.employees.index',
                    ['company' => app('authUser')->company->sub_domain]
                ),

            );
            return;
        }

        if ($employee->user) {
            $employee->user->delete();
        }

        $employee->delete();

        $this->toast('Employee deleted successfully!', 'success');



        $this->redirect(
            route(
                'company.dashboard.employees.index',
                ['company' => app('authUser')->company->sub_domain]
            ),

        );
    }



    public function toggleDepartments()
    {
        $this->showAllDepartments = ! $this->showAllDepartments;
    }

    public function toggleTeams()
    {
        $this->showAllTeams = !$this->showAllTeams;
    }

    public function notifyEmployee($typeId, $employeeId, $type)
    {
        $docType = DocumentType::findOrFail($typeId);

        $companyId = auth()->user()->company->id;
        $emp    = Employee::with('user')->find($employeeId);


        if ($type === 'expired') {
            $message = "{$docType->name} expired. Please upload a new one.";
        }

        if ($type === 'soon') {
            $message = "{$docType->name} expiring soon. Please update it.";
        }


        $notification = Notification::create([
            'company_id'     => $companyId,
            'user_id'        => $emp->user->id,
            'type'           => 'document_expired',
            'notifiable_id'  => $docType->id,
            'data'           => [
                'message'          => $message,
            ],
        ]);

        event(new NotificationEvent($notification));


        $this->toast('Employee notified successfully', 'success');
    }


    public function openDocModal($docId, $index)
    {
        $this->resetInputFields();
        $this->modalDocument = EmpDocument::with('documentType', 'employee')
            ->find($docId);

        $this->modalFileIndex = $index;
        $this->editDocId = $docId;

        $this->doc_type_id = $this->modalDocument->doc_type_id;
        $this->emp_id      = $this->modalDocument->emp_id;
        $this->expires_at = $this->modalDocument->expires_at;


        $this->dispatch('documentModalOpened');
    }

    public function updateDocument()
    {
        $this->validate([
            'doc_type_id' => 'required',
            'expires_at'  => 'required|date',
            'emp_id'      => 'required|integer|exists:employees,id',
            'new_file'   => 'nullable|file|mimes:pdf|max:20240',
        ]);

        $document = EmpDocument::findOrFail($this->editDocId);



        if ($this->new_file instanceof TemporaryUploadedFile) {


            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }


            $filePath = $this->new_file->store('pdf/employee/documents', 'public');
            $document->file_path = $filePath;
        }

        // ✅ Update fields
        $document->update([
            'doc_type_id' => $this->doc_type_id,
            'expires_at'  => $this->expires_at,
            'emp_id'      => $this->emp_id,
        ]);



        $this->toast('Document updated successfully!', 'success');
        $this->dispatch('closemodal');

        $this->resetInputFields();
    }




    public function sendPasswordResetLink(Employee $employee)
    {
        // Generate a new token
        $employee->invite_token = Str::random(64);
        $employee->invite_token_expires_at = Carbon::now()->addHours(48);
        $employee->save();

        // Generate invite URL
        $inviteUrl = route(
            'employee.auth.set-password',
            [
                'company' => app('authUser')->company->sub_domain,
                'token' => $employee->invite_token,
            ]
        );

        // Dispatch job
        SendEmployeeInvitation::dispatch($employee, $inviteUrl)
            ->onConnection('sync')
            ->onQueue('urgent');

        $this->toast('Password reset link sent successfully!', 'success');
    }




    public function render()
    {
        return view('livewire.backend.company.employees.employee-details');
    }
}
