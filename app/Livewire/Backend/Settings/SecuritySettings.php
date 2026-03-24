<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\SecuritySetting;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class SecuritySettings extends BaseComponent
{

    public $old_password;
    public $new_password;
    public $confirm_new_password;

    public $twoStepEnabled = false;
    public $verificationMethod = 'mobile';

    public SecuritySetting $securitySetting;

    protected $rules = [
        'old_password' => 'required',
        'new_password' => 'required|min:8',
        'confirm_new_password' => 'required|same:new_password',
    ];


    public function save(UserService $userService)
    {
        $this->validate();


        $result = $userService->changePassword($this->old_password, $this->new_password);

        if ($result['success']) {
            $this->reset(['old_password', 'new_password', 'confirm_new_password']);
            $this->toast($result['message'], 'success');
        } else {
            $this->toast($result['message'], 'error');
        }
    }



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
        return view('livewire.backend.settings.security-settings');
    }
}
