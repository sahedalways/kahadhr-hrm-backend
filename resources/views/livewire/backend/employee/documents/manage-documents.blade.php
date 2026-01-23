@php
    $id = request('id');
@endphp



<div class="row">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
                <!-- Title -->
                <h4 class="fw-bold mb-0 d-flex align-items-center">
                    <i class="fas fa-folder-open me-2"></i> Manage Documents
                </h4>


                <div class="d-flex flex-row gap-2 align-items-center">
                    <!-- Document Type Filter -->
                    <select class="form-select form-select-lg"
                            style="min-width: 220px;"
                            wire:change="filterByType($event.target.value)">
                        <option value="">All Types</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    {{-- <select class="form-select form-select-lg"
                            style="min-width: 180px;"
                            wire:change="handleFilter($event.target.value)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select> --}}
                </div>


            </div>


            <p class="mb-0">View and upload documents for each category. Click a PDF to view or the plus
                button to upload.</p>
        </div>
    </div>
    @php
        $hasDocuments = $documents->isNotEmpty();
    @endphp



    <div class="row g-4">
        @foreach ($this->filteredDocumentTypes as $type)
            @if (auth()->user()->employee->nationality === 'British' && $type->name === 'Share Code')
                @continue
            @endif

            <div class="col-lg-3 col-md-4">


                <div class="card shadow-sm rounded-3 h-100
    {{ $id == $type->id ? 'border border-2 border-danger' : 'border-0' }}"
                     style="
        {{ $id == $type->id ? 'background:#fff5f5; box-shadow:0 0 0 2px rgba(220,53,69,.25);' : '' }}
     "
                     wire:ignore.self>
                    <!-- Card header / type name -->
                    <div class="card-header bg-light text-primary fw-semibold d-flex align-items-center">
                        <i class="fas fa-folder me-2"></i> {{ $type->name }}
                    </div>
                    <div class="card-body">

                        @php
                            $docsForType = $documents->where('doc_type_id', $type->id)->sortByDesc('created_at');

                            $latestDocs = $docsForType->take(3)->values();
                        @endphp

                        @if ($latestDocs->count())
                            @if ($latestDocs->count())
                                <div class="d-flex align-items-center">

                                    {{-- Scrollable docs --}}
                                    <div class="d-flex align-items-center"
                                         style="
                overflow-x:auto;
                overflow-y:hidden;
                padding:14px 6px;
                min-height:105px;
                scrollbar-width:none;
                white-space:nowrap;
                flex: 1 1 auto;
             ">

                                        @foreach ($latestDocs as $index => $doc)
                                            @php
                                                $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
                                                $currentColor = $colors[$index % count($colors)];
                                                $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                                $expiresAt = $doc->expires_at
                                                    ? \Carbon\Carbon::parse($doc->expires_at)
                                                    : null;
                                                $isExpired = $expiresAt && $expiresAt->isPast();
                                                $zIndex = count($latestDocs) - $index;
                                            @endphp

                                            <div style="
                    position:relative;
                    min-width:90px;
                    margin-right:-70px;
                    z-index:{{ $zIndex }};
                    transition:all .35s cubic-bezier(.165,.84,.44,1);
                    cursor:pointer;
                    display:inline-block;
                "
                                                 onmouseover="this.style.transform='translateY(-10px) scale(1.08)';this.style.zIndex=99;this.style.marginRight='10px';"
                                                 onmouseout="this.style.transform='';this.style.zIndex='{{ $zIndex }}';this.style.marginRight='-70px';"
                                                 wire:click="openDocModal({{ $doc->id }}, {{ $index + 1 }})">

                                                <div
                                                     style="
                        background:#fff;
                        border-radius:10px;
                        padding:10px;
                        text-align:center;
                        border-top:3px solid {{ $currentColor }};
                        box-shadow:-4px 0 12px rgba(0,0,0,.08);
                        border:1px solid #eee;
                    ">

                                                    <!-- icon -->
                                                    <div
                                                         style="
                            width:34px;
                            height:34px;
                            background:{{ $currentColor }}15;
                            color:{{ $currentColor }};
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            margin:0 auto 6px;
                            font-size:1.05rem;
                        ">
                                                        @if (in_array($ext, ['jpg', 'png', 'jpeg']))
                                                            <i class="bi bi-image"></i>
                                                        @elseif ($ext === 'pdf')
                                                            <i class="bi bi-file-earmark-pdf"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark-text"></i>
                                                        @endif
                                                    </div>

                                                    <!-- filename -->
                                                    <div
                                                         style="
                            font-weight:700;
                            font-size:.65rem;
                            color:#333;
                            white-space:nowrap;
                            overflow:hidden;
                            text-overflow:ellipsis;
                        ">
                                                        File-{{ $index + 1 }}
                                                    </div>

                                                    <!-- expiry -->
                                                    <div style="font-size:.6rem;margin-top:4px;">
                                                        <span style="color:{{ $isExpired ? '#dc3545' : '#999' }}">
                                                            {{ $doc->expires_at ? $expiresAt->format('d M, Y') : 'No Expiry' }}
                                                        </span>
                                                    </div>

                                                    @if ($isExpired)
                                                        <span class="badge bg-danger mt-1"
                                                              style="font-size:.55rem;">
                                                            Expired
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>
                                        @endforeach
                                    </div>


                                    <div
                                         style="
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 60px;
    margin-left: 80px;
    flex-shrink: 0;
    padding-left: 10px;
">
                                        <button type="button"
                                                wire:click="openUploadModal({{ $type->id }})"
                                                style="
                width: 42px;
                height: 42px;
                border-radius: 12px;
                border: 2px dashed #4e73df;
                background: #f8f9fc;
                color: #4e73df;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                cursor: pointer;
            "
                                                onmouseover="this.style.background='#4e73df'; this.style.color='#fff'; this.style.borderStyle='solid'; this.style.transform='scale(1.1)';"
                                                onmouseout="this.style.background='#f8f9fc'; this.style.color='#4e73df'; this.style.borderStyle='dashed'; this.style.transform='scale(1)';">
                                            <i class="bi bi-plus-lg"
                                               style="font-size: 1.2rem;"></i>
                                        </button>
                                    </div>
                                </div>

                                <style>
                                    .d-flex::-webkit-scrollbar {
                                        display: none;
                                    }
                                </style>

                                <link rel="stylesheet"
                                      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
                            @endif
                        @else
                            {{-- Empty state (same behavior, better look) --}}
                            @php
                                $employee = auth()->user()->employee;
                                $hasShareCode = !empty($employee->share_code);
                            @endphp

                            <div class="d-flex justify-content-center align-items-center"
                                 style="height:90px; cursor:pointer;"
                                 wire:click="openUploadModal({{ $type->id }})">

                                @if ($type->name === 'Share Code' && $hasShareCode)
                                    <div class="text-center">
                                        <div class="fw-semibold text-primary">
                                            {{ $employee->share_code }}
                                        </div>
                                        <div class="small text-muted">
                                            Pending Verification
                                        </div>
                                    </div>
                                @else
                                    <span class="fw-bold text-primary fs-4">+</span>
                                @endif
                            </div>
                        @endif

                    </div>

                </div>


            </div>
        @endforeach
    </div>



    <div wire:ignore.self
         class="modal fade"
         id="documentModal"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">
                            File-{{ $modalFileIndex ?? '' }}
                        </h5>


                        @if ($modalDocument && $modalDocument->expires_at)
                            <div style="font-size: 12px; color: #6c757d;">
                                Expires on:
                                <span
                                      style="color: {{ \Carbon\Carbon::parse($modalDocument->expires_at)->isPast() ? '#dc3545' : '#333' }};">
                                    {{ \Carbon\Carbon::parse($modalDocument->expires_at)->format('d M, Y') }}
                                </span>
                            </div>
                        @else
                            <div style="font-size: 12px; color: #6c757d;">
                                No Expiry
                            </div>
                        @endif
                    </div>

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




    <div wire:ignore.self
         class="modal fade"
         id="openUploadModal"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content"
                 x-data="{ dragover: false, fileUrl: null }"
                 x-init="window.addEventListener('reset-file-url', () => { fileUrl = null });"
                 x-on:dragover.prevent="dragover = true"
                 x-on:dragleave.prevent="dragover = false"
                 x-on:drop.prevent="
                 dragover = false;
                 if ($event.dataTransfer.files.length > 0) {
                     let file = $event.dataTransfer.files[0];
                     if (file.type !== 'application/pdf') { alert('Only PDF files are allowed.'); return; }
                     fileUrl = URL.createObjectURL(file);
                     $wire.upload('file_path', file);
                 }
             ">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if ($selectedDocName === 'Share Code')
                            <i class="fas fa-user-check me-2"></i>
                            {{ $existingDocument ? 'View Share Code' : 'Add Share Code' }}
                        @else
                            <i class="fas fa-file-alt me-2"></i>
                            {{ $existingDocument ? 'View Document' : 'Upload Document' }}
                        @endif
                    </h5>

                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>


                <form wire:submit.prevent="saveDocument">

                    @if ($selectedDocName === 'Share Code')

                        <div class="modal-body">
                            <div class="row justify-content-center">
                                <div class="col-lg-6">

                                    <div class="alert alert-info mb-4 text-center text-white">
                                        <strong>Note:</strong>
                                        Please enter your Share Code and Date of Birth.
                                        No document upload is required.
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Share Code <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control text-uppercase"
                                               wire:model.lazy="share_code"
                                               placeholder="Example: WLE JFZ 6FT">
                                        @error('share_code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">
                                            Date of Birth <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               class="form-control"
                                               wire:model="date_of_birth"
                                               max="{{ date('Y-m-d') }}">
                                        @error('date_of_birth')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="button"
                                    class="btn btn-secondary"
                                    wire:click="$dispatch('closemodal')">
                                Close
                            </button>

                            <button type="submit"
                                    class="btn btn-primary"
                                    wire:loading.attr="disabled"
                                    wire:target="saveDocument">
                                <span wire:loading
                                      wire:target="saveDocument">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Saving...
                                </span>
                                <span wire:loading.remove
                                      wire:target="saveDocument">
                                    Save Share Code
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-lg-4 mb-3">

                                    <div class="border border-dashed rounded p-4 text-center"
                                         :class="dragover ? 'border-primary bg-light' : ''"
                                         style="cursor:pointer; height:200px; display:flex; flex-direction:column; justify-content:center; align-items:center;"
                                         x-on:click="$refs.fileInput.click()">

                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Drag & Drop your file here or click to select
                                        </div>

                                        <input type="file"
                                               x-ref="fileInput"
                                               class="d-none"
                                               accept="application/pdf"
                                               x-on:change="fileUrl = URL.createObjectURL($refs.fileInput.files[0]);"
                                               wire:model="file_path">
                                    </div>

                                    @error('file_path')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                    @if ($file_path)
                                        <div class="mt-2 small text-success">
                                            {{ $file_path->getClientOriginalName() }}
                                        </div>
                                    @endif

                                    @if ($file_path)
                                        <div class="row mt-3">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Expires At <span
                                                          class="text-danger">*</span></label>
                                                <input type="date"
                                                       class="form-control"
                                                       wire:model="expires_at"
                                                       min="{{ date('Y-m-d') }}">
                                                @error('expires_at')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif

                                </div>

                                <div class="col-lg-8"
                                     wire:key="preview-{{ $selectedType }}-{{ optional($existingDocument)->id }}">
                                    <label class="form-label fw-semibold">Document Preview</label>
                                    <div class="border rounded p-2"
                                         style="height: 70vh; overflow: auto;">

                                        <template x-if="fileUrl">
                                            <embed :src="fileUrl"
                                                   type="application/pdf"
                                                   width="100%"
                                                   height="100%"></embed>
                                        </template>

                                        <template x-if="!fileUrl && @js($existingDocument)">
                                            <embed src="{{ $existingDocument ? $existingDocument->document_url : '' }}"
                                                   type="application/pdf"
                                                   width="100%"
                                                   height="100%"></embed>
                                        </template>

                                        <template x-if="!fileUrl && !@js($existingDocument)">
                                            <div class="border rounded p-4 text-center text-muted"
                                                 style="height: 100%;">
                                                No document selected
                                            </div>
                                        </template>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button type="button"
                                    class="btn btn-secondary"
                                    wire:click="$dispatch('closemodal')">
                                Close
                            </button>

                            <button type="submit"
                                    class="btn btn-success"
                                    wire:loading.attr="disabled"
                                    wire:target="saveDocument"
                                    @if (!$file_path) disabled @endif>
                                <span wire:loading
                                      wire:target="saveDocument">
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Uploading...
                                </span>
                                <span wire:loading.remove
                                      wire:target="saveDocument">
                                    Upload
                                </span>
                            </button>
                        </div>

                    @endif

                </form>

            </div>
        </div>
    </div>

</div>


<script>
    window.addEventListener('openUploadModal', function() {
        let modalEl = document.getElementById('openUploadModal');
        if (!modalEl) return;

        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });


    window.addEventListener('documentModalOpened', function() {
        let modalEl = document.getElementById('documentModal');
        if (!modalEl) return;

        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });
</script>


<script>
    window.addEventListener('clear-notification-route', function() {
        const url = new URL(window.location.href);


        if (url.searchParams.has('id')) {
            url.searchParams.delete('id');
            window.history.replaceState({}, document.title, url.toString());

            setTimeout(function() {
                window.location.reload();
            }, 1000);
        }
    });
</script>
