<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
REQUIRE_ONCE(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/order_helpers.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$basePath = "";
require_once(ROOT_PATH . "/includes/header.php");

$orderId = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "<div class='card'><p>Order not found.</p></div>";
    include("includes/footer.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderItems = $stmt->get_result();
?>

<div class="card">

    <h1>Thank you for your order!</h1>
    <p>Order <strong><?php echo htmlspecialchars($order['order_number']); ?></strong> has been placed
       and is currently <span class="status-badge <?php echo statusBadgeClass($order['status']); ?>"><?php echo $order['status']; ?></span>.</p>

    <table>
        <tr><th>Book</th><th>Qty</th><th>Line Total</th></tr>
        <?php while ($item = $orderItems->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['line_total'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div style="margin-top:16px;">
        <div class="summary-line"><span>Subtotal</span><span>₱<?php echo number_format($order['subtotal'], 2); ?></span></div>
        <div class="summary-line"><span>Discount</span><span>-₱<?php echo number_format($order['discount_amount'], 2); ?></span></div>
        <div class="summary-line total"><span>Total</span><span>₱<?php echo number_format($order['total_amount'], 2); ?></span></div>
    </div>

    <p style="margin-top:16px;">Shipping to: <?php echo htmlspecialchars($order['shipping_address']); ?>
       (<?php echo htmlspecialchars($order['shipping_method']); ?>)</p>

    <div style="margin-top:20px;">
        <a href="my_orders.php" class="btn btn-primary">View My Orders</a>
        <a href="shop.php" class="btn btn-outline">Continue Shopping</a>
    </div>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>
