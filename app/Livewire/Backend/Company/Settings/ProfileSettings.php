<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
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

    public $company;



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
}
