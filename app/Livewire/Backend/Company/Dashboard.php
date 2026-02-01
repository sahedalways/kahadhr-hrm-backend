<?php

namespace App\Livewire\Backend\Company;

use Livewire\Component;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\EmpDocument;
use App\Models\Employee;
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
            ->whereBetween('clock_in', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->count();




        $absent = $totalEmployees - ($present + $onLeave);

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
            'absent'  => $absent,
        ];



        return view('livewire.backend.company.dashboard', [
            'liveStatus' => $liveStatus,
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
