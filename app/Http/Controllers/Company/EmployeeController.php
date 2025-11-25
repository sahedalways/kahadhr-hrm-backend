<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Employee;
use App\Traits\ToastTrait;


class EmployeeController extends Controller
{
    use ToastTrait;
    public function details($id)
    {
        $details = Employee::findOrFail($id);


        return view('livewire.backend.employee-details', compact('details'));
    }


    public function changeCompanyPassword(Request $request, $id)
    {

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $company = Company::findOrFail($id);

        // Update password
        $company->user->password = bcrypt($request->password);
        $company->user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
}
