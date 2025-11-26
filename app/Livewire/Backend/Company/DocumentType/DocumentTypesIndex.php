<?php

namespace App\Livewire\Backend\Company\DocumentType;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\DocumentType;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DocumentTypesIndex extends BaseComponent
{
    use Exportable, WithPagination;

    public $name, $documentType_id, $company_id;
    public $perPage = 10;
    public $sortOrder = 'desc';
    public $search;
    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    protected $listeners = ['deleteDocumentType', 'sortUpdated' => 'handleSort'];

    public function mount()
    {
        $this->company_id = app('authUser')->company->id;
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.document-type.document-types-index', [
            'infos' => $this->loaded
        ]);
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->documentType_id = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_types', 'name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->documentType_id)
            ],
        ]);

        DocumentType::create([
            'name' => $this->name,
            'company_id' => $this->company_id,
            'user_id' => auth()->id()
        ]);

        $this->toast('Document Type created successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function edit($id)
    {
        $docType = DocumentType::where('company_id', $this->company_id)->find($id);

        if (!$docType) {
            $this->toast('Document Type not found!', 'error');
            return;
        }

        $this->documentType_id = $docType->id;
        $this->name = $docType->name;
    }

    public function update()
    {
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('document_types', 'name')
                    ->where('company_id', $this->company_id)
                    ->ignore($this->documentType_id)
            ],
        ]);

        $docType = DocumentType::where('company_id', $this->company_id)->find($this->documentType_id);

        if (!$docType) {
            $this->toast('Document Type not found!', 'error');
            return;
        }

        $docType->update(['name' => $this->name]);

        $this->toast('Document Type updated successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }

    public function deleteDocumentType($id)
    {
        $docType = DocumentType::where('company_id', $this->company_id)->find($id);
        if ($docType) $docType->delete();

        $this->toast('Document Type deleted successfully!', 'success');
        $this->resetLoaded();
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = DocumentType::where('company_id', $this->company_id);

        if ($this->search && $this->search != '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)->limit($this->perPage)->get();

        if ($items->count() == 0) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
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

    public function exportDocumentTypes($type)
    {
        $data = $this->loaded->map(function ($doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'company_name' => $doc->company ? $doc->company->company_name : 'N/A',
                'created_at' => $doc->created_at ? Carbon::parse($doc->created_at)->format('d F, Y') : 'N/A',
                'updated_at' => $doc->updated_at ? Carbon::parse($doc->updated_at)->format('d F, Y') : 'N/A',
            ];
        });

        $columns = ['ID', 'Name', 'Company', 'Created At', 'Updated At'];
        $keys = ['id', 'name', 'company_name', 'created_at', 'updated_at'];

        return $this->export($data, $type, 'document-types', 'exports.generic-table-pdf', [
            'title' => siteSetting()->site_title . ' - Document Types',
            'columns' => $columns,
            'keys' => $keys,
        ]);
    }
}
