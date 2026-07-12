<?php
session_start();

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit();
}

// 1. Validate that an ID has been passed via the URL string parameter
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: books.php");
    exit();
}

$book_id = mysqli_real_escape_string($conn, $_GET['id']);

// ==========================================
// NEW: DATABASE UPDATE ENGINE (POST METHOD)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $author      = mysqli_real_escape_string($conn, $_POST['author']);
    $isbn        = mysqli_real_escape_string($conn, $_POST['isbn']);
    $genre       = mysqli_real_escape_string($conn, $_POST['genre']);
    $year        = mysqli_real_escape_string($conn, $_POST['year']);
    $publisher   = mysqli_real_escape_string($conn, $_POST['publisher']);
    $format      = mysqli_real_escape_string($conn, $_POST['format']);
    $price       = mysqli_real_escape_string($conn, $_POST['price']);
    $discount = mysqli_real_escape_string($conn, $_POST['discount_percent']);
    $stock       = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Grab the existing data first to keep the current cover path if no new file is uploaded
    $current_query = mysqli_query($conn, "SELECT cover FROM books WHERE id = '$book_id' LIMIT 1");
    $current_book = mysqli_fetch_assoc($current_query);
    $cover_path = $current_book['cover']; 

    // Check if the user is uploading a new cover file
    if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] == 0) {
        $target_dir = "../uploads/covers/";
        
        // Ensure directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES["book_cover"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["book_cover"]["tmp_path"] ?? $_FILES["book_cover"]["tmp_name"], $target_file)) {
            // Save the path relative to your public index workspace root directory
            $cover_path = "uploads/covers/" . $new_filename;
        }
    }

        // ==========================================
        // FIXED SQL UPDATE BLOCK
        // ==========================================
        $update_sql = "UPDATE books SET 
                        title = '$title', 
                        author = '$author', 
                        isbn = '$isbn', 
                        genre = '$genre', 
                        publication_year = '$year', /* Changed column key name here */
                        publisher = '$publisher', 
                        format = '$format', 
                        price = '$price',
                        discount_percent = '$discount',
                        stock = '$stock',
                        description = '$description',
                        cover = '$cover_path'
                    WHERE id = '$book_id'";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: books.php?updated=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
    }
}

// 2. Fetch the active record data to pre-populate form elements
$query = "SELECT * FROM books WHERE id = '$book_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: books.php");
    exit();
}

$book = mysqli_fetch_assoc($result);
?>

<div class="admin-wide-container">
    <form action="edit_book.php?id=<?php echo $book['id']; ?>" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $book['id']; ?>">

        <div class="inventory-split-layout">

            <div class="cover-preview-card" style="position: relative;">
                <div class="preview-title-tag">COVER</div>
                
                <div class="interactive-file-overlay">
                    <input type="file" name="book_cover" class="file-input-ghost" id="cover-file-picker">
                    <div class="custom-upload-prompt" id="upload-prompt-text">Change File</div>
                    <img id="view-panel-cover" src="../<?php echo htmlspecialchars($book['cover']); ?>" class="preview-viewport-img" alt="Current Cover Art" style="object-fit: cover;">
                </div>
            </div>

            <div class="inventory-table-card">
                
                <div class="form-header-bar">
                    <h2 style="margin: 0;">Edit Book Details</h2>
                    <div>
                        <a href="books.php" class="btn btn-danger" style="background:#d9534f; padding: 10px 24px; text-decoration:none; border-radius: 8px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="background:#66C0F4; padding: 10px 24px; border-radius: 8px; margin-left:8px;">Save Book</button>
                    </div>
                </div>

                <div class="scrollable-form-container">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Genre</label>
                        <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Publication Year</label>
                        <input type="number" name="year" value="<?php echo htmlspecialchars($book['year'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['publisher'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select name="format">
                            <option value="Hardcover" <?php echo ($book['format'] == 'Hardcover') ? 'selected' : ''; ?>>Hardcover</option>
                            <option value="Paperback" <?php echo ($book['format'] == 'Paperback') ? 'selected' : ''; ?>>Paperback</option>
                            <option value="E-Book" <?php echo ($book['format'] == 'E-Book') ? 'selected' : ''; ?>>E-Book</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Discount (%)</label>

                        <input
                            type="number"
                            name="discount_percent"
                            min="0"
                            max="100"
                            step="0.01"
                            value="<?php echo htmlspecialchars($book['discount_percent']); ?>">
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"><?php echo htmlspecialchars($book['description'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.getElementById('cover-file-picker').addEventListener('change', function(event) {
        const selectedFile = event.target.files[0];
        if (selectedFile) {
            const temporaryObjectURL = URL.createObjectURL(selectedFile);
            document.getElementById('view-panel-cover').src = temporaryObjectURL;
        }
    });
</script>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>