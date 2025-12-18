<div class="row g-2 mb-3 shift-form-row">


    <div class="col-3 pt-1">
        <label class="fw-semibold">Employees <span class="text-danger">*</span></label>
    </div>




    <div class="col-9">

        <div class="p-2 border rounded d-flex flex-wrap align-items-center gap-2"
            style="min-height: 50px; background-color: #f9f9f9;">
            @foreach ($this->selectedShiftEmployees as $employee)
                <div class="d-flex align-items-center bg-primary text-white rounded-pill px-2 py-1"
                    style="font-size: 0.75rem;">
                    <img src="{{ $employee->avatar_url ?? '/assets/img/default-avatar.png' }}"
                        alt="{{ $employee->full_name }}" class="rounded-circle me-2" width="28" height="28">
                    <span class="me-2">{{ $employee->full_name }}</span>
                    <button type="button" class="btn btn-sm btn-transparent p-0 text-white ms-auto"
                        wire:click="removeEmployeeFromShift({{ $employee->id }})" aria-label="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endforeach



            {{-- Add button --}}
            <button type="button" class="btn btn-outline-primary btn-xs px-2 py-1" style="font-size: 0.75rem;"
                data-bs-toggle="collapse" data-bs-target="#addUserPanel" aria-expanded="false"
                aria-controls="addUserPanel" wire:click="toggleAddUserPanel">
                {{ $showAddUserPanel ? 'Close' : '+ Add' }}
            </button>

        </div>


        @error('newShift.employees')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror


        <div class="collapse mt-2" id="addUserPanel" wire:ignore.self>
            <div class="card card-body p-0 shadow-sm">
                {{-- Search Input --}}

                <div class="input-group input-group-sm shift-employee-list">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search employees"
                        wire:model.live="shiftEmployeeSearch">
                </div>

                <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                    <div class="list-group-item list-group-item-action bg-light fw-semibold small py-1">
                        All employees ({{ $this->availableShiftEmployees->count() }})
                    </div>

                    @forelse ($this->availableShiftEmployees as $employee)
                        <a href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            wire:click.prevent="addEmployeeToShift({{ $employee->id }})">
                            <div>
                                <img src="{{ $employee->avatar_url ?? '/assets/img/default-avatar.png' }}"
                                    class="rounded-circle me-2" width="32" height="32">
                                {{ $employee->full_name }}
                            </div>
                            <small class="text-success">Available</small>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            No available employees found.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
