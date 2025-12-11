<?php

namespace App\Livewire\Backend\Company\Timesheet;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Employee;
use Livewire\WithPagination;

class TimesheetIndex extends BaseComponent
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';
    public $employeeId = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';
    public $sortOrder = 'desc';

    public $loaded;
    public $hasMore = true;
    public $lastId = null;
    public $expandedRequest = null;


    public $manualDate;
    public $clockInTime;
    public $clockOutTime;
    public $reason;

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.timesheet.timesheet-index', [
            'records' => $this->loaded,
            'employees' => Employee::where('company_id', auth()->user()->company->id)
                ->whereNotNull('user_id')
                ->get(),

        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }
    public function updatedEmployeeId()
    {
        $this->resetLoaded();
    }
    public function updatedDateFrom()
    {
        $this->resetLoaded();
    }
    public function updatedDateTo()
    {
        $this->resetLoaded();
    }
    public function updatedStatusFilter()
    {
        $this->resetLoaded();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Attendance::with(['requests' => function ($q) {
            $q->where('status', 'pending');
        }])
            ->where('company_id', auth()->user()->company->id)
            ->whereHas('requests', function ($q) {
                $q->where('status', 'pending');
            });

        // SEARCH by employee name
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            });
        }

        // EMPLOYEE FILTER
        if ($this->employeeId) {
            $query->where('user_id', $this->employeeId);
        }

        // DATE RANGE
        if ($this->dateFrom) {
            $query->whereDate('clock_in', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('clock_in', '<=', $this->dateTo);
        }

        // STATUS FILTER
        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        // INFINITE SCROLL PAGINATION
        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() === 0) {
            $this->hasMore = false;
            return;
        }

        // Update lastId for infinite scroll
        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        // Merge into loaded collection
        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }



    public function approveRequest($requestId)
    {
        $request = AttendanceRequest::find($requestId);

        if (!$request) {
            return;
        }

        $request->status = 'approved';

        $request->save();


        $attendance = $request->attendance;
        if ($attendance) {
            $attendance->status = 'approved';
            $attendance->save();
        }


        $this->toast('Request approved successfully!', 'success');


        $this->resetLoaded();
    }


    public function rejectRequest($requestId)
    {
        $request = AttendanceRequest::find($requestId);

        if (!$request) {
            return;
        }

        // Reject the request
        $request->status = 'rejected';

        $request->save();

        $attendance = $request->attendance;
        if ($attendance) {
            $attendance->status = 'rejected';
            $attendance->save();
        }


        $this->toast('Request rejected successfully!', 'error');
        $this->resetLoaded();
    }


    public function toggleReason($requestId)
    {
        if ($this->expandedRequest === $requestId) {
            $this->expandedRequest = null;
        } else {
            $this->expandedRequest = $requestId;
        }
    }


    public function submitManualEntry()
    {
        $this->validate([
            'employeeId' => 'required|exists:users,id',
            'manualDate' => 'required|date',
            'clockInTime' => 'required|date_format:H:i',
            'clockOutTime' => 'nullable|date_format:H:i|after:clockInTime',
        ]);

        $clockIn = $this->manualDate . ' ' . $this->clockInTime;
        $clockOut = $this->clockOutTime ? $this->manualDate . ' ' . $this->clockOutTime : null;

        Attendance::where('user_id', $this->employeeId)
            ->whereDate('clock_in', $this->manualDate)
            ->delete();


        $attendance = Attendance::create([
            'user_id' => $this->employeeId,
            'company_id' => auth()->user()->company->id,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'clock_in_location' => 'Manual Entry',
            'clock_out_location' => $clockOut ? 'Manual Entry' : null,
            'is_manual' => 1,
            'needs_approval' => 0,
            'status' => 'approved',
        ]);

        $this->toast('Manual entry submitted successfully!', 'success');

        $this->reset(['employeeId', 'manualDate', 'clockInTime', 'clockOutTime', 'reason']);

        $this->dispatch('closemodal');
    }




    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }
}
