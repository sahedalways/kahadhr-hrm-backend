 <div class="modal fade"
      id="generateReportModal"
      tabindex="-1"
      aria-hidden="true">
     <div class="modal-dialog modal-sm modal-dialog-centered">
         <div class="modal-content border-0 shadow">
             <div class="modal-header bg-light">
                 <h6 class="modal-title fw-bold">Choose Format</h6>
                 <button type="button"
                         class="btn-close"
                         data-bs-dismiss="modal"></button>
             </div>
             <div class="modal-body p-4 bg-light-soft">
                 <div class="row g-3">
                     <div class="col-12">
                         <button wire:click="exportFile('pdf')"
                                 wire:loading.attr="disabled"
                                 wire:target="exportFile('pdf')"
                                 class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between group">
                             <div class="d-flex align-items-center">
                                 <div class="bg-danger-soft p-2 rounded-3 me-3 text-danger">
                                     <i class="far fa-file-pdf fa-lg"></i>
                                 </div>
                                 <div>
                                     <div class="fw-bold text-dark mb-0">PDF Document</div>
                                     <small class="text-muted">High-quality print ready</small>
                                 </div>
                             </div>
                             <div wire:loading
                                  wire:target="exportFile('pdf')">
                                 <span class="spinner-border spinner-border-sm text-danger"
                                       role="status"></span>
                             </div>
                             <i class="fas fa-chevron-right text-light group-hover-text-muted d-none d-sm-block"
                                wire:loading.remove
                                wire:target="exportFile('pdf')"></i>
                         </button>
                     </div>

                     <div class="col-12">
                         <button wire:click="exportFile('excel')"
                                 wire:loading.attr="disabled"
                                 wire:target="exportFile('excel')"
                                 class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between">
                             <div class="d-flex align-items-center">
                                 <div class="bg-success-soft p-2 rounded-3 me-3 text-success">
                                     <i class="far fa-file-excel fa-lg"></i>
                                 </div>
                                 <div>
                                     <div class="fw-bold text-dark mb-0">Excel Spreadsheet</div>
                                     <small class="text-muted">Detailed data analysis</small>
                                 </div>
                             </div>
                             <div wire:loading
                                  wire:target="exportFile('excel')">
                                 <span class="spinner-border spinner-border-sm text-success"
                                       role="status"></span>
                             </div>
                             <i class="fas fa-chevron-right text-light d-none d-sm-block"
                                wire:loading.remove
                                wire:target="exportFile('excel')"></i>
                         </button>
                     </div>

                     <div class="col-12">
                         <button wire:click="exportFile('csv')"
                                 wire:loading.attr="disabled"
                                 wire:target="exportFile('csv')"
                                 class="btn btn-white border w-100 p-3 text-start shadow-sm hover-shadow transition-all d-flex align-items-center justify-content-between">
                             <div class="d-flex align-items-center">
                                 <div class="bg-info-soft p-2 rounded-3 me-3 text-info">
                                     <i class="far fa-file-alt fa-lg"></i>
                                 </div>
                                 <div>
                                     <div class="fw-bold text-dark mb-0">CSV File</div>
                                     <small class="text-muted">Plain text raw data</small>
                                 </div>
                             </div>
                             <div wire:loading
                                  wire:target="exportFile('csv')">
                                 <span class="spinner-border spinner-border-sm text-info"
                                       role="status"></span>
                             </div>
                             <i class="fas fa-chevron-right text-light d-none d-sm-block"
                                wire:loading.remove
                                wire:target="exportFile('csv')"></i>
                         </button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </div>
