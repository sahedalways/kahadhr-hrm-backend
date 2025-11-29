document.addEventListener("DOMContentLoaded", function () {
    var pusherKey = document.getElementById("pusher_key").value;
    var pusherCluster = document.getElementById("pusher_cluster").value;
    var companyId = document.getElementById("current_company_id").value;
    const indicator = document.getElementById("typingIndicator");
    const userSpan = document.getElementById("typingUser");

    // Initialize Pusher
    var pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        forceTLS: true,
    });

    var allUserChatChannel = pusher.subscribe("company." + companyId);

    allUserChatChannel.bind("allUsersMessage", function (data) {
        Livewire.dispatch("incomingMessage", { id: data.message.id });
    });

    var typingChannel = pusher.subscribe("chat-between-all-users");

    typingChannel.bind("UserTyping", function (data) {
        const currentUserId = parseInt(
            document.getElementById("current_user_id").value
        );
        if (data.user_id === currentUserId) return;

        userSpan.textContent = data.user_name;
        indicator.style.display = "block";

        clearTimeout(window.typingTimeout);
        window.typingTimeout = setTimeout(() => {
            indicator.style.display = "none";
        }, 2000);
    });
});
