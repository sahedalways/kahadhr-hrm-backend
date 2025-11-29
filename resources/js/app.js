// Import EmojiButton
import { EmojiButton } from "@joeattardi/emoji-button/dist/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("message_input");
    const trigger = document.getElementById("show_emoji_box");

    if (!trigger || !input) return;

    const picker = new EmojiButton({
        position: "top-start",
        autoHide: false,
    });

    // When an emoji is selected
    picker.on("emoji", (selection) => {
        const emojiChar = selection.emoji;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;

        input.value = text.slice(0, start) + emojiChar + text.slice(end);
        input.focus();
        input.selectionStart = input.selectionEnd = start + emojiChar.length;

        input.dispatchEvent(new Event("input", { bubbles: true }));
    });

    trigger.addEventListener("click", () => picker.togglePicker(trigger));
});
