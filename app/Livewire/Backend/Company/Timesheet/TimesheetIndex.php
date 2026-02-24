<?php

namespace App\Livewire\Backend\Company\Timesheet;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\ShiftDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class TimesheetIndex extends BaseComponent
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';
    public $showEmployeeFilter = false;

    public $employeeId;
    public $filterUsers = [];
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
    public $viewMode = 'weekly';
    public $weeks = [];

    public $startDate;
    public $endDate;
    public $currentDate;
    public $employees;
    public $company_id;

    public $employeeSearch;

    public $currentAttendance = [];
    public $selectedDateAttendances = [];
    public $attendanceCalendar = [];

    public $shiftMap = [];

    public $selectedAttendance = null;
    public $selectedDate = null;

    public $absentDetails = [];


    public $totalLeaves = 0;
    public $totalPending = 0;
    public $totalApproved = 0;
    public $totalRejected = 0;
    public $totalHours = '0h 0m';
    public $absentDate;
    public $totalAbsents;
    public bool $selectAllUsers = false;

    public $requestDetails;
    public $highlightId;

    public $editingClockIn = false;
    public $editingClockOut = false;

    public function toggleClockInEdit()
    {
        $this->editingClockIn = !$this->editingClockIn;
    }

    public function toggleClockOutEdit()
    {
        $this->editingClockOut = !$this->editingClockOut;
    }


    public function updateClockIn()
    {
        if (!$this->selectedAttendance) return;

        $this->selectedAttendance->update([
            'clock_in' => Carbon::parse($this->clockInTime),
        ]);

        $this->buildAttendanceCalendar();
        $this->editingClockIn = false;

        $this->toast('Clock In updated!', 'success');
    }

    public function updateClockOut()
    {
        if (!$this->selectedAttendance) return;

        $this->selectedAttendance->update([
            'clock_out' => $this->clockOutTime ? Carbon::parse($this->clockOutTime) : null,
        ]);

        $this->buildAttendanceCalendar();
        $this->editingClockOut = false;

        $this->toast('Clock Out updated!', 'success');
    }




    public function updatedSelectAllUsers($value)
    {
        if ($value) {
            $this->filterUsers = $this->employees
                ->pluck('user_id')
                ->toArray();
        } else {
            $this->filterUsers = [];
        }

        $this->resetLoaded();
    }


    public function updatedFilterUsers()
    {
        $this->selectAllUsers =
            count($this->filterUsers) === $this->employees->count();
        $this->resetLoaded();
    }




    public function openAttendanceModal($attendanceId)
    {
        $this->selectedAttendance = Attendance::with(['user', 'requests'])
            ->where('company_id', $this->company_id)
            ->find($attendanceId);

        if ($this->selectedAttendance) {
            $this->clockInTime = Carbon::parse($this->selectedAttendance->clock_in)->format('H:i');
            $this->clockOutTime = $this->selectedAttendance->clock_out
                ? Carbon::parse($this->selectedAttendance->clock_out)->format('H:i')
                : null;
        }

        $this->dispatch('open-attendance-modal');
    }




    public function updatedEmployeeSearch()
    {
        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->when($this->employeeSearch, function ($query) {
                $query->where(function ($q) {
                    $q->where('f_name', 'like', '%' . $this->employeeSearch . '%')
                        ->orWhere('l_name', 'like', '%' . $this->employeeSearch . '%')
                        ->orWhereRaw("CONCAT(f_name, ' ', l_name) LIKE ?", ['%' . $this->employeeSearch . '%']);
                });
            })
            ->orderBy('f_name')
            ->get();
    }




    public function setViewMode($mode)
    {
        $this->viewMode = $mode;

        if ($mode === 'weekly') {

            $this->startDate = now()->startOfDay();
            $this->endDate = now()->addDays(6)->endOfDay();
        } elseif ($mode === 'monthly') {
            $this->startDate = now()->startOfMonth();
            $this->endDate = now()->endOfMonth();
        }
        $this->buildAttendanceCalendar();
        $this->loadEmployees();
    }


    public function goToPrevious()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate->subWeek();
            $this->endDate->subWeek();
            $this->currentDate->subWeek();
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate->subMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->currentDate->subMonth();
        }
        $this->buildAttendanceCalendar();
    }

    public function goToNext()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate->addWeek();
            $this->endDate->addWeek();
            $this->currentDate->addWeek();
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate->addMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->currentDate->addMonth();
        }
        $this->buildAttendanceCalendar();
    }

    public function getDisplayDateRangeProperty()
    {
        if ($this->viewMode === 'weekly') {
            return $this->startDate->format('M d') . ' - ' . $this->endDate->format('M d');
        } elseif ($this->viewMode === 'monthly') {
            return $this->startDate->format('F Y');
        }
    }


    public function getFilteredEmployeesProperty()
    {
        return $this->employees->filter(function ($employee) {
            if (!$this->search) return true;
            $name = strtolower($employee->f_name . ' ' . $employee->l_name);
            return str_contains($name, strtolower($this->search));
        });
    }


    public function deleteAttendance($attendanceId)
    {
        $attendance = Attendance::find($attendanceId);

        if (!$attendance) {
            $this->toast('Attendance not found.', 'error');
            return;
        }


        AttendanceRequest::where('attendance_id', $attendanceId)->delete();

        $attendance->delete();

        $this->toast('Attendance deleted successfully!', 'success');


        $this->selectedAttendance = null;
        $this->buildAttendanceCalendar();
        $this->resetLoaded();

        $this->dispatch('closemodal');
    }


    public function getShiftHours($attendance)
    {
        $clockIn = $attendance->clock_in instanceof Carbon ? $attendance->clock_in : Carbon::parse($attendance->clock_in);
        $clockOut = $attendance->clock_out instanceof Carbon ? $attendance->clock_out : Carbon::parse($attendance->clock_out);

        if (!$clockIn) {
            return [
                'shift_hours'  => '8h 0m',
                'worked_hours' => '---',
                'break_hours'  => '0h 0m'
            ];
        }

        $date = $clockIn->format('Y-m-d');
        $employeeId = $attendance->user->employee->id;

        $shiftDate = ShiftDate::where('date', $date)
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
            ->with('breaks')
            ->first();

        $breakMinutes = 0;
        if ($shiftDate) {
            foreach ($shiftDate->breaks as $b) {
                $duration = $b->duration ?? 0;
                $hoursPart = floor($duration);
                $minutesPart = round(($duration - $hoursPart) * 100);
                $breakMinutes += $hoursPart * 60 + $minutesPart;
            }
        }

        if (!$clockOut) {
            return [
                'shift_hours'  => '8h 0m',
                'worked_hours' => '0h 0m',
                'break_hours'  => sprintf('%dh %dm', intdiv($breakMinutes, 60), $breakMinutes % 60)
            ];
        }

        if ($clockOut->lessThan($clockIn)) {
            $clockOut->addDay();
        }

        $workedMinutes = $clockIn->diffInMinutes($clockOut);

        return [
            'shift_hours'  => '8h 0m',
            'worked_hours' => sprintf('%dh %dm', intdiv($workedMinutes, 60), $workedMinutes % 60),
            'break_hours'  => sprintf('%dh %dm', intdiv($breakMinutes, 60), $breakMinutes % 60)
        ];
    }
    protected function loadEmployees()
    {
        $query = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id');

        if (!empty($this->employeeSearch)) {
            $query->where(function ($q) {
                $q->where('f_name', 'like', '%' . $this->employeeSearch . '%')
                    ->orWhere('l_name', 'like', '%' . $this->employeeSearch . '%');
            });
        }

        $this->employees = $query->orderBy('f_name')->get();
    }




    public function buildAttendanceCalendar()
    {
        $start = $this->viewMode === 'weekly'
            ? $this->startDate->copy()->startOfWeek()
            : $this->startDate->copy()->startOfMonth()->startOfWeek();

        $end = $this->viewMode === 'weekly'
            ? $this->endDate->copy()->endOfWeek()
            : $this->endDate->copy()->endOfMonth()->endOfWeek();

        $rows = Attendance::with('user:id,f_name,l_name', 'requests')
            ->where('company_id', $this->company_id)
            ->whereBetween('clock_in', [$start, $end])
            ->get()
            ->groupBy(fn($r) => Carbon::parse($r->clock_in)->format('Y-m-d'));


        $this->attendanceCalendar = $rows->map(fn($day) => $day->map(fn($att) => [


            'id'        => $att->id,
            'user_id'   => $att->user_id,
            'type'      => 'Attendance',
            'color'     => match ($att->status) {
                'approved' => '#28a745',
                'pending'  => '#ffc107',
                'rejected'    => '#dc3545',
                default   => '#000000ff'
            },
            'title'     => $att->user->full_name,
            'start_time' => Carbon::parse($att->clock_in)->format('g:i A'),
            'end_time'  => $att->clock_out ? Carbon::parse($att->clock_out)->format('g:i A') : '---',
            'status'    => $att->status,
            'is_manual' => $att->is_manual,
            'location'  => $att->clock_in_location,
            'note'      => $att->clock_out_location,
        ]));


        $this->shiftMap = DB::table('shift_dates as sd')
            ->join('shift_employees as se', 'sd.id', '=', 'se.shift_date_id')
            ->selectRaw('sd.date, se.employee_id')
            ->whereBetween('sd.date', [$start, $end])
            ->whereIn('se.employee_id', $this->employees->pluck('id'))
            ->get()
            ->groupBy(fn($row) => $row->date)               // Y-m-d
            ->map(fn($g) => $g->pluck('employee_id')->toArray());
    }


    public function getWeekDaysProperty()
    {
        $days = [];

        if ($this->viewMode === 'weekly') {
            $monday = $this->startDate->copy()->startOfWeek(Carbon::MONDAY);

            for ($i = 0; $i < 7; $i++) {
                $day = $monday->copy()->addDays($i);
                $days[] = [
                    'full_date' => $day->format('Y-m-d'),
                    'day'       => $day->format('D'),
                    'date'      => $day->format('d/m'),
                    'highlight' => $day->equalTo($this->currentDate->copy()->startOfDay()),
                ];
            }
        }

        return $days;
    }



    public function openAttendanceMoreModal($dateKey)
    {
        $this->selectedDate = $dateKey;
        $this->selectedDateAttendances = collect($this->attendanceCalendar[$dateKey] ?? []);


        $this->dispatch('show-more-attendance-modal');
    }





    public function showAbsentDetails($date)
    {
        $this->absentDate = $date;

        // Get absent users for that date
        $shiftEmployees = $this->shiftMap[$date] ?? [];
        $attendanceUsers = collect($this->attendanceCalendar[$date] ?? [])
            ->pluck('user_id')
            ->unique();

        $this->absentDetails = collect($shiftEmployees)
            ->diff($attendanceUsers)
            ->map(fn($id) => $this->employees->firstWhere('id', $id)?->full_name ?? 'Unknown')
            ->toArray();

        $this->dispatch('showAbsentModal');
    }


    public function getRecordsProperty()
    {
        return $this->loaded;
    }


    public function calculateTotalAbsents()
    {
        $total = 0;

        foreach ($this->shiftMap as $date => $shiftEmployeeIds) {


            if (Carbon::parse($date)->isFuture()) {
                continue;
            }

            $attendanceUserIds = collect($this->attendanceCalendar[$date] ?? [])
                ->pluck('user_id')
                ->unique();

            $absentCount = collect($shiftEmployeeIds)
                ->diff($attendanceUserIds)
                ->count();

            $total += $absentCount;
        }

        return $total;
    }




    public function calculateTotalLeaves()
    {
        $totalLeaves = 0;
        foreach ($this->employees as $emp) {
            foreach ($this->weekDays as $day) {
                if (hasLeave($emp->id, $day['full_date'])) {
                    $totalLeaves++;
                }
            }
        }
        return $totalLeaves;
    }




    public function calculateTotalHours()
    {
        $totalMinutes = 0;

        $this->flatAttendances()->each(function ($att) use (&$totalMinutes) {

            if (
                empty($att['start_time']) ||
                empty($att['end_time']) ||
                $att['start_time'] === '---' ||
                $att['end_time'] === '---'
            ) {
                return;
            }

            $start = Carbon::createFromFormat('g:i A', $att['start_time']);
            $end   = Carbon::createFromFormat('g:i A', $att['end_time']);


            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            $totalMinutes += $start->diffInMinutes($end);
        });

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return "{$hours}h {$minutes}m";
    }




    private function flatAttendances()
    {
        return collect($this->attendanceCalendar)->flatten(1);
    }


    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->loaded = collect();
        $this->loadMore();
        $this->startDate = Carbon::today();
        $this->endDate = Carbon::today()->copy()->addDays(6);
        $this->currentDate = Carbon::today();

        $start = $this->startDate->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end   = $this->startDate->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $days  = collect(Carbon::parse($start)->daysUntil($end)->toArray());

        $this->weeks = $days->chunk(7);
        $this->loadEmployees();
        $this->buildAttendanceCalendar();


        $flat = $this->flatAttendances();

        $this->totalAbsents  = $this->calculateTotalAbsents();
        $this->totalLeaves   = $this->calculateTotalLeaves();

        $this->totalPending  = $flat->where('status', 'pending')->count();
        $this->totalApproved = $flat->where('status', 'approved')->count();
        $this->totalRejected = $flat->where('status', 'rejected')->count();

        $this->totalHours    = $this->calculateTotalHours();
    }

    public function render()
    {


        return view('livewire.backend.company.timesheet.timesheet-index', [
            'records' => $this->loaded,
            'weekDays' => $this->weekDays,
            'viewMode' => $this->viewMode,

            'displayDateRange' => $this->displayDateRange,
            'employees' =>  $this->employees,

        ]);
    }

    public function updatedSearch()
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


        if (!empty($this->filterUsers)) {
            $query->whereIn('user_id', $this->filterUsers);
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


        $this->syncAttendanceStatus($request->attendance_id);


        $message = "Your attendance request has been approved.";


        $notification = Notification::create([
            'company_id' => auth()->user()->company->id,
            'user_id'    => $request->user_id,
            'notifiable_id' => $request->id,
            'type'       => 'attendance_request_approved',
            'data'       => [
                'message' => $message
            ],
        ]);

        // Fire real-time event
        event(new NotificationEvent($notification));

        $this->toast('Request approved successfully!', 'success');


        $this->dispatch('closemodal');
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

        $this->syncAttendanceStatus($request->attendance_id);


        $message = "Your attendance request has been rejected.";

        $notification = Notification::create([
            'company_id' => auth()->user()->company->id,
            'user_id'    => $request->user_id,
            'notifiable_id' => $request->id,
            'type'       => 'attendance_request_rejected',
            'data'       => [
                'message' => $message
            ],
        ]);

        // Fire real-time event
        event(new NotificationEvent($notification));

        $this->toast('Request rejected successfully!', 'error');
        $this->resetLoaded();

        $this->dispatch('closemodal');
    }







    public function approveAttendance($attendanceId)
    {
        $attendance = Attendance::with('requests')->find($attendanceId);
        if (!$attendance) return;

        // approve all pending requests
        foreach ($attendance->requests as $req) {
            if ($req->status === 'pending') {
                $req->update(['status' => 'approved']);
            }
        }

        $attendance->update(['status' => 'approved']);

        $this->toast('Attendance approved successfully', 'success');

        // refresh modal data
        $this->selectedAttendance = $attendance->fresh(['user', 'requests']);
        $this->buildAttendanceCalendar();

        $this->dispatch('closemodal');
    }

    public function rejectAttendance($attendanceId)
    {
        $attendance = Attendance::with('requests')->find($attendanceId);
        if (!$attendance) return;

        foreach ($attendance->requests as $req) {
            if ($req->status === 'pending') {
                $req->update(['status' => 'rejected']);
            }
        }

        $attendance->update(['status' => 'rejected']);

        $this->toast('Attendance rejected', 'error');

        $this->selectedAttendance = $attendance->fresh(['user', 'requests']);
        $this->buildAttendanceCalendar();

        $this->dispatch('closemodal');
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

        $message = "Manual attendance has been submitted for {$this->manualDate}.";

        $notification = Notification::create([
            'company_id' => auth()->user()->company->id,
            'user_id'    => $this->employeeId,
            'notifiable_id' => $attendance->id,
            'type'       => 'manual_attendance_submitted',
            'data'       => [
                'message' => $message
            ],
        ]);

        // Fire real-time event
        event(new NotificationEvent($notification));

        $this->toast('Manual entry submitted successfully!', 'success');

        $this->reset(['employeeId', 'manualDate', 'clockInTime', 'clockOutTime', 'reason']);

        $this->dispatch('closemodal');
    }



    private function syncAttendanceStatus(int $attendanceId): void
    {
        $attendance = Attendance::find($attendanceId);
        if (!$attendance) return;

        $all = AttendanceRequest::where('attendance_id', $attendanceId)->get();

        // সব approved ?
        if ($all->every(fn($r) => $r->status === 'approved')) {
            $attendance->update(['status' => 'approved']);
            return;
        }

        if ($all->contains(fn(AttendanceRequest $r) => $r->status === 'rejected')) {
            $attendance->update(['status' => 'rejected']);
            return;
        }
    }

    public function viewRequest($requestId)
    {
        $this->highlightId = $requestId;

        $this->requestDetails = AttendanceRequest::with('user.employee', 'attendance')
            ->find($requestId);

        if ($this->requestDetails && $this->requestDetails->attendance) {
            $attendance = $this->requestDetails->attendance;


            $this->requestDetails->typeName = ucfirst(str_replace('_', ' ', $this->requestDetails->type));
            $this->requestDetails->typeEmoji = $this->requestDetails->type === 'late_clock_in' ? '⏰' : '🕔';


            $this->requestDetails->reason = $this->requestDetails->reason ?: '-';


            $this->requestDetails->start_date = $attendance->clock_in;
            $this->requestDetails->end_date   = $attendance->clock_out ?? $attendance->clock_in;


            $this->requestDetails->clock_in_location  = $attendance->clock_in_location ?? '-';
            $this->requestDetails->clock_out_location = $attendance->clock_out_location ?? '-';

            // Status
            $this->requestDetails->status = $this->requestDetails->status;

            $this->requestDetails->time = $attendance->clock_in;


            $this->requestDetails->location = $attendance->clock_in_location;

            $this->dispatch('show-request-modal');
        }
    }



    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }
}
