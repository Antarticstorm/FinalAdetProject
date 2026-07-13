<?php
require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}

$user_id = $_SESSION["user_id"];

/* User data */
$stmt = $conn->prepare("
    SELECT fullname, email, phone, address, avatar, membership_status, created_at
    FROM customers
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

/* ==========================================
   ACCOUNT TIMELINE
========================================== */

$activityStmt = $conn->prepare("

    SELECT
        'wishlist' AS activity_type,
        books.id AS reference_id,
        books.title AS activity_title,
        wishlist.created_at AS activity_date
    FROM wishlist
    INNER JOIN books
        ON wishlist.book_id = books.id
    WHERE wishlist.customer_id = ?

    UNION ALL

    SELECT
        'order' AS activity_type,
        orders.id AS reference_id,
        orders.order_number AS activity_title,
        orders.created_at AS activity_date
    FROM orders
    WHERE orders.customer_id = ?

    ORDER BY activity_date DESC
    LIMIT 8

");

$activityStmt->bind_param(
    "ii",
    $user_id,
    $user_id
);

$activityStmt->execute();

$timeline = $activityStmt->get_result();

$activityStmt->close();


/* Wishlist count */
$wishlistCount = 0;
$wishlistStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM wishlist
    WHERE customer_id = ?
");
$wishlistStmt->bind_param("i", $user_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlist = $wishlistResult->fetch_assoc();
$wishlistCount = (int)$wishlist["total"];
$wishlistStmt->close();

/* Orders count */
$orderStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM orders
    WHERE customer_id = ?
");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$orderCount = (int)$orderResult->fetch_assoc()["total"];
$orderStmt->close();

/* Purchased count */
$purchasedStmt = $conn->prepare("
    SELECT COALESCE(SUM(order_items.quantity), 0) AS total
    FROM order_items
    INNER JOIN orders
        ON order_items.order_id = orders.id
    WHERE orders.customer_id = ?
    AND orders.status = 'delivered'
");
$purchasedStmt->bind_param("i", $user_id);
$purchasedStmt->execute();
$purchasedResult = $purchasedStmt->get_result();
$purchasedCount = (int)$purchasedResult->fetch_assoc()["total"];
$purchasedStmt->close();

/* Recent Orders */

$recentOrdersStmt = $conn->prepare("
    SELECT
        order_number,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE customer_id = ?
    ORDER BY created_at DESC
    LIMIT 3
");

$recentOrdersStmt->bind_param("i", $user_id);
$recentOrdersStmt->execute();

$recentOrders = $recentOrdersStmt->get_result();

$recentOrdersStmt->close();
?>

<div class="profile-page">

    
    <div class="card profile-card">

        <div class="profile-header">

            <img
                src="<?= BASE_URL . htmlspecialchars($user["avatar"]) ?>"
                class="profile-avatar"
                alt="Avatar">

            <div class="profile-title">

                <h1><?= htmlspecialchars($user["fullname"]) ?></h1>

                <div class="profile-header-actions">

                    <span class="membership-badge">
                        <?= htmlspecialchars($user["membership_status"]) ?> Member
                    </span>

                    <a href="<?= url('customer/edit_profile.php') ?>"
                       class="btn btn-primary profile-edit-btn">
                        Edit Profile
                    </a>

                </div>

            </div>

        </div> <!-- ✅ CLOSE profile-header HERE -->

        <hr class="profile-divider">

        <div class="profile-info">

            <div>
                <h4>Email</h4>
                <p><?= htmlspecialchars($user["email"]) ?></p>
            </div>

            <div>
                <h4>Phone</h4>
                <p><?= htmlspecialchars($user["phone"]) ?></p>
            </div>

            <div>
                <h4>Address</h4>
                <p><?= htmlspecialchars($user["address"]) ?></p>
            </div>

            <div>
                <h4>Member Since</h4>
                <p><?= date("F d, Y", strtotime($user["created_at"])) ?></p>
            </div>

        </div>

    </div>
    
    <div class="profile-stats">
        <a href="<?= url('customer/wishlist.php') ?>" class="stat-card">
            <div class="stat-number"><?= $wishlistCount ?></div>
            <div class="stat-label">Wishlist</div>
        </a>

        <a href="<?= url('orders/my_orders.php') ?>" class="stat-card">
            <div class="stat-number"><?= $orderCount ?></div>
            <div class="stat-label">Orders</div>
        </a>

        <a href="<?= url('orders/history.php') ?>" class="stat-card">
            <div class="stat-number"><?= $purchasedCount ?></div>
            <div class="stat-label">Purchased</div>
        </a>
    </div>

    <div class="card activity-card">

            <h2>Recent Activity</h2>

            <?php if($timeline->num_rows == 0): ?>

                <div class="empty-state">

                    <div class="empty-icon">

                        📚

                    </div>

                    <h3>

                        No Recent Activity

                    </h3>

                    <p>

                        Start exploring the bookstore.

                    </p>

                </div>

            <?php else: ?>

                <?php while($activity = $timeline->fetch_assoc()): ?>

                    <?php

                    switch($activity["activity_type"]){

                        case "wishlist":

                            $icon = "❤️";
                            $title = "Wishlist Updated";

                            $message =
                                "Added <span class='activity-book'>"
                                . htmlspecialchars($activity["activity_title"])
                                . "</span> to your wishlist.";

                            $link =
                                url("orders/book.php?id=" . $activity["reference_id"]);

                            break;

                        case "order":

                            $icon = "📦";
                            $title = "Order Placed";

                            $message =
                                "Placed order <strong>"
                                . htmlspecialchars($activity["activity_title"])
                                . "</strong>.";

                            $link =
                                url("orders/my_orders.php");

                            break;

                    }

                    ?>

                    <a
                        href="<?= $link ?>"
                        class="activity-item">

                        <div class="activity-icon">

                            <?= $icon ?>

                        </div>

                        <div class="activity-content">

                            <div class="activity-header">

                                <strong>

                                    <?= $title ?>

                                </strong>

                                <span class="activity-date">

                                    <?= timeAgo($activity["activity_date"]) ?>

                                </span>

                            </div>

                            <p>

                                <?= $message ?>

                            </p>

                        </div>

                    </a>

                <?php endwhile; ?>

            <?php endif; ?>

        <div class="card quick-actions">

            <h2>

                Quick Actions

            </h2>

            <div class="quick-grid">

                <a
                    href="<?= url('orders/shop.php') ?>"
                    class="quick-card">

                    📚

                    <span>

                        Browse Books

                    </span>

                </a>

                <a
                    href="<?= url('customer/wishlist.php') ?>"
                    class="quick-card">

                    ❤️

                    <span>

                        Wishlist

                    </span>

                </a>

                <a
                    href="<?= url('orders/history.php') ?>"
                    class="quick-card">

                    📦

                    <span>

                        Order History

                    </span>

                </a>

            </div>

        </div>

        <div class="card recent-orders">

            <h2>

                Recent Orders

            </h2>

            <?php if($recentOrders->num_rows == 0): ?>

                <p>

                    You haven't placed any orders yet.

                </p>

            <?php else: ?>

                <?php while($order = $recentOrders->fetch_assoc()): ?>

                    <div class="order-item">

                        <div>

                            <strong>

                                <?= htmlspecialchars($order["order_number"]) ?>

                            </strong>

                            <br>

                            <small>

                                <?= date("F d, Y",strtotime($order["created_at"])) ?>

                            </small>

                        </div>

                        <span class="status-badge status-<?= strtolower($order["status"]) ?>">

                            <?= ucfirst($order["status"]) ?>

                        </span>

                    </div>

                <?php endwhile; ?>

            <?php endif; ?>

    </div>


<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>