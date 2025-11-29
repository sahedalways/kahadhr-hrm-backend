document.addEventListener("DOMContentLoaded", function () {
    let allBtn = document.querySelector("#chat-filters button:nth-child(1)");
    let unreadBtn = document.querySelector("#chat-filters button:nth-child(2)");
    let teamsBtn = document.querySelector("#chat-filters button:nth-child(3)");

    let allList = document.getElementById("all-chats-list");
    let unreadList = document.getElementById("unread-chats-list");
    let teamsList = document.getElementById("teams-chats-list");

    function showTab(tab) {
        allList.classList.add("d-none");
        unreadList.classList.add("d-none");
        teamsList.classList.add("d-none");

        tab.classList.remove("d-none");
    }

    allBtn.addEventListener("click", function () {
        showTab(allList);
    });

    unreadBtn.addEventListener("click", function () {
        showTab(unreadList);
    });

    teamsBtn.addEventListener("click", function () {
        showTab(teamsList);
    });
});

function activate(btn) {
    document.querySelectorAll("#chat-filters button").forEach((b) => {
        b.style.backgroundColor = "#f8f9fa";
        b.style.color = "#6c757d";
        b.style.border = "1px solid #ddd";
    });

    btn.style.backgroundColor = "#0d6efd";
    btn.style.color = "#fff";
    btn.style.border = "1px solid #0d6efd";
}

allBtn.addEventListener("click", function () {
    activate(allBtn);
    showTab(allList);
});

unreadBtn.addEventListener("click", function () {
    activate(unreadBtn);
    showTab(unreadList);
});

teamsBtn.addEventListener("click", function () {
    activate(teamsBtn);
    showTab(teamsList);
});
