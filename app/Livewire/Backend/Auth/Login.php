<?php

namespace App\Livewire\Backend\Auth;

use App\Livewire\Backend\Components\BaseComponent;
use App\Repositories\AuthRepository;


class Login extends BaseComponent
{
    public $email, $password, $success = false;


    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];



    //Render Page
    public function render()
    {
        return view('livewire.backend.auth.login')->extends('components.layouts.login_layout')->section('content');
    }
    //Process Login
    public function login(AuthRepository $authRepository)
    {
        $this->validate();
        if ($authRepository->loginAdmin($this->email, $this->password)) {
            return redirect()->intended('super-admin/dashboard');
        }

        $this->toast('Invalid Email or Password', 'error');
    }


    //Initialize Variables
    public function mount()
    {
        if (app('authUser')) {
            if (app('authUser')->user_type == 'superAdmin') {
                return redirect()->route('super-admin.dashboard');
            }
        }
    }
}