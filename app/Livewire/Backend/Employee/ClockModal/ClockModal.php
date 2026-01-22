<?php

namespace App\Livewire\Backend\Employee\ClockModal;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClockModal extends BaseComponent
{
    public $clockInLocation = '';
    public $clockOutLocation = '';

    public $clockOutReason = '';
    public $clockInReason = '';
    public $showClockInReason = false;
    public $showClockOutReason = false;

    public $shiftStartTime;
    public $shiftEndTime;
    public $shiftTotalHours;
    public $graceMinutes;
    public $currentTime;
    public $currentAttendance;

    public $todayWorkingHours = null;

    public $clockInTime;
    public $timezone;
    public $attendance;
    public $elapsedTime = 0;
    public $userTimezone = 'UTC';
    public $previousAttendances = [];


    protected $listeners = [
        'setLocation' => 'setLocation',
        'clockOut' => 'clockOut',
        'setUserTimezone' => 'handleSetUserTimezone',
        'resetReasons' => 'resetReasons',

    ];


    public function clockIn()
    {
        $shiftStart = $this->shiftStartTime;
        $grace = config('attendance.grace_minutes');


        $bdTime = now()->setTimezone('Asia/Dhaka');


        $ukTime = now()->setTimezone('Europe/London');

        $needsApproval = false;

        if ($bdTime->gt($shiftStart->copy()->addMinutes($grace))) {
            $needsApproval = true;
            $this->showClockInReason = true;


            if (empty($this->clockInReason)) {
                $this->addError('clockInReason', 'Reason is required for late clock in.');
                return;
            }
        } else {
            $this->showClockInReason = false;
        }


        $attendance = Attendance::create([
            'user_id' => Auth::id(),
            'company_id' => Auth::user()->employee->company_id,
            'clock_in' => $bdTime,
            'clock_in_location' => $this->clockInLocation,
            'is_manual' => false,
            'needs_approval' => $needsApproval,
            'status' => $needsApproval ? 'pending' : 'approved',
        ]);

        if ($needsApproval) {
            $item = AttendanceRequest::create([
                'user_id' => Auth::id(),
                'attendance_id' => $attendance->id,
                'type' => 'late_clock_in',
                'reason' => $this->clockInReason,
                'status' => 'pending',
            ]);


            $submitterName = auth()->user()->full_name;
            $message = "Employee '{$submitterName}' clocked in late.";

            $notification = Notification::create([
                'company_id' => auth()->user()->employee->company_id,
                'user_id' => null,
                'notifiable_id' => $item->id,
                'type' => 'late_clock_in',

                'data' => [
                    'message' => $message

                ],
            ]);


            event(new NotificationEvent($notification));


            $this->clockInReason = '';
            $this->showClockInReason = false;
            $this->updateWorkingHoursCount();
            $this->dispatch('closemodal');
            $this->dispatch('reloadPage');

            $this->toast("Late Clock-In recorded, pending approval.", 'warning');
        } else {

            $this->clockInReason = '';
            $this->showClockInReason = false;
            $this->updateWorkingHoursCount();
            $this->dispatch('closemodal');
            $this->dispatch('reloadPage');

            $this->toast("Clock In successful!", 'success');
        }
    }



    public function clockOut()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->latest()
            ->first();

        if (!$attendance || $attendance->clock_out) {
            $this->toast("No active Clock In found", 'error');
            return;
        }

        $shiftEnd = $this->shiftEndTime;
        $grace = config('attendance.grace_minutes');
        $bdTime = now()->setTimezone('Asia/Dhaka');
        $ukTime = now()->setTimezone('Europe/London');

        $needsApproval = false;
        $type = null;

