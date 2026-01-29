<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Company;
use App\Services\CompanyService;
use App\Traits\Exportable;
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

    public $search;

    protected $companyService;

    protected $listeners = ['sortUpdated' => 'handleSort', 'openModal'];




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
}
