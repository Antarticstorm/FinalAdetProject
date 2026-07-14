<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");
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
$check = $conn->prepare("
    SELECT id
    FROM wishlist
    WHERE customer_id = ?
    AND book_id = ?
");

$check->bind_param("ii", $customer_id, $book_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {

    $delete = $conn->prepare("
        DELETE
        FROM wishlist
        WHERE customer_id = ?
        AND book_id = ?
    ");

    $delete->bind_param("ii", $customer_id, $book_id);
    $delete->execute();
    $delete->close();

} else {

    $insert = $conn->prepare("
        INSERT INTO wishlist (customer_id, book_id)
        VALUES (?, ?)
    ");

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