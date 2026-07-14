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
$genre = trim($_GET["genre"] ?? "");
$format = trim($_GET["format"] ?? "");
$availability = trim($_GET["availability"] ?? "");
$year = trim($_GET["year"] ?? "");
$isbn = trim($_GET["isbn"] ?? "");
$minPrice = trim($_GET["min_price"] ?? "");
$maxPrice = trim($_GET["max_price"] ?? "");
$discountOnly = isset($_GET["discount_only"]) ? 1 : 0;
$sort = trim($_GET["sort"] ?? "newest");

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
if ($format !== "") {
    $where[] = "format = ?";
    $params[] = $format;
    $types .= "s";
}

if ($availability === "in_stock") {
    $where[] = "stock > 0";
} elseif ($availability === "low_stock") {
    $where[] = "stock BETWEEN 1 AND 5";
} elseif ($availability === "out_stock") {
    $where[] = "stock = 0";
}

if ($discountOnly) {
    $where[] = "discount_percent > 0";
}

if ($minPrice !== "") {
    $where[] = "price >= ?";
    $params[] = (float)$minPrice;
    $types .= "d";
}

if ($maxPrice !== "") {
    $where[] = "price <= ?";
    $params[] = (float)$maxPrice;
    $types .= "d";
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

<div class="shop-page">

    <section class="shop-hero">

        <h1>
            Discover Your Next Read
        </h1>

        <p>
            Browse our growing collection of books across every genre.
            From timeless classics to modern bestsellers,
            your next favorite book is waiting.
        </p>

    </section>


    <section class="shop-filters">

        <form method="GET" action="shop.php#shop-results" class="shop-filter-panel">
            <div class="filter-grid">
                <input
                    type="text"
                    name="search"
                    placeholder="Search title, author, ISBN..."
                    value="<?= htmlspecialchars($search) ?>">

                <select name="genre">
                    <option value="">All Genres</option>
                    <?php mysqli_data_seek($genresResult, 0); while ($g = mysqli_fetch_assoc($genresResult)): ?>
                        <option value="<?= htmlspecialchars($g["genre"]) ?>" <?= $genre === $g["genre"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($g["genre"]) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <select name="format">
                    <option value="">All Formats</option>
                    <option value="Hardcover" <?= $format === "Hardcover" ? "selected" : "" ?>>Hardcover</option>
                    <option value="Paperback" <?= $format === "Paperback" ? "selected" : "" ?>>Paperback</option>
                    <option value="E-Book" <?= $format === "E-Book" ? "selected" : "" ?>>E-Book</option>
                </select>

                <select name="availability">
                    <option value="">Any Stock</option>
                    <option value="in_stock" <?= $availability === "in_stock" ? "selected" : "" ?>>In stock</option>
                    <option value="low_stock" <?= $availability === "low_stock" ? "selected" : "" ?>>Low stock</option>
                    <option value="out_stock" <?= $availability === "out_stock" ? "selected" : "" ?>>Out of stock</option>
                </select>

                <input type="number" name="year" placeholder="Year" value="<?= htmlspecialchars($year) ?>">
                <input type="number" step="0.01" name="min_price" placeholder="Min price" value="<?= htmlspecialchars($minPrice) ?>">
                <input type="number" step="0.01" name="max_price" placeholder="Max price" value="<?= htmlspecialchars($maxPrice) ?>">

                <label class="check-filter">
                    <input type="checkbox" name="discount_only" value="1" <?= $discountOnly ? "checked" : "" ?>>
                    On sale only
                </label>

                <select name="sort">
                    <option value="newest" <?= $sort === "newest" ? "selected" : "" ?>>Newest</option>
                    <option value="price_asc" <?= $sort === "price_asc" ? "selected" : "" ?>>Price ↑</option>
                    <option value="price_desc" <?= $sort === "price_desc" ? "selected" : "" ?>>Price ↓</option>
                    <option value="title_asc" <?= $sort === "title_asc" ? "selected" : "" ?>>Title A-Z</option>
                </select>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary" type="submit">Apply Filters</button>
                <a href="shop.php" class="btn btn-outline">Clear</a>
            </div>
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

<section class="shop-results">

<section class="shop-layout" id="shop-results">

        <div class="results-title">

            <h2>

                <?= mysqli_num_rows($result) ?>

                Result<?= mysqli_num_rows($result) != 1 ? "s" : "" ?>

            </h2>

        </div>

        <form
            method="GET"
            class="shop-toolbar">

            <!-- Preserve current filters -->

            <input
                type="hidden"
                name="genre"
                value="<?= htmlspecialchars($genre) ?>">

            <input
                type="hidden"
                name="format"
                value="<?= htmlspecialchars($format) ?>">

            <input
                type="hidden"
                name="availability"
                value="<?= htmlspecialchars($availability) ?>">

            <input
                type="hidden"
                name="year"
                value="<?= htmlspecialchars($year) ?>">

            <input
                type="hidden"
                name="min_price"
                value="<?= htmlspecialchars($minPrice) ?>">

            <input
                type="hidden"
                name="max_price"
                value="<?= htmlspecialchars($maxPrice) ?>">

            <?php if($discountOnly): ?>

                <input
                    type="hidden"
                    name="discount_only"
                    value="1">

            <?php endif; ?>

            <input
                type="search"
                name="search"
                placeholder="Search books..."
                value="<?= htmlspecialchars($search) ?>">

            <select
                name="sort"
                onchange="this.form.submit()">

                <option value="newest" <?= $sort=="newest" ? "selected" : "" ?>>
                    Newest
                </option>

                <option value="price_asc" <?= $sort=="price_asc" ? "selected" : "" ?>>
                    Price ↑
                </option>

                <option value="price_desc" <?= $sort=="price_desc" ? "selected" : "" ?>>
                    Price ↓
                </option>

                <option value="title_asc" <?= $sort=="title_asc" ? "selected" : "" ?>>
                    Title A-Z
                </option>

            </select>

        </form>

    </div>
<!-- =======================================
SEARCH
======================================== -->

    <details class="shop-filters">

        <summary>

        ⚙ Advanced Filters

        </summary>

        <form method="GET" class="shop-filter-panel">
            <div class="filter-grid">

                <select name="format">
                    <option value="">All Formats</option>
                    <option value="Hardcover" <?= $format === "Hardcover" ? "selected" : "" ?>>Hardcover</option>
                    <option value="Paperback" <?= $format === "Paperback" ? "selected" : "" ?>>Paperback</option>
                    <option value="E-Book" <?= $format === "E-Book" ? "selected" : "" ?>>E-Book</option>
                </select>

                <select name="availability">
                    <option value="">Any Stock</option>
                    <option value="in_stock" <?= $availability === "in_stock" ? "selected" : "" ?>>In stock</option>
                    <option value="low_stock" <?= $availability === "low_stock" ? "selected" : "" ?>>Low stock</option>
                    <option value="out_stock" <?= $availability === "out_stock" ? "selected" : "" ?>>Out of stock</option>
                </select>

                <input
                    type="number"
                    name="year"
                    placeholder="Year">

                <input
                    type="number"
                    name="min_price"
                    placeholder="Min price">

                <input
                    type="number"
                    name="max_price"
                    placeholder="Max price">

                <label class="check-filter">

                    <input
                        type="checkbox"
                        name="discount_only"
                        value="1"
                        <?= $discountOnly ? "checked" : "" ?>>

                    On sale only

                </label>

                <button
                    class="btn btn-primary"
                    type="submit">

                    Apply

                </button>

                <a
                    href="shop.php"
                    class="btn btn-outline">

                    Clear

                </a>

            </div>

        </form>

    </details>
    

            <div class="book-grid-shop">

                <?php while ($book = $result->fetch_assoc()):

                    $unitPrice = getEffectivePrice(
                        $book["price"],
                        $book["discount_percent"]
                    );

                    $hasDiscount = $book["discount_percent"] > 0;

                    include(ROOT_PATH . "/includes/book_card.php");

                endwhile; ?>

            </div>

</section>

        <div id="toast" class="toast"></div>
        <script src="<?= asset('js/shop.js') ?>"></script>                

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>