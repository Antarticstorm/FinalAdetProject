<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Literary Nook</title>
    <link rel="stylesheet" href="<?php echo isset($basePath) ? $basePath : ''; ?>css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="topbar">
        <div class="container navwrap">
            <a href="<?php echo isset($basePath) ? $basePath : ''; ?>index.php" class="brand">
                The Literary Nook
            </a>
                <nav>

                    <?php if (isset($_SESSION['user_id'])): ?>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "admin"): ?>

                            <a href="<?php echo (isset($basePath) ? $basePath : ''); ?>admin/dashboard.php">
                                Admin Panel
                            </a>

                        <?php endif; ?>

                        <a href="<?php echo (isset($basePath) ? $basePath : ''); ?>profile.php">
                            Profile
                        </a>

                        <a href="<?php echo (isset($basePath) ? $basePath : ''); ?>logout.php" class="btn btn-outline">
                            Logout
                        </a>

                    <?php else: ?>

                        <a href="<?php echo (isset($basePath) ? $basePath : ''); ?>login.php">
                            Login
                        </a>

                        <a href="<?php echo (isset($basePath) ? $basePath : ''); ?>register.php" class="btn btn-primary">
                            Register
                        </a>

                    <?php endif; ?>

                </nav>
        </div>
    </header>
    <main class="container page-content">