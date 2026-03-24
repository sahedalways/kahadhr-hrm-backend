<div class="accordion-item mt-3"
     wire:ignore>

    <h6 class="accordion-header">
        <button class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#collapseLoginSessions">
            🔑 Login Sessions
            <span class="arrow">
                <i class="fas fa-chevron-right"></i>
            </span>
        </button>
    </h6>

    <div id="collapseLoginSessions"
         class="accordion-collapse collapse"
         data-bs-parent="#securityAccordion">

        <div class="accordion-body">

            <div class="mb-3 d-flex justify-content-between align-items-center">
                <p class="text-muted mb-0">
                    View your active login sessions and log out of other devices if needed.
                </p>

                @if (!$sessions->isEmpty())
                    <button class="btn btn-sm btn-outline-danger"
                            wire:click="logoutAllSessions"
                            wire:loading.attr="disabled"
                            wire:target="logoutAllSessions">

                        <span wire:loading
                              wire:target="logoutAllSessions">
                            <i class="fas fa-spinner fa-spin me-2"></i> Signing Out...
                        </span>

                        <span wire:loading.remove
                              wire:target="logoutAllSessions">
                            Sign Out All Sessions
                        </span>
                    </button>
                @endif
            </div>

            @if ($sessions->isEmpty())
                <div class="text-center text-muted mt-3">
                    No active sessions found.
                </div>
            @else
                <ul class="list-group mt-2">
                    @foreach ($sessions as $session)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $session->device ?? 'Unknown Device' }}</strong> –
                                <span class="text-muted">{{ $session->location ?? 'Unknown Location' }}</span>
                                <br>
                                <small class="text-muted">
                                    IP: {{ $session->ip_address ?? 'Unknown IP' }} • Login:
                                    {{ $session->login_time ? \Carbon\Carbon::parse($session->login_time)->format('d M, Y h:i A') : 'Unknown' }}
                                </small>
                            </div>

                            @if ($session->id !== session()->getId())
                                <button class="btn btn-sm btn-outline-danger"
                                        wire:click="logoutSession('{{ $session->id }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="logoutSession('{{ $session->id }}')">

                                    <span wire:loading
                                          wire:target="logoutSession('{{ $session->id }}')">
                                        <i class="fas fa-spinner fa-spin me-2"></i> Logging Out...
                                    </span>

                                    <span wire:loading.remove
                                          wire:target="logoutSession('{{ $session->id }}')">
                                        Log Out
                                    </span>
                                </button>
                            @else
                                <span class="badge bg-success">Current</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>
</div>
