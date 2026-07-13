<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
require_once(ROOT_PATH . "/includes/order_helpers.php");

/* ----------------------------
   FILTERS
---------------------------- */
$search = trim($_GET["search"] ?? "");
$genre  = trim($_GET["genre"] ?? "");
$year   = trim($_GET["year"] ?? "");
$isbn   = trim($_GET["isbn"] ?? "");
$sort   = trim($_GET["sort"] ?? "newest");

/* ----------------------------
   SORTING
---------------------------- */
$orderBy = "id DESC";
switch ($sort) {
    case "price_asc":
        $orderBy = "price ASC";
        break;
    case "price_desc":
        $orderBy = "price DESC";
        break;
    case "title_asc":
        $orderBy = "title ASC";
        break;
    case "newest":
    default:
        $orderBy = "id DESC";
        break;
}

/* ----------------------------
   GENRES FOR BROWSING
---------------------------- */
$genresResult = mysqli_query(
    $conn,
    "SELECT DISTINCT genre 
     FROM books 
     WHERE genre IS NOT NULL AND genre != '' 
     ORDER BY genre ASC"
);

/* ----------------------------
   BUILD DYNAMIC QUERY
---------------------------- */
$where = [];
$params = [];
$types = "";

if ($search !== "") {
    $where[] = "(title LIKE ? OR author LIKE ? OR isbn LIKE ? OR genre LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= "ssss";
}

if ($genre !== "") {
    $where[] = "genre = ?";
    $params[] = $genre;
    $types .= "s";
}

if ($year !== "") {
    $where[] = "publication_year = ?";
    $params[] = (int)$year;
    $types .= "i";
}

if ($isbn !== "") {
    $where[] = "isbn = ?";
    $params[] = $isbn;
    $types .= "s";
}

$sql = "SELECT * FROM books";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY " . $orderBy;

if (!empty($params)) {
    $stmt = $conn->prepare($sql);

    /* bind_param needs references */
    $bindNames = [];
    $bindNames[] = $types;
    for ($i = 0; $i < count($params); $i++) {
        $bindNames[] = &$params[$i];
    }

    call_user_func_array([$stmt, "bind_param"], $bindNames);

    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, $sql);
}
?>

<div class="shop-page">

    <!-- HERO -->

    <section class="shop-hero">

        <span class="shop-tag">
            THE LITERARY NOOK
        </span>

        <h1>
            Discover Your Next Read
        </h1>

        <p>
            Browse our growing collection of books across every genre.
            From timeless classics to modern bestsellers,
            your next favorite book is waiting.
        </p>

    </section>

    <!-- =======================================
         SEARCH
    ======================================== -->

    <section class="shop-search">

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search title, author or ISBN..."
            value="<?= htmlspecialchars($search) ?>">

        <button
            class="btn btn-primary">

            Search

        </button>

    </form>

    </section>

    <!-- =======================================
         GENRES
    ======================================== -->

    <section class="shop-genres">

        <a
            href="shop.php"
            class="genre-chip <?= $genre=='' ? 'active' : '' ?>">

            All

        </a>

        <?php
        mysqli_data_seek($genresResult,0);

        while($g=mysqli_fetch_assoc($genresResult)):
        ?>

            <a
                href="shop.php?genre=<?= urlencode($g["genre"]) ?>"
                class="genre-chip <?= $genre==$g["genre"] ? 'active' : '' ?>">

                <?= htmlspecialchars($g["genre"]) ?>

            </a>

        <?php endwhile; ?>

    </section>

    <!-- =======================================
         SHOP
    ======================================== -->

<section class="shop-layout">

    <!-- SIDEBAR -->

    <aside class="shop-sidebar">

        <h2>Filters</h2>

        <form method="GET">

            <input
                type="hidden"
                name="search"
                value="<?= htmlspecialchars($search) ?>">

            <label>Sort</label>

            <select name="sort">

                <option value="newest">Newest</option>

                <option value="price_asc">Price ↑</option>

                <option value="price_desc">Price ↓</option>

                <option value="title_asc">Title A-Z</option>

            </select>

            <label>Publication Year</label>

            <input
                type="number"
                name="year"
                value="<?= htmlspecialchars($year) ?>">

            <button
                class="btn btn-primary"
                style="width:100%;margin-top:25px;">

                Apply Filters

            </button>

        </form>

    </aside>

    <!-- RESULTS -->

    <main class="shop-results">

        <div class="results-header">

            <h2>

                <?= $result->num_rows ?>

                Books

            </h2>

            <span>

                Sorted by <?= ucfirst(str_replace("_"," ",$sort)) ?>

            </span>

        </div>

        <div class="book-grid-shop">

            <?php while ($book = $result->fetch_assoc()):
                $unitPrice = getEffectivePrice(
                    $book["price"],
                    $book["discount_percent"]
                );

                $hasDiscount = $book["discount_percent"] > 0;
            ?>

            <div class="shop-book-card">

                <a href="<?= url('orders/book.php?id=' . $book['id']) ?>" class="book-cover-link">

                    <img
                        src="<?= url($book["cover"]) ?>"
                        class="shop-book-cover"
                        alt="<?= htmlspecialchars($book["title"]) ?>">

                    <?php if ($hasDiscount): ?>

                        <span class="badge-discount">
                            <?= (int)$book["discount_percent"] ?>% OFF
                        </span>

                    <?php endif; ?>

                </a>

                <div class="book-body">

                    <span class="book-format">
                        <?= htmlspecialchars($book["format"]) ?>
                    </span>

                    <h3><?= htmlspecialchars($book["title"]) ?></h3>

                    <p class="book-author">
                        by <?= htmlspecialchars($book["author"]) ?>
                    </p>

                    <div class="price-row">

                        <?php if($hasDiscount): ?>

                            <span class="price-strike">
                                ₱<?= number_format($book["price"],2) ?>
                            </span>

                        <?php endif; ?>

                        <span class="price-now">
                            ₱<?= number_format($unitPrice,2) ?>
                        </span>

                    </div>

                    <p class="book-meta">

                        <?= htmlspecialchars($book["genre"]) ?>

                        <?php if(!empty($book["publication_year"])): ?>

                            • <?= htmlspecialchars($book["publication_year"]) ?>

                        <?php endif; ?>

                    </p>

                    <?php if($book["stock"] > 0): ?>

                        <p class="book-stock">

                            <?= $book["stock"] ?> in stock

                        </p>

                        <div class="book-actions">

                            <a
                                href="<?= url('orders/book.php?id='.$book["id"]) ?>"
                                class="btn btn-outline">

                                View Book

                            </a>

                            <form
                                action="cart_action.php"
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

                                <button
                                    class="btn btn-primary">

                                    Add to Cart

                                </button>

                            </form>

                        </div>

                    <?php else: ?>

                        <span class="out-stock">

                            Out of Stock

                        </span>

                    <?php endif; ?>

                </div>

            </div>

            <?php endwhile; ?>

            </div>

        </main>

    </section>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>