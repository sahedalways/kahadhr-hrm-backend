<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Services\API\VerificationService;
use Livewire\WithFileUploads;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;


class ProfileSettings extends BaseComponent
{
    use WithFileUploads;

    public $company_name, $sub_domain, $company_house_number;
    public $company_mobile, $company_email, $business_type;
    public $address_contact_info, $registered_domain, $calendar_year;
    public $company_logo, $old_company_logo;

    public $new_email;
    public $new_mobile;

    public $code_sent = false;
    public $otpCooldown = 0;
    public $otp = [];


    public $verification_code;
    public $generated_code;


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

        $this->company_name          = $this->company->company_name;
        $this->sub_domain            = $this->company->sub_domain;
        $this->company_house_number  = $this->company->company_house_number;
        $this->company_mobile        = $this->company->company_mobile;
        $this->company_email         = $this->company->company_email;
        $this->business_type         = $this->company->business_type;
        $this->address_contact_info  = $this->company->address_contact_info;
        $this->registered_domain     = $this->company->registered_domain;
        $this->calendar_year         = $this->company->calendar_year;

        $this->old_company_logo = $this->company->company_logo_url;
    }

    /* Save company profile settings */
    public function save()
    {
        $validatedData = $this->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_house_number' => 'required|string|max:255',
            'company_mobile' => ['required', 'string', 'max:20', Rule::unique('companies', 'company_mobile')->ignore($this->company->id)],
            'business_type' => 'nullable|string|max:255',
            'address_contact_info' => 'nullable|string',
            'registered_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,6}$/',
                Rule::unique('companies', 'registered_domain')->ignore($this->company->id),
            ],
            'calendar_year' => 'nullable|in:english,hmrc',
            'company_logo' => 'nullable|image|max:2048',
        ]);

        $company = $this->company;

        $companyNameChanged = $company->company_name !== $this->company_name;


        if ($this->company_logo instanceof UploadedFile) {
            $image = $this->company_logo;


            if ($company->company_logo && file_exists(storage_path('app/public/' . $company->company_logo))) {
                unlink(storage_path('app/public/' . $company->company_logo));
            }

            $directory = storage_path('app/public/image/company/logo');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();

            $img = Image::read($image->getRealPath());
            $img->save($directory . '/' . $filename);

            $company->company_logo = 'image/company/logo/' . $filename;
        }

        $company->update([
            'company_name'          => $this->company_name,
            'company_house_number'  => $this->company_house_number,
            'business_type'         => $this->business_type,
            'address_contact_info'  => $this->address_contact_info,
            'registered_domain'     => $this->registered_domain,
            'calendar_year'         => $this->calendar_year,
            'company_logo'          => $company->company_logo,
            'company_mobile'          => $this->company_mobile,
        ]);


        if ($companyNameChanged) {

            session()->flash('settingsUpdatedMessage', 'Profile Settings Updated. Please log in again due to company sub domain change.');


            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            $newSubdomain = $company->sub_domain;
            $baseDomain = preg_replace('/^https?:\/\//', '', config('app.base_domain'));

            $loginUrl = "http://{$newSubdomain}.{$baseDomain}?message=" . urlencode('Profile Settings Updated. Please log in again due to company sub domain change.') . "&type=info";


            return redirect()->to($loginUrl);
        }


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

        $this->generated_code = rand(100000, 999999);

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

            $this->toast("{$this->updating_field} updated successfully!", 'success');


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
        $this->generated_code = null;
        $this->updating_field = null;
        $this->code_sent = false;
        $this->otpCooldown = 0;
    }
}
