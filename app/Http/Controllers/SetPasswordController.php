<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetPasswordController extends Controller
{
    public function showForm($token)
    {
        $employee = Employee::where('invite_token', $token)
            ->where('invite_token_expires_at', '>=', now())
            ->firstOrFail();

        return view('auth.set-password', [
            'employee' => $employee,
            'token' => $token,
        ]);
    }



    public function setPassword(Request $request, $token)
    {
        $employee = Employee::where('invite_token', $token)
            ->where('invite_token_expires_at', '>=', now())
            ->firstOrFail();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user in users table
        $user =  User::create([
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
            'permissions'       => json_encode([]),
            'remember_token'    => Str::random(60),
        ]);


        $employee->invite_token = null;
        $employee->invite_token_expires_at = null;
        $employee->user_id  = $user->id;
        $employee->start_date  = now();
        $employee->save();

        return redirect()->route('login')->with('success', 'Password set successfully! You can now login.');
    }
}
