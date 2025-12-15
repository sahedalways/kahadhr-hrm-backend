<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;

class ScheduleIndex extends Component
{
    public $startDate;
    public $endDate;
    public $currentDate;
    public $employees;

    public $company_id;
    public $search;
    public $viewMode = 'weekly';


    public $shifts = [

        1 => [
            '2025-10-27' => ['type' => 'Shift', 'time' => '9:00 AM - 5:00 PM', 'color' => 'bg-success'],
        ],

        2 => [
            '2025-10-27' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-10-28' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-10-29' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-10-30' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-10-31' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-11-01' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
            '2025-11-02' => ['type' => 'Leave', 'label' => 'Unpaid Leave', 'all_day' => true],
        ],
    ];
    public function mount()
    {
        $this->company_id = auth()->user()->company->id;
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();

        $this->startDate = Carbon::createFromDate(2025, 10, 27);
        $this->endDate = Carbon::createFromDate(2025, 11, 2);
        $this->currentDate = Carbon::createFromDate(2025, 10, 31);
    }


    public function updatedSearch()
    {
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('f_name', 'like', '%' . $this->search . '%')
                        ->orWhere('l_name', 'like', '%' . $this->search . '%')
                        ->orWhereRaw("CONCAT(f_name, ' ', l_name) like ?", ['%' . $this->search . '%']);
                });
            })
            ->orderBy('f_name')
            ->get();
    }




    public function getWeekDaysProperty()
    {
        $days = [];
        $current = $this->startDate->copy();
        $end = $this->endDate->copy();

        $maxIterations = $this->viewMode === 'monthly' ? $this->startDate->daysInMonth : 7;
        $count = 0;

        while ($current->lte($end) && $count < $maxIterations) {
            $days[] = [
                'full_date' => $current->format('Y-m-d'),

                'day' => $this->viewMode === 'weekly' ? $current->format('D') : $current->format('j'),
                'date' => $current->format('m/d'),
                'highlight' => $current->equalTo($this->currentDate->copy()->startOfDay()),
            ];
            $current->addDay();
            $count++;
        }

        return $days;
    }

    public function getCellContent($employeeId, $date)
    {
        return $this->shifts[$employeeId][$date] ?? null;
    }



    public function setViewMode($mode)
    {
        $this->viewMode = $mode;

        if ($mode === 'weekly') {
            $this->endDate = $this->startDate->copy()->addDays(6);
        } elseif ($mode === 'monthly') {
            $this->startDate = $this->startDate->copy()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
        }
    }

    public function goToPrevious()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate->subWeek();
            $this->endDate->subWeek();
            $this->currentDate->subWeek();
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate->subMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->currentDate->subMonth();
        }
    }

    public function goToNext()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate->addWeek();
            $this->endDate->addWeek();
            $this->currentDate->addWeek();
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate->addMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->currentDate->addMonth();
        }
    }

    public function getDisplayDateRangeProperty()
    {
        if ($this->viewMode === 'weekly') {
            return $this->startDate->format('M d') . ' - ' . $this->endDate->format('M d');
        } elseif ($this->viewMode === 'monthly') {
            return $this->startDate->format('F Y');
        }
    }





    public function render()
    {
        return view('livewire.backend.company.schedule.schedule-index', [
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,
            'employees' => $this->employees,
            'displayDateRange' => $this->displayDateRange,
        ]);
    }
}
