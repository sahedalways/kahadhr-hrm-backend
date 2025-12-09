<div wire:ignore.self class="modal fade" id="AppClockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content app-clock-card-refined">

            <div class="modal-body p-4">
                <div class="text-center app-clock-container">

                    <div class="app-clock-time-display mb-4">
                        <h1 class="display-3 fw-bolder mb-1 app-clock-time-main">Counting time ...</h1>
                        <p class="fs-6 app-clock-date-text">Getting date ...</p>
                    </div>

                    <div class="d-flex justify-content-around my-4 pt-2 pb-3 gap-3">
                        <button type="button" wire:click="clockIn" class="btn btn-app-clock-in btn-lg w-50">
                            <i class="bi bi-box-arrow-in-right me-2"></i> CLOCK IN
                        </button>

                        <button type="button" wire:click="clockOut" class="btn btn-app-clock-out btn-lg w-50">
                            <i class="bi bi-box-arrow-left me-2"></i> CLOCK OUT
                        </button>
                    </div>

                    <div class="app-clock-location-display text-start mt-4 p-3">
                        <small class="d-block mb-1">
                            <i class="bi bi-geo-alt-fill me-1"></i> CURRENT LOCATION
                        </small>

                        <div class="d-flex justify-content-between align-items-center">
                            <p id="userLocation" class="mb-0 fs-6 fw-bold text-white">Detecting location...</p>
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


        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();

        const ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;

        const timeString =
            String(hours).padStart(2, '0') + ":" +
            String(minutes).padStart(2, '0') + ":" +
            String(seconds).padStart(2, '0') + " " + ampm;

        document.querySelector(".app-clock-time-main").textContent = timeString;

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

    setInterval(updateClock, 1000);
    updateClock();
</script>


<script>
    function getDeepLocation() {
        if (!navigator.geolocation) {
            document.getElementById("userLocation").textContent = "Location not supported";
            return;
        }

        navigator.geolocation.getCurrentPosition(async (position) => {
            let lat = position.coords.latitude;
            let lon = position.coords.longitude;


            let url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json`;

            try {
                let response = await fetch(url);
                let data = await response.json();

                let loc = data.address;

                let fullLocation = `
                ${loc.house_number ?? ""} ${loc.road ?? ""}
                ${loc.suburb ?? ""}
                ${loc.city ?? loc.town ?? loc.village ?? ""}
                ${loc.state ?? ""}
                ${loc.postcode ?? ""}
                ${loc.country ?? ""}
            `;

                document.getElementById("userLocation").textContent = fullLocation.trim();

            } catch (error) {
                document.getElementById("userLocation").textContent = "Unable to get location";
            }

        }, () => {
            document.getElementById("userLocation").textContent = "Location permission denied";
        });
    }

    getDeepLocation();
</script>
