@php
    $user = app('authUser');

    $userType = $user->user_type;

@endphp



<div class="position-relative">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl z-index-sticky
        @if (in_array($userType, ['admin', 'superAdmin'])) admin-fixed-header @endif"
         id="navbarBlur"
         data-scroll="false">
        <div class="container-fluid py-1 px-3 position-relative">



            <input type="hidden"
                   id="pusher_key"
                   value="{{ config('broadcasting.connections.pusher.key') }}">
            <input type="hidden"
                   id="pusher_cluster"
                   value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
            <input type="hidden"
                   id="current_company_id"
                   value="{{ currentCompanyId() }}">
            <input type="hidden"
                   id="current_user_id"
                   value="{{ auth()->id() }}">

            <!-- RIGHT SIDE ICON + PROFILE -->
            <div class="d-flex align-items-center gap-3 position-relative w-100">



                @if (auth()->user()->user_type == 'employee' || auth()->user()->user_type == 'manager')
                    <div x-data
                         x-on:header-timer-update.window="$wire.updateTimer($event.detail.time, $event.detail.running)"
                         class="d-flex align-items-center gap-2">

                        <div class="
            d-inline-flex align-items-center gap-2
            px-3 py-2
            rounded-3
            text-white
            fw-500
            shadow-sm
            border border-1 border-white-10
            bg-gradient-to-r from-primary to-info
        "
                             style="--bs-bg-opacity:.15;
               background: linear-gradient(135deg, rgba(var(--bs-primary-rgb),.85) 0%, rgba(var(--bs-info-rgb),.75) 100%);
               backdrop-filter: blur(4px);
               -webkit-backdrop-filter: blur(4px);">

                            <i class="bi bi-stopwatch fs-5"></i>


                            <span class="timer-time font-monospace fs-5 lh-1 text-white">{{ $headerTimer }}</span>


                            @if ($isRunning)
                                <div class="timer-running-dot rounded-circle bg-white"
                                     style="width:8px;height:8px;animation:pulse 1.2s infinite"></div>
                            @endif
                        </div>
                    </div>
                @endif

                @php
                    $company = auth()->check() ? auth()->user()->company : null;
                    $showBanner = $company && in_array($company->subscription_status, ['trial', 'suspended']);
                    $bannerText =
                        $company && $company->subscription_status === 'trial'
                            ? getTrialInfo($company->subscription_status, $company->subscription_end)
                            : ($company && $company->subscription_status === 'suspended'
                                ? 'Your account is suspended. Contact to support'
                                : '');
                @endphp

                @if ($showBanner)
                    <div class="status-banner text-center text-white py-1 mx-auto d-md-block d-none">
                        {!! $bannerText !!}
                    </div>
                @endif

                <!-- RIGHT SECTION (Everything else) -->
                <div class="d-flex align-items-center gap-3 ms-auto">

                    <!-- NOTIFICATION ICON -->
                    @if ($userType !== 'superAdmin')
                        <span class="d-flex position-relative cursor-pointer"
                              id="notificationBell"
                              wire:click="markAllAsRead">
                            <i class="fa-regular fa-bell fs-4 text-white"></i>

                            @if ($unreadCount > 0)
                                <span
                                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger p-1 fs-6">
                                    {{ $unreadCount }}
                                </span>
                            @endif
                        </span>


                        <!-- NOTIFICATION DROPDOWN -->

                        <div class="notification-dropdown px-2"
                             id="notificationDropdown"
                             wire:ignore>
                            @livewire('backend.components.notifications')
                        </div>
                    @endif

                    @if ($userType !== 'superAdmin')
                        <div class="dropdown">


                            <div id="profileDropdownToggle"
                                 data-bs-toggle="dropdown"
                                 aria-expanded="false"
                                 class="cursor-pointer d-flex flex-nowrap align-items-center gap-2">
                                <img src="{{ $userType === 'company' ? getCompanyLogoUrl() ?? '/assets/img/default-avatar.png' : $user->employee->avatar_url }}"
                                     alt="Avatar"
                                     class="rounded-circle cursor-pointer dropdown-toggle"
                                     width="40"
                                     height="40">
                                <i class="fa-solid fa-caret-down"></i>
                            </div>


                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown"
                                id="profileDropdown"
                                wire:ignore
                                style="min-width: 280px; max-height: 400px; overflow-y: auto;">

                                {{-- My Profile Link --}}
                                @if (in_array($userType, ['employee', 'manager']))
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('employee.dashboard.profile.index', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                            <i class="fas fa-user-circle me-2"></i> My Profile
                                        </a>
                                    </li>
                                @endif


                                <li class="dropdown-submenu">

                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                       data-bs-toggle="collapse"
                                       href="#settingsCollapse"
                                       role="button"
                                       aria-expanded="{{ Request::is('*dashboard/settings*') ? 'true' : 'false' }}"
                                       aria-controls="settingsCollapse">
                                        <span class="d-flex align-items-center"><i class="fas fa-cog me-2"></i>
                                            Settings</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>


                                    <div class="collapse {{ Request::is('*dashboard/settings*') ? 'show' : '' }} nested-settings-menu"
                                         id="settingsCollapse"
                                         **wire:ignore.self**>


                                        <ul class="list-unstyled ms-4 p-0">
                                            @if ($userType === 'company')
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.profile', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-user me-2"></i> Profile Settings
                                                    </a>
                                                </li>
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.bank-info', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-university me-2"></i> Subscriptions Settings
                                                    </a>
                                                </li>
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.verification-center', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-shield-alt me-2"></i> Verification Center
                                                    </a>
                                                </li>
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.mail', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-envelope me-2"></i> Mail Settings
                                                    </a>
                                                </li>
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.calendar-year', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-calendar-alt me-2"></i> Calendar Year Settings
                                                    </a>
                                                </li>
                                                <li class="p-0">
                                                    <a class="dropdown-item"
                                                       href="{{ route('company.dashboard.settings.password', ['company' => app('authUser')->company->sub_domain]) }}">
                                                        <i class="fas fa-lock me-2"></i> Password Settings
                                                    </a>
                                                </li>
                                            @elseif(in_array($userType, ['employee', 'manager']))
                                                <li class="p-0 mt-1">
                                                    <a class="dropdown-item"
                                                       href="{{ route('employee.dashboard.settings.verification-center', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                                        <i class="fas fa-shield-alt me-2"></i> Verification Center
                                                    </a>
                                                </li>
                                                <li class="p-0 mt-1">
                                                    <a class="dropdown-item"
                                                       href="{{ route('employee.dashboard.settings.password', ['company' => app('authUser')->employee->company->sub_domain]) }}">
                                                        <i class="fas fa-lock me-2"></i> Password Settings
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>


                                <li class="dropdown-divider-item">
                                    <hr class="dropdown-divider">
                                </li>


                                {{-- <li><a class="dropdown-item"
                                       href="#"><i class="fas fa-question-circle me-2"></i>
                                        Help
                                        Center</a></li> --}}
                                <li><a class="dropdown-item"
                                       href="#"><i class="fas fa-headset me-2"></i>
                                        Support</a>
                                </li>

                                {{-- Logout --}}
                                <li class="dropdown-divider-item">
                                    <hr class="dropdown-divider">
                                </li>
                                <li>

                                    <a class="dropdown-item"
                                       href="#"
                                       wire:click.prevent="logout"><i class="fas fa-sign-out-alt me-2"></i>
                                        Logout</a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    @php
                        $todaysShift = todaysShiftForUser();
                    @endphp


                    <!-- CLOCK ICON -->
                    @if (auth()->user()->user_type == 'employee')
                        <span class="d-flex cursor-pointer text-white"
                              onclick="checkTodaysShift()">
                            <i class="fa-regular fa-clock fs-4"></i>
                        </span>
                    @endif


                </div>

            </div>

        </div>
    </nav>


</div>


<script src="https://js.pusher.com/7.2/pusher.min.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        const notificationBell = document.getElementById("notificationBell");
        const notificationDropdown = document.getElementById("notificationDropdown");



        // Toggle notification dropdown
        notificationBell.addEventListener("click", (e) => {
            e.stopPropagation();
            notificationDropdown.style.display =
                notificationDropdown.style.display === "block" ? "none" : "block";
            profileDropdown.style.display = "none";
        });

    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const profileDropdown = document.getElementById("profileDropdown");
        const profileToggle = document.getElementById("profileDropdownToggle");


        profileToggle.addEventListener("click", function(e) {
            e.stopPropagation();
            profileDropdown.style.display = profileDropdown.style.display === "block" ? "none" :
                "block";
        });


        profileDropdown.addEventListener("click", function(e) {
            e.stopPropagation();
        });

        document.addEventListener("click", function() {
            profileDropdown.style.display = "none";
        });


        const collapses = profileDropdown.querySelectorAll('[data-bs-toggle="collapse"]');
        collapses.forEach(trigger => {
            const icon = trigger.querySelector('i.fas.fa-chevron-right');
            const targetId = trigger.getAttribute('href');
            const collapseEl = document.querySelector(targetId);

            if (!collapseEl || !icon) return;

            collapseEl.addEventListener('shown.bs.collapse', () => {
                icon.style.transform = 'rotate(90deg)';
                icon.style.transition = 'transform 0.3s';
            });

            collapseEl.addEventListener('hidden.bs.collapse', () => {
                icon.style.transform = 'rotate(0deg)';
            });
        });


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
            Livewire.dispatch("allNotifications", {
                notification: payload.notification
            });
        });
    });
</script>


<script>
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notificationDropdown');


        if (!dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

<script>
    document.addEventListener('livewire:init', () => {
        setInterval(() => {
            Livewire.dispatch('tick');
        }, 1000);
    });
</script>


<script>
    function checkTodaysShift() {
        const hasShift = @json((bool) $todaysShift);

        if (!hasShift) {
            toastr.error("You don't have any shift scheduled for today.");
            return;
        }

        const modal = new bootstrap.Modal(
            document.getElementById('AppClockModal')
        );
        modal.show();
    }

    document.addEventListener("livewire:init", () => {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 5000,
            positionClass: "toast-center",
        };
    });
</script>
