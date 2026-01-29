<div>
    <div class="row g-3 align-items-center justify-content-between mb-4">

        <!-- LEFT: Title -->
        <div class="col-auto">
            <h5 class="fw-500 text-primary m-0">Companies Management</h5>
        </div>



        <!-- RIGHT: Export Buttons -->
        <div class="col-auto d-flex gap-2">


            <button wire:click="exportCompanies('pdf')"
                    class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportCompanies('excel')"
                    class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportCompanies('csv')"
                    class="btn btn-sm btn-white text-info">
                <i class="fa fa-file-csv me-1"></i> CSV
            </button>
        </div>

    </div>


    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-body">
                    <div class="row g-3 align-items-center mb-3">
                        <!-- Search Input -->
                        <div class="col-lg-8 col-md-6 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                       class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text"
                                       class="form-control border-start-0"
                                       placeholder="Search by company name, email, or phone"
                                       wire:model="search"
                                       wire:keyup="set('search', $event.target.value)" />
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="col-lg-4 col-md-6 col-12 d-flex gap-2">
                            <select class="form-select form-select-lg"
                                    wire:change="handleSort($event.target.value)">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>


                            <select class="form-select form-select-lg"
                                    wire:change="handleFilter($event.target.value)">

                                <option value="Active"
                                        selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                    </div>

                    <!-- Live Search Result Indicator -->
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for: <strong>{{ $search ?: 'All Companies' }}</strong>
                        </p>
                        <div wire:loading
                             wire:target="search">
                            <span class="spinner-border spinner-border-sm text-primary"
                                  role="status"
                                  aria-hidden="true"></span>
                            <span class="text-primary small">Searching...</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Company Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Employee</th>
                                    <th>Storage (MB)</th>
                                    <th>Register</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($infos as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>

                                        <td>
                                            <a href="{{ route('super-admin.company.details.show', $row->id) }}"
                                               class="badge badge-xs text-primary"
                                               style="
        background: transparent;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
   "
                                               onmouseover="this.style.textDecoration='underline'"
                                               onmouseout="this.style.textDecoration='none'">
                                                {{ $row->company_name ?? 'N/A' }}
                                            </a>


                                        </td>

                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_email ?? '' }}')"
                                                  onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                  onmouseout="this.style.backgroundColor='transparent';"
                                                  style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  class="tooltip-btn"
                                                  data-tooltip="Click to copy"
                                                  aria-label="copy email">
                                                {{ $row->company_email ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span onclick="copyToClipboard('{{ $row->company_mobile ?? '' }}')"
                                                  onmouseover="this.style.backgroundColor='#f0f0f0';"
                                                  onmouseout="this.style.backgroundColor='transparent';"
                                                  style="cursor: pointer; color: inherit; padding: 2px 4px; border-radius: 4px;"
                                                  data-bs-toggle="tooltip"
                                                  data-bs-placement="top"
                                                  class="tooltip-btn"
                                                  data-tooltip="Click to copy"
                                                  aria-label="copy email">
                                                {{ $row->company_mobile ?? 'N/A' }}
                                            </span>
                                        </td>


                                        <td> {{ $row->employees->count() }}</td>

                                        <td>{{ $row->total_storage_mb }} MB</td>

                                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y') }}</td>






                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            class="text-center">No companies found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore"
                                        class="btn btn-outline-primary rounded-pill px-4 py-2">
                                    Load More
                                </button>
                            </div>
                        @endif


                    </div>

                    @php
                        $activeCount = $infos->where('status', 'Active')->count();
                        $formerCount = $infos->where('status', 'Inactive')->count();
                    @endphp

                    <div class="row mt-4">
                        <div class="col-12 d-flex gap-3 justify-content-center">
                            @if ($statusFilter == 'Active')
                                <div class="px-4 py-2 rounded-pill shadow-sm small"
                                     style="background-color: #e9f5ee; color: #1b5e20; font-weight: 600; border: 1px solid #d1e7dd;">
                                    Total Active Companies: {{ $activeCount }}
                                </div>
                            @else
                                <div class="px-4 py-2 rounded-pill shadow-sm small"
                                     style="background-color: #fce8e8; color: #c62828; font-weight: 600; border: 1px solid #f8d7da;">
                                    Total Inactive Companies: {{ $formerCount }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>






</div>


<script>
    function copyToClipboard(text) {
        if (!text) return;
        navigator.clipboard.writeText(text)
            .then(() => {
                alert("Copied: " + text);
            })
            .catch(err => {
                console.error('Failed to copy: ', err);
            });
    }
</script>
