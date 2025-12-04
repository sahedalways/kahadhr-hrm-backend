<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveSetting;
use Carbon\Carbon;

class ResetCarryOverLeave extends Command
{
    protected $signature = 'leave:reset-carry-over';
    protected $description = 'Reset carry over hours for employees after 1 year from join date';

    public function handle()
    {
        $today = Carbon::today();

        $employees = Employee::all();

        foreach ($employees as $employee) {
            $joinDate = Carbon::parse($employee->start_date);
            $oneYearLater = $joinDate->copy()->addYear();

            if ($today->equalTo($oneYearLater) || $today->gt($oneYearLater)) {

                $leaveBalance = LeaveBalance::firstOrNew([
                    'user_id' => $employee->user_id,
                    'company_id' => $employee->company_id,
                ]);

                if ($leaveBalance->exists) {
                    if ($leaveBalance->carry_over_hours > 0) {
                        $settingHours = LeaveSetting::where('company_id', $employee->company_id)
                            ->value('full_time_hours') ?? 0;

                        $leaveBalance->total_hours = $settingHours + $leaveBalance->carry_over_hours;
                        $leaveBalance->used_hours = 0;
                        $leaveBalance->carry_over_hours = $settingHours + $leaveBalance->carry_over_hours;

                        $leaveBalance->save();

                        $this->info("Adjusted leave balance for employee ID: {$employee->user_id}");
                    } else {
                        $settingHours = LeaveSetting::where('company_id', $employee->company_id)
                            ->value('full_time_hours') ?? 0;

                        $leaveBalance->total_hours = $settingHours;
                        $leaveBalance->used_hours = 0;
                        $leaveBalance->carry_over_hours = $settingHours;

                        $leaveBalance->save();

                        $this->info("Reset leave balance for employee ID: {$employee->user_id}");
                    }
                }
            }
        }

        return 0;
    }
}
