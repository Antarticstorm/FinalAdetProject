<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
$featuredBooks = mysqli_query($conn, "
    SELECT *
    FROM books
    ORDER BY created_at DESC
    LIMIT 4
");

$genreQuery = mysqli_query($conn, "
    SELECT DISTINCT genre
    FROM books
    ORDER BY genre
");
$totalBooks = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM books
    ")
);

$totalGenres = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(DISTINCT genre) AS total
        FROM books
    ")
);

$totalHardcover = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM books
        WHERE format='Hardcover'
    ")
);

$totalEbook = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM books
        WHERE format='E-Book'
    ")
);

?>

<!-- HERO -->
<section class="hero reveal">

    <div class="hero-content">

        <p class="eyebrow">
            THE LITERARY NOOK
        </p>

        <h1>
            Reading<br>Matters.
        </h1>

        <p>
            Discover timeless classics, modern bestsellers,
            and stories that stay with you long after the final page.
            Welcome to your next great read.
        </p>

        <div class="hero-buttons">

            <a href="#featured" class="btn btn-primary">
                Browse Collection
            </a>

            <?php if(!isset($_SESSION["user_id"])): ?>

                <a href="<?= url("auth/register.php") ?>" class="btn btn-outline">
                    Create Account
                </a>

            <?php else: ?>

                <?php if($_SESSION["role"]=="admin"): ?>

                    <a href="<?= url("admin/dashboard.php") ?>" class="btn btn-outline">
                        Dashboard
                    </a>

                <?php else: ?>

                    <a href="<?= url("customer/profile.php") ?>" class="btn btn-outline">
                        My Library
                    </a>

                <?php endif; ?>

            <?php endif; ?>
        </div>

    </div>

    <div class="scroll-indicator">

        ↓

        <br>

        <small>SCROLL</small>

    </div>

</section>



    <div class="container">

        <p class="section-tag">
            FEATURED COLLECTION
        </p>

        <h2 class="section-title">
            Discover Our Latest Books
        </h2>

        <div class="book-grid">

            <?php while($book = mysqli_fetch_assoc($featuredBooks)): ?>

                <div class="book-card">

                    <img
                        src="<?= url($book['cover']) ?>"
                        alt="<?= htmlspecialchars($book['title']) ?>"
                    >

                    <div class="book-info">

                        <span class="book-format">
                            <?= htmlspecialchars($book['format']) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($book['title']) ?>
                        </h3>

                        <p class="book-author">
                            <?= htmlspecialchars($book['author']) ?>
                        </p>

                        <p class="book-price">
                            ₱<?= number_format($book['price'], 2) ?>
                        </p>

                        <div class="book-buttons">

                            <a href="<?= url("book.php?id=".$book["id"]) ?>">
                                View Details
                            </a>

                            <a
                            href="<?= url("wishlist_toggle.php?book_id=".$book["id"]) ?>"
                            class="btn btn-outline">

                            Wishlist

                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</section>

<div class="section-divider"></div>
<section class="stats-section reveal">

    <div class="container">

        <p class="section-tag">
            OUR COLLECTION
        </p>

        <h2 class="section-title">
            The Literary Nook In Numbers
        </h2>

        <div class="stats-grid">

            <div class="stat-card">

                <h2>
                    <?= $totalBooks['total'] ?>
                </h2>

                <p>Books Available</p>

            </div>

            <div class="stat-card">

                <h2>
                    <?= $totalGenres['total'] ?>
                </h2>

                <p>Genres</p>

            </div>

            <div class="stat-card">

                <h2>
                    <?= $totalHardcover['total'] ?>
                </h2>

                <p>Hardcover Books</p>

            </div>

            <div class="stat-card">

                <h2>
                    <?= $totalEbook['total'] ?>
                </h2>

                <p>E-Books</p>

            </div>

        </div>

    </div>


</section>

    <!-- ===========================================
     GENRES
=========================================== -->

<section class="genres-section reveal">
    
    <div class="container">

        <p class="section-tag">
            EXPLORE BY CATEGORY
        </p>

        <h2 class="section-title">
            Browse Your Favorite Genres
        </h2>

        <div class="genre-grid">

            <?php while($genre = mysqli_fetch_assoc($genreQuery)): ?>

                <div class="genre-card">

                    <?php
                        $icon = "📚";

                        switch(strtolower(trim($genre['genre']))){

                            case "fantasy":
                                $icon = "🐉";
                                break;

                            case "mystery":
                                $icon = "🕵️";
                                break;

                            case "science fiction":
                                $icon = "🚀";
                                break;

                            case "romance":
                                $icon = "❤️";
                                break;

                            case "history":
                                $icon = "🏛️";
                                break;

                            case "programming":
                                $icon = "💻";
                                break;

                            case "business":
                                $icon = "💼";
                                break;

                            case "horror":
                                $icon = "👻";
                                break;
                        }
                    ?>

                    <div class="genre-icon">

                        <?= $icon ?>

                    </div>

                    <h3>

                        <?= htmlspecialchars($genre['genre']) ?>

                    </h3>

                </div>

            <?php endwhile; ?>

        </div>

    </div>

</section>
<!-- ===========================================
     WHY CHOOSE US
=========================================== -->

<section class="why-section reveal">

    <div class="container">

        <p class="section-tag">
            WHY CHOOSE US
        </p>

        <h2 class="section-title">
            More Than Just A Bookstore
        </h2>

        <div class="why-grid">

            <div class="why-card">
                <div class="why-icon">📚</div>
                <h3>Curated Collection</h3>
                <p>
                    Browse carefully selected titles ranging from timeless classics to modern bestsellers.
                </p>
            </div>

            <div class="why-card">
                <div class="why-icon">📖</div>
                <h3>Physical & Digital Books</h3>
                <p>
                    Discover hardcover, paperback, and digital editions all in one convenient place.
                </p>
            </div>

            <div class="why-card">
                <div class="why-icon">🔒</div>
                <h3>Secure Ordering</h3>
                <p>
                    Shop confidently with a secure and reliable ordering experience.
                </p>
            </div>

            <div class="why-card">
                <div class="why-icon">❤️</div>
                <h3>Personalized Wishlist</h3>
                <p>
                    Save your favorite books and easily return to them whenever you're ready.
                </p>
            </div>

        </div>

    </div>

</section>
<!-- ===========================================
     LIVE STATISTICS
=========================================== -->


<!-- ===========================================
     READER REVIEWS
=========================================== -->

<section class="reviews-section reveal">

    <div class="container">

        <p class="section-tag">
            READER REVIEWS
        </p>

        <h2 class="section-title">
            What Our Readers Say
        </h2>

        <div class="reviews-grid">

            <div class="review-card">

                <div class="stars">★★★★★</div>

                <p>
                    "The Literary Nook made discovering books effortless.
                    I found titles I couldn't find anywhere else."
                </p>

                <h4>— Maria Santos</h4>

            </div>

            <div class="review-card">

                <div class="stars">★★★★★</div>

                <p>
                    "The clean interface and organized categories make
                    browsing enjoyable."
                </p>

                <h4>— John Cruz</h4>

            </div>

            <div class="review-card">

                <div class="stars">★★★★★</div>

                <p>
                    "My wishlist keeps growing every week.
                    It's my favorite feature."
                </p>

                <h4>— Angela Reyes</h4>

            </div>

        </div>

    </div>


<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>