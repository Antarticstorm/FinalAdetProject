document.addEventListener("DOMContentLoaded", () => {

    const input = document.getElementById("password");
    const button = document.getElementById("togglePasswordBtn");

    if (!input || !button) return;

    button.addEventListener("click", () => {

        if (input.type === "password") {
            input.type = "text";
            button.textContent = "🙈";
        } else {
            input.type = "password";
            button.textContent = "👁";
        }

    });

});