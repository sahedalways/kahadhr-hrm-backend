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

                         <div class="d-flex align-items-center justify-content-end">
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


         {{-- Leave Balance Section --}}
         @if ($filterEmployeeId && $selectedEmployeeForYear)
             <div class="col-12 mb-4">
                 <div class="card border-0 shadow-sm">
                     <div class="card-header bg-gradient-primary text-white py-3"
                          style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                         <div class="d-flex align-items-center justify-content-between">
                             <div>
                                 <h5 class="mb-0 fw-bold text-white">
                                     <i class="fas fa-chart-line me-2 "></i>
                                     Leave Balance Overview
                                 </h5>
                                 <small class="text-white-50">
                                     {{ optional($employees->firstWhere('user_id', $filterEmployeeId))->full_name }} -
                                     Current Leave Statistics
                                 </small>
                             </div>
                             <div class="text-end">
                                 <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
                             </div>
                         </div>
                     </div>
                     <div class="card-body p-4">
                         <div class="row g-4">
                             {{-- Annual Leave Card --}}
                             <div class="col-md-6">
                                 <div class="card h-100 border-0 shadow-sm hover-shadow transition-all"
                                      style="transition: transform 0.3s ease-in-out;">
                                     <div class="card-body p-4">
                                         <div class="d-flex align-items-center justify-content-between mb-4">
                                             <div>
                                                 <h6 class="text-uppercase text-muted mb-1 fw-semibold"
                                                     style="letter-spacing: 0.5px;">
                                                     <i class="fas fa-calendar-check text-success me-1"></i> Annual
                                                     Leave
                                                 </h6>
                                                 <h3 class="mb-0 fw-bold text-success">
                                                     {{ number_format($remainingAnnualHours, 2) }}
                                                     <small class="text-muted fs-6">hours</small>
                                                 </h3>
                                                 <small class="text-muted">Remaining Balance</small>
                                             </div>
                                             <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                                 <i class="fas fa-umbrella-beach fa-2x text-white"></i>
                                             </div>
                                         </div>

                                         <div class="progress mb-3"
                                              style="height: 8px;">
                                             @php
                                                 $annualPercentage =
                                                     $totalAnnualHours > 0
                                                         ? ($usedAnnualHours / $totalAnnualHours) * 100
                                                         : 0;
                                             @endphp
                                             <div class="progress-bar bg-success"
                                                  style="width: {{ min(100, $annualPercentage) }}%"
                                                  role="progressbar"
                                                  aria-valuenow="{{ $annualPercentage }}"
                                                  aria-valuemin="0"
                                                  aria-valuemax="100">
                                             </div>
                                         </div>

                                         <div class="row mt-3">
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Total</div>
                                                     <div class="fw-bold text-dark">
                                                         {{ number_format($totalAnnualHours, 2) }}</div>
                                                 </div>
                                             </div>
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Used</div>
                                                     <div class="fw-bold text-warning">
                                                         {{ number_format($usedAnnualHours, 2) }}</div>
                                                 </div>
                                             </div>
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Remaining</div>
                                                     <div class="fw-bold text-success">
                                                         {{ number_format($remainingAnnualHours, 2) }}</div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             {{-- Leave in Lieu Card --}}
                             <div class="col-md-6">
                                 <div class="card h-100 border-0 shadow-sm hover-shadow transition-all"
                                      style="transition: transform 0.3s ease-in-out;">
                                     <div class="card-body p-4">
                                         <div class="d-flex align-items-center justify-content-between mb-4">
                                             <div>
                                                 <h6 class="text-uppercase text-muted mb-1 fw-semibold"
                                                     style="letter-spacing: 0.5px;">
                                                     <i class="fas fa-hourglass-half text-primary me-1"></i> Leave in
                                                     Lieu
                                                 </h6>
                                                 <h3 class="mb-0 fw-bold text-primary">
                                                     {{ number_format($remainingLeaveInLiewHours, 2) }}
                                                     <small class="text-muted fs-6">hours</small>
                                                 </h3>
                                                 <small class="text-muted">Remaining Balance</small>
                                             </div>
                                             <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                                 <i class="fas fa-exchange-alt fa-2x text-white"></i>
                                             </div>
                                         </div>

                                         <div class="progress mb-3"
                                              style="height: 8px;">
                                             @php
                                                 $liewPercentage =
                                                     $totalLeaveInLiewHours > 0
                                                         ? ($usedLeaveInLiewHours / $totalLeaveInLiewHours) * 100
                                                         : 0;
                                             @endphp
                                             <div class="progress-bar bg-primary"
                                                  style="width: {{ min(100, $liewPercentage) }}%"
                                                  role="progressbar"
                                                  aria-valuenow="{{ $liewPercentage }}"
                                                  aria-valuemin="0"
                                                  aria-valuemax="100">
                                             </div>
                                         </div>

                                         <div class="row mt-3">
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Total</div>
                                                     <div class="fw-bold text-dark">
                                                         {{ number_format($totalLeaveInLiewHours, 2) }}</div>
                                                 </div>
                                             </div>
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Used</div>
                                                     <div class="fw-bold text-secondary">
                                                         {{ number_format($usedLeaveInLiewHours, 2) }}</div>
                                                 </div>
                                             </div>
                                             <div class="col-4">
                                                 <div class="text-center">
                                                     <div class="text-muted small mb-1">Remaining</div>
                                                     <div class="fw-bold text-primary">
                                                         {{ number_format($remainingLeaveInLiewHours, 2) }}</div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                         {{-- Additional Info Banner --}}
                         @if ($totalAnnualHours == 0 && $totalLeaveInLiewHours == 0)
                             <div class="alert alert-info mt-4 mb-0">
                                 <i class="fas fa-info-circle me-2"></i>
                                 No leave balance records found for this employee.
                             </div>
                         @endif
                     </div>
                 </div>
             </div>
         @endif
     @endif
