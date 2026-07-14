const saveTimers = {};

function formatMoney(n) {

    return n
        .toFixed(2)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ',');

}

function recalcCart() {

    let subtotal = 0;

    document.querySelectorAll(".qty-input").forEach(function(input){

        const price =
            parseFloat(input.dataset.price);

        const qty =
            parseInt(input.value) || 0;

        const lineTotal =
            price * qty;

        subtotal += lineTotal;

        const line =
            document.getElementById(
                "line-total-" + input.dataset.bookId
            );

        if(line){

            line.textContent =
                "₱" + formatMoney(lineTotal);

        }

    });

    let discount = 0;

    if(PROMO_TYPE === "percent"){

        discount =
            subtotal * (PROMO_VALUE / 100);

    }

    else if(PROMO_TYPE === "amount"){

        discount = PROMO_VALUE;

    }

    discount =
        Math.min(discount, subtotal);

    const total =
        subtotal - discount;

    document.getElementById("js-subtotal").textContent =
        "₱" + formatMoney(subtotal);

    document.getElementById("js-discount").textContent =
        "-₱" + formatMoney(discount);

    document.getElementById("js-total").textContent =
        "₱" + formatMoney(total);

}

function changeQty(button, change){

    const input =
        button.parentElement.querySelector(".qty-input");

    let value =
        parseInt(input.value);

    const min =
        parseInt(input.min);

    const max =
        parseInt(input.max);

    value += change;

    value =
        Math.max(min, Math.min(value, max));

    input.value = value;

        onQtyChange(input);

}

function onQtyChange(input){

    recalcCart();

    const qty =
        parseInt(input.value);

    const min =
        parseInt(input.min) || 1;

    const max =
        parseInt(input.max);

    if(!qty || qty < min || qty > max){

        return;

    }

    const id =
        input.dataset.bookId;

    clearTimeout(saveTimers[id]);

    saveTimers[id] = setTimeout(function(){

        input.closest("form").submit();

    },700);

}