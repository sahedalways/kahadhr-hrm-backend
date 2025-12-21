<?php

namespace App\Livewire\Backend\Employee\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CustomEmployeeProfileField;
use App\Models\CustomEmployeeProfileFieldValue;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use App\Models\Department;
use App\Models\Team;

class ProfileSettings extends BaseComponent
{
    use WithFileUploads;
    public $countries = [];
    public $cities = [];
    public $locations = [];

    public $filteredCountries = [];

    public $customFields = [];
    public $customValues = [];
    public $countrySearch = '';

    public $f_name, $l_name, $avatar, $old_avatar;
    public $job_title, $department_id, $team_id;
    public $contract_hours, $salary_type, $start_date, $end_date;

    public $date_of_birth, $street_1, $street_2, $city, $state, $postcode, $country,
        $nationality, $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status, $brp_number, $brp_expiry_date,
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



    /* Load employee info */
    public function mount()
    {
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
        $this->salary_type    = $this->employee->salary_type;
        $this->start_date     = optional($this->employee->start_date)->format('Y-m-d');
        $this->end_date       = optional($this->employee->end_date)->format('Y-m-d');
        $this->old_avatar     = $this->employee->avatar_url;


        $this->date_of_birth             = optional($this->employee->profile?->date_of_birth)->format('Y-m-d');
        $this->street_1                  = $this->employee->profile?->street_1;
        $this->street_2                  = $this->employee->profile?->street_2;
        $this->city                       = $this->employee->profile?->city ?: null;
        $this->state                      = $this->employee->profile?->state ?: null;
        $this->postcode                   = $this->employee->profile?->postcode;
        $this->country                    = $this->employee->profile?->country ?: null;
        $this->nationality                = $this->employee->profile?->nationality;
        $this->home_phone                 = $this->employee->profile?->home_phone;
        $this->mobile_phone               = $this->employee->profile?->mobile_phone;
        $this->personal_email             = $this->employee->profile?->personal_email;
        $this->gender                     = $this->employee->profile?->gender;
        $this->marital_status             = $this->employee->profile?->marital_status;
        $this->tax_reference_number       = $this->employee->profile?->tax_reference_number;
        $this->immigration_status         = $this->employee->profile?->immigration_status ?: null;
        $this->brp_number                 = $this->employee->profile?->brp_number;
        $this->brp_expiry_date            = optional($this->employee->profile?->brp_expiry_date)->format('Y-m-d');
        $this->right_to_work_expiry_date  = optional($this->employee->profile?->right_to_work_expiry_date)->format('Y-m-d');
        $this->passport_number            = $this->employee->profile?->passport_number;
        $this->passport_expiry_date       = optional($this->employee->profile?->passport_expiry_date)->format('Y-m-d');

        $this->departments = Department::where('company_id', $this->employee->company_id)->pluck('name', 'id');
        $this->teams       = Team::where('company_id', $this->employee->company_id)->pluck('name', 'id');

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

        $this->customFields = CustomEmployeeProfileField::where('company_id', $this->employee->company_id)
            ->orderBy('id')
            ->get();

        $this->customValues = $this->employee->customFieldValues
            ->pluck('value', 'field_id')
            ->toArray();
    }

    public function updatedState($value)
    {
        $this->cities = collect($this->locations)
            ->firstWhere('state', $value)['cities'] ?? [];
        $this->city = null;
    }



    /* Save employee profile */
    public function save()
    {
        $validatedData = $this->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',

            'date_of_birth' => 'nullable|date',
            'street_1' => 'nullable|string|max:255',
            'street_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'nationality' => 'nullable|string|max:255',
            'home_phone' => 'nullable|string|max:20',
            'mobile_phone' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:male,female,other',
            'marital_status' => 'nullable|in:single,married',
            'tax_reference_number' => 'nullable|string|max:50',
            'immigration_status' => 'nullable|string|max:255',
            'brp_number' => 'nullable|string|max:50',
            'brp_expiry_date' => 'nullable|date',
            'right_to_work_expiry_date' => 'nullable|date',
            'passport_number' => 'nullable|string|max:50',
            'passport_expiry_date' => 'nullable|date',
        ]);

        // Handle avatar upload
        if ($this->avatar instanceof UploadedFile) {
            $validatedData['avatar'] = uploadImage(
                $this->avatar,
                'image/employee/avatar',
                $this->employee->avatar
            );
        }

        $this->employee->update([
            'f_name' => $validatedData['f_name'],
            'l_name' => $validatedData['l_name'],
            'avatar' => $validatedData['avatar'] ?? $this->employee->avatar,
        ]);



        $this->employee->profile()->updateOrCreate(
            ['emp_id' => $this->employee->id],
            [
                'street_1' => $validatedData['street_1'],
                'street_2' => $validatedData['street_2'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'postcode' => $validatedData['postcode'],
                'country' => $validatedData['country'],
                'nationality' => $validatedData['nationality'],
                'home_phone' => $validatedData['home_phone'],
                'mobile_phone' => $validatedData['mobile_phone'] ?? null,
                'personal_email' => $validatedData['personal_email'],
                'gender' => $validatedData['gender'],
                'marital_status' => $validatedData['marital_status'],
                'tax_reference_number' => $validatedData['tax_reference_number'],
                'immigration_status' => $validatedData['immigration_status'],
                'brp_number' => $validatedData['brp_number'],
                'passport_number' => $validatedData['passport_number'],

                'date_of_birth' => $validatedData['date_of_birth'] ?: null,
                'brp_expiry_date' => $validatedData['brp_expiry_date'] ?: null,
                'right_to_work_expiry_date' => $validatedData['right_to_work_expiry_date'] ?: null,
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
