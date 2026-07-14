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
   INVENTORY REPORT
========================================== */

$totalBooks = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total FROM books"
    )
)["total"];

$inStock = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM books
         WHERE stock > 5"
    )
)["total"];

$lowStock = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM books
         WHERE stock BETWEEN 1 AND 5"
    )
)["total"];

$outStock = mysqli_fetch_assoc(
    mysqli_query($conn,
        "SELECT COUNT(*) AS total
         FROM books
         WHERE stock = 0"
    )
)["total"];

$lowStockBooks = mysqli_query(
    $conn,
    "
    SELECT
        title,
        author,
        stock
    FROM books
    WHERE stock <= 5
    ORDER BY stock ASC
    "
);

$totalOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) total
        FROM orders
        "
    )
)["total"];

$averageOrder = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
        COALESCE(AVG(total_amount),0) total
        FROM orders
        "
    )
)["total"];

$totalCustomers = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) total
        FROM customers
        "
    )
)["total"];

$totalWishlist = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) total
        FROM wishlist
        "
    )
)["total"];
$totalDiscounts = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
        COALESCE(SUM(discount_amount),0) total
        FROM orders
        "
    )
)["total"];

/* ==========================================
   SALES REPORT
========================================== */

// Revenue from delivered orders
$totalRevenue = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
            COALESCE(SUM(total_amount),0) AS total
        FROM orders
        WHERE status='delivered'
        "
    )
)["total"];

// Average order value
$averageOrder = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
            COALESCE(AVG(total_amount),0) AS total
        FROM orders
        WHERE status='delivered'
        "
    )
)["total"];

// Total delivered orders
$totalOrders = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM orders
        WHERE status='delivered'
        "
    )
)["total"];

// Top Selling Books
$topSelling = mysqli_query(
    $conn,
    "
    SELECT
        title,
        SUM(quantity) AS sold,
        SUM(line_total) AS revenue
    FROM order_items
    GROUP BY title
    ORDER BY sold DESC
    LIMIT 10
    "
);
/* ==========================================
   FINANCIAL REPORT
========================================== */

$totalDiscounts = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT
            COALESCE(SUM(discount_amount),0) AS total
        FROM orders
        "
    )
)["total"];

$netRevenue = $totalRevenue - $totalDiscounts;
?>


<div class="admin-wide-container">

<h1>

Reports

</h1>

<section class="report-section">

    <h2>

        📚 Inventory Status

    </h2>

    <div class="report-grid">

        <div class="report-card">

            <h3>Total Books</h3>

            <h2><?= $totalBooks ?></h2>

        </div>

        <div class="report-card">

            <h3>In Stock</h3>

            <h2><?= $inStock ?></h2>

        </div>

        <div class="report-card">

            <h3>Low Stock</h3>

            <h2><?= $lowStock ?></h2>

        </div>

        <div class="report-card">

            <h3>Out of Stock</h3>

            <h2><?= $outStock ?></h2>

        </div>

    </div>

    <div class="report-widget">

        <h3>

            Books Requiring Attention

        </h3>

        <table class="report-table">

            <thead>

                <tr>

                    <th>Title</th>

                    <th>Author</th>

                    <th>Stock</th>

                </tr>

            </thead>

            <tbody>

            <?php while($book = mysqli_fetch_assoc($lowStockBooks)): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars($book["title"]) ?>

                    </td>

                    <td>

                        <?= htmlspecialchars($book["author"]) ?>

                    </td>

                    <td>

                        <span class="low-stock-count">

                            <?= $book["stock"] ?>

                        </span>

                    </td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</section>

<section class="report-section">

    <h2>💰 Sales Statistics</h2>

    <div class="report-grid">

        <div class="report-card">
            <h3>Total Revenue</h3>
            <h2>₱<?= number_format($totalRevenue,2) ?></h2>
        </div>

        <div class="report-card">
            <h3>Orders</h3>
            <h2><?= $totalOrders ?></h2>
        </div>

        <div class="report-card">
            <h3>Average Order</h3>
            <h2>₱<?= number_format($averageOrder,2) ?></h2>
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

            <?php while($book = mysqli_fetch_assoc($topSelling)): ?>

                <tr>

                    <td><?= htmlspecialchars($book["title"]) ?></td>

                    <td><?= $book["sold"] ?></td>

                    <td>₱<?= number_format($book["revenue"],2) ?></td>

                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>

    </div>

</section>

<section class="report-section">

    <h2>📈 Revenue Summary</h2>

    <div class="report-grid">

        <div class="report-card">

            <h3>Gross Revenue</h3>

            <h2>₱<?= number_format($totalRevenue,2) ?></h2>

        </div>

        <div class="report-card">

            <h3>Discounts Given</h3>

            <h2>₱<?= number_format($totalDiscounts,2) ?></h2>

        </div>

        <div class="report-card">

            <h3>Net Revenue</h3>

            <h2>₱<?= number_format($netRevenue,2) ?></h2>

        </div>

    </div>

</section>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>