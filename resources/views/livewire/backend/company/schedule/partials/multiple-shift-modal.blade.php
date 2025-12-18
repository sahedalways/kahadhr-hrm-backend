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
                                @if ($shift['all_day']) disabled @endif>

                            <input type="time" class="form-control form-control-sm"
                                wire:model="multipleShifts.{{ $index }}.end_time"
                                @if ($shift['all_day']) disabled @endif>
                        </div>

                        {{-- ALL DAY --}}
                        <div class="col-1 text-center">
                            <input type="checkbox" class="form-check-input"
                                wire:model="multipleShifts.{{ $index }}.all_day">
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
                            <a href="#" class="text-primary small" data-bs-toggle="modal"
                                data-bs-target="#customAddBreakModal" wire:click="openBreaks({{ $index }})">
                                Breaks
                            </a>
                        </div>

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
                            <input type="text" class="form-control form-control-sm" placeholder="Address (optional)"
                                wire:model.defer="multipleShifts.{{ $index }}.address">
                        </div>

                        {{-- NOTE --}}
                        <div class="col-12 ps-2 pe-2 mt-1">
                            <input type="text" class="form-control form-control-sm" placeholder="Note"
                                wire:model.defer="multipleShifts.{{ $index }}.note">
                        </div>

                    </div>
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
