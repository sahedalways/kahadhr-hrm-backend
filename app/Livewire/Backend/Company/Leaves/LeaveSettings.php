<?php

namespace App\Livewire\Backend\Company\Leaves;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\LeaveBalance;


class LeaveSettings extends BaseComponent
{

    public $leaveSetting;
    public $company;
    public $selectedEmployee;
    public $employees;
    public $total_annual_hours;

    public $total_leave_in_liew;


    public function mount()
    {

        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }


        $this->employees = Employee::where('company_id', $this->company->id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();
    }



    public function selectEmployee($userId)
    {
        $this->selectedEmployee = $userId;

        $leaveBalance = LeaveBalance::where('user_id', $userId)
            ->where('company_id', $this->company->id)
            ->first();

        if ($leaveBalance) {
            $this->total_annual_hours  = $leaveBalance->total_annual_hours;
            $this->total_leave_in_liew = $leaveBalance->total_leave_in_liew;
        } else {
            $this->total_annual_hours = null;
            $this->total_leave_in_liew = null;
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'selectedEmployee'     => 'required|exists:users,id',
            'total_annual_hours'   => 'required|numeric|min:0',
            'total_leave_in_liew'  => 'required|numeric|min:0',
        ]);



        foreach ($validatedData as $key => $value) {
            if ($value === '' || $value === null) {
                $validatedData[$key] = null;
            }
        }

        $userId = $this->selectedEmployee;
        $companyId = $this->company->id;


        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id'    => $userId,
            'company_id' => $companyId,
        ]);


        $leaveBalance->total_annual_hours  = $validatedData['total_annual_hours'];
        $leaveBalance->total_leave_in_liew = $validatedData['total_leave_in_liew'];




        $leaveBalance->carry_over_hours = max(
            0,
            ($leaveBalance->total_annual_hours ?? 0) - ($leaveBalance->used_annual_hours ?? 0)
        );

        // Save
        $leaveBalance->save();

        $this->toast('Leave Settings Updated Successfully!', 'success');
    }


    public function render()
    {
        return view('livewire.backend.company.leaves.leave-settings');
    }
}
