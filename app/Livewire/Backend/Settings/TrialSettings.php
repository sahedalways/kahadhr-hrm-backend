<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\TrialSetting;

class TrialSettings extends BaseComponent
{
    public $trial_days;

    protected $rules = [
        'trial_days' => 'required|integer|min:1|max:600',
    ];

    protected $messages = [
        'trial_days.required' => 'Trial days is required.',
        'trial_days.integer' => 'Trial days must be a whole number.',
        'trial_days.max' => 'Trial days cannot exceed 600 days.',
        'trial_days.min' => 'Trial days should minimum 1 day.',
    ];

    public function mount()
    {
        $trialSetting = TrialSetting::first();

        if ($trialSetting) {
            $this->trial_days = $trialSetting->trial_days;
        }
    }

    public function save()
    {
        $this->validate();

        $trialSetting = TrialSetting::first();

        if ($trialSetting) {
            $trialSetting->update([
                'trial_days' => $this->trial_days,
            ]);
        } else {
            TrialSetting::create([
                'trial_days' => $this->trial_days,
            ]);
        }

        $this->toast('Trial settings updated successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.backend.settings.trial-settings');
    }
}
