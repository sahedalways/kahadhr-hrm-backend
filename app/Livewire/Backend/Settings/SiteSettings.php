<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\SiteSetting;
use App\Services\SettingService;
use Livewire\WithFileUploads;



class SiteSettings extends BaseComponent
{

    public $site_title, $logo, $favicon, $hero_image, $site_phone_number, $site_email, $copyright_text;
    public $old_favicon, $old_logo, $old_hero_image;
    use WithFileUploads;



    /* render the page */
    public function render()
    {
        return view('livewire.backend.settings.site-settings');
    }

    protected $rules = [
        'site_title' => 'required|string|max:255',
        'site_phone_number' => 'required|string|max:20',
        'site_email'   => 'required|email|max:255',
        'copyright_text' => 'required|string|max:255',
        'logo' => 'nullable|image|max:2048',
        'favicon' => 'nullable|image|max:1024',
        'hero_image' => 'nullable|image|max:2096',
    ];

    /* set value at the time of render */
    public function mount()
    {
        $settings = SiteSetting::first();

        if ($settings) {
            $this->site_title = $settings->site_title;
            $this->site_phone_number = $settings->site_phone_number;
            $this->site_email = $settings->site_email;
            $this->copyright_text = $settings->copyright_text;

            $this->old_logo = $settings->logo_url;
            $this->old_favicon = $settings->favicon_url;
            $this->old_hero_image = $settings->hero_image_url;
        }
    }



    /* save the Site settings data */
    public function save(SettingService $service)
    {

        $this->validate();



        $service->saveSiteSettings([
            'site_title'        => $this->site_title,
            'site_phone_number' => $this->site_phone_number,
            'site_email'        => $this->site_email,
            'copyright_text'    => $this->copyright_text,
            'logo'              => $this->logo,
            'favicon'           => $this->favicon,
            'hero_image'        => $this->hero_image,
        ]);

        $this->toast('Site Settings Updated Successfully!', 'success');
    }
}
