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

// ==========================================
// DATABASE INSERT ENGINE (POST METHOD)
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

    // Default placeholder path if no cover file is selected
    $cover_path = "uploads/covers/default.webp"; 

    // Handle the image upload file block
    if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] == 0) {
        $target_dir = "../uploads/covers/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES["book_cover"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["book_cover"]["tmp_name"], $target_file)) {
            $cover_path = "uploads/covers/" . $new_filename;
        }
    }

    // MATCH CHECK: Update 'publication_year' here if your DB column uses that name instead of 'year'
        $insert_sql = "INSERT INTO books (
            title,
            author,
            isbn,
            genre,
            publication_year,
            publisher,
            format,
            price,
            discount_percent,
            stock,
            description,
            cover
        )
        VALUES (
            '$title',
            '$author',
            '$isbn',
            '$genre',
            '$year',
            '$publisher',
            '$format',
            '$price',
            '$discount',
            '$stock',
            '$description',
            '$cover_path'
        )";

    if (mysqli_query($conn, $insert_sql)) {
        header("Location: books.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Database Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<div class="admin-wide-container">
    <form action="add_book.php" method="POST" enctype="multipart/form-data">
        
        <div class="inventory-split-layout">

            <div class="cover-preview-card" style="position: relative;">
                <div class="preview-title-tag">COVER</div>
                
                <div class="interactive-file-overlay">
                    <input type="file" name="book_cover" class="upload-input" id="cover-file-picker">
                    <div class="custom-upload-prompt" id="upload-prompt-text">Choose File</div>
                    <img id="view-panel-cover" src="../uploads/covers/default.webp" class="preview-viewport-img" alt="Preview Layout" style="object-fit: cover;">
                </div>
            </div>

            <div class="inventory-table-card">
                
                <div class="form-header-bar">
                    <h2 style="margin: 0;">Add New Book</h2>
                    <div>
                        <a href="books.php" class="btn btn-danger" style="background:#d9534f; padding: 10px 24px; text-decoration:none; border-radius: 8px;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="background:#66C0F4; padding: 10px 24px; border-radius: 8px; margin-left:8px;">Save Book</button>
                    </div>
                </div>

                <div class="scrollable-form-container">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>

                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" required>
                    </div>

                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn">
                    </div>

                    <div class="form-group">
                        <label>Genre</label>
                        <input type="text" name="genre">
                    </div>

                    <div class="form-group">
                        <label>Publication Year</label>
                        <input type="number" name="year">
                    </div>

                    <div class="form-group">
                        <label>Publisher</label>
                        <input type="text" name="publisher">
                    </div>

                    <div class="form-group">
                        <label>Format</label>
                        <select name="format">
                            <option value="Hardcover">Hardcover</option>
                            <option value="Paperback">Paperback</option>
                            <option value="E-Book">E-Book</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" required>
                    </div>

                    <div class="form-group">
                    <label>Discount (%)</label>

                    <input
                        type="number"
                        name="discount_percent"
                        min="0"
                        max="100"
                        step="0.01"
                        value="0">

                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"></textarea>
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
            document.getElementById('upload-prompt-text').textContent = 'Change File';
        }
    });
</script>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>