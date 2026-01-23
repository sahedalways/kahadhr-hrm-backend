<div class="modal fade" id="openDocumentModal" data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Document</h5>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form>
                <input type="hidden" name="doc_type_id" wire:model="selectedDocTypeId">
                <input type="hidden" name="doc_id" wire:model="selectedDocId">

                <div class="modal-body">
                    <div class="row">

                        <!-- Upload Section -->
                        <div class="col-lg-4" style="{{ $selectedDocId ? 'display:none;' : 'display:block;' }}">
                            <label class="form-label">Upload PDF</label>
                            <input type="file" name="file_path" accept="application/pdf" class="form-control">

                            <div class="mt-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expires_at" class="form-control"
                                    wire:model="selectedExpiresAt">
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Comment</label>
                                <input type="text" name="comment" class="form-control" wire:model="selectedComment">
                            </div>
                        </div>

                        <!-- Existing Document Section -->
                        <div class="col-lg-4" style="{{ $selectedDocId ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control" wire:model="selectedExpiresAt"
                                readonly>

                            <label class="form-label mt-3">Comment</label>
                            <input type="text" name="comment" class="form-control" wire:model="selectedComment"
                                readonly>
                        </div>

                        <!-- PDF Preview -->
                        <div class="col-lg-8">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="border rounded p-2" style="height:70vh;">
                                <embed id="pdf_viewer" src="{{ $selectedFileUrl }}" width="100%" height="100%">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Buttons -->
                @if (auth()->user()->user_type !== 'superAdmin')
                    <div class="d-flex justify-content-center mt-3">
                        <button type="button" class="btn btn-danger text-center" wire:click="deleteDocument"
                            wire:loading.attr="disabled" wire:target="deleteDocument">
                            <span wire:loading.remove wire:target="deleteDocument">Delete</span>
                            <span class="spinner-border spinner-border-sm" wire:loading
                                wire:target="deleteDocument"></span>
                        </button>
                    </div>
                @endif


            </form>
        </div>
    </div>
</div>
