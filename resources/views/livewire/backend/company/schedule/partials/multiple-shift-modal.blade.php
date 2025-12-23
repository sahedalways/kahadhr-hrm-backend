<div class="modal fade" id="customAddMultipleShiftModal" tabindex="-1" wire:ignore.self data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="modal-content">


            {{-- BODY --}}
            <div class="modal-body p-4 bg-light position-relative">
                <div class="d-flex justify-content-end">
                    <button type="button"
                        class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                        data-bs-dismiss="modal" style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                    </button>
                </div>

                {{-- PAGE HEADER --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="h5 mb-1 fw-bold text-dark">Multiple Shift Entry</h4>
                        <p class="text-muted small mb-0">Define and schedule multiple shift blocks for your team.</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-primary border border-primary px-3 py-2 rounded-pill">
                            {{ count($multipleShifts) }} Active Shift(s)
                        </span>
                    </div>
                </div>

                @foreach ($multipleShifts as $index => $shift)
                    <div class="card border-0 shadow-sm mb-4 rounded-4">
                        {{-- CARD HEADER --}}
                        <div
                            class="card-header bg-white border-bottom-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 32px; height: 32px; font-size: 14px; font-weight: 600;">
                                    {{ $index + 1 }}
                                </div>
                                <h6 class="mb-0 fw-bold text-secondary text-uppercase tracking-wider"
                                    style="font-size: 0.85rem;">Shift Configuration</h6>
                            </div>

                            @if (count($multipleShifts) > 1)
                                <button type="button" wire:click="removeShiftRow({{ $index }})"
                                    class="btn btn-sm btn-light text-danger rounded-circle shadow-none circle-sm tooltip-btn"
                                    data-tooltip="Remove Shift">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            @endif
                        </div>

                        <div class="card-body px-4 pb-4">
                            {{-- SECTION 1: IDENTITY --}}
                            <div class="row g-3 align-items-md-end">

                                <!-- Shift Date -->
                                <div class="col-12 col-md-2">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Shift Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="far fa-calendar-alt text-muted"></i>
                                        </span>
                                        <input type="date"
                                            class="form-control form-control-sm border-start-0 ps-2
                @error("multipleShifts.$index.date") is-invalid @enderror"
                                            wire:model.live="multipleShifts.{{ $index }}.date">
                                    </div>
                                    @error("multipleShifts.$index.date")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Internal Title -->
                                <div class="col-12 col-md-2">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Internal Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-sm
            @error("multipleShifts.$index.title") is-invalid @enderror"
                                        placeholder="e.g., Inventory Audit"
                                        wire:model.defer="multipleShifts.{{ $index }}.title">
                                    @error("multipleShifts.$index.title")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Color -->
                                <div class="col-lg-1 col-2">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Color
                                    </label>
                                    <input type="color" wire:model="multipleShifts.{{ $index }}.color"
                                        class="form-control form-control-color w-100 border-0 p-0"
                                        style="height:31px;border-radius:6px;">
                                </div>

                                <!-- Working Hours -->
                                <div class="col-12 col-xl-2 col-md-4">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Working Hours <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="time" class="form-control"
                                            wire:model="multipleShifts.{{ $index }}.start_time"
                                            @if ($shift['all_day']) disabled @endif
                                            wire:change="calculateMultiTotalHours({{ $index }})">
                                        <span class="input-group-text bg-white text-muted">to</span>
                                        <input type="time" class="form-control"
                                            wire:model="multipleShifts.{{ $index }}.end_time"
                                            @if ($shift['all_day']) disabled @endif
                                            wire:change="calculateMultiTotalHours({{ $index }})">
                                    </div>

                                    @error("multipleShifts.$index.start_time")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error("multipleShifts.$index.end_time")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- All Day -->
                                <div class="col-lg-1 col-md-2 col-12 text-md-center">
                                    <label class="form-label text-muted fw-bold small text-uppercase d-block">
                                        All Day
                                    </label>
                                    <div class="form-check form-switch d-inline-block m-0">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="multipleShifts.{{ $index }}.all_day"
                                            wire:change="toggleMultiAllDayForShift({{ $index }}, $event.target.checked)">
                                    </div>
                                </div>

                                <!-- Net Hours -->
                                <div class="col-6 col-md-2">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Net Hours
                                    </label>
                                    <input type="text" readonly
                                        class="form-control form-control-sm bg-light text-center fw-bold"
                                        wire:model="multipleShifts.{{ $index }}.total_hours">
                                </div>

                                <!-- Job Role -->
                                <div class="col-12 col-md-2">
                                    <label class="form-label text-muted fw-bold small text-uppercase">
                                        Job Role <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control form-control-sm
            @error("multipleShifts.$index.job") is-invalid @enderror"
                                        placeholder="Specify Role"
                                        wire:model.defer="multipleShifts.{{ $index }}.job">
                                    @error("multipleShifts.$index.job")
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>




                            {{-- SECTION 3: PERSONNEL & BREAKS --}}
                            <div class="row g-3 mt-4 align-items-center bg-light mx-0 py-3 rounded-3 border">
                                <div class="col-md-6 mx-auto">
                                    <div class="row align-items-center shift-header py-3 px-2 rounded-3 mb-3">
                                        <div class="col-6 d-flex align-items-center">
                                            <div class="me-3">
                                                <label class="text-muted fw-bold small text-uppercase d-block mb-1">
                                                    Assign Employees <span class="text-danger">*</span>
                                                </label>

                                                @include('livewire.backend.company.schedule.partials.multiple-shift-employees')
                                            </div>
                                        </div>

                                        <div class="col-6 text-md-end">
                                            <button type="button"
                                                class="btn btn-sm break-btn px-3 rounded-pill fw-bold
            {{ $isShowMultiBreak[$index] ?? false ? 'btn-dark active' : 'btn-outline-dark' }}"
                                                wire:click.prevent="{{ $isShowMultiBreak[$index] ?? false ? 'hideBreaksSection' : 'showBreaksSection' }}({{ $index }})">

                                                <i class="fas fa-coffee me-2"></i>
                                                {{ $isShowMultiBreak[$index] ?? false ? 'Close Break Manager' : 'Manage Breaks' }}
                                            </button>
                                        </div>
                                    </div>


                                </div>



                                @if ($isShowMultiBreak[$index] ?? false)
                                    <div class="col-12 mt-3 pt-3 border-top">
                                        @foreach ($multipleShiftNewBreaks[$index] ?? [] as $breakIndex => $break)
                                            <div
                                                class="row g-2 align-items-center mb-2 bg-white p-2 rounded border shadow-xs mx-0">
                                                <div class="col-4">
                                                    <label class="form-label form-label-sm">Break Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm border"
                                                        placeholder="Break Description"
                                                        wire:model.live="multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.name">
                                                </div>
                                                <div class="col-3">
                                                    <label class="form-label form-label-sm">Type <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select form-select-sm border"
                                                        wire:model="multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.type">
                                                        <option value="">Select Type</option>
                                                        <option value="Paid">Paid</option>
                                                        <option value="Unpaid">Unpaid</option>
                                                    </select>
                                                </div>
                                                <div class="col-3">
                                                    <label class="form-label form-label-sm">Duration <span
                                                            class="text-danger">*</span></label>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle w-100 border"
                                                            type="button" data-bs-toggle="dropdown">
                                                            {{ $break['duration'] ?? 'Duration' }}
                                                        </button>
                                                        <div class="dropdown-menu text-center"
                                                            style="max-height:200px;overflow-y:auto;">
                                                            @for ($i = 0; $i <= 23; $i += 0.05)
                                                                <button type="button" class="dropdown-item"
                                                                    wire:click="$set('multipleShiftNewBreaks.{{ $index }}.{{ $breakIndex }}.duration','{{ number_format($i, 2) }}')">
                                                                    {{ number_format($i, 2) }}
                                                                </button>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-2 text-start ">

                                                    <button type="button"
                                                        class="btn btn-sm text-danger border-0 circle-sm "
                                                        style="margin-top: 1.7rem !important"
                                                        wire:click="removeMultipleBreakRow({{ $index }}, {{ $breakIndex }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="mt-3 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary px-3 shadow-none"
                                                wire:click="addMultipleBreakRow({{ $index }})">+ Add Break
                                                Row</button>
                                            <button type="button" class="btn btn-sm btn-success px-3 shadow-none"
                                                wire:click="confirmMultipleBreaksAndSave({{ $index }})">Save &
                                                Apply Breaks</button>
                                        </div>
                                    </div>
                                @endif


                                @error("multipleShifts.$index.employees")
                                    <div class="text-danger small mt-1 text-center">{{ $message }}</div>
                                @enderror

                            </div>

                            {{-- SECTION 4: METADATA --}}
                            <div class="row g-3 mt-2 pt-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Deployment
                                        Address</label>
                                    <input type="text" class="form-control form-control-sm"
                                        placeholder="Search location or enter manually"
                                        wire:model.defer="multipleShifts.{{ $index }}.address">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted fw-bold small text-uppercase">Manager's
                                        Notes</label>
                                    <input type="text" class="form-control form-control-sm"
                                        placeholder="Additional instructions..."
                                        wire:model.defer="multipleShifts.{{ $index }}.note">
                                </div>
                            </div>
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

                {{-- ADD ROW BUTTON --}}
                <div
                    class="text-center py-4 border-2 border-dashed border-secondary-subtle rounded-4 bg-white shadow-xs">
                    <button type="button" wire:click="addShiftRow"
                        class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow">
                        <i class="fas fa-layer-group me-2"></i> Add Another Shift Block
                    </button>
                    <p class="text-muted small mt-2 mb-0">You can add multiple shifts and assign different teams to
                        each.</p>
                </div>
            </div>




            {{-- FOOTER --}}
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" wire:click="publishMultipleShifts" wire:loading.attr="disabled"
                    wire:target="publishMultipleShifts">

                    <!-- Normal content -->
                    <span wire:loading.remove wire:target="publishMultipleShifts">
                        <i class="fas fa-upload me-2"></i> Publish Shifts
                    </span>

                    <!-- Loading content -->
                    <span wire:loading wire:target="publishMultipleShifts">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Publishing...
                    </span>
                </button>

            </div>

        </div>
    </div>


</div>
