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

$result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>

<div class="admin-wide-container">

    <?php if(isset($_GET["success"])): ?>
        <div class="alert alert-success">Book added successfully!</div>
    <?php endif; ?>
    <?php if(isset($_GET["deleted"])): ?>
        <div class="alert alert-success">Book deleted successfully!</div>
    <?php endif; ?>
    <?php if(isset($_GET["updated"])): ?>
        <div class="alert alert-success">Book updated successfully!</div>
    <?php endif; ?>

    <div class="inventory-split-layout">

        <div class="cover-preview-card">
            <div class="preview-title-tag">COVER</div>
            
            <div class="preview-viewport-placeholder" id="placeholder-box">
                <span>Select a book</span>
            </div>
            
            <img id="view-panel-cover" src="" class="preview-viewport-img" alt="Selection Preview" style="display: none;">
        </div>

        <div class="inventory-table-card">
            
            <div class="inventory-control-bar">
                <input type="text" class="search-input" placeholder="Search book options...">
                <button class="btn btn-primary" style="background:#52B1E2; padding: 10px 20px;">Search</button>
                <a href="add_book.php" class="btn btn-primary" style="background:#4da6ff; padding: 10px 20px;">Add Book</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($book = mysqli_fetch_assoc($result)){ ?>
                    <tr data-cover="../<?php echo $book['cover']; ?>" onclick="switchPreviewCover(this)">
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <?php
                            $discounted = $book["price"];

                            if($book["discount_percent"] > 0){
                                $discounted =
                                    $book["price"] *
                                    (1 - ($book["discount_percent"]/100));
                            }
                            ?>

                            <td>

                            <?php if($book["discount_percent"] > 0): ?>

                                <span style="text-decoration:line-through;color:#999;">
                                    ₱<?= number_format($book["price"],2) ?>
                                </span>

                                <br>

                                <span style="color:#D4AF37;font-weight:bold;">
                                    ₱<?= number_format($discounted,2) ?>
                                </span>

                                <br>

                                <small>
                                    <?= $book["discount_percent"] ?>% OFF
                                </small>

                            <?php else: ?>

                                ₱<?= number_format($book["price"],2) ?>

                            <?php endif; ?>

                            </td>
                        <td>
                            <?php
                            if($book['stock'] == 0){
                                echo "<span class='out-stock'>Out of Stock</span>";
                            } else {
                                echo $book['stock'];
                            }
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-primary" style="background:#52B1E2; padding:6px 14px;" href="edit_book.php?id=<?php echo $book['id']; ?>">Edit</a>
                            <a class="btn btn-danger" style="padding:6px 14px; margin-left: 5px;" href="delete_book.php?id=<?php echo $book['id']; ?>" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function switchPreviewCover(rowElement) {
        // 1. Remove background highlights from all rows
        const structuralRows = document.querySelectorAll('.inventory-table-card table tbody tr');
        structuralRows.forEach(r => r.classList.remove('active-interactive-row'));
        
        // 2. Add highlight styling background to current selection row
        rowElement.classList.add('active-interactive-row');
        
        // 3. Hide the text placeholder container box
        document.getElementById('placeholder-box').style.display = 'none';
        
        // 4. Extract path from row and push it into the image tag, then make it visible
        const dynamicPath = rowElement.getAttribute('data-cover');
        const imgElement = document.getElementById('view-panel-cover');
        
        imgElement.src = dynamicPath;
        imgElement.style.display = 'block';
    }
</script>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>