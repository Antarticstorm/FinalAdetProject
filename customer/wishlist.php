<?php
require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}

$customer_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT w.id AS wishlist_id, b.id AS book_id, b.title, b.author, b.price, b.cover
    FROM wishlist w
    INNER JOIN books b ON w.book_id = b.id
    WHERE w.customer_id = ?
    ORDER BY w.created_at DESC
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card">
    <h1>Your Wishlist</h1>

    <?php if ($result->num_rows == 0): ?>
        <div class="empty-state">

            <h2>

                ❤️ Your Wishlist is Empty

            </h2>

            <p>

                Browse books and save your favorites.

            </p>

            <a
                href="<?= url('orders/shop.php') ?>"
                class="btn btn-primary">

                Browse Books

            </a>

        </div>
    <?php else: ?>
        <div class="book-grid-shop">
            

        <?php while ($book = $result->fetch_assoc()): ?>

            <div class="shop-book-card">

                <a href="<?= url('orders/book.php?id=' . $book['book_id']) ?>">

                    <img
                        src="<?= url($book["cover"]) ?>"
                        class="shop-book-cover"
                        alt="<?= htmlspecialchars($book["title"]) ?>">

                </a>

                <div class="book-body">

                    <h3><?= htmlspecialchars($book["title"]) ?></h3>

                    <p class="book-author">

                        <?= htmlspecialchars($book["author"]) ?>

                    </p>

                    <div class="price-row">

                        <span class="price-now">

                            ₱<?= number_format($book["price"],2) ?>

                        </span>

                    </div>

                    <div class="book-actions">

                        <a
                            href="<?= url('orders/book.php?id='.$book["book_id"]) ?>"
                            class="btn btn-outline">

                            View

                        </a>

                        <a
                            href="<?= url('customer/wishlist_toggle.php?book_id='.$book["book_id"]) ?>"
                            class="btn btn-danger">

                            Remove

                        </a>

                    </div>

                </div>

            </div>

        <?php endwhile; ?>

        </div>
    <?php endif; ?>
</div>

<?php
$stmt->close();
require_once(ROOT_PATH . "/includes/footer.php");