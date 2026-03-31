<?php

namespace App\Livewire\Backend\Company\Schedule;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\BreakofShift;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\ShiftBreak;
use App\Models\ShiftDate;
use App\Models\ShiftTemplates;
use App\Models\WeeklyTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleIndex extends BaseComponent
{

    public $perPageEmployees = 10;

    public $hasMoreEmployees = true;
    public $perPage = 4;
    public $loaded = 0;
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
    public $endRepeatDate;

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

    public $editingShiftDateId = null;
    public $editingEmployeeId = null;

    public $selectedDates = [];
    public $selectedDateDisplay = '';
    public $hasMultipleDates = false;


    public $weeklyTemplates = [];
    public $showSaveWeekTemplateModal = false;
    public $showLoadWeekTemplateModal = false;
    public $newTemplateName = '';
    public $newTemplateDescription = '';
    public $selectedTemplateId = null;


    public $isLoading = true;
    public $loadedShifts = [];
    public $loadedEmployees = [];


    public function saveWeekAsTemplate()
    {
        $this->validate([
            'newTemplateName' => 'required|string|max:255',
        ]);


        $weekData = [];
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        foreach ($this->employees as $employee) {
            $employeeData = [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'shifts' => []
            ];

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateKey = $date->format('Y-m-d');
                $shiftContent = $this->getCellContent($employee->id, $dateKey);

                if ($shiftContent && $shiftContent['type'] === 'Shift') {
                    $timeRange = $shiftContent['time'] ?? null;

                    $startTime = null;
                    $endTime = null;

                    if ($timeRange) {
                        [$start, $end] = explode(' - ', $timeRange);

                        $startTime = Carbon::createFromFormat('g:i A', trim($start))->format('H:i');
                        $endTime   = Carbon::createFromFormat('g:i A', trim($end))->format('H:i');
                    }


                    $employeeData['shifts'][] = [
                        'date' => $dateKey,
                        'day_of_week' => $date->format('l'),
                        'shift_id' => $shiftContent['id'],
                        'title' => $shiftContent['title'],
                        'start_time' => $startTime,
                        'end_time'   => $endTime,
                        'total_hours'   => $shiftContent['total_hours'] ?? null,
                        'color' => $shiftContent['color'],
                        'address' => $shiftContent['shift']['address'] ?? null,
                        'note' => $shiftContent['shift']['note'] ?? null,
                        'breaks' => $shiftContent['breaks'] ?? [],
                    ];
                }
            }

            if (!empty($employeeData['shifts'])) {
                $weekData['employees'][] = $employeeData;
            }
        }

        $weekData['week_days'] = $this->weekDays;
        $weekData['start_date'] = $startDate->format('Y-m-d');
        $weekData['end_date'] = $endDate->format('Y-m-d');

        WeeklyTemplate::create([
            'company_id' => $this->company_id,
            'name' => $this->newTemplateName,
            'description' => $this->newTemplateDescription,
            'template_data' => $weekData,
        ]);

        $this->toast('Weekly template saved successfully!', 'success');
        $this->showSaveWeekTemplateModal = false;
        $this->newTemplateName = '';
        $this->dispatch('closemodal');
        $this->newTemplateDescription = '';
        $this->loadWeeklyTemplates();
    }




    public function hasCurrentWeekShifts()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $shiftCount = ShiftDate::whereBetween('date', [$startDate, $endDate])
            ->whereHas('shift', function ($q) {
                $q->where('company_id', $this->company_id);
            })
            ->count();

        return $shiftCount > 0;
    }


    public function getCurrentWeekShiftCount()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        return ShiftDate::whereBetween('date', [$startDate, $endDate])
            ->whereHas('shift', function ($q) {
                $q->where('company_id', $this->company_id);
            })
            ->count();
    }


    public function confirmApplyWeeklyTemplate($templateId)
    {
        $template = WeeklyTemplate::findOrFail($templateId);
        $shiftCount = $this->getCurrentWeekShiftCount();

        if ($shiftCount > 0) {
            $this->dispatch('show-confirm-template-modal', [
                'templateId' => $templateId,
                'templateName' => $template->name,
                'shiftCount' => $shiftCount,
                'dateRange' => $this->displayDateRange
            ]);
        } else {
            $this->applyWeeklyTemplate($templateId);
        }
    }




    public function loadWeeklyTemplates()
    {
        $this->weeklyTemplates = WeeklyTemplate::where('company_id', $this->company_id)
            ->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $this->loaded = count($this->weeklyTemplates);
    }


    public function applyWeeklyTemplate($templateId)
    {
        $template = WeeklyTemplate::findOrFail($templateId);
        $weekData = $template->template_data;


        $this->clearCurrentWeekShifts();


        foreach ($weekData['employees'] ?? [] as $employeeData) {
            foreach ($employeeData['shifts'] ?? [] as $shiftData) {

                $targetDate = $this->getTargetDateForDay($shiftData['day_of_week']);

                if ($targetDate) {
                    $this->createShiftFromTemplate($employeeData['id'], $targetDate, $shiftData);
                }
            }
        }

        $this->loadShifts();
        $this->showLoadWeekTemplateModal = false;
        $this->toast('Weekly template applied successfully!', 'success');
    }


    private function clearCurrentWeekShifts()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        $shiftDates = ShiftDate::whereBetween('date', [$startDate, $endDate])
            ->whereHas('shift', function ($q) {
                $q->where('company_id', $this->company_id);
            })
            ->get();

        foreach ($shiftDates as $shiftDate) {
            $shiftDate->employees()->detach();
            $shiftDate->breaks()->delete();
            $shiftDate->delete();
        }


        Shift::whereDoesntHave('dates')->delete();
    }


    private function getTargetDateForDay($dayOfWeek)
    {
        $startDate = Carbon::parse($this->startDate);
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $targetDayIndex = array_search($dayOfWeek, $days);
        $currentDayIndex = $startDate->dayOfWeekIso - 1;

        $diff = $targetDayIndex - $currentDayIndex;
        return $startDate->copy()->addDays($diff)->format('Y-m-d');
    }


    private function createShiftFromTemplate($employeeId, $date, $shiftData)
    {

        $shift = Shift::firstOrCreate([
            'company_id' => $this->company_id,
            'title' => $shiftData['title'],
            'job' => $shiftData['job'] ?? '',
            'color' => $shiftData['color'],
            'address' => $shiftData['address'] ?? '',
            'note' => $shiftData['note'] ?? '',
        ]);


        $shiftDate = $shift->dates()->create([
            'date' => $date,
            'start_time' => $shiftData['start_time'] ?? '09:00',
            'end_time' => $shiftData['end_time'] ?? '17:00',
            'total_hours' => $shiftData['total_hours'] ?? '08:00',
        ]);


        $shiftDate->employees()->attach($employeeId);

        foreach ($shiftData['breaks'] ?? [] as $break) {
            $shiftDate->breaks()->create([
                'title' => $break['title'],
                'type' => $break['type'],
                'duration' => $break['duration'],
            ]);
        }
    }


    public function deleteWeeklyTemplate($id)
    {
        WeeklyTemplate::findOrFail($id)->delete();
        $this->loadWeeklyTemplates();
        $this->toast('Template deleted successfully!', 'success');
    }



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
            ->take($this->perPage)
            ->get();

        $this->loaded = count($this->templates);
    }


    public function loadMoreWeeklyTemplates()
    {
        $this->perPage += 4;
        $this->loadWeeklyTemplates();
    }



    public function loadMore()
    {
        $this->perPage += 4;
        $this->templates = ShiftTemplates::where('company_id', $this->company_id)
            ->orderBy('created_at', 'desc')
            ->take($this->perPage)
            ->get();

        $this->loaded = count($this->templates);
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
                'start_time'  => Carbon::parse($template->start_time)->format('H:i'),
                'end_time'    => Carbon::parse($template->end_time)->format('H:i'),
                'total_hours' => $template->total_hours ?? $this->calculateTotalHours(),
                'color'       => $template->color ?? '#000000',
                'address'     => $template->address ?? '',
                'note'        => $template->note ?? '',
                'all_day'     => $template->all_day ?? false,
                'employees'   => $template->employees ?? [],
            ];

            if ($template->dates) {
                $dates = json_decode($template->dates, true);
                if (is_array($dates) && count($dates) > 0) {
                    $this->selectedDates = $dates;
                    $this->selectedDateDisplay = implode(', ', $dates);
                    $this->hasMultipleDates = count($dates) > 1;
                    $this->selectedDate = $dates[0];


                    if (!$this->hasMultipleDates) {
                        $this->selectedDate = $dates[0];
                    }
                }
            } elseif ($template->created_at) {

                $this->selectedDates = [$template->created_at->format('Y-m-d')];
                $this->selectedDateDisplay = $template->created_at->format('Y-m-d');
                $this->hasMultipleDates = false;
                $this->selectedDate = $template->created_at->format('Y-m-d');
            }


            if ($template->breaks) {
                $this->newBreaks = json_decode($template->breaks, true);
                if (!empty($this->newBreaks)) {
                    $this->showAddBreakForm = true;
                    $this->confirmBreaksAndSave();
                }
            }

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

        $this->cleanSelectedDates();
        $datesToProcess = !empty($this->selectedDates) ? array_unique($this->selectedDates) : [$this->selectedDate];

        if (empty($datesToProcess)) {
            $this->toast('Please select at least one date for the template', 'error');
            return;
        }

        $this->validate([
            'newShift.title'      => 'required|string|max:255',
            'newShift.job'        => 'required|string|max:255',
            'newShift.start_time' => 'required|date_format:H:i',
            'newShift.end_time'   => 'required|date_format:H:i|after:newShift.start_time',
        ], [
            'newShift.title.required' => 'Shift title is required.',
            'newShift.job.required'   => 'Job title is required.',
            'newShift.start_time.required' => 'Start time is required.',
            'newShift.end_time.required'   => 'End time is required.',
        ]);


        $isMultiDate = count($datesToProcess) > 1;

        $template = ShiftTemplates::create([
            'company_id'  => $this->company_id,
            'title'       => $this->newShift['title'],
            'job'         => $this->newShift['job'],
            'color'       => $this->newShift['color'],
            'address'     => $this->newShift['address'],
            'note'        => $this->newShift['note'],
            'start_time'  => $this->newShift['start_time'],
            'end_time'    => $this->newShift['end_time'],
            'all_day'     => $this->newShift['all_day'] ?? false,
            'total_hours' => $this->newShift['total_hours'],
            'dates'       => json_encode($datesToProcess),
            'is_multi_date' => $isMultiDate,
        ]);

        // Save breaks if any
        if (!empty($this->newBreaks)) {
            $template->update([
                'breaks' => json_encode($this->newBreaks),
            ]);
        }

        $dateCount = count($datesToProcess);
        $this->toast("Template saved successfully for {$dateCount} date(s)!", 'success');
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


    public function updateSelectedDates($date)
    {
        if (empty($date)) {
            return;
        }


        if (!in_array($date, $this->selectedDates)) {
            $this->selectedDates[] = $date;
        }

        foreach (array_reverse($this->selectedDates) as $item) {
            if (is_string($item)) {
                $this->selectedDate = $item;
                break;
            }
        }


        $this->cleanSelectedDates();


        $uniqueDates = array_unique($this->selectedDates);
        $this->hasMultipleDates = count($uniqueDates) > 1;


        if ($this->hasMultipleDates && $this->isSavedRepeatShift) {
            $this->isSavedRepeatShift = false;
            $this->cancelRepeatShift();
            $this->toast('Repetition has been disabled because multiple dates are selected.', 'info');
        }
    }






    private function cleanSelectedDates()
    {
        $cleaned = [];
        foreach ($this->selectedDates as $item) {
            if (is_string($item)) {
                $cleaned[] = $item;
            } elseif (is_array($item)) {

                foreach ($item as $subItem) {
                    if (is_string($subItem)) {
                        $cleaned[] = $subItem;
                    }
                }
            }
        }

        $this->selectedDates = array_unique($cleaned);

        $this->selectedDates = array_values($this->selectedDates);
    }




    public function openAddShiftPanelForMonth($date)
    {


        $this->updateSelectedDates($date);
        $this->resetFields();

        $this->selectedDateDisplay = $this->selectedDates[0] ?? '';

        $this->isClickMultipleShift =  false;

        $this->dispatch('shift-panel-opened');
    }





    public function openAddShiftPanel($date, $employeeId)
    {

        $this->isEditableShift = false;
        $this->updateSelectedDates($date);
        $this->newShift['employees'][] = (int) $employeeId;
        $this->selectedEmployees[] = $employeeId;

        $this->selectedDateDisplay = $this->selectedDates[0] ?? '';

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
        $this->hasMultipleDates = false;
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
        $this->editingShiftDateId = null;
        $this->editingEmployeeId = null;
        $this->selectedDates = [];
        $this->selectedDateDisplay = '';
        $this->hasMultipleDates = false;
        $this->selectedDate = null;

        $this->reset('newShift');
        $this->dispatch('reset-flatpickr');
    }



    public function saveRepeatShift()
    {
        if ($this->hasMultipleDates) {
            $this->toast('Repetition cannot be added when multiple dates are selected. Please select a single date for repeated shifts.', 'error');
            return;
        }


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

        $this->cleanSelectedDates();

        $datesToProcess = !empty($this->selectedDates) ? array_unique($this->selectedDates) : [$this->selectedDate];

        if ($this->isSavedRepeatShift) {

            $baseDate = !empty($this->selectedDates) ? $this->selectedDates[0] : $this->selectedDate;
            $datesToProcess = $this->generateRepeatedDates($baseDate);
        }

        if (empty($datesToProcess)) {
            $this->toast('Please select at least one date for the shift', 'error');
            return;
        }

        $this->validate([
            'selectedDate' => ['required', 'date'],
            'newShift.title' => 'required|string',
            'newShift.start_time' => 'required|date_format:H:i',
            'newShift.end_time'   => 'required|date_format:H:i',
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



        // Check for employees on leave
        $employeesOnLeave = [];
        foreach ($this->newShift['employees'] as $emp) {
            $empId = is_array($emp) ? $emp['id'] : $emp;

            foreach ($datesToProcess as $date) {
                if (hasLeave($empId, $date)) {
                    $employee = Employee::find($empId);
                    $employeesOnLeave[] = $employee->full_name . " ($date)";
                }
            }
        }

        if ($employeesOnLeave) {
            $display = implode(', ', array_slice($employeesOnLeave, 0, 3));
            if (count($employeesOnLeave) > 3) $display .= ' ...';
            $this->toast("Employees on leave: $display", 'error');
            return;
        }


        foreach ($this->newShift['employees'] as $emp) {
            $empId = is_array($emp) ? $emp['id'] : $emp;
            $employee = Employee::find($empId);

            if (!$employee || !$employee->working_hours_restriction) continue;

            $weeklyLimitMinutes = ($employee->max_weekly_hours ?? 0) * 60;


            $weeklyMinutes = [];
            foreach ($datesToProcess as $date) {
                $weekKey = Carbon::parse($date)->startOfWeek()->format('Y-m-d');
                if (!isset($weeklyMinutes[$weekKey])) {
                    $weeklyMinutes[$weekKey] = 0;
                }
                $weeklyMinutes[$weekKey] += $this->getShiftWorkingMinutes(
                    $this->newShift['start_time'],
                    $this->newShift['end_time'],
                    $this->newBreaks ?? []
                );
            }

            foreach ($weeklyMinutes as $weekStart => $newMinutes) {
                $usedMinutes = $this->getEmployeeWeeklyMinutes($empId, $weekStart);
                if ($usedMinutes + $newMinutes > $weeklyLimitMinutes) {
                    $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
                    $this->toast("Weekly limit exceeded for {$employee->full_name} ($weekStart to $weekEnd)", 'error');
                    return;
                }
            }
        }

        // Check for conflicts
        if (!$this->skipConflictCheck) {
            $allConflicts = [];
            foreach ($datesToProcess as $date) {
                $conflicts = $this->getConflicts($date, $this->newShift['employees']);
                if ($conflicts->isNotEmpty()) {
                    $allConflicts[$date] = $conflicts;
                }
            }

            if (!empty($allConflicts)) {
                $this->conflictData = $allConflicts;
                $this->dispatch('show-conflict-modal');
                return;
            }
        }

        // Check for already assigned employees
        if (!$this->skipConflictCheck) {
            foreach ($datesToProcess as $date) {
                $alreadyAssigned = DB::table('shift_employees')
                    ->join('shift_dates', 'shift_dates.id', '=', 'shift_employees.shift_date_id')
                    ->where('shift_dates.date', $date)
                    ->whereIn('shift_employees.employee_id', $this->newShift['employees'])
                    ->pluck('shift_employees.employee_id')
                    ->toArray();

                if ($alreadyAssigned) {
                    $names = Employee::whereIn('id', $alreadyAssigned)->pluck('f_name')->implode(', ');
                    $this->toast("Employees already assigned on $date: $names", 'error');

                    $this->closeAddShiftPanel();
                    $this->dispatch('refreshSchedule');
                    return;
                }
            }
        }

        // Create the shift
        $shift = Shift::create([
            'company_id' => $this->company_id,
            'title' => $this->newShift['title'],
            'job' => $this->newShift['job'],
            'color' => $this->newShift['color'],
            'address' => $this->newShift['address'],
            'note' => $this->newShift['note'],
        ]);

        // Create shift dates
        foreach ($datesToProcess as $date) {
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

        $this->isEditableShift = false;
        $this->closeAddShiftPanel();
        $this->cancelRepeatShift();
        $this->selectedDates = [];
        $this->hasMultipleDates = false;
        $this->loadShifts();
        $this->dispatch('refreshSchedule');
        $this->toast('Shift has been published successfully for ' . count($datesToProcess) . ' date(s)!', 'success');
    }


    private function generateRepeatedDates($startDate)
    {
        $dates = [];
        $currentDate = $startDate;
        $count = 0;

        while (true) {
            $dates[] = $currentDate;
            $count++;

            if ($this->endRepeat === 'After' && $count >= $this->occurrences) {
                break;
            }
            if ($this->endRepeat === 'On' && Carbon::parse($currentDate) >= Carbon::parse($this->endRepeatDate)) {
                break;
            }

            $currentDate = $this->getNextOccurrence($currentDate);
        }

        return $dates;
    }







    public function confirmOverwrite()
    {
        $empIdsToReplace = $this->isClickMultipleShift
            ? collect($this->multipleShifts)->pluck('employees')->flatten(1)->pluck('id')->unique()->toArray()
            : $this->newShift['employees'];


        foreach ($this->conflictData as $date => $shifts) {
            foreach ($shifts as $shift) {

                $shiftDate = $shift->dates->firstWhere('date', $date);

                if ($shiftDate) {

                    $shiftDate->employees()->detach($empIdsToReplace);


                    if ($shiftDate->employees()->doesntExist()) {
                        $shiftDate->breaks()->delete();
                        $shiftDate->delete();
                    }
                }
            }
        }

        $this->skipConflictCheck = true;

        if ($this->isClickMultipleShift) {
            $this->publishMultipleShifts();
        } else {
            $this->publishShift();
        }

        // Clean-up
        $this->skipConflictCheck = false;
        $this->conflictData = [];
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
            if (empty($row['date'])) continue;

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






        foreach ($this->multipleShifts as $shift) {

            foreach ($shift['employees'] as $emp) {

                $empId = is_array($emp) ? $emp['id'] : $emp;

                $employee = Employee::find($empId);

                if (!$employee || !$employee->working_hours_restriction) continue;

                $weeklyLimitMinutes = ($employee->max_weekly_hours ?? 0) * 60;

                $usedMinutes = $this->getEmployeeWeeklyMinutes($empId, $shift['date']);

                $newShiftMinutes = $this->getShiftWorkingMinutes(
                    $shift['start_time'],
                    $shift['end_time'],
                    $this->multipleShiftNewBreaks ?? []
                );

                if ($usedMinutes + $newShiftMinutes > $weeklyLimitMinutes) {

                    $name = $employee->full_name;

                    $this->toast("Weekly limit exceeded for $name", 'error');
                    return;
                }
            }
        }



        $this->validate($rules, $messages);

        foreach ($this->multipleShifts as $index => $shift) {
            if (!$this->skipConflictCheck) {
                $allConflicts = [];
                foreach ($this->multipleShifts as $index => $shift) {
                    $employeeIds = collect($shift['employees'])->pluck('id')->toArray();
                    $conflicts = $this->getConflicts($shift['date'], $employeeIds);

                    if ($conflicts->isNotEmpty()) {

                        if (!isset($allConflicts[$shift['date']])) {
                            $allConflicts[$shift['date']] = $conflicts;
                        } else {

                            $allConflicts[$shift['date']] = $allConflicts[$shift['date']]->merge($conflicts);
                        }
                    }
                }

                if (!empty($allConflicts)) {
                    $this->conflictData = $allConflicts;
                    $this->dispatch('show-conflict-modal');
                    return;
                }
            }


            if (!$this->skipConflictCheck) {
                foreach ($this->multipleShifts as $shift) {
                    $employeeIds = collect($shift['employees'])->pluck('id')->toArray();

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
                }
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
        $this->startDate = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $this->endDate = $this->startDate->copy()->endOfWeek(Carbon::SUNDAY);
        $this->currentDate = Carbon::today();

        $this->loadShiftsAsync();

        $this->selectedDates = [];
        $this->selectedDateDisplay = '';
        $this->selectedDate = null;
    }




    public function loadShiftsAsync()
    {
        $this->isLoading = true;


        $this->dispatch('start-loading-shifts');


        $startDate = $this->startDate instanceof Carbon ? $this->startDate : Carbon::parse($this->startDate);
        $endDate = $this->endDate instanceof Carbon ? $this->endDate : Carbon::parse($this->endDate);


        $shifts = ShiftDate::whereHas('shift', function ($q) {
            $q->where('company_id', $this->company_id);
        })
            ->whereBetween('date', [$startDate, $endDate])
            ->with([
                'shift:id,title,color,address,note',
                'employees:id,f_name,l_name',
                'breaks:id,shift_date_id,title,type,duration'
            ])
            ->get();


        $this->loadedShifts = $shifts->map(function ($shiftDate) {
            return [
                'id' => $shiftDate->id,
                'date' => $shiftDate->date,
                'start_time' => $shiftDate->start_time,
                'end_time' => $shiftDate->end_time,
                'total_hours' => $shiftDate->total_hours,
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
                })->values(),
                'breaks' => $shiftDate->breaks->map(function ($b) {
                    return [
                        'title' => $b->title,
                        'type' => $b->type,
                        'duration' => $b->duration,
                    ];
                })->values(),
            ];
        })->groupBy('date')
            ->map(function ($group) {
                return $group->values();
            })
            ->toArray();

        $this->calendarShifts = $this->loadedShifts;
        $this->isLoading = false;

        $this->dispatch('shifts-loaded');
        $this->dispatch('refresh-summary');
    }


    public function loadShifts()
    {
        if (!$this->isLoading) {
            $this->loadShiftsAsync();
        }
    }




    public function loadMoreEmployees()
    {
        if (!$this->hasMoreEmployees) {
            return;
        }

        $moreEmployees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->skip($this->loadedEmployees)
            ->take($this->perPageEmployees)
            ->get(['id', 'f_name', 'l_name', 'role']);

        $this->employees = $this->employees->concat($moreEmployees);
        $this->loadedEmployees = count($this->employees);


        $totalEmployees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->count();

        $this->hasMoreEmployees = $this->loadedEmployees < $totalEmployees;
    }




    protected function loadEmployees()
    {
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->take($this->perPageEmployees)
            ->get(['id', 'f_name', 'l_name', 'role']);

        $this->loadedEmployees = count($this->employees);


        $totalEmployees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->count();

        $this->hasMoreEmployees = $this->loadedEmployees < $totalEmployees;
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
                        'type' => 'Shift',
                        'id' => $shiftDate['id'],
                        'title' => $shiftDate['shift']['title'],
                        'color' => $shiftDate['shift']['color'],
                        'time' => Carbon::parse($shiftDate['start_time'])->format('g:i A')
                            . ' - ' .
                            Carbon::parse($shiftDate['end_time'])->format('g:i A'),
                        'employees' => $shiftDate['employees'],
                        'total_hours' => $shiftDate['total_hours'],
                        'shift' => [
                            'address' => $shiftDate['shift']['address'] ?? '-',
                            'note' => $shiftDate['shift']['note'] ?? '-',
                        ],
                        'breaks' => $shiftDate['breaks'],
                    ];
                }
            }
        }

        return null;
    }




    public function updatedSearch()
    {
        $query = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->search . '%')
                    ->orWhere('l_name', 'like', '%' . $this->search . '%')
                    ->orWhereRaw("CONCAT(f_name, ' ', l_name) like ?", ['%' . $this->search . '%']);
            });
        }

        $this->employees = $query->orderBy('f_name')
            ->take($this->perPageEmployees)
            ->get(['id', 'f_name', 'l_name', 'role']);

        $this->loadedEmployees = count($this->employees);

        if (!$this->search) {
            $totalEmployees = Employee::where('company_id', $this->company_id)
                ->whereNotNull('user_id')
                ->count();
            $this->hasMoreEmployees = $this->loadedEmployees < $totalEmployees;
        } else {
            $this->hasMoreEmployees = false;
        }


        $this->dispatch('refresh-summary');
    }



    public function getWeekDaysProperty()
    {
        $days = [];

        if ($this->viewMode === 'weekly') {
            $monday = $this->startDate->copy()->startOfWeek(Carbon::MONDAY);

            for ($i = 0; $i < 7; $i++) {
                $day = $monday->copy()->addDays($i);
                $days[] = [
                    'full_date' => $day->format('Y-m-d'),
                    'day'       => $day->format('D'),
                    'date'      => $day->format('d/m'),
                    'highlight' => $day->equalTo($this->currentDate->copy()->startOfDay()),
                ];
            }
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

            if ($this->startDate->format('Y') !== $this->endDate->format('Y')) {
                return $this->startDate->format('M d, Y') . ' - ' . $this->endDate->format('M d, Y');
            } elseif ($this->startDate->format('m') !== $this->endDate->format('m')) {
                return $this->startDate->format('M d') . ' - ' . $this->endDate->format('M d, Y');
            } else {
                return $this->startDate->format('M d') . ' - ' . $this->endDate->format('d, Y');
            }
        } elseif ($this->viewMode === 'monthly') {
            return $this->startDate->format('F Y');
        }

        return '';
    }



    public function toggleOnAllDay($value)
    {
        $this->newShift['all_day'] = $value;

        if ($value) {
            $this->newShift['start_time'] = '09:00';
            $this->newShift['end_time'] = '17:00';

            $this->newShift['total_hours'] = '08:00';
            $this->originalShiftTotalTime = $this->newShift['total_hours'];

            [$hours, $minutes] = explode(':', $this->newShift['total_hours']);
            $totalMinutes = ($hours * 60) + $minutes;


            if (!empty($this->paidBreaksDuration)) {
                [$paidHours, $paidMinutes] = explode(':', $this->paidBreaksDuration);
                $totalMinutes += ($paidHours * 60) + $paidMinutes;
            }

            $newHours = floor($totalMinutes / 60);
            $newMinutes = $totalMinutes % 60;

            $this->newShift['total_hours'] = sprintf('%02d:%02d', $newHours, $newMinutes);
        }
    }



    public function toggleMultiAllDayForShift($shiftIndex, $value)
    {

        $this->multipleShifts[$shiftIndex]['all_day'] = $value;

        if ($value) {

            $this->multipleShifts[$shiftIndex]['start_time'] = '09:00';
            $this->multipleShifts[$shiftIndex]['end_time'] = '17:00';
            $this->multipleShifts[$shiftIndex]['total_hours'] = '08:00';


            $this->originalMultiShiftTotalTime[$shiftIndex] = $this->multipleShifts[$shiftIndex]['total_hours'];


            [$hours, $minutes] = explode(':', $this->multipleShifts[$shiftIndex]['total_hours']);
            $totalMinutes = ($hours * 60) + $minutes;


            $paidBreakMinutes = $this->calculateTotalBreakMinutes($shiftIndex);
            $totalMinutes += $paidBreakMinutes;

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

        $paidBreakMinutes = $this->calculateTotalBreakMinutes($shiftIndex);
        $totalMinutes += $paidBreakMinutes;

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

                $durationStr = $break['duration'];
                $parts = explode('.', $durationStr);

                $hours = (int) $parts[0];
                $minutes = isset($parts[1]) ? (int) substr($parts[1], 0, 2) : 0;


                $breakMinutes = ($hours * 60) + $minutes;


                if ($minutes >= 60) {
                    $hours += floor($minutes / 60);
                    $minutes = $minutes % 60;
                    $breakMinutes = ($hours * 60) + $minutes;
                }

                if (strtolower($break['type']) == 'paid') {
                    $this->totalBreakMinutes += $breakMinutes;
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

            $shiftTotalMinutes += $paidMinutes;

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

            $this->loadTemplates();


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




    private function recalculateBreakSummary()
    {
        $paid = collect($this->newBreaks)->where('type', 'Paid');
        $unpaid = collect($this->newBreaks)->where('type', 'Unpaid');

        $this->paidBreaksCount   = $paid->count();
        $this->unpaidBreaksCount = $unpaid->count();


        $paidTotalMinutes = $paid->sum(function ($break) {
            return $this->decimalFormatToMinutes($break['duration']);
        });

        $unpaidTotalMinutes = $unpaid->sum(function ($break) {
            return $this->decimalFormatToMinutes($break['duration']);
        });

        $this->paidBreaksDuration = $this->minutesToTime($paidTotalMinutes);
        $this->unpaidBreaksDuration = $this->minutesToTime($unpaidTotalMinutes);
    }


    private function decimalFormatToMinutes($decimalFormat)
    {
        if (empty($decimalFormat) && $decimalFormat !== 0) {
            return 0;
        }

        $durationStr = (string) $decimalFormat;
        $parts = explode('.', $durationStr);

        $hours = (int) $parts[0];
        $minutes = isset($parts[1]) ? (int) substr($parts[1], 0, 2) : 0;

        return ($hours * 60) + $minutes;
    }


    private function minutesToTime($totalMinutes)
    {
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        return sprintf('%02d:%02d', $hours, $minutes);
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


    public function calcCalendarSummary(): array
    {
        $totalMinutes = 0;
        $shiftCount   = 0;
        $userIds      = [];

        $start = $this->startDate instanceof Carbon
            ? $this->startDate
            : Carbon::parse($this->startDate);
        $end = $this->endDate instanceof Carbon
            ? $this->endDate
            : Carbon::parse($this->endDate);

        $filteredEmployeeIds = $this->employees->pluck('id')->toArray();

        foreach ($this->calendarShifts as $dateKey => $shifts) {
            $date = Carbon::parse($dateKey);

            if ($date->lt($start) || $date->gt($end)) {
                continue;
            }

            foreach ($shifts as $row) {

                $filteredEmployees = $row['employees']->filter(function ($emp) use ($filteredEmployeeIds) {
                    return in_array($emp['id'], $filteredEmployeeIds);
                });

                $employeeCount = $filteredEmployees->count();

                if ($employeeCount > 0) {
                    $shiftCount += $employeeCount;

                    [$h, $m] = explode(':', $row['total_hours'] ?? '00:00');
                    $shiftMinutes = ((int)$h * 60) + (int)$m;
                    $totalMinutes += ($shiftMinutes * $employeeCount);

                    foreach ($filteredEmployees as $emp) {
                        $userIds[$emp['id']] = true;
                    }
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



    public function downloadSchedulePDF()
    {
        $employees = Employee::where('company_id', auth()->user()->company->id)
            ->orderBy('f_name')
            ->get();

        $weekDays = $this->weekDays;
        $calendarShifts = $this->calendarShifts;
        $viewMode = $this->viewMode;

        $weeks = [];

        if ($viewMode === 'monthly') {
            $dateInMonth = $this->startDate ?? Carbon::now();
            $startOfMonth = Carbon::parse($dateInMonth)->startOfMonth();
            $endOfMonth = Carbon::parse($dateInMonth)->endOfMonth();
            $calendarStart = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
            $calendarEnd = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

            $dates = [];
            $current = $calendarStart->copy();
            while ($current->lte($calendarEnd) && count($dates) < 42) {
                $dates[] = $current->copy();
                $current->addDay();
            }
            $weeks = array_chunk($dates, 7);
        }

        $calendarShiftsByEmployee = [];

        foreach ($calendarShifts as $dateKey => $shifts) {
            foreach ($shifts as $shift) {
                foreach ($shift['employees'] as $emp) {
                    $calendarShiftsByEmployee[$emp['id']][$dateKey][] = $shift;
                }
            }
        }

        $employeesWithShifts = $employees->filter(function ($employee) use ($calendarShiftsByEmployee) {
            return isset($calendarShiftsByEmployee[$employee->id]) && !empty($calendarShiftsByEmployee[$employee->id]);
        });


        $startDate = Carbon::parse($this->startDate ?? now())->format('Y-m-d');
        $endDate = Carbon::parse($this->endDate ?? now())->format('Y-m-d');

        $pdf = Pdf::loadView('livewire.backend.company.schedule.schedule-pdf', [
            'employees' => $employeesWithShifts,
            'weekDays' => $weekDays,
            'calendarShifts' => $calendarShiftsByEmployee,
            'viewMode' => $viewMode,
            'weeks' => $weeks,
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->setPaper('a4', 'landscape');

        $startDateFormatted = Carbon::parse($startDate)->format('d F Y');
        $endDateFormatted = Carbon::parse($endDate)->format('d F Y');

        $filename = 'schedule_all_employees_' . "_{$startDateFormatted}_to_{$endDateFormatted}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }


    private function getShiftWorkingMinutes($startTime, $endTime, $breaks): int
    {
        $start = Carbon::createFromFormat('H:i', $startTime);
        $end   = Carbon::createFromFormat('H:i', $endTime);

        if ($end->lt($start)) {
            $end->addDay();
        }

        $totalMinutes = $end->diffInMinutes($start);
        $totalMinutes = abs($totalMinutes);

        $paidBreakMinutes = 0;

        foreach ($breaks as $break) {
            if (
                !empty($break['duration']) &&
                strtolower($break['type'] ?? '') === 'paid'
            ) {
                $duration = (float) $break['duration'];

                $hours = floor($duration);
                $minutes = ($duration - $hours) * 60;

                $paidBreakMinutes += ($hours * 60) + $minutes;
            }
        }

        return $totalMinutes + $paidBreakMinutes;
    }



    private function getEmployeeWeeklyMinutes($employeeId, $date): int
    {
        $weekStart = Carbon::parse($date)->startOfWeek();
        $weekEnd   = Carbon::parse($date)->endOfWeek();

        $shiftDates = ShiftDate::whereBetween('date', [$weekStart, $weekEnd])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->with('breaks')
            ->get();

        $totalMinutes = 0;

        foreach ($shiftDates as $sd) {

            [$h, $m] = explode(':', $sd->total_hours ?? '00:00');
            $shiftMinutes = ($h * 60) + $m;


            foreach ($sd->breaks as $b) {
                if (strtolower($b->type) === 'paid') {
                    $duration = (float) $b->duration;

                    $hours = floor($duration);
                    $minutes = ($duration - $hours) * 100;

                    $shiftMinutes += ($hours * 60) + $minutes;
                }
            }

            $totalMinutes += $shiftMinutes;
        }

        return $totalMinutes;
    }



    public function editOneEmpShift($dateId, $empId)
    {
        $this->isClickMultipleShift = false;
        $this->isEditableShift = true;
        $this->resetFields();

        $shiftDate = ShiftDate::findOrFail($dateId);

        $this->editingShiftDateId = $dateId;
        $this->editingEmployeeId = $empId;

        $formattedDate = Carbon::parse($shiftDate->date)->format('Y-m-d');
        $this->selectedDate = $formattedDate;
        $this->selectedDates = [$formattedDate];

        $this->selectedDateDisplay = $formattedDate;


        $this->newShift = [
            'title'       => $shiftDate->shift->title ?? '',
            'job'         => $shiftDate->shift->job ?? '',
            'start_time'  => Carbon::parse($shiftDate->start_time)->format('H:i'),
            'end_time'    => Carbon::parse($shiftDate->end_time)->format('H:i'),
            'total_hours' => $shiftDate->total_hours,
            'color'       => $shiftDate->shift->color ?? '#000000',
            'address'     => $shiftDate->shift->address ?? '',
            'note'        => $shiftDate->shift->note ?? '',
            'all_day'     => $shiftDate->shift->all_day ?? false,
            'employees'   => [(int) $empId],
        ];


        if ($shiftDate->breaks->isNotEmpty()) {
            $this->newBreaks = $shiftDate->breaks
                ->map(fn($b) => [
                    'name'     => $b->title,
                    'type'     => $b->type,
                    'duration' => $b->duration,
                ])
                ->toArray();

            $this->showAddBreakForm = true;
            $this->recalculateBreakSummary();
        } else {
            $this->showAddBreakForm = false;
            $this->newBreaks = [];
            $this->paidBreaksCount = 0;
            $this->unpaidBreaksCount = 0;
            $this->paidBreaksDuration = '00:00';
            $this->unpaidBreaksDuration = '00:00';
        }

        $this->dispatch('shift-panel-opened');
    }



    public function updateShift()
    {
        $this->cleanSelectedDates();


        if ($this->isSavedRepeatShift) {
            $baseDate = !empty($this->selectedDates) ? $this->selectedDates[0] : $this->selectedDate;
            $datesToProcess = $this->generateRepeatedDates($baseDate);
        } else {
            $datesToProcess = !empty($this->selectedDates) ? array_unique($this->selectedDates) : [$this->selectedDate];
        }

        if (empty($datesToProcess)) {
            $this->toast('Please select at least one date for the shift', 'error');
            return;
        }

        $this->validate([
            'newShift.title' => 'required|string',
            'newShift.start_time' => 'required|date_format:H:i',
            'newShift.end_time' => 'required|date_format:H:i|after:newShift.start_time',
            'newShift.employees' => 'required|array|min:1',
            'newShift.address' => 'nullable|string|max:255',
            'newShift.note' => 'nullable|string|max:500',
            'newShift.job' => 'required|string|max:100',
            'newBreaks' => 'nullable|array',
        ]);

        // Check for employees on leave
        $employeesOnLeave = [];
        foreach ($this->newShift['employees'] as $emp) {
            $empId = is_array($emp) ? $emp['id'] : $emp;

            foreach ($datesToProcess as $date) {
                if (hasLeave($empId, $date)) {
                    $employee = Employee::find($empId);
                    $employeesOnLeave[] = $employee->full_name . " ($date)";
                }
            }
        }

        if ($employeesOnLeave) {
            $display = implode(', ', array_slice($employeesOnLeave, 0, 3));
            if (count($employeesOnLeave) > 3) $display .= ' ...';
            $this->toast("Employees on leave: $display", 'error');
            return;
        }


        foreach ($this->newShift['employees'] as $empId) {
            $employeeId = is_array($empId) ? $empId['id'] : $empId;
            $employee = Employee::find($employeeId);

            if ($employee && $employee->working_hours_restriction) {
                $weeklyLimitMinutes = ($employee->max_weekly_hours ?? 0) * 60;

                $weeklyMinutes = [];
                foreach ($datesToProcess as $date) {
                    $weekKey = Carbon::parse($date)->startOfWeek()->format('Y-m-d');
                    if (!isset($weeklyMinutes[$weekKey])) {
                        $weeklyMinutes[$weekKey] = 0;
                    }
                    $weeklyMinutes[$weekKey] += $this->getShiftWorkingMinutes(
                        $this->newShift['start_time'],
                        $this->newShift['end_time'],
                        $this->newBreaks ?? []
                    );
                }

                foreach ($weeklyMinutes as $weekStart => $newMinutes) {
                    $usedMinutes = $this->getEmployeeWeeklyMinutesExcludingShift(
                        $employeeId,
                        $weekStart,
                        $this->editingShiftDateId
                    );

                    if ($usedMinutes + $newMinutes > $weeklyLimitMinutes) {
                        $weekEnd = Carbon::parse($weekStart)->endOfWeek()->format('Y-m-d');
                        $this->toast("Weekly limit of {$employee->max_weekly_hours} hours for {$employee->full_name} ($weekStart to $weekEnd) will be exceeded", 'error');
                        return;
                    }
                }
            }
        }

        if (!$this->skipConflictCheck) {
            $allConflicts = [];
            foreach ($datesToProcess as $date) {

                if ($date == $this->selectedDate && $this->editingShiftDateId) {
                    continue;
                }
                $conflicts = $this->getConflicts($date, $this->newShift['employees']);
                if ($conflicts->isNotEmpty()) {
                    $allConflicts[$date] = $conflicts;
                }
            }

            if (!empty($allConflicts)) {
                $this->conflictData = $allConflicts;
                $this->dispatch('show-conflict-modal');

                return;
            }
        }


        $originalShiftDate = ShiftDate::findOrFail($this->editingShiftDateId);
        $originalDate = $originalShiftDate->date;
        $shiftId = $originalShiftDate->shift_id;


        foreach ($datesToProcess as $date) {

            if ($date == $originalDate) {
                continue;
            }

            $alreadyAssigned = DB::table('shift_employees')
                ->join('shift_dates', 'shift_dates.id', '=', 'shift_employees.shift_date_id')
                ->where('shift_dates.date', $date)
                ->whereIn('shift_employees.employee_id', $this->newShift['employees'])
                ->pluck('shift_employees.employee_id')
                ->toArray();

            if ($alreadyAssigned) {
                $names = Employee::whereIn('id', $alreadyAssigned)->pluck('f_name')->implode(', ');
                $this->toast("Employees already assigned on $date: $names", 'error');

                $this->closeAddShiftPanel();

                $this->dispatch('refreshSchedule');
                return;
            }
        }


        if (count($datesToProcess) > 1 || $datesToProcess[0] != $originalDate || $this->isSavedRepeatShift) {

            if (!in_array($originalDate, $datesToProcess)) {
                $originalShiftDate->employees()->detach();
                $originalShiftDate->breaks()->delete();
                $originalShiftDate->delete();
            } else {

                $datesToProcess = array_filter($datesToProcess, function ($date) use ($originalDate) {
                    return $date != $originalDate;
                });
            }


            $shift = Shift::find($shiftId);
            $shift->update([
                'title' => $this->newShift['title'],
                'job' => $this->newShift['job'],
                'color' => $this->newShift['color'],
                'address' => $this->newShift['address'],
                'note' => $this->newShift['note'],
            ]);


            foreach ($datesToProcess as $date) {
                $existingShiftDate = ShiftDate::where('shift_id', $shiftId)
                    ->where('date', $date)
                    ->first();

                if ($existingShiftDate) {

                    $existingShiftDate->update([
                        'start_time' => $this->newShift['start_time'],
                        'end_time' => $this->newShift['end_time'],
                        'total_hours' => $this->newShift['total_hours'],
                    ]);
                    $existingShiftDate->employees()->sync($this->newShift['employees']);

                    $existingShiftDate->breaks()->delete();
                    if (!empty($this->newBreaks)) {
                        foreach ($this->newBreaks as $break) {
                            if (!empty($break['name']) && !empty($break['type']) && !empty($break['duration'])) {
                                $existingShiftDate->breaks()->create([
                                    'title' => $break['name'],
                                    'type' => $break['type'],
                                    'duration' => $break['duration'],
                                ]);
                            }
                        }
                    }
                } else {

                    $shiftDate = ShiftDate::create([
                        'shift_id' => $shiftId,
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
        } else {

            $shiftDate = $originalShiftDate;

            $shiftDate->update([
                'date' => $this->selectedDate,
                'start_time' => $this->newShift['start_time'],
                'end_time' => $this->newShift['end_time'],
                'total_hours' => $this->newShift['total_hours'],
            ]);

            $shiftDate->shift->update([
                'title' => $this->newShift['title'],
                'job' => $this->newShift['job'],
                'color' => $this->newShift['color'],
                'address' => $this->newShift['address'],
                'note' => $this->newShift['note'],
            ]);

            $shiftDate->employees()->sync($this->newShift['employees']);

            $shiftDate->breaks()->delete();

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

        $this->isEditableShift = false;
        $this->editingShiftDateId = null;
        $this->editingEmployeeId = null;
        $this->closeAddShiftPanel();
        $this->cancelRepeatShift();
        $this->selectedDates = [];
        $this->hasMultipleDates = false;
        $this->isSavedRepeatShift = false;
        $this->skipConflictCheck = false;
        $this->loadShifts();
        $this->dispatch('refreshSchedule');
        $this->toast('Shift Updated Successfully for ' . count($datesToProcess) . ' date(s)!', 'success');
    }


    private function getEmployeeWeeklyMinutesExcludingShift($employeeId, $date, $excludeShiftDateId = null): int
    {
        $weekStart = Carbon::parse($date)->startOfWeek();
        $weekEnd   = Carbon::parse($date)->endOfWeek();

        $shiftDates = ShiftDate::whereBetween('date', [$weekStart, $weekEnd])
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->with('breaks')
            ->get();


        if ($excludeShiftDateId) {
            $shiftDates = $shiftDates->filter(fn($sd) => $sd->id != $excludeShiftDateId);
        }

        $totalMinutes = 0;

        foreach ($shiftDates as $sd) {
            [$h, $m] = explode(':', $sd->total_hours ?? '00:00');
            $shiftMinutes = ($h * 60) + $m;

            foreach ($sd->breaks as $b) {
                if (strtolower($b->type) === 'paid') {
                    $duration = (float) $b->duration;
                    $hours = floor($duration);
                    $minutes = ($duration - $hours) * 100;
                    $shiftMinutes += ($hours * 60) + $minutes;
                }
            }

            $totalMinutes += $shiftMinutes;
        }

        return $totalMinutes;
    }



    public function render()
    {
        $this->loadShifts();

        $summary = $this->calcCalendarSummary();


        return view('livewire.backend.company.schedule.schedule-index', [
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,
            'employees' => $this->employees,
            'displayDateRange' => $this->displayDateRange,
            'isLoading' => $this->isLoading,
            'summary' => $summary,
            'availableMultipleShiftEmployees' => $this->availableMultipleShiftEmployees,
            'conflictData' => $this->conflictData ?? collect(),
        ]);
    }
}
