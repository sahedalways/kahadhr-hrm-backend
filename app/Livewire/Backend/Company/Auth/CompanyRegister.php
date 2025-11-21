<?php

namespace App\Livewire\Backend\Company\Auth;

use Livewire\Component;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;

class CompanyRegister extends Component
{
    // Step control
    public $step = 1;

    // Step 1: Company & User Info
    public $company_name, $company_number, $company_mobile, $company_email, $password, $password_confirmation;

    // Step 2: Email OTP
    public $email_otp, $generated_email_otp;

    // Step 3: Phone OTP
    public $phone_otp, $generated_phone_otp;

    // Step 4: Bank info
    public $bank_name, $card_number, $expiry_date, $cvv;

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'company_number' => 'required|string|max:50',
        'company_mobile' => 'required|string|max:20',
        'company_email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed',
    ];

    public function nextStep()
    {
        $this->validateStep();
        $this->step++;
    }

    public function previousStep()
    {
        $this->step--;
    }

    public function validateStep()
    {
        if ($this->step == 1) {
            $this->validate([
                'company_name' => 'required|string|max:255',
                'company_number' => 'required|string|max:50',
                'company_mobile' => 'required|string|max:20',
                'company_email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);
        }
        // Step 2 & 3 OTP validation handled separately
        if ($this->step == 4) {
            $this->validate([
                'bank_name' => 'required|string|max:255',
                'card_number' => 'required|string|max:20',
                'expiry_date' => 'required|string|max:10',
                'cvv' => 'required|string|max:4',
            ]);
        }
    }

    public function sendEmailOtp()
    {
        $this->generated_email_otp = rand(100000, 999999);

        // Send OTP email (example)
        // Mail::to($this->company_email)->send(new VerifyEmail($this->generated_email_otp));

        $this->step = 3; // move to phone verification step
    }

    public function sendPhoneOtp()
    {
        $this->generated_phone_otp = rand(100000, 999999);

        // Send SMS via Twilio
        // Twilio::message($this->company_mobile, "Your OTP is {$this->generated_phone_otp}");

        $this->step = 4; // move to bank info step
    }

    public function completeRegistration()
    {
        // Save Company
        $company = Company::create([
            'name' => $this->company_name,
            'mobile' => $this->company_mobile,
            'email' => $this->company_email,
            'number' => $this->company_number,
            // 'subdomain' => generateSubdomain($this->company_name)
        ]);

        // Save User
        $user = User::create([
            'name' => $this->company_name,
            'email' => $this->company_email,
            'password' => Hash::make($this->password),
            'company_id' => $company->id,
            'role' => 'admin',
        ]);

        auth()->login($user);

        return redirect()->route('company.dashboard.home', ['company' => $company->subdomain]);
    }

    public function render()
    {
        return view('livewire.backend.company.auth.company-register');
    }
}
