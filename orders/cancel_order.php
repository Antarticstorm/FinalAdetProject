<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: my_orders.php");
    exit();
}

$orderId = (int) $_POST['order_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($order && $order['status'] === 'pending') {

    mysqli_begin_transaction($conn);

    try {
        $stmt = $conn->prepare("SELECT book_id, quantity FROM order_items WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $items = $stmt->get_result();

        while ($item = $items->fetch_assoc()) {
            $stmt2 = $conn->prepare("UPDATE books SET stock = stock + ? WHERE id = ?");
            $stmt2->bind_param("ii", $item['quantity'], $item['book_id']);
            $stmt2->execute();
            $stmt2->close();
        }

        $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();

        mysqli_commit($conn);

    } catch (Exception $e) {
        mysqli_rollback($conn);
    }
}

header("Location: order_details.php?id=" . $orderId . "&cancelled=1");
exit();
