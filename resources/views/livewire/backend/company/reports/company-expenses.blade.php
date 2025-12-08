<div>
    <div class="row align-items-center justify-content-between mb-4">

        {{-- LEFT: Title --}}
        <div class="col-auto">
            <h5 class="fw-500 text-white m-0">Expense Management (Company Admin)</h5>
        </div>

        {{-- RIGHT: Export --}}
        <div class="col-auto d-flex gap-2">

            <button wire:click="exportExpenses('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportExpenses('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportExpenses('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>

        </div>
    </div>


    {{-- Search + Sort + Category Filter + Employee Filter --}}
    <div class="row">
        <div class="col-12">

            <div class="card mb-4">
                <div class="card shadow-sm border-0">

                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">

                            {{-- Employee Filter (Admin Only) --}}
                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleEmployeeFilter($event.target.value)">
                                    <option value="">All Employees</option>

                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Sort --}}
                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>

                            {{-- Category Filter --}}
                            <div class="col-md-3">
                                <select class="form-select" wire:change="handleCategoryFilter($event.target.value)">
                                    <option value="">All Categories</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Meals & Entertainment">Meals & Entertainment</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="IT & Software">IT & Software</option>
                                    <option value="Equipment & Hardware">Equipment & Hardware</option>
                                    <option value="Communication">Communication</option>
                                    <option value="Training & Development">Training & Development</option>
                                    <option value="Marketing & Advertising">Marketing & Advertising</option>
                                    <option value="Professional Services">Professional Services</option>
                                    <option value="Employee Welfare">Employee Welfare</option>
                                    <option value="Utilities & Facilities">Utilities & Facilities</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>




                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleDateFilter($event.target.value)">
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
                                    <input type="date" class="form-control"
                                        wire:change="handleDateFrom($event.target.value)" wire:model="date_from">
                                </div>

                                {{-- Date To --}}
                                <div class="col-md-2">
                                    <input type="date" class="form-control"
                                        wire:change="handleDateTo($event.target.value)" wire:model="date_to">
                                </div>
                            @endif
                        </div>


                        {{-- Summary --}}
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="text-muted small mb-0">
                                Showing:
                                <strong>{{ $search ?: 'All Expenses' }}</strong>
                            </p>

                            <div wire:loading wire:target="search">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                                <span class="text-primary small">Searching...</span>
                            </div>
                        </div>

                    </div>
                </div>



                {{-- TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Category</th>
                                    <th>Amount (£)</th>
                                    <th>Description</th>
                                    <th>Attachment</th>
                                    <th>Submission Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp

                                @forelse($infos as $row)
                                    <tr>

                                        <td>{{ $i++ }}</td>

                                        <td>{{ $row->user->full_name ?? 'N/A' }}</td>

                                        <td>{{ $row->category }}</td>

                                        <td>{{ number_format($row->amount, 2) }}</td>

                                        <td>{{ Str::limit($row->description, 40) }}</td>

                                        {{-- Attachments --}}
                                        <td>
                                            @if ($row->attachments && count($row->attachments) > 0)
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    wire:click="openAttachments({{ $row->id }})"
                                                    data-bs-toggle="modal" data-bs-target="#attachmentsModal">
                                                    View
                                                </button>
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $row->submitted_at ? date('d M, Y', strtotime($row->submitted_at)) : 'N/A' }}
                                        </td>

                                        {{-- Only Delete for Company Admin --}}
                                        <td>
                                            <a href="#" class="badge bg-danger text-white"
                                                wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No expenses found</td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>


                        {{-- Load More --}}
                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore" class="btn btn-outline-primary rounded-pill px-4 py-2">
                                    Load More
                                </button>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- Attachments Modal --}}
    <div wire:ignore.self class="modal fade" id="attachmentsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Attachments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($currentAttachments && count($currentAttachments) > 0)
                        <div class="row g-3">

                            @foreach ($currentAttachments as $file)
                                <div class="col-md-4 text-center">

                                    @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp

                                    @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset('storage/' . $file) }}" class="img-fluid rounded"
                                            style="max-height:150px; cursor:pointer;"
                                            onclick="window.open('{{ asset('storage/' . $file) }}','_blank')">
                                    @elseif ($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $file) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm mt-2 w-100">
                                            View PDF
                                        </a>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-muted">No attachments available.</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>


<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Delete this expense?")) {
            Livewire.dispatch('deleteExpense', {
                id
            })
        }
    });
</script>
