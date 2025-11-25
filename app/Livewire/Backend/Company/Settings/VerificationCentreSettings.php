<?php


namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Services\API\VerificationService;
use Illuminate\Validation\Rule;


class VerificationCentreSettings extends BaseComponent
{

    public $company_mobile, $company_email, $company_name;


    public $new_email;
    public $new_mobile;

    public $code_sent = false;
    public $otpCooldown = 0;
    public $otp = [];


    public $verification_code;

    public $updating_field;

    public $company;



    protected $listeners = ['openModal', 'tick'];

    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }



    /* Load company info from middleware authenticated company */
    public function mount()
    {
        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found for this user.');
        }


        $this->company_mobile        = $this->company->company_mobile;
        $this->company_email         = $this->company->company_email;
        $this->company_name         = $this->company->company_name;
    }

    /* Save company profile settings */
    public function save()
    {
        $validatedData = $this->validate([
            'company_email' => ['required', 'email', Rule::unique('companies', 'company_email')->ignore($this->company->id)],
            'company_mobile' => ['required', 'string', 'max:20', Rule::unique('companies', 'company_mobile')->ignore($this->company->id)],

        ]);

        $company = $this->company;


        $company->update([
            'company_email'          => $this->company_email,
            'company_mobile'          => $this->company_mobile,
        ]);



        $this->toast('Company Profile Updated Successfully!', 'success');
    }



    public function requestVerification($field, VerificationService $verificationService)
    {
        $this->updating_field = $field;

        if ($field === 'email') {
            $this->validate(['new_email' => 'required|email|max:255']);
            $target = $this->new_email;
        } else {
            $this->validate(['new_mobile' => 'required|min:10|max:20']);
            $target = $this->new_mobile;
        }



        $sent = false;
        if ($field === 'email') {
            $sent = $verificationService->sendEmailOtp($target, $this->company_name);
        } else {
            $sent = $verificationService->sendPhoneOtp($target, $this->company_name);
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


    public function verifyAndUpdate(VerificationService $verificationService)
    {

        $code = implode('', $this->otp);
        $this->verification_code = $code;


        $this->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $target = $this->updating_field === 'email' ? $this->new_email : $this->new_mobile;

        try {

            $verificationService->verifyOtp($target, $this->verification_code);


            if ($this->updating_field === 'email') {
                $this->company_email = $this->new_email;
            } else {
                $this->company_mobile = $this->new_mobile;
            }

            $this->save();

            $this->resetVerificationFields();
            $this->dispatch('closemodal');
        } catch (\Exception $e) {

            $this->toast("OTP does not match.", 'error');
        }
    }


    public function resetVerificationFields()
    {
        $this->new_email = null;
        $this->new_mobile = null;
        $this->otp = [];
        $this->verification_code = null;

        $this->updating_field = null;
        $this->code_sent = false;
        $this->otpCooldown = 0;
    }
}
