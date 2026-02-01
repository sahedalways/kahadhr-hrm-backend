<?php

namespace App\Livewire\Backend\Company;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Models\PaySlipRequest;
use App\Models\ShiftDate;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $statusFilter = 'day';

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
            ->take(3)
            ->get();


        $expiringDocs = EmpDocument::where('company_id', $companyId)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$today, $today->copy()->addDays(60)])
            ->with(['employee', 'documentType'])
            ->get();


        $liveStatus = [
            'present' => $present,
            'leave'   => $onLeave,
            'absent'  => $absentFilterWise,
        ];



        return view('livewire.backend.company.dashboard', [
            'liveStatus' => $liveStatus,
            'leaveRequests' => $leaveRequests,
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
