@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}" rel="stylesheet" />
@endpush


<div class="container-fluid my-3 schedule-app">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
            @include('livewire.backend.company.schedule.partials.header_nav', [
                'startDate' => $startDate ?? 'Oct 27',
                'endDate' => $endDate ?? 'Nov 2',
            ])
        </div>

        <div class="card-body p-0">
            <div class="d-flex">
                @include('livewire.backend.company.schedule.partials.sidebar')

                <div class="flex-grow-1 schedule-grid-container mt-5" style="overflow-x: auto;">
                    @include('livewire.backend.company.schedule.partials.schedule-grid')

                </div>
            </div>
        </div>

        <div class="card-footer bg-white py-2">
            @include('livewire.backend.company.schedule.partials.footer_summary')
        </div>
    </div>
</div>
