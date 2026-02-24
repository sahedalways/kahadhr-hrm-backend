<div class="card">
    <div class="card-body">
        <form class="row g-3"
              wire:submit.prevent="save">

            <h5 class="fw-bold mb-2 text-primary">Leave Settings</h5>
            <hr>

            <!-- Employee Select -->
            <div class="mb-3">
                <label for="employeeSelect"
                       class="form-label fw-medium">
                    Select Employee <span class="text-danger">*</span>
                </label>

                <div class="dropdown">
                    <button class="btn btn-light form-select text-start w-100"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        {{ $selectedEmployeeName ?? '-- Select Employee --' }}
                    </button>

                    <ul class="dropdown-menu w-100"
                        style="max-height:200px; overflow-y:auto;">
                        @foreach ($employees as $employee)
                            <li>
                                <a class="dropdown-item"
                                   wire:click="selectEmployee({{ $employee->user_id }})">
                                    {{ $employee->full_name }}

                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @error('selectedEmployee')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            <!-- Total Annual Hours -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Total Annual Hours <span class="text-danger">*</span></label>
                <input type="number"
                       class="form-control shadow-sm"
                       wire:model="total_annual_hours"
                       min="0"
                       step="0.01"
                       placeholder="Enter total annual hours">
                @error('total_annual_hours')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>


            <!-- Total Leave In Lieu -->
            <div class="col-md-6">
                <label class="form-label fw-semibold">Total Leave in Lieu </label>
                <input type="number"
                       class="form-control shadow-sm"
                       wire:model="total_leave_in_liew"
                       min="0"
                       step="0.01"
                       placeholder="Enter total leave in lieu">
                @error('total_leave_in_liew')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>



            <!-- Save Button -->
            <div class="d-flex justify-content-start mt-3">
                <button type="submit"
                        class="btn btn-success px-4 py-2 shadow-sm"
                        wire:loading.attr="disabled"
                        wire:target="save">
                    <span wire:loading
                          wire:target="save">
                        <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                    </span>
                    <span wire:loading.remove
                          wire:target="save">Save</span>
                </button>
            </div>

        </form>
    </div>
</div>
