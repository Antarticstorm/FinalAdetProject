function initializeWishlist() {
document.querySelectorAll(".wishlist-heart").forEach(button => {

    button.addEventListener("click", function(e){

        e.preventDefault();

        fetch(this.href, {

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

            }else{

                this.classList.remove("saved");

                this.textContent = "♡";

            }

            /* Play animation */

            this.classList.add("animate");

            setTimeout(() => {

                this.classList.remove("animate");

            },350);

        })

        .catch(console.error);

    });

});
}
initializeWishlist();