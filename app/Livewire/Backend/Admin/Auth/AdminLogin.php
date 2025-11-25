<?php

namespace App\Livewire\Backend\Admin\Auth;

use App\Livewire\Backend\Components\BaseComponent;
use App\Repositories\AuthRepository;
use App\Services\API\VerificationService;
use Illuminate\Support\Facades\Auth;

class AdminLogin extends BaseComponent
{
    public $email, $phone_no, $userId, $password, $success = false;
    public $otp = [], $generatedOtp, $showOtpModal = false;
    public $updating_field;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $rememberMe = false;


    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];



    //Render Page
    public function render()
    {
        return view('livewire.backend.admin.auth.admin_login')->extends('components.layouts.login_layout')->section('content');
    }




    //Process Login
    public function login(AuthRepository $authRepository, VerificationService $verificationService)
    {
        $this->validate();

        $user = $authRepository->loginAdmin($this->email, $this->password);

        if (!$user) {
            $this->toast('Invalid Email or Password', 'error');
            return;
        }

        // user type from DB
        $userType = $user->user_type;
        $phone    = $user->phone_no;
        $this->phone_no    = $phone;
        $this->userId    = $user->id;


        if ($userType === 'superAdmin') {

            $sent = $verificationService->sendPhoneOtp($phone, $user->f_name . ' ' . $user->l_name);


            if ($sent) {

                $this->showOtpModal = true;
                $this->code_sent = true;
                $this->startOtpCooldown();
                $this->toast('OTP sent to your phone number', 'success');


                return;
            } else {
                $this->toast("Failed to send OTP", 'error');
                return;
            }
        }
    }



    public function verifyOtp(VerificationService $verificationService)
    {
        try {
            $this->validate([
                'otp' => 'required|array|size:6',
                'otp.*' => 'required|numeric|digits:1',
            ], [
                'otp.required' => 'OTP is required.',
                'otp.size'     => 'OTP must be 6 digits.',
                'otp.*.required' => 'Each OTP field is required.',
                'otp.*.numeric'  => 'OTP must be numeric.',
                'otp.*.digits'   => 'OTP must be 1 digit each.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Show toast manually for first error
            $this->toast(collect($e->errors())->flatten()->first(), 'error');
            return;
        }

        $otpString = implode('', $this->otp);

        try {

            $verificationService->verifyOtp($this->phone_no, $otpString);

            $this->dispatch('closemodal');



            Auth::loginUsingId($this->userId, $this->rememberMe);

            // Clear OTP session
            $this->toast('OTP verified successfully!', 'success');

            return redirect()->intended('dashboard/');
        } catch (\Exception $e) {

            $this->toast('Invalid OTP', 'error');
        }
    }



    public function startOtpCooldown()
    {
        $this->otpCooldown = 120;

        $this->dispatch('start-otp-countdown');
    }


    public function canResendOtp()
    {
        return $this->otpCooldown <= 0;
    }

    public function tick()
    {
        if ($this->otpCooldown > 0) {
            $this->otpCooldown--;
        }
    }





    //Initialize Variables
    public function mount()
    {
        if (app('authUser')) {
            if (app('authUser')->user_type == 'superAdmin') {
                return redirect()->route('super-admin.home');
            }
        }
    }
}
