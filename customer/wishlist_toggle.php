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
$book_id = (int)$_GET["book_id"];

/* Check if already in wishlist */

$check = $conn->prepare("
    SELECT id
    FROM wishlist
    WHERE customer_id = ?
    AND book_id = ?
");

$check->bind_param("ii", $customer_id, $book_id);
$check->execute();

$result = $check->get_result();

$saved = false;

if ($row = $result->fetch_assoc()) {

    $delete = $conn->prepare("
        DELETE
        FROM wishlist
        WHERE id = ?
    ");

    $delete->bind_param("i", $row["id"]);
    $delete->execute();
    $delete->close();

    $saved = false;

} else {

    $insert = $conn->prepare("
        INSERT INTO wishlist
        (
            customer_id,
            book_id
        )
        VALUES
        (?, ?)
    ");

    $insert->bind_param("ii", $customer_id, $book_id);
    $insert->execute();
    $insert->close();

    $saved = true;
}

$check->close();

/* AJAX Request */

$isAjax =
    isset($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
    strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";

if ($isAjax) {

    header("Content-Type: application/json");

    echo json_encode([

        "success" => true,

        "saved" => $saved,

        "message" => $saved
            ? "Added to wishlist."
            : "Removed from wishlist."

    ]);

    exit();
}

/* Normal Request */

if (isset($_SERVER["HTTP_REFERER"])) {

    header("Location: " . $_SERVER["HTTP_REFERER"]);

    exit();
}

redirect("index.php");