<!-- Conflict Modal -->
<div class="modal fade" id="conflictModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Shift Conflict</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>The following employees already have shifts on this date:</p>

                @php

                    $dateEmployees = [];
                    foreach ($conflictData as $shift) {
                        foreach ($shift->dates as $d) {
                            $key = \Carbon\Carbon::parse($d->date)->format('j M Y');
                            foreach ($d->employees as $emp) {
                                $dateEmployees[$key][$emp->id] = $emp->full_name;
                            }
                        }
                    }

                @endphp

                <ul class="list-unstyled mb-0">
                    @foreach ($dateEmployees as $date => $names)
                        <li>
                            • {{ $date }} – <span class="text-danger">{{ count($names) }} employees</span>
                            <br><small class="ms-3 text-muted">

                                {{ implode(', ', array_slice($names, 0, 3)) }}
                                @if (count($names) > 3)
                                    … <em>and {{ count($names) - 3 }} more</em>
                                @endif
                            </small>
                        </li>
                    @endforeach
                </ul>

                <p class="mt-2 mb-0 text-muted">Do you want to <strong>replace</strong> those assignments?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-danger" wire:click="confirmOverwrite" wire:loading.attr="disabled"
                    wire:target="confirmOverwrite">
                    <span wire:loading.remove wire:target="confirmOverwrite">Confirm & Replace</span>
                    <span wire:loading wire:target="confirmOverwrite">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Replacing...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- JS: open modal -->
<script>
    window.addEventListener('show-conflict-modal', () => {
        new bootstrap.Modal(document.getElementById('conflictModal')).show();
    });


    window.addEventListener('hide-conflict-modal', () => {
        bootstrap.Modal.getInstance(document.getElementById('conflictModal')).hide();
    });
</script>
