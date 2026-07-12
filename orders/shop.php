<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
require_once(ROOT_PATH . "/includes/order_helpers.php");

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

if ($search !== "") {
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY id DESC");
    $like = "%" . $search . "%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
}
?>

<div class="card">

    <h1>Shop Books</h1>

    <form method="GET" action="" style="margin-top:16px;">
        <div class="inline-form" style="width:100%;">
            <input type="text" name="search" placeholder="Search by title or author"
                   value="<?php echo htmlspecialchars($search); ?>" style="flex:1;">
            <button class="btn btn-primary" type="submit">Search</button>
            <?php if ($search !== ""): ?>
                <a href="shop.php" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['cart_message']); unset($_SESSION['cart_message']); ?></div>
    <?php endif; ?>

    <div class="book-grid">

        <?php if ($result->num_rows === 0): ?>
            <p>No books found.</p>
        <?php endif; ?>

        <?php while ($book = $result->fetch_assoc()):
            $unitPrice = getEffectivePrice($book['price'], $book['discount_percent']);
            $hasDiscount = $book['discount_percent'] > 0;
        ?>
            <div class="book-tile">
                <img src="<?php echo htmlspecialchars($book['cover']); ?>" class="book-cover" alt="cover">

                <?php if ($hasDiscount): ?>
                    <span class="badge-discount"><?php echo (int) $book['discount_percent']; ?>% OFF</span>
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p style="font-size:0.85rem;">by <?php echo htmlspecialchars($book['author']); ?></p>

                <div class="price-row">
                    <?php if ($hasDiscount): ?>
                        <span class="price-strike">₱<?php echo number_format($book['price'], 2); ?></span>
                    <?php endif; ?>
                    <span class="price-now">₱<?php echo number_format($unitPrice, 2); ?></span>
                </div>

                <?php if ($book['stock'] == 0): ?>
                    <span class="out-stock">Out of Stock</span>
                <?php else: ?>
                    <p style="font-size:0.8rem;"><?php echo (int) $book['stock']; ?> in stock</p>

                    <form action="cart_action.php" method="POST" class="inline-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo (int) $book['stock']; ?>" class="qty-input">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>

    </div>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>
