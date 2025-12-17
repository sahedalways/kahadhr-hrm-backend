@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}" rel="stylesheet" />
@endpush


<div class="container-fluid my-3 schedule-app">
    <div class="ms-3 mt-2">
        <h5 class="mb-0 fw-bold">Schedule</h5>
    </div>

    <div class="card shadow-sm">

        <div class="card-body p-0 position-relative">

            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 position-absolute"
                style="left: 50%">
                @include('livewire.backend.company.schedule.partials.header_nav', [
                    'startDate' => $startDate ?? 'Oct 27',
                    'endDate' => $endDate ?? 'Nov 2',
                ])
            </div>

            <div class="d-flex">
                @if ($viewMode === 'weekly')
                    @include('livewire.backend.company.schedule.partials.sidebar')
                @endif

                <div class="flex-grow-1 schedule-grid-container" style="overflow-x: auto; margin-top: 5rem !important;">
                    @include('livewire.backend.company.schedule.partials.schedule-grid')
                </div>
            </div>
        </div>

        <div class="card-footer bg-white py-2">
            @include('livewire.backend.company.schedule.partials.footer_summary')
        </div>
    </div>
</div>
