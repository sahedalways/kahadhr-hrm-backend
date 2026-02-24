@php
    $rtw = $this->rightToWorkStatus;
    $latestShareDoc = $rtw['doc'];
    $daysLeft = $rtw['daysLeft'];
@endphp

<div class="col-12 mt-3">
    <div class="p-3 rounded-3 border border-1 border-light bg-white">
        <label class="d-block text-muted x-small text-uppercase fw-bold mb-1">
            Right to Work Expiry
        </label>

        @if ($employee->nationality === 'British')
            <span class="badge"
                  style="background:#0d6efd; color:#fff; font-weight:600; border:1px solid #0d6efd;">
                Permanent
            </span>
        @else
            @if ($latestShareDoc && $latestShareDoc->expires_at)
                <strong class="{{ $daysLeft !== null && $daysLeft <= 60 ? 'blink-red' : '' }}"
                        style="color: {{ $daysLeft !== null && $daysLeft <= 60 ? '#dc3545' : '#198754' }};
                               font-size: 16px; display: block;">
                    {{ \Carbon\Carbon::parse($latestShareDoc->expires_at)->format('d F Y') }}
                </strong>

                <span class="text-muted x-small">
                    {{ $daysLeft !== null ? ($daysLeft < 0 ? 'Expired' : "$daysLeft days left") : '' }}
                </span>
            @else
                <span class="badge bg-light text-muted"
                      style="border: 1px dashed #ced4da;">
                    Not Verified
                </span>
            @endif
        @endif
    </div>
</div>
