<?php
session_start();

require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["role"] != "admin"){
    header("Location: ../index.php");
    exit();
}

$basePath="../";
include("../includes/header.php");

// ==========================================
// 1. FETCH CURRENT BOOK DETAILS (GET)
// ==========================================
$book = null;
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $book_id = intval($_GET["id"]);
    
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $book = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-error'>Book not found.</div>";
        require_once(ROOT_PATH . "/includes/footer.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: books.php");
    exit();
}

// ==========================================
// 2. PROCESS THE UPDATED DETAILS (POST)
// ==========================================
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $isbn = trim($_POST["isbn"]);
    $genre = trim($_POST["genre"]);
    $publication_year = $_POST["publication_year"];
    $publisher = trim($_POST["publisher"]);
    $format = $_POST["format"];
    $price = $_POST["price"];
    $stock = $_POST["stock"];
    $description = trim($_POST["description"]);

    // Keep the old cover by default if a new one isn't uploaded
    $cover = $book["cover"]; 

    // Handle new image upload if present
    if(isset($_FILES["cover"]) && $_FILES["cover"]["error"] == 0){
        $allowed = ["jpg","jpeg","png","webp"];
        $extension = strtolower(pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION));

        if(in_array($extension, $allowed)){
            $filename = uniqid().".".$extension;
            $destination = "../uploads/covers/".$filename;

            if(move_uploaded_file($_FILES["cover"]["tmp_name"], $destination)){
                $cover = "uploads/covers/".$filename;
            }
        }
    }

    // Run the UPDATE query matching the specific ID
    $update_stmt = $conn->prepare("
        UPDATE books 
        SET title = ?, author = ?, isbn = ?, genre = ?, publication_year = ?, 
            publisher = ?, format = ?, price = ?, stock = ?, description = ?, cover = ?
        WHERE id = ?
    ");

    $update_stmt->bind_param(
        "ssssissdissi", // Notice the extra 'i' at the end for $book_id
        $title, $author, $isbn, $genre, $publication_year, 
        $publisher, $format, $price, $stock, $description, $cover, $book_id
    );
    
    if($update_stmt->execute()){
        // Redirect back to books overview with a fresh updated URL flag
        echo "<script>window.location.href='books.php?updated=1';</script>";
        exit();
    } else {
        echo "<div class='alert alert-error'>".$update_stmt->error."</div>";
    }
    $update_stmt->close();
}
?>

<div class="card">
    <h1>Edit Book Details</h1>
    
    <form action="" method="POST" enctype="multipart/form-data">

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
            <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>">
        </div>

        <div class="form-group">
            <label>Genre</label>
            <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>">
        </div>

        <div class="form-group">
            <label>Publication Year</label>
            <input type="number" name="publication_year" value="<?php echo htmlspecialchars($book['publication_year']); ?>">
        </div>

        <div class="form-group">
            <label>Publisher</label>
            <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>">
        </div>

        <div class="form-group">
            <label>Format</label>
            <select name="format">
                <option <?php if($book['format'] == 'Hardcover') echo 'selected'; ?>>Hardcover</option>
                <option <?php if($book['format'] == 'Paperback') echo 'selected'; ?>>Paperback</option>
                <option <?php if($book['format'] == 'E-Book') echo 'selected'; ?>>E-Book</option>
                <option <?php if($book['format'] == 'Audiobook') echo 'selected'; ?>>Audiobook</option>
            </select>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($book['stock']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?php echo htmlspecialchars($book['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Current Book Cover</label><br>
            <img src="../<?php echo $book['cover']; ?>" class="book-cover" style="width:100px; marginBottom:10px;"><br>
            <label>Upload New Cover (Leave blank to keep current)</label>
            <input type="file" name="cover" accept="image/*">
        </div>

        <button class="btn btn-primary">Update Book</button>
    </form>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>