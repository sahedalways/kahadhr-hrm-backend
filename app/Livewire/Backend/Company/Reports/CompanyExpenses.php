<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Employee;
use App\Models\Expenses;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CompanyExpenses extends BaseComponent
{
    use WithPagination, Exportable, WithFileUploads;

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


    public $category;
    public $amount;
    public $description;
    public $attachments = [];
    public $newAttachment;
    public $expense_id;
    public $user_id;

    protected $listeners = [
        'deleteExpense' => 'deleteExpense',

    ];


    public function mount()
    {
        $this->company_id = auth()->user()->company->id;

        $this->employees = Employee::where('company_id', $this->company_id)
            ->whereNotNull('user_id')
            ->orderBy('f_name')
            ->get();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        $totalAmount = $this->getTotalAmount();


        return view('livewire.backend.company.reports.company-expenses', [
            'infos' => $this->loaded,
            'employees' => $this->employees,
            'totalAmount' => $totalAmount,
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



    public function resetInputFields()
    {
        $this->category = '';
        $this->amount = '';
        $this->description = '';
        $this->attachments = [];
        $this->expense_id = null;

        $this->resetErrorBag();
        $this->resetValidation();
    }



    public function save()
    {
        $this->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'attachments' => 'required|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);



        $files = [];
        foreach ($this->attachments as $file) {
            $extension = $file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $files[] = $file->storeAs('company/expenses', $randomName, 'public');
        }

        Expenses::create([
            'company_id' => auth()->user()->company->id,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'attachments' => $files,
            'submitted_at' => now(),
        ]);



        $this->toast('Expense added successfully!', 'success');

        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }





    public function editExpense($id)
    {
        $expense = Expenses::find($id);
        if ($expense) {
            $this->expense_id = $expense->id;
            $this->category = $expense->category;
            $this->amount = $expense->amount;
            $this->description = $expense->description;
            $this->attachments = $expense->attachments ?? [];
        }
    }

    public function update()
    {

        $newFiles = collect($this->attachments)->filter(fn($file) => is_object($file))->toArray();
        $existingFiles = collect($this->attachments)->filter(fn($file) => is_string($file))->toArray();

        // Validate main properties
        $this->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'attachments' => 'required|array|max:3',
        ]);

        // Validate only new uploads
        foreach ($newFiles as $index => $file) {
            $this->validate([
                "attachments.$index" => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);
        }

        $uploadedFiles = [];


        foreach ($newFiles as $file) {
            $extension = $file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $uploadedFiles[] = $file->storeAs('company/expenses', $randomName, 'public');
        }


        $allFiles = array_merge($existingFiles, $uploadedFiles);


        $expense = Expenses::find($this->expense_id);
        $oldFiles = $expense->attachments ?? [];
        foreach ($oldFiles as $oldFile) {
            if (!in_array($oldFile, $allFiles) && file_exists(storage_path('app/public/' . $oldFile))) {
                unlink(storage_path('app/public/' . $oldFile));
            }
        }


        $expense->update([
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'attachments' => $allFiles,
        ]);

        $this->toast('Expense updated successfully!', 'success');
        $this->dispatch('closemodal');
        $this->resetInputFields();
        $this->resetLoaded();
    }





    public function deleteExpense($id)
    {
        $exp = Expenses::find($id);

        if ($exp) {
            // Delete files from storage
            if (!empty($exp->attachments)) {
                foreach ($exp->attachments as $file) {
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }


            $exp->delete();

            $this->toast('Expense deleted along with attachments!', 'success');
        } else {
            $this->toast('Expense not found!', 'error');
        }

        $this->resetLoaded();
    }



    public function triggerFileInput()
    {
        $this->dispatch('openFileInput');
    }


    public function updatedNewAttachment()
    {

        $this->validate([
            'newAttachment' => 'file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        // Check max limit
        if (count($this->attachments) >= 3) {
            $this->addError('attachments', 'You can upload maximum 3 files.');
            return;
        }


        $this->attachments[] = $this->newAttachment;


        $this->newAttachment = null;
    }


    public function removeAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }



    public function getTotalAmount()
    {
        $query = Expenses::where('company_id', $this->company_id);

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

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

        return $query->sum('amount');
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
