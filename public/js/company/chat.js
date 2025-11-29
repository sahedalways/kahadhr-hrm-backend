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

    const emojiBtn = document.getElementById("emojiBtn");
    const emojiPicker = document.getElementById("emojiPicker");
    const messageInput = document.getElementById("messageInput");

    emojiBtn.addEventListener("click", () => {
        const isHidden = emojiPicker.style.display === "none";
        emojiPicker.style.display = isHidden ? "block" : "none";
    });

    emojiPicker.addEventListener("emoji-click", (event) => {
        const emoji = event.detail.unicode;

        const start = messageInput.selectionStart;
        const end = messageInput.selectionEnd;
        messageInput.value =
            messageInput.value.substring(0, start) +
            emoji +
            messageInput.value.substring(end);

        messageInput.selectionStart = messageInput.selectionEnd =
            start + emoji.length;
        messageInput.focus();

        emojiPicker.style.display = "none";
    });

    document.addEventListener("click", (event) => {
        if (
            !emojiBtn.contains(event.target) &&
            !emojiPicker.contains(event.target)
        ) {
            emojiPicker.style.display = "none";
        }
    });
    // Toggle picker when button clicked
    emojiBtn.addEventListener("click", () => {
        picker.togglePicker(emojiBtn);
    });
});

document.getElementById("mentionBtn").addEventListener("click", () => {
    // show employee list to mention
});
