<?php

namespace App\Livewire\Backend\Company\Employees;

use App\Jobs\SendEmployeeInvitation;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Services\API\VerificationService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class UsersIndex extends BaseComponent
{
    use WithFileUploads;

    public $employees, $employee, $employee_id;
    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';

    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';

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

    protected $listeners = ['deleteEmployee', 'sortUpdated' => 'handleSort', 'openModal', 'tick'];


    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }
    public function mount()
    {
        $this->loaded = collect();
        $this->departments = Department::all();
        $this->teams = Team::all();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.employees.users-index', [
            'infos' => $this->loaded
        ]);
    }

    /* Reset input fields */
    public function resetInputFields()
    {
        $this->employee = null;
        $this->f_name = '';
        $this->phone_no = '';
        $this->l_name = '';
        $this->email = '';
        $this->job_title = '';
        $this->department_id = '';
        $this->team_id = '';
        $this->role = '';
        $this->contract_hours = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = 1;
        $this->resetErrorBag();
    }





    public function submitEmployee()
    {
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


            'job_title' => ['nullable', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'role' => ['required', 'string', 'in:' . implode(',', config('roles'))],
            'salary_type' => ['required', 'in:hourly,monthly'],
        ];

        // Contract hours only required if hourly
        if ($this->salary_type === 'hourly') {
            $rules['contract_hours'] = ['required', 'numeric', 'min:0'];
        }

        $validatedData = $this->validate($rules);



        // Save employee
        $employee = Employee::create([
            'company_id' => auth()->user()->company->id,
            'email' => $this->email,
            'job_title' => $this->job_title,
            'department_id' => $this->department_id,
            'team_id' => $this->team_id,
            'role' => $this->role,
            'salary_type' => $this->salary_type,
            'contract_hours' => $this->salary_type === 'hourly' ? $this->contract_hours : null,
            'invite_token' => Str::random(64),
            'invite_token_expires_at' => Carbon::now()->addHours(48),

        ]);

        $inviteUrl = route('employee.set-password', ['token' => $employee->invite_token]);

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



    public function editProfile($id)
    {
        $this->editMode = true;

        $this->employee = Employee::with('user')->find($id);

        if (!$this->employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }

        // Load all relevant fields
        $this->f_name = $this->employee->f_name;
        $this->l_name = $this->employee->l_name;
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
        $this->phone_no = $this->employee->user->phone_no;
    }




    public function updateProfile()
    {
        if (!$this->employee) {
            $this->toast('Employee not found!', 'error');
            return;
        }

        // Validation rules
        $rules = [
            'f_name' => 'nullable|string|max:255',
            'l_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'team_id' => 'nullable|exists:teams,id',
            'role' => ['required', 'string', 'in:' . implode(',', config('roles'))],
            'salary_type' => 'required|in:hourly,monthly',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];

        // Contract hours only required if salary_type is hourly
        if ($this->salary_type === 'hourly') {
            $rules['contract_hours'] = 'required|numeric|min:0';
        } else {
            $this->contract_hours = null;
        }

        $validatedData = $this->validate($rules);

        // Update employee
        $this->employee->update([
            'f_name' => $this->f_name,
            'l_name' => $this->l_name,
            'job_title' => $this->job_title,
            'department_id' => $this->department_id,
            'team_id' => $this->team_id,
            'role' => $this->role,
            'salary_type' => $this->salary_type,
            'contract_hours' => $this->contract_hours,
            'is_active' => $this->is_active,
            'end_date' => $this->end_date,
        ]);

        // Reset form and close modal
        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('closemodal');
        $this->toast('Employee updated successfully!', 'success');
        $this->resetLoaded();
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

    /* Delete employee */
    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);
        if ($employee) {
            $employee->delete();
            $this->toast('Employee deleted successfully!', 'success');
            $this->resetLoaded();
        } else {
            $this->toast('Employee not found!', 'error');
        }
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

        $this->toast('Status updated successfully!', 'success');
        $this->resetLoaded();
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

        $this->toast('A-Admin role updated successfully!', 'success');
        $this->resetLoaded();
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
}
