<div>
    <div wire:ignore.self
         class="modal fade"
         id="techSupportModal"
         tabindex="-1"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"
                 style="border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">

                <div class="modal-header"
                     style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border-bottom: none; padding: 1.5rem;">
                    <h5 class="modal-title w-100 text-center fw-bold text-uppercase tracking-wide text-white"
                        id="techSupportModalLabel"
                        style="letter-spacing: 1px;">
                        Technical Support Form
                    </h5>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="modal-body px-4 py-4"
                     style="background-color: #f8fafc;">



                    <form wire:submit.prevent="submitSupportRequest"
                          enctype="multipart/form-data">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Company Name <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       wire:model.defer="company_name"
                                       class="form-control custom-input"
                                       placeholder="e.g. Acme Corp">
                                @error('company_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Company House Number <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       wire:model.defer="house_number"
                                       class="form-control custom-input"
                                       placeholder="e.g. 123 Business Way">
                                @error('house_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Email Address <span
                                          class="text-danger">*</span></label>
                                <input type="email"
                                       wire:model.defer="email"
                                       class="form-control custom-input"
                                       placeholder="name@company.com">
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">Mobile <span
                                          class="text-danger">*</span></label>
                                <input type="tel"
                                       wire:model.defer="mobile"
                                       class="form-control custom-input"
                                       placeholder="+1 (555) 000-0000">
                                @error('mobile')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">Subject <span
                                          class="text-danger">*</span></label>
                                <input type="text"
                                       wire:model.defer="subject"
                                       class="form-control custom-input"
                                       placeholder="Brief summary of the issue">
                                @error('subject')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">Detailed Description <span
                                          class="text-danger">*</span></label>
                                <textarea wire:model.defer="description"
                                          class="form-control custom-input"
                                          rows="4"
                                          placeholder="Please describe your issue in detail..."></textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary small">
                                    Attachment (Up to 5MB, optional)
                                </label>
                                <input type="file"
                                       wire:model="attachment"
                                       class="form-control custom-input"
                                       accept=".jpg,.png,.pdf">

                                @error('attachment')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                <div class="form-text mt-1"
                                     style="font-size: 0.75rem;">
                                    Supported formats: JPG, PNG, PDF
                                </div>

                                <!-- Preview -->
                                @if ($attachment)
                                    <div class="mt-2">
                                        @if (Str::startsWith($attachment->getMimeType(), 'image'))
                                            <!-- Image preview -->
                                            <img src="{{ $attachment->temporaryUrl() }}"
                                                 alt="Preview"
                                                 class="img-fluid rounded"
                                                 style="max-width: 200px;">
                                        @else
                                            <!-- File name for non-images -->
                                            <a href="{{ $attachment->temporaryUrl() }}"
                                               target="_blank"
                                               class="d-block text-primary fw-bold">
                                                {{ $attachment->getClientOriginalName() ?? 'file_' . time() . '.' . $attachment->getClientOriginalExtension() }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>



                        </div>

                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <div class="d-flex gap-2 mt-4 pt-3 border-top">
                                <!-- Submit Button -->
                                <button type="submit"
                                        class="btn btn-primary px-4 py-2 flex-grow-1"
                                        style="background: #50abff; border: none; font-weight: 600; border-radius: 8px; transition: all 0.3s;"
                                        wire:loading.attr="disabled"
                                        wire:target="submitSupportRequest">
                                    <span wire:loading.remove
                                          wire:target="submitSupportRequest">Submit Request</span>
                                    <span wire:loading
                                          wire:target="submitSupportRequest">
                                        <i class="fas fa-spinner fa-spin me-2"></i>Submitting...
                                    </span>
                                </button>

                                <!-- Reset Button -->
                                <button type="button"
                                        wire:click="resetForm"
                                        class="btn btn-light px-4 py-2"
                                        style="font-weight: 600; border-radius: 8px; color: #64748b;"
                                        wire:loading.attr="disabled">
                                    Reset
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <style>
        .custom-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 0.6rem 0.8rem !important;
            transition: all 0.2s ease-in-out !important;
            font-size: 0.95rem;
        }

        .custom-input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
            outline: none;
        }

        .custom-input::placeholder {
            color: #cbd5e1;
            font-weight: 400;
        }

        .btn-primary:hover {
            background: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }
    </style>

</div>
