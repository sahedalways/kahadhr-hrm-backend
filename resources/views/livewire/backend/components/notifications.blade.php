<div>
    <ul class="list-group list-group-flush"
        style="max-height: 400px; overflow-y: auto; background-color: #ffffff;">

        @forelse($notifications as $notification)
            @php
                $isRead = $notification['is_read'] ?? false;

                $itemClasses = 'list-group-item list-group-item-action py-3 px-4';
                $itemClasses .= $isRead ? ' text-muted' : ' fw-semibold';

                $itemStyle = $isRead
                    ? 'background-color: #ffffff;'
                    : 'background-color: #f0e6ff; border-left: 5px solid #6f42c1 !important;';

                $commonStyle = 'transition: all 0.2s ease-in-out; cursor: pointer;';
            @endphp


            <a
               href="{{ $notification['type'] === 'submitted_leave_request' ? route('company.dashboard.leaves.index', ['company' => app('authUser')->company->sub_domain]) : '#' }}">
                <li class="{{ $itemClasses }}"
                    style="{{ $itemStyle }} {{ $commonStyle }}"
                    data-bs-toggle="{{ !$isRead ? 'tooltip' : '' }}"
                    onmouseover="this.style.backgroundColor='{{ $isRead ? '#f5f5ff' : '#e6d3ff' }}'"
                    onmouseout="this.style.backgroundColor='{{ $isRead ? '#ffffff' : '#f0e6ff' }}'">

                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <p class="mb-0 small flex-grow-1"
                           style="line-height: 1.4; white-space: normal;">
                            {{ \Illuminate\Support\Str::limit($notification['data']['message'] ?? '', 60) }}
                        </p>

                        <small class="text-secondary flex-shrink-0"
                               style="font-size: 0.7rem;">
                            {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                        </small>
                    </div>
                </li>
            </a>


        @empty
            <li class="list-group-item text-center text-muted fst-italic py-5">
                <i class="fa-regular fa-face-smile me-2"
                   style="font-size: 1.5rem;"></i>
                <p class="mb-0 mt-2">All caught up! No new notifications.</p>
            </li>
        @endforelse
    </ul>

    <div class="card-footer text-center border-top p-3">
        @if (count($notifications) >= $perPage)
            <button wire:click="loadMore"
                    class="btn btn-sm btn-link text-primary"
                    @if ($loadingMore) disabled @endif>

                @if ($loadingMore)
                    <span class="spinner-border spinner-border-sm"></span>
                    Loading...
                @else
                    <i class="fa-solid fa-arrow-right me-1"></i>
                    View More Notifications
                @endif
            </button>
        @endif
    </div>
</div>



<script src="https://js.pusher.com/7.2/pusher.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        var pusherKey = document.getElementById("pusher_key").value;
        var pusherCluster = document.getElementById("pusher_cluster").value;


        var companyId = parseInt(
            document.getElementById("current_company_id").value
        );

        // Initialize Pusher
        var pusher = new Pusher(pusherKey, {
            cluster: pusherCluster,
            forceTLS: true,
        });

        var allUserChatChannel = pusher.subscribe("company." + companyId);

        allUserChatChannel.bind("allNotifications", function(payload) {
            Livewire.dispatch("newNotificationForDetails", {
                notification: payload.notification
            });
        });
    });
</script>


<script>
    window.addEventListener('mark-notifications-read-after-delay', function() {
        setTimeout(() => {
            Livewire.dispatch('markAllUiRead');
        }, 3000);
    });
</script>
