<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\EmailSetting;
use App\Services\SettingService;
use EnvUpdater;
use Illuminate\Support\Facades\Artisan;

class MailSettings extends BaseComponent
{

    public $mail_mailer, $mail_host, $mail_port, $mail_username, $mail_password, $mail_encryption, $mail_from_address, $mail_from_name;


    protected $rules = [
        'mail_mailer'       => 'required|string|max:255',
        'mail_host'         => 'required|string|max:255',
        'mail_port'         => 'required|string|max:255',
        'mail_username'     => 'required|string|max:255',
        'mail_password'     => 'required|string|max:255',
        'mail_encryption'   => 'required|string|max:255',
        'mail_from_address' => 'required|email|max:255',
        'mail_from_name'    => 'required|string|max:255',
    ];


    public function mount()
    {
        $settings = EmailSetting::first();


        if ($settings) {
            $this->fill($settings->toArray());
        }
    }



    public function save(SettingService $service)
    {
        $this->validate();

        $companyId = app('authUser')->company?->id ?? null;

        // Prepare mail settings array
        $mailSettings = [
            'mail_mailer'       => $this->mail_mailer,
            'mail_host'         => $this->mail_host,
            'mail_port'         => $this->mail_port,
            'mail_username'     => $this->mail_username,
            'mail_password'     => $this->mail_password,
            'mail_encryption'   => $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name'    => $this->mail_from_name ?? siteSetting()->site_title,
        ];

        // Save in database
        $service->saveMailSettings($mailSettings, $companyId);


        // Update .env
        EnvUpdater::set([
            'MAIL_MAILER'       => $this->mail_mailer,
            'MAIL_HOST'         => $this->mail_host,
            'MAIL_PORT'         => $this->mail_port,
            'MAIL_USERNAME'     => $this->mail_username ?? 'null',
            'MAIL_PASSWORD'     => $this->mail_password ?? 'null',
            'MAIL_ENCRYPTION'   => $this->mail_encryption ?? 'null',
            'MAIL_FROM_ADDRESS' => $this->mail_from_address,
            'MAIL_FROM_NAME'    => $this->mail_from_name ?? '${APP_NAME}',
        ]);

        // Clear and reload config cache
        Artisan::call('config:clear');



        $this->toast('Mail settings updated successfully!', 'success');
    }




    public function render()
    {
        return view('livewire.backend.settings.mail-settings');
    }
}
