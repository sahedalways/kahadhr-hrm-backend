<?php

namespace App\Livewire\Backend\Employee\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Services\API\VerificationService;
use Illuminate\Support\Facades\Auth;

class VerificationCentreSettings extends BaseComponent
{
    public $email, $phone_no, $full_name;
    public $new_email, $new_phone_no;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $otp = [];
    public $verification_code;
    public $updating_field;

    protected $listeners = ['openModal', 'tick'];

    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }

    public function mount()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Employee profile not found.');
        }

        $this->email = $employee->email;
        $this->phone_no = $user->phone_no ?? null;
        $this->full_name = $employee->full_name;
    }

    public function save()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Update employee email
        $employee->update([
            'email' => $this->email,
        ]);

        // Update user phone_no
        $user->update([
            'phone_no' => $this->phone_no,
            'email' => $this->email,
        ]);

        $this->toast('Employee contact info updated successfully!', 'success');
    }

    public function requestVerification($field, VerificationService $verificationService)
    {
        $this->updating_field = $field;

        if ($field === 'email') {

            $this->validate([
                'new_email' => 'required|email|max:255',
            ]);

            // Check existence in all tables
            $exists =
                Employee::where('email', $this->new_email)->exists() ||
                User::where('email', $this->new_email)->exists() ||
                Company::where('company_email', $this->new_email)->exists();

            if ($exists) {
                $this->toast('This email is already in use.', 'error');
                return;
            }

            $target = $this->new_email;
        } else {

            $this->validate([
                'new_phone_no' => 'required|min:10|max:20',
            ]);

            // Check existence in all tables
            $exists =
                User::where('phone_no', $this->new_phone_no)->exists() ||
                Company::where('company_mobile', $this->new_phone_no)->exists();

            if ($exists) {
                $this->toast('This phone number is already in use.', 'error');
                return;
            }

            $target = $this->new_phone_no;
        }


        $sent = false;
        if ($field === 'email') {
            $sent = $verificationService->sendEmailOtp($target, $this->full_name);
        } else {
            $sent = $verificationService->sendPhoneOtp($target, $this->full_name);
        }

        if ($sent) {
            $this->toast("Verification code sent to your {$field}.", 'info');
            $this->code_sent = true;
            $this->startOtpCooldown();
        } else {
            $this->toast("Failed to send OTP", 'error');
        }
    }

    public function startOtpCooldown()
    {
        $this->otpCooldown = 120;
        $this->dispatch('start-otp-countdown');
    }

    public function tick()
    {
        if ($this->otpCooldown > 0) {
            $this->otpCooldown--;
        }
    }

    public function verifyAndUpdate(VerificationService $verificationService)
    {
        $code = implode('', $this->otp);
        $this->verification_code = $code;

        $this->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $target = $this->updating_field === 'email' ? $this->new_email : $this->new_phone_no;

        try {
            $verificationService->verifyOtp($target, $this->verification_code);

            if ($this->updating_field === 'email') {
                $this->email = $this->new_email;
            } else {
                $this->phone_no = $this->new_phone_no;
            }

            $this->save();
            $this->resetVerificationFields();
            $this->dispatch('closemodal');
        } catch (\Exception $e) {
            $this->toast($e->getMessage(), 'error');
        }
    }

    public function resetVerificationFields()
    {
        $this->new_email = null;
        $this->new_phone_no = null;
        $this->otp = [];
        $this->verification_code = null;
        $this->updating_field = null;
        $this->code_sent = false;
        $this->otpCooldown = 0;
    }
}
