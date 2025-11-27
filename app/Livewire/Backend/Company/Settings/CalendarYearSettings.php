<?php

namespace App\Livewire\Backend\Company\Settings;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CalendarYearSetting;

class CalendarYearSettings extends BaseComponent
{
    public $calendar_year;
    public $companyCalendarYear;

    public $company;

    public function mount()
    {
        $this->company = app('authUser')->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        // Load existing calendar year setting
        $this->companyCalendarYear = CalendarYearSetting::where('company_id', $this->company->id)->first();

        $this->calendar_year = $this->companyCalendarYear->calendar_year ?? 'english';
    }

    public function save()
    {
        $validatedData = $this->validate([
            'calendar_year' => 'required|in:english,hmrc',
        ]);

        if ($this->companyCalendarYear) {
            $this->companyCalendarYear->update($validatedData);
        } else {
            $validatedData['company_id'] = $this->company->id;
            $this->companyCalendarYear = CalendarYearSetting::create($validatedData);
        }

        $this->toast('Calendar Year Setting Updated Successfully!', 'success');
    }

    public function render()
    {
        return view('livewire.backend.company.settings.calendar-year-settings');
    }
}
