<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Traits\ToastTrait;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ToastTrait;
    public function companyDetails($id)
    {
        $details = Company::with(['bankInfos', 'employees',  'billingPlan', 'calendarYearSetting'])->findOrFail($id);


        return view('livewire.backend.admin.company-details', compact('details'));
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
