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

$stmt = $conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$orders = $stmt->get_result();
?>

<div class="card">

    <h1>My Orders</h1>

    <?php if ($orders->num_rows === 0): ?>
        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="shop.php" class="btn btn-primary" style="margin-top:14px;display:inline-block;">Browse Books</a>
        </div>
    <?php else: ?>

        <table>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tracking</th>
                <th>Action</th>
            </tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                    <td><?php echo date("M j, Y", strtotime($order['created_at'])); ?></td>
                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><span class="status-badge <?php echo statusBadgeClass($order['status']); ?>"><?php echo $order['status']; ?></span></td>
                    <td>
                        <?php if ($order['tracking_number']): ?>
                            <?php echo htmlspecialchars($order['carrier'] . ' - ' . $order['tracking_number']); ?>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-primary" href="order_details.php?id=<?php echo $order['id']; ?>">View</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    <?php endif; ?>

</div>

<?php include("includes/footer.php"); ?>
