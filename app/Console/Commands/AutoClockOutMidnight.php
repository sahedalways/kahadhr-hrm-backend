<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\Employee;
use App\Models\ShiftDate;
use Carbon\Carbon;

class AutoClockOutMidnight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:auto-clock-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Clock Out all open attendances at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $attendances = Attendance::withoutGlobalScopes()
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->get();

        foreach ($attendances as $attendance) {
            $userTimezone = 'Europe/London';
            $attendanceDate = Carbon::parse($attendance->clock_in)->format('Y-m-d');

            $shiftEndTime = $this->getShiftEndTime($attendance->user_id, $attendanceDate, $userTimezone);
            $clockOutTime = $shiftEndTime ?? now()->setTimezone($userTimezone);


            $attendance->update([
                'clock_out' => $clockOutTime,
                'status' => 'approved',
                'clock_out_location' => 'Auto Clock Out',
            ]);



            $this->info("Auto Clock Out done for user {$attendance->user_id}");
        }

        $this->info('All open attendances auto clocked out.');
    }

    private function getShiftEndTime($userId, $attendanceDate, $timezone)
    {
        $employee = Employee::where('user_id', $userId)->first();
        if (!$employee) return null;

        $shift = ShiftDate::whereDate('date', $attendanceDate)
            ->whereHas('employees', fn($q) => $q->where('employee_id', $employee->id))
            ->first();

        if (!$shift || !$shift->end_time) return null;

        return Carbon::parse($shift->date . ' ' . $shift->end_time, $timezone);
    }
}
