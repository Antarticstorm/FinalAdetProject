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
    $dbname = "if0_42378459_bookstore_db";

}

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>