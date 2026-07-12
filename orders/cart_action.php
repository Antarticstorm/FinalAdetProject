<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("includes/db.php");
include("includes/order_helpers.php");

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: shop.php");
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === "add") {

    if (!isset($_SESSION["user_id"])) {
        $_SESSION['cart_message'] = "Please log in to add items to your cart.";
        header("Location: login.php");
        exit();
    }

    $bookId = (int) $_POST['book_id'];
    $quantity = max(1, (int) $_POST['quantity']);

    $stmt = $conn->prepare("SELECT stock FROM books WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $book = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$book) {
        $_SESSION['cart_message'] = "That book could not be found.";
        header("Location: shop.php");
        exit();
    }

    $currentQty = $_SESSION['cart'][$bookId] ?? 0;
    $newQty = $currentQty + $quantity;

    if ($newQty > $book['stock']) {
        $newQty = $book['stock'];
        $_SESSION['cart_message'] = "Only " . $book['stock'] . " in stock — added the maximum available.";
    } else {
        $_SESSION['cart_message'] = "Added to cart.";
    }

    if ($newQty <= 0) {
        unset($_SESSION['cart'][$bookId]);
        $_SESSION['cart_message'] = "This book is out of stock.";
    } else {
        $_SESSION['cart'][$bookId] = $newQty;
    }

    header("Location: shop.php");
    exit();

} elseif ($action === "update") {

    $bookId = (int) $_POST['book_id'];
    $quantity = (int) $_POST['quantity'];

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$bookId]);
    } else {
        $stmt = $conn->prepare("SELECT stock FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($book) {
            $_SESSION['cart'][$bookId] = min($quantity, max(1, (int) $book['stock']));
        }
    }

    header("Location: cart.php");
    exit();

} elseif ($action === "remove") {

    $bookId = (int) $_POST['book_id'];
    unset($_SESSION['cart'][$bookId]);

    header("Location: cart.php");
    exit();

} elseif ($action === "apply_promo") {

    $code = strtoupper(trim($_POST['promo_code']));
    $promo = getActivePromo($conn, $code);

    if ($promo) {
        $_SESSION['promo_code'] = $promo['code'];
        $_SESSION['cart_notice'] = "Promo code applied: " . $promo['code'];
    } else {
        unset($_SESSION['promo_code']);
        $_SESSION['cart_notice'] = "That promo code is invalid or expired.";
    }

    header("Location: cart.php");
    exit();

} elseif ($action === "remove_promo") {

    unset($_SESSION['promo_code']);
    header("Location: cart.php");
    exit();

}

header("Location: cart.php");
exit();
