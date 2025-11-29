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
    public $file_path;
    public $expires_at;
    public $comment;
    public $lastId = null;
    public $hasMore = true;
    public $statusFilter = null;

    public $existingDocument = null;

    protected $listeners = ['refreshDocuments' => '$refresh'];
    public $currentDocument;

    public function mount()
    {
        $companyId = auth()->user()->employee->company_id ?? auth()->user()->employee->company_id ?? null;

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

    /**
     * Set selected type (called from Blade)
     */
    public function filterByType($typeId)
    {
        // toggle: if clicking same type again, deselect
        if ($this->selectedType === $typeId) {
            $this->selectedType = null;
        } else {
            $this->selectedType = $typeId;
        }

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

    public function openUploadModal($typeId)
    {
        $this->resetValidation();
        $this->file_path = null;
        $this->comment = null;
        $this->expires_at = null;
        $this->statusFilter = null;
        $this->selectedType = null;
        $this->existingDocument = null;
        $this->selectedType = $typeId;

        $this->dispatch('reset-file-url');

        $employeeId = auth()->user()->employee->id ?? null;
        $this->existingDocument = EmpDocument::where('emp_id', $employeeId)
            ->where('doc_type_id', $typeId)
            ->latest()
            ->first();

        if ($this->existingDocument) {
            $this->comment = $this->existingDocument->comment;
            $this->expires_at = $this->existingDocument->expires_at;
        }
    }

    public function saveDocument()
    {
        $this->validate([
            'comment' => 'required|string|max:255',
            'expires_at' => 'required|date',
            'file_path' => 'required|file|mimes:pdf,jpg,png|max:20480',
        ]);

        $filePath = $this->file_path->store('pdf/employee/documents', 'public');

        EmpDocument::create([
            'doc_type_id' => $this->selectedType,
            'comment' => $this->comment,
            'expires_at' => $this->expires_at,
            'emp_id'      => auth()->user()->employee->id,
            'company_id'  => auth()->user()->employee->company_id,
            'file_path'   => $filePath,
        ]);

        $this->statusFilter = null;
        $this->file_path = null;
        $this->comment = null;
        $this->expires_at = null;
        $this->statusFilter = null;
        $this->selectedType = null;

        $this->dispatch('closemodal');
        $this->toast('Document uploaded successfully!', 'success');

        $this->resetLoaded();
    }

    public function getFilteredDocumentTypesProperty()
    {
        // If no filter applied, show all types
        if (!$this->selectedType && !$this->statusFilter) {
            return $this->documentTypes;
        }

        return $this->documentTypes->filter(function ($type) {
            $docs = $this->loaded->where('doc_type_id', $type->id);
            // Show type if it has matching documents OR is the selected type
            return $docs->isNotEmpty() || ($this->selectedType && $this->selectedType == $type->id);
        });
    }
}
