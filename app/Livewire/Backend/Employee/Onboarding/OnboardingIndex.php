<?php

namespace App\Livewire\Backend\Employee\Onboarding;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Announcement;
use Livewire\WithPagination;

class OnboardingIndex extends BaseComponent
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
        return view('livewire.backend.employee.onboarding.onboarding-index', [
            'announcements' => $this->loaded,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Announcement::query();


        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter !== '') {
            $query->where('onboarding', $this->statusFilter);
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
}
