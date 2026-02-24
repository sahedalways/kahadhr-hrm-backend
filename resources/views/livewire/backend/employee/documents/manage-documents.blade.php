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

                    @php
                        $shareStatus = auth()->user()->employee->share_code_status ?? 'unavailable';
                    @endphp





                    <div
                         class="card-header bg-light text-primary fw-semibold d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-folder me-2"></i> {{ $type->name }}
                        </div>

                        @if (strtolower($type->name) === 'share code')
                            <div>
                                @if ($shareStatus === 'unavailable')
                                    <span class="badge bg-secondary">Unavailable</span>
                                @elseif ($shareStatus === 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif ($shareStatus === 'verified')
                                    <span class="badge bg-success">Verified</span>
                                @elseif ($shareStatus === 'expired')
                                    <span class="badge bg-danger">Expired</span>
                                @endif
                            </div>
                        @endif
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



                                                </div>
                                            </div>
                                        @endforeach
                                    </div>





                                    @php
                                        $docsCount = $docsForType->count();
                                        $isShareCode = strtolower($type->name) === 'share code';
                                    @endphp

                                    @if ($isShareCode || $docsCount < 3)
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
                                                <i class="fas fa-plus"
                                                   style="font-size: 1.2rem;"></i>

                                            </button>
                                        </div>
                                    @endif






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
                                $shareStatus = $employee->share_code_status ?? 'unavailable';
                            @endphp

                            <div class="d-flex justify-content-center align-items-center"
                                 style="height:90px; cursor:pointer;"
                                 wire:click="openUploadModal({{ $type->id }})">

                                @if ($type->name === 'Share Code')
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div class="flex-grow-1">
                                            @if ($shareStatus === 'unavailable')
                                                {{-- only plus --}}
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
                                                    <i class="fas fa-plus"
                                                       style="font-size: 1.2rem;"></i>
                                                </button>
                                            @elseif ($shareStatus === 'pending')
                                                {{-- show code + pending --}}
                                                <div class="text-center"
                                                     style="cursor:pointer;"
                                                     wire:click="openUploadModal({{ $type->id }})">
                                                    <div
                                                         style="font-weight: 800; font-size: 1.1rem; color: #4e73df; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">
                                                        {{ $employee->share_code }}
                                                    </div>

                                                    <div
                                                         style="display:inline-flex; align-items:center; gap:5px; background:#fff3cd; color:#856404; padding:4px 10px; border-radius:50px; font-size:.65rem; font-weight:600; border:1px solid #ffeeba;">
                                                        <span
                                                              style="height:6px; width:6px; background:#856404; border-radius:50%; display:inline-block; animation:pulse 1.5s infinite;"></span>
                                                        Pending Verification
                                                    </div>
                                                </div>
                                            @elseif ($shareStatus === 'verified')
                                                {{-- show code + change --}}
                                                <div class="text-center"
                                                     style="cursor:pointer;"
                                                     wire:click="openUploadModal({{ $type->id }})">
                                                    <div
                                                         style="font-weight: 800; font-size: 1.1rem; color: #4e73df; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">
                                                        {{ $employee->share_code }}
                                                    </div>

                                                    <div
                                                         style="display:inline-flex; align-items:center; gap:5px; background:#d1e7dd; color:#0f5132; padding:4px 10px; border-radius:50px; font-size:.65rem; font-weight:600; border:1px solid #badbcc;">
                                                        <i class="fas fa-edit"></i>
                                                        Change Share Code
                                                    </div>
                                                </div>
                                            @elseif ($shareStatus === 'expired')
                                                {{-- show code + re-upload --}}
                                                <div class="text-center"
                                                     style="cursor:pointer;"
                                                     wire:click="openUploadModal({{ $type->id }})">
                                                    <div
                                                         style="font-weight: 800; font-size: 1.1rem; color: #dc3545; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px;">
                                                        {{ $employee->share_code }}
                                                    </div>

                                                    <div
                                                         style="display:inline-flex; align-items:center; gap:5px; background:#f8d7da; color:#842029; padding:4px 10px; border-radius:50px; font-size:.65rem; font-weight:600; border:1px solid #f5c2c7;">
                                                        <i class="fas fa-redo"></i>
                                                        Update New Share Code
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @else
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
                                            <i class="fas fa-plus"
                                               style="font-size: 1.2rem;"></i>

                                        </button>
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
                    <div class="d-flex align-items-start flex-column">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h5 class="modal-title"
                                style="font-weight: 800; color: #2d3748; letter-spacing: -0.5px; margin: 0;">
                                File-{{ $modalFileIndex ?? '' }}
                            </h5>

                            <span
                                  style="background: #edf2f7; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; color: #4a5568; text-transform: uppercase;">
                                DOC
                            </span>
                        </div>

                        <div class="d-flex align-items-center">
                            @if ($modalDocument && $modalDocument->expires_at)
                                @php
                                    $expiry = \Carbon\Carbon::parse($modalDocument->expires_at);
                                    $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                                @endphp

                                <div
                                     style="
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 4px 12px;
                border-radius: 8px;
                font-size: 0.75rem;
                font-weight: 600;
                @if ($daysLeft < 0) background: #fff5f5; color: #c53030; border: 1px solid #feb2b2;
                @elseif($daysLeft === 0) background: #fffaf0; color: #975a16; border: 1px solid #fbd38d;
                @else background: #f0fff4; color: #276749; border: 1px solid #9ae6b4; @endif
            ">
                                    <span
                                          style="
                    height: 8px; width: 8px; border-radius: 50%;
                    @if ($daysLeft < 0) background: #c53030;
                    @elseif($daysLeft === 0) background: #975a16;
                    @else background: #276749; @endif
                "></span>

                                    @if ($daysLeft < 0)
                                        Expired ({{ abs($daysLeft) }} days ago)
                                    @elseif ($daysLeft === 0)
                                        Expires Today
                                    @else
                                        Expires in {{ $daysLeft }} days
                                    @endif
                                </div>
                            @else
                                <div
                                     style="display: flex; align-items: center; gap: 5px; color: #718096; font-size: 0.75rem; font-weight: 500;">
                                    <i class="bi bi-calendar-x"
                                       style="font-size: 0.85rem;"></i>
                                    No Expiry Set
                                </div>
                            @endif
                        </div>
                    </div>


                    <button type="button"
                            class="btn btn-light rounded-pill"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body p-0">

                    <div class="container-fluid">
                        <div class="row g-0"
                             style="min-height: 70vh;">

                            {{-- LEFT SIDE : Fields --}}
                            <div class="col-md-4 p-3 border-end"
                                 style="background:#f8fafc;">


                                {{-- Document Type --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Document Type </label>
                                    <div class="form-control form-control-sm">
                                        {{ $documentTypes->firstWhere('id', $doc_type_id)->name ?? 'N/A' }}
                                    </div>
                                </div>






                            </div>

                            {{-- RIGHT SIDE : Document Preview --}}
                            <div class="col-md-8 p-0">

                                @if ($modalDocument && $modalDocument->file_path)
                                    <iframe src="{{ asset('storage/' . $modalDocument->file_path) }}"
                                            style="width:100%; height:100%; min-height:70vh; border:0;">
                                    </iframe>
                                @else
                                    <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                                        No file found
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>


                <div class="modal-footer d-flex justify-content-center align-items-center">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Close
                    </button>
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
                            aria-label="Close"
                            wire:click="resetSelectedType">
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
                                               accept="application/pdf, image/*"
                                               x-on:change="
            file = $refs.fileInput.files[0];
            if(file){
                fileUrl = URL.createObjectURL(file);
            } else {
                fileUrl = null;
            }
        "
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
                                                <label class="form-label">Expires At </label>
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
