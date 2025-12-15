<div class="flex-grow-1 schedule-grid-container" style="overflow-x: auto;">
    <table class="table table-bordered schedule-table m-0">
        <thead>
            <tr class="text-center">
                @foreach ($weekDays as $day)
                    <th class="{{ $day['highlight'] ? 'bg-primary-light text-primary border-bottom-0' : 'bg-light border-bottom-0' }}"
                        style="width: {{ $viewMode === 'weekly' ? '14.28%' : '3.2%' }}">

                        <div class="fw-bold">{{ $day['day'] }}</div>

                        @if ($viewMode === 'weekly')
                            <div class="small">{{ $day['date'] }}</div>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-clock me-1"></i> 0
                                <i class="fas fa-users me-1 ms-2"></i> 0
                            </div>
                        @endif

                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>


            @foreach ($employees as $employee)
                <tr>
                    @foreach ($weekDays as $day)
                        @php
                            $content = $this->getCellContent($employee['id'], $day['full_date']);
                        @endphp

                        <td class="schedule-cell {{ $day['highlight'] ? 'bg-primary-light-cell' : '' }}">

                            @if ($content && $content['type'] === 'Leave')
                                <div
                                    class="unpaid-leave text-center p-1 rounded {{ $day['highlight'] ? 'unpaid-leave-highlight' : '' }}">
                                    <div class="small fw-bold">
                                        {{ $viewMode === 'weekly' ? $content['label'] : 'Leave' }}
                                    </div>

                                    @if ($viewMode === 'weekly')
                                        <div class="smaller text-muted">All day</div>
                                    @endif
                                </div>
                            @elseif ($content && $content['type'] === 'Shift')
                                <div class="shift-block {{ $content['color'] }} text-white p-1 rounded">
                                    <div class="small fw-bold">Shift</div>

                                    @if ($viewMode === 'weekly')
                                        <div class="smaller">{{ $content['time'] }}</div>
                                    @endif
                                </div>
                            @endif

                        </td>
                    @endforeach
                </tr>
            @endforeach

        </tbody>
    </table>
</div>
