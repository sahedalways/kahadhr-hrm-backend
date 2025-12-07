<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function employeeDetails($id)
    {
        $details = Employee::with('documents', 'documents.documentType', 'profile')->findOrFail($id);


        $types = DocumentType::all();


        return view('livewire.backend.admin.employee-details', compact('details'));
    }
}
