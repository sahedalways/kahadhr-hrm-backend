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
use App\Models\Notification;
use App\Models\User;
use App\Services\API\VerificationService;
use App\Traits\VerifyPassword;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
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
    public $types;

    public $employees, $employee_id, $title;

    public $nationality = 'British';
    public $share_code;
    public $nationalities = [];

    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $avatar, $avatar_preview, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';


    public $date_of_birth, $street_1, $street_2, $city, $state, $postcode, $country,
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
    public $cities = [];
    public $locations = [];

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



    public function openDocumentModal($docId)
    {
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

    public function updatedCountrySearch($value)
    {
        $this->filteredCountries = collect($this->countries)
            ->filter(function ($c) use ($value) {
                return str_contains(strtolower($c['name']), strtolower($value));
            })
            ->values()
            ->toArray();
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
        $this->employee = Employee::with(
            'documents',
            'documents.documentType',
            'profile',
            'user',
            'company',
            'department',
            'user.teams'
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


        $jsonPath = resource_path('data/countries.json');
        if (file_exists($jsonPath)) {
            $this->countries = json_decode(file_get_contents($jsonPath), true);
        }

        $json = resource_path('data/uk_locations.json');
        if (file_exists($json)) {
            $this->locations = json_decode(file_get_contents($json), true);
        }

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


    public function updatedState($value)
    {
        $this->cities = collect($this->locations)
            ->firstWhere('state', $value)['cities'] ?? [];
        $this->city = null;
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
        $this->street_1 = '';
        $this->street_2 = '';
        $this->city = '';
        $this->state = '';
        $this->postcode = '';
        $this->country = '';
        $this->nationality = '';
        $this->home_phone = '';
        $this->mobile_phone = '';
        $this->personal_email = '';
        $this->gender = '';
        $this->marital_status = '';
        $this->tax_reference_number = '';
        $this->immigration_status = '';
        $this->brp_number = '';
        $this->brp_expiry_date = '';
        $this->right_to_work_expiry_date = '';
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
        $this->team_id = $this->employee->team_id;
        $this->role = $this->employee->role;
        $this->salary_type = $this->employee->salary_type;
        $this->contract_hours = $this->employee->contract_hours;
        $this->is_active = $this->employee->is_active;
        $this->start_date = $this->employee->start_date;
        $this->end_date = $this->employee->end_date;
        $this->phone_no = $this->employee->user->phone_no ?? null;
        $this->avatar_preview = $this->employee->avatar_url;


        $profile = $this->employee->profile;

        if ($profile) {
            $this->street_1 = $profile->street_1;
            $this->street_2 = $profile->street_2;
            $this->city = $profile->city ?: null;
            $this->state = $profile->state ?: null;
            $this->postcode = $profile->postcode;
            $this->country = $profile->country ?: 'United Kingdom';
            $this->home_phone = $profile->home_phone;
            $this->mobile_phone = $profile->mobile_phone;
            $this->personal_email = $profile->personal_email;
            $this->gender = $profile->gender;
            $this->marital_status = $profile->marital_status;
            $this->tax_reference_number = $profile->tax_reference_number;
            $this->immigration_status = $profile->immigration_status ?: null;
            $this->brp_number = $profile->brp_number;
            $this->brp_expiry_date = $profile->brp_expiry_date;
            $this->right_to_work_expiry_date = $profile->right_to_work_expiry_date;
            $this->passport_number = $profile->passport_number;
            $this->passport_expiry_date = $profile->passport_expiry_date;
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
            'title' => 'nullable|in:Mr,Mrs',
            'job_title' => 'nullable|string|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'salary_type' => 'required|in:hourly,monthly',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'avatar' => 'nullable|image|max:2048',


            'street_1' => 'nullable|string|max:255',
            'street_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'nationality' => 'required|string',
            'date_of_birth' => 'required|date',


            'home_phone' => 'nullable|string|max:20',

            'personal_email' => 'nullable|email|max:255',

            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married',

            'tax_reference_number' => 'nullable|string|max:100',

            'immigration_status' => 'nullable|string|max:255',

            'brp_number' => 'nullable|string|max:100',
            'brp_expiry_date' => 'nullable|date',

            'right_to_work_expiry_date' => 'nullable|date',

            'passport_number' => 'nullable|string|max:100',
            'passport_expiry_date' => 'nullable|date',
        ];

        // Contract hours only required if salary_type is hourly
        if ($this->salary_type === 'hourly') {
            $rules['contract_hours'] = 'required|numeric|min:0';
        } else {
            $this->contract_hours = null;
        }

        if ($this->nationality !== 'British') {
            $rules['share_code'] = 'nullable|string|max:20';
        } else {
            $this->share_code = null;
        }

        $validatedData = $this->validate($rules);


        if ($this->avatar instanceof UploadedFile) {
            $this->employee->avatar = uploadImage($this->avatar, 'image/employee/avatar', $this->employee->avatar);
        }

        if ($this->team_id) {
            $team = Team::find($this->team_id);
        }


        // Update employee
        $this->employee->update([
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'job_title' => $this->job_title,
            'title' => $this->title,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth == '' ? null : $this->date_of_birth,
            'share_code' => $this->share_code ?? null,

            'role' => 'employee',
            'salary_type' => $this->salary_type,
            'contract_hours' => $this->contract_hours,
            'is_active' => $this->is_active,
            'end_date' => $this->end_date == '' ? null : $this->end_date,
            'start_date' => $this->start_date == '' ? null : $this->start_date,
            'avatar' => $this->employee->avatar,
        ]);




        $this->employee->profile()->updateOrCreate(
            ['emp_id' => $this->employee->id],
            [
                'date_of_birth' => $this->date_of_birth == '' ? null : $this->date_of_birth,
                'street_1' => $this->street_1,
                'street_2' => $this->street_2,
                'city' => $this->city,
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
                'brp_number' => $this->brp_number,
                'brp_expiry_date' => $this->brp_expiry_date == '' ? null : $this->brp_expiry_date,
                'right_to_work_expiry_date' => $this->right_to_work_expiry_date == '' ? null : $this->right_to_work_expiry_date,
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
                $this->phone_no = $this->new_mobile;
                $this->employee->user->update(['phone_no' => $this->new_mobile]);
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
