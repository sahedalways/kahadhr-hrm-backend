<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\Exportable;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;

class ManageCompanies extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $companies, $company, $company_id, $company_name, $business_type, $address_contact_info, $company_email, $company_mobile, $company_logo, $company_logo_preview;
    public $perPage = 10;
    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $editMode = false;
    public $search;

    protected $companyService;

    protected $listeners = ['deleteCompany'];

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



    /* Edit company */
    public function edit($id)
    {
        $this->editMode = true;
        $this->company = $this->companyService->getCompany($id);

        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }

        $this->company_name = $this->company->company_name;
        $this->business_type = $this->company->business_type;
        $this->address_contact_info = $this->company->address_contact_info;
        $this->company_email = $this->company->company_email;
        $this->company_mobile = $this->company->company_mobile;
        $this->company_logo_preview = $this->company->company_logo_url;
    }

    /* Update company */
    public function update()
    {
        if (!$this->company) {
            $this->toast('Company not found!', 'error');
            return;
        }

        $this->validate([
            'company_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'address_contact_info' => 'nullable|string|max:500',
            'company_email' => [
                'nullable',
                'email',
                Rule::unique('companies', 'company_email')->ignore($this->company->id),
            ],
            'company_mobile' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('companies', 'company_mobile')->ignore($this->company->id),
            ],
            'company_logo' => 'nullable|mimes:jpeg,jpg,png,webp,heic,heif|max:2048',
        ]);

        $data = [
            'company_name' => $this->company_name,
            'business_type' => $this->business_type,
            'address_contact_info' => $this->address_contact_info,
            'company_email' => $this->company_email,
            'company_mobile' => $this->company_mobile,
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
            $query->where('company_name', 'like', $searchTerm)
                ->orWhere('business_type', 'like', $searchTerm)
                ->orWhere('company_email', 'like', $searchTerm)
                ->orWhere('company_mobile', 'like', $searchTerm);
        }

        if ($this->lastId) {
            $query->where('id', '<', $this->lastId);
        }

        $items = $query->orderBy('id', 'desc')
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $items->last()->id;
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
        $data = Company::all();


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
                'title' => 'Company List',
                'columns' => $columns,
                'keys' => $keys
            ]
        );
    }
}
