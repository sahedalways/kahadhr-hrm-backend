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


    public function destroy($company, $id)
    {
        $doc = EmpDocument::findOrFail($id);


        if ($doc->file_path && file_exists(storage_path('app/public/' . $doc->file_path))) {
            unlink(storage_path('app/public/' . $doc->file_path));
        }
        $doc->delete();

        return response()->json(['success' => true, 'message' => 'Document deleted successfully.']);
    }


    public function destroyEmp($company, $id)
    {
        $employee = Employee::findOrFail($id);

        // Delete related data
        $employee->documents()->delete();
        $employee->customFieldValues()->delete();
        $employee->profile()->delete();
        if ($employee->user) {
            $employee->user->delete();
        }
        $employee->delete();

        return redirect()->route('company.dashboard.employees.index', [
            'company' => $company
        ])->with('success', 'Employee deleted successfully!');
    }
}
