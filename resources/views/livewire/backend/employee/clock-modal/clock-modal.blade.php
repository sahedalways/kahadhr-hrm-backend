<div wire:ignore.self class="modal fade" id="AppClockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content app-clock-card-refined">

            <div class="modal-body p-4">
                <div class="text-center app-clock-container">

                    <div class="app-clock-time-display mb-4">
                        <div class="app-clock-time-display mb-4">


                            <p id="elapsedLabel" class="fs-5 fw-bold text-white mb-1">
                                {{ $statusLabel }}
                            </p>


                            <!-- ELAPSED TIME -->
                            <h1 id="elapsedTime" class="display-3 fw-bolder mb-1" wire:ignore>
                                {{ $elapsedTime ?? '00:00:00' }}
                            </h1>


                            <p class="fs-6 app-clock-date-text" wire:ignore>Getting date ...</p>

                        </div>



                    </div>

                    <div class="d-flex flex-column gap-2 my-4 pt-2 pb-3">

                        @if ($showClockInReason)
                            <div class="mb-2">
                                <input type="text" class="form-control" placeholder="Enter reason for late"
                                    wire:model.defer="clockInReason">
                                @error('clockInReason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        @if ($showClockInButton)
                            <button type="button" wire:click="clockIn" wire:loading.attr="disabled"
                                wire:target="clockIn" class="btn btn-app-clock-in btn-lg">

                                <span wire:loading.remove wire:target="clockIn">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> CLOCK IN
                                </span>

                                <span wire:loading wire:target="clockIn">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Processing...
                                </span>
                            </button>
                        @endif


                        @if ($showClockOutReason)
                            <div class="mb-2">
                                <input type="text" class="form-control" placeholder="Enter reason"
                                    wire:model.defer="clockOutReason">
                                @error('clockOutReason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif


                        @if ($showClockOutButton)
                            <button type="button" onclick="confirmClockOut()" wire:loading.attr="disabled"
                                wire:target="clockOut" class="btn btn-app-clock-out btn-lg">

                                <!-- Normal Text -->
                                <span wire:loading.remove wire:target="clockOut">
                                    <i class="bi bi-box-arrow-left me-2"></i> CLOCK OUT
                                </span>

                                <!-- Loading State -->
                                <span wire:loading wire:target="clockOut">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Processing...
                                </span>
                            </button>
                        @endif

                    </div>
                    <input type="hidden" id="initialElapsedSeconds"
                        value="{{ $elapsedTime
                            ? explode(':', $elapsedTime)[0] * 3600 + explode(':', $elapsedTime)[1] * 60 + explode(':', $elapsedTime)[2]
                            : 0 }}">



                    <div class="app-clock-location-display text-start mt-4 p-3">
                        <small class="d-block mb-1">
                            <i class="bi bi-geo-alt-fill me-1"></i> CURRENT LOCATION
                        </small>

                        <div class="d-flex justify-content-between align-items-center">
                            <p id="userLocation" class="mb-0 fs-6 fw-bold text-white" wire:ignore>Detecting location...
                            </p>
                            <span class="app-clock-location-status"></span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function updateClock() {
        const now = new Date();




        // TODAY DATE
        const dateOptions = {
            weekday: 'long',
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        };
        const todayDate = now.toLocaleDateString("en-US", dateOptions);

        document.querySelector(".app-clock-date-text").textContent = todayDate;
    }

    updateClock();
</script>



<script>
    function getDeepLocation() {
        if (!navigator.geolocation) {
            document.getElementById("userLocation").textContent = "Location not supported";
            return;
        }

        navigator.geolocation.getCurrentPosition(async (position) => {
            updateLocation(position.coords.latitude, position.coords.longitude);
        });


        setInterval(() => {
            navigator.geolocation.getCurrentPosition(async (position) => {
                updateLocation(position.coords.latitude, position.coords.longitude);
            });
        }, 30000);
    }

    async function updateLocation(lat, lon) {
        let url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`;

        try {
            let response = await fetch(url);
            let data = await response.json();
            let loc = data.address;

            let fullLocation = `
            ${loc.house_number ?? ""} ${loc.road ?? ""}
            ${loc.suburb ?? ""} ${loc.city ?? loc.town ?? loc.village ?? ""}
            ${loc.state ?? ""} ${loc.postcode ?? ""} ${loc.country ?? ""}
        `;
            let fullLocationSingleLine = fullLocation.replace(/\s+/g, ' ').trim();

            document.getElementById("userLocation").textContent = fullLocationSingleLine;


            Livewire.dispatch('setLocation', fullLocationSingleLine);
            let timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
            Livewire.dispatch('setUserTimezone', timezone);

        } catch (error) {
            document.getElementById("userLocation").textContent = "Unable to get location";
        }
    }

    getDeepLocation();



    const modalEl = document.getElementById('AppClockModal');
    modalEl.addEventListener('shown.bs.modal', () => {
        if (!navigator.geolocation) {
            document.getElementById("userLocation").textContent = "Location not supported";
            return;
        }

        navigator.geolocation.getCurrentPosition((position) => {
            updateLocation(position.coords.latitude, position.coords.longitude);
        });

        Livewire.dispatch('resetReasons');
    });
</script>


@if ($showClockOutButton)
    <script>
        const elapsedTimeDisplay = document.getElementById('elapsedTime');
        let elapsedSeconds = parseInt(document.getElementById('initialElapsedSeconds').value) || 0;

        function startCountdown() {
            // Convert total seconds back to HH:MM:SS
            let hours = Math.floor(elapsedSeconds / 3600);
            let minutes = Math.floor((elapsedSeconds % 3600) / 60);
            let seconds = elapsedSeconds % 60;

            elapsedTimeDisplay.textContent =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            // Increment seconds for next tick
            elapsedSeconds++;
        }

        // Update timer every 1 second
        setInterval(startCountdown, 1000);
    </script>
@endif


<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('reloadPage', () => {
            location.reload();
        });
    });
</script>


<script>
    function confirmClockOut() {
        if (confirm("Are you sure you want to Clock Out?")) {
            Livewire.dispatch('clockOut');
        }
    }
</script>
