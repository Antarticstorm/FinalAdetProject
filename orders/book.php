<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/order_helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

$stmt = $conn->prepare("
    SELECT *
    FROM books
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    redirect("orders/shop.php");
}

$book = $result->fetch_assoc();

$unitPrice = getEffectivePrice(
    $book["price"],
    $book["discount_percent"]
);

$hasDiscount = $book["discount_percent"] > 0;

/* Related Books */

$related = $conn->prepare("
    SELECT *
    FROM books
    WHERE genre = ?
    AND id != ?
    LIMIT 4
");

$related->bind_param(
    "si",
    $book["genre"],
    $book["id"]
);

$related->execute();

$relatedBooks = $related->get_result();

/* ==========================================
   LOAD USER'S WISHLIST
========================================== */

$wishlistIds = [];

if (isset($_SESSION["user_id"])) {

    $wish = $conn->prepare("
        SELECT book_id
        FROM wishlist
        WHERE customer_id = ?
    ");

    $wish->bind_param(
        "i",
        $_SESSION["user_id"]
    );

    $wish->execute();

    $wishResult = $wish->get_result();

    while ($row = $wishResult->fetch_assoc()) {

        $wishlistIds[$row["book_id"]] = true;

    }

    $wish->close();
}
?>

<div class="container-wide">

<nav class="breadcrumb">

    <a href="<?= url('index.php') ?>">Home</a>

    <span>›</span>

    <a href="<?= url('orders/shop.php') ?>">Shop</a>

    <span>›</span>

    <a href="<?= url('orders/shop.php?genre=' . urlencode($book["genre"])) ?>">
        <?= htmlspecialchars($book["genre"]) ?>
    </a>

    <span>›</span>

    <span><?= htmlspecialchars($book["title"]) ?></span>

</nav>

    <section class="book-details">

        <div class="book-left">

            <img
                src="<?= url($book["cover"]) ?>"
                class="details-cover"
                alt="<?= htmlspecialchars($book["title"]) ?>">

        </div>

        <div class="book-right">

            <span class="book-format">
                <?= htmlspecialchars($book["format"]) ?>
            </span>

            <h1>
                <?= htmlspecialchars($book["title"]) ?>
            </h1>

            <p class="details-author">
                by <?= htmlspecialchars($book["author"]) ?>
            </p>

            <div class="details-price">

                <?php if($hasDiscount): ?>

                    <span class="price-strike">
                        ₱<?= number_format($book["price"],2) ?>
                    </span>

                <?php endif; ?>

                <span class="price-now">
                    ₱<?= number_format($unitPrice,2) ?>
                </span>

            </div>

            <?php if($hasDiscount): ?>

                <span class="badge-discount">
                    <?= (int)$book["discount_percent"] ?>% OFF
                </span>

            <?php endif; ?>

            <div class="details-meta">

                <p><strong>Genre</strong> <?= htmlspecialchars($book["genre"]) ?></p>

                <p><strong>Publisher</strong> <?= htmlspecialchars($book["publisher"]) ?></p>

                <p><strong>Publication Year</strong> <?= htmlspecialchars($book["publication_year"]) ?></p>

                <p><strong>ISBN</strong> <?= htmlspecialchars($book["isbn"]) ?></p>

            </div>

            <div class="details-description">

                <div class="section-heading">

                    <span class="section-line"></span>

                    <h2>About this Book</h2>

                </div>

                <p>

                    <?= nl2br(htmlspecialchars($book["description"])) ?>

                </p>

            </div>

            <?php if($book["stock"] > 0): ?>

                <form
                    action="cart_action.php"
                    method="POST"
                    class="details-cart">

                    <input
                        type="hidden"
                        name="action"
                        value="add">

                    <input
                        type="hidden"
                        name="book_id"
                        value="<?= $book["id"] ?>">

                    <input
                        type="number"
                        name="quantity"
                        value="1"
                        min="1"
                        max="<?= $book["stock"] ?>"
                        class="qty-input">

                    <button class="btn btn-primary">

                        Add to Cart

                    </button>

                    <a
                                href="<?= url('customer/wishlist_toggle.php?book_id=' . $book["id"]) ?>"
                                class="btn btn-outline">

                            <?php if (isset($wishlistIds[$book["id"]])): ?>

                            Saved!

                        <?php else: ?>

                           Wishlist? 

                        <?php endif; ?>

                    </a>

                </form>

            <?php else: ?>

                <span class="out-stock">

                    Out of Stock

                </span>

            <?php endif; ?>

        </div>

    </section>

    <section class="related-books">

        <h2>More Like This</h2>

        <p class="related-subtitle">

        Books in the <strong><?= htmlspecialchars($book["genre"]) ?></strong> genre.

        </p>

        <div class="book-grid">

            <?php while($relatedBook = $relatedBooks->fetch_assoc()): ?>

                <div class="book-card">

                    <img
                        src="<?= url($relatedBook["cover"]) ?>"
                        alt="<?= htmlspecialchars($relatedBook["title"]) ?>">

                    <div class="book-info">

                        <h3>
                            <?= htmlspecialchars($relatedBook["title"]) ?>
                        </h3>

                        <p class="book-author">
                            <?= htmlspecialchars($relatedBook["author"]) ?>
                        </p>

                        <a
                            href="book.php?id=<?= $relatedBook["id"] ?>"
                            class="btn btn-primary">

                            View Book

                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

            </div>

            </section>

            <!-- NOW it goes here -->

            <section class="recently-viewed">

                <h2>Recently Viewed</h2>

                <div class="book-grid">

                    <div class="empty-state">

                        Your recently viewed books will appear here.

                    </div>

                </div>

            </section>

        </div>

    </section>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>