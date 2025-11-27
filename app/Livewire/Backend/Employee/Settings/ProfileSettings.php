<?php

namespace App\Livewire\Backend\Employee\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use Livewire\WithFileUploads;
use Illuminate\Http\UploadedFile;
use App\Models\Department;
use App\Models\Team;

class ProfileSettings extends BaseComponent
{
    use WithFileUploads;

    public $f_name, $l_name, $avatar, $old_avatar;
    public $job_title, $department_id, $team_id;
    public $contract_hours, $salary_type, $start_date, $end_date;

    public $employee;
    public $departments = [];
    public $teams = [];

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

        $this->departments = Department::where('company_id', $this->employee->company_id)->pluck('name', 'id');
        $this->teams       = Team::where('company_id', $this->employee->company_id)->pluck('name', 'id');
    }

    /* Save employee profile */
    public function save()
    {
        $validatedData = $this->validate([
            'f_name' => 'required|string|max:255',
            'l_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ]);

        // Handle avatar upload
        if ($this->avatar instanceof UploadedFile) {
            $validatedData['avatar'] = uploadImage(
                $this->avatar,
                'image/employee/avatar',
                $this->employee->avatar
            );
        }

        $this->employee->update($validatedData);

        $this->toast('Employee Profile Updated Successfully!', 'success');
    }
}
