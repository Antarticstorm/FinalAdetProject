/* ==========================================================
   SHOP
========================================================== */

/* ------------------------------------------
   Toast
------------------------------------------ */

function showToast(message) {

    const toast = document.getElementById("toast");

    if (!toast) return;

    toast.textContent = message;

    toast.classList.add("show");

    clearTimeout(toast.timer);

    toast.timer = setTimeout(() => {

        toast.classList.remove("show");

    }, 2500);

}

/* ------------------------------------------
   Add to Cart (AJAX)
------------------------------------------ */

document.querySelectorAll(".book-cart-form").forEach(form => {

    form.addEventListener("submit", function (e) {

        e.preventDefault();

        const url = this.getAttribute("action");

        fetch(url, {
            method: "POST",
            body: new FormData(this),
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })

        .then(response => {

            if (!response.ok) {

                throw new Error("Server returned " + response.status);

            }

            return response.json();

        })

        .then(data => {

            if (!data.success) {

                showToast(data.message || "Unable to add book.");

                return;

            }

            showToast(data.message);

            const badge = document.getElementById("cart-count");

            if (badge) {

                badge.textContent = data.cartCount;

            }

        })

        .catch(error => {

            console.error(error);

            showToast("Something went wrong.");

        });

    });

});

/* ------------------------------------------
   Smooth Navigation
------------------------------------------ */

function fadeBooks() {

    const grid = document.querySelector(".book-grid-shop");

    if (!grid) return;

    grid.classList.add("shop-loading");

}

/* Genre Chips */

document.querySelectorAll(".genre-chip").forEach(link => {

    link.addEventListener("click", function (e) {

        e.preventDefault();

        fadeBooks();

        setTimeout(() => {

            window.location.href = this.href;

        }, 220);

    });

});

/* Search */

const toolbar = document.querySelector(".shop-toolbar");

if (toolbar) {

    toolbar.addEventListener("submit", fadeBooks);

}

/* Advanced Filters */

const filters = document.querySelector(".shop-filter-panel");

if (filters) {

    filters.addEventListener("submit", fadeBooks);

}

/* Sort */

document.querySelectorAll(".shop-toolbar select").forEach(select => {

    select.addEventListener("change", () => {

        fadeBooks();

    });

});