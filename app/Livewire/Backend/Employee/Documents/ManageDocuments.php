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
    public $comment;
    public $lastId = null;
    public $hasMore = true;
    public $statusFilter = null;
    public $selectedDocName = null;
    public $share_code;
    public $date_of_birth;

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
            'selectedType' => $this->filterType,
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
        if ($this->filterType === $typeId) {
            $this->filterType = null;
        } else {
            $this->filterType = $typeId;
        }

        $this->statusFilter = null;

        $this->resetLoaded();
    }

    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->filterType = null;
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

        $this->file_path = null;
        $this->comment = null;
        $this->expires_at = null;
        $this->statusFilter = null;
        $this->selectedType = null;
        $this->existingDocument = null;
        $this->selectedDocName = null;
        $this->date_of_birth = null;
        $this->share_code = null;
        $this->selectedType = $typeId;

        $this->dispatch('reset-file-url');

        $employeeId = auth()->user()->employee->id ?? null;
        $this->existingDocument = EmpDocument::where('emp_id', $employeeId)
            ->where('doc_type_id', $typeId)
            ->latest()
            ->first();


        $this->selectedDocName = optional($this->documentTypes->where('id', $typeId)->first())->name;

        if ($this->existingDocument) {
            $this->comment = $this->existingDocument->comment;
            $this->expires_at = $this->existingDocument->expires_at;
        }


      $this->share_code = auth()->user()->employee->share_code ?? null;
      $this->date_of_birth = auth()->user()->employee->date_of_birth ?? null;

       $this->dispatch('openUploadModal');
    }


    public function updatedShareCode($value)
    {
     $this->share_code = strtoupper($value);
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

        // Reset fields
        $this->reset([
            'share_code',
            'date_of_birth',
            'selectedType',
            'selectedDocName',
        ]);

        $this->dispatch('closemodal');
        $this->toast('Share Code saved successfully!', 'success');

        return;
    }


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
        $this->selectedDocName = null;
        $this->date_of_birth = null;
        $this->share_code = null;

        $this->dispatch('closemodal');
        $this->toast('Document uploaded successfully!', 'success');

        $this->resetLoaded();
    }

    public function getFilteredDocumentTypesProperty()
    {
        if (!$this->filterType && !$this->statusFilter) {
            return $this->documentTypes;
        }

        return $this->documentTypes->filter(function ($type) {
            $docs = $this->loaded->where('doc_type_id', $type->id);

            return $docs->isNotEmpty() || ($this->filterType && $this->filterType == $type->id);
        });
    }
}
