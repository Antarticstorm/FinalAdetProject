document.addEventListener("DOMContentLoaded", () => {

    const input = document.getElementById("avatar");
    const preview = document.getElementById("avatarPreview");

    if (!input || !preview) return;

    input.addEventListener("change", () => {

        const file = input.files[0];

        if (!file) return;

        preview.src = URL.createObjectURL(file);

    });

});