<div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
    <div class="d-flex align-items-center gap-3">


        <div class="dropdown me-1">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                {{ $viewMode === 'weekly' ? 'Week' : 'Month' }}
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item @if ($viewMode === 'weekly') active @endif" href="#"
                        wire:click="setViewMode('weekly')">Week</a></li>
                <li><a class="dropdown-item @if ($viewMode === 'monthly') active @endif" href="#"
                        wire:click="setViewMode('monthly')">Month</a></li>
            </ul>
        </div>


        <div class="d-flex align-items-center border rounded p-2 me-4 flex-nowrap">


            <button wire:click="goToPrevious" class="btn btn-sm btn-link text-dark p-0 me-2">
                <i class="fas fa-chevron-left"></i>
            </button>


            <span class="fw-bold text-nowrap">{{ $this->displayDateRange }}</span>

            <button wire:click="goToNext" class="btn btn-sm btn-link text-dark p-0 ms-2">
                <i class="fas fa-chevron-right"></i>
            </button>

            <button class="btn btn-sm btn-link text-primary p-0 ms-3">
                <i class="fas fa-calendar-alt"></i>
            </button>
        </div>
    </div>


</div>
