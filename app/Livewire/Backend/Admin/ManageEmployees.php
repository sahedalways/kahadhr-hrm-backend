<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Traits\Exportable;
use Livewire\WithFileUploads;

class ManageEmployees extends BaseComponent
{
    use WithFileUploads, Exportable;

    public $employees, $employee, $employee_id, $title;
    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $avatar, $avatar_preview, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';

    public $date_of_birth, $street_1, $street_2, $city, $state, $postcode, $country,
        $nationality, $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status, $brp_number, $brp_expiry_date,
        $right_to_work_expiry_date, $passport_number, $passport_expiry_date;

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
    public $csv_file;
    public $addMethod = 'manual';

    protected $listeners = ['deleteEmployee', 'sortUpdated' => 'handleSort', 'openModal', 'tick'];

    protected $rulesCsv = [
        'csv_file' => 'required|file|mimes:csv,txt|max:2048',
    ];

    public function mount()
    {
        $this->loaded = collect();
        $this->departments = Department::all();
        $this->teams = Team::all();
        $this->resetLoaded();
    }

    public function render()
    {
        return view('livewire.backend.admin.manage-employees', [
            'infos' => $this->loaded
        ]);
    }

    /**
     * Build the base query for employees (with search & filter)
     */
    private function baseQuery()
    {
        $query = Employee::query();

        // Apply search
        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('f_name', 'like', $searchTerm)
                    ->orWhere('l_name', 'like', $searchTerm)
                    ->orWhere('job_title', 'like', $searchTerm)
                    ->orWhereHas('user', fn($q2) => $q2->where('email', 'like', $searchTerm));
            });
        }

        // Apply status filter
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active' ? 1 : 0);
        }

        return $query;
    }

    /**
     * Load more employees
     */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = $this->baseQuery();

        // Apply lastId pagination
        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        // Exclude already loaded employees to prevent duplicates
        if ($this->loaded->isNotEmpty()) {
            $query->whereNotIn('id', $this->loaded->pluck('id'));
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;

        // Merge and make sure no duplicates
        $this->loaded = $this->loaded->merge($items)->unique('id');

        // If less than perPage, mark hasMore false
        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    /**
     * Reset loaded employees and pagination
     */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    /**
     * Sort handler
     */
    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    /**
     * Filter handler
     */
    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }

    /**
     * Search updated
     */
    public function updatedSearch()
    {
        $this->resetLoaded();
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

            $department = Department::where('name', $rowAssoc['department'] ?? '')->first();

            try {
                Employee::create([
                    'company_id' => auth()->user()->company->id,
                    'email' => $rowAssoc['email'],
                    'f_name' => $rowAssoc['f_name'] ?? null,
                    'l_name' => $rowAssoc['l_name'] ?? null,
                    'department_id' => $department?->id,
                    'role' => in_array($rowAssoc['role'] ?? '', config('roles')) ? $rowAssoc['role'] : 'employee',
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
