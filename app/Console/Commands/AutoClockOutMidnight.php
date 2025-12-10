<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
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

        $attendances = Attendance::whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->get();

        foreach ($attendances as $attendance) {
            $user = $attendance->user;
            $userTimezone = $user->timezone ?? 'Asia/Dhaka';
            $now = now()->setTimezone($userTimezone);


            $attendance->update([
                'clock_out' => $now,
                'clock_out_location' => 'Auto Clock Out',
            ]);


            AttendanceRequest::create([
                'user_id' => $attendance->user_id,
                'attendance_id' => $attendance->id,
                'type' => 'auto_clock_out',
                'reason' => 'Auto Clock Out at Midnight',
                'status' => 'pending',
            ]);

            $this->info("Auto Clock Out done for user {$attendance->user_id}");
        }

        $this->info('All open attendances auto clocked out.');
    }
}
