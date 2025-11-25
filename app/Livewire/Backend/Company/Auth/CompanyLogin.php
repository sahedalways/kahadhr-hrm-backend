<?php

namespace App\Livewire\Backend\Company\Auth;

use App\Livewire\Backend\Components\BaseComponent;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class CompanyLogin extends BaseComponent
{
    public $email, $password, $success = false;
    public $otp = [], $generatedOtp, $showOtpModal = false;
    public $rememberMe = false;


    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];



    //Render Page
    public function render()
    {
        return view('livewire.backend.company.auth.company_login')->extends('components.layouts.login_layout')->section('content');
    }




    //Process Login
    public function login(AuthRepository $authRepository)
    {
        $this->validate();

        $user = $authRepository->loginCompany($this->email, $this->password);

        if (!$user) {
            $this->toast('Invalid Email or Password', 'error');
            return;
        }


        $currentHost = request()->getHost();
        $baseDomain = config('app.base_domain');

        $currentSubdomain = str_replace('.' . $baseDomain, '', $currentHost);


        if (!str_contains($currentSubdomain, $user->company->sub_domain)) {
            $this->toast('Invalid subdomain for this account.', 'error');
            return;
        }
        // user type from DB
        $userType = $user->user_type;
        $phone    = $user->phone_no;


        if ($userType === 'company') {

            // $this->generatedOtp = rand(100000, 999999);
            $this->generatedOtp = 123456;
            session(['otp' => $this->generatedOtp, 'otp_user_id' => $user->id]);

            $authRepository->sendOtpSms($phone, $this->generatedOtp);

            $this->showOtpModal = true;

            $this->toast('OTP sent to your phone number', 'success');
            return;
        }


        Auth::login($user, $this->rememberMe);

        return redirect()->intended('dashboard/');
    }



    public function verifyOtp()
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

        if ($otpString == session('otp')) {
            $remember = session('otp_remember', false);

            Auth::loginUsingId(session('otp_user_id'), $remember);

            // Clear OTP session
            session()->forget(['otp', 'otp_user_id', 'otp_remember']);
            session()->forget(['otp', 'otp_user_id']);
            $this->toast('OTP verified successfully!', 'success');

            return redirect()->intended('dashboard/');
        }

        $this->toast('Invalid OTP', 'error');
    }




    //Initialize Variables
    public function mount()
    {
        if (app('authUser')) {
            if (app('authUser')->user_type == 'company') {
                return redirect()->route('company.dashboard.index', ['company' => app('authUser')->company->sub_domain]);
            }
        }
    }
}
