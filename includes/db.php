<?php
$host = "sql207.infinityfree.com";
$user = "if0_42378459";
$pass = "oMpOgqNOQ0vWT";
$dbname = "if0_42378459_bookstore_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>