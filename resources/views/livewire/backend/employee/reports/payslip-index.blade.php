<div>
    <div class="row align-items-center justify-content-between mb-4">

        {{-- LEFT: Title --}}
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">My Payslips</h5>
        </div>


        <div class="col-auto">

            <a data-bs-toggle="modal" data-bs-target="#requestPayslipModal"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa-solid fa-file-invoice me-2"></i> Request for Payslip
            </a>

        </div>
    </div>

    {{-- Filters --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">

                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleMonthFilter($event.target.value)">
                                    <option value="">All Months</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleYearFilter($event.target.value)">
                                    <option value="">All Years</option>
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
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
                                    <th>Period</th>
                                    <th>File</th>
                                    <th>Uploaded At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->period }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $row->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">View</a>
                                        </td>
                                        <td>{{ date('d M, Y', strtotime($row->created_at)) }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No payslips found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

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

    <div wire:ignore.self class="modal fade" id="requestPayslipModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">Upload Payslip</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="send">
                    <div class="modal-body">

                        {{-- Period --}}
                        <div class="mb-3">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                {{-- Month --}}
                                <select class="form-select" wire:model="month">
                                    <option value="">Month</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>

                                {{-- Year --}}
                                <select class="form-select" wire:model="year">
                                    <option value="">Year</option>
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            @error('month')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('year')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="send">
                            <span wire:loading wire:target="send">
                                <i class="fas fa-spinner fa-spin me-2"></i>Requesting ...
                            </span>
                            <span wire:loading.remove wire:target="send">Send Request</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Delete this payslip?")) {
            Livewire.dispatch('deletePayslip', {
                id
            });
        }
    });
</script>
