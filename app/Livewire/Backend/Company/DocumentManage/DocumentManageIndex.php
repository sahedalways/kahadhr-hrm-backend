<?php

namespace App\Livewire\Backend\Company\DocumentManage;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyDocument;
use App\Models\Employee;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DocumentManageIndex extends BaseComponent
{
    use Exportable, WithPagination;
    use WithFileUploads;

    public $name, $expires_at, $status, $file_path, $emp_id, $existingDocument;
    public $document_id, $company_id;
    public $search, $sortOrder = 'desc', $perPage = 10;
    public $loaded, $lastId = null, $hasMore = true;
    public $employees;

    protected $listeners = [
        'deleteDocument' => 'deleteDocument',
        'sortUpdated' => 'handleSort'
    ];

    public function mount()
    {
        $this->employees = Employee::where('company_id', auth()->user()->company->id)->get();
        $this->company_id = auth()->user()->company->id;
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {

        return view('livewire.backend.company.document-manage.document-manage-index', [
            'infos' => $this->loaded,

        ]);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->expires_at = null;
        $this->status = 'pending';
        $this->file_path = null;
        $this->document_id = null;
        $this->emp_id = null;

        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
            'emp_id' => 'nullable|integer|exists:employees,id',
            'file_path' => 'required|file|mimes:pdf|max:20240',
        ]);


        $filePath = $this->file_path->store('pdf/company/documents', 'public');


        CompanyDocument::create([
            'company_id' => $this->company_id,
            'emp_id' => $this->emp_id,
            'name' => $this->name,
            'file_path' => $filePath,
            'expires_at' => $this->expires_at,
            'status' => 'pending',
        ]);

        $this->toast('Document created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function edit($id)
    {
        $doc = CompanyDocument::where('company_id', $this->company_id)->find($id);

        if (!$doc) {
            $this->toast('Document not found!', 'error');
            return;
        }


        $this->document_id = $doc->id;
        $this->name = $doc->name;
        $this->emp_id = $doc->emp_id;
        $this->existingDocument = $doc->document_url;
        $this->expires_at = $doc->expires_at;
        $this->status = $doc->status;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'emp_id' => 'nullable|integer|exists:employees,id',
            'status' => 'required|in:pending,signed,expired',
            'expires_at' => 'nullable|date',
            'file_path' => 'nullable|file|mimes:pdf|max:20240',
        ]);

        $doc = CompanyDocument::where('company_id', $this->company_id)
            ->find($this->document_id);

        if (!$doc) {
            $this->toast('Document not found!', 'error');
            return;
        }


        $newFilePath = $doc->file_path;

        if ($this->file_path) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            // upload new file
            $newFilePath = $this->file_path->store('pdf/company/documents', 'public');
        }


        $doc->update([
            'name'       => $this->name,
            'emp_id'     => $this->emp_id,
            'status'     => $this->status,
            'expires_at' => $this->expires_at,
            'file_path'  => $newFilePath,
        ]);

        $this->toast('Document updated successfully!', 'success');
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

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = CompanyDocument::where('company_id', $this->company_id);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
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
