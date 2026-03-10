<div class="flex-grow-1 schedule-grid-container ">
    <div class="bg-white d-flex justify-content-center align-items-center py-2">
        @include('livewire.backend.employee.schedule.partials.header_nav', [
            'startDate' => $startDate ?? 'Oct 27',
            'endDate' => $endDate ?? 'Nov 2',
        ])
    </div>



    <div class="table-responsive">
        <table class="table table-bordered schedule-table m-0 {{ $viewMode === 'weekly' ? 'weekly-mode' : '' }}">
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
                                                                <div style="background-color:{{ $shift['shift']['color'] }};font-size:11px;cursor:pointer;max-width:90%;"
                                                                     class="position-relative"
                                                                     data-bs-toggle="modal"
                                                                     wire:click='openShiftModalMonthly(@json($shift))'>
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
                    <tr class="weekly-mode-rows">
                        @foreach ($weekDays as $day)
                            @php
                                $content = $this->getCellContent($employee['id'], $day['full_date']);

                                $onLeave = hasLeave($employee['id'], $day['full_date']);
                                $dayLabel = $day['day'] . ' ' . $day['date'];
                            @endphp
                            <td class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}"
                                style="position:relative;"
                                data-day-label="{{ $dayLabel }}">

                                @if ($onLeave)
                                    <div class="d-flex align-items-center justify-content-center h-100 user-select-none"
                                         style="background-color:#f8d7da;opacity:0.7;border-radius:4px;pointer-events:none;">
                                        <span class="text-danger small fw-bold px-2">Unavailable</span>
                                    </div>
                                @elseif ($content && $content['type'] === 'Shift')
                                    <div class="shift-block text-white rounded shadow-sm p-3"
                                         style="background-color:{{ $content['color'] ?? '#6c757d' }};cursor:pointer;transition:all .25s ease-in-out;"
                                         data-bs-toggle="modal"
                                         wire:click='openShiftModalWeekly(@json($content))'>
                                        <div class="small fw-bold text-truncate">
                                            {{ \Illuminate\Support\Str::limit($content['title'], 15) }}
                                        </div>
                                        <div class="smaller opacity-75">{{ $content['time'] }}</div>
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                @endif
            </tbody>
        </table>
    </div>


    <div class="modal fade"
         id="showShiftModalMonthly"
         tabindex="-1"
         aria-hidden="true"
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check fa-lg me-2"></i>
                        <h5 class="modal-title mb-0 text-white">
                            {{ $modalContentMonthly['shift']['title'] ?? 'Shift Details' }}
                        </h5>
                    </div>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    @if ($modalContentMonthly)
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Time:</strong>
                                <span class="ms-1">
                                    {{ \Carbon\Carbon::parse($modalContentMonthly['start_time'])->format('g:i A') }}
                                    -
                                    {{ \Carbon\Carbon::parse($modalContentMonthly['end_time'])->format('g:i A') }}</span>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <strong>Address:</strong>
                                <span class="ms-1">{{ $modalContentMonthly['shift']['address'] ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <i class="fas fa-user text-success me-2"></i>
                            <strong>Employee:</strong>
                            <span class="ms-1">{{ auth()->user()->employee->full_name }}</span>
                        </div>

                        @if (!empty($modalContentMonthly['shift']['note']))
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-sticky-note text-info me-2 mt-1"></i>
                                    <div>
                                        <strong>Note:</strong>
                                        <p class="mb-0">{{ $modalContentMonthly['shift']['note'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (!empty($modalContentMonthly['breaks']))
                            <div class="mb-3">
                                <h6 class="fw-bold text-secondary">Breaks</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Type</th>
                                                <th>Duration (hr)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modalContentMonthly['breaks'] as $break)
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
                    @else
                        <p class="text-muted text-center my-3">No shift data available for this date.</p>
                    @endif
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-1"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>




    <div class="modal fade"
         id="showShiftModalWeekly"
         tabindex="-1"
         aria-hidden="true"
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">

                <!-- Header -->
                <div class="modal-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check fa-lg me-2"></i>
                        <h5 class="modal-title mb-0 text-white">
                            {{ $modalContentWeekly['shift']['title'] ?? 'Shift Details' }}
                        </h5>
                    </div>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    @if ($modalContentWeekly)
                        <div class="row g-3 mb-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Time:</strong>
                                <span class="ms-1">{{ $modalContentWeekly['time'] ?? '-' }}</span>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                <strong>Address:</strong>
                                <span class="ms-1">{{ $modalContentWeekly['shift']['address'] ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <i class="fas fa-user text-success me-2"></i>
                            <strong>Employee:</strong>
                            <span class="ms-1">{{ auth()->user()->employee->full_name }}</span>
                        </div>

                        @if (!empty($modalContentWeekly['shift']['note']))
                            <div class="mb-3 p-3 bg-light rounded">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-sticky-note text-info me-2 mt-1"></i>
                                    <div>
                                        <strong>Note:</strong>
                                        <p class="mb-0">{{ $modalContentWeekly['shift']['note'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (!empty($modalContentWeekly['breaks']))
                            <div class="mb-3">
                                <h6 class="fw-bold text-secondary">Breaks</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle mb-0 text-center">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Title</th>
                                                <th>Type</th>
                                                <th>Duration (hr)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($modalContentWeekly['breaks'] as $break)
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
                    @else
                        <p class="text-muted text-center my-3">No shift data available for this date.</p>
                    @endif
                </div>

                <!-- Footer -->
                <div class="modal-footer border-0">
                    <button type="button"
                            class="btn btn-outline-secondary rounded-1"
                            data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
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


<script>
    (function() {
        function checkMobile() {
            if (window.innerWidth <= 768) {
                document.body.classList.add('mobile-view');
            } else {
                document.body.classList.remove('mobile-view');
            }
        }


        checkMobile();

        window.addEventListener('resize', checkMobile);
    })();
</script>


<script>
    window.addEventListener('showShiftModalWeekly', event => {
        const modal = new bootstrap.Modal(document.getElementById('showShiftModalWeekly'));
        modal.show();
    });


    window.addEventListener('showShiftModalMonthly', event => {
        const modal = new bootstrap.Modal(document.getElementById('showShiftModalMonthly'));
        modal.show();
    });
</script>
