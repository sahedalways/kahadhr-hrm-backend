<?php

namespace App\Livewire\Backend\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyChargeRate;

class ChargeSettings extends BaseComponent
{
    public $rate;

    protected $rules = [
        'rate' => 'required|numeric|min:0',
    ];

    public function mount()
    {

        $charge = CompanyChargeRate::first();

        if ($charge) {
            $this->rate = $charge->rate;
        }
    }

    public function save()
    {
        $this->validate();


        $charge = CompanyChargeRate::first();
        if ($charge) {
            $charge->update([
                'rate' => $this->rate,
            ]);
        } else {
            CompanyChargeRate::create([
                'rate' => $this->rate,
            ]);
        }

        $this->toast('Charge rate updated successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.backend.settings.charge-settings');
    }
}
