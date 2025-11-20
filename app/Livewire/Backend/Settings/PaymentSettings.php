<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\PaymentSetting;
use App\Services\SettingService;



class PaymentSettings extends BaseComponent
{

    public $gateway = 'bkash';
    public $app_key;
    public $app_secret;
    public $username;
    public $password;
    public $base_url;



    /* render the page */
    public function render()
    {
        return view('livewire.backend.settings.payment-settings');
    }

    protected $rules = [
        'gateway'    => 'required|string|max:255',
        'app_key'    => 'required|string|max:255',
        'app_secret' => 'required|string|max:255',
        'username'   => 'required|string|max:255',
        'password'   => 'required|string|max:255',
        'base_url'   => 'required|url|max:255',

    ];

    /* set value at the time of render */
    public function mount()
    {
        $settings = PaymentSetting::first();

        if ($settings) {
            $this->gateway = $settings->gateway;
            $this->app_key = $settings->app_key;
            $this->app_secret = $settings->app_secret;
            $this->username = $settings->username;
            $this->password = $settings->password;
            $this->base_url = $settings->base_url;
        }
    }



    /* save the Payment settings data */
    public function save(SettingService $service)
    {
        $this->validate();

        $service->savePaymentSettings([
            'gateway'        => $this->gateway,
            'app_key' => $this->app_key,
            'app_secret'        => $this->app_secret,
            'username'    => $this->username,
            'password'              => $this->password,
            'base_url'           => $this->base_url,

        ]);

        $this->toast('Payment Settings Updated Successfully!', 'success');
    }
}
