<?php

session_start();

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if(isset($_GET["success"])){
    echo "<div class='alert alert-success'>
            Book added successfully!
          </div>";
}
if(isset($_GET["deleted"])){
    echo "<div class='alert alert-success'>
            Book deleted successfully!
          </div>";
}
if(isset($_GET["updated"])){
    echo "<div class='alert alert-success'>
            Book updated successfully!
          </div>";
}

if(!isset($_SESSION["user_id"])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION["role"]!="admin"){
    header("Location: ../index.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM books ORDER BY id DESC");

?>

<div class="card">

<h1>Books</h1>

<br>

<a href="add_book.php"
class="btn btn-primary">
Add Book
</a>

<br><br>

<table>

<tr>

<th>Cover</th>

<th>Title</th>

<th>Author</th>

<th>Price</th>

<th>Stock</th>

<th>Action</th>

</tr>

<?php while($book=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><img src="../<?php echo $book['cover']; ?>"
class="book-cover"></td>

<td><?php echo $book['title']; ?></td>

<td><?php echo $book['author']; ?></td>

<td>₱<?php echo number_format($book['price'],2); ?></td>

<td>

<?php

if($book['stock']==0){

echo "<span class='out-stock'>Out of Stock</span>";

}else{

echo $book['stock'];

}

?>

</td>

<td>

<a
class="btn btn-primary"
href="edit_book.php?id=<?php echo $book['id']; ?>">

Edit

</a>

<a
class="btn btn-danger"
href="delete_book.php?id=<?php echo $book['id']; ?>">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>