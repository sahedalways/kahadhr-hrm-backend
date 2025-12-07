<?php

namespace App\Livewire\Backend\Company\Leaves;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveSetting;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeavesIndex extends BaseComponent
{
    public $employees;
    public $requestDetails;
    public $calendarLeaveInfo;
    public $leaveRequests;
    public $company;
    public $selectedEmployee;
    public $selectedEmployeeName;
    public $leaveTypes;
    public $search;
    public $other_leave_reason;
    public $leave_type_id, $start_date, $end_date;

    public $selectedEmployeeLeaves = [];
    public $activeEmployeeId = null;
    protected $listeners = ['showLeaveRequestInfo'];




    public function mount()
    {
        $this->company = Auth::user()->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }


        $this->employees = Employee::where('company_id', $this->company->id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();


        $this->leaveRequests = LeaveRequest::with('leaveType', 'user.employee')->where('company_id', $this->company->id)->where('status', "pending")
            ->orderBy('created_at', 'desc')
            ->get();

        $this->leaveTypes = LeaveType::all();
    }



    public function updatedSearch()
    {
        $this->employees = Employee::where('company_id', $this->company->id)
            ->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->search . '%')
                    ->orWhere('l_name', 'like', '%' . $this->search . '%')
                    ->orWhere('job_title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('department', function ($d) {
                        $d->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('f_name')
            ->get();
    }



    public function viewRequestInfo($id)
    {
        $this->requestDetails = null;


        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        $this->requestDetails = LeaveRequest::with('leaveType', 'user.employee')->find($id);
    }



