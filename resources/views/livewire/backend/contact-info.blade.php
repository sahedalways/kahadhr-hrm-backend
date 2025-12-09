<div>
    <div class="row align-items-center justify-content-between mb-4">
        <div class="col">
            <h5 class="fw-500">Contact Inquiries</h5>
        </div>


        <div class="col-auto d-flex gap-2">


            <button wire:click="exportContacts('pdf')" class="btn btn-sm btn-white text-primary">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </button>

            <button wire:click="exportContacts('excel')" class="btn btn-sm btn-white text-success">
                <i class="fa fa-file-excel me-1"></i> Excel
            </button>

            <button wire:click="exportContacts('csv')" class="btn btn-sm btn-white text-info">
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
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i
                                        class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Search by name, phone no or email" wire:model="search"
                                    wire:keyup="set('search', $event.target.value)" />
                            </div>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="col-md-4 d-flex gap-2">
                            <select class="form-select form-select-lg" wire:change="handleSort($event.target.value)">
                                <option value="desc">Newest First</option>
                                <option value="asc">Oldest First</option>
                            </select>

                        </div>

                    </div>

                    <!-- Live Search Result Indicator -->
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <p class="text-muted small mb-0">
                            Showing results for: <strong>{{ $search ?: 'All Companies' }}</strong>
                        </p>
                        <div wire:loading wire:target="search">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"
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
                        <table class="table mb-0 table-bordered text-center align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Topic</th>
                                    <th>Description</th>
                                    <th>Sent At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($contacts as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                                        <td>{{ $row->phone }}</td>
                                        <td>{{ $row->email ?? 'N/A' }}</td>
                                        <td>{{ $row->topic ?? 'N/A' }}</td>
                                        <td>
                                            <div x-data="{ expanded: false }">
                                                <span
                                                    x-text="expanded ? '{{ addslashes($row->description ?? '-') }}' : '{{ addslashes(strlen($row->description ?? '-') > 55 ? substr($row->description, 0, 55) . '...' : $row->description ?? '-') }}'"></span>
                                                <template x-if="{{ strlen($row->description ?? '') }} > 55">
                                                    <a href="javascript:;" @click="expanded = !expanded"
                                                        class="btn btn-link p-0 mt-1"
                                                        style="font-size: 0.75rem; text-decoration: underline;">
                                                        <span x-text="expanded ? 'See less' : 'See more'"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </td>
                                        <td>{{ $row->created_at?->format('d M, Y h:i A') }}</td>
                                        <td>
                                            <a href="#" class="badge badge-xs badge-danger fw-600 text-xs"
                                                wire:click.prevent="$dispatch('confirmDelete', {{ $row->id }})">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No contacts found!</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        @if ($hasMore)
                            <div class="text-center mt-4">
                                <button wire:click="loadMore"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-4 py-2">
                                    Load More
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    Livewire.on('confirmDelete', id => {
        if (confirm("Are you sure you want to delete this contact? This action cannot be undone.")) {
            Livewire.dispatch('deleteItem', {
                id: id
            });
        }
    });
</script>
