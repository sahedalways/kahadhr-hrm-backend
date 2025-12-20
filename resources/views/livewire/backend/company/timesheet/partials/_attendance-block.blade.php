@php $modalId = 'att-'.$att['id']; @endphp
<div class="position-relative mb-1 d-flex align-items-center justify-content-center" style="min-height:100%;">
    <div class="shift-block text-white rounded px-1 py-0 mx-auto text-truncate"
        style="background-color:{{ $att['color'] }};font-size:11px;cursor:pointer;max-width:90%;"
        wire:click="viewAttendance({{ $att['id'] }})">
        <div class="fw-semibold">{{ Str::limit($att['title'], 12) }}</div>
        <div>{{ $att['start_time'] }} - {{ $att['end_time'] }}</div>
    </div>
</div>
