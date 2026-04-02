<?php

namespace App\Livewire\Backend\Repots;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\ReportingDuty;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class LegalDuties extends BaseComponent
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $visibilityFilter = '';
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
        return view('livewire.backend.reports.legal-duties', [
            'duties' => $this->loaded,
        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = ReportingDuty::query();


        $user = auth()->user();

        if ($user->employee) {
            $query->whereIn('visibility', ['both', 'employee']);
        } elseif ($user->company) {
            $query->whereIn('visibility', ['both', 'company']);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->visibilityFilter !== '') {
            $query->where('visibility', $this->visibilityFilter);
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
        $this->visibilityFilter = $value;
        $this->resetLoaded();
    }

    public function downloadDuty($dutyId)
    {
        $duty = ReportingDuty::findOrFail($dutyId);


        $user = auth()->user();
        $hasAccess = false;

        if ($user->employee) {
            $hasAccess = in_array($duty->visibility, ['both', 'employee']);
        } elseif ($user->company) {
            $hasAccess = in_array($duty->visibility, ['both', 'company']);
        }

        if (!$hasAccess) {
            $this->toast('You do not have permission to download this file!', 'error');

            return null;
        }

        if ($duty->file_path && Storage::disk('public')->exists($duty->file_path)) {
            return response()->download(storage_path('app/public/' . $duty->file_path));
        }


        $this->toast('File not found.', 'error');
        return null;
    }

    /**
     * Get visibility badge class for display
     */
    public function getVisibilityBadgeClass($visibility)
    {
        return match ($visibility) {
            'both' => 'bg-info',
            'company' => 'bg-primary',
            'employee' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    /**
     * Get visibility text for display
     */
    public function getVisibilityText($visibility)
    {
        return match ($visibility) {
            'both' => 'All Users',
            'company' => 'Company Only',
            'employee' => 'Employee Only',
            default => ucfirst($visibility)
        };
    }
}
