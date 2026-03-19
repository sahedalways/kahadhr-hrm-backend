<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Traits\Exportable;
use Illuminate\Support\Facades\Schema;

class CompanyEmployeeProfile extends BaseComponent
{
    use Exportable;
    public $status = 'active';

    public $selectedEmployees = [];
    public $profileFields = [];
    public $selectedFields = [];

    public $selectAllUsers = false;
    public $company_id = null;
    public $selectedYear = null;
    public $selectAllFields = false;

    public $employees;

    public function updatedStatus($value)
    {

        $this->loadEmployees();
    }

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;
        $this->profileFields = [
            'f_name' => 'First Name',
            'l_name' => 'Last Name',
            'email' => 'Email',
            'is_active' => 'Status',
            'role' => 'Role',
            'job_title' => 'Job Title',
            'departments' => 'Department',
            'teams' => 'Team',
            'contract_hours' => 'Contract Hours',
            'salary_type' => 'Salary Type',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'verified' => 'Is Verified',
            'billable_from' => 'Billable From',

            'date_of_birth' => 'Date of Birth',
            'street' => 'Street Address',
            'city' => 'City',
            'state' => 'State',
            'postcode' => 'Postcode',
            'country' => 'Country',
            'nationality' => 'Nationality',
            'home_phone' => 'Home Phone',
            'mobile_phone' => 'Mobile Phone',
            'personal_email' => 'Personal Email',
            'gender' => 'Gender',
            'marital_status' => 'Marital Status',
            'tax_reference_number' => 'Tax Reference Number',
            'immigration_status' => 'Immigration Status',
            'brp_number' => 'BRP Number',
            'brp_expiry_date' => 'BRP Expiry Date',
            'right_to_work_expiry_date' => 'Right To Work Expiry',
            'passport_number' => 'Passport Number',
            'passport_expiry_date' => 'Passport Expiry Date',
        ];


        $this->selectedYear = now()->year;

        $this->loadEmployees();
    }

    /** 🔹 Load employees by status */
    public function loadEmployees()
    {
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($q) {
                $q->where('is_active', $this->status == 'former' ? 0 : 1);
            })
            ->orderBy('f_name')
            ->get();


        // Reset selection when status changes
        $this->selectedEmployees = [];
        $this->selectAllUsers = false;
    }


    public function updatedSelectAllUsers($value)
    {
        if ($value) {
            $this->selectedEmployees = $this->employees->pluck('id')->toArray();
        } else {
            $this->selectedEmployees = [];
        }

        $this->dispatch('keep-employee-dropdown-open');
    }

    public function updatedSelectedEmployees()
    {

        if (count($this->selectedEmployees) != $this->employees->count()) {
            $this->selectAllUsers = false;
        }

        $this->dispatch('keep-employee-dropdown-open');
    }

    public function updatedSelectAllFields($value)
    {
        if ($value) {
            $this->selectedFields = array_keys($this->profileFields);
        } else {
            $this->selectedFields = [];
        }

        $this->dispatch('keep-field-dropdown-open');
    }

    public function updatedSelectedFields()
    {
        if (count($this->selectedFields) != count($this->profileFields)) {
            $this->selectAllFields = false;
        }

        $this->dispatch('keep-field-dropdown-open');
    }



    public function exportFile($type)
    {
        $employees = Employee::with(['profile', 'documents'])->where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->where('is_active', $this->status == 'active' ? 1 : 0)
            ->whereIn('id', $this->selectedEmployees)
            ->get();

        $documentTypes = DocumentType::query()
            ->where('company_id', auth()->user()->company->id)
            ->orderBy('name')
            ->get();


        $shareCodeType = $documentTypes->firstWhere('name', 'Share Code');

        $data = $employees->map(function ($emp) use ($shareCodeType) {

            $row = [];

            $latestShareDoc = null;
            $shareCodeStatus = 'Not Verified';
            $shareCodeColor = null;

            if ($shareCodeType) {
                $latestShareDoc = $emp->documents()
                    ->where('doc_type_id', $shareCodeType->id)
                    ->latest('created_at')
                    ->first();

                if ($latestShareDoc && $latestShareDoc->expires_at) {
                    $expiresAt = \Carbon\Carbon::parse($latestShareDoc->expires_at);
                    $daysLeft = now()->diffInDays($expiresAt, false);

                    if ($daysLeft < 0) {
                        $shareCodeStatus = 'Expired';
                        $shareCodeColor = '#dc3545';
                    } elseif ($daysLeft <= 60) {
                        $shareCodeStatus = 'Expires Soon';
                        $shareCodeColor = '#fd7e14';
                    } else {
                        $shareCodeStatus = 'Valid';
                        $shareCodeColor = '#198754';
                    }
                }
            }

            foreach ($this->selectedFields as $field) {
                $value = 'N/A';

                if ($field === 'departments') {
                    $departments = $emp->user
                        ? $emp->user->teams->pluck('department')->filter()->unique('id')->values()
                        : collect();
                    $value = $departments->pluck('name')->implode(', ') ?: 'N/A';
                }

                if ($field === 'teams') {
                    $teams = $emp->user ? $emp->user->teams : collect();
                    $value = $teams->pluck('name')->implode(', ') ?: 'N/A';
                }

                if (Schema::hasColumn('employees', $field)) {
                    $value = $emp->$field ?? 'N/A';
                }

                if ($value === 'N/A' && $emp->profile && Schema::hasColumn('employee_profiles', $field)) {
                    $value = $emp->profile->$field ?? 'N/A';
                }

                if ($field === 'is_active') {
                    $value = $emp->is_active ? 'Active' : 'Inactive';
                }

                if ($field === 'verified') {
                    $value = $emp->verified ? 'Verified' : 'Not Verified';
                }

                if (in_array($field, [
                    'date_of_birth',
                    'start_date',
                    'end_date',
                    'billable_from',
                    'brp_expiry_date',
                    'right_to_work_expiry_date',
                    'passport_expiry_date'
                ]) && $value !== 'N/A') {
                    $value = \Carbon\Carbon::parse($value)->format('d-m-Y');
                }

                if ($field === 'salary_type' && is_string($value)) {
                    $value = ucfirst($value);
                }


                if ($field === 'right_to_work_expiry_date') {
                    if ($emp->profile && $emp->profile->nationality === 'British') {
                        $value = 'Permanent';
                    } else {
                        $value = $shareCodeStatus === 'Not Verified' ? 'Not Verified' : \Carbon\Carbon::parse($latestShareDoc->expires_at)->format('d-m-Y');
                    }
                }


                if ($field === 'share_code_status') {
                    $value = $shareCodeStatus;
                }

                $row[$field] = $value;
            }

            $row['employee_name'] = $emp->full_name ? ucfirst($emp->full_name) : 'N/A';
            $row['is_verified'] = $emp->user ? 'Verified' : 'Not Verified';

            return $row;
        });

        $this->dispatch('closemodal');

        $columns = collect($this->selectedFields)
            ->map(fn($field) => $this->profileFields[$field] ?? ucwords(str_replace('_', ' ', $field)))
            ->toArray();

        array_unshift($columns, 'Employee Name');

        $keys = $this->selectedFields;
        array_unshift($keys, 'employee_name');

        return $this->export(
            $data,
            $type,
            'employee_profile_report',
            'exports.generic-table-pdf',
            [
                'title'   => siteSetting()->site_title . ' - Employee Profile Report',
                'columns' => $columns,
                'keys'    => $keys
            ]
        );
    }




    public function render()
    {
        return view('livewire.backend.company.reports.company-employee-profile');
    }
}
