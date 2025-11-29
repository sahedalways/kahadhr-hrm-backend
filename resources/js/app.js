// Import EmojiButton
import { EmojiButton } from "@joeattardi/emoji-button/dist/index.js";

document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("message_input");
    const trigger = document.getElementById("show_emoji_box");

    if (!trigger || !input) return; // safety check

    // Initialize Emoji Picker
    const picker = new EmojiButton({
        position: "top-start", // adjust position relative to button
        autoHide: false, // allow multiple emoji selections
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

        // Trigger input event for Livewire
        input.dispatchEvent(new Event("input", { bubbles: true }));
    });

    // Toggle emoji picker on button click
    trigger.addEventListener("click", () => picker.togglePicker(trigger));
});
