<?php

session_start();

include("../includes/db.php");

$basePath = "../";
include("../includes/header.php");

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

<th>ID</th>

<th>Title</th>

<th>Author</th>

<th>Price</th>

<th>Stock</th>

<th>Action</th>

</tr>

<?php while($book=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $book['id']; ?></td>

<td><?php echo $book['title']; ?></td>

<td><?php echo $book['author']; ?></td>

<td>₱<?php echo $book['price']; ?></td>

<td><?php echo $book['stock']; ?></td>

<td>

<a href="edit_book.php?id=<?php echo $book['id']; ?>">Edit</a>

|

<a href="delete_book.php?id=<?php echo $book['id']; ?>">

Delete

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

<?php include("../includes/footer.php"); ?>