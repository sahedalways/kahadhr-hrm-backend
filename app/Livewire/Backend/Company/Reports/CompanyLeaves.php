<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Traits\Exportable;
use Carbon\Carbon;

class CompanyLeaves extends BaseComponent
{
    use Exportable;
    public $company;
    public $employees;
    public $status = 'active';

    public $employeeType = 'all';
    public $selectedEmployees = [];

    public $selectAllUsers = false;
    public $dateRangeType = 'custom';
    public $startDate;
    public $endDate;
    public $selectedMonth;
    public $selectedYear;

    public $company_id = null;

    public $leaveCategory = ['paid', 'unpaid'];



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


    public function updatedDateRangeType()
    {
        $this->reset(['startDate', 'endDate', 'selectedMonth', 'selectedYear']);
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;
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

    public function exportFile($type)
    {
        [$from, $to] = $this->resolveDateRange();

        $query = LeaveRequest::with('leaveType', 'user.employee')
            ->where('company_id', $this->company_id)
            ->whereBetween('start_date', [$from, $to]);

        if ($this->employeeType == 'selected' && !empty($this->selectedEmployees)) {
            $userIds = Employee::whereIn('id', $this->selectedEmployees)->pluck('user_id');
            $query->whereIn('user_id', $userIds);
        }

        if (!empty($this->leaveCategory)) {
            $query->whereIn('paid_status', $this->leaveCategory);
        }

        $leaves = $query->orderBy('start_date')->get();

        $data = $leaves->map(function ($leave) {
            return [
                'employee' => $leave->user->employee->full_name ?? '',
                'leave_type' => $leave->leaveType->name ?? '',
                'start_date' => Carbon::parse($leave->start_date)->format('d-m-Y'),
                'end_date'   => Carbon::parse($leave->end_date)->format('d-m-Y'),
                'hours' => $leave->total_hours,
                'status' => ucfirst($leave->status),
                'paid_status' => ucfirst($leave->paid_status ?? '---'),
                'remaining_annual_hours' => $leave->remaining_annual_hours,
            ];
        });

        $this->dispatch('closemodal');

        return $this->export(
            $data,
            $type,
            'leaves_report',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Leaves Report',
                'columns' => [
                    'Employee',
                    'Leave Type',
                    'Start Date',
                    'End Date',
                    'Hours',
                    'Status',
                    'Paid Status',
                    'Remaining Annual Hours',
                ],
                'keys' => [
                    'employee',
                    'leave_type',
                    'start_date',
                    'end_date',
                    'hours',
                    'status',
                    'paid_status',
                    'remaining_annual_hours',
                ],
            ]
        );
    }

    public function render()
    {
        return view('livewire.backend.company.reports.company-leaves', [
            'employees' => $this->employees,
        ]);
    }
}
