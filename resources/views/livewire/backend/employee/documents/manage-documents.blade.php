<div class="row">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-3">
                <!-- Title -->
                <h4 class="fw-bold text-white mb-0 d-flex align-items-center">
                    <i class="fas fa-folder-open me-2"></i> Manage Documents
                </h4>


                <div class="d-flex flex-row gap-2 align-items-center">
                    <!-- Document Type Filter -->
                    <select class="form-select form-select-lg" style="min-width: 220px;"
                        wire:change="filterByType($event.target.value)">
                        <option value="">All Types</option>
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select class="form-select form-select-lg" style="min-width: 180px;"
                        wire:change="handleFilter($event.target.value)">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>


            </div>


            <p class="text-white mb-0">View and upload documents for each category. Click a PDF to view or the plus
                button to upload.</p>
        </div>
    </div>
    @php
        $hasDocuments = $documents->isNotEmpty();
    @endphp



    <div class="row g-4">
        @foreach ($this->filteredDocumentTypes as $type)
            <div class="col-md-3">
                <div class="card shadow-sm border-0 rounded-3 h-100">
                    <!-- Card header / type name -->
                    <div class="card-header bg-light text-primary fw-semibold d-flex align-items-center">
                        <i class="fas fa-folder me-2"></i> {{ $type->name }}
                    </div>
                    <div class="card-body d-flex flex-column">

                        @php
                            $docsForType = $documents->where('doc_type_id', $type->id);
                        @endphp

                        <!-- Documents -->
                        <div class="mb-3 flex-grow-1">
                            @foreach ($documents->where('doc_type_id', $type->id) as $doc)
                                <div class="shadow-sm rounded p-3 mb-2 border position-relative"
                                    wire:key="doc-{{ $doc->id }}"
                                    style="cursor:pointer; background-color:#f9f9f9; transition: all 0.3s ease;"
                                    wire:click="openUploadModal({{ $doc->doc_type_id }})" data-bs-toggle="modal"
                                    data-bs-target="#openUploadModal"
                                    onmouseover="this.style.backgroundColor='#e6f0ff';"
                                    onmouseout="this.style.backgroundColor='#f9f9f9';">

                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf text-primary"
                                            style="font-size:28px; margin-right:10px;"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold text-truncate" style="max-width: 200px;">
                                                {{ $doc->name ?? 'Document' }}
                                            </div>
                                            <div class="small text-muted mt-1">
                                                Expiry:
                                                <span
                                                    class="{{ $doc->expires_at && \Carbon\Carbon::parse($doc->expires_at)->isPast() ? 'text-danger' : '' }}">
                                                    {{ $doc->expires_at ? \Carbon\Carbon::parse($doc->expires_at)->format('d M, Y') : 'No Expiry' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($doc->expires_at && \Carbon\Carbon::parse($doc->expires_at)->isPast())
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Expired</span>
                                    @endif

                                </div>
                            @endforeach
                        </div>


                        <!-- Smaller + button at bottom (show only if no documents) -->
                        @if ($docsForType->isEmpty())
                            <div class="d-flex justify-content-center" style="margin-top: 0;">
                                <div class="shadow-sm rounded p-2 bg-light border-dashed d-inline-flex justify-content-center align-items-center"
                                    style="cursor:pointer; width:170px; height:60px; transition: all 0.3s ease;"
                                    wire:click="openUploadModal({{ $type->id }})" data-bs-toggle="modal"
                                    data-bs-target="#openUploadModal"
                                    onmouseover="this.style.transform='scale(1.05)'; this.style.backgroundColor='#e9f5ff';"
                                    onmouseout="this.style.transform='scale(1)'; this.style.backgroundColor='#f8f9fa';">
                                    <span class="fw-bold text-primary fs-4">+</span>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
            </div>
        @endforeach
    </div>




    <div wire:ignore.self class="modal fade" id="openUploadModal" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" x-data="{ dragover: false, fileUrl: null }" x-init="window.addEventListener('reset-file-url', () => { fileUrl = null });"
                x-on:dragover.prevent="dragover = true" x-on:dragleave.prevent="dragover = false"
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
                        {{ $existingDocument ? 'View Document' : 'Upload Document' }}
                    </h5>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>


                <form wire:submit.prevent="saveDocument">
                    <div class="modal-body">
                        <div class="row">
                            <!-- Left: Drag & Drop -->

                            @if (!$existingDocument)
                                <div class="col-lg-4 mb-3">

                                    <div class="border border-dashed rounded p-4 text-center"
                                        :class="dragover ? 'border-primary bg-light' : ''"
                                        style="cursor:pointer; height:200px; display:flex; flex-direction:column; justify-content:center; align-items:center;"
                                        x-on:click="$refs.fileInput.click()">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                        <div class="small text-muted">Drag & Drop your file here or click to select
                                        </div>
                                        <input type="file" x-ref="fileInput" class="d-none" accept="application/pdf"
                                            x-on:change="
                                           fileUrl = URL.createObjectURL($refs.fileInput.files[0]);
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
                                                <label class="form-label">Expires At <span
                                                        class="text-danger">*</span></label>
                                                <input type="date" class="form-control" wire:model="expires_at"
                                                    min="{{ date('Y-m-d') }}">
                                                @error('expires_at')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="col-12 mb-3">
                                                <label class="form-label">Comment <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" wire:model="comment">
                                                @error('comment')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="col-lg-4 mb-3">
                                    <div class="row mt-3">
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Expires At </label>
                                            <input type="date" class="form-control" wire:model="expires_at"
                                                min="{{ date('Y-m-d') }}" readonly>
                                            @error('expires_at')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-12 mb-3">
                                            <label class="form-label">Comment </label>
                                            <input type="text" class="form-control" wire:model="comment" readonly>
                                            @error('comment')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif


                            <!-- Right: PDF Preview -->
                            <div class="col-lg-8"
                                wire:key="preview-{{ $selectedType }}-{{ optional($existingDocument)->id }}">
                                <label class="form-label fw-semibold">Document Preview</label>
                                <div class="border rounded p-2" style="height: 70vh; overflow: auto;">

                                    <!-- Show newly uploaded file if exists -->
                                    <template x-if="fileUrl">
                                        <embed :src="fileUrl" type="application/pdf" width="100%"
                                            height="100%"></embed>
                                    </template>

                                    <!-- Show existing document if no new file selected -->
                                    <template x-if="!fileUrl && @js($existingDocument)">
                                        <embed src="{{ $existingDocument ? $existingDocument->document_url : '' }}"
                                            type="application/pdf" width="100%" height="100%"></embed>
                                    </template>

                                    <!-- Placeholder if nothing exists -->
                                    <template x-if="!fileUrl && !@js($existingDocument)">
                                        <div class="border rounded p-4 text-center text-muted" style="height: 100%;">
                                            No document selected
                                        </div>
                                    </template>

                                </div>
                            </div>

                        </div>
                    </div>
                    @if (!$existingDocument)
                        <div class="d-flex justify-content-center mt-3">
                            <template x-if="fileUrl">
                                <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                    wire:target="saveDocument">
                                    <span wire:loading wire:target="saveDocument">
                                        <i class="fas fa-spinner fa-spin me-2"></i>
                                        {{ $existingDocument ? 'Replacing...' : 'Uploading...' }}
                                    </span>
                                    <span wire:loading.remove wire:target="saveDocument">
                                        {{ $existingDocument ? 'Replace Document' : 'Upload' }}
                                    </span>
                                </button>
                            </template>
                        </div>
                    @endif

                </form>
            </div>
        </div>
    </div>

</div>
