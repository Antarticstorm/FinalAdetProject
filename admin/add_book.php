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