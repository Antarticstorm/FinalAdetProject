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
        <p>You have no books in your wishlist yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
                <th>Action</th>
            </tr>

            <?php while ($book = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <img src="<?= BASE_URL . htmlspecialchars($book["cover"]) ?>" class="book-cover" alt="Cover">
                    </td>
                    <td><?= htmlspecialchars($book["title"]) ?></td>
                    <td><?= htmlspecialchars($book["author"]) ?></td>
                    <td>₱<?= number_format($book["price"], 2) ?></td>
                    <td>
                        <a class="btn btn-danger" href="<?= url('wishlist_toggle.php?book_id=' . $book['book_id']) ?>">
                            Remove
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<?php
$stmt->close();
require_once(ROOT_PATH . "/includes/footer.php");