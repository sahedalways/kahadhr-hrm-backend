<div class="flex-grow-1 schedule-grid-container" style="overflow-x: auto; position: relative;">
    <table class="table table-bordered schedule-table m-0">
        @if ($viewMode === 'weekly')
            <thead>
                <tr class="text-center">
                    @foreach ($weekDays as $day)
                        <th class="{{ $day['highlight'] ? 'bg-primary-light text-primary border-bottom-0' : 'bg-light border-bottom-0' }}"
                            style="width: {{ $viewMode === 'weekly' ? '14.28%' : '3.2%' }}">
                            @if ($viewMode === 'weekly')
                                <div class="fw-bold">{{ $day['day'] }}</div>
                                <div class="small">{{ $day['date'] }}</div>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody>
            @if ($viewMode === 'monthly')
                <div class="table-responsive">
                    <table class="table table-bordered text-center" style="table-layout: fixed; width: 100%;">

                        <thead>
                            <tr>
                                <th>Monday</th>
                                <th>Tuesday</th>
                                <th>Wednesday</th>
                                <th>Thursday</th>
                                <th>Friday</th>
                                <th>Saturday</th>
                                <th>Sunday</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $dateInMonth = \Carbon\Carbon::parse($weekDays[0]['full_date']);
                                $startOfMonth = $dateInMonth->copy()->startOfMonth();
                                $endOfMonth = $dateInMonth->copy()->endOfMonth();

                                $calendarStart = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                                $calendarEnd = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

                                $dates = [];
                                $current = $calendarStart->copy();
                                while ($current->lte($calendarEnd)) {
                                    if (count($dates) >= 42) {
                                        break;
                                    } // max 6 weeks
                                    $dates[] = $current->copy();
                                    $current->addDay();
                                }

                                $weeks = array_chunk($dates, 7);
                            @endphp

                            @foreach ($weeks as $week)
                                <tr>
                                    @foreach ($week as $day)
                                        @php
                                            $hoverKey = 'month_' . $day->format('Y-m-d');
                                            $isInCurrentMonth = $day->month == $dateInMonth->month;
                                        @endphp

                                        <td class="schedule-cell {{ $day->equalTo(\Carbon\Carbon::today()) ? 'bg-primary-light-cell' : '' }}"
                                            style="position: relative; height: 80px; width: 14.285%;"
                                            wire:mouseenter="$set('hoveredCell', '{{ $hoverKey }}')"
                                            wire:mouseleave="$set('hoveredCell', null)">

                                            @if ($isInCurrentMonth)
                                                {{-- Show date number only for current month --}}
                                                <div class="small text-end p-1 text-dark {{ $day->equalTo(\Carbon\Carbon::today()) ? 'fw-bold' : '' }}"
                                                    style="font-size: 0.85rem;">
                                                    {{ $day->day }}
                                                </div>

                                                @if ($hoveredCell === $hoverKey)
                                                    <button
                                                        wire:click="openAddShiftPanel('{{ $day->format('Y-m-d') }}')"
                                                        class="btn btn-sm btn-primary position-absolute d-flex justify-content-center align-items-center"
                                                        style="width: 28px; height: 28px; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; padding: 0; border-radius: 50%;"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Add Shift">
                                                        +
                                                    </button>
                                                @endif
                                            @else
                                                <div style="height: 100%;"></div>
                                            @endif

                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                @foreach ($employees as $employee)
                    <tr>
                        @foreach ($weekDays as $day)
                            @php
                                $content = $this->getCellContent($employee['id'], $day['full_date']);
                                $hoverKey = $employee['id'] . '_' . $day['full_date'];
                            @endphp
                            <td class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}"
                                style="position: relative;"
                                wire:mouseenter="$set('hoveredCell', '{{ $hoverKey }}')"
                                wire:mouseleave="$set('hoveredCell', null)">

                                @if ($content && $content['type'] === 'Leave')
                                    <div
                                        class="unpaid-leave text-center p-1 rounded {{ $day['highlight'] ? 'unpaid-leave-highlight' : '' }}">
                                        <div class="small fw-bold">
                                            {{ $viewMode === 'weekly' ? $content['label'] : 'Leave' }}</div>
                                    </div>
                                @elseif ($content && $content['type'] === 'Shift')
                                    <div class="shift-block {{ $content['color'] }} text-white p-1 rounded">
                                        <div class="small fw-bold">Shift</div>
                                        @if ($viewMode === 'weekly')
                                            <div class="smaller">{{ $content['time'] }}</div>
                                        @endif
                                    </div>
                                @endif


                                {{-- @if ($hoveredCell === $hoverKey)
                                    <button
                                        wire:click="openAddShiftPanel('{{ $day['full_date'] }}', {{ $employee['id'] }})"
                                        class="btn btn-sm btn-primary position-absolute d-flex justify-content-center align-items-center"
                                        style="width: 24px; height: 24px; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10; padding: 0;"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Add Shift">+</button>
                                @endif --}}

                                <button
                                    wire:click="openAddShiftPanel('{{ $day['full_date'] }}', {{ $employee['id'] }})"
                                    class="btn btn-sm btn-primary add-shift-btn position-absolute"
                                    style="
            width: 28px;
            height: 28px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 0;
            z-index: 20;
            border-radius: 50%;
        "
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Add Shift">
                                    +
                                </button>



                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>


    @if ($showAddShiftPanel)
        <div class="shift-panel-overlay" wire:click="closeAddShiftPanel">
            <div class="shift-panel" wire:click.stop>

                {{-- HEADER --}}
                <div
                    class="shift-panel-header d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('l, M d, Y') }}
                    </h6>

                    <button type="button" wire:click="closeAddShiftPanel"
                        class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 28px; height: 28px; padding: 0;">
                        <i class="fas fa-times" style="font-size: 14px; color: #000;"></i>
                    </button>
                </div>

                {{-- TABS --}}
                <ul class="nav nav-tabs shift-tabs px-3 pt-2">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#shift-details">Details</button>
                    </li>
                    <li class="nav-item ms-2">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#shift-tasks">Tasks</button>
                    </li>
                    <li class="nav-item ms-2">
                        <button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#shift-templates">Templates</button>
                    </li>
                </ul>

                {{-- BODY --}}
                <div class="tab-content shift-panel-body px-4 py-3">

                    {{-- DETAILS TAB --}}
                    <div class="tab-pane fade show active" id="shift-details">

                        <form wire:submit.prevent="saveShift" class="d-flex flex-column gap-3">

                            {{-- DATE --}}
                            <div class="row align-items-center g-2 mb-3 shift-form-row">
                                <div class="col-3">
                                    <label class="fw-semibold">Date <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-8 d-flex align-items-center">
                                    <div class="input-group input-group-sm me-3" style="max-width: 160px;">
                                        <input type="date" class="form-control no-calendar-icon"
                                            wire:model="selectedDate" id="shiftDate">
                                        <span class="input-group-text bg-white" style="cursor: pointer;"
                                            onclick="document.getElementById('shiftDate').showPicker()">
                                            <i class="far fa-calendar-alt text-muted"></i>
                                        </span>

                                        @error('selectedDate')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>



                                    <div class="form-check form-switch ms-auto">
                                        <input class="form-check-input" type="checkbox" wire:model="newShift.all_day"
                                            wire:change="toggleOnAllDay($event.target.checked)">
                                        <label class="form-check-label small text-muted ms-1">All day</label>
                                    </div>

                                </div>
                            </div>

                            {{-- TIME --}}
                            <div class="row align-items-center g-2 mb-1 shift-form-row">
                                <div class="col-3">
                                    <label class="fw-semibold">Time <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-9 d-flex align-items-center gap-2">
                                    <input type="time" class="form-control form-control-sm"
                                        wire:model.live="newShift.start_time" style="max-width: 110px;"
                                        wire:change="calculateTotalHours()"
                                        @if ($newShift['all_day']) readonly @endif>
                                    <span class="text-muted">→</span>
                                    <input type="time" class="form-control form-control-sm"
                                        wire:model.live="newShift.end_time" style="max-width: 110px;"
                                        wire:change="calculateTotalHours()"
                                        @if ($newShift['all_day']) readonly @endif>
                                    <span class="ms-auto small fw-semibold " style="color: #000000;">
                                        {{ $newShift['total_hours'] ?? '08:00' }} hrs
                                    </span>
                                </div>

                                @error('newShift.start_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('newShift.end_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>



                            @if ($this->unpaidBreaksCount > 0 || $this->paidBreaksCount > 0)
                                <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                    <i class="fas fa-coffee me-2"></i>

                                    <span class="me-3 text-dark small">
                                        @if ($this->unpaidBreaksCount > 0)
                                            {{ $this->unpaidBreaksCount }} Unpaid
                                            break{{ $this->unpaidBreaksCount > 1 ? 's' : '' }}
                                            {{ $this->unpaidBreaksDuration }}
                                        @endif

                                        @if ($this->paidBreaksCount > 0)
                                            @if ($this->unpaidBreaksCount > 0)
                                                •
                                            @endif
                                            {{ $this->paidBreaksCount }} Paid
                                            break{{ $this->paidBreaksCount > 1 ? 's' : '' }}
                                            {{ $this->paidBreaksDuration }}
                                        @endif
                                    </span>

                                    @if ($this->unpaidBreaksCount > 0 || $this->paidBreaksCount > 0)
                                        <a href="#" data-bs-toggle="modal"
                                            data-bs-target="#customAddBreakModal" wire:click="getDefaultBreaks()"
                                            class="text-primary small ms-auto" style="font-size: 0.8rem;">
                                            Edit Breaks
                                        </a>
                                    @endif
                                </div>


                                {{-- Repeat and Timezone links on new line --}}
                                <div class="d-flex align-items-center mb-3">
                                    <a href="#" class="me-3 text-primary small" data-bs-toggle="modal"
                                        data-bs-target="#customRepeatShiftModal">
                                        <i class="fas fa-redo me-2"></i> Does not repeat
                                    </a>
                                    <a class="text-primary small">
                                        <i class="fas fa-globe me-2"></i> Europe/London
                                    </a>
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-3 text-primary" style="font-size: 0.875rem;">

                                    <a href="#" data-bs-toggle="modal" data-bs-target="#customAddBreakModal"
                                        wire:click="getDefaultBreaks()" class="text-primary small me-3">
                                        <i class="fas fa-coffee me-2"></i>
                                        Add break
                                    </a>

                                    <a href="#" class="me-3 text-primary small" data-bs-toggle="modal"
                                        data-bs-target="#customRepeatShiftModal">
                                        <i class="fas fa-redo me-2"></i> Does not repeat
                                    </a>
                                    <a class="text-primary small">
                                        <i class="fas fa-globe me-2"></i> Europe/London
                                    </a>
                                </div>
                            @endif






                            {{-- TITLE --}}
                            <div class="row align-items-center g-2 mb-3 shift-form-row">
                                <div class="col-3">
                                    <label class="fw-semibold">Shift Title <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-9">
                                    <input type="text" id="shiftTitleInput" wire:model.defer="newShift.title"
                                        class="form-control form-control-sm" placeholder="Enter shift title…"
                                        required>
                                    @error('newShift.title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            {{-- JOB + COLOR --}}
                            <div class="row align-items-center g-2 mb-3 shift-form-row">
                                <div class="col-3">
                                    <label class="fw-semibold">Job <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-9 d-flex align-items-center gap-2">
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.defer="newShift.job" placeholder="Enter job…">

                                    <input type="color" wire:model="newShift.color"
                                        class="form-control form-control-color border-0"
                                        style="width: 38px; height: 31px;">
                                </div>

                                <div class="col-12" style="margin-left: 7.3rem;">
                                    @error('newShift.job')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- EMPLOYEES --}}
                            @include('livewire.backend.company.schedule.partials.employees-box')

                            {{-- ADDRESS --}}
                            <div class="row align-items-center g-2 mb-3 shift-form-row">
                                <div class="col-3">
                                    <label class="fw-semibold">Address</label>
                                </div>
                                <div class="col-9">
                                    <input type="text" wire:model.defer="newShift.address"
                                        class="form-control form-control-sm" placeholder="Enter address…">
                                    @error('newShift.address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- NOTE --}}
                            <div class="row g-2 mb-4 shift-form-row">
                                <div class="col-3 pt-1">
                                    <label class="fw-semibold">Note</label>
                                </div>
                                <div class="col-9">

                                    <textarea wire:model.defer="newShift.note" class="form-control form-control-sm" rows="3"                      
                                                  placeholder="Add a note…"></textarea>

                                    @error('newShift.note')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror


                                </div>
                            </div>

                        </form>
                    </div>

                    {{-- TASKS --}}
                    <div class="tab-pane fade" id="shift-tasks">
                        <p class="text-muted small">No tasks added yet.</p>
                    </div>

                    {{-- TEMPLATES --}}
                    <div class="tab-pane fade" id="shift-templates">
                        <p class="text-muted small">Select a template to auto-fill shift details.</p>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="shift-panel-footer d-flex align-items-center px-4 py-3 border-top bg-white">
                    <!-- Normal state -->
                    <button class="btn btn-primary" wire:click="publishShift" wire:loading.attr="disabled"
                        wire:target="publishShift">

                        <!-- Normal content -->
                        <span wire:loading.remove wire:target="publishShift">
                            <i class="fas fa-upload"></i> Publish
                        </span>

                        <!-- Loading content -->
                        <span wire:loading wire:target="publishShift">
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>
                            Publishing...
                        </span>
                    </button>



                    <div class="d-flex gap-2 ms-auto">
                        <button class="btn btn-light" wire:click="saveDraft"><i class="fas fa-save"></i></button>
                        <button class="btn btn-light"><i class="fas fa-trash"></i></button>
                        <button class="btn btn-light" wire:click="saveAsTemplate"><i
                                class="far fa-clock"></i></button>
                    </div>
                </div>

            </div>
        </div>
    @endif


    @include('livewire.backend.company.schedule.partials.break-modal')
    @include('livewire.backend.company.schedule.partials.repeat-shift-modal')

</div>


<script>
    window.addEventListener('shift-panel-opened', () => {
        setTimeout(() => {
            const titleInput = document.getElementById('shiftTitleInput');
            if (titleInput) {
                titleInput.focus();
            }
        }, 50);
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
