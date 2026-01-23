<?php

namespace App\Livewire\Backend\Company\Employees;

use App\Jobs\SendEmployeeInvitation;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Models\CustomEmployeeProfileField;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;


class UsersIndex extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $countries = [];
    public $cities = [];
    public $locations = [];

    public $filteredCountries = [];

    public $nationality = 'British';
    public $share_code;
    public $nationalities = [];


    public $countrySearch = '';

    public $employees, $employee, $employee_id, $title;
    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $avatar, $avatar_preview, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';


    public $date_of_birth, $street_1, $street_2, $city, $state, $postcode, $country,
        $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status, $brp_number, $brp_expiry_date,
        $right_to_work_expiry_date, $passport_number, $passport_expiry_date;


    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = 'active';

    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;
    public $search;
    public $otp = [],  $showOtpModal = false;
    public $updating_field;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $new_email;
    public $new_mobile;
    public $verification_code;

    public $departments, $teams;
    public $csv_file;

    public $customFields = [];
    public $customValues = [];

    public $addMethod = 'manual';

    protected $listeners = ['deleteEmployee', 'sortUpdated' => 'handleSort', 'openModal', 'tick'];

    public $customField = [
        'label' => '',
        'type' => 'text',
        'options' => '',
        'required' => false,
    ];


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



    protected $rulesCsv = [
        'csv_file' => 'required|file|mimes:csv,txt|max:2048',
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

    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }



    public function updatedState($value)
    {
        $this->cities = collect($this->locations)
            ->firstWhere('state', $value)['cities'] ?? [];
        $this->city = null;
    }


    public function render()
    {
        return view('livewire.backend.company.employees.users-index', [
            'infos' => $this->loaded
        ]);

    }


    public function mount()
    {
        $this->loaded = collect();
        $this->departments = Department::all();
        $this->teams = Team::all();

        $jsonPath = resource_path('data/countries.json');
        if (file_exists($jsonPath)) {
            $this->countries = json_decode(file_get_contents($jsonPath), true);
        }


        $this->loadMore();

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

        $this->customFields = CustomEmployeeProfileField::where('company_id', auth()->user()->company->id)
            ->orderBy('id')
            ->get();



    $this->nationalities = [
        'British',
        'Bangladeshi',
        'Indian',
        'Pakistani',
        'Sri Lankan',
        'Nepalese',
        'Afghan',
        'Chinese',
        'Japanese',
        'Korean',
        'Thai',
        'Malaysian',
        'Indonesian',
        'Filipino',
        'Saudi',
        'UAE',
        'Qatari',
        'Kuwaiti',
        'Omani',
        'Egyptian',
        'Nigerian',
        'Kenyan',
        'South African',
        'American',
        'Canadian',
        'Mexican',
        'Brazilian',
        'Argentinian',
        'German',
        'French',
        'Italian',
        'Spanish',
        'Portuguese',
        'Dutch',
        'Belgian',
        'Swedish',
        'Norwegian',
        'Danish',
        'Finnish',
        'Russian',
        'Ukrainian',
        'Polish',
        'Romanian',
        'Australian',
        'New Zealander',
    ];



    }

    /* Reset input fields */
    public function resetInputFields()
    {
        // Basic info
        $this->employee = null;
        $this->f_name = '';
        $this->l_name = '';
        $this->email = '';
        $this->phone_no = '';
        $this->job_title = '';
        $this->title = '';
        $this->nationality = 'British';
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






    public function saveCustomField()
    {
        $rules = [
            'customField.label' => 'required|string|max:255',
            'customField.type' => 'required|in:text,number,date,textarea,select',
            'customField.options' => 'nullable|required_if:customField.type,select',
            'customField.required' => 'boolean',
        ];

        $this->validate($rules);



        CustomEmployeeProfileField::create([
            'company_id' => auth()->user()->company->id,
            'name'       => $this->customField['label'],
            'key'        => Str::slug($this->customField['label'], '_'),
            'type'       => $this->customField['type'],
            'options'    => $this->customField['type'] === 'select'
                ? array_map('trim', explode(',', $this->customField['options']))
                : null,
            'required'   => $this->customField['required'],
        ]);

        $this->reset('customField');

        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
        $this->toast('Custom field added successfully', 'success');
    }





    public function submitEmployee()
    {

        if($this->nationality == ''){
            $this->nationality = 'British';
        }

        // Validation rules
        $rules = [
            'email' => ['required', 'email', function ($attribute, $value, $fail) {
                if (User::where('email', $value)->exists()) {
                    $fail('This email is already used.');
                } elseif (Company::where('company_email', $value)->exists()) {
                    $fail('This email is already used.');
                } elseif (Employee::where('email', $value)->exists()) {
                    $fail('This email is already used.');
                }
            }],

            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'nationality' => 'required|string',
            'date_of_birth' => 'required|date',
            'job_title' => ['nullable', 'string', 'max:255'],
            'salary_type' => ['required', 'in:hourly,monthly'],

        ];


        if ($this->nationality !== 'British') {
            $rules['share_code'] = 'nullable|string|max:20';
        }else{
            $this->share_code = null;
        }

        // Contract hours only required if hourly
        if ($this->salary_type === 'hourly') {
            $rules['contract_hours'] = ['required', 'numeric', 'min:0'];
        }

        $validatedData = $this->validate($rules);



        // Save employee
        $employee = Employee::create([
            'company_id' => auth()->user()->company->id,
            'email' => $this->email,
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'job_title' => $this->job_title == '' || $this->job_title === null ? null : $this->job_title,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth == '' ? null : $this->date_of_birth,
            'share_code' => $this->share_code ?? null,
            'role' => 'employee',
            'salary_type' => $this->salary_type,
            'contract_hours' => $this->salary_type === 'hourly' ? $this->contract_hours : null,
            'invite_token' => Str::random(64),
            'invite_token_expires_at' => Carbon::now()->addHours(48),
            'billable_from' => now()->addDays(3),

        ]);


        $inviteUrl = route(
            'employee.auth.set-password',
            [
                'company' => app('authUser')->company->sub_domain,
                'token' => $employee->invite_token,
            ]
        );


        // Dispatch queued job
        // SendEmployeeInvitation::dispatch($employee, $inviteUrl);
        SendEmployeeInvitation::dispatch($employee, $inviteUrl)->onConnection('sync')->onQueue('urgent');

        // Reset form
        $this->reset(['email', 'job_title', 'department_id', 'team_id', 'role', 'salary_type', 'contract_hours']);
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
        $this->toast('Employee added successfully!', 'success');
    }


    public function updatedShareCode($value)
    {
     $this->share_code = strtoupper($value);
    }


    /* Load more employees */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Employee::query();

        if ($this->search && $this->search != '') {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('f_name', 'like', "%{$searchTerm}%")
                    ->orWhere('l_name', 'like', "%{$searchTerm}%")
                    ->orWhere('job_title', 'like', "%{$searchTerm}%")
                    ->orWhereHas('user', function ($q2) use ($searchTerm) {
                        $q2->where('email', 'like', "%{$searchTerm}%");
                    });
            });
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active' ? 1 : 0);
        }

        if ($this->lastId) {
            if ($this->sortOrder === 'desc') {
                $query->where('id', '<', $this->lastId);
            } else {
                $query->where('id', '>', $this->lastId);
            }
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);
    }

    /* Reset loaded */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }



    /* Handle sort */
    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    /* Handle filter */
    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }

    /* Search updated */
    public function updatedSearch()
    {
        $this->resetLoaded();
    }


    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);

        if ($employee) {

            if ($employee->user) {
                $employee->user->delete();
            }


            $employee->delete();

            $this->toast('Employee deleted successfully!', 'success');
            $this->resetInputFields();
            $this->resetLoaded();
        } else {
            $this->toast('Employee not found!', 'error');
        }
    }




    public function exportEmployees($type)
    {
        $employees = $this->loaded;

        if ($employees->isEmpty()) {
            $this->toast('No employees to export!', 'info');
            return;
        }

        // Define column headers
        $columns = [
            'Employee ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Job Title',
            'Department',
            'Team',
            'Role',
            'Salary Type',
            'Contract Hours',
            'Status',
            'Start Date',
            'End Date',
            'Created At'
        ];

        // Define keys for PDF / Excel mapping
        $keys = [
            'employee_id',
            'f_name',
            'l_name',
            'email',
            'phone_no',
            'job_title',
            'department',
            'team',
            'role',
            'salary_type',
            'contract_hours',
            'status',
            'start_date',
            'end_date',
            'created_at'
        ];

        // Map data for export
        $data = $employees->map(function ($emp) {
            return [
                'employee_id'    => $emp->id,
                'f_name'         => $emp->f_name,
                'l_name'         => $emp->l_name,
                'email'          => $emp->email,
                'phone_no'       => $emp->user?->phone_no ?? '',
                'job_title'      => $emp->job_title,
                'department'     => $emp->department?->name ?? '',
                'team'           => $emp->team?->name ?? '',
                'role'           => ucfirst($emp->role),
                'salary_type'    => ucfirst($emp->salary_type),
                'contract_hours' => $emp->contract_hours ?? '',
                'status'         => $emp->is_active ? 'Active' : 'Former',
                'start_date'     => optional($emp->start_date)->format('Y-m-d'),
                'end_date'       => optional($emp->end_date)->format('Y-m-d'),
                'created_at'     => $emp->created_at->format('Y-m-d H:i:s'),
            ];
        });

        // Call your existing export helper
        return $this->export(
            $data,
            $type,
            'employees',
            'exports.generic-table-pdf',
            [
                'title'   => siteSetting()->site_title . ' - Employee List',
                'columns' => $columns,
                'keys'    => $keys
            ]
        );
    }



    public function importCsv()
    {
        $this->validate($this->rulesCsv);

        $file = $this->csv_file->getRealPath();

        $rows = array_map('str_getcsv', file($file));

        if (empty($rows)) {
            $this->toast("CSV file is empty", 'error');
            return;
        }


        $header = array_map('trim', array_shift($rows));
        if (empty($header)) {
            $this->toast("CSV file has no header", 'error');
            return;
        }

        foreach ($rows as $index => $row) {

            $row = array_map('trim', $row);


            if (count(array_filter($row)) === 0) {
                continue;
            }


            $rowAssoc = array_combine($header, $row);

            // Skip rows with empty email
            if (empty($rowAssoc['email'])) {
                continue;
            }


            if (!filter_var($rowAssoc['email'], FILTER_VALIDATE_EMAIL)) {
                $this->toast("Row " . ($index + 2) . ": Invalid email", 'error');
                continue;
            }

            // Skip if email exists
            if (Employee::where('email', $rowAssoc['email'])->exists() || User::where('email', $rowAssoc['email'])->exists()) {
                $this->toast("Row " . ($index + 2) . ": Email already exists", 'error');
                continue;
            }



            try {
                Employee::create([
                    'company_id' => auth()->user()->company->id,
                    'email' => $rowAssoc['email'],
                    'f_name' => $rowAssoc['f_name'] ?? null,
                    'l_name' => $rowAssoc['l_name'] ?? null,
                    'nationality' => $rowAssoc['nationality'] ?? null,
                    'date_of_birth' => $rowAssoc['date_of_birth'] ?? null,
                    'salary_type' => $rowAssoc['salary_type'] ?? null,
                    'contract_hours' => $rowAssoc['salary_type'] == 'monthly' ? null : $rowAssoc['contract_hours'] ?? null,
                    'billable_from' => now()->addDays(3),
                    'role' => 'employee',
                    'job_title' => $rowAssoc['job_title'] ?? null,
                ]);
            } catch (\Exception $e) {
                $this->toast("Row " . ($index + 2) . ": " . $e->getMessage(), 'error');
                continue;
            }
        }

        $this->toast("CSV import finished!", 'success');
        $this->reset(['csv_file']);
        $this->resetLoaded();
        $this->addMethod = 'manual';
        $this->csv_file = '';
        $this->dispatch('closemodal');
    }
}
