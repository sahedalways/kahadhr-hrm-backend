<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CalendarYearSetting;
use App\Models\Company;
use App\Models\User;
use App\Services\API\VerificationService;
use App\Services\CompanyService;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

class ManageCompanies extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $companies, $company, $company_id, $company_name, $business_type, $company_house_number, $address_contact_info, $company_email, $company_mobile, $company_logo, $company_logo_preview, $registered_domain, $calendar_year = 'english', $billing_plan_id, $subscription_status, $subscription_start, $subscription_end, $status;

    public $billingPlans;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = 'Active';

    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;

    public $otp = [],  $showOtpModal = false;
    public $updating_field;
    public $code_sent = false;
    public $otpCooldown = 0;
    public $new_email;
    public $new_mobile;
    public $verification_code;
    public $search;

    protected $companyService;

    protected $listeners = ['deleteCompany', 'sortUpdated' => 'handleSort', 'openModal', 'tick'];




    public function boot(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {

        return view('livewire.backend.admin.manage-companies', [
            'infos' => $this->loaded
        ]);
    }

    /* Reset input fields */
    public function resetInputFields()
    {
        $this->company = null;
        $this->company_name = '';
        $this->business_type = '';
        $this->address_contact_info = '';
        $this->company_email = '';
        $this->company_mobile = '';
        $this->calendar_year = 'english';
        $this->company_logo = null;
        $this->company_logo_preview = null;
        $this->resetErrorBag();
    }



    public function manageCompanyProfile($id)
    {
        $this->editMode = true;
        $this->company = $this->companyService->getCompany($id);

        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }


        $this->company->load('calendarYearSetting');

        $this->company_name = $this->company->company_name;
        $this->company_house_number = $this->company->company_house_number;
        $this->company_email = $this->company->company_email;
        $this->company_mobile = $this->company->company_mobile;
        $this->business_type = $this->company->business_type;
        $this->address_contact_info = $this->company->address_contact_info;
        $this->registered_domain = $this->company->registered_domain;
        $this->calendar_year = $this->company->calendarYearSetting->calendar_year ?? null;
        $this->subscription_status = $this->company->subscription_status;
        $this->subscription_start = $this->company->subscription_start
            ? Carbon::parse($this->company->subscription_start)->format('Y-m-d')
            : null;

        $this->subscription_end = $this->company->subscription_end
            ? Carbon::parse($this->company->subscription_end)->format('Y-m-d')
            : null;
        $this->status = $this->company->status == 'Active';
        $this->company_logo_preview = $this->company->company_logo_url;
    }




    public function update()
    {
        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }


        $this->validate([
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'company_name')->ignore($this->company->id),
            ],
            'company_house_number' => 'required|string|max:255',
            'company_email' => ['required', 'email', Rule::unique('companies', 'company_email')->ignore($this->company->id)],
            'company_mobile' => ['required', 'string', 'max:20', Rule::unique('companies', 'company_mobile')->ignore($this->company->id)],
            'business_type' => 'nullable|string|max:255',
            'address_contact_info' => 'nullable|string|max:500',
            'registered_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(?!:\/\/)([a-zA-Z0-9-_]+\.)+[a-zA-Z]{2,6}$/',
                Rule::unique('companies', 'registered_domain')->ignore($this->company->id),
            ],
            'calendar_year' => 'nullable|in:english,hmrc',
            'subscription_status' => 'required|in:active,trial,expired,suspended',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date|after:subscription_start',
            'company_logo' => 'nullable|image|max:2048',
        ]);



        $data = [
            'company_name' => $this->company_name,
            'company_house_number' => $this->company_house_number,
            'company_email' => $this->company_email,
            'company_mobile' => $this->company_mobile,
            'business_type' => $this->business_type,
            'address_contact_info' => $this->address_contact_info,
            'registered_domain' => $this->registered_domain,
            'subscription_status' => $this->subscription_status,
            'subscription_start' => $this->subscription_start,
            'subscription_end' => $this->subscription_end !== '' ? $this->subscription_end : null,
            'status' => $this->status ? 'Active' : 'Inactive',
        ];

        if ($this->company_logo) {
            $data['company_logo'] = $this->company_logo;
        }

        $this->companyService->updateCompany($this->company, $data);



        if ($this->calendar_year) {
            CalendarYearSetting::updateOrCreate(
                ['company_id' => $this->company->id],
                ['calendar_year' => $this->calendar_year]
            );
        }


        $this->resetInputFields();
        $this->editMode = false;
        $this->dispatch('closemodal');
        $this->toast('Company updated successfully!', 'success');
        $this->resetLoaded();
    }



    /* Search companies */
    public function searchCompanies()
    {
        $this->resetLoaded();
    }

    /* Load more */
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Company::query();

        if ($this->search && $this->search != '') {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('company_name', 'like', $searchTerm)
                    ->orWhere('business_type', 'like', $searchTerm)
                    ->orWhere('company_email', 'like', $searchTerm)
                    ->orWhere('company_mobile', 'like', $searchTerm);
            });
        }


        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }


        if ($this->lastId) {
            if ($this->sortOrder === 'desc') {
                $query->where('id', '<', $this->lastId);
            } else {
                $query->where('id', '>', $this->lastId);
            }
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);
    }


    /* Reset loaded */
    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    /* Delete company */
    public function deleteCompany($id)
    {
        $company = Company::findOrFail($id);

        if ($company->user) {
            $company->user->delete();
        }

        $company->delete();


        $this->toast('Company deleted successfully!', 'success');
        $this->resetLoaded();
    }


    public function exportCompanies($type)
    {
        $data = $this->loaded;


        $columns = [

            'User ID',
            'Company Name',
            'House Number',
            'Phone',
            'Email',
            'Business Type',
            'Address',

        ];


        $keys = [

            'user_id',
            'company_name',
            'company_house_number',
            'company_mobile',
            'company_email',
            'business_type',
            'address_contact_info',

        ];

        return $this->export(
            $data,
            $type,
            'companies',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Company List',
                'columns' => $columns,
                'keys' => $keys
            ]
        );
    }

    public function updatedSearch()
    {

        $this->resetLoaded();
    }




    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }


    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }





    public function toggleStatus($id)
    {
        $company = Company::find($id);

        if (!$company) {
            $this->toast('Company not found!', 'error');
            return;
        }

        $company->status = $company->status == 'Active' ? "Inactive" : "Active";
        $company->save();

        $this->toast('Status updated successfully!', 'success');


        $this->resetLoaded();
    }



    public function openModal($field)
    {
        $this->resetVerificationFields();
        $this->updating_field = $field;
        $this->code_sent = false;
        $this->verification_code = null;
    }



    public function requestVerification($field, VerificationService $verificationService)
    {
        $this->updating_field = $field;

        if ($field === 'email') {
            $this->validate([
                'new_email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('companies', 'company_email')->ignore($this->company->id),
                ],
            ]);

            $emailExists =
                User::where('email', $this->new_email)->where('id', '!=', $this->company->user_id)->exists();

            if ($emailExists) {
                $this->toast('This email is already in use.', 'error');
                return;
            }

            $target = $this->new_email;
        } else {


            $this->validate([
                'new_mobile' => [
                    'required',
                    'string',
                    'min:10',
                    'max:20',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users', 'phone_no')->ignore($this->company->user_id),
                ],
            ]);


            $mobileExists =
                Company::where('company_mobile', $this->new_mobile)
                ->where('id', '!=', $this->company->id)
                ->exists();

            if ($mobileExists) {
                $this->toast('This phone number is already in use.', 'error');
                return;
            }

            $target = $this->new_mobile;
        }




        $sent = false;
        if ($field === 'email') {
            $sent = $verificationService->sendEmailOtp($target, null);
        } else {
            $sent = $verificationService->sendPhoneOtp($target, null);
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
                $this->company->update(['company_email' => $this->new_email]);
            } else {
                $this->company_mobile = $this->new_mobile;
                $this->company->user->update(['phone_no' => $this->new_mobile]);
                $this->company->update(['company_mobile' => $this->new_mobile]);
            }


            $this->toast(ucfirst($this->updating_field) . " has been changed successfully.", 'success');
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
