  @php

      $cleanDates = array_filter(array_map(fn($d) => is_string($d) ? $d : null, $selectedDates ?? []));

      sort($cleanDates);

      $selectedDatesCount = count($cleanDates);
  @endphp

  <h6 class="mb-0 fw-semibold">
      @if ($selectedDatesCount === 0)
          <span class="text-muted">Select date(s)</span>
      @elseif($selectedDatesCount === 1)
          {{ \Carbon\Carbon::parse($cleanDates[0])->format('l, M d, Y') }}
      @elseif($selectedDatesCount <= 3)
          @foreach ($cleanDates as $index => $date)
              {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
              @if ($index < $selectedDatesCount - 1)
                  <span class="mx-1">,</span>
              @endif
          @endforeach
      @else
          {{ \Carbon\Carbon::parse($cleanDates[0])->format('M d, Y') }}
          <span class="mx-1">-</span>
          {{ \Carbon\Carbon::parse($cleanDates[$selectedDatesCount - 1])->format('M d, Y') }}
          <span class="badge bg-primary ms-2"
                style="font-size: 10px; padding: 2px 6px;">
              {{ $selectedDatesCount }} days
          </span>
      @endif
  </h6>
