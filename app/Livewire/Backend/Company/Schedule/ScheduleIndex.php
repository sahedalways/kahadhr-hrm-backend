<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftBreak;
use App\Models\ShiftTemplates;
use Carbon\Carbon;


class ScheduleIndex extends BaseComponent
{
    public $startDate;
    public $endDate;
    public $currentDate;
    public $employees;
    public $shiftEmployees;
    public $multipleShiftEmployees;

    public $company_id;
    public $search;
    public $viewMode = 'weekly';
    public $selectedEmployees = [];
    public $hoveredDate;
    public $showAddShiftPanel = false;
    public $selectedDate;

    public $hoveredCell;
    public $totalBreakMinutes = 0;
    public $originalShiftTotalTime = 0;

    public $employeeSearch = '';
    public $shiftEmployeeSearch = '';
    public $multipleShiftEmployeeSearch = [];

    public $showAddBreakForm = false;

    public $breaks = [];
    public $newBreaks = [];
    public $paidBreaksCount = 0;
    public $unpaidBreaksCount = 0;
    public $paidBreaksDuration = '00:00';
    public $unpaidBreaksDuration = '00:00';

    public $frequency = 'Monthly';
    public $every = 5;
    public $everyOptions = [];
    public $repeatOptions = [];
    public $repeatOn = '';
    public $endRepeat = 'After';
    public $occurrences = 5;
    public $isSavedRepeatShift = false;
    public $isShiftTempTab = false;

    public $templates = [];

    public $showAddUserPanel = false;
    public $isClickMultipleShift = false;


    public $multipleShifts = [];

    public $showMultipleShiftAddUserPanel = [];


    public $newShift = [
        'title' => '',
        'job' => '',
        'start_time' => '09:00',
        'end_time' => '17:00',
        'total_hours' => '08:00',
        'color' => '#0000',
        'address' => '',
        'note' => '',
        'all_day' => false,
        'employees' => [],
    ];




    public function addShiftRow()
    {
        $this->multipleShifts[] = [
            'date' => null,
            'all_day' => false,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'total_hours' => '08:00',

            'title' => '',
            'job' => '',
            'color' => '#000000',

            'employees' => [],


            'breaks' => [],
            'newBreaks' => [],

            'address' => '',
            'note' => '',
        ];


        $index = count($this->multipleShifts) - 1;


        $this->multipleShiftEmployees[$index] = $this->employees;


        $this->showMultipleShiftAddUserPanel[$index] = false;
        $this->multipleShiftEmployeeSearch[$index] = '';
    }



    public function addEmployeeToMultipleShift($shiftIndex, $employeeId)
    {
        $selected = $this->multipleShifts[$shiftIndex]['employees'] ?? [];

        if (!in_array($employeeId, array_column($selected, 'id'))) {
            $employee = Employee::find($employeeId);
            $this->multipleShifts[$shiftIndex]['employees'][] = [
                'id' => $employee->id,
                'full_name' => $employee->full_name,
                'avatar_url' => $employee->avatar_url,
            ];
        }
    }


    public function removeEmployeeFromMultipleShift($shiftIndex, $employeeId)
    {
        $this->multipleShifts[$shiftIndex]['employees'] = array_values(
            array_filter($this->multipleShifts[$shiftIndex]['employees'], fn($e) => $e['id'] !== $employeeId)
        );
    }




    public function removeShiftRow($index)
    {
        unset($this->multipleShifts[$index]);
        $this->multipleShifts = array_values($this->multipleShifts);
    }

    public function clickMultipleShiftModal()
    {
        $this->isClickMultipleShift =  true;
    }