    public function approveRequest($id)
    {
        $request = LeaveRequest::with('user.employee')->find($id);

        if (!$request) return;
        $userId = $request->user_id;
        $hoursUsed = $request->total_hours ?? 0;
        $companyId = $request->user->employee->company_id;



        // 1️⃣ Check before approve
        $availableHours = $this->getAvailableLeaveHours($userId);

        if ($hoursUsed > $availableHours) {
            $request->status = 'rejected';
            $request->save();


            $this->toast('User do not have enough leave hours available!', 'error');
            $this->resetForm();

            $this->mount();
            $this->dispatch('closemodal');
            return;
        }

        $request->status = 'approved';
        $request->save();



        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id' => $userId,
            'company_id' => $companyId,
        ]);

        if (!$leaveBalance->exists) {
            $employee = $request->user->employee;

            if ($employee->salary_type === 'hourly') {
                $contractHours = $employee->contract_hours ?? 0;
                $partTimePercent = config('leave.part_time_percentage', 100);
                $totalHours = $contractHours * 52 * ($partTimePercent / 100);
            } else {
                $totalHours = LeaveSetting::where('company_id', $companyId)
                    ->value('full_time_hours') ?? 0;
            }

            $leaveBalance->total_hours = $totalHours;
        }


        if (($leaveBalance->total_hours - ($leaveBalance->used_hours ?? 0)) > 0) {
            $leaveBalance->used_hours = ($leaveBalance->used_hours ?? 0) + $hoursUsed;
            $leaveBalance->carry_over_hours = max(0, $leaveBalance->total_hours - $leaveBalance->used_hours);
            $leaveBalance->save();
        }


        $this->resetForm();

        $this->mount();


        $this->toast('Request approved successfully!', 'success');
        $this->dispatch('closemodal');
    }




    public function saveRequest()
    {
        // Base validation
        $rules = [
            'selectedEmployee' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ];

        if ($this->leave_type_id && optional($this->leaveTypes->firstWhere('id', $this->leave_type_id))->name === 'Others') {
            $rules['other_leave_reason'] = 'required|string|max:255';
        }

        $this->validate($rules);

        $start = Carbon::parse($this->start_date)->startOfDay();
        $end = Carbon::parse($this->end_date)->startOfDay();

        $totalDays = $start->diffInDays($end, false) + 1;
        $totalDays = abs($totalDays);

        $hoursPerDay = 8;
        $totalHours = $totalDays * $hoursPerDay;



        LeaveRequest::create([
            'user_id'       => $this->selectedEmployee,
            'company_id'       => auth()->user()->company->id,
            'leave_type_id' => $this->leave_type_id,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'total_hours'   => $totalHours,
            'other_reason'  => $this->other_leave_reason,
            'status'        => 'approved',
        ]);




        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id' => $this->selectedEmployee,
            'company_id' => auth()->user()->company->id,
        ]);

        if (!$leaveBalance->exists) {
            $employee = Employee::find($this->selectedEmployee);

            if ($employee && $employee->salary_type === 'hourly') {
                $contractHours = $employee->contract_hours ?? 0;
                $partTimePercent = config('leave.part_time_percentage', 100);
                $totalSettingHours = $contractHours * 52 * ($partTimePercent / 100);
            } else {
                $totalSettingHours = LeaveSetting::where('company_id', auth()->user()->company->id)
                    ->value('full_time_hours') ?? 0;
            }

            $leaveBalance->total_hours = $totalSettingHours;
            $leaveBalance->carry_over_hours = $totalSettingHours;
            $leaveBalance->used_hours = 0;
            $leaveBalance->save();
        }


        if (($leaveBalance->carry_over_hours ?? $leaveBalance->total_hours) > 0) {
            $leaveBalance->used_hours = ($leaveBalance->used_hours ?? 0) + $totalHours;
            $leaveBalance->carry_over_hours = max(0, $leaveBalance->total_hours - $leaveBalance->used_hours);
            $leaveBalance->save();
        }



        $this->toast('Leave has been submitted successfully.', 'success');



        $this->resetForm();
    }



    public function resetForm()
    {
        $this->selectedEmployee = null;
        $this->selectedEmployeeName = null;
        $this->leave_type_id = null;
        $this->other_leave_reason = null;
        $this->requestDetails = null;
        $this->start_date = null;
        $this->calendarLeaveInfo = null;
        $this->end_date = null;
    }





    public function rejectRequest($id)
    {
        $request = LeaveRequest::find($id);
        if ($request) {
            $request->status = 'rejected';
            $request->save();

            $this->resetForm();
            $this->mount();
        }


        $this->toast('Request rejected successfully!', 'info');
        $this->dispatch('closemodal');
    }



    private function getAvailableLeaveHours($userId)
    {
        $leaveBalance = LeaveBalance::where('user_id', $userId)->first();

        return $leaveBalance->carry_over_hours ?? 0;
    }



    public function selectEmployee($id)
    {
        $this->selectedEmployee = $id;
        $emp = Employee::where('user_id', $id)->first();
        $this->selectedEmployeeName = $emp->full_name;
    }



    public function showEmployeeLeave($employeeId)
    {
        $this->activeEmployeeId = $employeeId;

        $this->selectedEmployeeLeaves = LeaveRequest::where('user_id', $employeeId)
            ->where('status', 'approved')
            ->with('leaveType:id,emoji')
            ->get()
            ->map(function ($leave) {
                return [
                    'start' => $leave->start_date,
                    'end' => $leave->end_date,
                    'emoji' => $leave->leaveType->emoji,
                    'id' => $leave->id,
                ];
            })
            ->toArray();

        $this->dispatch('employeeLeaveLoaded', leaves: $this->selectedEmployeeLeaves);
    }



    public function showLeaveRequestInfo($id)
    {
        $this->calendarLeaveInfo = LeaveRequest::with('leaveType', 'user.employee')
            ->where('id', $id)
            ->where('status', 'approved')
            ->first();

        $this->dispatch('show-leave-modal');
    }



    public function render()
    {
        return view('livewire.backend.company.leaves.leaves-index', [
            'employees' => $this->employees,
            'leaveRequests' => $this->leaveRequests,
        ]);
    }
}
