<?php

namespace App\Livewire\Backend\Employee\Schedule;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\ShiftDate;
use Carbon\Carbon;

class ScheduleIndex extends BaseComponent
{
    public $viewMode = 'weekly';
    public $employees;
    public $startDate;
    public $endDate;
    public $currentDate;
    public $company_id;

    public $calendarShifts = [];



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



    public function loadShifts()
    {
        $user = auth()->user();
        if (!$user) {
            $this->calendarShifts = [];
            return;
        }

        $today = Carbon::today();

        $this->calendarShifts = ShiftDate::whereDate('date', $today)
            ->whereHas('employees', fn($q) => $q->where('user_id', $user->id))
            ->whereHas('shift', fn($q) => $q->where('company_id', $this->company_id))
            ->with([
                'shift:id,title,color,address,note',
                'employees:id,f_name,l_name,user_id',
                'breaks:id,shift_date_id,title,type,duration'
            ])
            ->get()
            ->map(fn($sd) => [
                'id'           => $sd->id,
                'date'         => $sd->date,
                'start_time'   => $sd->start_time,
                'end_time'     => $sd->end_time,
                'total_hours'  => $sd->total_hours,
                'shift'        => [
                    'title'   => $sd->shift->title ?? null,
                    'color'   => $sd->shift->color ?? '#6c757d',
                    'address' => $sd->shift->address ?? null,
                    'note'    => $sd->shift->note ?? null,
                ],
                'employees' => $sd->employees->map(fn($e) => [
                    'id'   => $e->id,
                    'name' => $e->f_name . ' ' . $e->l_name,
                ])->toArray(),
                'breaks' => $sd->breaks->map(fn($b) => [
                    'title'    => $b->title,
                    'type'     => $b->type,
                    'duration' => $b->duration,
                ])->toArray(),
            ])
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


    public function getDisplayDateRangeProperty()
    {
        if ($this->viewMode === 'weekly') {
            return $this->startDate->format('M d') . ' - ' . $this->endDate->format('M d');
        } elseif ($this->viewMode === 'monthly') {
            return $this->startDate->format('F Y');
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




    public function mount()
    {
        $this->company_id = auth()->user()->employee->company_id;

        $this->startDate = Carbon::today();
        $this->endDate = Carbon::today()->copy()->addDays(6);
        $this->currentDate = Carbon::today();
        $this->loadEmployees();
        $this->loadShifts();
        $this->calcCalendarSummary();
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




    public function render()
    {

        $this->loadShifts();


        return view('livewire.backend.employee.schedule.schedule-index', [
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,
            'employees' => $this->employees,
            'displayDateRange' => $this->displayDateRange,
        ]);
    }
}
