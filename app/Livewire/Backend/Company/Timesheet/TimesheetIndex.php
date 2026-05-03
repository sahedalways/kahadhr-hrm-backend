<?php

namespace App\Livewire\Backend\Company\Timesheet;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\BreakofShift;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\ShiftDate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public $absentDate;
    public $totalAbsents;
    public bool $selectAllUsers = false;

    public $requestDetails;
    public $highlightId;

    public $editingClockIn = false;
    public $editingClockOut = false;
    public $manualBreakDuration;
    public $isPaidBreak = false;
    public $customPaidBreakHours = '';
    public $customUnpaidBreakHours = '';


    public $selectedPaidBreakDuration = null;
    public $selectedUnpaidBreakDuration = null;

    public $totalShiftHours = '0h 0m';
    public $totalWorkedHours = '0h 0m';

    public function setCustomPaidBreak()
    {
        if (!$this->selectedAttendance) return;

        $duration = (float) $this->customPaidBreakHours;

        // Validation
        if ($duration < 0) {
            $this->toast('Duration cannot be negative', 'error');
            return;
        }

        if ($duration > 2) {
            $this->toast('Duration cannot exceed 2 hours', 'error');
            return;
        }

        $this->selectedPaidBreakDuration = $duration;


        BreakofShift::where('attendance_id', $this->selectedAttendance->id)
            ->where('type', 'Paid')
            ->delete();


        if ($duration > 0) {
            BreakofShift::create([
                'attendance_id' => $this->selectedAttendance->id,
                'type' => 'Paid',
                'duration' => number_format($duration, 2),
                'title' => null,
                'shift_date_id' => null,
            ]);
        }


        $this->selectedAttendance->refresh();

        $this->customPaidBreakHours = '';

        $message = $duration > 0 ? "Paid break updated to " . number_format($duration, 2) . " hours!" : "Paid break removed!";
        $this->toast($message, 'success');
    }

    public function setCustomUnpaidBreak()
    {
        if (!$this->selectedAttendance) return;

        $newDuration = (float) $this->customUnpaidBreakHours;
        $newBreakMinutes = $newDuration * 60;

        // Validation
        if ($newDuration < 0) {
            $this->toast('Duration cannot be negative', 'error');
            return;
        }

        if ($newDuration > 2) {
            $this->toast('Duration cannot exceed 2 hours', 'error');
            return;
        }

        // Get existing unpaid break duration
        $existingBreak = BreakofShift::where('attendance_id', $this->selectedAttendance->id)
            ->where('type', 'Unpaid')
            ->first();

        $oldDuration = $existingBreak ? (float) $existingBreak->duration : 0;
        $oldBreakMinutes = $oldDuration * 60;

        // Calculate the difference (how many minutes to add or subtract from shift)
        $minutesDifference = $newBreakMinutes - $oldBreakMinutes;

        // Update or create break record
        if ($newDuration > 0) {
            if ($existingBreak) {
                // Update existing break
                $existingBreak->update([
                    'duration' => number_format($newDuration, 2),
                ]);
            } else {
                // Create new break
                BreakofShift::create([
                    'attendance_id' => $this->selectedAttendance->id,
                    'type' => 'Unpaid',
                    'duration' => number_format($newDuration, 2),
                    'title' => null,
                    'shift_date_id' => null,
                ]);
            }
        } else {
            // Remove break if duration is 0
            if ($existingBreak) {
                $existingBreak->delete();
            }
        }

        $this->selectedUnpaidBreakDuration = $newDuration;


        if ($minutesDifference != 0) {
            $this->updateShiftTotalHours($minutesDifference);
        }


        $this->selectedAttendance->refresh();

        $this->customUnpaidBreakHours = '';

        $message = $newDuration > 0
            ? "Unpaid break updated to " . number_format($newDuration, 2) . " hours!"
            : "Unpaid break removed!";
        $this->toast($message, 'success');
    }


    private function updateShiftTotalHours($minutesToAdjust)
    {

        if ($this->selectedAttendance->is_manual == 0) {
            $clockIn = Carbon::parse($this->selectedAttendance->clock_in);
            $date = $clockIn->format('Y-m-d');
            $employeeId = $this->selectedAttendance->user->employee->id ?? null;

            if ($employeeId) {

                $shiftDate = ShiftDate::where('date', $date)
                    ->whereHas('employees', function ($q) use ($employeeId) {
                        $q->where('employee_id', $employeeId);
                    })
                    ->first();

                if ($shiftDate) {

                    $currentTotalMinutes = parseTimeToMinutes($shiftDate->total_hours);


                    $newTotalMinutes = $currentTotalMinutes - $minutesToAdjust;


                    if ($newTotalMinutes < 0) {
                        $newTotalMinutes = 0;
                    }


                    $shiftDate->update([
                        'total_hours' => formatMinutesToHours($newTotalMinutes)
                    ]);
                }
            }
        }
    }



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

        $date = Carbon::parse($this->selectedAttendance->clock_in)->format('Y-m-d');
        $newTime = $this->clockInTime;


        $newClockIn = Carbon::parse($date . ' ' . $newTime);

        $this->selectedAttendance->update([
            'clock_in' => $newClockIn,
        ]);


        $this->calculateTotalWorkedHours();

        $this->buildAttendanceCalendar();
        $this->editingClockIn = false;

        $this->toast('Clock In updated!', 'success');
    }

    public function updateClockOut()
    {
        if (!$this->selectedAttendance) return;

        $date = Carbon::parse($this->selectedAttendance->clock_in)->format('Y-m-d');

        if ($this->clockOutTime) {
            $newClockOut = Carbon::parse($date . ' ' . $this->clockOutTime);


            if ($newClockOut->lessThan($this->selectedAttendance->clock_in)) {
                $newClockOut->addDay();
            }

            $this->selectedAttendance->update([
                'clock_out' => $newClockOut,
            ]);
        } else {
            $this->selectedAttendance->update([
                'clock_out' => null,
            ]);
        }


        $this->calculateTotalWorkedHours();

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
        $this->selectedAttendance = Attendance::with(['user', 'requests', 'breaks'])
            ->where('company_id', $this->company_id)
            ->find($attendanceId);

        if ($this->selectedAttendance) {
            $this->clockInTime = Carbon::parse($this->selectedAttendance->clock_in)->format('H:i');
            $this->clockOutTime = $this->selectedAttendance->clock_out
                ? Carbon::parse($this->selectedAttendance->clock_out)->format('H:i')
                : null;



            $paidBreak = $this->selectedAttendance->breaks->where('type', 'Paid')->first();
            $unpaidBreak = $this->selectedAttendance->breaks->where('type', 'Unpaid')->first();

            $this->selectedPaidBreakDuration = $paidBreak ? (float) $paidBreak->duration : 0;
            $this->selectedUnpaidBreakDuration = $unpaidBreak ? (float) $unpaidBreak->duration : 0;
        }

        $this->dispatch('open-attendance-modal');
    }



    private function refreshStats()
    {
        $flat = $this->flatAttendances();

        $this->totalAbsents  = $this->calculateTotalAbsents();
        $this->totalLeaves   = $this->calculateTotalLeaves();

        $this->totalPending  = $flat->where('status', 'pending')->count();
        $this->totalApproved = $flat->where('status', 'approved')->count();
        $this->totalRejected = $flat->where('status', 'rejected')->count();

        $this->totalShiftHours = $this->calculateTotalShiftHours();
        $this->totalWorkedHours = $this->calculateTotalWorkedHours();
    }



    public function calculateTotalShiftHours()
    {
        $totalMinutes = 0;

        if ($this->viewMode === 'weekly') {
            $start = Carbon::parse($this->startDate)->startOfWeek(Carbon::MONDAY);
            $end = Carbon::parse($this->endDate)->endOfWeek(Carbon::SUNDAY);
        } else {
            $start = Carbon::parse($this->startDate)->startOfMonth();
            $end = Carbon::parse($this->endDate)->endOfMonth();
        }

        $shiftDates = ShiftDate::whereBetween('date', [$start, $end])
            ->whereHas('shift', function ($q) {
                $q->where('company_id', $this->company_id);
            })
            ->get();

        foreach ($shiftDates as $shiftDate) {

            $startTime = Carbon::parse($shiftDate->start_time);
            $endTime = Carbon::parse($shiftDate->end_time);

            if ($endTime->lessThan($startTime)) {
                $endTime->addDay();
            }

            $totalMinutes += $startTime->diffInMinutes($endTime);
        }

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        return "{$hours}h {$minutes}m";
    }

    public function calculateTotalWorkedHours()
    {
        $totalMinutes = 0;

        if ($this->viewMode === 'weekly') {
            $start = Carbon::parse($this->startDate)->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfWeek(Carbon::SUNDAY)->endOfDay();
        } else {
            $start = Carbon::parse($this->startDate)->startOfMonth()->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfMonth()->endOfDay();
        }

        $attendances = Attendance::where('company_id', $this->company_id)
            ->whereBetween('clock_in', [$start, $end])
            ->with('breaks')
            ->get();

        foreach ($attendances as $attendance) {
            if (!$attendance->clock_in || !$attendance->clock_out) {
                continue;
            }

            $clockIn = Carbon::parse($attendance->clock_in);
            $clockOut = Carbon::parse($attendance->clock_out);
            if ($clockOut->lessThan($clockIn)) {
                $clockOut->addDay();
            }
            $workedMinutes = $clockIn->diffInRealMinutes($clockOut);


            $mergedBreaks = collect();


            if ($attendance->breaks && $attendance->breaks->count() > 0) {
                $mergedBreaks = $mergedBreaks->merge($attendance->breaks);
            }


            $employeeId = $attendance->user->employee->id ?? null;
            if ($employeeId) {
                $shiftDate = ShiftDate::whereDate('date', $clockIn->format('Y-m-d'))
                    ->whereHas('employees', fn($q) => $q->where('employee_id', $employeeId))
                    ->with('breaks')
                    ->first();
                if ($shiftDate && $shiftDate->breaks && $shiftDate->breaks->count() > 0) {
                    $mergedBreaks = $mergedBreaks->merge($shiftDate->breaks);
                }
            }


            foreach ($mergedBreaks as $break) {
                if (!$break->duration) continue;
                $breakMinutes = parseTimeToMinutes($break->duration);
                if (strtolower($break->type) === 'unpaid') {
                    $workedMinutes -= $breakMinutes;
                }
            }

            $totalMinutes += max(0, $workedMinutes);
        }

        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        return "{$hours}h {$minutes}m";
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
        $today = Carbon::today();

        if ($mode === 'weekly') {
            $this->startDate = $today->copy()->startOfWeek(Carbon::MONDAY);
            $this->endDate = $today->copy()->endOfWeek(Carbon::SUNDAY);
            $this->currentDate = $today;
        } elseif ($mode === 'monthly') {
            $this->startDate = $today->copy()->startOfMonth();
            $this->endDate = $today->copy()->endOfMonth();
            $this->currentDate = $today;
            $this->generateMonthlyWeeks();
        }

        $this->loadEmployees();
        $this->totalShiftHours = $this->calculateTotalShiftHours();
        $this->totalWorkedHours = $this->calculateTotalWorkedHours();
        $this->buildAttendanceCalendar();
    }

    public function goToPrevious()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate = $this->startDate->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
            $this->endDate = $this->startDate->copy()->endOfWeek(Carbon::SUNDAY);
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate = $this->startDate->copy()->subMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->generateMonthlyWeeks();
        }



        $this->currentDate = $this->startDate->copy();
        $this->totalShiftHours = $this->calculateTotalShiftHours();
        $this->totalWorkedHours = $this->calculateTotalWorkedHours();
        $this->buildAttendanceCalendar();
    }

    public function goToNext()
    {
        if ($this->viewMode === 'weekly') {
            $this->startDate = $this->startDate->copy()->addWeek()->startOfWeek(Carbon::MONDAY);
            $this->endDate = $this->startDate->copy()->endOfWeek(Carbon::SUNDAY);
        } elseif ($this->viewMode === 'monthly') {
            $this->startDate = $this->startDate->copy()->addMonth()->startOfMonth();
            $this->endDate = $this->startDate->copy()->endOfMonth();
            $this->generateMonthlyWeeks();
        }


        $this->currentDate = $this->startDate->copy();
        $this->totalShiftHours = $this->calculateTotalShiftHours();
        $this->totalWorkedHours = $this->calculateTotalWorkedHours();
        $this->buildAttendanceCalendar();
    }


    public function getDisplayDateRangeProperty()
    {
        if ($this->viewMode === 'weekly') {
            $monday = $this->startDate->copy()->startOfWeek(Carbon::MONDAY);
            $sunday = $monday->copy()->endOfWeek(Carbon::SUNDAY);

            if ($monday->format('Y') !== $sunday->format('Y')) {
                return $monday->format('M d, Y') . ' - ' . $sunday->format('M d, Y');
            } elseif ($monday->format('m') !== $sunday->format('m')) {
                return $monday->format('M d') . ' - ' . $sunday->format('M d, Y');
            } else {
                return $monday->format('M d') . ' - ' . $sunday->format('d, Y');
            }
        } elseif ($this->viewMode === 'monthly') {
            return $this->startDate->format('F Y');
        }

        return '';
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

    public function generateMonthlyWeeks()
    {
        $startOfMonth = Carbon::parse($this->startDate)->startOfMonth();
        $startOfCalendar = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfCalendar = $startOfMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $days = collect();
        $current = $startOfCalendar->copy();

        while ($current <= $endOfCalendar) {
            $days->push($current->copy());
            $current->addDay();
        }

        $this->weeks = $days->chunk(7);
    }




    public function buildAttendanceCalendar()
    {
        if ($this->viewMode === 'weekly') {
            $start = $this->startDate->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
            $end = $this->startDate->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
        } else {
            $start = Carbon::parse($this->startDate)->startOfMonth()->startOfDay();
            $end = Carbon::parse($this->startDate)->endOfMonth()->endOfDay();
        }




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




        $this->refreshStats();
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


        $scheduledEmployeeIds = $this->shiftMap[$date] ?? [];

        if (empty($scheduledEmployeeIds)) {
            $this->absentDetails = [];
            $this->dispatch('showAbsentModal');
            return;
        }


        $presentEmployeeIds = Attendance::whereDate('clock_in', $date)
            ->where('company_id', $this->company_id)
            ->where('status', 'approved')
            ->get()
            ->map(function ($attendance) {
                $employee = Employee::where('user_id', $attendance->user_id)
                    ->where('company_id', $this->company_id)
                    ->first();
                return $employee ? $employee->id : null;
            })
            ->filter()
            ->unique()
            ->toArray();


        $absentEmployeeIds = array_diff($scheduledEmployeeIds, $presentEmployeeIds);


        $this->absentDetails = Employee::whereIn('id', $absentEmployeeIds)
            ->get()
            ->map(fn($emp) => $emp->f_name . ' ' . $emp->l_name)
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


        if ($this->viewMode === 'weekly') {
            $start = Carbon::parse($this->startDate)->startOfWeek(Carbon::MONDAY);
            $end = Carbon::parse($this->endDate)->endOfWeek(Carbon::SUNDAY);
        } else {
            $start = Carbon::parse($this->startDate)->startOfMonth();
            $end = Carbon::parse($this->endDate)->endOfMonth();
        }


        $current = $start->copy();
        while ($current <= $end) {
            $date = $current->format('Y-m-d');

            $shiftEmployeeIds = $this->shiftMap[$date] ?? [];

            if (empty($shiftEmployeeIds)) {
                $current->addDay();
                continue;
            }

            if (Carbon::parse($date)->isFuture()) {
                $current->addDay();
                continue;
            }

            $userIds = Employee::whereIn('id', $shiftEmployeeIds)
                ->pluck('user_id')
                ->filter();

            if ($userIds->isEmpty()) {
                $total += count($shiftEmployeeIds);
                $current->addDay();
                continue;
            }

            $presentUserIds = Attendance::whereIn('user_id', $userIds)
                ->whereDate('clock_in', $date)
                ->where('status', 'approved')
                ->pluck('user_id')
                ->unique();

            $absentCount = $userIds->diff($presentUserIds)->count();
            $total += $absentCount;

            $current->addDay();
        }

        return $total;
    }


    public function calculateTotalLeaves()
    {
        $totalLeaves = 0;


        if ($this->viewMode === 'weekly') {
            $start = Carbon::parse($this->startDate)->startOfWeek(Carbon::MONDAY);
            $end = Carbon::parse($this->endDate)->endOfWeek(Carbon::SUNDAY);
        } else {
            $start = Carbon::parse($this->startDate)->startOfMonth();
            $end = Carbon::parse($this->endDate)->endOfMonth();
        }

        $dates = [];
        $current = $start->copy();
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        foreach ($this->employees as $emp) {
            foreach ($dates as $date) {
                if (hasLeave($emp->id, $date)) {
                    $totalLeaves++;
                }
            }
        }

        return $totalLeaves;
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
        $this->startDate = Carbon::today()->startOfWeek(Carbon::MONDAY);
        $this->endDate = Carbon::today()->endOfWeek(Carbon::SUNDAY);
        $this->currentDate = Carbon::today();

        $this->generateMonthlyWeeks();

        $this->loadEmployees();
        $this->buildAttendanceCalendar();
        $this->refreshStats();
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
        $this->buildAttendanceCalendar();
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
        $this->buildAttendanceCalendar();
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
            'clockOutTime' => 'nullable|date_format:H:i',
            'manualBreakDuration' => 'nullable|numeric|min:0',
            'isPaidBreak' => 'boolean',
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



        if ($this->manualBreakDuration && $this->manualBreakDuration > 0) {
            BreakofShift::create([
                'title' => null,
                'type' => $this->isPaidBreak ? 'Paid' : 'Unpaid',
                'duration' => number_format($this->manualBreakDuration, 2),
                'shift_date_id' => null,
                'attendance_id' => $attendance->id,
            ]);
        }



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
        $this->buildAttendanceCalendar();
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

            if ($this->requestDetails->type == 'late_clock_in') {
                $this->requestDetails->time = $attendance->clock_in;
            } else {
                $this->requestDetails->time = $attendance->clock_out;
            }


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
