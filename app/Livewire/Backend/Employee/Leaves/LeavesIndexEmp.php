<?php

namespace App\Livewire\Backend\Employee\Leaves;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\LeaveSetting;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;

class LeavesIndexEmp extends BaseComponent
{
    use WithPagination;
    use Exportable;

    public $leave_type_id, $start_date, $end_date;
    public $search;
    public $sortOrder = 'desc';
    public $perPage = 10;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $leaveTypes;
    public $entitlementHours;
    public $usedHours;
    public $remainingHours;
    public $other_leave_reason;

    protected $listeners = ['deleteLeaveRequest'];

    public function mount()
    {
        $this->leaveTypes = LeaveType::all();
        $this->loaded = collect();
        $this->calculateLeaveHours();
        $this->loadMore();
    }


    public function render()
    {
        return view('livewire.backend.employee.leaves.leaves-index-emp', [
            'leaveRequests' => $this->loaded
        ]);
    }

    public function resetInputFields()
    {
        $this->leave_type_id = $this->start_date = $this->end_date = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        // Base validation
        $rules = [
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


        if ($totalHours > $this->remainingHours) {
            $this->toast('You do not have enough leave hours.', 'error');
            return;
        }

        // Create leave request
        LeaveRequest::create([
            'user_id'       => auth()->id(),
            'company_id'       => auth()->user()->employee->company_id,
            'leave_type_id' => $this->leave_type_id,
            'start_date'    => $this->start_date,
            'end_date'      => $this->end_date,
            'total_hours'   => $totalHours,
            'other_reason'  => $this->other_leave_reason,
            'status'        => 'pending',
        ]);

        $this->toast('Leave request submitted successfully!', 'success');


        $this->dispatch('closemodal');
        $this->resetInputFields();


        $this->calculateLeaveHours();


        $this->resetLoaded();
    }


    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = LeaveRequest::with('leaveType')->where('user_id', auth()->id());

        if ($this->search && $this->search != '') {
            $query->whereHas('leaveType', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)->limit($this->perPage)->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    private function calculateLeaveHours()
    {
        $user = auth()->user();


        $leaveBalance = LeaveBalance::where('user_id', $user->id)->first();

        $this->remainingHours = $leaveBalance->carry_over_hours;
        $this->entitlementHours = $leaveBalance->total_hours;
        $this->usedHours = $leaveBalance->used_hours;
    }




    public function exportLeaveEmp($type)
    {
        // Map the loaded leave requests
        $data = $this->loaded->map(function ($leave) {
            return [
                'id' => $leave->id,
                'leave_type' => $leave->leaveType->name ?? 'N/A',
                'start_date' => Carbon::parse($leave->start_date)->format('d M, Y'),
                'end_date' => Carbon::parse($leave->end_date)->format('d M, Y'),
                'total_hours' => number_format($leave->total_hours, 2),
                'status' => ucfirst($leave->status),
                'created_at' => $leave->created_at ? Carbon::parse($leave->created_at)->format('d F, Y') : 'N/A',
                'updated_at' => $leave->updated_at ? Carbon::parse($leave->updated_at)->format('d F, Y') : 'N/A',
            ];
        });

        // Columns for export file
        $columns = [
            'ID',
            'Leave Type',
            'Start Date',
            'End Date',
            'Total Hours',
            'Status',
            'Created At',
            'Updated At',
        ];

        // Keys as per dataset
        $keys = [
            'id',
            'leave_type',
            'start_date',
            'end_date',
            'total_hours',
            'status',
            'created_at',
            'updated_at',
        ];

        return $this->export(
            $data,
            $type,
            'employee-leave-requests',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Employee Leave Requests',
                'columns' => $columns,
                'keys' => $keys,
            ]
        );
    }
}
