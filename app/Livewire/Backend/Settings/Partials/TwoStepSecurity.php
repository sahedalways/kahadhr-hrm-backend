<?php

namespace App\Livewire\Backend\Settings\Partials;

use App\Models\SecuritySetting;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Backend\Components\BaseComponent;



class TwoStepSecurity extends BaseComponent
{
    public $twoStepEnabled = true;
    public $verificationMethod = 'mobile';

    public SecuritySetting $securitySetting;


    public function mount()
    {
        $user = Auth::user();


        $this->securitySetting = SecuritySetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'two_step_enabled' => true,
                'verification_method' => 'mobile'
            ]
        );


        $this->twoStepEnabled = $this->securitySetting->two_step_enabled;
        $this->verificationMethod = $this->securitySetting->verification_method;
    }


    public function saveSettings()
    {
        $this->securitySetting->update([
            'two_step_enabled' => $this->twoStepEnabled,
            'verification_method' => $this->verificationMethod,
        ]);

        $this->toast('Security settings updated', 'success');
    }

    public function updatedTwoStepEnabled($value)
    {

        $this->saveSettings();
    }

    public function updatedVerificationMethod($value)
    {

        $this->saveSettings();
    }




    public function render()
    {
        return view('livewire.backend.settings.partials.two-step-security');
    }
}
