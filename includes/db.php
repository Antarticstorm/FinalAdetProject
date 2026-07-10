<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    // Local XAMPP
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "bookstore_db";
} else {
    //InfinityFree
    $host = "sql207.infinityfree.com";
    $user = "if0_42378459";
    $pass = "oMpOgqNOQ0vWT";
    $db = "if0_42378459_bookstore_db";

}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>