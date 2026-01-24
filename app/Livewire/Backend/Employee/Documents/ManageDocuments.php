<?php

namespace App\Livewire\Backend\Employee\Documents;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\DocumentType;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\EmpDocument;
use App\Traits\Exportable;

class ManageDocuments extends BaseComponent
{
    use Exportable, WithPagination;
    use WithFileUploads;

    public $perPage = 12;
    public $search = '';
    public $sortOrder = 'desc';
    public $loaded;
    public $documentTypes;

    public $selectedType = null;
    public $filterType = null;

    public $file_path;
    public $expires_at;

    public $lastId = null;
    public $hasMore = true;
    public $statusFilter = null;
    public $selectedDocName = null;
    public $emp_id = null;
    public $doc_type_id = null;
    public $share_code;
    public $date_of_birth;

    public $existingDocument = null;

    public $modalDocument;
    public $modalFileIndex;

    protected $listeners = ['refreshDocuments' => '$refresh'];
    public $currentDocument;

    public function mount()
    {
        $companyId = auth()->user()->employee->company_id ?? null;

        $this->documentTypes = DocumentType::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.documents.manage-documents', [
            'documents' => $this->loaded,
            'documentTypes' => $this->documentTypes,
            'selectedType' => $this->selectedType,
        ]);
    }

    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    // Filter by type
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

    // Filter by status
    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->selectedType = null;
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $employeeId = auth()->user()->employee->id ?? null;

        $query = EmpDocument::query()
            ->with(['employee', 'documentType'])
            ->where('emp_id', $employeeId);

        // Type filter
        if ($this->selectedType) {
            $query->where('doc_type_id', $this->selectedType);
        }

        // Status filter
        if ($this->statusFilter === 'expired') {
            $query->whereDate('expires_at', '<', now());
        } elseif ($this->statusFilter === 'active') {
            $query->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhereDate('expires_at', '>=', now());
            });
        }

        // Pagination by lastId
        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->limit($this->perPage)->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;

        // Replace old items instead of merge to avoid stale data
        if ($this->lastId === $items->first()->id) {
            $this->loaded = $items;
        } else {
            $this->loaded = $this->loaded->merge($items);
        }

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }

    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function resetSelectedType()
    {
        $this->selectedType = null;
    }


    public function openUploadModal($typeId)
    {
        $this->file_path = null;
        $this->expires_at = null;
        $this->statusFilter = null;

        $this->selectedType = $typeId;
        $this->existingDocument = null;
        $this->selectedDocName = null;
        $this->date_of_birth = null;
        $this->share_code = null;

        $this->dispatch('reset-file-url');

        $docType = DocumentType::find($typeId);

        if ($docType && $docType->name === 'Share Code') {
            $this->selectedDocName = 'Share Code';
        }

        $this->share_code = auth()->user()->employee->share_code ?? null;
        $this->date_of_birth = auth()->user()->employee->date_of_birth ?? null;

        $this->dispatch('openUploadModal');
    }

    public function updatedShareCode($value)
    {
        $this->share_code = strtoupper($value);
    }

    public function openDocModal($docId, $index)
    {
        $this->doc_type_id = null;
        $this->emp_id      = null;
        $this->expires_at = null;


        $this->modalDocument = EmpDocument::with('documentType', 'employee')
            ->find($docId);

        $this->modalFileIndex = $index;


        $this->doc_type_id = $this->modalDocument->doc_type_id;
        $this->emp_id      = $this->modalDocument->emp_id;
        $this->expires_at = $this->modalDocument->expires_at;


        $this->dispatch('documentModalOpened');
    }

    public function saveDocument()
    {
        if ($this->selectedDocName === 'Share Code') {

            $this->validate([
                'share_code'     => 'required|string|max:20',
                'date_of_birth'  => 'required|date',
            ]);

            $employee = auth()->user()->employee;

            $employee->update([
                'share_code'     => strtoupper($this->share_code),
                'date_of_birth'  => $this->date_of_birth,
            ]);

            $this->reset([
                'share_code',
                'date_of_birth',
                'selectedType',
                'selectedDocName',
            ]);

            $this->dispatch('closemodal');
            $this->toast('Share Code saved successfully!', 'success');
            $this->dispatch('clear-notification-route');
            return;
        }

        $this->validate([
            'expires_at' => 'required|date',
            'file_path' => 'required|file|mimes:pdf,jpg,png|max:20480',
        ]);

        $filePath = $this->file_path->store('pdf/employee/documents', 'public');

        EmpDocument::create([
            'doc_type_id' => $this->selectedType,
            'expires_at' => $this->expires_at,
            'emp_id'      => auth()->user()->employee->id,
            'company_id'  => auth()->user()->employee->company_id,
            'file_path'   => $filePath ?: null,
        ]);

        $this->reset([
            'file_path',
            'expires_at',
            'selectedType',
            'selectedDocName',
            'date_of_birth',
            'share_code',
        ]);

        $this->dispatch('closemodal');
        $this->toast('Document uploaded successfully!', 'success');

        $this->resetLoaded();
        $this->dispatch('clear-notification-route');
    }

    public function getFilteredDocumentTypesProperty()
    {
        if (!$this->selectedType && !$this->statusFilter) {
            return $this->documentTypes;
        }

        return $this->documentTypes->filter(function ($type) {
            $docs = $this->loaded->where('doc_type_id', $type->id);
            return $docs->isNotEmpty() || ($this->selectedType && $this->selectedType == $type->id);
        });
    }
}
