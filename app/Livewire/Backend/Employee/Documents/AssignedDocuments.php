<?php

namespace App\Livewire\Backend\Employee\Documents;

use App\Events\NotificationEvent;
use App\Jobs\EmployeeSignedNotificationJob;
use App\Livewire\Backend\Components\BaseComponent;
use Livewire\WithPagination;
use App\Models\CompanyDocument;
use App\Models\Notification;
use Carbon\Carbon;
use setasign\Fpdi\Fpdi;

class AssignedDocuments extends BaseComponent
{
    use WithPagination;

    public $perPage = 12;
    public $openDocId = null;
    public $search = '';
    public $sortOrder = 'desc';
    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    protected $listeners = ['refreshDocuments' => '$refresh'];
    public $currentDocument;
    public $statusFilter = null;

    public function mount()
    {
        if (request()->has('id')) {
            $this->openDocId = request('id');


            $this->openDocumentModal($this->openDocId);
        }


        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.employee.documents.assigned-documents', [
            'documents' => $this->loaded
        ]);
    }

    public function updatedSearch()
    {
        $this->resetLoaded();
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

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $employeeId = auth()->user()->employee->id ?? null;

        $query = CompanyDocument::query()
            ->with('employee')
            ->where('emp_id', $employeeId)
            ->orderBy('id', $this->sortOrder);

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }


        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->lastId) {
            $query->where('id', $this->sortOrder === 'desc' ? '<' : '>', $this->lastId);
        }

        $items = $query->limit($this->perPage)->get();

        if ($items->isEmpty()) {
            $this->hasMore = false;
            return;
        }

        $this->lastId = $this->sortOrder === 'desc' ? $items->last()->id : $items->first()->id;
        $this->loaded = $this->loaded->merge($items);

        if ($items->count() < $this->perPage) {
            $this->hasMore = false;
        }
    }


    public function addSignature()
    {
        if (!$this->currentDocument) return;

        $employee = auth()->user()->employee;
        $signatureName = $employee->full_name;

        $filePath = storage_path('app/public/' . $this->currentDocument->file_path);
        $outputPath = storage_path('app/public/' . 'signed_' . basename($this->currentDocument->file_path));

        $pdf = new Fpdi();

        $pageCount = $pdf->setSourceFile($filePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $tplId = $pdf->importPage($pageNo);
            $pdf->AddPage();
            $pdf->useTemplate($tplId);

            // Add signature only on the last page
            if ($pageNo == $pageCount) {
                // Bold "Signature" label
                $pdf->SetFont('Helvetica', 'B', 14);
                $pdf->SetTextColor(220, 50, 50);
                $pdf->SetXY(150, 260);
                $pdf->Write(0, 'Signature');

                // Employee full name below
                $pdf->SetFont('Helvetica', '', 12);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(150, 265);
                $pdf->Write(0, $signatureName);

                // Current date below the signature
                $pdf->SetFont('Helvetica', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetXY(150, 270);
                $pdf->Write(0, Carbon::now()->format('d M Y'));
            }
        }

        $pdf->Output('F', $outputPath);

        $this->currentDocument->update([
            'file_path' => 'signed_' . basename($this->currentDocument->file_path),
            'status' => 'signed'
        ]);


        EmployeeSignedNotificationJob::dispatch($this->currentDocument->id);


        $documentName = $this->currentDocument->name;
        $submitterName = $employee->full_name;
        $message = "Employee '{$submitterName}' has submitted the document '{$documentName}'.";

        $notification = Notification::create([
            'company_id' => $employee->company_id,
            'user_id' => null,
            'notifiable_id' => $this->currentDocument->id,
            'type' => 'added_signature',

            'data' => [
                'message' => $message

            ],
        ]);


        event(new NotificationEvent($notification));



        $this->toast('Signature added to document successfully!', 'success');
        $this->resetLoaded();
    }

    public function resetLoaded()
    {
        $this->loaded = collect();
        $this->lastId = null;
        $this->hasMore = true;
        $this->loadMore();
    }



    public function openDocumentModal($docId)
    {
        $this->currentDocument = CompanyDocument::find($docId);

        $this->dispatch('show-doc-modal');
    }
}
