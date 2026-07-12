<?php
require_once("config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}

if (!isset($_GET["book_id"])) {
    redirect("index.php");
}

$customer_id = $_SESSION["user_id"];
$book_id = intval($_GET["book_id"]);

/* Check if already in wishlist */
$check = $conn->prepare("SELECT id FROM wishlist WHERE customer_id = ? AND book_id = ?");
$check->bind_param("ii", $customer_id, $book_id);
$check->execute();
$result = $check->get_result();

if ($row = $result->fetch_assoc()) {
    $delete = $conn->prepare("DELETE FROM wishlist WHERE id = ?");
    $delete->bind_param("i", $row["id"]);
    $delete->execute();
    $delete->close();
} else {
    $insert = $conn->prepare("INSERT INTO wishlist (customer_id, book_id) VALUES (?, ?)");
    $insert->bind_param("ii", $customer_id, $book_id);
    $insert->execute();
    $insert->close();
}

$check->close();

if (isset($_SERVER["HTTP_REFERER"])) {
    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit();
}

redirect("index.php");