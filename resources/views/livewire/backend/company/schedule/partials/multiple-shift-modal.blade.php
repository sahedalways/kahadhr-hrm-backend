<div class="modal fade" id="customAddMultipleShiftModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header border-bottom justify-content-center">
                <h5 class="modal-title fw-semibold">Add Multiple Shifts</h5>
            </div>

            {{-- BODY --}}
            <div class="modal-body pt-3">

                {{-- TABLE HEADER --}}
                <div class="row fw-semibold small text-muted border-bottom pb-2 mb-2 align-items-center">
                    <div class="col-1">Date</div>
                    <div class="col-2">Title</div>
                    <div class="col-2">Time</div>
                    <div class="col-1 text-center">All</div>
                    <div class="col-1">Hours</div>
                    <div class="col-2">Job</div>
                    <div class="col-1 text-center">Color</div>
                    <div class="col-1 text-center">Emp</div>
                    <div class="col-1 text-center">Break</div>
                    <div class="col-1 text-center">✕</div>
                </div>

                {{-- TABLE ROWS --}}
                @foreach ($multipleShifts as $index => $shift)
                    <div class="row align-items-center g-2 mb-2 pb-2 border-bottom">

                        {{-- DATE --}}
                        <div class="col-1">
                            <input type="date" class="form-control form-control-sm"
                                wire:model="multipleShifts.{{ $index }}.date">
                        </div>

                        {{-- TITLE --}}
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" placeholder="Shift title"
                                wire:model.defer="multipleShifts.{{ $index }}.title">
                        </div>

                        {{-- TIME --}}
                        <div class="col-2 d-flex gap-1">
                            <input type="time" class="form-control form-control-sm"
                                wire:model="multipleShifts.{{ $index }}.start_time"
                                @if ($shift['all_day']) disabled @endif
                                wire:change="calculateMultiTotalHours({{ $index }})">

                            <input type="time" class="form-control form-control-sm"
                                wire:model="multipleShifts.{{ $index }}.end_time"
                                @if ($shift['all_day']) disabled @endif
                                wire:change="calculateMultiTotalHours({{ $index }})">
                        </div>

                        {{-- ALL DAY --}}
                        <div class="col-1 text-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allDaySwitch{{ $index }}"
                                    wire:model="multipleShifts.{{ $index }}.all_day"
                                    wire:change="toggleMultiAllDayForShift({{ $index }}, $event.target.checked)">
                            </div>
                        </div>

                        {{-- TOTAL HOURS --}}
                        <div class="col-1">
                            <input type="text" readonly class="form-control form-control-sm bg-light"
                                wire:model="multipleShifts.{{ $index }}.total_hours">
                        </div>

                        {{-- JOB --}}
                        <div class="col-2">
                            <input type="text" class="form-control form-control-sm" placeholder="Job"
                                wire:model.defer="multipleShifts.{{ $index }}.job">
                        </div>

                        {{-- COLOR --}}
                        <div class="col-1 d-flex justify-content-center">
                            <input type="color" wire:model="multipleShifts.{{ $index }}.color"
                                class="form-control form-control-color border-0 p-0">
                        </div>

                        {{-- EMPLOYEES --}}
                        @include('livewire.backend.company.schedule.partials.multiple-shift-employees')


                        {{-- BREAKS --}}
                        <div class="col-1 text-center">
                            @if ($isShowMultiBreak[$index] ?? false)
                                <button type="button" class="btn btn-link p-0 small text-primary"
                                    wire:click.prevent="hideBreaksSection({{ $index }})">
                                    Hide Breaks
                                </button>
                            @else
                                <button type="button" class="btn btn-link p-0 small text-primary"
                                    wire:click.prevent="showBreaksSection({{ $index }})">
                                    Add Breaks
                                </button>
                            @endif
                        </div>

                        {{-- Breaks section --}}
                        @if ($isShowMultiBreak[$index] ?? false)
                            <div class="row mb-2 ps-4 pe-4 bg-light rounded">
                                @foreach ($multipleShiftNewBreaks[$index] ?? [] as $breakIndex => $break)
                                    <div class="col-12 d-flex gap-2 align-items-center mb-1">


                                        <div class="col-4">
                                            <input type="text" class="form-control form-control-sm text-center"
                                                wire:model.live="multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.name"
                                                placeholder="Break name">
                                            @error("multipleShiftNewBreaks.$index.$breakIndex.name")
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>


                                        <div class="col-3">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100"
                                                    type="button" data-bs-toggle="dropdown">
                                                    {{ $multipleShiftNewBreaks[$index][$breakIndex]['type'] ?? 'Select type' }}
                                                </button>

                                                <div class="dropdown-menu p-2 text-center">
                                                    @foreach (['Paid', 'Unpaid'] as $type)
                                                        <button type="button" class="dropdown-item text-center"
                                                            wire:click="$set('multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.type', '{{ $type }}')">
                                                            {{ $type }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>

                                            @error("multipleShiftNewBreaks.$index.$breakIndex.type")
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-3">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle w-100"
                                                    type="button" data-bs-toggle="dropdown">
                                                    {{ $multipleShiftNewBreaks[$index][$breakIndex]['duration'] ?? 'Select duration' }}
                                                </button>

                                                <div class="dropdown-menu p-2"
                                                    style="max-height: 200px; overflow-y:auto;">
                                                    @for ($i = 0; $i <= 23; $i += 0.05)
                                                        <button type="button" class="dropdown-item text-center"
                                                            wire:click="$set('multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.duration', '{{ number_format($i, 2) }}')">
                                                            {{ number_format($i, 2) }}
                                                        </button>
                                                    @endfor
                                                </div>
                                            </div>

                                            @error("multipleShiftNewBreaks.$index.$breakIndex.duration")
                                                <span class="text-danger small">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <button type="button" class="btn btn-light btn-sm"
                                            wire:click="removeMultipleBreakRow({{ $index }}, {{ $breakIndex }})">
                                            ✕
                                        </button>
                                    </div>
                                @endforeach

                                <button type="button" class="btn btn-primary btn-sm"
                                    wire:click="addMultipleBreakRow({{ $index }})">
                                    + Add Break
                                </button>


                                <button type="button" class="btn btn-success btn-sm"
                                    wire:click="confirmMultipleBreaksAndSave({{ $index }})">
                                    Confirm Breaks
                                </button>
                            </div>
                        @endif




                        {{-- REMOVE --}}
                        <div class="col-1 d-flex justify-content-center">
                            @if (count($multipleShifts) > 1)
                                <button type="button" wire:click="removeShiftRow({{ $index }})"
                                    class="btn btn-outline-danger p-0" style="width:20px;height:20px;font-size:12px;">
                                    ✕
                                </button>
                            @endif
                        </div>

                        {{-- ADDRESS (FULL WIDTH BELOW ROW) --}}
                        <div class="col-12 ps-2 pe-2 mt-1">
                            <input type="text" class="form-control form-control-sm"
                                placeholder="Address (optional)"
                                wire:model.defer="multipleShifts.{{ $index }}.address">
                        </div>

                        {{-- NOTE --}}
                        <div class="col-12 ps-2 pe-2 mt-1">
                            <input type="text" class="form-control form-control-sm" placeholder="Note"
                                wire:model.defer="multipleShifts.{{ $index }}.note">
                        </div>

                    </div>


                    @if (!empty($multipleShifts[$index]['breaks']))
                        <div class="row mb-2 ps-4 pe-4 bg-light rounded">
                            <div class="col-12 small text-dark">
                                @php
                                    $paidBreaks = collect($multipleShifts[$index]['breaks'])->where('type', 'Paid');
                                    $unpaidBreaks = collect($multipleShifts[$index]['breaks'])->where('type', 'Unpaid');

                                    $paidCount = $paidBreaks->count();
                                    $unpaidCount = $unpaidBreaks->count();

                                    $paidDuration = $paidBreaks->sum(fn($b) => (float) $b['duration']);
                                    $unpaidDuration = $unpaidBreaks->sum(fn($b) => (float) $b['duration']);
                                @endphp

                                @if ($unpaidCount > 0)
                                    {{ $unpaidCount }} Unpaid break{{ $unpaidCount > 1 ? 's' : '' }}
                                    ({{ number_format($unpaidDuration, 2) }}h)
                                @endif

                                @if ($paidCount > 0)
                                    @if ($unpaidCount > 0)
                                        •
                                    @endif
                                    {{ $paidCount }} Paid break{{ $paidCount > 1 ? 's' : '' }}
                                    ({{ number_format($paidDuration, 2) }}h)
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach


                {{-- ADD ROW --}}
                <div class="text-center mt-3">
                    <button type="button" wire:click="addShiftRow" class="btn btn-outline-primary px-3 py-1"
                        style="font-size:13px;">
                        + Add another shift
                    </button>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button wire:click="saveMultipleShifts" class="btn btn-primary">
                    Save Shifts
                </button>
            </div>

        </div>
    </div>


</div>
