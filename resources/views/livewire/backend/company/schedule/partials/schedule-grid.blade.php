<div class="flex-grow-1 schedule-grid-container">
    <div class="bg-white d-flex justify-content-center align-items-center py-2">
        @include('livewire.backend.company.schedule.partials.header_nav', [
            'startDate' => $startDate ?? 'Oct 27',
            'endDate' => $endDate ?? 'Nov 2',
        ])
    </div>
    <div class="table-responsive">
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

                                            <td class="schedule-cell month-cell {{ $day->equalTo(\Carbon\Carbon::today()) ? 'bg-primary-light-cell' : '' }}"
                                                style="position: relative; height: 80px; width: 14.285%;"
                                                x-data="{ hover: false }" @mouseenter="hover = true"
                                                @mouseleave="hover = false">

                                                @if ($isInCurrentMonth)
                                                    {{-- Date Number --}}
                                                    <div class="small text-end p-1 text-dark {{ $day->equalTo(\Carbon\Carbon::today()) ? 'fw-bold' : '' }}"
                                                        style="font-size: 0.85rem;">
                                                        {{ $day->day }}
                                                    </div>

                                                    @php
                                                        $dateKey = $day->format('Y-m-d');
                                                        $hasShift = !empty($this->calendarShifts[$dateKey]);
                                                    @endphp

                                                    {{-- Show Shifts --}}
                                                    @if ($hasShift)
                                                        @foreach ($this->calendarShifts[$dateKey] as $shift)
                                                            @php
                                                                $modalId = 'shiftDetailsModal-' . $shift['id'];
                                                            @endphp


                                                            <div class="position-relative">

                                                                <div class="shift-block text-white rounded px-1 py-0 mb-1 mx-auto"
                                                                    style="background-color:{{ $shift['shift']['color'] }};font-size:11px;cursor:pointer;max-width:90%;">
                                                                    <div class="fw-semibold text-truncate"
                                                                        style="max-width: 100%;">
                                                                        {{ $shift['shift']['title'] }}</div>
                                                                    <div>
                                                                        {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                                                    </div>
                                                                </div>


                                                                <div class="dropdown shift-dropdown">
                                                                    <button class="btn btn-xs btn-link text-white p-0"
                                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <ul
                                                                        class="dropdown-menu dropdown-menu-end shadow-sm dropdown-schedule-cell">
                                                                        <li>
                                                                            <button class="dropdown-item" type="button"
                                                                                wire:click="editShift({{ $shift['id'] }})">
                                                                                <i class="fas fa-edit fa-fw me-1"></i>
                                                                                Edit
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <button class="dropdown-item" type="button"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#{{ $modalId }}">
                                                                                <i class="fas fa-eye fa-fw me-1"></i>
                                                                                View
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <hr class="dropdown-divider">
                                                                        </li>
                                                                        <li>
                                                                            <button class="dropdown-item text-danger"
                                                                                type="button"
                                                                                wire:click="deleteShiftForAllEmp({{ $shift['id'] }})"
                                                                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                                                                <i
                                                                                    class="fas fa-trash-alt fa-fw me-1"></i>
                                                                                Delete
                                                                            </button>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>






                                                            <div class="modal fade" id="{{ $modalId }}"
                                                                tabindex="-1" aria-hidden="true" wire:ignore.self
                                                                data-bs-backdrop="static" data-bs-keyboard="false">
                                                                <div
                                                                    class="modal-dialog modal-dialog-centered modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-primary text-white">
                                                                            <h5 class="modal-title text-white">
                                                                                {{ $shift['shift']['title'] ?? '-' }}
                                                                            </h5>
                                                                            <button type="button"
                                                                                class="btn-close btn-close-white"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row mb-2">
                                                                                <div class="col-sm-6">
                                                                                    <strong>Time:</strong>
                                                                                    {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }}
                                                                                    -
                                                                                    {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                                                                </div>
                                                                                <div class="col-sm-6">
                                                                                    <strong>Address:</strong>
                                                                                    {{ $shift['shift']['address'] ?? '-' }}
                                                                                </div>
                                                                            </div>

                                                                            @if (!empty($shift['employees']))
                                                                                @php
                                                                                    $employees = collect(
                                                                                        $shift['employees'],
                                                                                    )
                                                                                        ->pluck('name')
                                                                                        ->toArray();
                                                                                    $showLimit = 5;
                                                                                    $moreCount =
                                                                                        count($employees) - $showLimit;
                                                                                @endphp
                                                                                <div class="mb-2 mt-2">
                                                                                    <strong>Employees:</strong>
                                                                                    {{ implode(', ', array_slice($employees, 0, $showLimit)) }}
                                                                                    @if ($moreCount > 0)
                                                                                        <span
                                                                                            class="text-muted">+{{ $moreCount }}
                                                                                            more</span>
                                                                                    @endif
                                                                                </div>
                                                                            @endif

                                                                            @if (!empty($shift['shift']['note']))
                                                                                <div class="mb-2">
                                                                                    <strong>Note:</strong>
                                                                                    {{ $shift['shift']['note'] ?? '-' }}
                                                                                </div>
                                                                            @endif

                                                                            @if (!empty($shift['breaks']))
                                                                                <div class="mb-2">
                                                                                    <strong>Breaks:</strong>
                                                                                    <table
                                                                                        class="table table-sm table-bordered mt-1">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Title</th>
                                                                                                <th>Type</th>
                                                                                                <th>Duration (hr)
                                                                                                </th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($shift['breaks'] as $break)
                                                                                                <tr>
                                                                                                    <td>{{ $break['title'] }}
                                                                                                    </td>
                                                                                                    <td>{{ $break['type'] }}
                                                                                                    </td>
                                                                                                    <td>{{ $break['duration'] }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif


                                                    @if (!$hasShift)
                                                        <button
                                                            wire:click="openAddShiftPanelForMonth('{{ $day->format('Y-m-d') }}')"
                                                            class="btn btn-sm btn-primary position-absolute"
                                                            style="width: 28px; height: 28px; top: 50%; left: 50%;
                           transform: translate(-50%, -50%);
                           padding: 0; border-radius: 50%; z-index: 10;"
                                                            x-show="hover">
                                                            +
                                                        </button>
                                                    @endif
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
                                                {{ $viewMode === 'weekly' ? $content['label'] : 'Leave' }}
                                            </div>
                                        </div>
                                    @elseif ($content && $content['type'] === 'Shift')
                                        <div class="shift-block text-white rounded position-relative shadow-sm p-3"
                                            style="background-color: {{ $content['color'] ?? '#6c757d' }}; cursor: pointer; top: 50%; left: 50%; transform: translate(-50%, -50%); transition: all .25s ease-in-out;">


                                            <div class="small fw-bold text-truncate" style="max-width: 100%;">
                                                {{ \Illuminate\Support\Str::limit($content['title'], 15) }}
                                            </div>
                                            <div class="smaller opacity-75">{{ $content['time'] }}</div>

                                            {{-- <a href="#"
                                                id="shiftMenu-{{ $employee['id'] }}-{{ \Str::slug($content['title']) }}"
                                                class="text-warning position-absolute"
                                                style="bottom:4px; right:6px; font-size:12px;" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a> --}}





                                            {{-- Single trigger + menu --}}
                                            <div class="shift-dropdown position-absolute"
                                                style="bottom: 4px; right: 6px;">
                                                <button type="button"
                                                    class="btn btn-xs btn-link text-white p-0 shift-menu-btn">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>

                                                <ul class="dropdown-schedule-celll d-none">
                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                            wire:click="editOneEmpShift({{ $content['id'] }}, {{ $employee['id'] }})">
                                                            <i class="fas fa-edit fa-fw me-1"></i> Edit
                                                        </button>
                                                    </li>

                                                    <li>
                                                        <button class="dropdown-item" type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#shiftDetailsModal-{{ $employee['id'] }}-{{ \Str::slug($content['title']) }}">
                                                            <i class="fas fa-eye fa-fw me-1"></i> View
                                                        </button>
                                                    </li>

                                                    {{-- <li>
                                                        <hr class="dropdown-divider">
                                                    </li> --}}

                                                    <li>
                                                        <button class="dropdown-item text-danger" type="button"
                                                            wire:click="deleteShiftOneEmp({{ $content['id'] }}, {{ $employee['id'] }})"
                                                            onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                                            <i class="fas fa-trash-alt fa-fw me-1"></i> Delete
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>


                                        </div>




                                        <div class="modal fade"
                                            id="shiftDetailsModal-{{ $employee['id'] }}-{{ \Str::slug($content['title']) }}"
                                            aria-hidden="true" tabindex="-1" wire:ignore.self
                                            data-bs-backdrop="static" data-bs-keyboard="false">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">

                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title text-white">
                                                            {{ $content['title'] ?? '-' }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="row mb-2">
                                                            <div class="col-sm-6">
                                                                <strong>Time:</strong> {{ $content['time'] ?? '-' }}
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <strong>Address:</strong>
                                                                {{ $content['shift']['address'] ?? '-' }}
                                                            </div>
                                                        </div>

                                                        @if (!empty($content['employees']))
                                                            @php
                                                                $employees = collect($content['employees'])
                                                                    ->pluck('name')
                                                                    ->toArray();
                                                                $showLimit = 5;
                                                                $moreCount = count($employees) - $showLimit;
                                                            @endphp

                                                            <div class="mb-2">
                                                                <strong>Employees:</strong>
                                                                {{ implode(', ', array_slice($employees, 0, $showLimit)) }}
                                                                @if ($moreCount > 0)
                                                                    <span class="text-muted">+{{ $moreCount }}
                                                                        more</span>
                                                                @endif
                                                            </div>
                                                        @endif


                                                        @if (!empty($content['shift']['note']))
                                                            <div class="mb-2">
                                                                <strong>Note:</strong>
                                                                {{ $content['shift']['note'] ?? '-' }}
                                                            </div>
                                                        @endif

                                                        @if (!empty($content['breaks']))
                                                            <div class="mb-2">
                                                                <strong>Breaks:</strong>
                                                                <table class="table table-sm table-bordered mt-1">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Title</th>
                                                                            <th>Type</th>
                                                                            <th>Duration (hr)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($content['breaks'] as $break)
                                                                            <tr>
                                                                                <td>{{ $break['title'] }}</td>
                                                                                <td>{{ $break['type'] }}</td>
                                                                                <td>{{ $break['duration'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <button
                                            wire:click="openAddShiftPanel('{{ $day['full_date'] }}', {{ $employee['id'] }})"
                                            class="btn btn-sm btn-primary add-shift-btn position-absolute tooltip-btn"
                                            style="width: 28px; height: 28px; top: 50%; left: 50%; transform: translate(-50%, -50%); padding: 0; z-index: 20; border-radius: 50%;"
                                            data-tooltip="Add Shift">
                                            +
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>


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
                    <li class="nav-item" wire:click="clickShiftDetailsTab">
                        <button class="nav-link {{ $isShiftTempTab ? '' : 'active' }}" data-bs-toggle="tab"
                            data-bs-target="#shift-details">
                            Details
                        </button>
                    </li>

                    <li class="nav-item ms-2" wire:click="clickTempTab">
                        <button class="nav-link {{ $isShiftTempTab ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#shift-templates">
                            Templates
                        </button>
                    </li>
                </ul>

                {{-- BODY --}}

                <div class="tab-content shift-panel-body px-4 py-3 ">

                    {{-- DETAILS TAB --}}
                    <div class="tab-pane fade {{ $isShiftTempTab ? '' : 'show active' }}" id="shift-details">

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

                                    <a href="#" data-bs-toggle="modal" data-bs-target="#customAddBreakModal"
                                        wire:click="getDefaultBreaks()" class="text-primary small ms-auto"
                                        style="font-size: 0.8rem;">
                                        Edit Breaks
                                    </a>
                                </div>

                                {{-- Repeat + Timezone --}}
                                <div class="d-flex align-items-center mb-3">
                                    <a href="#"
                                        class="me-3 text-primary small {{ $isSavedRepeatShift ? 'p-2 bg-light rounded' : '' }}"
                                        data-bs-toggle="modal" data-bs-target="#customRepeatShiftModal">
                                        <i class="fas fa-redo me-2"></i>
                                        @if ($isSavedRepeatShift)

                                            {{ $frequency }}
                                            @if ($every)
                                                every {{ $every }}
                                            @endif
                                            @if ($repeatOn)
                                                on {{ $repeatOn }}
                                            @endif
                                            @if ($endRepeat === 'After')
                                                ending after {{ $occurrences }} occurrences
                                            @endif
                                        @else
                                            Does not repeat
                                        @endif
                                    </a>

                                    @if (!$isSavedRepeatShift)
                                        <a class="text-primary small">
                                            <i class="fas fa-globe me-2"></i> Europe/London
                                        </a>
                                    @endif
                                </div>
                            @else
                                <div class="d-flex align-items-center mb-3 text-primary" style="font-size: 0.875rem;">
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#customAddBreakModal"
                                        wire:click="getDefaultBreaks()" class="text-primary small me-3">
                                        <i class="fas fa-coffee me-2"></i> Add break
                                    </a>

                                    <a href="#" class="me-3 text-primary small" data-bs-toggle="modal"
                                        data-bs-target="#customRepeatShiftModal">
                                        <i class="fas fa-redo me-2"></i>
                                        @if ($isSavedRepeatShift)
                                            {{ $frequency }}
                                            @if ($every)
                                                every {{ $every }}
                                            @endif
                                            @if ($repeatOn)
                                                on {{ $repeatOn }}
                                            @endif
                                            @if ($endRepeat === 'After')
                                                ending after {{ $occurrences }} occurrences
                                            @endif
                                        @else
                                            Does not repeat
                                        @endif
                                    </a>

                                    @if (!$isSavedRepeatShift)
                                        <a class="text-primary small">
                                            <i class="fas fa-globe me-2"></i> Europe/London
                                        </a>
                                    @endif

                                </div>
                            @endif

                            @if ($isSavedRepeatShift)
                                <div class="d-flex align-items-center mb-3 text-primary" style="font-size: 0.875rem;">
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



                    {{-- TEMPLATES --}}

                    <div class="tab-pane fade {{ $isShiftTempTab ? 'show active' : '' }}" id="shift-templates">
                        <div class="mb-4">

                            @if (count($templates) > 0)
                                <p class="text-dark-50 small">
                                    Select a pre-defined template to quickly populate your shift details.
                                </p>
                            @endif

                        </div>

                        <div class="row g-3">
                            @forelse($templates as $template)
                                <div class="col-12 col-md-6">
                                    <div class="card border-0 shadow-lg position-relative text-white"
                                        style="background-color: #001f3f; width: 100%; min-height: 280px; border-radius: 12px;">

                                        {{-- Delete Icon --}}
                                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            wire:click="deleteTemplate({{ $template->id }})"
                                            style="border-radius: 50%; width: 10px; height: 32px;">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <div class="card-body d-flex flex-column p-4">

                                            <div class="mb-3">
                                                <h5 class="fw-bold mb-2 text-white">{{ $template->title }}</h5>
                                                <span class="badge"
                                                    style="background-color: #e7f1ff; color: #0056b3; font-size: 0.75rem;">
                                                    {{ $template->job ?? 'Standard Shift' }}
                                                </span>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="far fa-clock text-info me-3" style="width: 16px;"></i>
                                                    <div class="small">
                                                        <span class="d-block fw-bold">
                                                            {{ \Carbon\Carbon::parse($template->start_time)->format('h:i A') }}
                                                        </span>
                                                        <span class="d-block">
                                                            {{ \Carbon\Carbon::parse($template->end_time)->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                </div>

                                                @if ($template->address)
                                                    <div class="d-flex align-items-start mb-2">
                                                        <i class="fas fa-map-marker-alt text-warning me-3 mt-1"
                                                            style="width: 16px;"></i>
                                                        <span class="small">{{ $template->address }}</span>
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center mt-3">
                                                    <i class="far fa-calendar-alt text-light me-3"
                                                        style="width: 16px;"></i>
                                                    <span class="small" style="font-size: 0.8rem;">
                                                        {{ $template->created_at->format('d M Y') }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-light w-100 py-2 fw-bold shadow-sm"
                                                    wire:click="applyTemplate({{ $template->id }})"
                                                    style="border-radius: 8px; font-size: 0.9rem;">
                                                    <i class="fas fa-plus-circle me-2"></i> Use Template
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 py-5 text-center">
                                    <p class="text-white-50">No templates saved yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>


                </div>



                {{-- FOOTER --}}

                @if ($isShiftTempTab == false)
                    <div class="shift-panel-footer d-flex align-items-center px-4 py-3 border-top bg-white">
                        <!-- Normal state -->
                        <button class="btn btn-primary" wire:click="publishShift" wire:loading.attr="disabled"
                            wire:target="publishShift">

                            <!-- Normal content -->
                            <span wire:loading.remove wire:target="publishShift">
                                <i class="fas fa-upload me-2"></i> Publish
                            </span>

                            <!-- Loading content -->
                            <span wire:loading wire:target="publishShift">
                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                    aria-hidden="true"></span>
                                Publishing...
                            </span>
                        </button>



                        <div class="d-flex gap-2 ms-auto">
                            <div class="d-flex gap-2 ms-auto">
                                <button class="btn btn-light" wire:click="saveAsTemplate"
                                    wire:loading.attr="disabled" wire:target="saveAsTemplate"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Save as template">
                                    <span wire:loading.remove wire:target="saveAsTemplate">
                                        <i class="far fa-clone"></i>
                                    </span>
                                    <span wire:loading wire:target="saveAsTemplate">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    @endif





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
    document.addEventListener('click', function(e) {

        const btn = e.target.closest('.shift-menu-btn');

        if (btn) {
            const shiftBlock = btn.closest('.shift-block');
            const dropdown = btn
                .closest('.shift-dropdown')
                .querySelector('.dropdown-schedule-celll');

            // reset all
            document.querySelectorAll('.shift-block')
                .forEach(el => el.classList.remove('active-z'));

            document.querySelectorAll('.dropdown-schedule-celll')
                .forEach(el => el !== dropdown && el.classList.add('d-none'));

            // activate current
            shiftBlock.classList.add('active-z');
            dropdown.classList.toggle('d-none');

            e.stopPropagation();
            return;
        }

        // click outside → reset everything
        document.querySelectorAll('.dropdown-schedule-celll')
            .forEach(el => el.classList.add('d-none'));

        document.querySelectorAll('.shift-block')
            .forEach(el => el.classList.remove('active-z'));
    });
</script>
