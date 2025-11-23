document
    .getElementById("changePasswordForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const newPassword = document.getElementById("new_password");
        const confirmPassword = document.getElementById("confirm_password");
        const messageBox = document.getElementById("passwordMessage");
        const companyId = document.getElementById("companyId").value;
        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;

        const submitBtn = document.getElementById("changePasswordBtn");
        const btnText = document.getElementById("btnText");
        const btnLoader = document.getElementById("btnLoader");

        // Reset previous error styles
        newPassword.classList.remove("border-danger");
        confirmPassword.classList.remove("border-danger");
        messageBox.textContent = "";

        // Validation: passwords match
        if (newPassword.value.trim() !== confirmPassword.value.trim()) {
            messageBox.textContent = "Passwords do not match!";
            newPassword.classList.add("border-danger");
            confirmPassword.classList.add("border-danger");
            return;
        }

        // Show loader and update button text
        btnText.textContent = "Saving..."; // change text
        btnLoader.classList.remove("d-none");
        submitBtn.disabled = true;

        try {
            const response = await fetch(
                `/dashboard/companies/change-password/${companyId}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        password: newPassword.value,
                        password_confirmation: confirmPassword.value,
                    }),
                }
            );

            const result = await response.json();

            if (result.success) {
                toastr.success(result.message);

                // Reset form
                newPassword.value = "";
                confirmPassword.value = "";
                newPassword.classList.remove("border-danger");
                confirmPassword.classList.remove("border-danger");
            } else {
                toastr.error(result.message);
            }
        } catch (error) {
            toastr.error("Something went wrong. Please try again!");
            console.error(error);
        } finally {
            // Hide loader and reset button text
            btnText.textContent = "Save Password";
            btnLoader.classList.add("d-none");
            submitBtn.disabled = false;
        }
    });
