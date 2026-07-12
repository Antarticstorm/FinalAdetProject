<?php
require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT fullname, email, phone, address, created_at FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
/* Wishlist Count */
$wishlistStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM wishlist
    WHERE customer_id = ?
");

$wishlistStmt->bind_param("i", $user_id);
$wishlistStmt->execute();

$wishlistResult = $wishlistStmt->get_result();
$wishlist = $wishlistResult->fetch_assoc();

$wishlistCount = $wishlist["total"];

$wishlistStmt->close();
?>

<div class="card">
    <h1>Your Profile</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user["fullname"]); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user["phone"]); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($user["address"]); ?></p>
    <p><strong>Member Since:</strong> <?php echo date("F d, Y", strtotime($user["created_at"])); ?></p>
    <div class="card">

    <h3>Wishlist</h3>

    <h2><?= $wishlistCount ?></h2>

    <p>Books Saved</p>

    </div>

    <div style="margin-top: 20px;">
        <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
    </div>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>