<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        {{-- LEFT: Title --}}
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Expenses Report</h5>
        </div>

        {{-- RIGHT: Export --}}
        <div class="col-auto d-flex gap-2">

            <button wire:click="exportExpenses('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportExpenses('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportExpenses('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>

        </div>


        <div class="col-auto">
            <a data-bs-toggle="modal"
               data-bs-target="#add"
               wire:click="resetInputFields"
               class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i> Add New Expense
            </a>
        </div>
    </div>


    {{-- Search + Sort + Category Filter + Employee Filter --}}
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card">

                    <div class="card-body">
                        <div class="row g-3 align-items-center mb-3">

                            {{-- Employee Filter (Admin Only) --}}
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="dropdown w-100"
                                     wire:ignore>
                                    <!-- Dropdown button -->
                                    <button class="btn border shadow-none dropdown-toggle w-100 d-flex justify-content-between align-items-center"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        Filter by Employees
                                    </button>

                                    <!-- Dropdown menu -->
                                    <div class="dropdown-menu p-3 w-100 shadow"
                                         style="max-height: 250px; overflow-y: auto;"
                                         data-bs-auto-close="outside">




                                        <div class="form-check mb-2 border-bottom pb-2">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   wire:model.live="selectAllUsers"
                                                   id="emp-all">
                                            <label class="form-check-label fw-semibold"
                                                   for="emp-all">
                                                All Employees
                                            </label>
                                        </div>


                                        <div class="fw-semibold text-muted small mb-2">
                                            Select Employees
                                        </div>


                                        @foreach ($employees as $emp)
                                            <div class="form-check mb-1">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       value="{{ $emp->user_id }}"
                                                       wire:model.live="filterUsers"
                                                       id="emp-{{ $emp->user_id }}">
                                                <label class="form-check-label"
                                                       for="emp-{{ $emp->user_id }}">
                                                    {{ $emp->full_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>






                            {{-- Sort --}}
                            <div class="col-lg-3 col-md-6 col-12">
                                <select class="form-select form-select-lg"
                                        wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>

                            {{-- Category Filter --}}
                            <div class="col-lg-3 col-md-6 col-12">
                                <select class="form-select"
                                        wire:change="handleCategoryFilter($event.target.value)">
                                    <option value="">All Categories</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Meals & Entertainment">Meals & Entertainment</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="IT & Software">IT & Software</option>
                                    <option value="Equipment & Hardware">Equipment & Hardware</option>
                                    <option value="Communication">Communication</option>
                                    <option value="Training & Development">Training & Development</option>
                                    <option value="Marketing & Advertising">Marketing & Advertising</option>
                                    <option value="Professional Services">Professional Services</option>
                                    <option value="Employee Welfare">Employee Welfare</option>
                                    <option value="Utilities & Facilities">Utilities & Facilities</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 col-12">
                                <select class="form-select form-select-lg"
                                        wire:change="handleDateFilter($event.target.value)">
                                    <option value="">All Dates</option>
                                    <option value="day">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="year">This Year</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>



                            @if ($filterDate === 'custom')
                                <div class="col-md-2">
                                    <input type="date"
                                           class="form-control"
                                           wire:change="handleDateFrom($event.target.value)"
                                           wire:model="date_from">
                                </div>

                                {{-- Date To --}}
                                <div class="col-md-2">
                                    <input type="date"
                                           class="form-control"
                                           wire:change="handleDateTo($event.target.value)"
                                           wire:model="date_to">
                                </div>
                            @endif
                        </div>


                        {{-- Summary --}}
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <p class="text-muted small mb-0">
                                Showing:
                                <strong>{{ $search ?: 'All Expenses' }}</strong>
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
                {{-- TABLE --}}
                <div class="card-body">
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Category</th>
                                    <th>Amount (£)</th>
                                    <th>Description</th>
                                    <th>Attachment</th>
                                    <th>Submission Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp

                                @forelse($infos as $row)
                                    <tr>

                                        <td>{{ $i++ }}</td>

                                        <td>{{ $row->user->full_name ?? 'N/A' }}</td>

                                        <td>{{ $row->category }}</td>

                                        <td>{{ number_format($row->amount, 2) }}</td>

                                        <td>{{ Str::limit($row->description, 40) }}</td>

                                        {{-- Attachments --}}
                                        <td>
                                            @if ($row->attachments && count($row->attachments) > 0)
                                                <button type="button"
                                                        class="btn btn-sm btn-primary"
                                                        wire:click="openAttachments({{ $row->id }})"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#attachmentsModal">
                                                    View
                                                </button>
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ $row->submitted_at ? date('d M, Y', strtotime($row->submitted_at)) : 'N/A' }}
                                        </td>

                                        {{-- Only Delete for Company Admin --}}
                                        <td>
                                            <a href="#"
                                               class="badge bg-info text-white"
                                               wire:click="editExpense({{ $row->id }})"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editExpenseModal">
                                                Edit
                                            </a>



                                            <a href="#"
                                               class="badge bg-danger text-white"
                                               wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="text-center">No expenses found</td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                        <div class="mt-4 d-flex justify-content-end">
                            <div class="card shadow-sm border-0 rounded-4 p-3"
                                 style="max-width: 300px; background-color: #f8f9fa;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold text-muted">Total Expenses:</span>
                                    <span class="fw-bold text-dark">
                                        £ {{ number_format($totalAmount ?? 0, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>


                        {{-- Load More --}}
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

    <div wire:ignore.self
         class="modal fade"
         id="add"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Add Expense</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Category<span class="text-danger">*</span></label>
                            <select class="form-select"
                                    wire:model="category">
                                <option value="">Select</option>

                                <option value="Travel">Travel</option>
                                <option value="Meals & Entertainment">Meals & Entertainment</option>
                                <option value="Office Supplies">Office Supplies</option>
                                <option value="IT & Software">IT & Software</option>
                                <option value="Equipment & Hardware">Equipment & Hardware</option>
                                <option value="Communication">Communication</option>
                                <option value="Training & Development">Training & Development</option>
                                <option value="Marketing & Advertising">Marketing & Advertising</option>
                                <option value="Professional Services">Professional Services</option>
                                <option value="Employee Welfare">Employee Welfare</option>
                                <option value="Utilities & Facilities">Utilities & Facilities</option>
                                <option value="Other">Other</option>

                            </select>
                            @error('category')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label class="form-label mt-2">Amount (£) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="amount">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="mb-3">

                            <label class="form-label mt-2">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      wire:model="description"></textarea>

                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- Hidden File Input --}}
                        <div class="mb-3">
                            <input type="file"
                                   class="d-none"
                                   id="fileInput"
                                   wire:model="newAttachment"
                                   accept="application/pdf,image/*">

                            {{-- Add Attachment Button --}}
                            <div>
                                <button type="button"
                                        class="btn btn-sm btn-primary mt-1"
                                        @if (count($attachments) >= 3) disabled @endif
                                        onclick="document.getElementById('fileInput').click();">
                                    Add Attachment (Max: 3)
                                </button>
                            </div>

                            @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            @if ($errors->has('newAttachment'))
                                <span class="text-danger">{{ $errors->first('newAttachment') }}</span>
                            @endif




                            @if ($attachments)
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    @foreach ($attachments as $index => $file)
                                        @php
                                            $isObject = is_object($file);
                                            $extension = $isObject
                                                ? $file->getClientOriginalExtension()
                                                : pathinfo($file, PATHINFO_EXTENSION);
                                            $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                            $fileName = $isObject ? $file->getClientOriginalName() : basename($file);
                                            $shortName = shortFileName($fileName);
                                        @endphp

                                        <div class="border rounded p-2 position-relative"
                                             style="width: 120px; text-align:center;">
                                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ $fileUrl }}"
                                                     class="img-fluid rounded"
                                                     style="height:80px; object-fit:cover;">
                                            @else
                                                <a href="{{ $fileUrl }}"
                                                   target="_blank"
                                                   class="d-block mt-2 text-decoration-none">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                    <p class="small mb-0">{{ $shortName }}</p>
                                                </a>
                                            @endif

                                            <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                    wire:click="removeAttachment({{ $index }})"
                                                    style="padding: 0 6px; font-size: 12px;">×
                                            </button>
                                        </div>
                                    @endforeach
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



    <div wire:ignore.self
         class="modal fade"
         id="editExpenseModal"
         data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Edit Expense</h6>
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">

                        {{-- Category --}}
                        <div class="mb-3">
                            <label class="form-label">Category<span class="text-danger">*</span></label>
                            <select class="form-select"
                                    wire:model="category">
                                <option value="">Select</option>
                                <option value="Travel">Travel</option>
                                <option value="Meals & Entertainment">Meals & Entertainment</option>
                                <option value="Office Supplies">Office Supplies</option>
                                <option value="IT & Software">IT & Software</option>
                                <option value="Equipment & Hardware">Equipment & Hardware</option>
                                <option value="Communication">Communication</option>
                                <option value="Training & Development">Training & Development</option>
                                <option value="Marketing & Advertising">Marketing & Advertising</option>
                                <option value="Professional Services">Professional Services</option>
                                <option value="Employee Welfare">Employee Welfare</option>
                                <option value="Utilities & Facilities">Utilities & Facilities</option>
                                <option value="Other">Other</option>
                            </select>
                            @error('category')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Amount --}}
                        <div class="mb-3">
                            <label class="form-label mt-2">Amount (£) <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="amount">
                            @error('amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label mt-2">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      wire:model="description"></textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Attachments --}}
                        <div class="mb-3">
                            {{-- Hidden File Input --}}
                            <input type="file"
                                   class="d-none"
                                   id="editFileInput"
                                   wire:model="newAttachment"
                                   accept="application/pdf,image/*">

                            {{-- Add Attachment Button --}}
                            <div>
                                <button type="button"
                                        class="btn btn-sm btn-primary mt-1"
                                        @if (count($attachments) >= 3) disabled @endif
                                        onclick="document.getElementById('editFileInput').click();">
                                    Add Attachment (Max: 3)
                                </button>
                            </div>

                            @error('attachments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            @if ($errors->has('newAttachment'))
                                <span class="text-danger">{{ $errors->first('newAttachment') }}</span>
                            @endif



                            @if ($attachments)
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    @foreach ($attachments as $index => $file)
                                        @php
                                            $isObject = is_object($file);
                                            $extension = $isObject
                                                ? $file->getClientOriginalExtension()
                                                : pathinfo($file, PATHINFO_EXTENSION);
                                            $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                            $fileName = $isObject ? $file->getClientOriginalName() : basename($file);
                                            $shortName = shortFileName($fileName);
                                        @endphp

                                        <div class="border rounded p-2 position-relative"
                                             style="width: 120px; text-align:center;">
                                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ $fileUrl }}"
                                                     class="img-fluid rounded"
                                                     style="height:80px; object-fit:cover;">
                                            @else
                                                <a href="{{ $fileUrl }}"
                                                   target="_blank"
                                                   class="d-block mt-2 text-decoration-none">
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                    <p class="small mb-0">{{ $shortName }}</p>
                                                </a>
                                            @endif

                                            <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                    wire:click="removeAttachment({{ $index }})"
                                                    style="padding: 0 6px; font-size: 12px;">×
                                            </button>
                                        </div>
                                    @endforeach
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
                                wire:target="update">
                            <span wire:loading
                                  wire:target="update"><i class="fas fa-spinner fa-spin me-2"></i>Updating...</span>
                            <span wire:loading.remove
                                  wire:target="update">Update</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>



    {{-- Attachments Modal --}}
    <div wire:ignore.self
         class="modal fade"
         id="attachmentsModal"
         tabindex="-1"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Attachments</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    @if ($currentAttachments && count($currentAttachments) > 0)
                        <div class="row g-3">

                            @foreach ($currentAttachments as $file)
                                <div class="col-md-4 text-center">

                                    @php $ext = pathinfo($file, PATHINFO_EXTENSION); @endphp

                                    @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ asset('storage/' . $file) }}"
                                             class="img-fluid rounded"
                                             style="max-height:150px; cursor:pointer;"
                                             onclick="window.open('{{ asset('storage/' . $file) }}','_blank')">
                                    @elseif ($ext === 'pdf')
                                        <a href="{{ asset('storage/' . $file) }}"
                                           target="_blank"
                                           class="btn btn-outline-primary btn-sm mt-2 w-100">
                                            View PDF
                                        </a>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    @else
                        <p class="text-muted">No attachments available.</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>

</div>




<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Delete this expense?")) {
            Livewire.dispatch('deleteExpense', {
                id
            })
        }
    });
</script>
<script>
    function openFileInput() {
        document.getElementById('fileInput').click();
    }
</script>


<script>
    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
