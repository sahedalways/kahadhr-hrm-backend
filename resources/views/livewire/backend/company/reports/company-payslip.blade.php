<div>
    <div class="row align-items-center justify-content-between mb-4">

        {{-- LEFT: Title --}}
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Payslip Management</h5>
        </div>

        {{-- RIGHT: Upload Payslip --}}
        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#uploadPayslipModal" wire:click="resetFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-upload me-2"></i> Upload Payslip
            </a>

            <a data-bs-toggle="modal" data-bs-target="#requestPayslipModal" wire:click="loadRequests"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-list me-2"></i> Requested Payslips
            </a>

        </div>
    </div>


    {{-- Filters --}}
    <div class="row">
        <div class="col-12">

            <div class="card mb-4">
                <div class="card shadow-sm border-0">

                    <div class="card-body p-4">
                        <div class="row g-3 align-items-center mb-3">


                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleUserFilter($event.target.value)">
                                    <option value="">All Employees</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->user_id }}">{{ $emp->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select form-select-lg"
                                    wire:change="handleMonthFilter($event.target.value)">
                                    <option value="">All Months</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <select class="form-select form-select-lg"
                                    wire:change="handleYearFilter($event.target.value)">
                                    <option value="">All Years</option>
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Sort --}}
                            <div class="col-md-3">
                                <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                    <option value="desc">Newest First</option>
                                    <option value="asc">Oldest First</option>
                                </select>
                            </div>

                        </div>
                    </div>

                </div>


                {{-- TABLE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-bordered text-center align-middle">

                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>File</th>
                                    <th>Uploaded At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $i = 1; @endphp

                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>

                                        <td>{{ $row->user->full_name ?? 'N/A' }}</td>

                                        <td>{{ $row->period }}</td>

                                        {{-- PDF File --}}
                                        <td>
                                            <a href="{{ asset('storage/' . $row->file_path) }}" target="_blank"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>

                                        <td>
                                            {{ date('d M, Y', strtotime($row->created_at)) }}
                                        </td>

                                        <td>
                                            <a href="#" class="badge bg-info text-white"
                                                wire:click="editPayslip({{ $row->id }})" data-bs-toggle="modal"
                                                data-bs-target="#editPayslipModal">
                                                Edit
                                            </a>



                                            <a href="#" class="badge bg-danger text-white"
                                                wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No payslips found</td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                        {{-- Load More --}}
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


    {{-- Upload Payslip Modal --}}
    <div wire:ignore.self class="modal fade" id="uploadPayslipModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Upload Payslip</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="savePayslip">
                    <div class="modal-body">

                        {{-- Employee --}}
                        <div class="mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="user_id">
                                <option value="">Select</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->user_id }}">
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Period --}}
                        <div class="mb-3">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">

                                {{-- Month --}}
                                <select class="form-select" wire:model="month">
                                    <option value="">Month</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>

                                {{-- Year --}}
                                <select class="form-select" wire:model="year">
                                    <option value="">Year</option>
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>

                            </div>
                            @error('month')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('year')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- PDF Upload --}}
                        <div class="mb-3">
                            <label class="form-label">Payslip File (PDF) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="file" accept="application/pdf">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror


                            <div wire:loading wire:target="file" class="small text-primary mt-1">
                                <i class="fas fa-spinner fa-spin"></i> Uploading...
                            </div>

                            {{-- Existing File  Preview --}}
                            @if ($file)
                                @php
                                    $isObject = is_object($file);
                                    $extension = $isObject
                                        ? $file->getClientOriginalExtension()
                                        : pathinfo($file, PATHINFO_EXTENSION);
                                    $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                    $fileName = $isObject ? $file->getClientOriginalName() : basename($file);

                                    $shortName = shortFileName($fileName);
                                @endphp

                                <div class="border rounded p-2 position-relative mt-2"
                                    style="width: 180px; text-align:center;">
                                    @if (strtolower($extension) === 'pdf')
                                        <a href="{{ $fileUrl }}" target="_blank"
                                            class="d-block text-decoration-none">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        </a>
                                    @else
                                        {{-- fallback for unsupported type --}}
                                        <p class="small mb-0">{{ $shortName }}</p>
                                    @endif


                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="savePayslip">
                            <span wire:loading wire:target="savePayslip"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Saving...</span>
                            <span wire:loading.remove wire:target="savePayslip">Save</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="editPayslipModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Edit Payslip</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="updatePayslip">
                    <div class="modal-body">

                        {{-- Employee --}}
                        <div class="mb-3">
                            <label class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model="user_id">
                                <option value="">Select</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->user_id }}">
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Period --}}
                        <div class="mb-3">
                            <label class="form-label">Period <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">

                                {{-- Month --}}
                                <select class="form-select" wire:model="month">
                                    <option value="">Month</option>
                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>

                                {{-- Year --}}
                                <select class="form-select" wire:model="year">
                                    <option value="">Year</option>
                                    @for ($y = date('Y') - 5; $y <= date('Y') + 5; $y++)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>

                            </div>
                            @error('month')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('year')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        {{-- PDF Upload --}}
                        <div class="mb-3">
                            <label class="form-label">Payslip File (PDF) <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" wire:model="file" accept="application/pdf">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror


                            <div wire:loading wire:target="file" class="small text-primary mt-1">
                                <i class="fas fa-spinner fa-spin"></i> Uploading...
                            </div>

                            {{-- Existing File  Preview --}}
                            @if ($file)
                                @php
                                    $isObject = is_object($file);
                                    $extension = $isObject
                                        ? $file->getClientOriginalExtension()
                                        : pathinfo($file, PATHINFO_EXTENSION);
                                    $fileUrl = $isObject ? $file->temporaryUrl() : asset('storage/' . $file);
                                    $fileName = $isObject ? $file->getClientOriginalName() : basename($file);

                                    $shortName = shortFileName($fileName);
                                @endphp

                                <div class="border rounded p-2 position-relative mt-2"
                                    style="width: 180px; text-align:center;">
                                    @if (strtolower($extension) === 'pdf')
                                        <a href="{{ $fileUrl }}" target="_blank"
                                            class="d-block text-decoration-none">
                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            <p class="small mb-0">{{ $shortName }}</p>
                                        </a>
                                    @else
                                        {{-- fallback for unsupported type --}}
                                        <p class="small mb-0">{{ $shortName }}</p>
                                    @endif


                                </div>
                            @endif
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="updatePayslip">
                            <span wire:loading wire:target="updatePayslip"><i
                                    class="fas fa-spinner fa-spin me-2"></i>Updating ...</span>
                            <span wire:loading.remove wire:target="updatePayslip">Update</span>
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="requestPayslipModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-600">Requested Payslips</h6>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i></button>
                </div>

                <div class="modal-body">
                    @if ($requests->isEmpty())
                        <p class="text-center">No pending payslip requests.</p>
                    @else
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Period</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $index => $req)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $req->user->full_name ?? 'N/A' }}</td>
                                        <td>{{ $req->period }}</td>
                                        <td>
                                            @php $status = ucfirst($req->status); @endphp
                                            <span
                                                class="badge {{ $req->status === 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $existingFile = $req->payslip->file_path ?? null;
                                                $existingFileName = $existingFile ? basename($existingFile) : null;
                                            @endphp

                                            <div x-data="{ isUploading: false }"
                                                class="d-flex flex-column align-items-center">

                                                {{-- Existing file / Upload / Replace --}}
                                                @if ($existingFileName)
                                                    <a href="{{ asset('storage/' . $existingFile) }}" target="_blank"
                                                        class="badge bg-success mb-1 text-decoration-none">
                                                        {{ $existingFileName }}
                                                    </a>
                                                    <span x-show="!isUploading"
                                                        class="badge bg-info mb-1">Replace</span>
                                                @else
                                                    <span x-show="!isUploading"
                                                        class="badge bg-secondary mb-1">Upload</span>
                                                @endif

                                                <input type="file" accept="application/pdf"
                                                    wire:change="uploadRequestFile({{ $req->id }}, $event.target.files)"
                                                    wire:loading.attr="disabled"
                                                    x-on:livewire-upload-start="isUploading = true"
                                                    x-on:livewire-upload-finish="isUploading = false"
                                                    x-on:livewire-upload-error="isUploading = false"
                                                    x-bind:disabled="isUploading"
                                                    class="form-control form-control-sm mb-1">

                                                <div x-show="isUploading">
                                                    <span class="badge bg-warning text-dark mt-1">
                                                        <i class="fas fa-spinner fa-spin me-1"></i> Uploading...
                                                    </span>
                                                </div>

                                                {{-- Delete button --}}
                                                <button type="button" class="btn btn-sm btn-danger mt-1"
                                                    wire:click.prevent="$dispatch('confirmRequestDelete', {{ $req->id }})">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>






</div>


<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Delete this payslip?")) {
            Livewire.dispatch('deletePayslip', {
                id
            })
        }
    });
</script>

<script>
    Livewire.on('confirmRequestDelete', id => {
        if (confirm("Are you sure you want to delete this requested payslip?")) {
            Livewire.emit('deleteRequest', id);
        }
    });
</script>
