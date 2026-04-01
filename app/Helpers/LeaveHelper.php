<?php

namespace App\Helpers;

use App\Models\LeaveBalance;



class LeaveHelper
{
    /**
     * Get leave balance data for a specific employee
     *
     * @param int $employeeId
     * @return array
     */
    public static function getLeaveBalanceData($employeeId)
    {
        $leaveBalance = LeaveBalance::where('user_id', $employeeId)->first();

        $totalAnnualHours = $leaveBalance->total_annual_hours ?? 0;
        $usedAnnualHours = $leaveBalance->used_annual_hours ?? 0;
        $totalLeaveInLiewHours = $leaveBalance->total_leave_in_liew ?? 0;
        $usedLeaveInLiewHours = $leaveBalance->used_leave_in_liew ?? 0;

        return [
            'total_annual_hours' => $totalAnnualHours,
            'used_annual_hours' => $usedAnnualHours,
            'remaining_annual_hours' => max(0, $totalAnnualHours - $usedAnnualHours),
            'total_leave_in_liew_hours' => $totalLeaveInLiewHours,
            'used_leave_in_liew_hours' => $usedLeaveInLiewHours,
            'remaining_leave_in_liew_hours' => max(0, $totalLeaveInLiewHours - $usedLeaveInLiewHours),
            'has_balance' => !is_null($leaveBalance),
        ];
    }
}
