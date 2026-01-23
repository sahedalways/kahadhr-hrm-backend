<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\SmsSetting;
use App\Services\SettingService;

class SmsSettings extends BaseComponent
{
    public $twilio_sid, $twilio_auth_token, $twilio_from;

    protected $rules = [
        'twilio_sid'        => 'required|string|max:255',
        'twilio_auth_token' => 'required|string|max:255',
        'twilio_from'       => 'required|string|max:255',
    ];

    public function mount()
    {
        $settings = SmsSetting::first();

        if ($settings) {
            $this->fill([
                'twilio_sid' => $settings->twilio_sid,
                'twilio_auth_token' => $settings->twilio_auth_token,
                'twilio_from' => $settings->twilio_from,
            ]);
        }
    }

    public function save(SettingService $service)
    {
        $this->validate();

        $companyId = app('authUser')->company?->id ?? null;

        $smsSettings = [
            'twilio_sid'        => $this->twilio_sid,
            'twilio_auth_token' => $this->twilio_auth_token,
            'twilio_from'       => $this->twilio_from,
        ];

        // Save in database
        $service->saveSmsSettings($smsSettings, $companyId);



        $this->toast('SMS settings updated successfully!', 'success');
    }


    public function render()
    {
        return view('livewire.backend.settings.sms-settings');
    }
}
