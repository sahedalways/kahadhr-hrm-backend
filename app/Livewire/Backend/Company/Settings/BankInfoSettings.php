<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyBankInfo;

class BankInfoSettings extends BaseComponent
{
    public $bank_name, $card_number, $expiry_date, $cvv;
    public $companyBankInfo;

    public $company;

    public function mount()
    {
        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        $this->companyBankInfo = CompanyBankInfo::where('company_id', $this->company->id)->first();

        if ($this->companyBankInfo) {
            $this->bank_name   = $this->companyBankInfo->bank_name;
            $this->card_number = $this->companyBankInfo->card_number;
            $this->expiry_date = $this->companyBankInfo->expiry_date;
            $this->cvv         = $this->companyBankInfo->cvv;
        }
    }

    public function save()
    {
        $validatedData = $this->validate([
            'bank_name'   => 'required|string|max:255',
            'card_number' => 'required|digits_between:12,20',
            'expiry_date' => [
                'required',
                'string',
                'max:5',
                'regex:/^\d{2}\/\d{2}$/',
            ],
            'cvv' => [
                'required',
                'digits_between:3,4',
            ],
        ]);

        if ($this->companyBankInfo) {
            $this->companyBankInfo->update($validatedData);
        } else {
            $validatedData['company_id'] = $this->company->id;
            $this->companyBankInfo = CompanyBankInfo::create($validatedData);
        }

        $this->toast('Bank Info Updated Successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.backend.company.settings.bank-info-settings');
    }
}
