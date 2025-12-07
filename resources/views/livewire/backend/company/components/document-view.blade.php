<div class="modal fade" id="openDocumentModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Document</h5>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="closeDocumentBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form>
                <input type="hidden" name="doc_type_id" id="doc_type_id">
                <input type="hidden" name="doc_id" id="doc_id">

                <div class="modal-body">
                    <div class="row">

                        <!-- Upload Section -->
                        <div class="col-lg-4" id="upload_section">
                            <label class="form-label">Upload PDF</label>
                            <input type="file" name="file_path" accept="application/pdf" class="form-control">

                            <div class="mt-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expires_at" class="form-control">
                            </div>

                            <div class="mt-3">
                                <label class="form-label">Comment</label>
                                <input type="text" name="comment" class="form-control">
                            </div>
                        </div>

                        <!-- Existing Document Section -->
                        <div class="col-lg-4" id="existing_section" style="display:none;">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expires_at" class="form-control" readonly>

                            <label class="form-label mt-3">Comment</label>
                            <input type="text" name="comment" class="form-control" readonly>
                        </div>

                        <!-- PDF Preview -->
                        <div class="col-lg-8">
                            <label class="form-label fw-semibold">Preview</label>
                            <div class="border rounded p-2" style="height:70vh;">
                                <embed id="pdf_viewer" src="" width="100%" height="100%">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Buttons (Centered) -->
                @if (auth()->user()->user_type !== 'superAdmin')
                    <div class="modal-footer justify-content-center">
                        <!-- Delete Button -->
                        <button type="button" class="btn btn-danger" id="deleteDocumentBtn" style="display:none;">
                            <span id="deleteBtnText">Delete</span>
                            <span id="deleteBtnLoader" class="spinner-border spinner-border-sm d-none"></span>
                        </button>


                    </div>
                @endif


            </form>

        </div>
    </div>
</div>
