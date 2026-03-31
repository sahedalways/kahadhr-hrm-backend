@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}"
          rel="stylesheet" />


    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@endpush


<div class="schedule-app {{ $viewMode === 'weekly' ? 'min-width-weekly' : '' }}">

    <div class="d-flex justify-content-between align-items-center ms-3 mt-2 mb-2">


        <h5 class="fw-bold mb-0">Schedule</h5>

        <!-- Right Side (All Buttons) -->
        <div class="d-flex gap-2 align-items-center">

            @if ($viewMode === 'weekly')
                <button class="btn btn-outline-primary btn-sm"
                        wire:click="loadWeeklyTemplates"
                        data-bs-toggle="modal"
                        data-bs-target="#loadWeekTemplateModal"
                        title="Load weekly template">
                    <i class="fas fa-download me-1"></i> Import Week
                </button>

                <button class="btn btn-outline-success btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#saveWeekTemplateModal"
                        title="Save current week as template">
                    <i class="fas fa-save me-1"></i> Save Week
                </button>
            @endif


            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle"
                        type="button"
                        id="downloadDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fas fa-download me-1"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end"
                    aria-labelledby="downloadDropdown">
                    <li>
                        <a class="dropdown-item"
                           href="#"
                           wire:click.prevent="downloadSchedulePDF">
                            <i class="fas fa-file-pdf me-1"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                           href="#"
                           id="downloadScheduleImg">
                            <i class="fas fa-image me-1"></i> Image
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <div class="shadow-sm overflow-y-auto">

        <div class=" p-0 position-relative">



            <div class="d-flex all-schedule-info">

                @if ($isLoading)
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"
                             role="status">
                            <span class="visually-hidden">Loading shifts...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading schedule data...</p>
                    </div>
                @else
                    <div class="w-100">
                        @include('livewire.backend.company.schedule.partials.schedule-grid')
                    </div>
                @endif

            </div>
        </div>

        <div class=" bg-white py-2">
            @include('livewire.backend.company.schedule.partials.footer_summary')
        </div>
    </div>
    <style>
        .min-width-weekly {
            min-width: max-content;
        }
    </style>

    @include('livewire.backend.company.schedule.partials.save-week-template-modal')
    @include('livewire.backend.company.schedule.partials.load-week-template-modal')
</div>


<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    document.getElementById('downloadScheduleImg').addEventListener('click', function() {
        const container = document.querySelector('.all-schedule-info');


        const desktopWidth = 1400;

        html2canvas(container, {
            scrollY: -window.scrollY,
            useCORS: true,
            width: desktopWidth,
            windowWidth: desktopWidth,
            scale: 2,
            onclone: (clonedDoc) => {

                const clonedContainer = clonedDoc.querySelector('.all-schedule-info');
                clonedContainer.style.width = desktopWidth + 'px';
                clonedContainer.style.display = 'flex';
            }
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'schedule.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
</script>


<script>
    document.addEventListener('livewire:init', function() {

        Livewire.on('start-loading-shifts', () => {

            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'shifts-loading-overlay';
            loadingOverlay.innerHTML = `
            <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                 style="background: rgba(0,0,0,0.5); z-index: 9999;">
                <div class="bg-white p-4 rounded shadow text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div>Loading shifts...</div>
                </div>
            </div>
        `;
            document.body.appendChild(loadingOverlay);
        });


        Livewire.on('shifts-loaded', () => {
            const overlay = document.getElementById('shifts-loading-overlay');
            if (overlay) {
                overlay.remove();
            }
        });


        Livewire.hook('commit', ({
            component,
            commit,
            respond,
            fail
        }) => {
            if (component.name === 'backend.company.schedule.schedule-index') {

            }
        });
    });
</script>


<script>
    document.addEventListener('livewire:navigating', () => {

        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            const dropdown = bootstrap.Dropdown.getInstance(menu.parentElement);
            if (dropdown) dropdown.hide();
        });
    });

    document.addEventListener('livewire:load', function() {

        Livewire.hook('message.processed', () => {
            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(trigger => {
                if (!trigger.hasAttribute('data-bs-toggle-processed')) {
                    trigger.setAttribute('data-bs-toggle-processed', 'true');
                    new bootstrap.Dropdown(trigger);
                }
            });
        });
    });
</script>
