@php
    $user = app('authUser');

    $userType = $user->user_type;

@endphp



<div class="position-relative">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl z-index-sticky" id="navbarBlur"
        data-scroll="false">
        <div class="container-fluid py-1 px-3 position-relative">



            <input type="hidden" id="pusher_key" value="{{ config('broadcasting.connections.pusher.key') }}">
            <input type="hidden" id="pusher_cluster"
                value="{{ config('broadcasting.connections.pusher.options.cluster') }}">
            <input type="hidden" id="current_company_id" value="{{ currentCompanyId() }}">
            <input type="hidden" id="current_user_id" value="{{ auth()->id() }}">

            <!-- RIGHT SIDE ICON + PROFILE -->
            <div class="d-flex align-items-center gap-3 position-relative w-100">

                <!-- LEFT SECTION (Mobile menu icon) -->
                {{--<div class="d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-dark p-0 d-xl-none" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-dark"></i>
                            <i class="sidenav-toggler-line bg-dark"></i>
                            <i class="sidenav-toggler-line bg-dark"></i>
                        </div>
                    </a>
                </div>--}}

                @if (auth()->user()->user_type == 'employee' || auth()->user()->user_type == 'manager')
                    <div class="timer-box">
                        <span class="timer-time">{{ $headerTimer }}</span>

                        @if ($isRunning)
                            <div class="timer-running-dot"></div>
                        @endif
                    </div>
                @endif



                <!-- RIGHT SECTION (Everything else) -->
                <div class="d-flex align-items-center gap-3 ms-auto">

                    <!-- NOTIFICATION ICON -->
                    <span class="d-flex position-relative cursor-pointer" id="notificationBell"
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
                    <div class="notification-dropdown" id="notificationDropdown" wire:ignore style="width: 550px;">
                        @livewire('backend.components.notifications')
                    </div>


                    @if ($userType !== 'superAdmin')
                        <div class="dropdown">

                            <img src="{{ $userType === 'company'
                                ? getCompanyLogoUrl() ?? '/assets/img/default-avatar.png'
                                : $user->avatar_url ?? '/assets/img/default-avatar.png' }}"
                                alt="Avatar" class="rounded-circle cursor-pointer dropdown-toggle" width="40"
                                height="40" id="profileDropdownToggle" data-bs-toggle="dropdown"
                                aria-expanded="false">



                            <ul class="dropdown-menu dropdown-menu-end profile-dropdown" id="profileDropdown"
                                wire:ignore style="min-width: 280px; max-height: 400px; overflow-y: auto;">

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
                                        data-bs-toggle="collapse" href="#settingsCollapse" role="button"
                                        aria-expanded="{{ Request::is('*dashboard/settings*') ? 'true' : 'false' }}"
                                        aria-controls="settingsCollapse">
                                        <span class="d-flex align-items-center"><i class="fas fa-cog me-2"></i>
                                            Settings</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </a>


                                    <div class="collapse {{ Request::is('*dashboard/settings*') ? 'show' : '' }} nested-settings-menu"
                                        id="settingsCollapse" **wire:ignore.self**>


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
                                                        <i class="fas fa-university me-2"></i> Payment Settings
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


                                <li>
                                    <hr class="dropdown-divider">
                                </li>


                                {{-- Main Navigation Links --}}
                                @php
                                    $dashboardRoute = '';
                                    if ($userType === 'company') {
                                        $dashboardRoute = route('company.dashboard.index', [
                                            'company' => app('authUser')->company->sub_domain,
                                        ]);
                                    } elseif (in_array($userType, ['employee', 'manager'])) {
                                        $dashboardRoute = route('employee.dashboard.index', [
                                            'company' => app('authUser')->employee->company->sub_domain,
                                        ]);
                                    }
                                @endphp

                                <li>
                                    <a class="dropdown-item" href="{{ $dashboardRoute }}">
                                        <i class="fas fa-columns me-2"></i> Dashboard
                                    </a>
                                </li>
                                <li><a class="dropdown-item" href="#"><i
                                            class="fas fa-question-circle me-2"></i>
                                        Help
                                        Center</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-headset me-2"></i>
                                        Support</a>
                                </li>

                                {{-- Logout --}}
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>

                                    <a class="dropdown-item" href="#" wire:click.prevent="logout"><i
                                            class="fas fa-sign-out-alt me-2"></i>
                                        Logout</a>
                                </li>
                            </ul>
                        </div>
                    @endif


                    <!-- CLOCK ICON -->

                    @if (auth()->user()->user_type == 'employee' || auth()->user()->user_type == 'manager')
                        <span class="d-flex cursor-pointer" data-bs-toggle="modal" data-bs-target="#AppClockModal">
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
