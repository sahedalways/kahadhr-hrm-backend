<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\EmpDocument;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Traits\ToastTrait;


class EmployeeController extends Controller
{
    use ToastTrait;



    public function changePassword(Request $request, $company, $id)
    {

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $emp = Employee::with('user')->findOrFail($id);

        // Update password
        $emp->user->password = bcrypt($request->password);
        $emp->user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
