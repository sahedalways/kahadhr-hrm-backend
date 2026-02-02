<?php

namespace App\Livewire\Backend\Company;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\CompanyDocument;
use App\Models\LeaveRequest;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Models\Expenses;
use App\Models\PaySlipRequest;
use App\Models\ShiftDate;
use App\Models\User;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $statusFilter = 'day';

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



    public function toggleCalendarFilter($type)
    {
        if (isset($this->calendarFilters[$type])) {
            $this->calendarFilters[$type] = !$this->calendarFilters[$type];

            $this->loadCalendarEvents();
        }
    }




    public function mount()
    {
        $today = Carbon::today();
        $this->currentMonth = $today->month;
        $this->currentYear = $today->year;

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




    public function loadCalendarEvents()
    {
        $companyId = auth()->user()->company->id;

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $calendarEvents = [];

        // 1. Leaves
        $leaves = LeaveRequest::where('company_id', $companyId)
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
                            'text' => $leave->user->full_name . ' - ' . $leave->leaveType->name ?? 'Leave',
                        ];
                    }
                }
            }
        }

        // 2. Birthdays
        $employees = Employee::with('profile')->where('company_id', $companyId)->get();
        foreach ($employees as $emp) {
            $dob = optional($emp->profile)->date_of_birth;
            if ($dob && Carbon::parse($dob)->month == $this->currentMonth) {
                if ($this->calendarFilters['birthday']) {
                    $calendarEvents[Carbon::create($this->currentYear, $this->currentMonth, Carbon::parse($dob)->day)->toDateString()][] = [
                        'type' => 'birthday',
                        'text' => $emp->full_name . "'s Birthday",
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
        $companyDocs = CompanyDocument::where('company_id', $companyId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$startOfMonth, $endOfMonth])
            ->get();

        $empDocs = EmpDocument::where('company_id', $companyId)
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



    public function render()
    {
        $companyId = auth()->user()->company->id;
        $today = Carbon::today();
        $now = Carbon::now();

        $liveStatus = [
            'present' => 0,
            'leave'   => 0,
            'absent'  => 0,
        ];


        switch ($this->statusFilter) {

            case 'month':
                $startDate = $today->copy()->startOfMonth();
                $endDate   = $today->copy()->endOfMonth();
                break;
            case 'year':
                $startDate = $today->copy()->startOfYear();
                $endDate   = $today->copy()->endOfYear();
                break;
            case 'day':
            default:
                $startDate = $today;
                $endDate   = $today;
        }



        $leaveRequests = LeaveRequest::with('leaveType', 'user.employee')->where('company_id', $companyId)->where('status', "pending")
            ->orderBy('created_at', 'desc')
            ->get();


        $payslipRequests = PaySlipRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->get();


        $attendanceRequests = Attendance::with(['requests' => function ($q) {
            $q->where('status', 'pending');
        }])
            ->where('company_id', $companyId)
            ->whereHas('requests', function ($q) {
                $q->where('status', 'pending');
            })
            ->get();




        $shiftDates = ShiftDate::whereDate('date', $today)
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->with(['employees' => function ($q) use ($today) {

                $q->whereDoesntHave('attendances', function ($att) use ($today) {

                    $att->whereDate('clock_in', $today)
                        ->whereDoesntHave('requests', function ($req) {
                            $req->whereIn('status', ['pending', 'rejected']);
                        });
                });
            }])
            ->get();




        $attendanceAnomalies = Attendance::where('company_id', $companyId)
            ->whereDate('created_at', $today)
            ->with(['user', 'requests'])
            ->get()
            ->map(function ($attendance) {


                if (!$attendance->clock_in) {
                    return [
                        'name' => $attendance->user->full_name,
                        'type' => 'Not Clocked In',
                        'badge' => 'danger',
                        'time' => '--',
                    ];
                }

                $lateRequest = $attendance->requests
                    ->where('type', 'late_clock_in')
                    ->whereIn('status', ['pending', 'approved'])
                    ->first();

                if ($lateRequest) {
                    return [
                        'name' => $attendance->user->full_name,
                        'type' => 'Late In',
                        'badge' => 'warning',
                        'time' => \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A'),
                    ];
                }

                $earlyOutRequest = $attendance->requests
                    ->where('type', 'early_clock_out')
                    ->whereIn('status', ['pending', 'approved'])
                    ->first();

                if ($earlyOutRequest) {
                    return [
                        'name' => $attendance->user->full_name,
                        'type' => 'Early Out',
                        'badge' => 'info',
                        'time' => $attendance->clock_out
                            ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A')
                            : '--',
                    ];
                }

                return null;
            })
            ->filter()
            ->take(5);




        $absentEmployees = $shiftDates->pluck('employees')->flatten();
        $todayAbsent = $absentEmployees->count();




        $onLeaveToday = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();

        $totalEmployees = Employee::where('company_id', $companyId)->count();


        // On leave
        $onLeave = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->count();


        $present = Attendance::where('company_id', $companyId)
            ->whereBetween('clock_in', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->whereNotNull('clock_out')
            ->whereDoesntHave('requests', function ($req) {
                $req->whereIn('status', ['pending', 'rejected']);
            })
            ->count();




        $shiftDatesFilterWise = ShiftDate::whereBetween('date', [$startDate, $endDate])
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->with(['employees' => function ($q) use ($startDate, $endDate) {
                $q->whereDoesntHave('attendances', function ($att) use ($startDate, $endDate) {
                    $att->whereBetween('clock_in', [$startDate->startOfDay(), $endDate->endOfDay()])
                        ->whereDoesntHave('requests', function ($req) {
                            $req->whereIn('status', ['pending', 'rejected']);
                        });
                });
            }])
            ->get();


        $absentEmployeesFilterWise = $shiftDatesFilterWise->pluck('employees')->flatten();
        $absentFilterWise = $absentEmployeesFilterWise->count();



        $pendingRequests = LeaveRequest::where('company_id', $companyId)
            ->where('status', 'pending')
            ->count();

        $recentEmployees = Employee::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();


        $empExpiringDocs = EmpDocument::where('company_id', $companyId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $today->copy()->addDays(60)])
            ->with(['employee', 'documentType'])
            ->get();

        $expenses = Expenses::where('company_id', $companyId)
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $recentDocuments = CompanyDocument::where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get();

        $companyExpiringDocs = CompanyDocument::where('company_id', $companyId)
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



        $liveStatus = [
            'present' => $present,
            'leave'   => $onLeave,
            'absent'  => $absentFilterWise,
        ];








        return view('livewire.backend.company.dashboard', [
            'liveStatus' => $liveStatus,
            'expenses' => $expenses,
            'leaveRequests' => $leaveRequests,
            'calendarEvents' => $this->calendarEvents,
            'attendanceAnomalies' => $attendanceAnomalies,
            'recentDocuments' => $recentDocuments,
            'payslipRequests' => $payslipRequests,
            'attendanceRequests' => $attendanceRequests,
            'todayAbsent'     => $todayAbsent,
            'onLeaveToday'    => $onLeaveToday,
            'pendingRequests' => $pendingRequests,
            'recentEmployees' => $recentEmployees,
            'expiringDocs'    => $expiringDocs,
            'totalEmployees'    => $totalEmployees,

        ]);
    }


    public function handleFilter($value)
    {
        $this->statusFilter = $value;
    }
}