        // Late clock out or early clock out
        if ($bdTime->lt($shiftEnd->copy()->subMinutes($grace))) {
            $needsApproval = true;
            $type = 'early_clock_out';

            if (empty($this->clockOutReason)) {

                $this->showClockOutReason = true;
                $this->addError('clockOutReason', 'Reason is required for early clock out.');
                return;
            } else {
                $this->showClockOutReason = false;
            }
        } elseif ($bdTime->gt($shiftEnd->copy()->addMinutes($grace))) {
            $needsApproval = true;
            $type = 'late_clock_out';
            if (empty($this->clockOutReason)) {

                $this->showClockOutReason = true;
                $this->addError('clockOutReason', 'Reason is required for late clock out.');
                return;
            } else {
                $this->showClockOutReason = false;
            }
        }

        $attendance->update([
            'clock_out' => $ukTime,
            'clock_out_location' => $this->clockOutLocation,
            'needs_approval' => $needsApproval,
            'status' => $needsApproval ? 'pending' : 'approved',
        ]);

        if ($needsApproval) {
           $item = AttendanceRequest::create([
                'user_id' => Auth::id(),
                'attendance_id' => $attendance->id,
                'type' => $type,
                'reason' => $this->clockOutReason ?? '',
                'status' => 'pending',
            ]);


            $submitterName = auth()->user()->full_name;
            $message = "Employee '{$submitterName}' clocked out late.";

            $notification = Notification::create([
                'company_id' => auth()->user()->employee->company_id,
                'user_id' => null,
                'notifiable_id' => $item->id,
                'type' => 'late_clock_out',

                'data' => [
                    'message' => $message

                ],
            ]);


            event(new NotificationEvent($notification));

            $this->clockOutReason = '';
            $this->showClockOutReason = false;
            $this->updateWorkingHoursCount();
            $this->dispatch('closemodal');
            $this->dispatch('reloadPage');
            $this->toast("Clock Out requires approval", 'warning');
        } else {

            $this->clockOutReason = '';
            $this->showClockOutReason = false;
            $this->updateWorkingHoursCount();
            $this->dispatch('closemodal');
            $this->dispatch('reloadPage');
            $this->toast("Clock Out successful!", 'success');
        }
    }


    public function setLocation($location)
    {

        $this->clockInLocation = $location;
        $this->clockOutLocation = $location;
    }



    public function updatedClockInReason($value)
    {
        $this->clockInReason = $value;
    }

    public function mount()
    {
        $todaysShift = todaysShiftForUser();
        $this->updateWorkingHoursCount();
        $this->fetchPreviousAttendances();


        if ($todaysShift) {
            $this->shiftStartTime = Carbon::parse($todaysShift->start_time, auth()->user()->timezone);
            $this->shiftEndTime   = Carbon::parse($todaysShift->end_time, auth()->user()->timezone);
            $this->shiftTotalHours = $todaysShift->total_hours;
        } else {
            $this->shiftStartTime = null;
            $this->shiftEndTime   = null;
            $this->shiftTotalHours = null;
        }
        $this->graceMinutes = config('attendance.grace_minutes');
    }


    public function handleSetUserTimezone($timezone)
    {
        $this->userTimezone = $timezone;

        $user = auth()->user();
        $user->timezone = $timezone;
        $user->save();
    }



    public function fetchPreviousAttendances()
    {


        $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';

        $dates = [
            now()->setTimezone($userTimeZone)->toDateString(),
            now()->setTimezone($userTimeZone)->subDay(1)->toDateString(),
            now()->setTimezone($userTimeZone)->subDay(2)->toDateString(),
        ];

        $this->previousAttendances = Attendance::where('user_id', Auth::id())
            ->whereIn(DB::raw('DATE(clock_in)'), $dates)
            ->orderBy('clock_in', 'desc')
            ->get()
            ->map(function ($attendance) use ($userTimeZone) {

                $attendanceDate = Carbon::parse($attendance->clock_in, $userTimeZone)->toDateString();
                $today = now()->setTimezone($userTimeZone)->toDateString();
                $yesterday = now()->setTimezone($userTimeZone)->subDay()->toDateString();

                if ($attendanceDate == $today) {
                    $label = 'Today';
                } elseif ($attendanceDate == $yesterday) {
                    $label = 'Yesterday';
                } else {
                    $label = Carbon::parse($attendance->clock_in, $userTimeZone)->format('D, M d');
                }

                return [
                    'date_label' => $label,
                    'clock_in' => Carbon::parse($attendance->clock_in, $userTimeZone)->format('h:i A'),
                    'clock_out' => $attendance->clock_out
                        ? Carbon::parse($attendance->clock_out, $userTimeZone)->format('h:i A')
                        : 'N/A',
                    'location' => $attendance->clock_in_location ?? 'Unknown',
                    'status' => $attendance->status,
                ];
            });
    }




    public function updateWorkingHoursCount()
    {
        $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';

        $userTodayStart = now()->setTimezone('Asia/Dhaka')->startOfDay();
        $userTodayEnd   = now()->setTimezone('Asia/Dhaka')->endOfDay();

        $this->attendance = Attendance::where('user_id', Auth::id())
            ->whereBetween('clock_in', [$userTodayStart, $userTodayEnd])
            ->latest()
            ->first();


        if (!$this->attendance) {
            $this->elapsedTime = '00:00:00';
            return;
        }

        $this->currentAttendance = $this->attendance;


        if ($this->attendance) {
            $clockInTime = Carbon::parse($this->attendance->clock_in, $userTimeZone);
            $currentTime = now()->setTimezone($userTimeZone);

            if ($this->attendance->clock_out) {
                $clockOutTime = Carbon::parse($this->attendance->clock_out, $userTimeZone);

                $elapsedSeconds = $clockOutTime->diffInSeconds($clockInTime);
            } else {
                $elapsedSeconds = $currentTime->diffInSeconds($clockInTime);
            }


            $elapsedSeconds = abs($elapsedSeconds);

            $hours = floor($elapsedSeconds / 3600);
            $minutes = floor(($elapsedSeconds % 3600) / 60);
            $seconds = $elapsedSeconds % 60;

            $this->elapsedTime = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        } else {
            $this->elapsedTime = '00:00:00';
        }



        $this->dispatch(
            'update-header-timer',
            time: $this->elapsedTime,
            running: !$this->attendance->clock_out
        )->to('backend.components.header');
    }


    public function render()
    {
        $showClockInButton = false;
        $showClockOutButton = false;
        $statusLabel = 'Not Started';

        $userTimeZone = auth()->user()->timezone ?? 'Asia/Dhaka';

        $shiftStart = $this->shiftStartTime;
        $shiftEnd = $this->shiftEndTime;

        $shiftStartMinusGrace = $shiftStart->copy()->subMinutes(config('attendance.grace_minutes'));



        $now = now()->setTimezone($userTimeZone);

        if ($this->currentAttendance) {


            if ($this->currentAttendance->clock_in && !$this->currentAttendance->clock_out) {

                $showClockOutButton = true;
                $statusLabel = 'Working Time';
            } elseif ($this->currentAttendance->clock_in && $this->currentAttendance->clock_out) {

                $showClockInButton = false;
                $showClockOutButton = false;
                $statusLabel = 'Todays Worked';
            }
        } else {
            // No attendance today → Clock In button shows 15 min before shift start
            if ($now->greaterThanOrEqualTo($shiftStartMinusGrace) && $now->lessThanOrEqualTo($shiftEnd)) {
                $showClockInButton = true;
            }

            $statusLabel = 'Not Started';
        }

        return view('livewire.backend.employee.clock-modal.clock-modal', [
            'showClockInButton' => $showClockInButton,
            'showClockOutButton' => $showClockOutButton,
            'statusLabel' => $statusLabel,
        ]);
    }


    public function resetReasons()
    {
        $this->clockInReason = '';
        $this->clockOutReason = '';
        $this->showClockInReason = false;
        $this->showClockOutReason = false;
    }
}
