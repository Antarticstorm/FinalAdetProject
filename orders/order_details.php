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

    <h1>Order <?php echo htmlspecialchars($order['order_number']); ?></h1>
    <p>Placed on <?php echo date("F j, Y g:ia", strtotime($order['created_at'])); ?></p>
    <p style="margin-top:8px;">
        Status: <span class="status-badge <?php echo statusBadgeClass($order['status']); ?>"><?php echo $order['status']; ?></span>
    </p>

    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-success">Order cancelled.</div>
    <?php endif; ?>

    <?php if ($order['status'] === 'pending'): ?>
        <form action="cancel_order.php" method="POST" style="margin-top:12px;"
              onsubmit="return confirm('Cancel this order?');">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <button type="submit" class="btn btn-danger">Cancel Order</button>
        </form>
    <?php endif; ?>

    <h2 style="margin-top:24px;">Items</h2>
    <table>
        <tr><th>Book</th><th>Unit Price</th><th>Qty</th><th>Line Total</th></tr>
        <?php while ($item = $orderItems->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td>₱<?php echo number_format($item['unit_price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format($item['line_total'], 2); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div style="margin-top:16px;">
        <div class="summary-line"><span>Subtotal</span><span>₱<?php echo number_format($order['subtotal'], 2); ?></span></div>
        <div class="summary-line"><span>Discount<?php echo $order['promo_code'] ? " (" . htmlspecialchars($order['promo_code']) . ")" : ""; ?></span><span>-₱<?php echo number_format($order['discount_amount'], 2); ?></span></div>
        <div class="summary-line total"><span>Total</span><span>₱<?php echo number_format($order['total_amount'], 2); ?></span></div>
    </div>

    <h2 style="margin-top:24px;">Shipping</h2>
    <p><?php echo htmlspecialchars($order['shipping_fullname']); ?> &middot; <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
    <p>Method: <?php echo htmlspecialchars($order['shipping_method']); ?></p>

    <?php if ($order['carrier'] || $order['tracking_number']): ?>
        <p style="margin-top:8px;">
            Carrier: <?php echo htmlspecialchars($order['carrier'] ?: '—'); ?> &middot;
            Tracking #: <?php echo htmlspecialchars($order['tracking_number'] ?: '—'); ?>
        </p>
    <?php endif; ?>

    <?php if ($order['shipped_at']): ?>
        <p>Shipped: <?php echo date("F j, Y", strtotime($order['shipped_at'])); ?></p>
    <?php endif; ?>

    <?php if ($order['delivered_at']): ?>
        <p>Delivered: <?php echo date("F j, Y", strtotime($order['delivered_at'])); ?></p>
    <?php endif; ?>

    <a href="my_orders.php" class="btn btn-outline" style="margin-top:20px;display:inline-block;">Back to My Orders</a>

</div>

<?php include("includes/footer.php"); ?>
