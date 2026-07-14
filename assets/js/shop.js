document.querySelectorAll(".book-cart-form").forEach(form => {

    form.addEventListener("submit", function(e) {

        e.preventDefault();

        fetch(this.getAttribute("action") ||    "cart_action.php", {
            method: "POST",
            body: new FormData(this),
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error("Server returned status " + response.status);
                }
            return response.json();
        })
        .then(data => {

            if (!data.success) {
                alert(data.message || "Failed to add book.");
                return;
            }

        alert(data.message);

        const badge = document.getElementById("cart-count");

        if (badge) {
            badge.textContent = data.cartCount;
        }

        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong adding this book to your cart. Check the console/network tab for details.");
        });

    });

});