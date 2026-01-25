<?php

namespace App\Livewire\Backend\Employee\Auth;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\User;
use App\Repositories\AuthRepository;
use App\Services\API\VerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeLogin extends BaseComponent
{
    public $email = "emp@xyz.com";
    public $password = "12345678";
    public $company;
    public $rememberMe = false;

    public $resetMethod;
    public $resetEmail;
    public $resetPhone;

    public $resetOtpCooldown = 0;

    public $currentStep = 1;
    public $changePasswordOtp = [];
    public $newPassword;
    public $confirmPassword;


    protected $rules = [
        'email'    => 'required|email',
        'password' => 'required',
    ];


    public function sendResetOtp(VerificationService $verificationService)
    {
        try {
            $this->validate(['resetMethod' => 'required',]);
            if ($this->resetMethod === 'email') {
                $this->validate([
                    'resetEmail' => 'required|email'
                ]);

                // Check if user exists
                $userExists = User::where('email', $this->resetEmail)
                    ->where('user_type', 'employee')
                    ->exists();

                if (!$userExists) {
                    $this->toast('No employee user found with this email.', 'error');
                    return;
                }



                $sent = $verificationService->sendEmailOtp($this->resetEmail, null);
            } else {
                $this->validate([
                    'resetPhone' => 'required|numeric'
                ]);

                // Check if user exists
                $userExists = User::where('phone_no', $this->resetPhone)
                    ->where('user_type', 'employee')
                    ->exists();

                if (!$userExists) {
                    $this->toast('No employee user found with this phone number.', 'error');
                    return;
                }


                $sent = $verificationService->sendPhoneOtp($this->resetPhone, null);
            }

            if ($sent) {
                $this->resetOtpCooldown = 120;
                $this->currentStep = 2;
                $this->toast('OTP sent successfully', 'success');
            } else {
                $this->toast("Failed to send OTP", 'error');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->toast(collect($e->errors())->flatten()->first(), 'error');
        }
    }


    public function verifyResetOtp(VerificationService $verificationService)
    {
        try {
            $this->validate([
                'changePasswordOtp' => 'required|array|size:6',
                'changePasswordOtp.*' => 'required|numeric|digits:1',
            ], [
                'changePasswordOtp.required' => 'OTP is required.',
                'changePasswordOtp.size' => 'OTP must be 6 digits.',
                'changePasswordOtp.*.required' => 'OTP must be 6 digits.',
                'changePasswordOtp.*.numeric' => 'OTP must be numeric.',
                'changePasswordOtp.*.digits' => 'OTP must be 1 digit each.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->toast(collect($e->errors())->flatten()->first(), 'error');
            return;
        }

        try {

            $destination = $this->resetMethod === 'email'
                ? $this->resetEmail
                : $this->resetPhone;


            $otp = implode('', $this->changePasswordOtp);


            $verificationService->verifyOtp($destination, $otp);

            $this->toast('OTP verified successfully!', 'success');
            $this->currentStep = 3;
        } catch (\Exception $e) {
            $this->toast("OTP does not match.", 'error');
        }
    }



    public function updatePassword()
    {
        try {
            $this->validate([
                'newPassword' => 'required|min:8',
                'confirmPassword' => 'required|same:newPassword',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $message = implode('<br>', $errors);
            $this->toast($message, 'error');
            return;
        }


        $user = null;

        if ($this->resetMethod === 'email') {
            $user = User::where('email', $this->resetEmail)
                ->where('user_type', 'employee')
                ->first();
        } else {
            $user = User::where('phone_no', $this->resetPhone)
                ->where('user_type', 'employee')
                ->first();
        }


        if (!$user) {
            $this->toast('User not found!', 'error');
            return;
        }

        $user->password = Hash::make($this->newPassword);
        $user->save();


        $this->reset();

        return redirect()->route('password.set.success', [
            'user_type' => 'Employee'
        ]);
    }


    public function cleaResetPasswordFields()
    {
        $this->reset();
    }
    public function tickResetOtp()
    {
        if ($this->resetOtpCooldown > 0) {
            $this->resetOtpCooldown--;
        }
    }





    public function mount(Request $request)
    {
        $this->company = $request->route('company') ?? null;


        // Redirect already logged-in employees
        $authUser = app('authUser');
        if ($authUser?->user_type === 'employee' && $authUser->employee?->company?->sub_domain) {
            return redirect()->route(
                'employee.dashboard.index',
                ['company' => $authUser->employee->company->sub_domain]
            );
        }
    }

    public function render()
    {
        return view('livewire.backend.employee.auth.employee-login', ['company' => $this->company])
            ->extends('components.layouts.login_layout')
            ->section('content');
    }

    public function login(AuthRepository $authRepository)
    {
        $this->validate();

        $user = $authRepository->loginEmployee($this->email, $this->password);

        if (!$user) {
            return $this->toast('Invalid Email or Password', 'error');
        }

        // Check company subdomain
        $currentSubdomain = explode('.', request()->getHost())[0];
        $employee = $user->employee()->withoutGlobalScopes()->first();

        if (!$employee || $employee->company?->sub_domain !== $currentSubdomain) {
            return $this->toast('Invalid subdomain for this account.', 'error');
        }



        Auth::loginUsingId($user->id, $this->rememberMe);

        return redirect()->intended(route('employee.dashboard.index', ['company' => $employee->company->sub_domain]));
    }
}
