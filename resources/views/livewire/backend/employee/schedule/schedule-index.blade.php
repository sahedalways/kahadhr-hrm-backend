@push('styles')
    <link href="{{ asset('assets/css/company-schedule.css') }}"
          rel="stylesheet" />
@endpush


<div class="schedule-app">
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

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
    document.getElementById('downloadScheduleImg').addEventListener('click', function() {
        const element = document.querySelector('.schedule-grid-container');


        const desktopWidth = 1280;

        html2canvas(element, {
            useCORS: true,
            allowTaint: true,
            width: desktopWidth,
            windowWidth: desktopWidth,
            scale: 2,
            onclone: (clonedDoc) => {

                const clonedElement = clonedDoc.querySelector('.schedule-grid-container');
                clonedElement.style.width = desktopWidth + 'px';
                clonedElement.style.overflow =
                    'visible';
            }
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'schedule.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });
</script>
