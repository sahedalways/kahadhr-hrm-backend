<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Services\UserService;


class PasswordSettings extends BaseComponent
{

    public $old_password;
    public $new_password;
    public $confirm_new_password;

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



    public function render()
    {
        return view('livewire.backend.settings.password-settings');
    }
}
