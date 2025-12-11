@php
    $user = app('authUser');
@endphp



<div class="position-relative">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl z-index-sticky" id="navbarBlur"
        data-scroll="false">
        <div class="container-fluid py-1 px-3 position-relative">

            <div class=" sidenav-toggler-inner d-xl-block d-none w-100">
                <a href="javascript:;" class="nav-link p-0 w-fitcontent sidenav-toggler">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line bg-dark"></i>
                        <i class="sidenav-toggler-line bg-dark"></i>
                        <i class="sidenav-toggler-line bg-dark"></i>
                    </div>
                </a>
            </div>

            <!-- RIGHT SIDE ICON + PROFILE -->
            <div class="d-flex align-items-center gap-3 position-relative w-100">

                <!-- LEFT SECTION (Mobile menu icon) -->
                <div class="d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-dark p-0 d-xl-none" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line bg-dark"></i>
                            <i class="sidenav-toggler-line bg-dark"></i>
                            <i class="sidenav-toggler-line bg-dark"></i>
                        </div>
                    </a>
                </div>

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
                    <span class="d-flex cursor-pointer" id="notificationBell">
                        <i class="fa-regular fa-bell fs-4"></i>
                    </span>

                    <!-- NOTIFICATION DROPDOWN -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <ul>
                            <li>No new notifications</li>
                            <li>Message from admin</li>
                            <li>New user registered</li>
                            <li>System alert</li>
                            <li>Update available</li>
                            <li>Server restarted</li>
                            <li>User updated profile</li>
                            <li>Extra Data...</li>
                        </ul>
                    </div>

                    <!-- PROFILE IMAGE -->
                    <img src="/assets/img/default-avatar.png" alt="Avatar" class="rounded-circle cursor-pointer"
                        width="40" height="40" id="profileImage">

                    <!-- PROFILE DROPDOWN -->
                    <div class="profile-dropdown" id="profileDropdown">
                        <ul>
                            <li><a href="#">My Profile</a></li>
                            <li><a href="#">Account Settings</a></li>
                            <li><a href="#">Dashboard</a></li>
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Support</a></li>
                            <li><a href="#">Privacy</a></li>
                            <li><a href="#">Logout</a></li>
                        </ul>
                    </div>

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


<script>
    document.addEventListener("DOMContentLoaded", function() {

        const profileImage = document.getElementById("profileImage");
        const profileDropdown = document.getElementById("profileDropdown");

        const notificationBell = document.getElementById("notificationBell");
        const notificationDropdown = document.getElementById("notificationDropdown");

        // Toggle profile dropdown
        profileImage.addEventListener("click", (e) => {
            e.stopPropagation();
            profileDropdown.style.display =
                profileDropdown.style.display === "block" ? "none" : "block";
            notificationDropdown.style.display = "none"; // hide notification dropdown if open
        });

        // Toggle notification dropdown
        notificationBell.addEventListener("click", (e) => {
            e.stopPropagation();
            notificationDropdown.style.display =
                notificationDropdown.style.display === "block" ? "none" : "block";
            profileDropdown.style.display = "none"; // hide profile dropdown if open
        });

        // Close both when clicking outside
        document.addEventListener("click", (event) => {
            if (!profileDropdown.contains(event.target) &&
                !profileImage.contains(event.target)) {
                profileDropdown.style.display = "none";
            }

            if (!notificationDropdown.contains(event.target) &&
                !notificationBell.contains(event.target)) {
                notificationDropdown.style.display = "none";
            }
        });


    });
</script>


<script>
    document.addEventListener('livewire:init', () => {
        setInterval(() => {
            Livewire.dispatch('tick');
        }, 1000);
    });
</script>
