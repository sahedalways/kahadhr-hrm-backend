     @if ($filterEmployeeId && $selectedEmployeeForYear)
         <div class="col-12">
             <div class="card shadow-sm mb-4">
                 <div class="card-header bg-white p-3">

                     <div class="d-flex align-items-center justify-content-between">


                         <div>
                             <h5 class="mb-0">
                                 <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                 {{ optional($employees->firstWhere('user_id', $filterEmployeeId))->full_name }} - Leave
                                 Calendar
                             </h5>
                         </div>

                         <div class="d-flex justify-content-center flex-grow-1">
                             <div class="btn-group align-items-center">

                                 <button wire:click="changeYear('prev')"
                                         class="btn btn-sm btn-outline-secondary">
                                     <i class="fas fa-chevron-left"></i>
                                 </button>

                                 <span class="px-3 fw-bold text-primary border-start border-end">
                                     {{ $selectedYear }}
                                 </span>

                                 <button wire:click="changeYear('next')"
                                         class="btn btn-sm btn-outline-secondary">
                                     <i class="fas fa-chevron-right"></i>
                                 </button>

                             </div>
                         </div>


                         <div>
                             <button wire:click="backToMonthlyView"
                                     class="btn btn-sm btn-outline-primary">
                                 <i class="fas fa-calendar-week me-1"></i>
                                 Back to Monthly View
                             </button>
                         </div>

                     </div>

                 </div>
             </div>

             <div class="card-body">
                 {{-- Year Calendar Grid --}}
                 <div class="year-calendar-grid">
                     @php
                         $months = [];
                         for ($m = 1; $m <= 12; $m++) {
                             $months[] = \Carbon\Carbon::create($selectedYear, $m, 1);
                         }
                     @endphp

                     <div class="row">
                         @foreach ($months as $month)
                             <div class="col-md-3 col-sm-6 mb-4">
                                 <div class="month-card border rounded">
                                     <div class="month-header bg-light p-2 text-center fw-bold">
                                         {{ $month->format('F') }}
                                     </div>
                                     <div class="month-days p-2">
                                         @php
                                             $daysInMonth = $month->daysInMonth;
                                             $firstDayOfMonth = $month->copy()->startOfMonth();
                                             $startOffset = $firstDayOfMonth->dayOfWeek;
                                         @endphp

                                         <div class="day-names d-grid"
                                              style="grid-template-columns: repeat(7, 1fr);">
                                             @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                                                 <div class="text-center small text-muted">
                                                     {{ $day }}
                                                 </div>
                                             @endforeach
                                         </div>

                                         <div class="calendar-days d-grid"
                                              style="grid-template-columns: repeat(7, 1fr); gap: 2px;">
                                             @for ($i = 0; $i < $startOffset; $i++)
                                                 <div class="text-center text-muted small">-</div>
                                             @endfor

                                             @for ($day = 1; $day <= $daysInMonth; $day++)
                                                 @php
                                                     $currentDate = $month->copy()->day($day);
                                                     $leaveOnDate = $yearlyLeaves->first(function ($leave) use (
                                                         $currentDate,
                                                     ) {
                                                         $start = \Carbon\Carbon::parse($leave['start']);
                                                         $end = \Carbon\Carbon::parse($leave['end']);
                                                         return $currentDate->between($start, $end);
                                                     });
                                                     $isWeekend = in_array($currentDate->dayOfWeek, [
                                                         \Carbon\Carbon::SATURDAY,
                                                         \Carbon\Carbon::SUNDAY,
                                                     ]);
                                                 @endphp

                                                 <div class="calendar-day text-center p-1 position-relative
                                                            {{ $isWeekend ? 'bg-light' : '' }}
                                                            {{ $leaveOnDate ? 'has-leave' : '' }}"
                                                      style="font-size: 0.8rem;
                                                            {{ $leaveOnDate ? 'background-color: ' . ($leaveOnDate['color'] ?? '#f0f0f0') . '20' : '' }}">
                                                     <span class="day-number">{{ $day }}</span>
                                                     @if ($leaveOnDate)
                                                         <span class="leave-emoji position-absolute top-0 end-0 small"
                                                               style="font-size: 10px;">
                                                             {{ $leaveOnDate['emoji'] }}
                                                         </span>
                                                     @endif
                                                 </div>
                                             @endfor
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                     </div>
                 </div>



             </div>
         </div>
         </div>
     @endif
