<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
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
    public $selectAllFields = false;

    public $employees;

    public function updatedStatus($value)
    {
        logger('STATUS CHANGED TO: ' . $value);
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
            'department_id' => 'Department',
            'team_id' => 'Team',
            'contract_hours' => 'Contract Hours',
            'salary_type' => 'Salary Type',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'verified' => 'Is Verified',
            'billable_from' => 'Billable From',

            'date_of_birth' => 'Date of Birth',
            'street_1' => 'Street 1',
            'street_2' => 'Street 2',
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
            'right_to_work_expiry_date' => 'Right To Work Expiry Date',
            'passport_number' => 'Passport Number',
            'passport_expiry_date' => 'Passport Expiry Date',
        ];



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
        // Auto uncheck "All Employees" if any employee unchecked
        if (count($this->selectedEmployees) != $this->employees->count()) {
            $this->selectAllUsers = false;
        }

        $this->dispatch('keep-employee-dropdown-open');
    }

    public function updatedSelectAllFields($value)
    {
        if ($value) {
            $this->selectedFields = $this->profileFields;
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

        $employees = Employee::with('profile')
            ->where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->where('is_active', $this->status == 'active' ? 1 : 0)
            ->whereIn('id', $this->selectedEmployees)
            ->get();

        $data = $employees->map(function ($emp) {

            $row = [];

            foreach ($this->selectedFields as $field) {

                // Employee table field
                if (in_array($field, [
                    'f_name',
                    'l_name',
                    'email',
                    'avatar',
                    'is_active',
                    'role',
                    'job_title',
                    'department_id',
                    'team_id',
                    'contract_hours',
                    'salary_type',
                    'start_date',
                    'end_date',
                    'verified',
                    'billable_from'
                ])) {
                    $row[$field] = $emp->$field ?? '';
                }
                // EmployeeProfile table field
                else {
                    $row[$field] = $emp->profile ? ($emp->profile->$field ?? '') : '';
                }
            }

            $row['employee_name'] = $emp->full_name;
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
