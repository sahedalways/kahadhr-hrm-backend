<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        {{-- LEFT: Title --}}
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Invoice Management</h5>
        </div>

        {{-- RIGHT: Export --}}
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportInvoices('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>
            <button wire:click="exportInvoices('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <button wire:click="exportInvoices('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>



    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3 g-3">

                <div class="col-md-3">
                    <select class="form-select" wire:change="handleDateFilter($event.target.value)">
                        <option value="">All Dates</option>
                        <option value="day">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>

                @if ($filterDate === 'custom')
                    <div class="col-md-2">
                        <input type="date" class="form-control" wire:change="handleDateFrom($event.target.value)"
                            wire:model="date_from">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" wire:change="handleDateTo($event.target.value)"
                            wire:model="date_to">
                    </div>
                @endif

                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i
                                class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control border-start-0" placeholder="Search by invoice number"
                            wire:model="search" wire:keyup="set('search', $event.target.value)" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice #</th>
                                <th>Billing Period</th>
                                <th>Employee Fee (£)</th>
                                <th>Subtotal (£)</th>
                                <th>VAT (£)</th>
                                <th>Total (£)</th>
                                <th>Status</th>
                                <th>Invoice Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>{{ $invoice->billing_period_start->format('d M, Y') }} -
                                        {{ $invoice->billing_period_end->format('d M, Y') }}</td>
                                    <td>{{ number_format($invoice->employee_fee, 2) }}</td>
                                    <td>{{ number_format($invoice->subtotal, 2) }}</td>
                                    <td>{{ number_format($invoice->vat, 2) }}</td>
                                    <td>{{ number_format($invoice->total, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success text-white">Paid</span>
                                    </td>

                                    <td>{{ $invoice->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <a href="#" wire:click.prevent="downloadInvoice({{ $invoice->id }})"
                                            class="badge bg-primary text-white">
                                            PDF
                                        </a>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No invoices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Load More --}}
                    @if ($hasMore)
                        <div class="text-center mt-4">
                            <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">Load
                                More</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>




</div>
