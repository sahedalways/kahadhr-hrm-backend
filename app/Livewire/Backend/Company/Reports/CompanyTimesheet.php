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
        $this->employees = Employee::withoutGlobalScope('isActive')->where('company_id', $this->company_id)
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
                $q->whereIn('user_id', Employee::withoutGlobalScope('isActive')->whereIn('id', $this->selectedEmployees)->pluck('user_id'));
            })
            ->orderBy('clock_in')
            ->get();

        $data = $attendances->map(function ($att) {
            $clockIn = Carbon::parse($att->clock_in);
            $clockOut = $att->clock_out ? Carbon::parse($att->clock_out) : null;

            // Worked Hours Calculation
            $workedMinutes = $clockOut ? $clockIn->diffInMinutes($clockOut) : 0;
            $workedHoursFormatted = str_pad(floor($workedMinutes / 60), 2, '0', STR_PAD_LEFT) . ":" . str_pad($workedMinutes % 60, 2, '0', STR_PAD_LEFT);

            $shiftTotalHours = '00:00';
            $paidBreakFormatted = 'N/A';
            $unpaidBreakFormatted = 'N/A';

            if ($att->user && $att->user->employee) {
                $employee = $att->user->employee;
                $attendanceDate = $clockIn->format('Y-m-d');

                // Fetch shift with its related breaks
                $shiftDate = ShiftDate::whereDate('date', $attendanceDate)
                    ->whereHas('employees', function ($q) use ($employee) {
                        $q->where('employee_id', $employee->id);
                    })
                    ->with('breaks')
                    ->first();

                if ($shiftDate) {
                    $shiftTotalHours = $shiftDate->total_hours ?? '00:00';


                    $paidMinutes = $shiftDate->breaks->where('type', 'Paid')->sum(function ($break) {
                        $parts = explode('.', $break->duration);

                        $hours = (int) ($parts[0] ?? 0);
                        $minutes = (int) ($parts[1] ?? 0);

                        return ($hours * 60) + $minutes;
                    });


                    $unpaidMinutes = $shiftDate->breaks->where('type', 'Unpaid')->sum(function ($break) {
                        $parts = explode('.', $break->duration);

                        $hours = (int) ($parts[0] ?? 0);
                        $minutes = (int) ($parts[1] ?? 0);

                        return ($hours * 60) + $minutes;
                    });


                    if ($paidMinutes > 0) {
                        $paidBreakFormatted =
                            str_pad(floor($paidMinutes / 60), 2, '0', STR_PAD_LEFT) . ':' .
                            str_pad($paidMinutes % 60, 2, '0', STR_PAD_LEFT);
                    }


                    if ($unpaidMinutes > 0) {
                        $unpaidBreakFormatted =
                            str_pad(floor($unpaidMinutes / 60), 2, '0', STR_PAD_LEFT) . ':' .
                            str_pad($unpaidMinutes % 60, 2, '0', STR_PAD_LEFT);
                    }
                }
            }

            return [
                'employee'     => $att->user->employee->full_name ?? '',
                'date'         => $clockIn->format('d-m-Y'),
                'clock_in'     => $clockIn->format('h:i A'),
                'clock_out'    => $clockOut ? $clockOut->format('h:i A') : '---',
                'worked_hours' => $workedHoursFormatted,
                'shift_total'  => $shiftTotalHours,
                'paid_break'   => $paidBreakFormatted,
                'unpaid_break' => $unpaidBreakFormatted,
                'nature'       => $att->is_manual ? 'Manual' : 'Automatic',
                'status'       => ucfirst($att->status),
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
                    'Shift Total',
                    'Paid Break',
                    'Unpaid Break',
                    'Nature',
                    'Status'
                ],
                'keys' => [
                    'employee',
                    'date',
                    'clock_in',
                    'clock_out',
                    'worked_hours',
                    'shift_total',
                    'paid_break',
                    'unpaid_break',
                    'nature',
                    'status'
                ],
            ]
        );
    }



    public function render()
    {
        return view('livewire.backend.company.reports.company-timesheet');
    }
}
