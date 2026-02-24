<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\ShiftDate;
use App\Traits\Exportable;
use Carbon\Carbon;


class CompanyTimesheet extends BaseComponent
{
    use Exportable;
    public $status = 'active';

    public $selectedEmployees = [];

    public $attendanceNature = [];
    public $dateRangeType = 'custom';

    public $startDate;
    public $endDate;
    public $selectedMonth;
    public $selectedYear;

    public $selectAllUsers = false;
    public $company_id = null;

    public $employees;

    public function updatedStatus($value)
    {

        $this->loadEmployees();
    }

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;


        $this->loadEmployees();
    }

    /** 🔹 Load employees by status */
    public function loadEmployees()
    {
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->whereHas('user', function ($q) {
                $q->where('is_active', $this->status == 'former' ? 0 : 1);
            })
            ->orderBy('f_name')
            ->get();


        // Reset selection when status changes
        $this->selectedEmployees = [];
        $this->selectAllUsers = false;
    }


    public function updatedSelectAllUsers($value)
    {
        if ($value) {
            $this->selectedEmployees = $this->employees->pluck('id')->toArray();
        } else {
            $this->selectedEmployees = [];
        }

        $this->dispatch('keep-employee-dropdown-open');
    }

    public function updatedSelectedEmployees()
    {
        // Auto uncheck "All Employees" if any employee unchecked
        if (count($this->selectedEmployees) != $this->employees->count()) {
            $this->selectAllUsers = false;
        }

        $this->dispatch('keep-employee-dropdown-open');
    }

    private function resolveDateRange()
    {
        return match ($this->dateRangeType) {
            'year' => [
                Carbon::create($this->selectedYear)->startOfYear(),
                Carbon::create($this->selectedYear)->endOfYear(),
            ],
            'month' => [
                Carbon::create($this->selectedYear, $this->selectedMonth)->startOfMonth(),
                Carbon::create($this->selectedYear, $this->selectedMonth)->endOfMonth(),
            ],
            'week' => [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ],
            default => [
                Carbon::parse($this->startDate),
                Carbon::parse($this->endDate),
            ],
        };
    }



    public function updatedDateRangeType()
    {
        $this->reset([
            'startDate',
            'endDate',
            'selectedMonth',
            'selectedYear',
        ]);
    }



    public function exportFile($type)
    {
        [$from, $to] = $this->resolveDateRange();

        $attendances = Attendance::with('user.employee')
            ->where('company_id', $this->company_id)
            ->whereBetween('clock_in', [$from, $to])
            ->when(!empty($this->selectedEmployees), function ($q) {
                $q->whereIn('user_id', Employee::whereIn('id', $this->selectedEmployees)->pluck('user_id'));
            })
            ->when(!empty($this->attendanceNature), function ($q) {
                $q->where(function ($sub) {
                    if (in_array('auto', $this->attendanceNature)) {
                        $sub->orWhere('is_manual', 0);
                    }
                    if (in_array('manual', $this->attendanceNature)) {
                        $sub->orWhere('is_manual', 1);
                    }
                });
            })
            ->orderBy('clock_in')
            ->get();

        $data = $attendances->map(function ($att) {
            $clockIn = Carbon::parse($att->clock_in);
            $clockOut = $att->clock_out ? Carbon::parse($att->clock_out) : null;

            $workedMinutes = $clockOut ? $clockIn->diffInMinutes($clockOut) : 0;

            $totalBreakMinutes = 0;

            if ($att->user && $att->user->employee) {
                $employee = $att->user->employee;

                $clockIn = Carbon::parse($att->clock_in);
                $attendanceDate = $clockIn->format('Y-m-d');


                $shiftDates = ShiftDate::whereDate('date', $attendanceDate)
                    ->whereHas('employees', function ($q) use ($employee) {
                        $q->where('employee_id', $employee->id);
                    })
                    ->with(['breaks' => function ($q) {

                        $q->whereNotNull('shift_date_id');
                    }])
                    ->get();

                foreach ($shiftDates as $shiftDate) {
                    if ($shiftDate->breaks->isNotEmpty()) {
                        $totalBreakMinutes += $shiftDate->breaks->sum('duration') * 60;
                    }
                }
            }



            $actualWorkedMinutes = max($workedMinutes - $totalBreakMinutes, 0);
            $hours = floor($actualWorkedMinutes / 60);
            $minutes = $actualWorkedMinutes % 60;


            $breakHours = floor($totalBreakMinutes / 60);
            $breakMinutes = $totalBreakMinutes % 60;
            $breakHoursFormatted = "{$breakHours}:" . str_pad($breakMinutes, 2, '0', STR_PAD_LEFT);

            return [
                'employee' => $att->user->employee->full_name ?? '',
                'date' => $clockIn->format('d-m-Y'),
                'clock_in' => $clockIn->format('h:i A'),
                'clock_out' => $clockOut ? $clockOut->format('h:i A') : '---',
                'worked_hours' => "{$hours}:" . str_pad($minutes, 2, '0', STR_PAD_LEFT),
                'break_hours' => $breakHoursFormatted,
                'nature' => $att->is_manual ? 'Manual' : 'Automatic',
                'status' => ucfirst($att->status),
            ];
        });

        $this->dispatch('closemodal');

        return $this->export(
            $data,
            $type,
            'timesheet_report',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Timesheet Report',
                'columns' => [
                    'Employee',
                    'Date',
                    'Clock In',
                    'Clock Out',
                    'Worked Hours',
                    'Break',
                    'Attendance Nature',
                    'Status',
                ],
                'keys' => [
                    'employee',
                    'date',
                    'clock_in',
                    'clock_out',
                    'worked_hours',
                    'break_hours',
                    'nature',
                    'status',
                ],
            ]
        );
    }




    public function render()
    {
        return view('livewire.backend.company.reports.company-timesheet');
    }
}
