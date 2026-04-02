<?php

namespace App\Livewire\Backend\Employee\Policy;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\CompanyPolicy;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class CompanyPolicyIndex extends BaseComponent
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $statusFilter = '';
    public $sortOrder = 'desc';

    public $loaded;
    public $hasMore = true;
    public $lastId = null;

    public function mount()
    {
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.policy.company-policy', [
            'policies' => $this->loaded,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = CompanyPolicy::query();

        if (auth()->user()->employee->company_id) {
            $query->where('company_id', auth()->user()->employee->company_id);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== '') {

            $query->where('send_email', $this->statusFilter);
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

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }

        $this->lastId = $this->sortOrder === 'desc'
            ? $items->last()->id
            : $items->first()->id;

        $this->loaded = $this->loaded->merge($items);
    }

    public function resetLoaded()
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

    public function handleFilter($value)
    {
        $this->statusFilter = $value;
        $this->resetLoaded();
    }


    public function downloadPolicy($policyId)
    {
        $policy = CompanyPolicy::findOrFail($policyId);

        if ($policy->file_path && Storage::disk('public')->exists($policy->file_path)) {
            return response()->download(storage_path('app/public/' . $policy->file_path));
        }

        $this->toast('File not found.', 'error');
        return null;
    }
}
