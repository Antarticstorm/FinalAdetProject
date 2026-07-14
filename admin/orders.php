<?php

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}

if ($_SESSION["role"] != "admin") {
    redirect("index.php");
}
/* ==========================================
   ORDERS REPORT
========================================== */

$totalOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM orders"
    )
)["total"];

$pendingOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM orders
         WHERE status='pending'"
    )
)["total"];

$shippedOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM orders
         WHERE status='shipped'"
    )
)["total"];

$deliveredOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM orders
         WHERE status='delivered'"
    )
)["total"];

$latestOrders = mysqli_query(
    $conn,
    "
    SELECT
        order_number,
        shipping_fullname,
        total_amount,
        status,
        created_at
    FROM orders
    ORDER BY created_at DESC
    LIMIT 10
    "
);
$newCustomers = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM customers
        WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
        AND YEAR(created_at) = YEAR(CURRENT_DATE())
        "
    )
)["total"];
$averageOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
            COALESCE(
                COUNT(*) /
                NULLIF(
                    COUNT(DISTINCT customer_id),
                    0
                ),
                0
            ) AS average_orders
        FROM orders
        "
    )
)["average_orders"];

?>
<div class="admin-wide-container">
    <section class="report-section">

    <h2>📦 Orders Report</h2>

    <p>
        Monitor current order activity and fulfillment.
    </p>

    <div class="report-grid">

        <div class="report-card">
            <h3>Total Orders</h3>
            <h2><?= $totalOrders ?></h2>
        </div>

        <div class="report-card">
            <h3>Pending</h3>
            <h2><?= $pendingOrders ?></h2>
        </div>

        <div class="report-card">
            <h3>Shipped</h3>
            <h2><?= $shippedOrders ?></h2>
        </div>

        <div class="report-card">
            <h3>Delivered</h3>
            <h2><?= $deliveredOrders ?></h2>
        </div>

        <div class="report-card">
            <h3>
                New This Month
            </h3>
            <h2>
                <?= $newCustomers ?>
            </h2>
        </div>

        <div class="report-card">
            <h3>
                Avg. Orders
            </h3>
            <h2>
                <?= number_format($averageOrders,1) ?>
            </h2>
        </div>

    </div>

    <div class="dashboard-widget">

        <h3>Latest Orders</h3>

        <table class="report-table">
            <thead>

                <tr>

                    <th>Order</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>

                </tr>

            </thead>

                <tbody>

                <?php while ($order = mysqli_fetch_assoc($latestOrders)): ?>

                <tr>

                    <td><?= htmlspecialchars($order["order_number"]) ?></td>

                    <td><?= htmlspecialchars($order["shipping_fullname"]) ?></td>

                    <td>

                        <?= date("M d, Y", strtotime($order["created_at"])) ?>

                    </td>

                    <td>

                        <span class="status-badge status-<?= strtolower($order["status"]) ?>">

                            <?= ucfirst($order["status"]) ?>

                        </span>

                    </td>

                    <td>

                        ₱<?= number_format($order["total_amount"], 2) ?>

                    </td>

                </tr>

                <?php endwhile; ?>

                </tbody>

        </table>

    </div>

    </section>

    <div class="widget-footer">

    <a
        href="orders.php"
        class="btn btn-outline">

        View All Orders

    </a>

</div>
</div>
<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>