<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

?>

<div class="card">
    <h1>Hello Welcome to The Literary Nook! Axzel</h1>

    <?php if (isset($_SESSION["user_id"])): ?>
        <p>Hello, <strong><?php echo htmlspecialchars($_SESSION["fullname"]); ?></strong>. You are now logged in.</p>
        <p style="margin-top: 12px;">This is your starting dashboard for Phase 1.</p>
    <?php else: ?>
        <p>Log in or register to access your bookstore account.</p>
        <div style="margin-top: 18px;">
            <a href="<?= url("auth/login.php") ?>" class="btn btn-primary">Login</a>
            <a href="<?= url("auth/register.php") ?>" class="btn btn-outline">Register</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>