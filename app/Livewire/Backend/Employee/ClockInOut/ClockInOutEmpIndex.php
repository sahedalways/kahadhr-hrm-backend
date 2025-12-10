<?php

namespace App\Livewire\Backend\Employee\ClockInOut;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Traits\Exportable;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClockInOutEmpIndex extends BaseComponent
{
    use WithPagination;
    use Exportable;
    public $search;
    public $sortOrder = 'desc';
    public $perPage = 10;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;
    public $startDate;
    public $endDate;

    protected $listeners = ['reloadAttendance' => 'resetLoaded'];

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.clock-in-out.clock-in-out-emp-index', [
            'infos' => $this->loaded
        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $userId = Auth::id();
        $query = Attendance::where('user_id', $userId);


        if ($this->startDate && $this->endDate) {
            $query->whereBetween('clock_in', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get()
            ->map(function ($attendance) {
                $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';
                return [
                    'id' => $attendance->id,
                    'date' => Carbon::parse($attendance->clock_in, $userTimeZone)->format('D, M d, Y'),
                    'clock_in' => Carbon::parse($attendance->clock_in, $userTimeZone)->format('h:i A'),
                    'clock_out' => $attendance->clock_out
                        ? Carbon::parse($attendance->clock_out, $userTimeZone)->format('h:i A')
                        : 'N/A',
                    'location' => $attendance->clock_in_location ?? 'Unknown Location',
                    'status' => $attendance->status,
                ];
            });

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()['id'] : $items->first()['id'];

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



    public function handleStartDate($value)
    {
        $this->startDate = $value;
        $this->resetLoaded();
    }

    public function handleEndDate($value)
    {
        $this->endDate = $value;
        $this->resetLoaded();
    }

    public function resetFilters()
    {
        $this->startDate = null;
        $this->endDate = null;


        $this->lastId = null;
        $this->hasMore = true;
        $this->loaded = collect();


        $this->loadMore();
    }



    public function exportAttendance($type)
    {

        $data = $this->loaded->map(function ($attendance) {

            $request = null;
            if (!empty($attendance['needs_approval']) && $attendance['needs_approval']) {
                $request = AttendanceRequest::where('attendance_id', $attendance['id'])
                    ->where('status', 'pending')
                    ->first();
            }

            return [
                'id' => $attendance['id'],
                'date' => $attendance['date'],
                'clock_in' => $attendance['clock_in'],
                'clock_out' => $attendance['clock_out'],
                'location' => $attendance['location'] ?? 'Unknown',
                'status' => ucfirst($attendance['status']),
                'request_type' => $request->type ?? 'N/A',
                'reason' => $request->reason ?? 'N/A',
            ];
        });

        // Columns for export file
        $columns = [
            'ID',
            'Date',
            'Clock In',
            'Clock Out',
            'Location',
            'Status',
            'Request Type',
            'Reason',
        ];

        // Keys as per dataset
        $keys = [
            'id',
            'date',
            'clock_in',
            'clock_out',
            'location',
            'status',
            'request_type',
            'reason',
        ];

        return $this->export(
            $data,
            $type,
            'employee-attendance-records',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Employee Attendance',
                'columns' => $columns,
                'keys' => $keys,
            ]
        );
    }
}
