@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}" rel="stylesheet" />
@endpush


<div class="container-fluid my-3 schedule-app">
    <div class="ms-3 mt-2">
        <h5 class="mb-0 fw-bold">Schedule</h5>
    </div>

    <div class="card shadow-sm">

        <div class="card-body p-0 position-relative">

           

            <div class="d-flex">
                @if ($viewMode === 'weekly')
                    @include('livewire.backend.company.schedule.partials.sidebar')
                @endif

                <div>
                    @include('livewire.backend.company.schedule.partials.schedule-grid')
                </div>
            </div>
        </div>

        <div class="card-footer bg-white py-2">
            @include('livewire.backend.company.schedule.partials.footer_summary')
        </div>
    </div>

    @include('livewire.backend.company.schedule.partials.multiple-shift-modal')
    @include('livewire.backend.company.schedule.partials.repeat-shift-modal')
    @include('livewire.backend.company.schedule.partials.break-modal')
</div>
