<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/db.php");
include("includes/order_helpers.php");

$basePath = "";
include("includes/header.php");

$cartData = getCartDetails($conn);
$items = $cartData['items'];
$subtotal = $cartData['subtotal'];

$promo = isset($_SESSION['promo_code']) ? getActivePromo($conn, $_SESSION['promo_code']) : null;
$discount = calculatePromoDiscount($subtotal, $promo);
$total = round($subtotal - $discount, 2);
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
                                   min="1" max="<?php echo $item['stock']; ?>" class="qty-input">
                            <button type="submit" class="btn btn-outline">Update</button>
                        </form>

                        <form action="cart_action.php" method="POST">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="book_id" value="<?php echo $item['book_id']; ?>">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>

                        <strong>₱<?php echo number_format($item['line_total'], 2); ?></strong>
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

<?php include("includes/footer.php"); ?>
