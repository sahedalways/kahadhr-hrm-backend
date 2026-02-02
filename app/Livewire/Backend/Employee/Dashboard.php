<?php

namespace App\Livewire\Backend\Employee;

use App\Helpers\AttendanceHelper;
use App\Models\Attendance;
use App\Models\CompanyDocument;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Models\Expenses;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\PaySlip;
use App\Models\PaySlipRequest;
use App\Models\ShiftDate;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $isRunning = false;
    public $currentAttendance;
    public $weeklyProgress = 0;
    public $workedHours = 0;
    public $contractHours = 0;

    public $leaveBalances = [];
    public $currentMonth;
    public $currentYear;


    public $upcomingHoliday;
    public $calendarEvents = [];

    public $ukHolidays = [];

    public $calendarFilters = [
        'leave'       => true,
        'birthday'    => true,
        'uk_holiday'  => true,
        'doc_expiry'  => true,
    ];




    public function mount()
    {
        $today = Carbon::today();
        $this->currentMonth = $today->month;
        $this->currentYear = $today->year;


        $this->currentAttendance = Attendance::where('user_id', auth()->id())
            ->whereDate('clock_in', now()->toDateString())
            ->latest()
            ->first();


        $data = AttendanceHelper::approvedAttendancesThisWeek();

        $this->workedHours = $data['total_worked_hours'];
        $this->contractHours = $data['weekly_contract_hours'];
        $this->weeklyProgress = $this->contractHours > 0
            ? min(100, ($this->workedHours / $this->contractHours) * 100)
            : 0;


        $this->checkRunningAttendance();
        $this->fetchLeaveBalances();
        $this->loadCalendarEvents();
    }


    public function previousMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadCalendarEvents();
    }

    public function nextMonth()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadCalendarEvents();
    }


    public function toggleCalendarFilter($type)
    {
        if (isset($this->calendarFilters[$type])) {
            $this->calendarFilters[$type] = !$this->calendarFilters[$type];

            $this->loadCalendarEvents();
        }
    }


    public function loadCalendarEvents()
    {
        $userId = auth()->user()->id;

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $calendarEvents = [];

        // 1. Leaves
        $leaves = LeaveRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth]);
            })
            ->with('user.employee.profile')
            ->get();

        foreach ($leaves as $leave) {
            $period = Carbon::parse($leave->start_date)->toPeriod(Carbon::parse($leave->end_date));
            foreach ($period as $date) {
                if ($date->month == $this->currentMonth) {
                    if ($this->calendarFilters['leave']) {
                        $calendarEvents[$date->toDateString()][] = [
                            'type' => 'leave',
                            'text' => $leave->leaveType->name ?? 'Leave',
                        ];
                    }
                }
            }
        }

        // 2. Birthdays
        $employees = Employee::with('profile')->where('user_id', $userId)->get();
        foreach ($employees as $emp) {
            $dob = optional($emp->profile)->date_of_birth;
            if ($dob && Carbon::parse($dob)->month == $this->currentMonth) {
                if ($this->calendarFilters['birthday']) {
                    $calendarEvents[Carbon::create($this->currentYear, $this->currentMonth, Carbon::parse($dob)->day)->toDateString()][] = [
                        'type' => 'birthday',
                        'text' => 'Your' . " Birthday",
                    ];
                }
            }
        }

        $year = Carbon::now()->year;


        $holidays = config('uk_holidays');


        $this->ukHolidays = $holidays[$year]['UK'] ?? [];
        $today = Carbon::now();

        foreach ($this->ukHolidays as $date => $text) {
            if (Carbon::parse($date)->month == $this->currentMonth) {
                if ($this->calendarFilters['uk_holiday']) {
                    $calendarEvents[$date][] = [
                        'type' => 'uk_holiday',
                        'text' => $text,
                    ];
                }
            }
        }


        foreach ($this->ukHolidays as $date => $text) {
            $holidayDate = Carbon::parse($date);

            if ($holidayDate->greaterThanOrEqualTo($today)) {
                $soonestHoliday = $text;
                break;
            }
        }

        $this->upcomingHoliday = $soonestHoliday;


        // 4. Document Expiry
        $today = Carbon::today();
        $companyDocs = CompanyDocument::where('emp_id', auth()->user()->employee->id)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$startOfMonth, $endOfMonth])
            ->get();

        $empDocs = EmpDocument::with('documentType')->where('emp_id', auth()->user()->employee->id)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$startOfMonth, $endOfMonth])
            ->get();

        $allDocs = $companyDocs->merge($empDocs);
        foreach ($allDocs as $doc) {
            if ($this->calendarFilters['doc_expiry']) {
                $calendarEvents[Carbon::parse($doc->expires_at)->toDateString()][] = [
                    'type' => 'doc_expiry',
                    'text' => $doc->name . ' Expiry',
                ];
            }
        }

        $this->calendarEvents = $calendarEvents;
    }




    public function fetchLeaveBalances()
    {
        $balance = LeaveBalance::where('user_id', auth()->id())->first();

        if (!$balance) {
            $this->leaveBalances = [];
            return;
        }

        $this->leaveBalances = [
            [
                'name' => 'Annual Leave',
                'emoji' => '🏖️',
                'total' => $balance->total_annual_hours,
                'used' => $balance->used_annual_hours,
                'remaining' => $balance->total_annual_hours - $balance->used_annual_hours,
                'percentage' => $balance->total_annual_hours > 0
                    ? round(($balance->used_annual_hours / $balance->total_annual_hours) * 100, 0)
                    : 0,
            ],
            [
                'name' => 'Leave in Lieu',
                'emoji' => '🤝🏻',
                'total' => $balance->total_leave_in_liew,
                'used' => $balance->used_leave_in_liew,
                'remaining' => $balance->total_leave_in_liew - $balance->used_leave_in_liew,
                'percentage' => $balance->total_leave_in_liew > 0
                    ? round(($balance->used_leave_in_liew / $balance->total_leave_in_liew) * 100, 0)
                    : 0,
            ]
        ];
    }



    public function checkRunningAttendance()
    {
        $this->isRunning = AttendanceHelper::isAttendanceRunning();
    }

    public function render()
    {

        $employeeId = auth()->user()->employee->id;
        $userId = auth()->user()->id;
        $today = Carbon::now('Europe/London')->toDateString();

        $todayShift = ShiftDate::whereDate('date', $today)
            ->whereHas('employees', function ($q) use ($employeeId) {
                $q->where('employees.id', $employeeId);
            })
            ->with('shift')
            ->first();


        $leaveRequests = LeaveRequest::with('leaveType')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $payslipRequests = PaySlipRequest::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $attendanceRequests = Attendance::with(['requests' => function ($q) {
            $q->where('status', 'pending');
        }])
            ->where('user_id', $userId)
            ->whereHas('requests', function ($q) {
                $q->where('status', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $expenses = Expenses::where('user_id', $userId)
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        $today = Carbon::today();


        $empExpiringDocs = EmpDocument::where('emp_id', $employeeId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $today->copy()->addDays(60)])
            ->with(['employee', 'documentType'])
            ->get();


        $companyExpiringDocs = CompanyDocument::where('emp_id', $employeeId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $today->copy()->addDays(60)])
            ->get();



        $expiringDocs = $empExpiringDocs
            ->map(function ($doc) {
                $doc->source = 'employee';
                return $doc;
            })
            ->merge(
                $companyExpiringDocs->map(function ($doc) {
                    $doc->source = 'company';
                    return $doc;
                })
            )
            ->sortBy('expires_at')
            ->values();

        $payslips = PaySlip::where('user_id', $userId)
            ->orderBy('period', 'desc')
            ->get();


        return view('livewire.backend.employee.dashboard', [
            'todayShift' => $todayShift,
            'leaveRequests' => $leaveRequests,
            'payslips' => $payslips,
            'expiringDocs' => $expiringDocs,
            'expenses' => $expenses,
            'payslipRequests' => $payslipRequests,
            'attendanceRequests' => $attendanceRequests,
        ]);
    }
}
