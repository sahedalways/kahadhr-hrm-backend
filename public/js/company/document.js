document
    .getElementById("deleteDocumentBtn")
    .addEventListener("click", async function () {
        const modalEl = document.getElementById("openDocumentModal");
        const docId = modalEl.querySelector("#doc_id").value;
        if (!docId) return;

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const btn = this;
        const btnText = document.getElementById("deleteBtnText");
        const btnLoader = document.getElementById("deleteBtnLoader");

        // Show loader
        btnText.textContent = "Deleting...";
        btnLoader.classList.remove("d-none");
        btn.disabled = true;

        try {
            const response = await fetch(
                `/dashboard/employees/documents/delete/${docId}`,
                {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                }
            );

            const result = await response.json();

            if (result.success) {
                toastr.success(result.message);
                const docEl = document.querySelector(
                    `[data-doc-id="${docId}"]`
                );
                if (docEl) {
                    const parentCard = docEl.closest(".col-md-3");
                    docEl.remove();

                    const remainingDocs =
                        parentCard.querySelectorAll("[data-doc-id]");
                    if (remainingDocs.length === 0) {
                        parentCard.remove();
                    }
                }

                const remainingTypeCards =
                    document.querySelectorAll(".col-md-3");
                if (remainingTypeCards.length === 0) {
                    const container = document.querySelector(".row.g-4");
                    const alert = document.createElement("div");
                    alert.className = "col-12";
                    alert.innerHTML = `
                <div class="alert alert-info text-center text-white">
                    No documents found for this employee.
                </div>
            `;
                    container.appendChild(alert);
                }
                // Close modal
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            } else {
                toastr.error(result.message);
            }
        } catch (error) {
            toastr.error("Something went wrong. Please try again!");
            console.error(error);
        } finally {
            btnText.textContent = "Delete";
            btnLoader.classList.add("d-none");
            btn.disabled = false;
        }
    });

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
