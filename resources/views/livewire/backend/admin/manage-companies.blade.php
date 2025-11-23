<div>
    <div class="row align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-white m-0">Companies Management</h5>
        </div>



        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">


            <button wire:click="exportCompanies('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportCompanies('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportCompanies('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">
                            <!-- Search Input -->
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-search"></i></span>
                                    <input type="text" class="form-control form-control-lg border-start-0"
                                        placeholder="Search by company name, email, or phone" wire:model="search"
                                        wire:keyup="set('search', $event.target.value)" />
                                </div>
                            </div>

                            <!-- Sort Dropdown -->
                            <div class="col-md-4 d-flex gap-2">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>


                                <select class="form-select form-select-lg"
                                    wire:change="handleFilter($event.target.value)">
                                    <option value="">All Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                        </div>

                        <!-- Live Search Result Indicator -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="text-muted small mb-0">
                                Showing results for: <strong>{{ $search ?: 'All Companies' }}</strong>
                            </p>
                            <div wire:loading wire:target="search">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"
                                    aria-hidden="true"></span>
                                <span class="text-primary small">Searching...</span>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                    <th>Logo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->company_name }}</td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_email ?? '' }}')"
                                                onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                onmouseout="this.style.backgroundColor='transparent';"
                                                style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                title="Click to copy">
                                                {{ $row->company_email ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_mobile ?? '' }}')"
                                                onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                onmouseout="this.style.backgroundColor='transparent';"
                                                style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                title="Click to copy">
                                                {{ $row->company_mobile ?? 'N/A' }}
                                            </span>
                                        </td>



                                        <td>
                                            @if ($row->company_logo_url)
                                                <img src="{{ $row->company_logo_url }}" alt="Logo"
                                                    style="width:50px; height:auto;">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td> <a href="#" wire:click.prevent="toggleStatus({{ $row->id }})">
                                                {!! statusBadge($row->status) !!}
                                            </a></td>
                                        <td>
                                            <a data-bs-toggle="modal" data-bs-target="#editCompany"
                                                wire:click="edit({{ $row->id }})" class="badge badge-warning">
                                                Edit
                                            </a>
                                            {{-- Add Delete or other actions here if needed --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No companies found</td>
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

    <!-- Add Company Modal -->
    {{-- <div wire:ignore.self class="modal fade" id="addCompany" tabindex="-1" role="dialog" aria-labelledby="addCompany"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600" id="addCompany">Add Company</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times" style="color:black;"></i>
                    </button>
                </div>

                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" required class="form-control" placeholder="Enter Company Name"
                                    wire:model="company_name">
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Business Type</label>
                                <input type="text" class="form-control" placeholder="Enter Business Type"
                                    wire:model="business_type">
                                @error('business_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Address & Contact Info</label>
                                <textarea class="form-control" placeholder="Enter Address & Contact Info" wire:model="address_contact_info"></textarea>
                                @error('address_contact_info')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" wire:model="company_logo">
                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove>Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div wire:ignore.self class="modal fade" id="editCompany" tabindex="-1" role="dialog"
        aria-labelledby="editCompany" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600" id="editCompany">Edit Company</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times" style="color:black;"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="company_name">
                                @error('company_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Business Type</label>
                                <input type="text" class="form-control" wire:model="business_type">
                                @error('business_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Address & Contact Info</label>
                                <textarea class="form-control" wire:model="address_contact_info"></textarea>
                                @error('address_contact_info')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label">Company Logo</label>
                                <input type="file" class="form-control" wire:model="company_logo">
                                @error('company_logo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @if ($company_logo_preview)
                                    <img src="{{ $company_logo_preview }}" class="mt-2" style="width:50px;">
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i> Updating...
                            </span>
                            <span wire:loading.remove>Update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
</div>

<script>
    Livewire.on('confirmDelete', companyId => {
        if (confirm("Are you sure you want to delete this company? This action cannot be undone.")) {
            Livewire.dispatch('deleteCompany', {
                id: companyId
            });
        }
    });
</script>
<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => {
                alert("Copied: " + text);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    }
</script>
