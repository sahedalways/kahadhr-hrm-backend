<?php

namespace App\Livewire\Backend\Admin;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CustomEmployeeProfileField;
use App\Models\Employee;
use App\Models\DocumentType;
use App\Models\EmpDocument;
use App\Traits\Exportable;
use Livewire\WithFileUploads;

class AdminEmpDetails extends BaseComponent
{
    use WithFileUploads;
    use Exportable;

    public $customFields = [];
    public $customValues = [];

    public $departments;
    public $showAllTeams = false;
    public $employee;
    public $types;

    public $employees, $employee_id, $title;

    public $f_name, $l_name, $start_date, $end_date, $email, $phone_no, $job_title, $avatar, $avatar_preview, $department_id, $team_id, $role, $contract_hours, $is_active, $salary_type = '';


    public $date_of_birth, $street_1, $street_2, $city, $state, $postcode, $country,
        $nationality, $home_phone, $mobile_phone, $personal_email,
        $gender, $marital_status, $tax_reference_number,
        $immigration_status, $brp_number, $brp_expiry_date,
        $right_to_work_expiry_date, $passport_number, $passport_expiry_date;


    public $editMode = false;

    public bool $showAllDepartments = false;

    protected $listeners = ['openDocumentModal'];

    public $selectedDocTypeId;
    public $selectedFileUrl;
    public $selectedExpiresAt;
    public $selectedComment;
    public $selectedDocId;

    public function openDocumentModal($docId)
    {
        $this->selectedFileUrl = '';
        $this->selectedExpiresAt = '';
        $this->selectedComment = '';
        $this->selectedDocId = null;


        $this->selectedDocId = $docId;

        if ($docId) {
            $document = EmpDocument::find($docId);
            $this->selectedDocTypeId = $document->doc_type_id;
            $this->selectedFileUrl = $document->document_url;
            $this->selectedExpiresAt = $document->expires_at;
            $this->selectedComment = $document->comment;
        } else {
            $this->selectedFileUrl = '';
            $this->selectedExpiresAt = '';
            $this->selectedComment = '';
        }
    }



    public function mount($employee)
    {
        $this->employee = Employee::with(
            'documents',
            'documents.documentType',
            'profile',
            'user',
            'company',
            'department',
            'user.teams'
        )->find($employee);



        $this->types = DocumentType::all();


        $this->departments = $this->employee->user
            ? $this->employee->user->teams
            ->pluck('department')
            ->filter()
            ->unique('id')
            ->values()
            : collect();

        $this->customFields = CustomEmployeeProfileField::where('company_id', $this->employee->company_id)
            ->orderBy('id')
            ->get();


        $this->customValues = $this->employee->customFieldValues
            ->pluck('value', 'field_id')
            ->toArray();
    }




    /* Reset input fields */
    public function resetInputFields()
    {
        // Basic info
        $this->f_name = '';
        $this->l_name = '';
        $this->email = '';
        $this->phone_no = '';
        $this->job_title = '';
        $this->title = '';
        $this->department_id = '';
        $this->team_id = '';
        $this->role = '';
        $this->contract_hours = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = 1;

        // Avatar
        $this->avatar_preview = '';
        $this->avatar = '';

        // Profile info fields (ADD YOURS HERE)
        $this->date_of_birth = '';
        $this->street_1 = '';
        $this->street_2 = '';
        $this->city = '';
        $this->state = '';
        $this->postcode = '';
        $this->country = '';
        $this->nationality = '';
        $this->home_phone = '';
        $this->mobile_phone = '';
        $this->personal_email = '';
        $this->gender = '';
        $this->marital_status = '';
        $this->tax_reference_number = '';
        $this->immigration_status = '';
        $this->brp_number = '';
        $this->brp_expiry_date = '';
        $this->right_to_work_expiry_date = '';
        $this->passport_number = '';
        $this->passport_expiry_date = '';

        // Finally
        $this->resetErrorBag();
    }




    public function toggleDepartments()
    {
        $this->showAllDepartments = ! $this->showAllDepartments;
    }

    public function toggleTeams()
    {
        $this->showAllTeams = !$this->showAllTeams;
    }


    public function render()
    {
        return view('livewire.backend.admin.employee-details');
    }
}
