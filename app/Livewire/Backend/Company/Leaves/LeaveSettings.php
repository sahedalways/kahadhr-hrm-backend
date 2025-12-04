<?php

namespace App\Livewire\Backend\Company\Leaves;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\LeaveSetting;

class LeaveSettings extends BaseComponent
{
    public $full_time_hours;
    public $leaveSetting;
    public $company;

    public function mount()
    {

        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }


        $this->leaveSetting = LeaveSetting::where('company_id', $this->company->id)->first();

        $this->full_time_hours = $this->leaveSetting->full_time_hours ?? 224;
    }

    public function save()
    {
        $validatedData = $this->validate([
            'full_time_hours' => 'required|numeric|min:0',
        ]);

        if ($this->leaveSetting) {
            $this->leaveSetting->update($validatedData);
        } else {
            $validatedData['company_id'] = $this->company->id;
            $this->leaveSetting = LeaveSetting::create($validatedData);
        }

        $this->toast('Leave Settings Updated Successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.backend.company.leaves.leave-settings');
    }
}
