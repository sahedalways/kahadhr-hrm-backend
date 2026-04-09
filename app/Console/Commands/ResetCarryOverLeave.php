<?php

namespace App\Console\Commands;

use App\Models\CalendarYearSetting;
use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class ResetCarryOverLeave extends Command
{
    protected $signature = 'leave:reset-carry-over';
    protected $description = 'Reset carry over hours for employees after 1 year from join date';

    public function handle()
    {
        $today = Carbon::today();

        $employees = Employee::withoutGlobalScopes()->get();

        foreach ($employees as $employee) {

            $calendarSetting = CalendarYearSetting::where('company_id', $employee->company_id)->first();

            $calendarType = $calendarSetting->calendar_year ?? 'english';

            $resetDate = null;

            if ($calendarType === 'english') {
                $resetDate = Carbon::create($today->year, 1, 1);
            }

            if ($calendarType === 'hmrc') {
                $resetDate = Carbon::create($today->year, 4, 1);
            }

            if ($today->equalTo($resetDate)) {

                $leaveBalance = LeaveBalance::firstOrNew([
                    'user_id' => $employee->user_id,
                    'company_id' => $employee->company_id,
                ]);

                if ($leaveBalance->exists) {


                    if ($leaveBalance->carry_over_hours > 0) {
                        $leaveBalance->total_leave_in_liew += $leaveBalance->carry_over_hours;
                    }


                    $leaveBalance->used_annual_hours = 0;
                    $leaveBalance->used_leave_in_liew = 0;
                    $leaveBalance->carry_over_hours = 0;

                    $leaveBalance->save();

                    $this->info("Reset leave for employee ID: {$employee->user_id}");
                }
            }
        }

        return 0;
    }
}
