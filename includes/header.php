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

<link rel="stylesheet" href="<?= asset('css/base.css') ?>">
<link rel="stylesheet" href="<?= asset('css/layout.css') ?>">
<link rel="stylesheet" href="<?= asset('css/components.css') ?>">
<link rel="stylesheet" href="<?= asset('css/forms.css') ?>">
<link rel="stylesheet" href="<?= asset('css/books.css') ?>">
<link rel="stylesheet" href="<?= asset('css/profile.css') ?>">
<link rel="stylesheet" href="<?= asset('css/home.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<link rel="stylesheet" href="<?= asset('css/responsive.css') ?>">
<link rel="stylesheet" href="<?= asset('css/orders.css') ?>">


<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

</head>

<body>

<header class="topbar <?= $isHome ? 'home-nav' : '' ?>">

<div class="container navwrap">

    <a href="<?= url('index.php') ?>" class="logo">
        <img src="<?= asset('images/logo.png') ?>" class="logo-img">
    </a>

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

            <?php if(isset($_SESSION["user_id"])): ?>

                <div class="account-dropdown">

                    <button class="account-btn" id="accountBtn">
                        <?= explode(' ', $_SESSION["fullname"])[0] ?> ▼
                    </button>

                    <div class="dropdown-menu" id="accountMenu">

                        <?php if($_SESSION["role"]=="admin"): ?>

                            <a href="<?= url('admin/dashboard.php') ?>">
                                Dashboard
                            </a>

                        <?php endif; ?>

                        <a href="<?= url('customer/profile.php') ?>">
                             Profile
                        </a>

                        <a href="<?= url('orders/shop.php') ?>">
                            Shop
                        </a>

                        <a href="<?= url('customer/wishlist.php') ?>">
                            Wishlist
                        </a>
                        
                        <hr>
                    

                        <a href="<?= url('orders/cart.php') ?>">
                            Cart
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


<main class="<?= $isHome ? '' : 'container page-content' ?>">