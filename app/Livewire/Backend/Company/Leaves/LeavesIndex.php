<?php

namespace App\Livewire\Backend\Company\Leaves;

use App\Events\NotificationEvent;
use App\Helpers\LeaveHelper;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;


class LeavesIndex extends BaseComponent
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;
    public $openLeaveId = null;
    public $requestDetails;
    public $calendarLeaveInfo;
    public $leaveRequests;
    public $company;
    public $selectedEmployee;
    public $selectedEmployeeName;
    public $leaveTypes;
    public $search = '';
    public $other_leave_reason;
    public $leave_type_id, $start_date, $end_date;

    public $selectedEmployeeLeaves = [];
    public $activeEmployeeId = null;
    public $paidStatus = null;
    public $paidHours = null;

    public $editStartDate;
    public $editEndDate;
    public $editLeaveId;
    public $filterEmployeeId = null;

    protected $listeners = ['showLeaveRequestInfo'];

    public $selectedYear;
    public $yearlyLeaves = [];
    public $selectedEmployeeForYear;

    public $approvedLeavesCollection;

    public $totalAnnualHours = 0;
    public $usedAnnualHours = 0;
    public $remainingAnnualHours = 0;
    public $totalLeaveInLiewHours = 0;
    public $usedLeaveInLiewHours = 0;
    public $remainingLeaveInLiewHours = 0;

    public $currentYear;
    public $currentMonth;

    public $dates = [];

    public function mount()
    {
        $this->company = Auth::user()->company;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }


        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;

        $this->loadDates();

        if (request()->has('leave')) {
            $this->openLeaveId = request('leave');
            $this->viewRequestInfo($this->openLeaveId);
        }

        $this->loadApprovedLeaves();
        $this->loadPendingRequests();

        $this->leaveTypes = LeaveType::all();
        $this->selectedYear = now()->year;
        $this->selectedEmployeeForYear = null;
    }


    public function refreshData()
    {
        $this->loadApprovedLeaves();
        $this->loadPendingRequests();

        if ($this->filterEmployeeId) {
            $this->calculateLeaveHours($this->filterEmployeeId);
            $this->loadYearlyLeaveData($this->filterEmployeeId);
        }
    }

    public function loadDates()
    {
        // Make sure currentYear and currentMonth are set
        if (!$this->currentYear || !$this->currentMonth) {
            $this->currentYear = now()->year;
            $this->currentMonth = now()->month;
        }

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        $this->dates = [];
        $currentDate = $startOfMonth->copy();

        while ($currentDate <= $endOfMonth) {
            $this->dates[] = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->day,
                'letter' => $currentDate->format('D'),
                'is_weekend' => in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
            ];
            $currentDate->addDay();
        }
    }

    private function calculateLeaveHours($employeeId)
    {
        $balanceData = LeaveHelper::getLeaveBalanceData($employeeId);

        $this->totalAnnualHours = $balanceData['total_annual_hours'];
        $this->usedAnnualHours = $balanceData['used_annual_hours'];
        $this->remainingAnnualHours = $balanceData['remaining_annual_hours'];
        $this->totalLeaveInLiewHours = $balanceData['total_leave_in_liew_hours'];
        $this->usedLeaveInLiewHours = $balanceData['used_leave_in_liew_hours'];
        $this->remainingLeaveInLiewHours = $balanceData['remaining_leave_in_liew_hours'];
    }

    private function resetLeaveHours()
    {
        $this->totalAnnualHours = 0;
        $this->usedAnnualHours = 0;
        $this->remainingAnnualHours = 0;
        $this->totalLeaveInLiewHours = 0;
        $this->usedLeaveInLiewHours = 0;
        $this->remainingLeaveInLiewHours = 0;
    }

    /**
     * Load approved leaves for the current selected month/year
     */
    public function loadApprovedLeaves()
    {
        // Make sure currentYear and currentMonth are set
        if (!$this->currentYear || !$this->currentMonth) {
            $this->currentYear = now()->year;
            $this->currentMonth = now()->month;
        }

        $startOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->currentYear, $this->currentMonth, 1)->endOfMonth();

        $this->approvedLeavesCollection = LeaveRequest::with('leaveType', 'user.employee')
            ->where('company_id', $this->company->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                        $q->where('start_date', '<', $startOfMonth)
                            ->where('end_date', '>', $endOfMonth);
                    });
            })
            ->get();
    }

    public function loadPendingRequests()
    {
        $this->leaveRequests = LeaveRequest::with('leaveType', 'user.employee')
            ->where('company_id', $this->company->id)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function changeMonth($direction)
    {
        if ($direction === 'prev') {
            $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        } else {
            $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        }

        $this->currentYear = $date->year;
        $this->currentMonth = $date->month;


        // $this->resetPage();

        $this->loadDates();
        $this->loadApprovedLeaves();



        if ($this->filterEmployeeId) {
            $this->calculateLeaveHours($this->filterEmployeeId);
            $this->loadYearlyLeaveData($this->filterEmployeeId);
        }
    }



    /**
     * Get paginated employees with their leaves
     */
    public function getEmployeesProperty()
    {
        $query = Employee::where('company_id', $this->company->id)
            ->whereNotNull('user_id')
            ->orderBy('f_name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->search . '%')
                    ->orWhere('l_name', 'like', '%' . $this->search . '%');
            });
        }

        $employees = $query->paginate($this->perPage);

        // Attach leaves to employees from approvedLeavesCollection
        if ($this->approvedLeavesCollection && $this->approvedLeavesCollection->count() > 0) {
            $employees->getCollection()->transform(function ($emp) {
                $emp->leaves = $this->approvedLeavesCollection->where('user_id', $emp->user_id);
                return $emp;
            });
        } else {
            $employees->getCollection()->transform(function ($emp) {
                $emp->leaves = collect();
                return $emp;
            });
        }

        return $employees;
    }

    /**
     * Load more employees
     */
    public function loadMore()
    {
        $this->perPage += 10;
    }

    /**
     * Reset pagination when searching
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filtering by employee
     */
    public function filterByEmployee($employeeId)
    {
        if ($this->filterEmployeeId === $employeeId) {
            $this->filterEmployeeId = null;
            $this->selectedEmployeeForYear = null;
            $this->yearlyLeaves = collect();
            $this->resetPage();
            $this->resetLeaveHours();
            return;
        }

        $this->filterEmployeeId = $employeeId;
        $this->selectedYear = now()->year;
        $this->loadYearlyLeaveData($employeeId);
        $this->resetPage();
        $this->calculateLeaveHours($employeeId);
    }


    public function changeYear($direction)
    {
        if ($direction === 'prev') {
            $this->selectedYear--;
        } elseif ($direction === 'next') {
            $this->selectedYear++;
        }

        if ($this->selectedEmployeeForYear) {
            $this->loadYearlyLeaveData($this->selectedEmployeeForYear);
        }
    }

    public function loadYearlyLeaveData($employeeId)
    {
        $this->selectedEmployeeForYear = $employeeId;

        $this->yearlyLeaves = LeaveRequest::where('user_id', $employeeId)
            ->where('status', 'approved')
            ->where(function ($query) {
                $query->whereYear('start_date', $this->selectedYear)
                    ->orWhereYear('end_date', $this->selectedYear);
            })
            ->with('leaveType')
            ->get()
            ->map(function ($leave) {
                return [
                    'start' => $leave->start_date,
                    'end' => $leave->end_date,
                    'type' => $leave->leaveType->name,
                    'emoji' => $leave->leaveType->emoji,
                    'color' => $leave->leaveType->color,
                ];
            });
    }

    public function backToMonthlyView()
    {
        $this->dispatch('reload-page');
    }


    public function viewRequestInfo($id)
    {
        $this->requestDetails = null;
        $this->paidStatus = null;
        $this->paidHours = null;

        if (!$this->company) {
            abort(403, 'Company not found.');
        }

        $this->requestDetails = LeaveRequest::with('leaveType', 'user.employee')->find($id);
        $this->setPaidStatusDefault();

        $this->dispatch('show-leave-modal-for-status-change');
    }





    public function approveRequest($id)
    {
        $request = LeaveRequest::with('user.employee')->find($id);
        if (!$request) return;

        $userId = $request->user_id;
        $hoursUsed = $request->total_hours ?? 0;
        $companyId = $request->user->employee->company_id;


        if (in_array($request->leave_type_id, [1, 5])) {
            $this->paidHours = $hoursUsed;
            $this->paidStatus = 'paid';
            $availableHours = $this->getAvailableLeaveHours($userId, $request->leave_type_id);

            if ($hoursUsed > $availableHours) {
                $this->toast('Employee does not have enough leave hours available!', 'error');
                return;
            }
        }



        if (in_array($request->leave_type_id, [2, 3, 4, 6])) {
            if ($request->leave_type_id != 4) {
                if (!$this->paidStatus || !in_array($this->paidStatus, ['paid', 'unpaid'])) {
                    $this->toast('Please select Paid or Unpaid status.', 'error');
                    return;
                }

                if ($this->paidStatus === 'paid' && (!$this->paidHours || $this->paidHours <= 0)) {
                    $this->toast('Please enter valid hours for Paid leave.', 'error');
                    return;
                }
            } else {
                $this->paidStatus = 'unpaid';
                $this->paidHours = null;
            }
        }


        $request->update([
            'status'      => 'approved',
            'paid_status' => $this->paidStatus ?? null,
            'paid_hours'  => $this->paidStatus === 'paid' ? $this->paidHours : null,
            'remaining_annual_hours' => ($leaveBalance->total_annual_hours ?? 0) - ($leaveBalance->used_annual_hours ?? 0),
        ]);



        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id' => $userId,
            'company_id' => $companyId,
        ]);

        if ($request->leave_type_id == 1) {

            $leaveBalance->used_annual_hours = ($leaveBalance->used_annual_hours ?? 0) + $hoursUsed;
            $leaveBalance->carry_over_hours = max(0, ($leaveBalance->total_annual_hours ?? 0) - $leaveBalance->used_annual_hours);
        }

        if ($request->leave_type_id == 5) {

            $leaveBalance->used_leave_in_liew = ($leaveBalance->used_leave_in_liew ?? 0) + $hoursUsed;
        }




        $leaveTypeName = optional($request->leaveType)->name ?? 'Leave';
        $message = "{$leaveTypeName} request approved for you.";

        $notification = Notification::create([
            'company_id' => $companyId,
            'user_id'    => $userId,
            'type'       => 'leave_request_approved',
            'notifiable_id' => $id,
            'data'       => [
                'message' => $message
            ],
        ]);

        // Fire real-time event
        event(new NotificationEvent($notification));


        $leaveBalance->save();

        $request->update([
            'remaining_annual_hours' => ($leaveBalance->total_annual_hours ?? 0) - ($leaveBalance->used_annual_hours ?? 0),
        ]);


        $this->dispatch('closemodal');


        $this->toast('Request approved successfully!', 'success');

        $this->refreshData();
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


        if (in_array($this->leave_type_id, [2, 3, 6])) {
            $this->validate([
                'paidStatus' => 'required|in:paid,unpaid',
                'paidHours' => $this->paidStatus === 'paid' ? 'required|numeric|min:0.5' : 'nullable',
            ]);
        }



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



        if (in_array($this->leave_type_id, [1, 5])) {
            $this->paidHours = $totalHours;
            $this->paidStatus = 'paid';

            $availableHours = $this->getAvailableLeaveHours($this->selectedEmployee, $this->leave_type_id);

            if ($totalHours > $availableHours) {
                $this->toast('Employee does not have enough leave hours available!', 'error');

                return;
            }
        }



        $request =  LeaveRequest::create([
            'user_id'       => $this->selectedEmployee,
            'company_id'       => auth()->user()->company->id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date !== '' ? $this->start_date : null,
            'end_date'   => $this->end_date !== '' ? $this->end_date : null,
            'total_hours'   => $totalHours,
            'paid_hours'   => $this->paidHours ?? 0,
            'paid_status'   => $this->paidStatus ?? null,
            'other_reason'  => $this->other_leave_reason,
            'status'        => 'approved',
        ]);




        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id' => $this->selectedEmployee,
            'company_id' => auth()->user()->company->id,
        ]);



        if ($request->leave_type_id == 1) {
            $leaveBalance->used_annual_hours = ($leaveBalance->used_annual_hours ?? 0) + $totalHours;
            $leaveBalance->carry_over_hours = max(0, ($leaveBalance->total_annual_hours ?? 0) - $leaveBalance->used_annual_hours);
        }

        if ($request->leave_type_id == 5) {
            $leaveBalance->used_leave_in_liew = ($leaveBalance->used_leave_in_liew ?? 0) + $totalHours;
        }


        $leaveBalance->save();


        $leaveTypeName = optional($request->leaveType)->name ?? 'Leave';
        $message = "{$leaveTypeName} request approved for you.";

        // ✅ Notification
        $leaveTypeName = optional($request->leaveType)->name ?? 'Leave';

        $message = "{$leaveTypeName} approved by company for you.";

        $notification = Notification::create([
            'company_id' => auth()->user()->company->id,
            'user_id'    => $this->selectedEmployee,
            'notifiable_id' => $request->id,
            'type'       => 'manual_leave_approved',
            'data'       => [
                'message' => $message
            ],
        ]);


        $request->update([
            'remaining_annual_hours' => ($leaveBalance->total_annual_hours ?? 0) - ($leaveBalance->used_annual_hours ?? 0),
        ]);


        // Fire real-time event
        event(new NotificationEvent($notification));

        $this->toast('Leave has been submitted successfully.', 'success');

        $this->resetForm();

        $this->refreshData();
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
        $this->paidStatus = null;
        $this->paidHours = null;
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

        $leaveTypeName = optional($request->leaveType)->name ?? 'Leave';


        $message = "{$leaveTypeName} request has been rejected.";

        $notification = Notification::create([
            'company_id' => auth()->user()->company->id,
            'user_id'    => $request->user_id,
            'notifiable_id' => $id,
            'type'       => 'leave_request_rejected',
            'data'       => [
                'message' => $message
            ],
        ]);

        // Fire real-time event
        event(new NotificationEvent($notification));


        $this->toast('Request rejected successfully!', 'info');
        $this->dispatch('closemodal');
    }



    private function getAvailableLeaveHours($userId, $leaveTypeId)
    {
        $leaveBalance = LeaveBalance::where('user_id', $userId)->first();

        if (!$leaveBalance) {
            return 0;
        }

        if ($leaveTypeId == 1) {
            return $leaveBalance->carry_over_hours ?? 0;
        } elseif ($leaveTypeId == 5) {
            return ($leaveBalance->total_leave_in_liew - $leaveBalance->used_leave_in_liew) ?? 0;
        }


        return 0;
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
        $this->paidStatus = null;
        $this->paidHours = null;


        $this->calendarLeaveInfo = LeaveRequest::with('leaveType', 'user.employee')
            ->where('id', $id)
            ->where('status', 'approved')
            ->first();

        $this->dispatch('show-leave-modal');
    }


    private function setPaidStatusDefault()
    {
        if ($this->requestDetails) {
            if ($this->requestDetails->leave_type_id == 4) {
                $this->paidStatus = 'unpaid';
            } else {
                $this->paidStatus = null;
            }

            $this->paidHours = null;
        }
    }



    public function cancelLeave($id)
    {
        $leave = LeaveRequest::with('user.employee')->findOrFail($id);


        $userId = $leave->user_id;
        $companyId = $leave->user->employee->company_id;
        $hoursUsed = $leave->total_hours ?? 0;


        if ($leave->status === 'approved') {
            $leaveBalance = LeaveBalance::firstOrNew([
                'user_id' => $userId,
                'company_id' => $companyId,
            ]);

            if ($leave->leave_type_id == 1) {
                $leaveBalance->used_annual_hours = max(0, ($leaveBalance->used_annual_hours ?? 0) - $hoursUsed);
                $leaveBalance->carry_over_hours = max(0, ($leaveBalance->total_annual_hours ?? 0) - $leaveBalance->used_annual_hours);
            }

            if ($leave->leave_type_id == 5) {
                $leaveBalance->used_leave_in_liew = max(0, ($leaveBalance->used_leave_in_liew ?? 0) - $hoursUsed);
            }

            $leaveBalance->save();
        }


        $leave->update([
            'status' => 'cancelled',
            'paid_status' => null,
            'paid_hours' => null,
        ]);

        $this->resetForm();


        $this->dispatch('closemodal');

        $this->toast('Leave cancelled successfully!', 'success');

        $this->refreshData();
    }



    public function editLeave($id)
    {

        $leave = LeaveRequest::findOrFail($id);

        $this->editLeaveId = $id;


        $this->editStartDate = $leave->start_date;
        $this->editEndDate = $leave->end_date;


        $this->dispatch('show-edit-leave-modal');
    }


    public function updateLeave($id)
    {
        $leave = LeaveRequest::findOrFail($id);

        // Validate dates
        $this->validate([
            'editStartDate' => 'required|date|after_or_equal:today',
            'editEndDate' => 'required|date|after_or_equal:editStartDate',
        ]);


        $leaveBalance = LeaveBalance::firstOrNew([
            'user_id' => $leave->user_id,
            'company_id' => $leave->user->employee->company_id,
        ]);

        $hoursUsed = $leave->total_hours ?? 0;

        if ($leave->leave_type_id == 1) {
            $leaveBalance->used_annual_hours = max(0, ($leaveBalance->used_annual_hours ?? 0) - $hoursUsed);
            $leaveBalance->carry_over_hours = max(0, ($leaveBalance->total_annual_hours ?? 0) - $leaveBalance->used_annual_hours);
        }

        if ($leave->leave_type_id == 5) {
            $leaveBalance->used_leave_in_liew = max(0, ($leaveBalance->used_leave_in_liew ?? 0) - $hoursUsed);
        }

        $leaveBalance->save();


        $start = Carbon::parse($this->editStartDate)->startOfDay();
        $end = Carbon::parse($this->editEndDate)->startOfDay();

        $totalDays = $start->diffInDays($end, false) + 1;
        $totalDays = abs($totalDays);

        $hoursPerDay = 8;
        $totalHours = $totalDays * $hoursPerDay;


        if (in_array($leave->leave_type_id, [1, 5])) {
            $this->paidHours = $totalHours;
            $this->paidStatus = 'paid';

            $availableHours = $this->getAvailableLeaveHours($leave->user_id, $leave->leave_type_id);
            if ($totalHours > $availableHours) {
                $this->toast('Employee does not have enough leave hours available!', 'error');
                return;
            }
        }

        // 4️⃣ Update leave record
        $leave->update([
            'start_date'  => $this->editStartDate,
            'end_date'    => $this->editEndDate,
            'total_hours' => $totalHours,
            'paid_hours'  => $this->paidHours ?? 0,
            'paid_status' => $this->paidStatus ?? null,
        ]);


        if ($leave->leave_type_id == 1) {
            $leaveBalance->used_annual_hours = ($leaveBalance->used_annual_hours ?? 0) + $totalHours;
            $leaveBalance->carry_over_hours = max(0, ($leaveBalance->total_annual_hours ?? 0) - $leaveBalance->used_annual_hours);
        }

        if ($leave->leave_type_id == 5) { // Leave in Lieu
            $leaveBalance->used_leave_in_liew = ($leaveBalance->used_leave_in_liew ?? 0) + $totalHours;
        }

        $leaveBalance->save();

        $leave->refresh();

        $this->dispatch('closemodal');



        $this->toast('Leave updated successfully!', 'success');

        $this->refreshData();
    }

    public function render()
    {

        if (empty($this->dates) || count($this->dates) == 0) {
            $this->loadDates();
        }


        $this->loadDates();
        $this->loadApprovedLeaves();

        $employees = $this->getEmployeesProperty();

        return view('livewire.backend.company.leaves.leaves-index', [
            'employees' => $employees,
            'leaveRequests' => $this->leaveRequests,
            'approvedLeavesCollection' => $this->approvedLeavesCollection,
            'currentYear' => $this->currentYear,
            'currentMonth' => $this->currentMonth,
            'dates' => $this->dates,
            'filterEmployeeId' => $this->filterEmployeeId,
        ]);
    }
}
