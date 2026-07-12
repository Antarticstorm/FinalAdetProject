<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isHome = basename($_SERVER['PHP_SELF']) === "index.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Literary Nook</title>

    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
</head>

<body>

<header class="topbar <?= $isHome ? 'home-nav' : '' ?>">

    <div class="container navwrap">

        <a href="<?= url('index.php') ?>" class="brand">
            The Literary Nook
        </a>

        <nav>

            <?php if($isHome): ?>

                <a href="#featured">Books</a>
                <a href="#genres">Genres</a>
                <a href="#about">About</a>
                <a href="#reviews">Reviews</a>

            <?php endif; ?>


            <?php if(isset($_SESSION['user_id'])): ?>

                <?php if(isset($_SESSION['role']) && $_SESSION['role']=="admin"): ?>

                    <a href="<?= url('admin/dashboard.php') ?>">
                        Dashboard
                    </a>

                <?php endif; ?>

                <a href="<?= url('customer/wishlist.php') ?>">
                    Wishlist
                </a>

                <a href="<?= url('customer/profile.php') ?>">
                    Profile
                </a>

                <a href="<?= url('auth/logout.php') ?>" class="btn btn-outline">
                    Logout
                </a>

            <?php else: ?>

                <a href="<?= url('auth/login.php') ?>">
                    Login
                </a>

                <a href="<?= url('auth/register.php') ?>" class="btn btn-primary">
                    Create Account
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>

<main class="<?= $isHome ? '' : 'container page-content' ?>">