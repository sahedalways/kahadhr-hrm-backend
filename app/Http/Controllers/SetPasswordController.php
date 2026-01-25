<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetPasswordController extends Controller
{
    public function showForm($token)

    {
        $employee = Employee::withoutGlobalScope('filterByUserType')
            ->where('invite_token', $token)
            ->where('invite_token_expires_at', '>=', now())
            ->first();

        if (!$employee) {
            abort(404, 'Employee not found or token expired.');
        }


        return view('auth.set-password', [
            'employee' => $employee,
            'token' => $token,
            'company' => 'company',

        ]);
    }


    public function setPassword(Request $request, $token)
    {
        $employee = Employee::withoutGlobalScope('filterByUserType')
            ->where('invite_token', $token)
            ->where('invite_token_expires_at', '>=', now())
            ->first();


        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);


        $user = User::where('email', $employee->email)->first();

        if (!$user) {
            $user = User::create([
                'f_name'            => $employee->f_name,
                'l_name'            => $employee->l_name,
                'email'             => $employee->email,
                'phone_no'          => $employee->phone_no,
                'email_verified_at' => now(),
                'phone_verified_at' => null,
                'password'          => bcrypt($request->password),
                'user_type'         => 'employee',
                'is_active'         => $employee->is_active ?? 1,
                'profile_completed' => 0,
                'permissions'       => null,
                'remember_token'    => Str::random(60),
            ]);

            $annualLeaveHours = 0;



            if ($employee->employment_status == 'full-time') {
                $annualLeaveHours = floatval(config('leave.full_time_hours', 100)) ?? 0;
            } else {
                $contractHours = $employee->contract_hours ?? 0;
                $partTimePercent = floatval(config('leave.part_time_percentage', 100));
                $totalHours = ($contractHours * 52) * ($partTimePercent / 100);

                $annualLeaveHours = ceil($totalHours);
            }



            if ($user && $annualLeaveHours > 0) {
                LeaveBalance::create([
                    'company_id'       => $employee->company_id,
                    'user_id'          => $user->id,
                    'total_annual_hours'      => $annualLeaveHours,
                    'used_annual_hours'       => 0,
                    'carry_over_hours' => $annualLeaveHours,
                ]);
            }



            $employee->invite_token = null;
            $employee->invite_token_expires_at = null;
            $employee->user_id  = $user->id;
            $employee->verified  = true;
            $employee->start_date  = now();
            $employee->save();

            return view('auth.password-set-success', [
                'title' => 'Password Updated & Account Verified',
                'message' => 'Your password has been updated and your account is now verified. You can now log in using your credentials.',
                'user_type' => 'Employee',
            ]);
        } else {
            $user->password = bcrypt($request->password);
            $user->save();


            $employee->invite_token = null;
            $employee->invite_token_expires_at = null;

            $employee->save();

            return view('auth.password-set-success', [
                'title' => 'Password Updated!',
                'message' => 'You can now login using your new password.',
                'user_type' => 'Employee',
            ]);
        }
    }
}
