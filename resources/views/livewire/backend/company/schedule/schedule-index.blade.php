@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}" rel="stylesheet" />
@endpush


<div class="schedule-app">
    <div class="ms-3 mt-2">
        <h5 class=" fw-bold">Schedule</h5>
    </div>

    <div class=" shadow-sm">

        <div class=" p-0 position-relative">



            <div class="d-flex">
                @if ($viewMode === 'weekly')
                    @include('livewire.backend.company.schedule.partials.sidebar')
                @endif

                <div class="w-100">
                    @include('livewire.backend.company.schedule.partials.schedule-grid')
                </div>
            </div>
        </div>

        <div class=" bg-white py-2">
            @include('livewire.backend.company.schedule.partials.footer_summary')
        </div>
    </div>

    @include('livewire.backend.company.schedule.partials.multiple-shift-modal')
    @include('livewire.backend.company.schedule.partials.repeat-shift-modal')
    @include('livewire.backend.company.schedule.partials.break-modal')
    @include('livewire.backend.company.schedule.partials._conflict-shift-modal')
</div>
