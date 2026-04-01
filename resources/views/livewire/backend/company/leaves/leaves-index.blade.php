@push('styles')
    <link href="{{ asset('assets/css/manage-leave.css') }}"
          rel="stylesheet" />
@endpush

<div>

    <div class="container-fluid my-4">
        <div class="row g-3">

            <div class="d-flex justify-content-between align-items-center mb-3">


                <div class="col-auto">
                    <h5 class="fw-500 text-primary m-0">Leave Management</h5>
                </div>

                <!-- Right: Button -->
                @if ($filterEmployeeId && $selectedEmployeeForYear)
                    <div>
                        <button wire:click="backToMonthlyView"
                                class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-calendar-week me-1"></i>
                            Back to Monthly View
                        </button>
                    </div>
                @endif

            </div>

            @php
                use Carbon\Carbon;

                $currentDate = Carbon::now();
                $year = request('year', $currentDate->year);
                $month = request('month', $currentDate->month);

                $currentDate = Carbon::create($year, $month, 1);
                $prevMonth = $currentDate->copy()->subMonth();
                $nextMonth = $currentDate->copy()->addMonth();

                $daysInMonth = $currentDate->daysInMonth;

                $dates = [];
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $date = Carbon::create($year, $month, $d);
                    $dates[] = [
                        'day' => $d,
                        'letter' => $date->format('D')[0],
                        'date' => $date->format('Y-m-d'),
                        'is_weekend' => in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                    ];
                }
            @endphp


            @include('livewire.backend.company.leaves.partials.yearly-leave')



            @if (!$filterEmployeeId && !$selectedEmployeeForYear)
                @include('livewire.backend.company.leaves.partials.monthly-leave')
            @endif


        </div>



        <script>
            window.addEventListener('show-leave-modal', event => {
                let modalEl = document.getElementById('viewRequestInfoFromCalendar');
                let modal = new bootstrap.Modal(modalEl);
                modal.show();
            });
        </script>


        <script>
            function confirmCancel(id) {
                if (confirm('Are you sure you want to cancel this leave?')) {
                    @this.cancelLeave(id);
                }
            }
        </script>
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('reload-page', () => {

                    const url = new URL(window.location.href);
                    url.searchParams.delete('leave');


                    window.history.replaceState({}, document.title, url.toString());


                    window.location.reload();
                });

                Livewire.on('refresh-page-after-update', () => {

                    const url = new URL(window.location.href);
                    url.searchParams.delete('leave');

                    window.history.replaceState({}, document.title, url.toString());


                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);

                });
            });
        </script>




        <script>
            window.addEventListener('show-leave-modal-for-status-change', function() {
                let modalEl = document.getElementById('viewRequestInfo');
                if (!modalEl) return;

                let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });
        </script>
