<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Models\Employee;
use App\Models\Shift;
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
    public $selectedEmployees = [];
    public $hoveredDate;
    public $showAddShiftPanel = false;
    public $selectedDate;

    public $hoveredCell;

    public $employeeSearch = '';

    public $newShift = [
        'title' => '',
        'job' => '',
        'start_time' => '09:00',
        'end_time' => '17:00',
        'total_hours' => '08:00',
        'color' => '',
        'address' => '',
        'note' => '',
        'all_day' => false,
        'employees' => [],
    ];




    public function getSelectedShiftEmployeesProperty()
    {

        return Employee::whereIn('id', $this->newShift['employees'])->get();
    }


    public function getAvailableShiftEmployeesProperty()
    {

        $query = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->whereNotIn('id', $this->newShift['employees']);

        // Apply search filter
        if ($this->employeeSearch) {
            $search = '%' . $this->employeeSearch . '%';
            $query->where(function ($q) use ($search) {
                $q->where('f_name', 'like', $search)
                    ->orWhere('l_name', 'like', $search)
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) like ?", [$search]);
            });
        }

        return $query->orderBy('f_name')->get();
    }


    public function addEmployeeToShift($employeeId)
    {
        if (!in_array($employeeId, $this->newShift['employees'])) {
            $this->newShift['employees'][] = (int) $employeeId;
            $this->employeeSearch = '';
        }
    }


    public function removeEmployeeFromShift($employeeId)
    {
        $this->newShift['employees'] = array_diff($this->newShift['employees'], [(int) $employeeId]);
        $this->newShift['employees'] = array_values($this->newShift['employees']);
    }



    public function openAddShiftPanel($date, $employeeId)
    {
        $this->selectedDate = $date;
        $this->selectedEmployees[] = $employeeId;
        $this->showAddShiftPanel = true;
    }
    public function closeAddShiftPanel()
    {
        $this->showAddShiftPanel = false;
        $this->reset('newShift');
    }



    public function getShiftsByDate()
    {
        $grouped = [];
        foreach ($this->shifts as $shift) {
            foreach ($shift['dates'] as $date) {
                $grouped[$date][] = $shift;
            }
        }
        return $grouped;
    }

    public function saveShift()
    {
        // Validation
        $this->validate([
            'newShift.title' => 'required|string',
            'newShift.start_time' => 'required',
            'newShift.end_time' => 'required|after:newShift.start_time',
            'newShift.employees' => 'required|array',
        ]);

        $shift = Shift::create([
            'title' => $this->newShift['title'],
            'job' => $this->newShift['job'],
            'color' => $this->newShift['color'],
            'address' => $this->newShift['address'],
            'note' => $this->newShift['note'],
        ]);

        $shiftDate = $shift->dates()->create([
            'date' => $this->selectedDate,
            'start_time' => $this->newShift['start_time'],
            'end_time' => $this->newShift['end_time'],
            'total_hours' => Carbon::parse($this->newShift['end_time'])->diffInHours(Carbon::parse($this->newShift['start_time'])),
        ]);

        $shiftDate->employees()->attach($this->newShift['employees']);

        $this->closeAddShiftPanel();
        $this->emit('refreshSchedule'); // optional
    }


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



    public function toggleOnAllDay($value)
    {
        $this->newShift['all_day'] = $value;

        if ($value) {
            $this->newShift['start_time'] = '09:00';
            $this->newShift['end_time'] = '17:00';
            $this->newShift['total_hours'] = '08:00';
        }
    }



    public function calculateTotalHours()
    {
        if ($this->newShift['start_time'] && $this->newShift['end_time']) {
            $start = \Carbon\Carbon::createFromFormat('H:i', $this->newShift['start_time']);
            $end = \Carbon\Carbon::createFromFormat('H:i', $this->newShift['end_time']);


            if ($end->lt($start)) {
                $end->addDay();
            }

            $diff = $end->diffInMinutes($start, false);
            $diff = abs($diff);

            $hours = floor($diff / 60);
            $minutes = $diff % 60;

            $this->newShift['total_hours'] = sprintf('%02d:%02d', $hours, $minutes);
        }
    }



    public function getFilteredEmployeesProperty()
    {
        return $this->employees->filter(function ($employee) {
            if (!$this->search) return true;
            $name = strtolower($employee->f_name . ' ' . $employee->l_name);
            return str_contains($name, strtolower($this->search));
        });
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
