<div class="flex-grow-1 schedule-grid-container">
    <div class="bg-white d-flex justify-content-center align-items-center py-2">
        @include('livewire.backend.employee.schedule.partials.header_nav', [
            'startDate' => $startDate ?? 'Oct 27',
            'endDate' => $endDate ?? 'Nov 2',
        ])
    </div>

    <div class="table-responsive">
        <table class="table table-bordered schedule-table m-0">
            {{-- ---------- WEEKLY MODE HEADER ---------- --}}
            @if ($viewMode === 'weekly')
                <thead>
                    <tr class="text-center">
                        @foreach ($weekDays as $day)
                            <th class="{{ $day['highlight'] ? 'bg-primary-light text-primary border-bottom-0' : 'bg-light border-bottom-0' }}"
                                style="width:14.28%">
                                <div class="fw-bold">{{ $day['day'] }}</div>
                                <div class="small">{{ $day['date'] }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody>
                {{-- ---------- MONTHLY MODE ---------- --}}
                @if ($viewMode === 'monthly')

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


                    <tr>
                        <td colspan="7"
                            class="p-0 border-0">
                            <table class="table table-bordered text-center mb-0"
                                   style="table-layout:fixed;width:100%;">
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
                                    @foreach ($weeks as $week)
                                        <tr>
                                            @foreach ($week as $day)
                                                @php

                                                    $isInCurrentMonth = $day->month == $dateInMonth->month;
                                                @endphp
                                                <td class="schedule-cell month-cell {{ $day->equalTo(\Carbon\Carbon::today()) ? 'bg-primary-light-cell' : '' }}"
                                                    style="position:relative;height:80px;width:14.285%;"
                                                    x-data="{ hover: false }"
                                                    @mouseenter="hover=true"
                                                    @mouseleave="hover=false">

                                                    @if ($isInCurrentMonth)
                                                        <div class="small text-end p-1 text-dark {{ $day->equalTo(\Carbon\Carbon::today()) ? 'fw-bold' : '' }}"
                                                             style="font-size:0.85rem;">
                                                            {{ $day->day }}
                                                        </div>

                                                        @php
                                                            $dateKey = $day->format('Y-m-d');
                                                            $hasShift = !empty($this->calendarShifts[$dateKey]);
                                                        @endphp

                                                        @if ($hasShift)
                                                            @foreach ($this->calendarShifts[$dateKey] as $shift)
                                                                @php $modalId = 'shiftDetailsModal-'.$shift['id']; @endphp
                                                                <div style="background-color:{{ $shift['shift']['color'] }};font-size:11px;cursor:pointer;max-width:90%;"
                                                                     class="position-relative"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#{{ $modalId }}">
                                                                    <div
                                                                         class=" shift-block text-white rounded px-1 py-0 mb-1 mx-auto">
                                                                        <div class="fw-semibold text-truncate">
                                                                            {{ $shift['shift']['title'] }}
                                                                        </div>
                                                                        <div>
                                                                            {{ \Carbon\Carbon::parse($shift['start_time'])->format('g:i A') }}
                                                                            -
                                                                            {{ \Carbon\Carbon::parse($shift['end_time'])->format('g:i A') }}
                                                                        </div>
                                                                    </div>


                                                                </div>

                                                                {{-- Modal --}}
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
                                                                            <div
                                                                                 class="modal-header bg-primary text-white">
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

                                                                                <div class="mb-2 mt-2">
                                                                                    <strong>Employee:</strong>
                                                                                    {{ auth()->user()->employee->full_name }}
                                                                                </div>

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
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    {{-- ---------- WEEKLY MODE BODY ---------- --}}
                @else
                    @php
                        $employee = auth()->user()->employee;
                    @endphp
                    <tr>
                        @foreach ($weekDays as $day)
                            @php
                                $content = $this->getCellContent($employee['id'], $day['full_date']);

                                $onLeave = hasLeave($employee['id'], $day['full_date']);
                            @endphp
                            <td class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}"
                                style="position:relative;">

                                @if ($onLeave)
                                    <div class="d-flex align-items-center justify-content-center h-100 user-select-none"
                                         style="background-color:#f8d7da;opacity:0.7;border-radius:4px;pointer-events:none;">
                                        <span class="text-danger small fw-bold px-2">Unavailable</span>
                                    </div>
                                @elseif ($content && $content['type'] === 'Shift')
                                    @php $modalId = 'shiftDetailsModal-'.$employee['id'].'-'.\Str::slug($content['title']); @endphp
                                    <div class="shift-block text-white rounded position-relative shadow-sm p-3"
                                         style="background-color:{{ $content['color'] ?? '#6c757d' }};cursor:pointer;top:50%;left:50%;transform:translate(-50%,-50%);transition:all .25s ease-in-out;"
                                         data-bs-toggle="modal"
                                         data-bs-target="#{{ $modalId }}">
                                        <div class="small fw-bold text-truncate">
                                            {{ \Illuminate\Support\Str::limit($content['title'], 15) }}
                                        </div>
                                        <div class="smaller opacity-75">{{ $content['time'] }}</div>
                                    </div>

                                    {{-- Modal --}}
                                    <div class="modal fade"
                                         id="{{ $modalId }}"
                                         tabindex="-1"
                                         aria-hidden="true"
                                         wire:ignore.self
                                         data-bs-backdrop="static"
                                         data-bs-keyboard="false">
                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                            <div class="modal-content">
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
                                                <div class="modal-body">
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

                                                    @if (!empty($content['employees']))
                                                        @php
                                                            // keep only the row owner
                                                            $employees = collect($content['employees'])
                                                                ->where('id', $employee['id'])
                                                                ->pluck('name')
                                                                ->toArray();
                                                        @endphp

                                                        @if ($employees)
                                                            <div class="d-flex align-items-center mb-3">
                                                                <i class="fas fa-users text-success me-2"></i>
                                                                <strong>Employee:</strong>
                                                                <span class="ms-1">{{ $employees[0] }}</span>
                                                            </div>
                                                        @endif
                                                    @endif

                                                    @if (!empty($content['shift']['note']))
                                                        <div class="d-flex align-items-start mb-3">
                                                            <i class="fas fa-sticky-note text-info me-2 mt-1"></i>
                                                            <div>
                                                                <strong>Note:</strong>
                                                                <p class="mb-0">{{ $content['shift']['note'] }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endif

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
                                                                            <th>Duration (hr)</th>
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
                                                                                <td>{{ $break['duration'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
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
                                @endif
                            </td>
                        @endforeach
                    </tr>

                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.shift-menu-btn');
        if (btn) {
            const shiftBlock = btn.closest('.shift-block');
            const dropdown = btn.closest('.shift-dropdown').querySelector('.dropdown-schedule-celll');

            document.querySelectorAll('.shift-block').forEach(el => el.classList.remove('active-z'));
            document.querySelectorAll('.dropdown-schedule-celll').forEach(el => el !== dropdown && el.classList
                .add('d-none'));

            shiftBlock.classList.add('active-z');
            dropdown.classList.toggle('d-none');

            e.stopPropagation();
            return;
        }
        document.querySelectorAll('.dropdown-schedule-celll').forEach(el => el.classList.add('d-none'));
        document.querySelectorAll('.shift-block').forEach(el => el.classList.remove('active-z'));
    });
</script>
