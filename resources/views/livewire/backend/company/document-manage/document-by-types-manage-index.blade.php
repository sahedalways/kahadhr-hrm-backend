@php
    $id = request('id');
@endphp




<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Documents By Type</h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="exportDocuments('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportDocuments('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportDocuments('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#add"
               wire:click="resetInputFields"
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
                                           class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text"
                                           class="form-control border-start-0"
                                           placeholder="Search by document name"
                                           wire:model="search"
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

                            <div class="col-md-3">
                                <select class="form-select"
                                        wire:change="handleFilter($event.target.value)">
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
                            <div wire:loading
                                 wire:target="search">
                                <span class="spinner-border spinner-border-sm text-primary"></span>
                                <span class="text-primary small">Searching...</span>
                            </div>
                        </div>



                    </div>
                </div>



            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table -->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee Name</th>
                                    <th>Documents</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp

                                @forelse($infos as $employee)
                                    <tr class="{{ $id == $employee->id ? 'table-primary' : '' }}">
                                        <td>{{ $i++ }}</td>

                                        <td>
                                            {{ trim(($employee->f_name ?? '') . ' ' . ($employee->l_name ?? '')) ?: $employee->email ?? 'N/A' }}
                                        </td>

                                        <td class="text-start"
                                            style="width:260px; max-width:260px; white-space:nowrap;">

                                            @if ($employee->documents && $employee->documents->count() > 0)
                                                @php
                                                    $grouped = $employee->documents->groupBy(function ($doc) {
                                                        return $doc->documentType->name ?? 'Unknown Type';
                                                    });
                                                @endphp

                                                @foreach ($grouped as $type => $docs)
                                                    @php
                                                        // Latest 3 documents only
                                                        $latestDocs = $docs
                                                            ->sortByDesc('created_at')
                                                            ->take(3)
                                                            ->values();
                                                    @endphp

                                                    <div class="border rounded p-2 mb-2"
                                                         style="max-width:240px;">

                                                        <div class="mb-2">
                                                            <strong>{{ $type }}</strong>
                                                        </div>

                                                        <div class="d-flex align-items-center"
                                                             style="
                        overflow-x:auto;
                        overflow-y:hidden;
                        padding:16px 8px;
                        min-height:105px;
                        max-width:230px;
                        scrollbar-width:none;
                     ">

                                                            @foreach ($latestDocs as $index => $doc)
                                                                @php
                                                                    $colors = [
                                                                        '#4e73df',
                                                                        '#1cc88a',
                                                                        '#36b9cc',
                                                                        '#f6c23e',
                                                                        '#e74a3b',
                                                                    ];
                                                                    $currentColor = $colors[$index % count($colors)];
                                                                    $extension = pathinfo(
                                                                        $doc->file_path,
                                                                        PATHINFO_EXTENSION,
                                                                    );
                                                                    $expiresAt = $doc->expires_at
                                                                        ? \Carbon\Carbon::parse($doc->expires_at)
                                                                        : null;

                                                                    $isExpired = $expiresAt && $expiresAt->isPast();

                                                                    $isExpiringSoon =
                                                                        $expiresAt &&
                                                                        !$isExpired &&
                                                                        $expiresAt->isFuture() &&
                                                                        $expiresAt->lte(now()->addDays(60));

                                                                @endphp

                                                                <div class="doc-card-wrapper"
                                                                     style="
                                position:relative;
                                min-width:90px;
                                margin-right:-70px;
                                z-index:{{ $index }};
                                transition:all 0.4s cubic-bezier(0.165,0.84,0.44,1);
                                cursor:pointer;
                             "
                                                                     onmouseover="this.style.zIndex='999';this.style.transform='translateY(-12px) scale(1.08)';this.style.marginRight='10px';"
                                                                     onmouseout="this.style.zIndex='{{ $index }}';this.style.transform='translateY(0) scale(1)';this.style.marginRight='-70px';"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#documentModal"
                                                                     wire:click="openDocModal({{ $doc->id }})">

                                                                    <div
                                                                         style="
                                background:white;
                                border-radius:10px;
                                padding:10px;
                                box-shadow:-5px 0 12px rgba(0,0,0,0.08);
                                border-top:3px solid {{ $currentColor }};
                                text-align:center;
                                border:1px solid #eee;
                            ">

                                                                        <!-- Icon -->
                                                                        <div
                                                                             style="
                                    width:36px;
                                    height:36px;
                                    background:{{ $currentColor }}15;
                                    color:{{ $currentColor }};
                                    border-radius:50%;
                                    display:flex;
                                    align-items:center;
                                    justify-content:center;
                                    margin:0 auto 6px;
                                    font-size:1.1rem;
                                ">
                                                                            @if (in_array($extension, ['jpg', 'png', 'jpeg']))
                                                                                <i class="bi bi-image"></i>
                                                                            @elseif ($extension === 'pdf')
                                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                                            @else
                                                                                <i class="bi bi-file-earmark-text"></i>
                                                                            @endif
                                                                        </div>

                                                                        <!-- Filename -->
                                                                        <div
                                                                             style="font-weight:700;font-size:0.65rem;color:#333;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                                            File-{{ $index + 1 }}.{{ $extension }}
                                                                        </div>

                                                                        <!-- Expiry -->
                                                                        <div style="font-size:0.6rem;margin-top:4px;">
                                                                            <div
                                                                                 style="color:{{ $isExpired ? '#dc3545' : ($isExpiringSoon ? '#fd7e14' : '#999') }};">
                                                                                {{ $doc->expires_at ? date('d M, Y', strtotime($doc->expires_at)) : 'No Expiry' }}
                                                                            </div>

                                                                            @if ($isExpired)
                                                                                <span class="badge bg-danger mt-1"
                                                                                      style="font-size:0.55rem;">Expired</span>
                                                                            @elseif ($isExpiringSoon)
                                                                                <span class="badge bg-warning text-dark mt-1"
                                                                                      style="font-size:0.55rem;">Expires
                                                                                    Soon</span>
                                                                            @endif
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>



                                                    </div>
                                                @endforeach

                                                <style>
                                                    .d-flex::-webkit-scrollbar {
                                                        display: none;
                                                    }
                                                </style>

                                                <link rel="stylesheet"
                                                      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
                                            @else
                                                <span class="text-muted">No documents found</span>
                                            @endif
                                        </td>



                                        <td>

                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="text-center">No employees found</td>
                                    </tr>
                                @endforelse
                            </tbody>



                        </table>

                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore"
                                        class="btn btn-outline-primary rounded-pill px-4 py-2">
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
    <div wire:ignore.self
         class="modal fade"
         id="add"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Add New Document</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Document Name </label>
                                <input type="text"
                                       class="form-control"
                                       wire:model="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Employee </label>
                                <select id="employeeSelect"
                                        class="form-select"
                                        wire:model.live="emp_id"
                                        wire:key="emp_id">
                                    <option value=""
                                            selected>-- Select Employee --</option>
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
                                <input type="file"
                                       class="form-control"
                                       wire:model="file_path"
                                       accept="application/pdf">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expires At</label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="expires_at"
                                       min="{{ date('Y-m-d') }}">

                                @error('expires_at')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-check mb-1">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model.live="send_email"
                                       id="emailCheck">

                                <label class="form-check-label fw-bold"
                                       for="emailCheck">
                                    Send employee an email notification
                                </label>
                            </div>


                            @if ($emailGatewayMissing)
                                <div class="small text-danger mt-1">
                                    ⚠️ Email notification is not configured yet.
                                    Please set up your email gateway from
                                    <a href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}"
                                       class="text-decoration-underline fw-semibold">
                                        Settings → Mail Settings
                                    </a>
                                </div>
                            @endif



                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="save">
                            <span wire:loading
                                  wire:target="save"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove
                                  wire:target="save">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <!-- Edit Modal (Same as Add) -->
    <div wire:ignore.self
         class="modal fade"
         id="edit"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Edit Document</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="row g-2">

                            <div class="col-md-12 mb-2">
                                <label class="form-label">Document Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       wire:model="name">

                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Assign Employee </label>
                                <select id="employeeSelect"
                                        class="form-select"
                                        wire:model.live="emp_id"
                                        wire:key="emp_id">
                                    <option value=""
                                            selected>-- Select Employee --</option>
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
                                <input type="file"
                                       class="form-control"
                                       wire:model="file_path"
                                       accept="application/pdf">
                                @error('file_path')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if (!empty($existingDocument))
                                <div class="mt-2">
                                    <a href="{{ $existingDocument }}"
                                       target="_blank"
                                       class="btn btn-primary btn-sm">
                                        View Previous Document
                                    </a>
                                </div>
                            @endif



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Expires At</label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="expires_at"
                                       min="{{ date('Y-m-d') }}">

                                @error('expires_at')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>



                            <div class="col-md-12 mb-2">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select"
                                        wire:model="status">
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
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">Cancel</button>
                        <button type="submit"
                                class="btn btn-success"
                                wire:loading.attr="disabled"
                                wire:target="save">
                            <span wire:loading
                                  wire:target="update"><i class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove
                                  wire:target="update">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div wire:ignore.self
         class="modal fade"
         id="documentModal"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $modalDocument->name ?? 'Document' }}
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($modalDocument && $modalDocument->file_path)
                        <iframe src="{{ asset('storage/' . $modalDocument->file_path) }}"
                                style="width:100%; height:500px;"></iframe>
                    @else
                        <p>No file found</p>
                    @endif
                </div>

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

    document.addEventListener('livewire:load', function() {
        Livewire.on('documentModalOpened', () => {
            var modal = new bootstrap.Modal(document.getElementById('documentModal'));
            modal.show();
        });
    });
</script>
