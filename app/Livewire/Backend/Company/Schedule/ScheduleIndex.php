<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\BreakofShift;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftBreak;
use App\Models\ShiftDate;
use App\Models\ShiftTemplates;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    public $totalMultiBreakMinutes = [];
    public $originalShiftTotalTime = 0;
    public $originalMultiShiftTotalTime = [];

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
    public $isEditableShift = false;


    public $multipleShifts = [];

    public $showMultipleShiftAddUserPanel = [];

    public $showBreaks = [];
    public $multipleShiftNewBreaks = [];
    public $multipleShiftBreaks = [];
    public $showMultipleShiftAddBreakForm = [];
    public $isShowMultiBreak = [];

    public $calendarShifts = [];

    public $skipConflictCheck = false;
    public $conflictData      = [];
    public $dragSource = [];


    public $newShift = [
        'title' => '',
        'job' => '',
        'start_time' => '09:00',
        'end_time' => '17:00',
        'total_hours' => '08:00',
        'color' => '#000000',
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

        $this->multipleShiftNewBreaks[$index] = [
            [
                'name' => '',
                'type' => 'Paid',
                'duration' => '0.10',
            ]
        ];

        $this->originalMultiShiftTotalTime[$index] = '08:00';
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
        $this->multipleShifts = [];
        $this->multipleShiftNewBreaks = [];
        $this->isShowMultiBreak = [];
        $this->originalMultiShiftTotalTime = [];
        $this->skipConflictCheck = false;

        $this->addShiftRow();
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
                'color'       => $template->color ?? '#000000',
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
            $this->everyOptions = range(1, 20);
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
        $this->isEditableShift = false;
        $this->selectedDate = $date;
        $this->newShift['employees'][] = (int) $employeeId;
        $this->selectedEmployees[] = $employeeId;

        $this->isClickMultipleShift =  false;
        $this->skipConflictCheck = false;

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



    public function getConflicts($date, array $empIds)
    {
        return Shift::whereHas('dates', fn($q) => $q->where('date', $date))
            ->whereHas('dates.employees', fn($q) => $q->whereIn('employees.id', $empIds))
            ->with('dates.employees')
            ->get();
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



        $employeesOnLeave = [];
        foreach ($this->newShift['employees'] as $emp) {
            $empId   = is_array($emp) ? $emp['id'] : $emp;
            $empName = is_array($emp) ? $emp['full_name'] : optional(Employee::find($empId))->full_name;

            if (hasLeave($empId, $this->selectedDate)) {
                $employeesOnLeave[] = $empName;
            }
        }

        if ($employeesOnLeave) {
            $display = implode(', ', array_slice($employeesOnLeave, 0, 3));
            if (count($employeesOnLeave) > 3) $display .= ' ...';


            $this->toast("Employees on leave: $display", 'error');
            return;
        }




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


        if (!$this->isSavedRepeatShift) {

            if (!$this->skipConflictCheck) {
                $conflicts = $this->getConflicts(
                    $this->selectedDate,
                    $this->newShift['employees']
                );



                if ($conflicts->isNotEmpty()) {
                    $this->conflictData = $conflicts;

                    $this->dispatch('show-conflict-modal');
                    return;
                }
            }


            $employeesAlreadyAssigned = DB::table('shift_employees')
                ->join('shift_dates', 'shift_dates.id', '=', 'shift_employees.shift_date_id')
                ->where('shift_dates.date', $this->selectedDate)
                ->whereIn('shift_employees.employee_id', $this->newShift['employees'])
                ->pluck('shift_employees.employee_id')
                ->toArray();

            if ($employeesAlreadyAssigned) {
                $names = Employee::whereIn('id', $employeesAlreadyAssigned)
                    ->pluck('f_name')
                    ->implode(', ');
                $this->toast("Employees already assigned on {$this->selectedDate}: {$names}", 'error');
                return;
            }





            $shift = Shift::create([
                'company_id' => $this->company_id,
                'title' => $this->newShift['title'],
                'job' => $this->newShift['job'],
                'color' => $this->newShift['color'],
                'address' => $this->newShift['address'],
                'note' => $this->newShift['note'],
            ]);



            $shiftDate = $shift->dates()->create([
                'date' => $currentDate,
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
        } else {

            foreach ($dates as $date) {

                if (!$this->skipConflictCheck) {
                    $conflicts = $this->getConflicts(
                        $date,
                        $this->newShift['employees']
                    );


                    if ($conflicts->isNotEmpty()) {
                        $this->conflictData = $conflicts;
                        $this->dispatch('show-conflict-modal');
                        return;
                    }
                }
            }

            foreach ($dates as $date) {
                $already = DB::table('shift_employees')
                    ->join('shift_dates', 'shift_dates.id', '=', 'shift_employees.shift_date_id')
                    ->where('shift_dates.date', $date)
                    ->whereIn('shift_employees.employee_id', $this->newShift['employees'])
                    ->pluck('shift_employees.employee_id')
                    ->toArray();

                if ($already) {
                    $names = Employee::whereIn('id', $already)->pluck('f_name')->implode(', ');
                    $this->toast("Employees already assigned on {$date}: {$names}", 'error');
                    return;
                }
            }






            $shift = Shift::create([
                'company_id' => $this->company_id,
                'title' => $this->newShift['title'],
                'job' => $this->newShift['job'],
                'color' => $this->newShift['color'],
                'address' => $this->newShift['address'],
                'note' => $this->newShift['note'],
            ]);



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
        }


        $this->isEditableShift =  false;
        $this->closeAddShiftPanel();
        $this->cancelRepeatShift();
        $this->loadShifts();
        $this->dispatch('refreshSchedule');
        $this->toast('Shift has been published successfully!', 'success');
    }





    public function confirmOverwrite()
    {
        $empIdsToReplace = $this->isClickMultipleShift
            ? collect($this->multipleShifts)->pluck('employees')->flatten(1)->pluck('id')->unique()->toArray()
            : $this->newShift['employees'];

        foreach ($this->conflictData as $shift) {
            foreach ($shift->dates as $date) {
                if (Carbon::parse($date->date)->format('Y-m-d') !== $this->selectedDate) {
                    continue;
                }


                $date->employees()->detach($empIdsToReplace);


                if ($date->employees()->doesntExist()) {
                    $date->breaks()->delete();
                    $date->delete();
                }
            }
        }

        $this->skipConflictCheck = true;

        if ($this->isClickMultipleShift) {
            $this->publishMultipleShifts();
        } else {
            $this->publishShift();
        }

        /* 3. Clean-up */
        $this->skipConflictCheck = false;
        $this->dispatch('hide-conflict-modal');
    }




    public function publishMultipleShifts()
    {

        $rules = [];
        $messages = [];

        foreach ($this->multipleShifts as $index => $shift) {
            $rules["multipleShifts.$index.date"] = 'required|date';
            $rules["multipleShifts.$index.title"] = 'required|string';
            $rules["multipleShifts.$index.job"] = 'required|string|max:100';
            $rules["multipleShifts.$index.start_time"] = 'required|date_format:H:i';
            $rules["multipleShifts.$index.end_time"] = 'required|date_format:H:i';
            $rules["multipleShifts.$index.total_hours"] = 'required';
            $rules["multipleShifts.$index.employees"] = 'required|array|min:1';

            $rules["multipleShifts.$index.address"] = 'nullable|string|max:255';
            $rules["multipleShifts.$index.note"] = 'nullable|string|max:500';

            $messages["multipleShifts.$index.date.required"] = 'Shift date is required.';
            $messages["multipleShifts.$index.title.required"] = 'Shift title is required.';
            $messages["multipleShifts.$index.job.required"] = 'Job is required.';
            $messages["multipleShifts.$index.start_time.required"] = 'Start time is required.';
            $messages["multipleShifts.$index.end_time.required"] = 'End time is required.';
            $messages["multipleShifts.$index.employees.required"] = 'Select at least one employee.';
        }


        foreach ($this->multipleShifts as $idx => $row) {
            if (empty($row['date'])) continue;          // skip empty rows

            $onLeaveNames = [];
            foreach ($row['employees'] as $emp) {
                $empId = is_array($emp) ? $emp['id'] : $emp;
                if (hasLeave($empId, $row['date'])) {
                    $onLeaveNames[] = is_array($emp) ? $emp['full_name'] : optional(Employee::find($empId))->full_name;
                }
            }

            if ($onLeaveNames) {
                $display = implode(', ', array_slice($onLeaveNames, 0, 3));
                if (count($onLeaveNames) > 3) $display .= ' ...';



                $this->toast("Row " . ($idx + 1) . ": employees on leave – $display", 'error');

                return;
            }
        }




        $dateEmployeeMap = [];

        foreach ($this->multipleShifts as $shift) {
            $date = $shift['date'];
            foreach ($shift['employees'] as $employee) {
                $employeeId = is_array($employee) ? $employee['id'] : $employee;

                if (isset($dateEmployeeMap[$date][$employeeId])) {

                    $this->toast("Employee '{$employee['full_name']}' is assigned multiple times on $date", 'info');
                    return;
                }
                $dateEmployeeMap[$date][$employeeId] = true;
            }
        }



        $this->validate($rules, $messages);


        foreach ($this->multipleShifts as $index => $shift) {
            $employeeIds = collect($shift['employees'])->pluck('id')->toArray();


            if (!$this->skipConflictCheck) {
                $conflicts = $this->getConflicts(
                    $shift['date'],
                    $employeeIds
                );

                if ($conflicts->isNotEmpty()) {
                    $this->conflictData = $conflicts;
                    $this->dispatch('show-conflict-modal');
                    return;
                }
            }


            $alreadyAssigned = DB::table('shift_employees')
                ->join('shift_dates', 'shift_dates.id', '=', 'shift_employees.shift_date_id')
                ->where('shift_dates.date', $shift['date'])
                ->whereIn('shift_employees.employee_id', $employeeIds)
                ->pluck('shift_employees.employee_id')
                ->toArray();

            if ($alreadyAssigned) {
                $names = Employee::whereIn('id', $alreadyAssigned)
                    ->pluck('f_name')
                    ->implode(', ');
                $this->toast("Employees already assigned on {$shift['date']}: {$names}", 'error');
                return;
            }




            $shiftModel = Shift::create([
                'company_id' => $this->company_id,
                'title' => $shift['title'],
                'job' => $shift['job'],
                'color' => $shift['color'],
                'address' => $shift['address'],
                'note' => $shift['note'],
            ]);

            // create date row
            $shiftDate = $shiftModel->dates()->create([
                'date' => $shift['date'],
                'start_time' => $shift['start_time'],
                'end_time' => $shift['end_time'],
                'total_hours' => $shift['total_hours'],
            ]);

            $employeeIds = collect($shift['employees'])->pluck('id')->toArray();
            $shiftDate->employees()->attach($employeeIds);


            foreach ($this->multipleShiftNewBreaks[$index] ?? [] as $break) {
                if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {
                    $shiftDate->breaks()->create([
                        'title' => $break['name'],
                        'type' => $break['type'],
                        'duration' => $break['duration'],
                    ]);
                }
            }
        }



        $this->multipleShifts = [];
        $this->multipleShiftNewBreaks = [];
        $this->originalMultiShiftTotalTime = [];

        $this->loadShifts();

        $this->dispatch('closemodal');
        $this->toast('Multiple shifts published successfully!', 'success');
    }




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
        $this->loadShifts();
        $this->calcCalendarSummary();
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
                'date' => $current->format('d/m'),
                'highlight' => $current->equalTo($this->currentDate->copy()->startOfDay()),
            ];
            $current->addDay();
            $count++;
        }

        return $days;
    }



    public function updatedViewMode($value)
    {
        $this->loadShifts();
    }



    public function setViewMode($mode)
    {
        $this->viewMode = $mode;

        if ($mode === 'weekly') {

            $this->startDate = now()->startOfDay();
            $this->endDate = now()->addDays(6)->endOfDay();
        } elseif ($mode === 'monthly') {
            $this->startDate = now()->startOfMonth();
            $this->endDate = now()->endOfMonth();
        }


        $this->loadShifts();
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



    public function toggleMultiAllDayForShift($shiftIndex, $value)
    {
        // Update the all_day flag
        $this->multipleShifts[$shiftIndex]['all_day'] = $value;

        if ($value) {
            // Set default times for All Day
            $this->multipleShifts[$shiftIndex]['start_time'] = '09:00';
            $this->multipleShifts[$shiftIndex]['end_time'] = '17:00';
            $this->multipleShifts[$shiftIndex]['total_hours'] = '08:00';

            // Store original total hours for this shift
            $this->originalMultiShiftTotalTime[$shiftIndex] = $this->multipleShifts[$shiftIndex]['total_hours'];

            // Convert total_hours to minutes
            [$hours, $minutes] = explode(':', $this->multipleShifts[$shiftIndex]['total_hours']);
            $totalMinutes = ($hours * 60) + $minutes;

            // Subtract valid paid breaks only
            $totalBreakMinutes = $this->calculateTotalBreakMinutes($shiftIndex);

            if ($totalBreakMinutes > 0) {
                $totalMinutes -= $totalBreakMinutes;
                $totalMinutes = max(0, $totalMinutes);
            }

            // Convert back to HH:MM format
            $newHours = floor($totalMinutes / 60);
            $newMinutes = $totalMinutes % 60;

            $this->multipleShifts[$shiftIndex]['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
        }
    }



    private function calculateTotalBreakMinutes($shiftIndex): int
    {
        $totalMinutes = 0;

        foreach ($this->multipleShiftNewBreaks[$shiftIndex] ?? [] as $break) {
            if (
                !empty($break['name']) && !empty($break['type']) && !empty($break['duration'])
                && strtolower($break['type']) === 'paid'
            ) {

                $duration = (float) $break['duration'];
                $hours = floor($duration);
                $minutes = ($duration - $hours) * 100;
                $totalMinutes += ($hours * 60 + $minutes);
            }
        }

        return $totalMinutes;
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




    public function calculateMultiTotalHours($shiftIndex)
    {
        $shift = $this->multipleShifts[$shiftIndex] ?? null;
        if (!$shift) return;

        if (!empty($shift['start_time']) && !empty($shift['end_time'])) {
            $start = \Carbon\Carbon::createFromFormat('H:i', $shift['start_time']);
            $end = \Carbon\Carbon::createFromFormat('H:i', $shift['end_time']);


            if ($end->lt($start)) {
                $end->addDay();
            }

            $diff = $end->diffInMinutes($start, false);
            $diff = abs($diff);

            $hours = floor($diff / 60);
            $minutes = $diff % 60;

            $this->multipleShifts[$shiftIndex]['total_hours'] = sprintf('%02d:%02d', $hours, $minutes);


            $this->originalMultiShiftTotalTime[$shiftIndex] = $this->multipleShifts[$shiftIndex]['total_hours'];
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

    public function showBreaksSection($shiftIndex)
    {

        $this->isShowMultiBreak[$shiftIndex] = true;
    }



    public function addMultipleBreakRow($shiftIndex)
    {
        $this->multipleShiftNewBreaks[$shiftIndex][] = [
            'name' => '',
            'type' => 'Paid',
            'duration' => '0.10',
        ];

        $this->showBreaks[$shiftIndex] = true;
    }

    public function removeMultipleBreakRow($shiftIndex, $breakIndex)
    {
        unset($this->multipleShiftNewBreaks[$shiftIndex][$breakIndex]);
        $this->multipleShiftNewBreaks[$shiftIndex] = array_values($this->multipleShiftNewBreaks[$shiftIndex]);
    }

    public function confirmMultipleBreaksAndSave($shiftIndex)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $breaks = $this->multipleShiftNewBreaks[$shiftIndex] ?? [];

        // Validate each break
        foreach ($breaks as $key => $break) {
            if (empty($break['name'])) {
                $this->addError("multipleShiftNewBreaks.$shiftIndex.$key.name", "Break name is required.");
            }
            if (empty($break['type'])) {
                $this->addError("multipleShiftNewBreaks.$shiftIndex.$key.type", "Break type is required.");
            }
            if (empty($break['duration'])) {
                $this->addError("multipleShiftNewBreaks.$shiftIndex.$key.duration", "Break duration is required.");
            }
        }

        // Stop if there are errors
        if ($this->getErrorBag()->isNotEmpty()) {
            return;
        }


        $totalBreakMinutes = 0;
        $paidMinutes = 0;
        $unpaidMinutes = 0;

        foreach ($this->multipleShiftNewBreaks[$shiftIndex] ?? [] as $break) {
            if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {

                $duration = (float) $break['duration'];
                $hours = floor($duration);
                $minutes = ($duration - $hours) * 100;
                $breakMinutes = $hours * 60 + $minutes;

                if (strtolower($break['type']) === 'paid') {
                    $totalBreakMinutes += $breakMinutes;
                    $paidMinutes += $breakMinutes;
                } else {
                    $unpaidMinutes += $breakMinutes;
                }
            }
        }

        $this->multipleShifts[$shiftIndex]['breaks'] = $this->multipleShiftNewBreaks[$shiftIndex] ?? [];


        $this->showBreaks[$shiftIndex] = false;

        $this->multipleShifts[$shiftIndex]['breaks'] = $this->multipleShiftNewBreaks[$shiftIndex] ?? [];
        $this->showBreaks[$shiftIndex] = false;


        $originalHours = $this->originalMultiShiftTotalTime[$shiftIndex] ?? $this->multipleShifts[$shiftIndex]['total_hours'];

        [$hours, $minutes] = explode(':', $originalHours);
        $totalMinutes = ($hours * 60) + $minutes;


        $totalBreakMinutes = $this->calculateTotalBreakMinutes($shiftIndex);

        if ($totalBreakMinutes > 0) {
            $totalMinutes -= $totalBreakMinutes;
            $totalMinutes = max(0, $totalMinutes);
        }

        $newHours = floor($totalMinutes / 60);
        $newMinutes = $totalMinutes % 60;

        $this->multipleShifts[$shiftIndex]['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
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



    public function hideBreaksSection($index)
    {
        $this->isShowMultiBreak[$index] = false;
    }



    public function deleteShiftForAllEmp($dateId)
    {

        $shiftDate = ShiftDate::findOrFail($dateId);
        $shiftId   = $shiftDate->shift_id;


        $shiftDate->employees()->detach();


        $stillHasEmployees = $shiftDate->employees()->exists();


        if (! $stillHasEmployees) {
            $shiftDate->breaks()->delete();
            $shiftDate->delete();
        }



        $remaining = ShiftDate::where('shift_id', $shiftId)->exists();


        if (! $remaining) {
            Shift::where('id', $shiftId)->delete();
            BreakofShift::where('shift_date_id', $dateId)->delete();
        }


        $this->loadShifts();
        $this->toast('Shift deleted successfully!', 'success');
    }





    public function editOneEmpShift($dateId, $empId)
    {
        $this->isClickMultipleShift =  false;
        $this->isEditableShift =  true;
        $this->resetFields();



        $shiftDate = ShiftDate::findOrFail($dateId);

        $this->selectedDate = Carbon::parse($shiftDate->date)->format('Y-m-d');


        $this->newShift = [
            'title'       => $shiftDate->shift->title       ?? '',
            'job'         => $shiftDate->shift->job         ?? '',
            'start_time' => Carbon::parse($shiftDate->start_time)->format('H:i'),
            'end_time'   => Carbon::parse($shiftDate->end_time)->format('H:i'),
            'total_hours' => $shiftDate->total_hours,
            'color'       => $shiftDate->shift->color       ?? '#000000',
            'address'     => $shiftDate->shift->address     ?? '',
            'note'        => $shiftDate->shift->note        ?? '',
            'all_day'     => $shiftDate->shift->all_day     ?? false,
            'employees'   => [(int) $empId],
        ];


        if ($shiftDate->breaks->isNotEmpty()) {
            $this->newBreaks = $shiftDate->breaks
                ->map(fn($b) => [
                    'name'     => $b->title,
                    'type'     => $b->type,
                    'duration' => (float) $b->duration,
                ])
                ->toArray();

            $this->recalculateBreakSummary();
        } else {
            $this->newBreaks = [];
            $this->paidBreaksCount = 0;
            $this->unpaidBreaksCount = 0;
            $this->paidBreaksDuration = '00:00';
            $this->unpaidBreaksDuration = '00:00';
        }



        $this->dispatch('shift-panel-opened');
    }



    private function recalculateBreakSummary()
    {
        $paid = collect($this->newBreaks)->where('type', 'Paid');
        $unpaid = collect($this->newBreaks)->where('type', 'Unpaid');

        $this->paidBreaksCount   = $paid->count();
        $this->unpaidBreaksCount = $unpaid->count();

        $this->paidBreaksDuration   = $this->hoursToTime($paid->sum('duration'));
        $this->unpaidBreaksDuration = $this->hoursToTime($unpaid->sum('duration'));
    }

    private function hoursToTime($hours)
    {
        $h = floor($hours);
        $m = round(($hours - $h) * 60);
        return sprintf('%02d:%02d', $h, $m);
    }





    public function deleteShiftOneEmp($dateId, $empId)
    {
        $shiftDate = ShiftDate::findOrFail($dateId);
        $shiftId   = $shiftDate->shift_id;

        $shiftDate->employees()->detach($empId);


        $stillHasEmployees = $shiftDate->employees()->exists();


        if (! $stillHasEmployees) {
            $shiftDate->breaks()->delete();
            $shiftDate->delete();
        }



        $remaining = ShiftDate::where('shift_id', $shiftId)->exists();

        if (! $remaining) {
            Shift::where('id', $shiftId)->delete();
        }


        $this->loadShifts();
        $this->toast('Shift deleted successfully!', 'success');
    }




    public function loadShifts()
    {
        $this->calendarShifts = ShiftDate::whereHas('shift', function ($q) {
            $q->where('company_id', $this->company_id);
        })
            ->with([
                'shift:id,title,color,address,note',
                'employees:id,f_name,l_name',
                'breaks:id,shift_date_id,title,type,duration'
            ])
            ->get()
            ->map(function ($shiftDate) {
                return [
                    'id' => $shiftDate->id,
                    'date' => $shiftDate->date,
                    'start_time' => $shiftDate->start_time,
                    'end_time' => $shiftDate->end_time,
                    'total_hours'  => $shiftDate->total_hours,
                    'shift' => [
                        'title' => $shiftDate->shift->title ?? null,
                        'color' => $shiftDate->shift->color ?? '#6c757d',
                        'address' => $shiftDate->shift->address ?? null,
                        'note' => $shiftDate->shift->note ?? null,
                    ],
                    'employees' => $shiftDate->employees->map(function ($emp) {
                        return [
                            'id' => $emp->id,
                            'name' => $emp->f_name . ' ' . $emp->l_name,
                        ];
                    })->toArray(),
                    'breaks' => $shiftDate->breaks->map(function ($b) {
                        return [
                            'title' => $b->title,
                            'type' => $b->type,
                            'duration' => $b->duration,
                        ];
                    })->toArray(),
                ];
            })
            ->groupBy('date')
            ->toArray();
    }




    public function getCellContent($employeeId, $date)
    {

        if (empty($this->calendarShifts[$date])) {
            return null;
        }

        foreach ($this->calendarShifts[$date] as $shiftDate) {
            foreach ($shiftDate['employees'] as $employee) {
                if ($employee['id'] == $employeeId) {
                    return [
                        'type'  => 'Shift',
                        'id'    => $shiftDate['id'],
                        'title' => $shiftDate['shift']['title'],
                        'color' => $shiftDate['shift']['color'],
                        'time'  =>
                        Carbon::parse($shiftDate['start_time'])->format('g:i A')
                            . ' - ' .
                            Carbon::parse($shiftDate['end_time'])->format('g:i A'),

                        'employees' => $shiftDate['employees'] ?? [],
                        'shift' => [
                            'address' => $shiftDate['shift']['address'] ?? '-',
                            'note'    => $shiftDate['shift']['note'] ?? '-',
                        ],
                        'breaks'    => $shiftDate['breaks'] ?? [],
                    ];
                }
            }
        }

        return null;
    }



    public function dateChanged()
    {
        $this->shiftEmployees = $this->shiftEmployees;
    }


    public function updatedMultipleShifts($value, $nested)
    {

        if (str($nested)->afterLast('.')->value() === 'date') {
            $index = (int) str($nested)->before('.')->value();


            $this->multipleShifts[$index] = $this->multipleShifts[$index];


            $this->reset("multipleShiftEmployeeSearch.$index");
        }
    }




    private function calcCalendarSummary(): array
    {
        $totalMinutes = 0;
        $shiftCount   = 0;
        $userIds      = [];

        $period = Carbon::parse($this->startDate)->daysUntil($this->endDate);

        foreach ($period as $day) {
            $dateKey = $day->format('Y-m-d');
            if (empty($this->calendarShifts[$dateKey])) {
                continue;
            }

            foreach ($this->calendarShifts[$dateKey] as $row) {
                $shiftCount++;


                [$h, $m] = explode(':', $row['total_hours'] ?? '00:00');
                $totalMinutes += ((int)$h * 60) + (int)$m;


                foreach ($row['employees'] ?? [] as $emp) {
                    $userIds[$emp['id'] ?? $emp] = true;
                }
            }
        }

        $hours = sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60);

        return [
            'shifts' => $shiftCount,
            'hours'  => $hours,
            'users'  => count($userIds),
        ];
    }




    public function handleDrag(string $fromDate, int $fromEmpId, int $shiftDateId)
    {
        $this->dragSource = [
            'date' => $fromDate,
            'empId' => $fromEmpId,
            'shiftDateId' => $shiftDateId,
        ];
    }

    public function handleDrop(string $toDate, int $toEmpId)
    {
        if (empty($this->dragSource)) return;



        [$fromDate, $fromEmpId, $shiftDateId] = [
            $this->dragSource['date'],
            $this->dragSource['empId'],
            $this->dragSource['shiftDateId'],
        ];

        // same cell – do nothing
        if ($fromDate === $toDate && $fromEmpId === $toEmpId) {
            $this->dragSource = [];
            return;
        }

        $srcDate = ShiftDate::with('breaks')->find($shiftDateId);
        if (!$srcDate) return;


        $srcDate->employees()->detach($fromEmpId);


        if ($srcDate->employees()->doesntExist()) {
            $srcDate->breaks()->delete();
            $srcDate->delete();
        }


        $tgtDate = ShiftDate::firstOrCreate([
            'date'        => $toDate,
            'shift_id'    => $srcDate->shift_id,
            'start_time'  => $srcDate->start_time,
            'end_time'    => $srcDate->end_time,
            'total_hours' => $srcDate->total_hours,
        ]);


        foreach ($srcDate->breaks as $break) {
            $tgtDate->breaks()->create([
                'title'    => $break->title,
                'type'     => $break->type,
                'duration' => $break->duration,
            ]);
        }


        $tgtDate->employees()->attach($toEmpId);

        $this->loadShifts();
        $this->dragSource = [];
        $this->toast('Shift moved successfully!', 'success');
    }


    public function render()
    {
        $this->loadShifts();


        return view('livewire.backend.company.schedule.schedule-index', [
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,
            'employees' => $this->employees,
            'displayDateRange' => $this->displayDateRange,
            'availableMultipleShiftEmployees' => $this->availableMultipleShiftEmployees,

            'conflictData' => $this->conflictData ?? collect(),
        ]);
    }
}
