<div class="schedule-sidebar p-2 border-end" style="width: 280px; flex-shrink: 0;">

    <div class="input-group mb-3">
        <input type="text" class="form-control form-control-sm" placeholder="Search employees..." aria-label="Search"
            wire:model="search" wire:keyup="set('search', $event.target.value)">
    </div>

    <hr class="my-2">

    <div class="py-2">
        <h6 class="fw-bold text-muted text-uppercase mb-1">Unassigned shifts</h6>
    </div>
    <div class="employee-list mt-3">
        @if ($employees->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="fas fa-user-slash fa-2x mb-2"></i>
                <div>No employees found.</div>
            </div>
        @else
            @foreach ($employees as $employee)
                <div class="d-flex align-items-center py-2 px-2 employee-row shadow-sm rounded mb-2"
                    title="{{ $employee['f_name'] }} {{ $employee['l_name'] }}">

                    <div class="position-relative me-3">
                        <img src="{{ $employee['avatar_url'] ?? asset('assets/img/default-avatar.png') }}"
                            alt="{{ $employee['f_name'] . ' ' . $employee['l_name'] }}"
                            class="rounded-circle employee-avatar">
                    </div>

                    <div class="d-flex flex-column">
                        <span class="fw-semibold">{{ $employee['f_name'] }} {{ $employee['l_name'] }}</span>
                        <small class="text-muted">{{ ucfirst($employee['role'] ?? 'Employee') }}</small>
                    </div>
                </div>
            @endforeach
        @endif
    </div>


</div>
