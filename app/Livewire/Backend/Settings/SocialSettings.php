<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\SocialInfoSettings;
use App\Services\SettingService;

class SocialSettings extends BaseComponent
{
    public $facebook, $twitter, $instagram, $linkedin, $youtube;

    protected $rules = [
        'facebook'  => 'nullable|url|max:255',
        'twitter'   => 'nullable|url|max:255',
        'instagram' => 'nullable|url|max:255',
        'linkedin'  => 'nullable|url|max:255',
        'youtube'   => 'nullable|url|max:255',
    ];

    /* render the page */
    public function render()
    {
        return view('livewire.backend.settings.social-settings');
    }

    /* set value at the time of render */
    public function mount()
    {
        $settings = SocialInfoSettings::first();

        if ($settings) {
            $this->facebook  = $settings->facebook;
            $this->twitter   = $settings->twitter;
            $this->instagram = $settings->instagram;
            $this->linkedin  = $settings->linkedin;
            $this->youtube   = $settings->youtube;
        }
    }

    /* save the Social settings data */
    public function save(SettingService $service)
    {
        $this->validate();

        $service->saveSocialSettings([
            'facebook'  => $this->facebook,
            'twitter'   => $this->twitter,
            'instagram' => $this->instagram,
            'linkedin'  => $this->linkedin,
            'youtube'   => $this->youtube,
        ]);

        $this->toast('Social Settings Updated Successfully!', 'success');
    }
}
