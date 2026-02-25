@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}"
          rel="stylesheet" />
@endpush


<div class="schedule-app {{ $viewMode === 'weekly' ? 'min-width-weekly' : '' }}">

    <div class="d-flex justify-content-between align-items-center ms-3 mt-2">

        <h5 class="fw-bold mb-0">Schedule</h5>


        <div class="dropdown mb-0">
            <button class="btn btn-primary dropdown-toggle"
                    type="button"
                    id="downloadDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <i class="fas fa-download me-1"></i> Download
            </button>
            <ul class="dropdown-menu"
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

    <div class="shadow-sm overflow-y-auto">

        <div class=" p-0 position-relative">



            <div class="d-flex all-schedule-info">
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
    <style>
        .min-width-weekly {
            min-width: max-content;
        }
    </style>
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
