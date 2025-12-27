function openDocumentModal(
    typeId,
    docId = null,
    fileUrl = "",
    expiresAt = "",
    comment = ""
) {
    const modalEl = document.getElementById("openDocumentModal");
    if (!modalEl) return console.error("Modal not found in DOM");

    const docTypeInput = modalEl.querySelector("#doc_type_id");
    const docIdInput = modalEl.querySelector("#doc_id");
    const uploadSection = modalEl.querySelector("#upload_section");
    const existingSection = modalEl.querySelector("#existing_section");
    const pdfViewer = modalEl.querySelector("#pdf_viewer");
    const deleteBtn = modalEl.querySelector("#deleteDocumentBtn");

    docTypeInput.value = typeId;
    docIdInput.value = docId ?? "";

    if (docId) {
        uploadSection.style.display = "none";
        existingSection.style.display = "block";
        deleteBtn.style.display = "inline-block";

        pdfViewer.src = fileUrl;
        existingSection.querySelector('[name="expires_at"]').value = expiresAt;
        existingSection.querySelector('[name="comment"]').value = comment;
    } else {
        uploadSection.style.display = "block";
        existingSection.style.display = "none";
        deleteBtn.style.display = "none";

        pdfViewer.src = "";
        uploadSection.querySelector('[name="file_path"]').value = "";
        uploadSection.querySelector('[name="expires_at"]').value = "";
        uploadSection.querySelector('[name="comment"]').value = "";
    }

    // Use existing instance if exists
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal)
        modal = new bootstrap.Modal(modalEl, {
            backdrop: "static",
            keyboard: false,
        });
    modal.show();

    // Close button event
    const closeBtn =
        modalEl.querySelector("#closeDocumentBtn") ||
        modalEl.querySelector("#closeDocumentBtnTwo");

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.hide();
        });
    }
}
