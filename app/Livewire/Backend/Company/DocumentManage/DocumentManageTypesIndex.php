<?php

namespace App\Livewire\Backend\Company\DocumentManage;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyDocument;
use App\Models\DocumentType;
use App\Models\EmailSetting;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Models\Notification;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DocumentManageTypesIndex extends BaseComponent
{
    use Exportable, WithPagination;
    use WithFileUploads;

    public $name, $expires_at, $status, $file_path, $new_file, $emp_id, $doc_type_id, $existingDocument;
    public $document_id, $company_id;
    public $search, $sortOrder = 'desc', $perPage = 10;
    public $loaded, $lastId = null, $hasMore = true;

    public $confirmDeleteId = null;
    public $editDocId = null;
    public $employees;
    public $statusFilter = null;
    public $docTypes = [];
    public $send_email = false;

    public $modalDocument;

    public $modalTitle;

    public $shareCodeFile;
    public $shareCodeEmpId;
    public $selectedType;
    public $modalFileIndex;

    public $filterUsers = [];

    public bool $selectAllUsers = false;

    protected $listeners = [
        'sortUpdated' => 'handleSort'
    ];

    public $emailGatewayMissing = false;

    public function mount()
    {
        $this->employees = Employee::where('company_id', auth()->user()->company->id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();


        $this->docTypes = DocumentType::where('company_id', auth()->user()->company->id)
            ->orderBy('name')
            ->get()
            ->unique('name')
            ->values();




        $this->company_id = auth()->user()->company->id;
        $this->loaded = collect();

        $this->modalTitle = '';
        $this->loadMore();
    }


    public function filterByType($typeId)
    {
        if ($this->selectedType === $typeId) {
            $this->selectedType = null;
        } else {
            $this->selectedType = $typeId;
        }

        $this->statusFilter = null;
        $this->resetLoaded();
    }



    public function updatedFilterUsers()
    {
        $this->selectAllUsers =
            count($this->filterUsers) === $this->employees->count();
        $this->resetLoaded();
    }



    public function updatedSelectAllUsers($value)
    {
        if ($value) {
            $this->filterUsers = $this->employees
                ->pluck('user_id')
                ->toArray();
        } else {
            $this->filterUsers = [];
        }

        $this->resetLoaded();
    }


    public function notifyEmployee($typeId, $employeeId, $type)
    {
        $docType = DocumentType::findOrFail($typeId);

        $companyId = auth()->user()->company->id;
        $emp    = Employee::with('user')->find($employeeId);


        if ($type === 'expired') {
            $message = "{$docType->name} expired. Please upload a new one.";
        }

        if ($type === 'soon') {
            $message = "{$docType->name} expiring soon. Please update it.";
        }


        $notification = Notification::create([
            'company_id'     => $companyId,
            'user_id'        => $emp->user->id,
            'type'           => 'document_expired',
            'notifiable_id'  => $docType->id,
            'data'           => [
                'message'          => $message,
            ],
        ]);

        event(new NotificationEvent($notification));


        $this->toast('Employee notified successfully', 'success');
    }

    public function updateDocument()
    {
        $this->validate([
            'doc_type_id' => 'required',
            'expires_at'  => 'required|date',
            'emp_id'      => 'required|integer|exists:employees,id',
            'new_file'   => 'nullable|file|mimes:pdf|max:20240',
        ]);

        $document = EmpDocument::findOrFail($this->editDocId);



        if ($this->new_file instanceof TemporaryUploadedFile) {

            // delete old file
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }


            $filePath = $this->new_file->store('pdf/employee/documents', 'public');
            $document->file_path = $filePath;
        }

        // ✅ Update fields
        $document->update([
            'doc_type_id' => $this->doc_type_id,
            'expires_at'  => $this->expires_at,
            'emp_id'      => $this->emp_id,
        ]);



        $this->toast('Document updated successfully!', 'success');
        $this->dispatch('closemodal');

        $this->resetInputFields();
        $this->resetLoaded();
    }



    public function openDocModal($docId, $index)
    {
        $this->resetInputFields();
        $this->modalDocument = EmpDocument::with('documentType', 'employee')
            ->find($docId);

        $this->modalFileIndex = $index;
        $this->editDocId = $docId;

        $this->doc_type_id = $this->modalDocument->doc_type_id;
        $this->emp_id      = $this->modalDocument->emp_id;
        $this->expires_at = $this->modalDocument->expires_at;


        $this->dispatch('documentModalOpened');
    }



    public function render()
    {

        return view('livewire.backend.company.document-manage.document-by-types-manage-index', [
            'infos' => $this->loaded,

        ]);
    }


    public function updatedSendEmail($value)
    {
        if ($value) {
            $gateway = EmailSetting::where('company_id', $this->company_id)->first();

            $this->emailGatewayMissing = $gateway ? false : true;


            if (!$gateway) {
                $this->send_email = false;
            }
        } else {
            $this->emailGatewayMissing = false;
        }
    }



    public function resetInputFields()
    {
        $this->name = '';
        $this->expires_at = null;

        $this->file_path = null;
        $this->doc_type_id = null;
        $this->document_id = null;
        $this->emp_id = null;
        $this->selectedType = null;
        $this->shareCodeEmpId = null;
        $this->new_file = null;
        $this->editDocId = null;
        $this->emailGatewayMissing = false;

        $this->resetErrorBag();
    }






    public function save()
    {
        $this->validate([
            'doc_type_id' => 'required',
            'expires_at' => 'required|date',
            'emp_id' => 'required|integer|exists:employees,id',
            'file_path' => 'required|file|mimes:pdf|max:20240',
            'send_email' => 'boolean',
        ]);

        if ($this->send_email) {
            $gateway = EmailSetting::where('company_id', $this->company_id)->first();

            if (! $gateway) {
                $this->toast('SMTP gateway not found for this company!', 'error');

                return;
            }
        }

        $filePath = $this->file_path->store('pdf/employee/documents', 'public');


        EmpDocument::create([
            'doc_type_id' => $this->doc_type_id,
            'comment'      => 'Share Code File',
            'expires_at' => $this->expires_at,
            'emp_id'       => $this->emp_id,
            'company_id'   => auth()->user()->company->id,
            'file_path'   => $filePath ?: null,
        ]);



        $this->toast('Document uploaded successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }



    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }


    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Employee::where('company_id', $this->company_id)
            ->with(['documents.documentType']);



        if ($this->selectedType) {
            $query->whereHas('documents', function ($q) {
                $q->where('doc_type_id', $this->selectedType);
            });
        }


        if (!empty($this->filterUsers)) {
            $query->whereIn('user_id', $this->filterUsers);
        }


        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }


    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }




    public function confirmDelete($id)
    {
        $this->confirmDeleteId = $id;
    }

    public function deleteDocument($id)
    {
        $document = EmpDocument::find($id);

        if ($document) {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();
        }


        $this->confirmDeleteId = null;

        $this->toast('Document deleted successfully!', 'info');
        $this->dispatch('closemodal');
        $this->dispatch('reload-page');
        $this->resetInputFields();
        $this->resetLoaded();
    }



    public function exportDocuments($type)
    {
        $data = collect();

        foreach ($this->loaded as $employee) {

            if (!$employee->documents || $employee->documents->count() == 0) {
                continue;
            }


            $grouped = $employee->documents
                ->sortByDesc('created_at')
                ->groupBy(function ($doc) {
                    return $doc->documentType->name ?? 'Unknown Type';
                });

            foreach ($grouped as $typeName => $docs) {


                $latestDocs = $docs->sortByDesc('created_at')->take(3)->values();

                foreach ($latestDocs as $index => $doc) {

                    $data->push([

                        'employee_name' => trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? '')) ?: $employee->email ?? 'N/A',
                        'doc_type'      => $typeName,
                        'file_name'     => 'File-' . ($index + 1),

                        'expires_at'    => $doc->expires_at ? Carbon::parse($doc->expires_at)->format('d F, Y') : 'N/A',

                    ]);
                }
            }
        }

        return $this->export($data, $type, 'documents-by-type', 'exports.generic-table-pdf', [
            'title'   => siteSetting()->site_title . ' - Documents By Type',
            'columns' => ['Employee Name', 'Document Type', 'File Name',  'Expires At'],
            'keys'    => ['employee_name', 'doc_type', 'file_name', 'expires_at',],
        ]);
    }
}
