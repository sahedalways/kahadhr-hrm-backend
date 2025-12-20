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
                <div class="w-100">
                    @include('livewire.backend.employee.schedule.partials.schedule-grid')
                </div>
            </div>
        </div>

        <div class=" bg-white py-2">
            @include('livewire.backend.employee.schedule.partials.footer_summary')
        </div>
    </div>
</div>
