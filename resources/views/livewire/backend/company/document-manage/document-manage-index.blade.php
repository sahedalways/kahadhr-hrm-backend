<div>
    <div class="row align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-white m-0">Document Management</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportDocuments('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportDocuments('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportDocuments('csv')" class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#add" wire:click="resetInputFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Document
            </a>
        </div>
    </div>

    <!-- Search + Sort -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">

                            <!-- Search Input -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i
                                            class="bi bi-search"></i></span>
                                    <input type="text" class="form-control shadow-sm form-control-lg border-start-0"
                                        placeholder="Search by document name" wire:model="search"
                                        wire:keyup="set('search', $event.target.value)" />
                                </div>
                            </div>

                            <!-- Sort -->
                            <div class="col-md-3 d-flex gap-2">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <select class="form-select" wire:change="handleFilter($event.target.value)">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="signed">Signed</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <!-- Search Summary -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="text-muted small mb-0">
                                Showing results for: <strong>{{ $search ?: 'All Documents' }}</strong>
                            </p>
                            <div wire:loading wire:target="search">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                                <span class="text-primary small">Searching...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Document Name</th>
                                    <th>Employee Name</th>
                                    <th>File</th>
                                    <th>Expires At</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>

                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->name ?? '' }}')"
                                                style="cursor:pointer;" data-bs-toggle="tooltip" title="Click to copy">
                                                {{ $row->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ trim(($row->employee->f_name ?? '') . ' ' . ($row->employee->l_name ?? '')) ?: $row->employee->email ?? 'N/A' }}
                                        </td>



                                        <td>
                                            @if ($row->document_url)
                                                <a href="{{ $row->document_url }}" target="_blank"
                                                    class="text-primary">View</a>
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <td>{{ $row->expires_at ? date('d M, Y', strtotime($row->expires_at)) : 'N/A' }}
                                        </td>

                                        <td>
                                            @if ($row->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($row->status == 'signed')
                                                <span class="badge bg-success">Signed</span>
                                            @else
                                                <span class="badge bg-danger">Expired</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#edit"
                                                wire:click="edit({{ $row->id }})"
                                                class="badge badge-info badge-xs fw-600 text-xs"
                                                style="background-color:#4ba3f7;color:#fff;">
                                                Edit
                                            </a>

                                            <a href="#" class="badge badge-danger badge-xs fw-600 text-xs"
                                                wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No documents found</td>
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

    <!-- Add Modal -->
    <div wire:ignore.self class="modal fade" id="add" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Add New Document</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Document Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Assign Employee </label>
                                <select id="employeeSelect" class="form-select" wire:model.live="emp_id"
                                    wire:key="emp_id">
                                    <option value="" selected>-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ trim(($emp->f_name ?? '') . ' ' . ($emp->l_name ?? '')) ?: $emp->email }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('emp_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="col-md-12 mb-2">
                                <label class="form-label">File <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" wire:model="file_path"
                                    accept="application/pdf">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expires At</label>
                                <input type="date" class="form-control" wire:model="expires_at"
                                    min="{{ date('Y-m-d') }}">

                                @error('expires_at')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="save"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove wire:target="save">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Edit Modal (Same as Add) -->
    <div wire:ignore.self class="modal fade" id="edit" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Edit Document</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Document Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model="name">

                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Assign Employee </label>
                                <select id="employeeSelect" class="form-select" wire:model.live="emp_id"
                                    wire:key="emp_id">
                                    <option value="" selected>-- Select Employee --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ trim(($emp->f_name ?? '') . ' ' . ($emp->l_name ?? '')) ?: $emp->email }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('emp_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Replace File </label>
                                <input type="file" class="form-control" wire:model="file_path"
                                    accept="application/pdf">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (!empty($existingDocument))
                                <div class="mt-2">
                                    <a href="{{ $existingDocument }}" target="_blank"
                                        class="btn btn-primary btn-sm">
                                        View Previous Document
                                    </a>
                                </div>
                            @endif



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expires At</label>
                                <input type="date" class="form-control" wire:model="expires_at"
                                    min="{{ date('Y-m-d') }}">

                                @error('expires_at')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="status">
                                    <option value="pending">Pending</option>
                                    <option value="signed">Signed</option>
                                    <option value="expired">Expired</option>
                                </select>

                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="update"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove wire:target="update">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

<script>
    Livewire.on('confirmDelete', documentId => {
        if (confirm("Delete this document? This action cannot be undone.")) {
            Livewire.dispatch('deleteDocument', {
                id: documentId
            });
        }
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.log(err));
    }
</script>


<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>


<script>
    document.addEventListener('livewire:load', function() {
        const element = document.getElementById('employeeSelect');
        const choices = new Choices(element, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            maxItemCount: 1,
            position: 'bottom',
            placeholderValue: '-- Select Employee --',
            searchPlaceholderValue: 'Search employee...',
            renderChoiceLimit: 5 // visible items, baki scrollable
        });

        Livewire.hook('message.processed', (message, component) => {
            // re-initialize on update
            choices.destroy();
            new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                maxItemCount: 1,
                position: 'bottom',
                placeholderValue: '-- Select Employee --',
                searchPlaceholderValue: 'Search employee...',
                renderChoiceLimit: 5
            });
        });
    });
</script>
