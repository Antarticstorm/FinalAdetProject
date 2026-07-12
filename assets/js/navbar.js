console.log("Navbar JS Loaded");
document.addEventListener("DOMContentLoaded", () => {

    const button = document.getElementById("accountBtn");
    const menu = document.getElementById("accountMenu");

    if (!button || !menu) return;

    button.addEventListener("click", (e) => {
        e.stopPropagation();
        menu.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {

        if (
            !button.contains(e.target) &&
            !menu.contains(e.target)
        ) {
            menu.classList.remove("show");
        }

    });

});