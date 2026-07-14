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
require_once(ROOT_PATH . "/includes/admin_helpers.php");

$stats = getDashboardStats($conn);

$stats = getDashboardStats($conn);

$recentOrders = getRecentOrders($conn);

$lowStockBooks = getLowStockBooks($conn);

$recentCustomers = getRecentCustomers($conn);

$bestSellingBooks = getBestSellingBooks($conn);

$recentOrders = getRecentOrders($conn);

$lowStockBooks = getLowStockBooks($conn);

$recentCustomers = getRecentCustomers($conn);

$bestSellingBooks = getBestSellingBooks($conn);


?>

<h1>Admin Dashboard</h1>

<p>

Welcome back,

<strong>

<?= htmlspecialchars($_SESSION["fullname"]) ?>

</strong>

</p>

<div class="report-grid">

    <div class="report-card">

        <h3>Books</h3>

        <h2><?= $stats["books"] ?></h2>

    </div>

    <div class="report-card">

        <h3>Customers</h3>

        <h2><?= $stats["customers"] ?></h2>

    </div>

    <div class="report-card">

        <h3>Orders</h3>

        <h2><?= $stats["orders"] ?></h2>

    </div>

    <div class="report-card">

        <h3>Revenue</h3>

        <h2>

            <h2><?= number_format($stats["revenue"],2) ?></h2>

        </h2>

    </div>

</div>
<div class="admin-actions-grid">

    <a
        href="books.php"
        class="admin-action-card">

        📚

        <span>

            Manage Books

        </span>

    </a>

    <a
        href="reports.php"
        class="admin-action-card">

        📊

        <span>

            Reports

        </span>

    </a>

    <a
        href="orders.php"
        class="admin-action-card">

        📦

        <span>

            Orders

        </span>

    </a>

    <a
        href="customers.php"
        class="admin-action-card">

        👥

        <span>

            Customers

        </span>

    </a>

</div>

<div class="dashboard-widgets">

    <!-- Recent Orders -->

    <div class="dashboard-widget">

        <h2>📦 Recent Orders</h2>

        <table>

            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total</th>
            </tr>

            <?php while($order = mysqli_fetch_assoc($recentOrders)): ?>

            <tr>

                <td><?= htmlspecialchars($order["order_number"]) ?></td>

                <td><?= htmlspecialchars($order["shipping_fullname"]) ?></td>

                <td>

                    <span class="status-badge status-<?= strtolower($order["status"]) ?>">

                        <?= ucfirst($order["status"]) ?>

                    </span>

                </td>

                <td>

                    ₱<?= number_format($order["total_amount"],2) ?>

                </td>

            </tr>

            <?php endwhile; ?>

        </table>

    </div>

<div class="dashboard-widget">

    <h2>📚 Low Stock Books</h2>

    <table>

        <tr>
            <th>Book</th>
            <th>Stock</th>
        </tr>

        <?php while($book = mysqli_fetch_assoc($lowStockBooks)): ?>

        <tr>

            <td>
                <?= htmlspecialchars($book["title"]) ?>
            </td>

            <td>
                <span class="low-stock-count">
                    <?= $book["stock"] ?>
                </span>
            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

<!-- Recent Customers -->

<div class="dashboard-widget">

    <h2>👥 New Customers</h2>

    <?php while($customer = mysqli_fetch_assoc($recentCustomers)): ?>

        <div class="widget-item">

            <span>
                <?= htmlspecialchars($customer["fullname"]) ?>
            </span>

            <small>
                <?= date("M d", strtotime($customer["created_at"])) ?>
            </small>

        </div>

    <?php endwhile; ?>

</div>

<!-- Best Sellers -->

<div class="dashboard-widget">

    <h2>🏆 Best Sellers</h2>

    <?php while($book = mysqli_fetch_assoc($bestSellingBooks)): ?>

        <div class="widget-item">

            <span>
                <?= htmlspecialchars($book["title"]) ?>
            </span>

            <strong>
                <?= $book["sold"] ?> sold
            </strong>

        </div>

    <?php endwhile; ?>

</div>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>