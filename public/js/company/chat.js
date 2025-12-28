document.addEventListener("DOMContentLoaded", function () {
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

    allUserChatChannel.bind("allUsersMessage", function (data) {
        Livewire.dispatch("incomingMessage", { id: data.message.id });
    });

    const attachmentBtn = document.getElementById("attachmentBtn");
    const attachmentPopup = document.getElementById("attachmentPopup");

    const mentionBtn = document.getElementById("mentionBtn");
    const mentionPopup = document.getElementById("mentionPopup");

    attachmentBtn.addEventListener("click", () => {
        attachmentPopup.style.display =
            attachmentPopup.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", (e) => {
        if (
            !attachmentBtn.contains(e.target) &&
            !attachmentPopup.contains(e.target)
        ) {
            attachmentPopup.style.display = "none";
        }
    });

    mentionBtn.addEventListener("click", () => {
        mentionPopup.style.display =
            mentionPopup.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", (e) => {
        if (
            !mentionBtn.contains(e.target) &&
            !mentionPopup.contains(e.target)
        ) {
            mentionPopup.style.display = "none";
        }
    });

    function showTypingIndicator(userName) {
        const indicator = document.getElementById("typingIndicator");
        const userSpan = document.getElementById("typingUser");

        userSpan.textContent = userName;
        indicator.style.display = "block";

        clearTimeout(window.typingTimeout);
        window.typingTimeout = setTimeout(() => {
            indicator.style.display = "none";
        }, 2000);
    }
});
