document.addEventListener("DOMContentLoaded", function () {
    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        height: 500,
        events: [
            {
                title: "John Vacation",
                start: "2025-12-08",
                end: "2025-12-10",
                color: "#28a745",
            },
            {
                title: "Jane Sick",
                start: "2025-12-12",
                color: "#ffc107",
            },
        ],
    });
    calendar.render();
});
