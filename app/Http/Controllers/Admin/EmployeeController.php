<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Models\Employee;


class EmployeeController extends Controller
{

    public function employeeDetails($id)
    {
        $details = Employee::with(
            'documents',
            'documents.documentType',
            'profile',
            'user',
            'company',
            'department',
            'user.teams'
        )->findOrFail($id);


        $types = DocumentType::all();



        $departments = $details->user
            ? $details->user->teams
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->values()
            : collect();


        return view('livewire.backend.admin.employee-details', compact('details', 'departments', 'types'));
    }
}