    public function clickTempTab()
    {
        $this->isShiftTempTab =  true;
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = ShiftTemplates::where('company_id', $this->company_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function clickShiftDetailsTab()
    {
        $this->isShiftTempTab =  false;
    }




    public function applyTemplate($id)
    {

        $template = ShiftTemplates::find($id);

        if ($template) {
            $this->newShift = [
                'title'       => $template->title,
                'job'         => $template->job ?? '',
                'start_time'  => \Carbon\Carbon::parse($template->start_time)->format('H:i'),
                'end_time'    => \Carbon\Carbon::parse($template->end_time)->format('H:i'),

                'total_hours' => $this->calculateTotalHours(),
                'color'       => $template->color ?? '#0000',
                'address'     => $template->address ?? '',
                'note'        => $template->note ?? '',
                'all_day'     => $template->all_day ?? false,
                'employees'   => $template->employees ?? [],
            ];


            $this->isShiftTempTab = false;
        }
    }



    public function handleChangeFrequency($value)
    {
        $this->frequency = $value;
        $this->setEveryOptions();
        $this->every = reset($this->everyOptions);
    }


    private function setEveryOptions()
    {
        if ($this->frequency === 'Daily') {
            $this->everyOptions = range(1, 30);
        } elseif ($this->frequency === 'Weekly') {
            $this->everyOptions = range(1, 2);
        } else {
            $this->everyOptions = range(1, 12);
        }

        $this->repeatOn = null;
        $this->updateRepeatOptions();
    }


    public function saveAsTemplate()
    {
        $this->validate([
            'selectedDate'        => 'required|date',
            'newShift.title'      => 'required|string|max:255',
            'newShift.job'      => 'required|string|max:255',
            'newShift.start_time' => 'required|date_format:H:i',
            'newShift.end_time'   => 'required|date_format:H:i|after:newShift.start_time',
        ], [
            'selectedDate.required' => 'Please select a date for the template.',
            'selectedDate.date'     => 'Selected date must be a valid date.',
        ]);

        ShiftTemplates::create([
            'company_id' => $this->company_id,
            'title'      => $this->newShift['title'],
            'job'        => $this->newShift['job'],
            'color'      => $this->newShift['color'],
            'address'    => $this->newShift['address'],
            'note'       => $this->newShift['note'],
            'start_time' => $this->newShift['start_time'],
            'end_time'   => $this->newShift['end_time'],
            'created_at' => $this->selectedDate,
        ]);

        $this->toast('Template saved successfully!', 'success');
    }




    protected function updateRepeatOptions()
    {
        if ($this->frequency === 'Monthly') {
            $this->repeatOptions = [
                'First Sunday',
                'First Monday',
                'First Tuesday',
                'First Wednesday',
                'First Thursday',
                'First Friday',
                'First Saturday',
                'Second Sunday',
                'Second Monday',
                'Second Tuesday',
                'Second Wednesday',
                'Second Thursday',
                'Second Friday',
                'Second Saturday',
                'Third Sunday',
                'Third Monday',
                'Third Tuesday',
                'Third Wednesday',
                'Third Thursday',
                'Third Friday',
                'Third Saturday',
                'Last Sunday',
                'Last Monday',
                'Last Tuesday',
                'Last Wednesday',
                'Last Thursday',
                'Last Friday',
                'Last Saturday',
                'End Of Month',
                'First Of Month',
                'Middle Of Month',
                'Last Of Month',
            ];
        } elseif ($this->frequency === 'Weekly') {
            $this->repeatOptions = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        } else {
            $this->repeatOptions = [];
        }
    }


    protected function ordinal($number)
    {
        $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }



    public function toggleAddUserPanel()
    {
        $this->showAddUserPanel = !$this->showAddUserPanel;
    }


    public function toggleMultipleShiftAddUserPanel($index)
    {
        $this->showMultipleShiftAddUserPanel[$index] = !($this->showMultipleShiftAddUserPanel[$index] ?? false);
    }





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



    public function getAvailableMultipleShiftEmployeesProperty()
    {
        $available = [];

        foreach ($this->multipleShifts as $index => $shift) {
            $selectedIds = collect($shift['employees'] ?? [])
                ->map(fn($e) => $e['id'] ?? $e->id)
                ->toArray();

            $employees = collect($this->multipleShiftEmployees[$index] ?? []);

            $search = $this->multipleShiftEmployeeSearch[$index] ?? null;

            if ($search) {
                $like = strtolower($search);
                $employees = $employees->filter(function ($e) use ($selectedIds, $like) {
                    $id = $e['id'] ?? $e->id;
                    $fullName = strtolower($e['full_name'] ?? $e->full_name);
                    return !in_array($id, $selectedIds) && str_contains($fullName, $like);
                });
            } else {
                $employees = $employees->whereNotIn('id', $selectedIds);
            }

            $available[$index] = $employees->values();
        }

        return $available;
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



    public function openAddShiftPanelForMonth($date)
    {
        $this->selectedDate = $date;
        $this->resetFields();

        $this->isClickMultipleShift =  false;

        $this->dispatch('shift-panel-opened');
    }





    public function openAddShiftPanel($date, $employeeId)
    {
        $this->selectedDate = $date;
        $this->newShift['employees'][] = (int) $employeeId;
        $this->selectedEmployees[] = $employeeId;

        $this->isClickMultipleShift =  false;

        $this->resetFields();

        $this->dispatch('shift-panel-opened');
    }


    public function resetFields()
    {
        $this->showAddShiftPanel = true;
        $this->originalShiftTotalTime = '8:00';
        $this->breaks = [];
        $this->newBreaks = [];
        $this->paidBreaksCount = 0;
        $this->unpaidBreaksCount = 0;
        $this->paidBreaksDuration = '00:00';
        $this->unpaidBreaksDuration = '00:00';
        $this->frequency = 'Monthly';
        $this->every = 1;
        $this->isSavedRepeatShift = false;
        $this->isShiftTempTab = false;
        $this->showAddUserPanel = false;
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function resetRepeatFields()
    {
        $this->frequency = 'Monthly';
        $this->every = 1;
        $this->isSavedRepeatShift = false;
        $this->repeatOn = '';
        $this->occurrences = 5;
        $this->endRepeat = 'After';
        $this->everyOptions = [];
        $this->repeatOptions = [];
    }




    public function updatedShiftEmployeeSearch($value)
    {
        $this->shiftEmployeeSearch = $value;
        $this->loadShiftEmployees();
    }


    protected function loadEmployees()
    {
        $query = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id');

        if (!empty($this->employeeSearch)) {
            $query->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->employeeSearch . '%')
                    ->orWhere('l_name', 'like', '%' . $this->employeeSearch . '%');
            });
        }

        $this->employees = $query->orderBy('f_name')->get();
    }








    protected function loadShiftEmployees()
    {
        $query = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id');

        if (!empty($this->shiftEmployeeSearch)) {
            $query->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->shiftEmployeeSearch . '%')
                    ->orWhere('l_name', 'like', '%' . $this->shiftEmployeeSearch . '%');
            });
        }

        $this->shiftEmployees = $query->orderBy('f_name')->get();
    }




    public function closeAddShiftPanel()
    {
        $this->showAddShiftPanel = false;
        $this->reset('newShift');
    }



    public function saveRepeatShift()
    {
        $rules = [
            'frequency' => 'required|in:Daily,Weekly,Monthly',
            'every'     => 'required|integer|min:1',
            'endRepeat' => 'required|in:After,On',
            'occurrences' => 'required_if:endRepeat,After|integer|min:1',
        ];


        if (in_array($this->frequency, ['Weekly', 'Monthly'])) {
            $rules['repeatOn'] = 'required|string';
        }

        $this->validate($rules);



        $this->isSavedRepeatShift = true;

        $this->dispatch('close-repeat-shift-modal');
    }


    public function cancelRepeatShift()
    {
        $this->resetRepeatFields();
        $this->setEveryOptions();
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
            'newBreaks' => 'nullable|array',
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




        $dates = [];
        $currentDate = $this->selectedDate;
        $count = 0;

        if ($this->isSavedRepeatShift) {
            while (true) {
                $dates[] = $currentDate;
                $count++;

                if ($this->endRepeat === 'After' && $count >= $this->occurrences) {
                    break;
                }
                if ($this->endRepeat === 'On' && \Carbon\Carbon::parse($currentDate) >= \Carbon\Carbon::parse($this->endRepeatDate)) {
                    break;
                }

                $currentDate = $this->getNextOccurrence($currentDate);
            }
        }


        foreach ($dates as $date) {
            $shiftDate = $shift->dates()->create([
                'date' => $date,
                'start_time' => $this->newShift['start_time'],
                'end_time' => $this->newShift['end_time'],
                'total_hours' => $this->newShift['total_hours'],
            ]);

            $shiftDate->employees()->attach($this->newShift['employees']);

            if (!empty($this->newBreaks)) {
                foreach ($this->newBreaks as $break) {
                    if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {
                        $shiftDate->breaks()->create([
                            'title' => $break['name'],
                            'type' => $break['type'],
                            'duration' => $break['duration'],
                        ]);
                    }
                }
            }
        }



        $this->closeAddShiftPanel();
        $this->cancelRepeatShift();
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
        $this->loadEmployees();
        $this->shiftEmployees = $this->employees;
        $this->addShiftRow();

        $this->setEveryOptions();
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

            $this->newShift['total_hours']  = '08:00';
            $this->originalShiftTotalTime = $this->newShift['total_hours'];


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
            $this->originalShiftTotalTime = $this->newShift['total_hours'];
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
            'duration' => '0.10'
        ];
    }

    public function removeBreakRow($index)
    {
        unset($this->newBreaks[$index]);
        $this->newBreaks = array_values($this->newBreaks);
    }


    public function getDefaultBreaks()
    {

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

        $this->totalBreakMinutes = 0;
        $paidCount = 0;
        $unpaidCount = 0;
        $paidMinutes = 0;
        $unpaidMinutes = 0;

        foreach ($this->newBreaks as $break) {
            if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {

                $duration = (float) $break['duration'];
                $hours = floor($duration);
                $minutes = ($duration - $hours) * 100;
                $breakMinutes = $hours * 60 + $minutes;

                if (strtolower($break['type']) == 'paid') {
                    $this->totalBreakMinutes += $breakMinutes; // now safe
                    $paidCount++;
                    $paidMinutes += $breakMinutes;
                } elseif (strtolower($break['type']) == 'unpaid') {
                    $unpaidCount++;
                    $unpaidMinutes += $breakMinutes;
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

        $this->paidBreaksCount = $paidCount;
        $this->unpaidBreaksCount = $unpaidCount;
        $this->paidBreaksDuration = sprintf('%02d:%02d', floor($paidMinutes / 60), $paidMinutes % 60);
        $this->unpaidBreaksDuration = sprintf('%02d:%02d', floor($unpaidMinutes / 60), $unpaidMinutes % 60);


        if ($this->originalShiftTotalTime) {
            [$shiftHours, $shiftMinutes] = explode(':', $this->originalShiftTotalTime);
            $shiftTotalMinutes = ($shiftHours * 60) + $shiftMinutes;


            $shiftTotalMinutes -= $paidMinutes;
            $shiftTotalMinutes = max(0, $shiftTotalMinutes);

            $newHours = floor($shiftTotalMinutes / 60);
            $newMinutes = $shiftTotalMinutes % 60;

            $this->newShift['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
        }

        $this->dispatch('closemodal');
    }


    public function getNextOccurrence($currentDate)
    {

        $date = \Carbon\Carbon::parse($currentDate);

        if ($this->frequency === 'Daily') {
            $date->addDays($this->every);
        } elseif ($this->frequency === 'Weekly') {
            $targetDay = ucfirst(strtolower(trim($this->repeatOn ?? $date->format('l'))));
            $date->next($targetDay)->addWeeks($this->every - 1);
        } elseif ($this->frequency === 'Monthly') {
            $date->addMonths($this->every);

            if (preg_match('/^\d{1,2}(st|nd|rd|th)$/', $this->repeatOn)) {

                $day = intval($this->repeatOn);
                $date->day(min($day, $date->daysInMonth));
            } elseif (in_array($this->repeatOn, ['End Of Month', 'First Of Month', 'Middle Of Month'])) {
                // Special month days
                if ($this->repeatOn === 'End Of Month') {
                    $date->day($date->daysInMonth);
                } elseif ($this->repeatOn === 'First Of Month') {
                    $date->day(1);
                } elseif ($this->repeatOn === 'Middle Of Month') {
                    $date->day(intval($date->daysInMonth / 2));
                }
            } else {

                $parts = array_values(array_filter(explode(' ', trim($this->repeatOn))));
                $weekMap = ['First' => 1, 'Second' => 2, 'Third' => 3, 'Fourth' => 4, 'Last' => -1];
                $weekNumber = $weekMap[$parts[0]] ?? 1;
                $weekday = strtolower($parts[1] ?? 'monday');

                if ($weekNumber === -1) {

                    $date->day($date->daysInMonth);
                    while (strtolower($date->format('l')) !== $weekday) {
                        $date->subDay();
                    }
                } else {

                    $date->day(1);
                    $currentWeek = 0;
                    while (true) {
                        if (strtolower($date->format('l')) === $weekday) {
                            $currentWeek++;
                            if ($currentWeek === $weekNumber) break;
                        }
                        $date->addDay();
                    }
                }
            }
        }


        return $date->format('Y-m-d');
    }



    public function deleteTemplate($id)
    {

        $template = ShiftTemplates::find($id);

        if ($template) {
            $template->delete();


            $this->templates = ShiftTemplates::where('company_id', $this->company_id)
                ->orderBy('created_at', 'desc')
                ->get();


            $this->toast('Template deleted successfully.', 'success');
        } else {

            $this->toast('Template not found..', 'success');
        }
    }



    public function render()
    {
        return view('livewire.backend.company.schedule.schedule-index', [
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,
            'employees' => $this->employees,
            'displayDateRange' => $this->displayDateRange,
            'availableMultipleShiftEmployees' => $this->availableMultipleShiftEmployees,
        ]);
    }
}
