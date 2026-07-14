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
   CUSTOMER ACTIVITY
========================================== */

$totalCustomers = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM customers"
    )
)["total"];

$totalWishlist = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total FROM wishlist"
    )
)["total"];

$topCustomers = mysqli_query(
    $conn,
    "
    SELECT
        customers.fullname,
        COUNT(orders.id) AS total_orders,
        COALESCE(SUM(orders.total_amount),0) AS spent
    FROM customers
    LEFT JOIN orders
        ON customers.id = orders.customer_id
    GROUP BY customers.id
    ORDER BY total_orders DESC
    LIMIT 10
    "
);

$mostWishlisted = mysqli_query(
    $conn,
    "
    SELECT
        books.title,
        COUNT(*) AS saves
    FROM wishlist
    INNER JOIN books
        ON wishlist.book_id = books.id
    GROUP BY books.id
    ORDER BY saves DESC
    LIMIT 10
    "
);

?>
<section class="report-section">
    <button type="button" onclick="history.back()" class="back-btn">
    ← Back
    </button>

    <h2>👥 Customer Activity</h2>

    <p>
        Understand customer engagement and purchasing behavior.
    </p>

    <div class="report-grid">

        <div class="report-card">
            <h3>Customers</h3>
            <h2><?= $totalCustomers ?></h2>
        </div>

        <div class="report-card">
            <h3>Wishlist Saves</h3>
            <h2><?= $totalWishlist ?></h2>
        </div>

    </div>

    <div class="dashboard-widgets">

        <div class="dashboard-widget">

            <h3>🏆 Top Customers</h3>

            <table>

                <tr>

                    <th>Name</th>
                    <th>Orders</th>
                    <th>Spent</th>

                </tr>

                <?php while($customer = mysqli_fetch_assoc($topCustomers)): ?>

                <tr>

                    <td><?= htmlspecialchars($customer["fullname"]) ?></td>

                    <td><?= $customer["total_orders"] ?></td>

                    <td>₱<?= number_format($customer["spent"],2) ?></td>

                </tr>

                <?php endwhile; ?>

            </table>

        </div>

        <div class="dashboard-widget">

            <h3>❤️ Most Wishlisted Books</h3>

            <table>

                <tr>

                    <th>Book</th>
                    <th>Saves</th>

                </tr>

                <?php while($wish = mysqli_fetch_assoc($mostWishlisted)): ?>

                <tr>

                    <td><?= htmlspecialchars($wish["title"]) ?></td>

                    <td><?= $wish["saves"] ?></td>

                </tr>

                <?php endwhile; ?>

            </table>

        </div>

    </div>

</section>