document.addEventListener("DOMContentLoaded", function () {
    var pusherKey = document.getElementById("pusher_key").value;
    var pusherCluster = document.getElementById("pusher_cluster").value;
    var companyId = parseInt(
        document.getElementById("current_company_id").value
    );
    const currentUserId = parseInt(
        document.getElementById("current_user_id").value
    );
    var currentReceiverId = parseInt(
        document.getElementById("currentReceiverId").value
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

    const typingChannelAll = pusher.subscribe(
        `chat-${companyId}-between-all-users`
    );
    typingChannelAll.bind("UserTyping", function (data) {
        if (currentReceiverId !== "group") return;
        if (data.user_id === currentUserId) return;

        showTypingIndicator(data.user_name);
    });

    const typingChannelTwo = pusher.subscribe(
        `chat-${companyId}-${currentReceiverId}`
    );
    typingChannelTwo.bind("UserTyping", function (data) {
        if (currentReceiverId == "group") return;
        if (data.user_id === currentUserId) return;

        showTypingIndicator(data.user_name);
    });

    const attachmentBtn = document.getElementById("attachmentBtn");
    const attachmentPopup = document.getElementById("attachmentPopup");

    attachmentBtn.addEventListener("click", () => {
        // toggle popup visibility
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
