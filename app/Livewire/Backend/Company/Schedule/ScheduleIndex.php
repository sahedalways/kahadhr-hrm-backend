<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftBreak;
use Carbon\Carbon;


class ScheduleIndex extends BaseComponent
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
    public $totalBreakMinutes = 0;

    public $employeeSearch = '';

    public $showAddBreakForm = false;

    public $breaks = [];
    public $newBreaks = [];

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
        $this->newShift['employees'][] = (int) $employeeId;
        $this->selectedEmployees[] = $employeeId;
        $this->showAddShiftPanel = true;

        $this->dispatch('shift-panel-opened');
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

    public function publishShift()
    {
        // Validation
        $this->validate([
            'selectedDate' => ['required', 'date'],
            'newShift.title' => 'required|string',
            'newShift.start_time' => 'required|date_format:H:i',
            'newShift.end_time' => 'required|date_format:H:i|after:newShift.start_time',
            'newShift.employees' => 'required|array|min:1',

            // New fields
            'newShift.address' => 'nullable|string|max:255',
            'newShift.note' => 'nullable|string|max:500',
            'newShift.job' => 'required|string|max:100',
        ], [
            'selectedDate.required' => 'Please select a date for the shift.',
            'selectedDate.date' => 'The selected date is not valid.',

            'newShift.title.required' => 'Shift title is required.',

            'newShift.start_time.required' => 'Start time is required.',
            'newShift.start_time.date_format' => 'Start time must be a valid time.',
            'newShift.end_time.required' => 'End time is required.',
            'newShift.end_time.date_format' => 'End time must be a valid time.',
            'newShift.end_time.after' => 'End time must be after the start time.',

            'newShift.employees.required' => 'Please select at least one employee for this shift.',
            'newShift.employees.array' => 'Employee selection must be valid.',

            'newShift.address.string' => 'Address must be a valid text.',
            'newShift.address.max' => 'Address cannot exceed 255 characters.',

            'newShift.note.string' => 'Note must be a valid text.',
            'newShift.note.max' => 'Note cannot exceed 500 characters.',



            'newShift.job.required' => 'Shift job title is required.',
            'newShift.job.string' => 'Job must be a valid text.',
            'newShift.job.max' => 'Job cannot exceed 100 characters.',
        ]);



        $shift = Shift::create([
            'company_id' => $this->company_id,
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
            'total_hours' => $this->newShift['total_hours'],
        ]);

        $shiftDate->employees()->attach($this->newShift['employees']);

        $this->closeAddShiftPanel();
        $this->dispatch('refreshSchedule');
        $this->toast('Shift has been published successfully!', 'success');
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

        $this->startDate = Carbon::today();
        $this->endDate = Carbon::today()->copy()->addDays(6);
        $this->currentDate = Carbon::today();
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
            [$hours, $minutes] = explode(':', '08:00');
            $totalMinutes = ($hours * 60) + $minutes;

            if (!empty($this->totalBreakMinutes)) {
                $totalMinutes -= $this->totalBreakMinutes;
                $totalMinutes = max(0, $totalMinutes);
            }


            $newHours = floor($totalMinutes / 60);
            $newMinutes = $totalMinutes % 60;

            $this->newShift['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
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



    public function addBreakRow()
    {
        $this->showAddBreakForm = true;

        $this->newBreaks[] = [
            'name' => '',
            'type' => '',
            'duration' => '0.10',
            'new' => true,
        ];
    }

    public function removeBreakRow($index)
    {
        unset($this->newBreaks[$index]);
        $this->newBreaks = array_values($this->newBreaks);
    }


    public function getDefaultBreaks()
    {
        $this->showAddBreakForm = false;

        $this->newBreaks = [];


        $this->breaks = ShiftBreak::where('company_id', $this->company_id)
            ->orderBy('title')
            ->get()
            ->toArray();
    }



    public function addExistingBreak($breakId)
    {
        $break = ShiftBreak::find($breakId);

        if ($break) {
            $this->newBreaks[] = [
                'name' => $break->title,
                'type' => $break->type,
                'duration' => $break->duration,
            ];

            $this->showAddBreakForm = true;
        }
    }


    public function confirmBreaksAndSave()
    {


        foreach ($this->newBreaks as $break) {
            if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {


                if (strtolower($break['type']) == 'paid') {
                    $duration = (float) $break['duration'];
                    $hours = floor($duration);
                    $minutes = ($duration - $hours) * 100;
                    $breakMinutes = $hours * 60 + $minutes;

                    $this->totalBreakMinutes += $breakMinutes;
                }


                $exists = ShiftBreak::where('company_id', $this->company_id)
                    ->where('title', $break['name'])
                    ->exists();

                if (!$exists) {
                    ShiftBreak::create([
                        'company_id' => $this->company_id,
                        'title' => $break['name'],
                        'type' => ucfirst($break['type']),
                        'duration' => $break['duration'],
                    ]);
                }
            }
        }


        if ($this->totalBreakMinutes > 0 && $this->newShift['total_hours']) {
            [$shiftHours, $shiftMinutes] = explode(':', $this->newShift['total_hours']);
            $shiftTotalMinutes = ($shiftHours * 60) + $shiftMinutes;

            $shiftTotalMinutes -= $this->totalBreakMinutes;
            $shiftTotalMinutes = max(0, $shiftTotalMinutes);

            $newHours = floor($shiftTotalMinutes / 60);
            $newMinutes = $shiftTotalMinutes % 60;

            $this->newShift['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
        }

        // Clear newBreaks and hide form
        $this->newBreaks = [];
        $this->showAddBreakForm = false;

        $this->getDefaultBreaks();

        $this->dispatch('closemodal');
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
