<?php

namespace App\Helpers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceHelper
{
    /**
     * Check if current employee has running (active) attendance today
     *
     * @param  int|null $userId
     * @return bool
     */
    public static function isAttendanceRunning(?int $userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : Auth::user();

        if (!$user || $user->user_type !== 'employee') {
            return false;
        }

        $runningAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->latest()
            ->first();

        return $runningAttendance ? true : false;
    }


    /**
     * Get all approved attendances for current employee this week
     *
     * @param int|null $userId
     * @return \Illuminate\Support\Collection
     */
    public static function approvedAttendancesThisWeek(?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        $user = \App\Models\User::find($userId);

        if (!$user || !$user->employee) return collect();

        $employee = $user->employee;
        $weeklyHours = $employee->contract_hours;

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();

        // Approved attendances this week
        $attendances = Attendance::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('clock_in', [$startOfWeek, $endOfWeek])
            ->get()
            ->map(function ($attendance) {
                return [
                    'start_time' => $attendance->clock_in,
                    'end_time' => $attendance->clock_out,
                    'hours' => $attendance->clock_out
                        ? \Carbon\Carbon::parse($attendance->clock_in)
                        ->diffInMinutes(\Carbon\Carbon::parse($attendance->clock_out)) / 60
                        : 0,
                ];
            });


        $totalWorked = $attendances->sum('hours');

        return [
            'weekly_contract_hours' => $weeklyHours,
            'total_worked_hours' => $totalWorked,
            'attendances' => $attendances,
        ];
    }
}
