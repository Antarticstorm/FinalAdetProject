<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/order_helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

$cartData = getCartDetails($conn);
$items = $cartData['items'];
$subtotal = $cartData['subtotal'];

$promo = isset($_SESSION['promo_code']) ? getActivePromo($conn, $_SESSION['promo_code']) : null;
$discount = calculatePromoDiscount($subtotal, $promo);
$total = round($subtotal - $discount, 2);

$promoType = null;
$promoValue = 0;
if ($promo) {
    if (!empty($promo['discount_percent'])) {
        $promoType = 'percent';
        $promoValue = (float) $promo['discount_percent'];
    } else {
        $promoType = 'amount';
        $promoValue = (float) $promo['discount_amount'];
    }
}

?>



<div class="card">

    <h1>Your Cart</h1>

    <?php if (isset($_SESSION['cart_notice'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['cart_notice']); unset($_SESSION['cart_notice']); ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>

        <div class="empty-state">
            <p>Your cart is empty.</p>
            <a href="shop.php" class="btn btn-primary" style="margin-top:14px;display:inline-block;">Browse Books</a>
        </div>

    <?php else: ?>

        <div class="two-col">

            <div>
                <?php foreach ($items as $item): ?>
                    <div class="cart-row">
                        <img src="<?php echo htmlspecialchars($item['cover']); ?>" alt="cover">

                        <div class="grow">
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                            <p style="font-size:0.85rem;">by <?php echo htmlspecialchars($item['author']); ?></p>
                            <p style="font-size:0.85rem;">₱<?php echo number_format($item['unit_price'], 2); ?> each</p>
                        </div>

                        <form action="cart_action.php" method="POST" class="inline-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="book_id" value="<?php echo $item['book_id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                   min="1" max="<?php echo $item['stock']; ?>" class="qty-input"
                                   data-book-id="<?php echo $item['book_id']; ?>"
                                   data-price="<?php echo $item['unit_price']; ?>"
                                   oninput="onQtyChange(this)">
                        </form>

                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="book_id" value="<?php echo $item['book_id']; ?>">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>

                        <strong id="line-total-<?php echo $item['book_id']; ?>">₱<?php echo number_format($item['line_total'], 2); ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>

            <div>
                <div class="card" style="background:#202f40;">
                    <h2>Order Summary</h2>

                    <form action="cart_action.php" method="POST" class="inline-form" style="margin-bottom:14px;">
                        <input type="hidden" name="action" value="apply_promo">
                        <input type="text" name="promo_code" placeholder="Promo code"
                               value="<?php echo isset($_SESSION['promo_code']) ? htmlspecialchars($_SESSION['promo_code']) : ''; ?>">
                        <button type="submit" class="btn btn-outline">Apply</button>
                    </form>

                    <?php if ($promo): ?>
                        <div class="alert alert-success" style="display:flex;justify-content:space-between;align-items:center;">
                            <span><?php echo htmlspecialchars($promo['code']); ?> applied</span>
                            <form action="cart_action.php" method="POST">
                                <input type="hidden" name="action" value="remove_promo">
                                <button type="submit" class="btn btn-outline" style="padding:4px 10px;">Remove</button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <div class="summary-line">
                        <span>Discount</span>
                        <span>-₱<?php echo number_format($discount, 2); ?></span>
                    </div>

                    <div class="summary-line total">
                        <span>Total</span>
                        <span>₱<?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-primary" style="width:100%;text-align:center;margin-top:16px;display:block;">
                        Proceed to Checkout
                    </a>
                </div>
            </div>

        </div>

    <?php endif; ?>

</div>

<script>
const PROMO_TYPE = <?php echo json_encode($promoType); ?>;
const PROMO_VALUE = <?php echo json_encode($promoValue); ?>;
 
function formatMoney(n) {
    return n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
 
function recalcCart() {
    let subtotal = 0;
 
    document.querySelectorAll('.qty-input').forEach(function (input) {
        const price = parseFloat(input.dataset.price);
        const qty = parseInt(input.value, 10) || 0;
        const lineTotal = price * qty;
        subtotal += lineTotal;
 
        const lineEl = document.getElementById('line-total-' + input.dataset.bookId);
        if (lineEl) lineEl.textContent = '₱' + formatMoney(lineTotal);
    });
 
    let discount = 0;
    if (PROMO_TYPE === 'percent') {
        discount = subtotal * (PROMO_VALUE / 100);
    } else if (PROMO_TYPE === 'amount') {
        discount = PROMO_VALUE;
    }
    discount = Math.min(discount, subtotal);
 
    const total = subtotal - discount;
 
    document.getElementById('js-subtotal').textContent = '₱' + formatMoney(subtotal);
    document.getElementById('js-discount').textContent = '-₱' + formatMoney(discount);
    document.getElementById('js-total').textContent = '₱' + formatMoney(total);
}
 
// Debounce so we don't spam a request on every single keystroke/click,
// but still save the new quantity to the session cart in the background.
const saveTimers = {};
 
function onQtyChange(input) {
    recalcCart();
 
    const qty = parseInt(input.value, 10);
    const max = parseInt(input.max, 10);
    const min = parseInt(input.min, 10) || 1;
    if (!qty || qty < min || (max && qty > max)) return;
 
    const bookId = input.dataset.bookId;
    clearTimeout(saveTimers[bookId]);
    saveTimers[bookId] = setTimeout(function () {
        input.closest('form').submit();
    }, 700);
}
</script>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>
