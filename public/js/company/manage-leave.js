document.addEventListener("DOMContentLoaded", function () {
    let calendarEl = document.getElementById("calendar");
    let leaveDates = {}; // store all leave IDs per date

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        height: 550,
        displayEventTime: false,
        eventDidMount: function (info) {
            info.el.style.fontSize = "1.5rem";
            info.el.style.textAlign = "center";
        },
        // --- Existing dateClick handler (Fires when the date area *without* an event is clicked)
        dateClick: function (info) {
            // Re-use the common logic
            handleLeaveClick(info.dateStr);
        },
        // --- NEW eventClick handler (Fires when the event/emoji is clicked)
        eventClick: function (info) {
            // FullCalendar event objects have a 'start' property which holds the date.
            // Convert it to the required date string format (YYYY-MM-DD).
            let dateStr = info.event.startStr;
            // Re-use the common logic
            handleLeaveClick(dateStr);
        },
        // ---
    });

    // Helper function to handle the common logic for both dateClick and eventClick
    const handleLeaveClick = (dateStr) => {
        if (leaveDates[dateStr] && leaveDates[dateStr].length > 0) {
            Livewire.dispatch("showLeaveRequestInfo", leaveDates[dateStr]);
        }
    };

    calendar.render();

    Livewire.on("employeeLeaveLoaded", (data) => {
        calendar.removeAllEvents();
        leaveDates = {}; // reset

        data.leaves.forEach((leave) => {
            let start = new Date(leave.start);
            let end = new Date(leave.end);
            let loopDate = new Date(start);

            while (loopDate.getTime() <= end.getTime()) {
                let dateStr = loopDate.toISOString().split("T")[0];

                if (!leaveDates[dateStr]) leaveDates[dateStr] = [];
                leaveDates[dateStr].push(leave.id); // store all leave IDs for this date

                calendar.addEvent({
                    start: dateStr,
                    allDay: true,
                    title: leave.emoji,
                    extendedProps: {
                        leaveId: leave.id,
                    },
                });

                loopDate.setDate(loopDate.getDate() + 1);
            }
        });
    });
});
