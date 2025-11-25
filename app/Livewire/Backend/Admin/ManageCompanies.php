<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

class ManageCompanies extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $companies, $company, $company_id, $company_name, $business_type, $company_house_number, $address_contact_info, $company_email, $company_mobile, $company_logo, $company_logo_preview, $registered_domain, $calendar_year, $billing_plan_id, $subscription_status, $subscription_start, $subscription_end, $status;

    public $billingPlans;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $statusFilter = '';

    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;
    public $search;

    protected $companyService;

    protected $listeners = ['deleteCompany', 'sortUpdated' => 'handleSort'];




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

        $this->company_name = $this->company->company_name;
        $this->company_house_number = $this->company->company_house_number;
        $this->company_email = $this->company->company_email;
        $this->company_mobile = $this->company->company_mobile;
        $this->business_type = $this->company->business_type;
        $this->address_contact_info = $this->company->address_contact_info;
        $this->registered_domain = $this->company->registered_domain;
        $this->calendar_year = $this->company->calendar_year;
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
            'calendar_year' => 'required|in:english,hmrc',
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
            'calendar_year' => $this->calendar_year,
            'subscription_status' => $this->subscription_status,
            'subscription_start' => $this->subscription_start,
            'subscription_end' => $this->subscription_end,
            'status' => $this->status ? 'Active' : 'Inactive',
        ];

        if ($this->company_logo) {
            $data['company_logo'] = $this->company_logo;
        }

        $this->companyService->updateCompany($this->company, $data);

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
        $this->companyService->deleteCompany($id);
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
}
