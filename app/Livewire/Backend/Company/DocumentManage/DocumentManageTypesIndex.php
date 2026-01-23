<?php

namespace App\Livewire\Backend\Company\DocumentManage;


use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyDocument;
use App\Models\DocumentType;
use App\Models\EmailSetting;
use App\Models\EmpDocument;
use App\Models\Employee;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DocumentManageTypesIndex extends BaseComponent
{
    use Exportable, WithPagination;
    use WithFileUploads;

    public $name, $expires_at, $status, $file_path, $emp_id, $doc_type_id, $existingDocument;
    public $document_id, $company_id;
    public $search, $sortOrder = 'desc', $perPage = 10;
    public $loaded, $lastId = null, $hasMore = true;
    public $employees;
    public $statusFilter = null;
    public $docTypes;
    public $send_email = false;

    public $modalDocument;

    public $modalTitle;

    public $shareCodeFile;
    public $shareCodeEmpId;
    public $selectedType;

    protected $listeners = [
        'deleteDocument' => 'deleteDocument',
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




    public function openDocModal($docId)
    {
        $this->resetInputFields();
        $this->modalDocument = EmpDocument::with('documentType', 'employee')
            ->find($docId);


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




    public function deleteDocument($id)
    {
        $doc = CompanyDocument::where('company_id', $this->company_id)->find($id);

        if ($doc) $doc->delete();

        $this->toast('Document deleted successfully!', 'success');
        $this->resetLoaded();
    }

    public function updatedSearch()
    {
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

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }


        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
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

    public function exportDocuments($type)
    {
        $data = $this->loaded->map(function ($d) {
            return [
                'id' => $d->id,
                'name' => $d->name,
                'status' => ucfirst($d->status),
                'expires_at' => $d->expires_at ? Carbon::parse($d->expires_at)->format('d F, Y') : 'N/A',
                'created_at' => $d->created_at ? Carbon::parse($d->created_at)->format('d F, Y') : 'N/A',
            ];
        });

        return $this->export($data, $type, 'manage-documents', 'exports.generic-table-pdf', [
            'title' => siteSetting()->site_title . ' - Manage Documents',
            'columns' => ['ID', 'Name', 'Status', 'Expires At', 'Created At'],
            'keys' => ['id', 'name', 'status', 'expires_at', 'created_at'],
        ]);
    }
}
