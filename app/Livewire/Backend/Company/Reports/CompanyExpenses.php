<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Expenses;
use App\Models\User;
use App\Traits\Exportable;
use Carbon\Carbon;
use Livewire\WithPagination;

class CompanyExpenses extends BaseComponent
{
    use WithPagination, Exportable;

    public $company_id;
    public $search;

    // Filters
    public $filterUser = '';
    public $filterCategory = '';
    public $filterDate = '';
    public $date_from;
    public $date_to;


    public $sortOrder = 'desc';
    public $perPage = 20;

    // Infinite loading
    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $currentAttachments = [];

    public $employees;

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;

        $this->employees = Employee::where('company_id', $this->company_id)
            ->orderBy('f_name')
            ->get();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.reports.company-expenses', [
            'infos' => $this->loaded,
            'employees' => $this->employees,
        ]);
    }
    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Expenses::where('company_id', $this->company_id);

        // Filter: User
        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        // Filter: Category
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        // Date Range Filter (Day / Week / Month / Year / Custom)
        switch ($this->filterDate) {

            case 'day':
                $query->whereDate('submitted_at', Carbon::today());
                break;

            case 'week':
                $query->whereBetween('submitted_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;

            case 'month':
                $query->whereMonth('submitted_at', Carbon::now()->month)
                    ->whereYear('submitted_at', Carbon::now()->year);
                break;

            case 'year':
                $query->whereYear('submitted_at', Carbon::now()->year);
                break;

            case 'custom':
                if ($this->date_from && $this->date_to) {
                    $query->whereBetween('submitted_at', [
                        Carbon::parse($this->date_from)->startOfDay(),
                        Carbon::parse($this->date_to)->endOfDay()
                    ]);
                }
                break;
        }

        // Infinite scroll (ID based)
        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->orderBy('id', $this->sortOrder)
            ->limit($this->perPage)
            ->get();

        if ($items->isEmpty()) {
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


    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }

    public function handleEmployeeFilter($id)
    {
        $this->filterUser = $id;
        $this->resetLoaded();
    }

    public function handleCategoryFilter($value)
    {
        $this->filterCategory = $value;
        $this->resetLoaded();
    }

    public function updatedFilterDate()
    {
        $this->resetLoaded();
    }

    public function handleDateFrom($value)
    {
        $this->date_from = $value;

        if ($this->filterDate === 'custom') {
            $this->resetLoaded();
        }
    }

    public function handleDateTo($value)
    {
        $this->date_to = $value;

        if ($this->filterDate === 'custom') {
            $this->resetLoaded();
        }
    }


    public function handleDateFilter($value)
    {
        $this->filterDate = $value;
        $this->resetLoaded();
    }



    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    // ─── VIEW ATTACHMENTS ─────────────────────────────────────────────
    public function openAttachments($expenseId)
    {
        $expense = Expenses::find($expenseId);
        $this->currentAttachments = $expense ? $expense->attachments : [];
    }

    // ─── EXPORT ───────────────────────────────────────────────────────
    public function exportExpenses($type)
    {
        $data = Expenses::where('company_id', $this->company_id)
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'user' => $d->user->full_name,
                    'category' => $d->category,
                    'amount' => number_format($d->amount, 2),
                    'description' => $d->description,
                    'submitted_at' => $d->submitted_at ? Carbon::parse($d->submitted_at)->format('d F, Y') : 'N/A',
                ];
            });

        return $this->export(
            $data,
            $type,
            'company-expenses',
            'exports.generic-table-pdf',
            [
                'title' => 'Company Expenses Report',
                'columns' => ['ID', 'Employee', 'Category', 'Amount', 'Description', 'Submitted At'],
                'keys' => ['id', 'user', 'category', 'amount', 'description', 'submitted_at'],
            ]
        );
    }
}
