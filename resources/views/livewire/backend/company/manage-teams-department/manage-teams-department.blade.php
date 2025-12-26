<div>
    <!-- Mode Switch -->
    <div class="mb-4 d-flex gap-2">
        <button wire:click="switchMode('department')"
            class="btn {{ $mode === 'department' ? 'btn-primary' : 'btn-outline-primary' }}">
            Departments
        </button>
        <button wire:click="switchMode('team')"
            class="btn {{ $mode === 'team' ? 'btn-primary' : 'btn-outline-primary' }}">
            Teams
        </button>
    </div>

    <div class="row g-3 align-items-center justify-content-between mb-4">
        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">
                @if ($mode === 'department')
                    Departments Management
                @else
                    Teams Management
                @endif
            </h5>
        </div>

        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">
            <button wire:click="{{ $mode === 'department' ? 'exportDepartments(\'pdf\')' : 'exportTeams(\'pdf\')' }}"
                class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>
            <button
                wire:click="{{ $mode === 'department' ? 'exportDepartments(\'excel\')' : 'exportTeams(\'excel\')' }}"
                class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>
            <button wire:click="{{ $mode === 'department' ? 'exportDepartments(\'csv\')' : 'exportTeams(\'csv\')' }}"
                class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

        <div class="col-auto">
            <a data-bs-toggle="modal" data-bs-target="#add" wire:click="resetInputFields"
                class="btn btn-icon btn-3 btn-white text-primary mb-0">
                <i class="fa fa-plus me-2"></i>
                @if ($mode === 'department')
                    Add New Department
                @else
                    Add New Team
                @endif
            </a>
        </div>
    </div>

    <!-- Search & Sort -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Search by {{ $mode === 'department' ? 'department' : 'team' }} name"
                                    wire:model="search" wire:keyup="set('search', $event.target.value)" />
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-12 d-flex gap-2">
                            <select class="form-select form-select-lg" wire:change="handleSort($event.target.value)">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for:
                            <strong>{{ $search ?: ($mode === 'department' ? 'All Departments' : 'All Teams') }}</strong>
                        </p>
                        <div wire:loading wire:target="search">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"
                                aria-hidden="true"></span>
                            <span class="text-primary small">Searching...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ $mode === 'department' ? 'Department Name' : 'Team Name' }}</th>
                                    @if ($mode === 'team')
                                        <th>Department</th>
                                    @endif
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->name ?? '' }}')"
                                                style="cursor: pointer; padding: 2px 4px; border-radius: 4px;"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Click to copy">
                                                {{ $row->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        @if ($mode === 'team')
                                            <td>{{ $row->department->name ?? 'N/A' }}</td>
                                        @endif
                                        <td>
                                            <a data-bs-toggle="modal" data-bs-target="#edit"
                                                wire:click="edit({{ $row->id }})"
                                                class="badge badge-info badge-xs fw-600 text-xs"
                                                style="background-color: #4ba3f7; color: #fff; cursor: pointer;">
                                                Edit
                                            </a>
                                            <a href="#" class="badge badge-xs badge-danger fw-600 text-xs"
                                                wire:click.prevent="$dispatch('{{ $mode === 'department' ? 'deleteDepartment' : 'deleteTeam' }}', { id: {{ $row->id }} })">
                                                Delete
                                            </a>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $mode === 'department' ? 3 : 4 }}" class="text-center">
                                            No {{ $mode === 'department' ? 'departments' : 'teams' }} found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

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

    <!-- Add/Edit Modal -->
    <div wire:ignore.self class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="add"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">
                        @if ($editMode)
                            Edit {{ $mode === 'department' ? 'Department' : 'Team' }}
                        @else
                            Add New {{ $mode === 'department' ? 'Department' : 'Team' }}
                        @endif
                    </h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-2">
                            @if ($mode === 'team')
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select shadow-sm" wire:model="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $dep)
                                            <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="col-md-12 mb-2">
                                <label
                                    class="form-label">{{ $mode === 'department' ? 'Department Name' : 'Team Name' }}
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="name" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="save">
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                            </span>
                            <span wire:loading.remove wire:target="save">Save</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Edit Modal -->
    <div wire:ignore.self class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">
                        Edit {{ $mode === 'department' ? 'Department' : 'Team' }}
                    </h6>
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="{{ $mode === 'department' ? 'updateDepartment' : 'updateTeam' }}">
                    <div class="modal-body">
                        <div class="row g-2">
                            @if ($mode === 'team')
                                <div class="col-md-12 mb-2">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select shadow-sm" wire:model="department_id" required>
                                        <option value="">Select Department</option>
                                        @foreach ($departments as $dep)
                                            <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif

                            <div class="col-md-12 mb-2">
                                <label
                                    class="form-label">{{ $mode === 'department' ? 'Department Name' : 'Team Name' }}
                                    <span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow-sm" wire:model="name" required>
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                            wire:target="{{ $mode === 'department' ? 'updateDepartment' : 'updateTeam' }}">
                            <span wire:loading
                                wire:target="{{ $mode === 'department' ? 'updateDepartment' : 'updateTeam' }}">
                                <i class="fas fa-spinner fa-spin me-2"></i> Updating...
                            </span>
                            <span wire:loading.remove
                                wire:target="{{ $mode === 'department' ? 'updateDepartment' : 'updateTeam' }}">
                                Update
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => alert("Copied: " + text))
            .catch(err => console.error('Failed to copy: ', err));
    }
</script>
