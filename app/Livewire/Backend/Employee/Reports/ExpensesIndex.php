<?php

namespace App\Livewire\Backend\Employee\Reports;

use App\Events\NotificationEvent;
use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Expenses;
use App\Models\Notification;
use App\Traits\Exportable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ExpensesIndex extends BaseComponent
{
    use WithPagination, WithFileUploads, Exportable;

    public $company_id;
    public $employees;

    public $category;
    public $amount;
    public $description;
    public $attachments = [];
    public $newAttachment;
    public $expense_id;
    public $user_id;

    public $search;
    public $sortOrder = 'desc';
    public $perPage = 10;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public $filterCategory = '';

    public $currentAttachments = [];

    protected $listeners = [
        'deleteExpense' => 'deleteExpense',
        'sortUpdated' => 'handleSort'
    ];

    public function mount()
    {
        $this->company_id = auth()->user()->employee->company_id;
        $this->user_id = auth()->id();

        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.reports.expenses-index', [
            'infos' => $this->loaded
        ]);
    }

    public function resetInput()
    {
        $this->category = '';
        $this->amount = '';
        $this->description = '';
        $this->attachments = [];
        $this->expense_id = null;

        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'attachments' => 'required|array|max:3',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:2048',
            'user_id' => 'required|exists:users,id'
        ]);



        $files = [];
        foreach ($this->attachments as $file) {
            $extension = $file->getClientOriginalExtension();
            $randomName = rand(10000000, 99999999) . now()->format('is') . '.' . $extension;
            $files[] = $file->storeAs('company/expenses', $randomName, 'public');
        }

        Expenses::create([
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'category' => $this->category,
            'amount' => $this->amount,
            'description' => $this->description,
            'attachments' => $files,
            'submitted_at' => now(),
        ]);


        $submitterName = auth()->user()->full_name;
        $message = "Employee '{$submitterName}' submitted an expense report.";

        $notification = Notification::create([
            'company_id' => auth()->user()->employee->company_id,
            'user_id' => auth()->user()->employee->company->user_id,
            'type' => 'submitted_expense',

            'data' => [
                'message' => $message

            ],
        ]);


        event(new NotificationEvent($notification));



        $this->toast('Expense added successfully!', 'success');

        $this->dispatch('closemodal');
        $this->resetInput();
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
        $this->resetInput();
        $this->resetLoaded();
    }





    public function deleteExpense($id)
    {
        $exp = Expenses::where('user_id', auth()->id())->find($id);

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




    public function updatedSearch()
    {
        $this->resetLoaded();
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Expenses::where('user_id', auth()->id());


        // Category Filter
        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        // Sort Logic
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



    public function handleCategoryFilter($value)
    {
        $this->filterCategory = $value;
        $this->resetLoaded();
    }


    public function handleSort($value)
    {
        $this->sortOrder = $value;
        $this->resetLoaded();
    }

    private function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;

        $this->loadMore();
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




    public function openAttachments($expenseId)
    {
        $expense = Expenses::find($expenseId);
        if ($expense) {
            $this->currentAttachments = $expense->attachments ?? [];
        } else {
            $this->currentAttachments = [];
        }
    }


    public function exportExpenses($type)
    {
        $data = Expenses::select('id', 'company_id', 'user_id', 'category', 'amount', 'description', 'submitted_at', 'created_at', 'updated_at')
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'company' => $d->company->company_name,
                    'user' => $d->user->full_name,
                    'category' => $d->category,
                    'amount' => number_format($d->amount, 2),
                    'description' => $d->description,
                    'submitted_at' => $d->submitted_at ? Carbon::parse($d->submitted_at)->format('d F, Y H:i') : 'N/A',
                    'created_at' => $d->created_at ? Carbon::parse($d->created_at)->format('d F, Y H:i') : 'N/A',
                    'updated_at' => $d->updated_at ? Carbon::parse($d->updated_at)->format('d F, Y H:i') : 'N/A',
                ];
            });

        return $this->export(
            $data,
            $type,
            'expenses',
            'exports.generic-table-pdf',
            [
                'title' => siteSetting()->site_title . ' - Expenses',
                'columns' => ['ID', 'Company', 'User', 'Category', 'Amount', 'Description', 'Submitted At', 'Created At', 'Updated At'],
                'keys' => ['id', 'company', 'user', 'category', 'amount', 'description', 'submitted_at', 'created_at', 'updated_at'],
            ]
        );
    }
}
