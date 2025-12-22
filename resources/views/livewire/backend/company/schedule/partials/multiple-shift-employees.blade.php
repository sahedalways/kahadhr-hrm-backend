<div class="col-12">
    <div class="p-2 border rounded d-flex flex-wrap align-items-center gap-2 mb-2 bg-white" style="min-height:50px; ">

        {{-- Selected employees --}}
        @foreach ($multipleShifts[$index]['employees'] ?? [] as $employee)
            @php
                $onLeave = hasLeave($employee['id'], $multipleShifts[$index]['date']);
            @endphp
            <div class="d-flex align-items-center {{ $onLeave ? 'bg-secondary' : 'bg-primary' }} text-white rounded-pill px-2 py-1"
                style="font-size:0.75rem; {{ $onLeave ? 'opacity:.5;cursor:not-allowed;' : '' }}">

                <img src="{{ $employee['avatar_url'] ?? '/assets/img/default-avatar.png' }}" class="rounded-circle me-2"
                    width="28" height="28">
                <span class="me-2">{{ $onLeave ? 'Unavailable' : $employee['full_name'] }}</span>

                @unless ($onLeave)
                    <button type="button" class="btn btn-sm btn-transparent p-0 text-white ms-auto"
                        wire:click="removeEmployeeFromMultipleShift({{ $index }}, {{ $employee['id'] }})">
                        <i class="fas fa-times"></i>
                    </button>
                @endunless
            </div>
        @endforeach

        {{-- Add button --}}
        <button type="button" class="btn btn-outline-primary btn-xs px-2 py-1"
            wire:click="toggleMultipleShiftAddUserPanel({{ $index }})">
            {{ $showMultipleShiftAddUserPanel[$index] ?? false ? 'Close' : '+ Add' }}
        </button>
    </div>


    @if ($showMultipleShiftAddUserPanel[$index] ?? false)
        <div class="card card-body p-0 shadow-sm mt-2">
            {{-- Search --}}
            <div class="input-group input-group-sm shift-employee-list mb-1">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" placeholder="Search employees"
                    wire:model.live="multipleShiftEmployeeSearch.{{ $index }}">
            </div>

            {{-- Available employees --}}



            @if ($multipleShifts[$index]['date'])
                <div class="list-group list-group-flush" style="max-height:200px; overflow-y:auto;">
                    <div class="list-group-item list-group-item-action bg-light fw-semibold small py-1">
                        All employees ({{ $availableMultipleShiftEmployees[$index]->count() ?? 0 }})
                    </div>

                    @forelse ($this->availableMultipleShiftEmployees[$index] ?? [] as $employee)
                        @php

                            $shiftDate = $multipleShifts[$index]['date'] ?? null;
                            $onLeave = $shiftDate ? hasLeave($employee['id'], $shiftDate) : false;
                        @endphp

                        <a href="#"
                            class="list-group-item gap-3 list-group-item-action d-flex justify-content-between align-items-center
          {{ $onLeave ? 'disabled opacity-light' : '' }}"
                            wire:click.prevent="{{ $onLeave ? '' : 'addEmployeeToMultipleShift(' . $index . ',' . $employee['id'] . ')' }}"
                            style="{{ $onLeave ? 'cursor:not-allowed; opacity:.65;' : '' }}">

                            <div class="d-flex align-items-center text-sm">
                                <img src="{{ $employee['avatar_url'] ?? '/assets/img/default-avatar.png' }}"
                                    class="rounded-circle me-2" width="32" height="32">
                                {{ $employee['full_name'] }}
                            </div>

                            <small class=" {{ $onLeave ? 'text-danger' : 'text-success' }} fw-semibold">
                                {{ $onLeave ? 'Unavailable' : 'Available' }}
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item text-center text-muted">
                            No available employees found.
                        </div>
                    @endforelse
                </div>
            @else
                <div class="list-group-item text-muted text-center">
                    You need to select date first
                </div>
            @endif

        </div>
    @endif
</div>
