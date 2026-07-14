<?php
session_start();

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/admin_helpers.php");
$stats = getDashboardStats($conn);
if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
    exit();
}

if ($_SESSION["role"] !== "admin") {
    redirect("index.php");
    exit();
}

require_once(ROOT_PATH . "/includes/header.php");

/* ==========================================
   INVENTORY REPORT
========================================== */

$totalBooks = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM books
"))["total"] ?? 0;

$inStock = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM books
    WHERE stock > 5
"))["total"] ?? 0;

$lowStock = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM books
    WHERE stock BETWEEN 1 AND 5
"))["total"] ?? 0;

$outStock = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM books
    WHERE stock = 0
"))["total"] ?? 0;

$lowStockBooks = mysqli_query($conn, "
    SELECT title, author, stock
    FROM books
    WHERE stock <= 5
    ORDER BY stock ASC
");

$totalCustomers = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM customers
"))["total"] ?? 0;

$totalWishlist = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM wishlist
"))["total"] ?? 0;

/* ==========================================
   SALES REPORT
========================================== */

$totalRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM orders
    WHERE status = 'delivered'
"))["total"] ?? 0;

$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status = 'delivered'
"))["total"] ?? 0;

$averageOrder = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(AVG(total_amount),0) AS total
    FROM orders
    WHERE status = 'delivered'
"))["total"] ?? 0;

$pendingRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM orders
    WHERE status IN ('pending','confirmed','shipped')
"))["total"] ?? 0;

$deliveredRevenue = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM orders
    WHERE status = 'delivered'
"))["total"] ?? 0;

$grossSales = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(total_amount),0) AS total
    FROM orders
    WHERE status != 'cancelled'
"))["total"] ?? 0;

$topSelling = mysqli_query($conn, "
    SELECT
        title,
        SUM(quantity) AS sold,
        SUM(line_total) AS revenue
    FROM order_items
    GROUP BY title
    ORDER BY sold DESC
    LIMIT 10
");

/* ==========================================
   FINANCIAL REPORT
========================================== */

$totalDiscounts = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COALESCE(SUM(discount_amount),0) AS total
    FROM orders
"))["total"] ?? 0;


$netRevenue = $totalRevenue - $totalDiscounts;
?>

<div class="admin-wide-container">
    <button type="button" onclick="history.back()" class="back-btn">
    ← Back
    </button>
    <h1>Reports</h1>

    <section class="report-section">
        <h2>📚 Inventory Status</h2>

        <div class="report-grid">
            <div class="report-card">
                <h3>Total Books</h3>
                <h2><?= (int)$totalBooks ?></h2>
            </div>

            <div class="report-card">
                <h3>In Stock</h3>
                <h2><?= (int)$inStock ?></h2>
            </div>

            <div class="report-card">
                <h3>Low Stock</h3>
                <h2><?= (int)$lowStock ?></h2>
            </div>

            <div class="report-card">
                <h3>Out of Stock</h3>
                <h2><?= (int)$outStock ?></h2>
            </div>
        </div>

        <div class="report-widget">
            <h3>Books Requiring Attention</h3>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($lowStockBooks && mysqli_num_rows($lowStockBooks) > 0): ?>
                        <?php while ($book = mysqli_fetch_assoc($lowStockBooks)): ?>
                            <tr>
                                <td><?= htmlspecialchars($book["title"]) ?></td>
                                <td><?= htmlspecialchars($book["author"]) ?></td>
                                <td>
                                    <span class="low-stock-count">
                                        <?= (int)$book["stock"] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No low-stock books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="report-section">
        <h2>💰 Sales Statistics</h2>

            <div class="report-grid">

                <div class="report-card">
                    <h3>Total Revenue</h3>
                    <h2>₱<?= number_format((float)$stats["revenue"], 2) ?></h2>
                </div>

                <div class="report-card">
                    <h3>Orders</h3>
                    <h2><?= (int)$stats["orders"] ?></h2>
                </div>

            </div>

        <div class="report-widget">
            <h3>🏆 Top Selling Books</h3>

            <table class="report-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($topSelling && mysqli_num_rows($topSelling) > 0): ?>
                        <?php while ($book = mysqli_fetch_assoc($topSelling)): ?>
                            <tr>
                                <td><?= htmlspecialchars($book["title"]) ?></td>
                                <td><?= (int)$book["sold"] ?></td>
                                <td>₱<?= number_format((float)$book["revenue"], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No sales data found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    
</div>


<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>