<?php

session_start();

include("../includes/db.php");

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

if($_SERVER["REQUEST_METHOD"]=="POST"){

    // 1. Read form values
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

    // 2. Default cover
    $cover = "uploads/covers/default.webp";

    // 3. Upload image
    if(isset($_FILES["cover"]) && $_FILES["cover"]["error"]==0){

        $allowed = ["jpg","jpeg","png","webp"];

        $extension = strtolower(pathinfo($_FILES["cover"]["name"], PATHINFO_EXTENSION));

        if(in_array($extension,$allowed)){

            $filename = uniqid().".".$extension;

            $destination = "../uploads/covers/".$filename;

            if(move_uploaded_file($_FILES["cover"]["tmp_name"], $destination)){
    $cover = "uploads/covers/".$filename;
}

            $cover = "uploads/covers/".$filename;
        }
    }

        $stmt = $conn->prepare("
        INSERT INTO books(
            title,
            author,
            isbn,
            genre,
            publication_year,
            publisher,
            format,
            price,
            stock,
            description,
            cover
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "ssssissdiss",
            $title,
            $author,
            $isbn,
            $genre,
            $publication_year,
            $publisher,
            $format,
            $price,
            $stock,
            $description,
            $cover
        );
        
        if($stmt->execute()){

        header("Location: books.php?success=1");
        exit();

    }else{

        echo "<div class='alert alert-error'>
                ".$stmt->error."
            </div>";
    }

    $stmt->close();

    // bind_param goes here

    // execute goes here

}
?>

<div class="card">

<h1>Add New Book</h1>

<form action="" method="POST" enctype="multipart/form-data">

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
<input type="number" name="publication_year">
</div>

<div class="form-group">
<label>Publisher</label>
<input type="text" name="publisher">
</div>

<div class="form-group">
<label>Format</label>

<select name="format">

<option>Hardcover</option>

<option>Paperback</option>

<option>E-Book</option>

<option>Audiobook</option>

</select>

</div>

<div class="form-group">
<label>Price</label>
<input type="number" step="0.01" name="price" required>
</div>

<div class="form-group">
<label>Stock</label>
<input type="number" name="stock" required>
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description"></textarea>
</div>

<div class="form-group">
<label>Book Cover</label>
<input type="file" name="cover" accept="image/*">
</div>

<button class="btn btn-primary">

Save Book

</button>

</form>

</div>

<?php include("../includes/footer.php"); ?>