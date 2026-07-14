<div class="shop-book-card">

    <div class="book-cover-container">

        <a
            href="<?= url('orders/book.php?id=' . $book["id"]) ?>"
            class="book-cover-link">

            <img
                src="<?= url($book["cover"]) ?>"
                class="shop-book-cover"
                alt="<?= htmlspecialchars($book["title"]) ?>">

        </a>

        <?php if ($hasDiscount): ?>
            <span class="badge-discount">
                <?= (int)$book["discount_percent"] ?>% OFF
            </span>
        <?php endif; ?>

        <?php if ($book["stock"] == 0): ?>
            <span class="stock-badge out">Out of Stock</span>
        <?php elseif ($book["stock"] <= 5): ?>
            <span class="stock-badge low">Only <?= $book["stock"] ?> left</span>
        <?php else: ?>
            <span class="stock-badge in">In Stock</span>
        <?php endif; ?>

        <?php if (isset($_SESSION["user_id"])): ?>
            <a
                href="<?= url('customer/wishlist_toggle.php?book_id=' . $book["id"]) ?>"
                class="wishlist-heart <?= isset($wishlistIds[$book["id"]]) ? 'saved' : '' ?>"
                data-book-id="<?= $book["id"] ?>">

                <?= isset($wishlistIds[$book["id"]]) ? '❤️' : '♡' ?>

            </a>
        <?php endif; ?>

    </div>

    <div class="book-body">

        <span class="book-format">
            <?= htmlspecialchars($book["format"]) ?>
        </span>

        <h3>
            <?= htmlspecialchars($book["title"]) ?>
        </h3>

        <p class="book-author">
            by <?= htmlspecialchars($book["author"]) ?>
        </p>

        <div class="book-rating">
            ★★★★★
            <span>No reviews yet</span>
        </div>

        <div class="price-row">

            <?php if ($hasDiscount): ?>

                <span class="price-strike">
                    ₱<?= number_format($book["price"], 2) ?>
                </span>

                <span class="saving">
                    Save ₱<?= number_format($book["price"] - $unitPrice, 2) ?>
                </span>

            <?php endif; ?>

            <span class="price-now">
                ₱<?= number_format($unitPrice, 2) ?>
            </span>

        </div>

        <p class="book-meta">
            <?= htmlspecialchars($book["genre"]) ?>
            <?php if (!empty($book["publication_year"])): ?>
                • <?= htmlspecialchars($book["publication_year"]) ?>
            <?php endif; ?>
        </p>

        <div class="book-actions">

            <a
                href="<?= url('orders/book.php?id=' . $book["id"]) ?>"
                class="btn btn-outline">

                View Book

            </a>

            <form
                action="<?= url('orders/cart_action.php') ?>"
                method="POST"
                class="book-cart-form">

                <input
                    type="hidden"
                    name="action"
                    value="add">

                <input
                    type="hidden"
                    name="book_id"
                    value="<?= $book["id"] ?>">

                <button class="btn btn-primary">
                    Add to Cart
                </button>

            </form>

        </div>

    </div>

</div>