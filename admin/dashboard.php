<?php

session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit();
}

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

?>

<div class="card">

<h1>Admin Dashboard</h1>

<p>Welcome back,
<strong><?php echo $_SESSION["fullname"]; ?></strong>

</p>

<br>

<a href="books.php" class="btn btn-primary">
Manage Books
</a>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>