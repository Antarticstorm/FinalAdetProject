/* ==========================================================
   SHOP
========================================================== */

function initializeShop() {
    initializeCart();
    initializeWishlist();
    initializeDetailsWishlist();
    initializeGenreChips();
    initializeToolbar();
    initializeFilters();
}
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
   Smooth Navigation
------------------------------------------ */

function fadeBooks() {

    const grid = document.querySelector(".book-grid-shop");

    if (!grid) return;

    grid.classList.add("shop-loading");

}

function loadBooks(url) {

        console.log("Loading:", url);

    fadeBooks();

    fetch(url, {
        headers: {
            "X-Requested-With": "XMLHttpRequest"
        }
    })

    .then(r => r.text())

.then(html => {

                const doc = new DOMParser().parseFromString(html, "text/html");

                const newContainer = doc.getElementById("shop-results-container");

                if (!newContainer) {
                    console.error("AJAX response is missing #shop-results-container");
                    console.log(html);
                    return;
                }

                document.getElementById("shop-results-container").innerHTML =
                    newContainer.innerHTML;

                    initializeShop();

                    history.pushState({}, "", url);

                })
            .catch(console.error);

}

function initializeToolbar() {

    const toolbar = document.querySelector(".shop-toolbar");

    if (!toolbar) return;

    toolbar.onsubmit = function(e){

        e.preventDefault();

        const params = new URLSearchParams(new FormData(this));

        loadBooks(`${window.location.pathname}?${params.toString()}`);

    };

    toolbar.querySelectorAll("select").forEach(select => {

        select.onchange = () => {

            toolbar.requestSubmit();

        };

    });

}
/* ------------------------------------------
   Add to Cart (AJAX)
------------------------------------------ */

function initializeCart() {

    document.querySelectorAll(".book-cart-form").forEach(form => {

        form.onsubmit = function(e){

            e.preventDefault();

            const url = this.getAttribute("action");

            fetch(url,{
                method:"POST",
                body:new FormData(this),
                headers:{
                    "X-Requested-With":"XMLHttpRequest"
                }
            })

            .then(response=>{

                if(!response.ok){
                    throw new Error("Server returned " + response.status);
                }

                return response.json();

            })

            .then(data=>{

                if(!data.success){

                    showToast(data.message || "Unable to add book.");

                    return;

                }

                showToast(data.message);

                const badge = document.getElementById("cart-count");

                if(badge){

                    badge.textContent = data.cartCount;

                }

            })

            .catch(error=>{

                console.error(error);

                showToast("Something went wrong.");

            });

        };

    });

}


/* Genre Chips */

function initializeGenreChips(){

    document.querySelectorAll(".genre-chip").forEach(link=>{

        link.onclick = function(e){

            e.preventDefault();

            console.log("Genre AJAX");

            loadBooks(this.href);

        };

    });

}
/* Advanced Filters */

function initializeFilters() {

    const filters = document.querySelector(".shop-filter-panel");

    if (!filters) return;

    filters.onsubmit = function(e){

        e.preventDefault();

        const params = new URLSearchParams(new FormData(this));

        loadBooks(`${window.location.pathname}?${params.toString()}`);

    };

}

function initializeDetailsWishlist() {

    document.querySelectorAll(".details-wishlist").forEach(button => {

        button.onclick = function(e){

            e.preventDefault();

            fetch(this.href,{
                headers:{
                    "X-Requested-With":"XMLHttpRequest"
                }
            })

            .then(r => r.json())

            .then(data => {

                if(!data.success){

                    showToast(data.message);
                    return;

                }

                if(data.saved){

                    this.classList.add("saved");
                    this.textContent = "Saved!";

                }else{

                    this.classList.remove("saved");
                    this.textContent = "Wishlist?";

                }

                showToast(data.message);

            })

            .catch(console.error);

        };

    });

}
function initializeWishlist() {

    document.querySelectorAll(".wishlist-heart").forEach(button => {

        button.onclick = function(e){

            e.preventDefault();

            fetch(this.href,{
                headers:{
                    "X-Requested-With":"XMLHttpRequest"
                }
            })

            .then(r => r.json())

            .then(data => {

                if(!data.success){
                    alert(data.message);
                    return;
                }

                if(data.saved){
                    this.classList.add("saved");
                    this.textContent = "❤️";
                } else {
                    this.classList.remove("saved");
                    this.textContent = "♡";
                }

                this.classList.add("animate");

                setTimeout(() => {
                    this.classList.remove("animate");
                }, 350);

            })

            .catch(console.error);

        };

    });

}

initializeShop();