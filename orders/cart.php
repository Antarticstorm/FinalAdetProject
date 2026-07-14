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

<div id="cart-data"
            data-promo-type="<?php echo htmlspecialchars($promoType); ?>"
            data-promo-value="<?php echo $promoValue; ?>">
        </div>  

<div class="card">

    <h1>

        Shopping Cart

    </h1>

    <p class="cart-subtitle">

        <?= count($items) ?>

        item<?= count($items) != 1 ? "s" : "" ?>

        ready for checkout.

    </p>    

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
                        <div class="card cart-item-card">

                            <img
                                src="<?= url($item['cover']) ?>"
                                class="cart-cover"
                                alt="<?= htmlspecialchars($item['title']) ?>">

                            <div class="cart-info">

                                <h3>

                                    <?= htmlspecialchars($item['title']) ?>

                                </h3>

                                <p class="cart-author">

                                    by <?= htmlspecialchars($item['author']) ?>

                                </p>

                                <p class="cart-price">

                                    ₱<?= number_format($item['unit_price'],2) ?>

                                </p>

                            </div>

                            <div class="cart-controls">

                                <form
                                    action="cart_action.php"
                                    method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update">

                                    <input
                                        type="hidden"
                                        name="book_id"
                                        value="<?= $item['book_id'] ?>">

                                    <div class="quantity-control">

                                        <button
                                            type="button"
                                            class="qty-btn"
                                            onclick="changeQty(this,-1)">

                                            −

                                        </button>

                                        <input
                                            class="qty-input"
                                            type="number"
                                            name="quantity"
                                            value="<?= $item['quantity'] ?>"
                                            min="1"
                                            max="<?= $item['stock'] ?>"
                                            data-book-id="<?= $item['book_id'] ?>"
                                            data-price="<?= $item['unit_price'] ?>"
                                            readonly>

                                        <button
                                            type="button"
                                            class="qty-btn"
                                            onclick="changeQty(this,1)">

                                            +

                                        </button>

                                    </div>

                                </form>

                                <strong
                                    id="line-total-<?= $item['book_id'] ?>">

                                    ₱<?= number_format($item['line_total'],2) ?>

                                </strong>

                                <form
                                    action="cart_action.php"
                                    method="POST">

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="remove">

                                    <input
                                        type="hidden"
                                        name="book_id"
                                        value="<?= $item['book_id'] ?>">

                                    <button
                                        class="btn btn-danger">

                                        🗑 Remove

                                    </button>

                                </form>

                            </div>

                        </div>
                <?php endforeach; ?>
            </div>

            <div>
                <div class="card order-summary">
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
                        <span  id="js-subtotal">₱<?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <div class="summary-line">
                        <span>Discount</span>
                        <span  id="js-discount">-₱<?php echo number_format($discount, 2); ?></span>
                    </div>

                    <div class="summary-line total">
                        <span>Total</span>
                        <span id="js-total">₱<?php echo number_format($total, 2); ?></span>
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

function changeCartQty(button, change){

    changeQty(button, change);

    const input =
        button.parentElement.querySelector(".qty-input");

    onQtyChange(input);

}
 
</script>
<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>
