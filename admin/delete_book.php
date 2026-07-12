<?php
session_start();

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["role"] != "admin") {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $book_id = intval($_GET["id"]);

    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);

    if ($stmt->execute()) {
        $stmt->close();
        // Standard PHP Redirect
        header("Location: books.php?deleted=1");
        // JavaScript Backup Redirect if headers are being finicky
        echo "<script>window.location.href='books.php?deleted=1';</script>";
        exit();
    } else {
        die("Database Error: " . $conn->error);
    }
} else {
    header("Location: books.php");
    echo "<script>window.location.href='books.php';</script>";
    exit();
}
?>