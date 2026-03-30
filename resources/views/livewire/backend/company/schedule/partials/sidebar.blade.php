<div class="pt-4 bg-white h-100">
    <div class="schedule-sidebar p-2 border-end h-100 d-flex flex-column"
         style="width: clamp(250px, 18vw, 280px); flex-shrink: 0;">

        <div class="input-group mb-3">
            <input type="text"
                   class="form-control form-control-sm"
                   placeholder="Search employees..."
                   aria-label="Search"
                   wire:model.live="search"
                   wire:keyup="set('search', $event.target.value)">
        </div>

        <hr class="my-2">

        <div class="py-2 flex-shrink-0">
            <h6 class="fw-bold text-muted text-uppercase mb-0">Employees</h6>

        </div>

        <div class="employee-list mt-2 flex-grow-1 overflow-auto"
             style="min-height: 0;"
             x-data="employeeScroll()"
             @scroll.debounce.100ms="handleScroll"
             x-init="init()">

            @if ($employees->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="fas fa-user-slash fa-2x mb-2"></i>
                    <div>No employees found.</div>
                </div>
            @else
                @foreach ($employees as $employee)
                    <div class="d-flex align-items-center py-3 px-2 employee-row border rounded mb-2"
                         style="cursor: default;"
                         title="{{ $employee['f_name'] }} {{ $employee['l_name'] }}">

                        <div class="position-relative me-3">
                            <img src="{{ $employee['avatar_url'] ?? asset('assets/img/default-avatar.png') }}"
                                 alt="{{ $employee['f_name'] . ' ' . $employee['l_name'] }}"
                                 class="rounded-circle employee-avatar"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        </div>

                        <div class="d-flex flex-column">
                            <span class="fw-semibold">{{ $employee['f_name'] }} {{ $employee['l_name'] }}</span>
                            <small class="text-muted">{{ ucfirst($employee['role'] ?? 'Employee') }}</small>
                        </div>
                    </div>
                @endforeach


                <div x-show="loading"
                     class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary"
                         role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2 text-muted">Loading more employees...</span>
                </div>


                <div x-show="!loading && hasMore"
                     id="employee-list-scroll-trigger"
                     class="text-center py-3 text-muted">
                    <i class="fas fa-arrow-down me-1"></i> Scroll for more employees
                </div>


                <div x-show="!hasMore && !loading && {{ count($employees) }} > 0"
                     class="text-center py-3 text-muted small">
                    <i class="fas fa-check-circle me-1"></i> All employees loaded
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function employeeScroll() {
        return {
            loading: false,
            hasMore: @json($hasMoreEmployees),

            init() {
                this.hasMore = @json($hasMoreEmployees);


                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !this.loading && this.hasMore) {
                            this.loadMore();
                        }
                    });
                }, {
                    threshold: 0.1
                });

                const target = document.querySelector('#employee-list-scroll-trigger');
                if (target) observer.observe(target);
            },

            async handleScroll(event) {
                const element = event.target;
                const scrollTop = element.scrollTop;
                const scrollHeight = element.scrollHeight;
                const clientHeight = element.clientHeight;


                if (scrollTop + clientHeight >= scrollHeight - 50) {
                    if (!this.loading && this.hasMore) {
                        await this.loadMore();
                    }
                }
            },

            async loadMore() {
                this.loading = true;

                try {
                    await @this.loadMoreEmployees();
                    this.hasMore = @json($hasMoreEmployees);
                } catch (error) {
                    console.error('Error loading employees:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
