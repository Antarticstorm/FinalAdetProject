<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/db.php");
include("includes/order_helpers.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$basePath = "";
include("includes/header.php");

$cartData = getCartDetails($conn);
$items = $cartData['items'];
$subtotal = $cartData['subtotal'];

$promo = isset($_SESSION['promo_code']) ? getActivePromo($conn, $_SESSION['promo_code']) : null;
$discount = calculatePromoDiscount($subtotal, $promo);
$total = round($subtotal - $discount, 2);

// Pull the customer's saved details to prefill the shipping form.
$stmt = $conn->prepare("SELECT fullname, phone, address FROM customers WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = "";

if (empty($items)) {
    $error = "Your cart is empty.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($error)) {

    $shippingName = trim($_POST['shipping_fullname']);
    $shippingPhone = trim($_POST['shipping_phone']);
    $shippingAddress = trim($_POST['shipping_address']);
    $shippingMethod = $_POST['shipping_method'];

    if (empty($shippingName) || empty($shippingPhone) || empty($shippingAddress)) {
        $error = "Please complete all shipping fields.";
    } else {

        mysqli_begin_transaction($conn);

        try {
            // Re-check stock for every item, locking the rows so two
            // customers can't both check out the last copy at once.
            $verifiedItems = [];
            $verifiedSubtotal = 0;

            foreach ($items as $item) {
                $stmt = $conn->prepare("SELECT id, title, price, discount_percent, stock FROM books WHERE id = ? FOR UPDATE");
                $stmt->bind_param("i", $item['book_id']);
                $stmt->execute();
                $book = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$book) {
                    throw new Exception("One of the books in your cart is no longer available.");
                }

                if ($book['stock'] < $item['quantity']) {
                    throw new Exception("Only " . $book['stock'] . " left of \"" . $book['title'] . "\". Please update your cart.");
                }

                $unitPrice = getEffectivePrice($book['price'], $book['discount_percent']);
                $lineTotal = round($unitPrice * $item['quantity'], 2);

                $verifiedItems[] = [
                    "book_id"    => $book['id'],
                    "title"      => $book['title'],
                    "unit_price" => $unitPrice,
                    "quantity"   => $item['quantity'],
                    "line_total" => $lineTotal,
                ];

                $verifiedSubtotal += $lineTotal;
            }

            $verifiedSubtotal = round($verifiedSubtotal, 2);
            $verifiedDiscount = calculatePromoDiscount($verifiedSubtotal, $promo);
            $verifiedTotal = round($verifiedSubtotal - $verifiedDiscount, 2);
            $promoCode = $promo ? $promo['code'] : null;
            $orderNumber = generateOrderNumber($conn);

            $stmt = $conn->prepare("
                INSERT INTO orders
                    (order_number, customer_id, subtotal, discount_amount, promo_code, total_amount,
                     status, shipping_fullname, shipping_phone, shipping_address, shipping_method)
                VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "siddsdssss",
                $orderNumber,
                $_SESSION['user_id'],
                $verifiedSubtotal,
                $verifiedDiscount,
                $promoCode,
                $verifiedTotal,
                $shippingName,
                $shippingPhone,
                $shippingAddress,
                $shippingMethod
            );
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();

            foreach ($verifiedItems as $vi) {
                $stmt = $conn->prepare("
                    INSERT INTO order_items (order_id, book_id, title, unit_price, quantity, line_total)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "iisdid",
                    $orderId,
                    $vi['book_id'],
                    $vi['title'],
                    $vi['unit_price'],
                    $vi['quantity'],
                    $vi['line_total']
                );
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
                $stmt->bind_param("ii", $vi['quantity'], $vi['book_id']);
                $stmt->execute();
                $stmt->close();
            }

            mysqli_commit($conn);

            // Clear the cart now that the order is placed.
            unset($_SESSION['cart']);
            unset($_SESSION['promo_code']);

            // Best-effort confirmation email; checkout still succeeds if this fails.
            if (file_exists(__DIR__ . "/config/mail_config.php")) {
                include_once("includes/mail.php");

                $stmt = $conn->prepare("SELECT email FROM customers WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $custEmail = $stmt->get_result()->fetch_assoc()['email'];
                $stmt->close();

                $itemRows = "";
                foreach ($verifiedItems as $vi) {
                    $itemRows .= "<tr><td style='padding:6px;'>" . htmlspecialchars($vi['title']) . "</td>"
                        . "<td style='padding:6px;'>x" . $vi['quantity'] . "</td>"
                        . "<td style='padding:6px;'>₱" . number_format($vi['line_total'], 2) . "</td></tr>";
                }

                sendEmail(
                    $custEmail,
                    "Order Confirmation - " . $orderNumber,
                    "
                    <div style='max-width:600px;margin:auto;background:#1B2838;color:white;font-family:Arial,sans-serif;padding:30px;border-radius:10px;'>
                        <h1 style='color:#66C0F4;text-align:center;'>The Literary Nook</h1>
                        <hr style='border:1px solid #2A475E;'>
                        <h2>Thanks for your order, " . htmlspecialchars($shippingName) . "!</h2>
                        <p>Order number: <strong>" . $orderNumber . "</strong></p>
                        <table style='width:100%;margin-top:12px;'>" . $itemRows . "</table>
                        <p style='margin-top:12px;'>Total: <strong>₱" . number_format($verifiedTotal, 2) . "</strong></p>
                        <p>We'll email you again once your order ships.</p>
                    </div>
                    "
                );
            }

            header("Location: order_confirmation.php?id=" . $orderId);
            exit();

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();

            // Refresh cart data since stock may have changed underneath us.
            $cartData = getCartDetails($conn);
            $items = $cartData['items'];
            $subtotal = $cartData['subtotal'];
            $discount = calculatePromoDiscount($subtotal, $promo);
            $total = round($subtotal - $discount, 2);
        }
    }
}
?>

<div class="card">

    <h1>Checkout</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>

        <div class="two-col">

            <form method="POST" action="">
                <h2>Shipping Details</h2>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="shipping_fullname" required
                           value="<?php echo htmlspecialchars($customer['fullname'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="shipping_phone" required
                           value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Shipping Address</label>
                    <textarea name="shipping_address" required><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Shipping Method</label>
                    <select name="shipping_method">
                        <option value="Standard Shipping">Standard Shipping (3-5 days)</option>
                        <option value="Express Shipping">Express Shipping (1-2 days)</option>
                        <option value="Store Pickup">Store Pickup</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>

            <div class="card" style="background:#202f40;">
                <h2>Order Summary</h2>

                <?php foreach ($items as $item): ?>
                    <div class="summary-line">
                        <span><?php echo htmlspecialchars($item['title']); ?> x<?php echo $item['quantity']; ?></span>
                        <span>₱<?php echo number_format($item['line_total'], 2); ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                </div>

                <div class="summary-line">
                    <span>Discount<?php echo $promo ? " (" . htmlspecialchars($promo['code']) . ")" : ""; ?></span>
                    <span>-₱<?php echo number_format($discount, 2); ?></span>
                </div>

                <div class="summary-line total">
                    <span>Total</span>
                    <span>₱<?php echo number_format($total, 2); ?></span>
                </div>
            </div>

        </div>

    <?php else: ?>
        <div class="empty-state">
            <p>There's nothing to check out.</p>
            <a href="shop.php" class="btn btn-primary" style="margin-top:14px;display:inline-block;">Browse Books</a>
        </div>
    <?php endif; ?>

</div>

<?php include("includes/footer.php"); ?>
