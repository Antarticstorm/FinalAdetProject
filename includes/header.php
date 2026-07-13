<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isHome = basename($_SERVER['PHP_SELF']) === "index.php";
$isStore =
    in_array(
        basename($_SERVER['PHP_SELF']),
        [
            "shop.php",
            "book.php",
            "cart.php",
            "checkout.php",
            "orders.php"
        ]
    );
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>The Literary Nook</title>

<link rel="stylesheet" href="<?= asset('css/base.css') ?>">
<link rel="stylesheet" href="<?= asset('css/layout.css') ?>">
<link rel="stylesheet" href="<?= asset('css/components.css') ?>">
<link rel="stylesheet" href="<?= asset('css/forms.css') ?>">
<link rel="stylesheet" href="<?= asset('css/books.css') ?>">
<link rel="stylesheet" href="<?= asset('css/profile.css') ?>">
<link rel="stylesheet" href="<?= asset('css/home.css') ?>">
<link rel="stylesheet" href="<?= asset('css/animations.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
<link rel="stylesheet" href="<?= asset('css/orders.css') ?>">
<link rel="stylesheet" href="<?= asset('css/shop.css') ?>">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

</head>

<body>

<header class="topbar <?= $isHome ? 'home-nav' : '' ?>">

<div class="<?= $isStore ? 'container-wide navwrap' : 'container navwrap' ?>">

    <a href="<?= url('index.php') ?>" class="brand">
        The Literary Nook
    </a>

        <nav>
            <?php if(isset($_SESSION["user_id"])): ?>


                <a href="<?= url('orders/shop.php') ?>">
                    Books
                </a>
                <a href="<?= url('orders/cart.php') ?>">
                    Cart
                </a>
                <a href="<?= url('orders/shop.php') ?>">
                    Shop
                </a>

                <a href="<?= url('customer/profile.php') ?>" class="profile-link">
                    <?= htmlspecialchars(explode(' ', $_SESSION["fullname"])[0]) ?>
                </a>

                <div class="account-dropdown">

                    <button class="account-btn" id="accountBtn">
                        Menu ▼
                    </button>

                    <div class="dropdown-menu" id="accountMenu">

                        <?php if($_SESSION["role"]=="admin"): ?>

                            <a href="<?= url('admin/dashboard.php') ?>">
                                Dashboard
                            </a>

                        <hr>

                        <?php endif; ?>

                        <a href="<?= url('index.php') ?>">
                             Home
                        </a>

                        <a href="<?= url('customer/wishlist.php') ?>">
                            Wishlist
                        </a>
                    

                        <hr>

                        <a href="<?= url('auth/logout.php') ?>">
                            Logout
                        </a>

                    </div>

                </div>

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


<main class="<?=
    $isHome
        ? ''
        : ($isStore
            ? 'container-wide page-content'
            : 'container page-content')
?>">