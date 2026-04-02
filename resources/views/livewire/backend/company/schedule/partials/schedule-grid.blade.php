<div class="flex-grow-1 schedule-grid-container">
    <div class="bg-white d-flex justify-content-center align-items-center py-2 px-3">
        @include('livewire.backend.company.schedule.partials.header_nav', [
            'startDate' => $startDate ?? 'Oct 27',
            'endDate' => $endDate ?? 'Nov 2',
        ])
    </div>
    <div class="table-responsive">
        <table class="table table-bordered schedule-table m-0">


            <tbody>
                @if ($viewMode === 'monthly')
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">

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

                                    $dateInMonth = $this->startDate ?? \Carbon\Carbon::now();
                                    $startOfMonth = \Carbon\Carbon::parse($dateInMonth)->startOfMonth();
                                    $endOfMonth = \Carbon\Carbon::parse($dateInMonth)->endOfMonth();

                                    // Always start calendar from Monday
                                    $calendarStart = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                                    $calendarEnd = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

                                    $dates = [];
                                    $current = $calendarStart->copy();
                                    while ($current->lte($calendarEnd) && count($dates) < 42) {
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
                                                x-data="{ hover: false }"
                                                @mouseenter="hover = true"
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

                                                                <div class="shift-block text-white rounded px-1 py-0 mb-3 mx-auto"
                                                                     style="background-color:{{ $shift['shift']['color'] }};font-size:11px;cursor:pointer;">
                                                                    <div class="fw-semibold text-truncate"
                                                                         style="max-width: 100%;">
                                                                        {{ $shift['shift']['title'] }}</div>
                                                                    <div>
                                                                        {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                                                    </div>
                                                                </div>


                                                                <div class="dropdown shift-dropdown position-absolute"
                                                                     style="top: 0; right: 7px; z-index: 1050;">
                                                                    <button class="btn btn-xs btn-link text-white p-0"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false">
                                                                        <i class="fa-solid fa-bars"></i>
                                                                    </button>
                                                                    <ul
                                                                        class="dropdown-menu dropdown-menu-end shadow-sm dropdown-schedule-cell">
                                                                        <li>
                                                                            @foreach ($shift['employees'] as $emp)
                                                                                <button class="dropdown-item"
                                                                                        type="button"
                                                                                        wire:click="editOneEmpShift({{ $shift['id'] }}, {{ $emp['id'] }})">
                                                                                    <i
                                                                                       class="fas fa-edit fa-fw me-1"></i>
                                                                                    Edit {{ $emp['name'] }}
                                                                                </button>
                                                                            @endforeach
                                                                        </li>
                                                                        <li>
                                                                            <button class="dropdown-item"
                                                                                    type="button"
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






                                                            <div class="modal fade"
                                                                 id="{{ $modalId }}"
                                                                 tabindex="-1"
                                                                 aria-hidden="true"
                                                                 wire:ignore.self
                                                                 data-bs-backdrop="static"
                                                                 data-bs-keyboard="false">
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
                                                                                                    <td> <span
                                                                                                              class="badge bg-{{ $break['type'] === 'Paid' ? 'success' : 'secondary' }}">
                                                                                                            {{ $break['type'] }}
                                                                                                        </span>
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
                                                        <button wire:click="openAddShiftPanelForMonth('{{ $day->format('Y-m-d') }}')"
                                                                class="btn btn-sm btn-primary position-absolute tooltip-btn"
                                                                style="width: 28px; height: 28px; top: 50%; left: 50%;
                           transform: translate(-50%, -50%);
                           padding: 0; border-radius: 50%; z-index: 10;"
                                                                x-show="hover"
                                                                data-tooltip="Add Shift">
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
                    <thead>
                        <tr class="text-center">
                            <th class="bg-light border-bottom-0 align-middle"
                                style="width: 280px; min-width: 280px; vertical-align: top;">
                                <div class="d-flex flex-column gap-2">
                                    {{-- Header Title --}}
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="fw-bold text-dark">
                                            <i class="fas fa-users me-2 text-primary"></i>Employees
                                        </div>
                                        <span class="badge bg-primary rounded-pill"
                                              id="employeeCount">
                                            {{ count($employees) }}
                                        </span>
                                    </div>

                                    {{-- Search Input --}}
                                    <div class="position-relative">
                                        <i class="fas fa-search position-absolute text-muted"
                                           style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 12px;"></i>
                                        <input type="text"
                                               class="form-control form-control-sm ps-5"
                                               placeholder="Search by name..."
                                               aria-label="Search employees"
                                               wire:model.live="search"
                                               wire:keyup.debounce.300ms="set('search', $event.target.value)"
                                               style="border-radius: 20px; background-color: #f8f9fa; border: 1px solid #e9ecef;">

                                        <div wire:loading
                                             wire:target="search"
                                             class="text-center py-3">
                                            <div class="spinner-border spinner-border-sm text-primary"
                                                 role="status"></div>
                                            <span class="ms-2 text-muted">Searching...</span>
                                        </div>

                                        @if ($search)
                                            <button wire:click="set('search', '')"
                                                    class="btn btn-link btn-sm position-absolute text-muted"
                                                    style="right: 8px; top: 50%; transform: translateY(-50%); padding: 0; text-decoration: none;">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        @endif
                                    </div>


                                </div>
                            </th>
                            @foreach ($weekDays as $day)
                                <th class="{{ $day['highlight'] ? 'bg-primary-light text-primary border-bottom-0' : 'bg-light border-bottom-0' }}"
                                    style="width: 14.28%">
                                    <div class="fw-bold">{{ $day['day'] }}</div>
                                    <div class="small">{{ $day['date'] }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
            <tbody>


                @forelse ($employees as $employee)
                    <tr x-data="employeeScroll()"
                        @scroll.debounce.100ms="handleScroll"
                        x-init="init()">

                        <td class="align-middle"
                            style="background-color: #f8f9fa; width: 120px; min-width: 120px;">
                            <div class="d-flex align-items-center">
                                <img src="{{ $employee['avatar_url'] ?? asset('assets/img/default-avatar.png') }}"
                                     alt="{{ $employee['f_name'] . ' ' . $employee['l_name'] }}"
                                     class="rounded-circle me-2"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                                <div>
                                    <div class="fw-semibold">
                                        {{ \Illuminate\Support\Str::limit($employee['f_name'] . ' ' . $employee['l_name'], 30, '...') }}
                                    </div>
                                    <small class="text-muted">{{ ucfirst($employee['role'] ?? 'Employee') }}</small>
                                </div>
                            </div>
                        </td>

                        {{-- Schedule Cells for each day --}}
                        @foreach ($weekDays as $day)
                            @php
                                $content = $this->getCellContent($employee['id'], $day['full_date']);
                                $hoverKey = $employee['id'] . '_' . $day['full_date'];
                            @endphp
                            <td class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}"
                                style="position: relative; vertical-align: middle;"
                                wire:mouseenter="$set('hoveredCell', '{{ $hoverKey }}')"
                                wire:mouseleave="$set('hoveredCell', null)"
                                x-data="{ dragging: false }"
                                x-on:drop.prevent="if ($el.querySelector('.user-select-none')) return; $wire.handleDrop('{{ $day['full_date'] }}', {{ $employee['id'] }})"
                                x-on:dragover.prevent="dragging = true"
                                x-on:dragleave="dragging = false"
                                :class="{ 'bg-light': dragging }">


                                @php
                                    $onLeave = hasLeave($employee['id'], $day['full_date']);
                                @endphp

                                @if ($onLeave)
                                    {{-- Leave cell --}}
                                    <div class="d-flex align-items-center justify-content-center h-100 user-select-none"
                                         style="background-color: #f8d7da; opacity: 0.7; border-radius: 4px;
            pointer-events: none; min-height: 80px;">
                                        <span class="text-danger small fw-bold px-2">Unavailable</span>
                                    </div>
                                @elseif ($content && $content['type'] === 'Shift')
                                    <div class="shift-block text-white rounded position-relative shadow-sm p-2"
                                         style="background-color: {{ $content['color'] ?? '#6c757d' }}; cursor: pointer; transition: all .25s ease-in-out; min-height: 70px;"
                                         draggable="true"
                                         x-on:dragstart="$wire.handleDrag('{{ $day['full_date'] }}', {{ $employee['id'] }}, {{ $content['id'] }})">
                                        <div class="small fw-bold text-truncate"
                                             style="max-width: 100%;">
                                            {{ \Illuminate\Support\Str::limit($content['title'], 15) }}
                                        </div>
                                        <div class="smaller opacity-75">{{ $content['time'] }}</div>

                                        {{-- Single trigger + menu --}}
                                        <div class="shift-dropdown position-absolute"
                                             style="top: 2px; right: 6px;">
                                            <button type="button"
                                                    class="btn btn-xs btn-link text-white p-0 shift-menu-btn">
                                                <i class="fa-solid fa-bars"></i>
                                            </button>

                                            <ul class="dropdown-schedule-celll d-none"
                                                wire:ignore.self>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center"
                                                            type="button"
                                                            wire:click="editOneEmpShift({{ $content['id'] }}, {{ $employee['id'] }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="editOneEmpShift({{ $content['id'] }}, {{ $employee['id'] }})">

                                                        <i class="fas fa-edit fa-fw me-1"
                                                           wire:loading.remove
                                                           wire:target="editOneEmpShift({{ $content['id'] }}, {{ $employee['id'] }})"></i>

                                                        Edit

                                                        <span wire:loading
                                                              wire:target="editOneEmpShift({{ $content['id'] }}, {{ $employee['id'] }})"
                                                              class="spinner-border spinner-border-sm ms-auto"></span>
                                                    </button>
                                                </li>




                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center"
                                                            type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#shiftDetailsModal-{{ $employee['id'] }}-{{ \Str::slug($content['title']) }}">

                                                        <i class="fas fa-eye fa-fw me-1"></i>
                                                        View
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger d-flex align-items-center"
                                                            type="button"
                                                            wire:click="deleteShiftOneEmp({{ $content['id'] }}, {{ $employee['id'] }})"
                                                            onclick="if(!confirm('Are you sure?')) event.stopImmediatePropagation()"
                                                            wire:loading.attr="disabled"
                                                            wire:target="deleteShiftOneEmp({{ $content['id'] }}, {{ $employee['id'] }})">

                                                        <i class="fas fa-trash-alt fa-fw me-1"
                                                           wire:loading.remove
                                                           wire:target="deleteShiftOneEmp({{ $content['id'] }}, {{ $employee['id'] }})"></i>

                                                        Delete

                                                        <span wire:loading
                                                              wire:target="deleteShiftOneEmp({{ $content['id'] }}, {{ $employee['id'] }})"
                                                              class="spinner-border spinner-border-sm ms-auto"></span>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>

                                        {{-- Modal --}}

                                    </div>

                                    <div class="modal fade"
                                         id="shiftDetailsModal-{{ $employee['id'] }}-{{ \Str::slug($content['title']) }}"
                                         tabindex="-1"
                                         aria-hidden="true"
                                         wire:ignore.self
                                         data-bs-backdrop="static"
                                         data-bs-keyboard="false">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
                                                <!-- Header -->
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title text-white mb-0">
                                                        <i class="fas fa-calendar-check me-2"></i>
                                                        {{ $content['title'] ?? 'Shift Details' }}
                                                    </h5>
                                                    <button type="button"
                                                            class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>

                                                <!-- Body -->
                                                <div class="modal-body">
                                                    <!-- Time & Address row -->
                                                    <div class="row mb-3">
                                                        <div class="col-sm-6">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="fas fa-clock text-primary me-2"></i>
                                                                <strong>Time:</strong>
                                                                <span
                                                                      class="ms-1">{{ $content['time'] ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                                <strong>Address:</strong>
                                                                <span
                                                                      class="ms-1">{{ $content['shift']['address'] ?? '-' }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Employees -->
                                                    @if (!empty($content['employees']))
                                                        @php
                                                            $employeesList = collect($content['employees'])
                                                                ->pluck('name')
                                                                ->toArray();
                                                            $showLimit = 5;
                                                            $moreCount = count($employeesList) - $showLimit;
                                                        @endphp
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="fas fa-users text-success me-2"></i>
                                                            <strong>Employees:</strong>
                                                            <span class="ms-1">
                                                                {{ implode(', ', array_slice($employeesList, 0, $showLimit)) }}
                                                                @if ($moreCount > 0)
                                                                    <span class="text-muted">+{{ $moreCount }}
                                                                        more</span>
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endif

                                                    <!-- Note -->
                                                    @if (!empty($content['shift']['note']))
                                                        <div class="d-flex align-items-start mb-3">
                                                            <i class="fas fa-sticky-note text-info me-2 mt-1"></i>
                                                            <div>
                                                                <strong>Note:</strong>
                                                                <p class="mb-0">
                                                                    {{ $content['shift']['note'] }}</p>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Breaks -->
                                                    @if (!empty($content['breaks']))
                                                        <div class="mb-0">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <i class="fas fa-coffee text-warning me-2"></i>
                                                                <strong>Breaks:</strong>
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Title</th>
                                                                            <th>Type</th>
                                                                            <th>Duration (hrs)</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($content['breaks'] as $break)
                                                                            <tr>
                                                                                <td>{{ $break['title'] }}</td>
                                                                                <td>
                                                                                    <span
                                                                                          class="badge bg-{{ $break['type'] === 'Paid' ? 'success' : 'secondary' }}">
                                                                                        {{ $break['type'] }}
                                                                                    </span>
                                                                                </td>
                                                                                <td>{{ $break['duration'] }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Footer -->
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-1"></i> Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="openAddShiftPanel('{{ $day['full_date'] }}', {{ $employee['id'] }})"
                                            class="btn btn-sm btn-primary add-shift-btn position-absolute tooltip-btn"
                                            style="width: 28px; height: 28px; top: 50%; left: 50%;
                   transform: translate(-50%, -50%); padding: 0; z-index: 20; border-radius: 50%;"
                                            data-tooltip="Add Shift">
                                        +
                                    </button>
                                @endif

                            </td>
                        @endforeach
                    </tr>

                @empty
                    <tr>
                        <td colspan="{{ count($weekDays) + 1 }}">
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <div>No employees found.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse

                @if ($hasMoreEmployees)
                    <tr id="employee-list-scroll-trigger"
                        wire:key="scroll-trigger">
                        <td colspan="{{ count($weekDays) + 1 }}"
                            class="py-2 text-start">
                            <div class="text-muted small">
                                <i class="fas fa-arrow-down me-1"></i> Scroll for more employees
                            </div>
                        </td>
                    </tr>
                @endif


            </tbody>
            @endif
            </tbody>
        </table>
    </div>


    @if ($showAddShiftPanel)
        <div class="shift-panel-overlay"
             wire:click="closeAddShiftPanel">
            <div class="shift-panel"
                 @click.stop>

                {{-- HEADER --}}
                <div
                     class="shift-panel-header d-flex align-items-center justify-content-between px-4 py-3 border-bottom">

                    @include('livewire.backend.company.schedule.partials._selected-date-range')

                    <button type="button"
                            wire:click="closeAddShiftPanel"
                            class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center position-relative"
                            style="width: 28px; height: 28px; padding: 0;"
                            wire:loading.attr="disabled"
                            wire:target="closeAddShiftPanel">


                        <i class="fas fa-times"
                           style="font-size: 14px; color: #000;"
                           wire:loading.remove
                           wire:target="closeAddShiftPanel"></i>

                        <span wire:loading
                              wire:target="closeAddShiftPanel"
                              class="spinner-border spinner-border-sm text-dark"></span>
                    </button>
                </div>

                {{-- TABS --}}
                <ul class="nav nav-tabs shift-tabs px-3 pt-2">
                    <li class="nav-item"
                        wire:click="clickShiftDetailsTab">
                        <button class="nav-link {{ $isShiftTempTab ? '' : 'active' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#shift-details">
                            Details
                        </button>
                    </li>

                    <li class="nav-item ms-2"
                        wire:click="clickTempTab">
                        <button class="nav-link {{ $isShiftTempTab ? 'active' : '' }}"
                                data-bs-toggle="tab"
                                data-bs-target="#shift-templates">
                            Templates
                        </button>
                    </li>
                </ul>

                {{-- BODY --}}

                <div class="tab-content shift-panel-body px-4 py-3 ">

                    {{-- DETAILS TAB --}}
                    <div class="tab-pane fade {{ $isShiftTempTab ? '' : 'show active' }}"
                         id="shift-details">

                        <form wire:submit.prevent="saveShift"
                              class="d-flex flex-column gap-3">

                            {{-- DATE --}}
                            <div class="row align-items-md-center g-2 mb-3 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label class="fw-semibold mb-1 mb-md-0">
                                        Date <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <!-- Input + Switch -->
                                <div class="col-12 col-md-9">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">
                                        <div class="input-group input-group-sm"
                                             style="max-width: 260px;"
                                             x-data="{
                                                 selectedDates: @entangle('selectedDates'),
                                                 selectedDateDisplay: @entangle('selectedDateDisplay'),
                                                 fp: null
                                             }"
                                             x-init="() => {
                                                 fp = flatpickr($refs.datepicker, {
                                                     dateFormat: 'Y-m-d',
                                                     mode: 'multiple',
                                                     defaultDate: selectedDates.length > 0 ? selectedDates : null,
                                                     allowInput: false,
                                                     disableMobile: true,
                                                     nextArrow: '<i class=\'fas fa-chevron-right\'></i>',
                                                     prevArrow: '<i class=\'fas fa-chevron-left\'></i>',
                                                     onReady: function(selectedDatesArr, dateStr, instance) {
                                                         if (selectedDatesArr.length === 0) {
                                                             instance.calendarContainer.querySelectorAll('.today')
                                                                 .forEach(el => el.classList.remove('today'));
                                                         }
                                             
                                                         if (selectedDatesArr.length > 0) {
                                                             selectedDateDisplay = dateStr;
                                                         }
                                                     },
                                                     onChange: function(selectedDatesArr, dateStr, instance) {
                                                         if (selectedDatesArr.length === 0) {
                                                             if (selectedDates && selectedDates.length > 0) {
                                                                 instance.setDate(selectedDates, false);
                                                             } else {
                                                                 // Don't auto-set today's date
                                                                 selectedDates = [];
                                                                 selectedDateDisplay = '';
                                             
                                                                 @this.set('selectedDates', []);
                                                                 @this.set('selectedDateDisplay', '');
                                                                 @this.set('selectedDate', null);
                                                             }
                                                             return;
                                                         }
                                             
                                                         const formattedDates = selectedDatesArr.map(d => instance.formatDate(d, 'Y-m-d'));
                                             
                                                         selectedDates = formattedDates;
                                                         selectedDateDisplay = dateStr;
                                             
                                                         @this.set('selectedDates', formattedDates);
                                                         @this.set('selectedDateDisplay', dateStr);
                                                         @this.set('selectedDate', formattedDates[0] ?? null);
                                                         @this.call('updateSelectedDates', formattedDates);
                                                     }
                                                 });
                                             
                                                 // Watch for changes from Livewire
                                                 $watch('selectedDates', (value) => {
                                                     if (fp) {
                                                         if (value && value.length > 0) {
                                                             fp.setDate(value, false);
                                                         } else {
                                                             fp.clear();
                                                             // Don't set any default date
                                                         }
                                                     }
                                                 });
                                             
                                                 // Listen for reset event
                                                 window.addEventListener('reset-flatpickr', () => {
                                                     if (fp) {
                                                         fp.clear();
                                                     }
                                                     selectedDates = [];
                                                     selectedDateDisplay = '';
                                                 });
                                             }"
                                             @reset-flatpickr.window="
                 if (fp) {
                     fp.clear();
                 }
                 selectedDates = [];
                 selectedDateDisplay = '';
             ">
                                            <input class="form-control"
                                                   type="text"
                                                   x-ref="datepicker"
                                                   x-model="selectedDateDisplay"
                                                   placeholder="Select date"
                                                   readonly
                                                   style="background-color: #e9ecef; cursor: pointer;"
                                                   wire:ignore
                                                   onkeydown="return false;">
                                            <span class="input-group-text bg-white"
                                                  style="cursor: pointer;"
                                                  @click="$refs.datepicker._flatpickr.open()">
                                                <i class="far fa-calendar-alt text-muted"></i>
                                            </span>
                                        </div>

                                        <!-- All day switch -->
                                        <div class="form-check form-switch ms-md-auto col-md-auto">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   wire:model="newShift.all_day"
                                                   wire:change="toggleOnAllDay($event.target.checked)">
                                            <label class="form-check-label small text-muted ms-1">All day</label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            {{-- TIME --}}
                            <div class="row align-items-md-center g-2 mb-1 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label class="fw-semibold mb-1 mb-md-0">
                                        Time <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <!-- Time inputs -->
                                <div class="col-12 col-md-9">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">

                                        <!-- Start time -->
                                        <input type="time"
                                               class="form-control form-control-sm"
                                               wire:model.live="newShift.start_time"
                                               wire:change="calculateTotalHours()"
                                               @if ($newShift['all_day']) readonly @endif>

                                        <span class="text-muted d-none d-md-inline">→</span>

                                        <!-- End time -->
                                        <input type="time"
                                               class="form-control form-control-sm"
                                               wire:model.live="newShift.end_time"
                                               wire:change="calculateTotalHours()"
                                               @if ($newShift['all_day']) readonly @endif>

                                        <!-- Total hours -->
                                        <span class="small fw-semibold ms-md-auto col-md-auto"
                                              style="color:#000;">
                                            {{ $newShift['total_hours'] ?? '08:00' }} hrs
                                        </span>

                                    </div>

                                    <!-- Errors -->
                                    @error('newShift.start_time')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('newShift.end_time')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
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

                                    <a href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#customAddBreakModal"
                                       wire:click="getDefaultBreaks()"
                                       class="text-primary small ms-auto"
                                       style="font-size: 0.8rem;">
                                        Edit Breaks
                                    </a>
                                </div>

                                {{-- Repeat + Timezone --}}
                                <div class="d-flex align-items-center mb-3">
                                    <a href="#"
                                       class="me-3 text-primary small {{ $isSavedRepeatShift ? 'p-2 bg-light rounded' : '' }}"
                                       data-bs-toggle="modal"
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
                            @else
                                <div class="gap-2 d-flex align-items-center flex-wrap mb-3 text-primary"
                                     style="font-size: 0.875rem;">
                                    <a href="#"
                                       data-bs-toggle="modal"
                                       data-bs-target="#customAddBreakModal"
                                       wire:click="getDefaultBreaks()"
                                       class="text-primary small">
                                        <i class="fas fa-coffee me-2"></i> Add break
                                    </a>

                                    <a href="#"
                                       class="text-primary small"
                                       data-bs-toggle="modal"
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
                                <div class="d-flex align-items-center mb-3 text-primary"
                                     style="font-size: 0.875rem;">
                                    <a class="text-primary small">
                                        <i class="fas fa-globe me-2"></i> Europe/London
                                    </a>
                                </div>
                            @endif



                            {{-- TITLE --}}
                            <div class="row align-items-md-center g-2 mb-3 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label for="shiftTitleInput"
                                           class="fw-semibold mb-1 mb-md-0">
                                        Shift Title <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <!-- Input -->
                                <div class="col-12 col-md-9">
                                    <input type="text"
                                           id="shiftTitleInput"
                                           wire:model.defer="newShift.title"
                                           class="form-control form-control-sm"
                                           placeholder="Enter shift title…"
                                           required>

                                    @error('newShift.title')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            {{-- JOB + COLOR --}}
                            <div class="row align-items-md-center g-2 mb-3 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label class="fw-semibold mb-1 mb-md-0">
                                        Job <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <!-- Input + Color -->

                                <div class="col-12 col-md-9">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">




                                        <!-- Job input second -->
                                        <input type="text"
                                               class="form-control form-control-sm order-md-2 flex-grow-1"
                                               wire:model.defer="newShift.job"
                                               placeholder="Enter job…">

                                    </div>

                                    <!-- Error -->
                                    @error('newShift.job')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="row align-items-md-center g-2 mb-3 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label class="fw-semibold mb-1 mb-md-0">
                                        Shift Color <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <!-- Input + Color -->

                                <div class="col-12 col-md-9">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center gap-2">


                                        <div class="order-md-1">
                                            <input type="color"
                                                   wire:model="newShift.color"
                                                   class="form-control form-control-color border-0 p-0"
                                                   style="width: 38px; height: 31px;">
                                        </div>


                                    </div>


                                </div>
                            </div>




                            {{-- EMPLOYEES --}}
                            @include('livewire.backend.company.schedule.partials.employees-box')

                            {{-- ADDRESS --}}
                            <div class="row align-items-md-center g-2 mb-3 shift-form-row">
                                <!-- Label -->
                                <div class="col-12 col-md-3">
                                    <label class="fw-semibold mb-1 mb-md-0">
                                        Address
                                    </label>
                                </div>

                                <!-- Input -->
                                <div class="col-12 col-md-9">
                                    <input type="text"
                                           wire:model.defer="newShift.address"
                                           class="form-control form-control-sm"
                                           placeholder="Enter address…">

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

                                    <textarea wire:model.defer="newShift.note"
                                              class="form-control form-control-sm"
                                              rows="3"
                                              placeholder="Add a note…"></textarea>

                                    @error('newShift.note')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror


                                </div>
                            </div>

                        </form>
                    </div>



                    {{-- TEMPLATES --}}

                    <div class="tab-pane fade {{ $isShiftTempTab ? 'show active' : '' }}"
                         id="shift-templates">
                        <div class="mb-4">
                            @if (count($templates) > 0)
                                <p class="text-dark-50 small">
                                    Select a pre-defined template to quickly populate your shift details.
                                </p>
                            @endif

                        </div>

                        <div class="row g-3">
                            @forelse($templates as $template)
                                <div class="col-12 col-md-6 d-flex">
                                    <div class="card border-0 shadow-lg position-relative text-white w-100 d-flex flex-column"
                                         style="background-color: #001f3f; border-radius: 12px; min-height: 280px;">

                                        {{-- Delete Icon --}}
                                        <button class="btn btn-sm btn-danger position-absolute top-0 end-0 d-flex justify-content-center align-items-center"
                                                wire:click="deleteTemplate({{ $template->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="deleteTemplate({{ $template->id }})"
                                                style="border-radius: 50%; width: 28px; height: 28px; padding: 0; z-index: 1;">

                                            <i class="fas fa-trash-alt"
                                               wire:loading.remove
                                               wire:target="deleteTemplate({{ $template->id }})"
                                               style="font-size: 0.8rem;"></i>

                                            <span wire:loading
                                                  wire:target="deleteTemplate({{ $template->id }})"
                                                  class="spinner-border spinner-border-sm"
                                                  role="status"
                                                  aria-hidden="true">
                                            </span>
                                        </button>

                                        <div class="card-body d-flex flex-column p-4 h-100">

                                            <div class="mb-3">
                                                <h5 class="fw-bold mb-2 text-white">
                                                    {{ strlen($template->title) > 10 ? substr($template->title, 0, 10) . '...' : $template->title }}
                                                </h5>
                                                <span class="badge d-inline-block"
                                                      style="background-color: #e7f1ff; color: #0056b3; font-size: 0.75rem;"
                                                      title="{{ $template->job ?? 'Standard Shift' }}">
                                                    {{ \Illuminate\Support\Str::limit($template->job ?? 'Standard Shift', 10, '...') }}
                                                </span>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="far fa-clock text-info me-3"
                                                       style="width: 16px;"></i>
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
                                                        <span class="small text-break"
                                                              title="{{ $template->address }}">
                                                            {{ \Illuminate\Support\Str::limit($template->address, 10, '...') }}
                                                        </span>
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center mt-3">
                                                    <i class="far fa-calendar-alt text-light me-3"
                                                       style="width: 16px;"></i>
                                                    <span class="small"
                                                          style="font-size: 0.8rem;">
                                                        @if ($template->dates)
                                                            @php
                                                                $dates = json_decode($template->dates, true);
                                                                $dateCount = count($dates);
                                                                $formattedDates = array_map(function ($date) {
                                                                    return \Carbon\Carbon::parse($date)->format(
                                                                        'M j, Y',
                                                                    );
                                                                }, $dates);
                                                            @endphp

                                                            @if ($dateCount == 1)
                                                                {{ $formattedDates[0] }}
                                                            @elseif($dateCount == 2)
                                                                {{ $formattedDates[0] }} & {{ $formattedDates[1] }}
                                                            @else
                                                                {{ $formattedDates[0] }}, {{ $formattedDates[1] }}
                                                                <span class="badge bg-secondary rounded-pill ms-1">
                                                                    +{{ $dateCount - 2 }} more
                                                                </span>
                                                            @endif
                                                        @else
                                                            {{ \Carbon\Carbon::parse($template->created_at)->format('M j, Y') }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-light w-100 py-2 fw-bold shadow-sm"
                                                        wire:click="applyTemplate({{ $template->id }})"
                                                        wire:loading.attr="disabled"
                                                        wire:target="applyTemplate({{ $template->id }})"
                                                        style="border-radius: 8px; font-size: 0.9rem;">

                                                    <span wire:loading.remove
                                                          wire:target="applyTemplate({{ $template->id }})">
                                                        <i class="fas fa-plus-circle me-2"></i> Use Template
                                                    </span>

                                                    <span wire:loading
                                                          wire:target="applyTemplate({{ $template->id }})">
                                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                                        Applying...
                                                    </span>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 py-5 text-center">
                                    <p class="text-dark-50">No templates saved yet.</p>
                                </div>
                            @endforelse
                        </div>

                        @if ($loaded >= $perPage)
                            <div class="text-center mt-3">
                                <button wire:click="loadMore"
                                        wire:loading.attr="disabled"
                                        class="btn btn-outline-primary"
                                        style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <span wire:loading.remove>Load More</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                            </div>
                        @endif
                    </div>


                </div>



                {{-- FOOTER --}}

                @if ($isShiftTempTab == false)
                    <div class="shift-panel-footer d-flex align-items-center px-4 py-3 border-top bg-white">
                        <!-- Normal state -->


                        <button class="btn btn-primary"
                                wire:click="{{ $isEditableShift ? 'updateShift' : 'publishShift' }}"
                                wire:loading.attr="disabled"
                                wire:target="{{ $isEditableShift ? 'updateShift' : 'publishShift' }}">


                            <span wire:loading.remove
                                  wire:target="{{ $isEditableShift ? 'updateShift' : 'publishShift' }}">
                                <i class="fas fa-upload me-2"></i>
                                {{ $isEditableShift ? 'Update Shift' : 'Publish Shift' }}
                            </span>


                            <span wire:loading
                                  wire:target="{{ $isEditableShift ? 'updateShift' : 'publishShift' }}">
                                <span class="spinner-border spinner-border-sm me-2"
                                      role="status"
                                      aria-hidden="true"></span>
                                Publishing...
                            </span>
                        </button>





                        <div class="d-flex gap-2 ms-auto">
                            <button class="btn btn-light btn-sm"
                                    wire:click="saveAsTemplate"
                                    wire:loading.attr="disabled"
                                    wire:target="saveAsTemplate"
                                    data-bs-toggle="tooltip"
                                    title="Save as template">

                                <span wire:loading.remove
                                      wire:target="saveAsTemplate">
                                    <i class="far fa-clone me-1"></i> Save as Template
                                </span>

                                <span wire:loading
                                      wire:target="saveAsTemplate">
                                    <span class="spinner-border spinner-border-sm"
                                          role="status"
                                          aria-hidden="true"></span>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </div>
                @endif


            </div>
        </div>
    @endif


    @include('livewire.backend.company.schedule.partials.multiple-shift-modal')
    @include('livewire.backend.company.schedule.partials.repeat-shift-modal')
    @include('livewire.backend.company.schedule.partials.break-modal')
    @include('livewire.backend.company.schedule.partials._conflict-shift-modal')

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
            const shiftDropdown = btn.closest('.shift-dropdown');
            const dropdown = shiftDropdown.querySelector('.dropdown-schedule-celll');


            document.querySelectorAll('.shift-block').forEach(el => {
                el.classList.remove('active-z');
            });

            document.querySelectorAll('.dropdown-schedule-celll').forEach(el => {
                if (el !== dropdown) {
                    el.classList.add('d-none');
                    el.style.display = '';
                    el.style.position = '';
                    el.style.top = '';
                    el.style.bottom = '';
                    el.style.left = '';
                    el.style.right = '';
                }
            });


            if (dropdown.classList.contains('d-none')) {

                const rect = btn.getBoundingClientRect();


                dropdown.style.position = 'fixed';
                dropdown.style.display = 'block';
                dropdown.style.visibility = 'hidden';


                const actualMenuHeight = dropdown.offsetHeight;

                dropdown.style.visibility = '';


                dropdown.style.left = 'auto';
                dropdown.style.right = (window.innerWidth - rect.right) + 'px';
                dropdown.style.top = (rect.bottom + 5) + 'px';
                dropdown.style.bottom = 'auto';


                const dropdownBottom = rect.bottom + 5 + actualMenuHeight;

                if (dropdownBottom > window.innerHeight) {

                    dropdown.style.top = 'auto';
                    dropdown.style.bottom = (window.innerHeight - rect.top + 5) + 'px';
                }


                const testRect = dropdown.getBoundingClientRect();
                if (testRect.left < 0) {
                    dropdown.style.left = '10px';
                    dropdown.style.right = 'auto';
                }


                shiftBlock.classList.add('active-z');

                dropdown.classList.remove('d-none');
            } else {
                dropdown.classList.add('d-none');
                shiftBlock.classList.remove('active-z');
            }

            e.stopPropagation();
            return;
        }


        document.querySelectorAll('.dropdown-schedule-celll').forEach(el => {
            el.classList.add('d-none');
            el.style.display = '';
            el.style.position = '';
        });

        document.querySelectorAll('.shift-block').forEach(el => {
            el.classList.remove('active-z');
        });
    });


    window.addEventListener('scroll', function() {
        document.querySelectorAll('.dropdown-schedule-celll:not(.d-none)').forEach(el => {
            el.classList.add('d-none');
            el.closest('.shift-block')?.classList.remove('active-z');
        });
    }, true);

    window.addEventListener('resize', function() {
        document.querySelectorAll('.dropdown-schedule-celll:not(.d-none)').forEach(el => {
            el.classList.add('d-none');
            el.closest('.shift-block')?.classList.remove('active-z');
        });
    });


    document.addEventListener('DOMContentLoaded', function() {

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const dropdown = mutation.target;
                    if (!dropdown.classList.contains('d-none')) {

                        const shiftBlock = dropdown.closest('.shift-dropdown')?.closest(
                            '.shift-block');
                        if (shiftBlock) {
                            shiftBlock.style.zIndex = '9999998';
                        }
                    } else {
                        const shiftBlock = dropdown.closest('.shift-dropdown')?.closest(
                            '.shift-block');
                        if (shiftBlock) {
                            shiftBlock.style.zIndex = '';
                        }
                    }
                }
            });
        });

        document.querySelectorAll('.dropdown-schedule-celll').forEach(el => {
            observer.observe(el, {
                attributes: true
            });
        });
    });
</script>




<script>
    function employeeScroll() {
        return {
            loading: false,
            hasMore: @json($hasMoreEmployees ?? false),
            page: 1,

            init() {
                this.hasMore = @json($hasMoreEmployees ?? false);
                console.log('init called, hasMore:', this.hasMore);
                this.setupScrollObserver();
            },

            setupScrollObserver() {

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.loading && this.hasMore) {

                            this.loadMore();
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px 100px 0px'
                });


                const target = document.querySelector('#employee-list-scroll-trigger');
                if (target) {
                    console.log('Observer attached to trigger');
                    observer.observe(target);
                } else {
                    console.log('Trigger element not found');
                }
            },

            async handleScroll(event) {
                const element = event.target;
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;


                if (scrollTop + clientHeight >= scrollHeight - 100) {
                    if (!this.loading && this.hasMore) {

                        await this.loadMore();
                    }
                }
            },

            async loadMore() {
                if (this.loading || !this.hasMore) {

                    return;
                }


                this.loading = true;

                try {
                    await @this.call('loadMoreEmployees');
                    this.hasMore = @json($hasMoreEmployees ?? false);

                } catch (error) {
                    console.error('Error loading employees:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
