<?php

namespace App\Livewire\Backend\Company\Reports;

use App\Livewire\Backend\Components\BaseComponent;
use App\Models\Invoice;
use App\Traits\Exportable;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyInvoice extends BaseComponent
{
    use WithPagination, Exportable;

    public $company_id;
    public $search;

    // Filters
    public $filterStatus = '';
    public $filterDate = '';
    public $date_from;
    public $date_to;

    public $sortOrder = 'desc';
    public $perPage = 20;

    public $loaded;
    public $lastId = null;
    public $hasMore = true;

    public function mount()
    {
        $this->company_id = auth()->user()->company->id;
        $this->loaded = collect();
        $this->loadMore();
    }

    public function render()
    {
        return view('livewire.backend.company.reports.company-invoice', [
            'invoices' => $this->loaded,
        ]);
    }

    public function loadMore()
    {
        if (!$this->hasMore) return;

        $query = Invoice::where('company_id', $this->company_id);

        if ($this->search) {
            $query->where('invoice_number', 'like', "%{$this->search}%");
        }

        // Date Filter
        switch ($this->filterDate) {
            case 'day':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($this->date_from && $this->date_to) {
                    $query->whereBetween('created_at', [
                        Carbon::parse($this->date_from)->startOfDay(),
                        Carbon::parse($this->date_to)->endOfDay()
                    ]);
                }
                break;
        }

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

    public function handleStatusFilter($value)
    {
        $this->filterStatus = $value;
        $this->resetLoaded();
    }

    public function handleDateFilter($value)
    {
        $this->filterDate = $value;
        $this->resetLoaded();
    }

    public function updatedSearch()
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


    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            $this->toast('Invoice not found!', 'error');
            return;
        }

        $pdf = Pdf::loadView('exports.single-invoice-pdf', [
            'invoice' => $invoice
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $invoice->invoice_number . '.pdf', [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    // ─── EXPORT ───────────────────────────────────────────────────────
    public function exportInvoices($type)
    {
        $data = Invoice::where('company_id', $this->company_id)
            ->get()
            ->map(function ($d) {
                return [
                    'Invoice #' => $d->invoice_number,
                    'Billing Period' => $d->billing_period_start->format('d M, Y') . ' - ' . $d->billing_period_end->format('d M, Y'),
                    'Employee Fee' => number_format($d->employee_fee, 2),
                    'Total Employees' => $d->total_employees_billed,
                    'Subtotal' => number_format($d->subtotal, 2),
                    'VAT' => number_format($d->vat, 2),
                    'Total' => number_format($d->total, 2),
                    'Status' => ucfirst($d->status),
                    'Invoice Date' => $d->created_at->format('d M, Y'),
                ];
            });

        return $this->export(
            $data,
            $type,
            'company-invoices',
            'exports.generic-table-pdf',
            [
                'title' => 'Company Invoices Report',
                'columns' => ['Invoice #', 'Billing Period', 'Employee Fee', 'Total Employees', 'Subtotal', 'VAT', 'Total', 'Status', 'Invoice Date'],
                'keys' => ['Invoice #', 'Billing Period', 'Employee Fee', 'Total Employees', 'Subtotal', 'VAT', 'Total', 'Status', 'Invoice Date'],
            ]
        );
    }
}
