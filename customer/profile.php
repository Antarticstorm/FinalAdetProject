<?php
require_once("../config/app.php");
require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

if (!isset($_SESSION["user_id"])) {
    redirect("auth/login.php");
}

$user_id = $_SESSION["user_id"];

/* User data */
$stmt = $conn->prepare("
    SELECT fullname, email, phone, address, avatar, membership_status, created_at
    FROM customers
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

/* Wishlist count */
$wishlistCount = 0;
$wishlistStmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM wishlist
    WHERE customer_id = ?
");
$wishlistStmt->bind_param("i", $user_id);
$wishlistStmt->execute();
$wishlistResult = $wishlistStmt->get_result();
$wishlist = $wishlistResult->fetch_assoc();
$wishlistCount = (int)$wishlist["total"];
$wishlistStmt->close();

/* Temporary values for now */
$orderCount = 0;
$purchasedCount = 0;
?>

<div class="profile-page">
    <div class="card profile-card">
        <div class="profile-header">
            <img
                src="<?= BASE_URL . htmlspecialchars($user["avatar"]) ?>"
                class="profile-avatar"
                alt="Avatar">

            <div class="profile-title">
                <h1><?= htmlspecialchars($user["fullname"]) ?></h1>
                <span class="membership-badge">
                    <?= htmlspecialchars($user["membership_status"]) ?> Member
                </span>
            </div>
        </div>

        <hr class="profile-divider">

        <div class="profile-info">
            <div>
                <h4>Email</h4>
                <p><?= htmlspecialchars($user["email"]) ?></p>
            </div>

            <div>
                <h4>Phone</h4>
                <p><?= htmlspecialchars($user["phone"]) ?></p>
            </div>

            <div>
                <h4>Address</h4>
                <p><?= htmlspecialchars($user["address"]) ?></p>
            </div>

            <div>
                <h4>Member Since</h4>
                <p><?= date("F d, Y", strtotime($user["created_at"])) ?></p>
            </div>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number"><?= $wishlistCount ?></div>
            <div class="stat-label">Wishlist</div>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $orderCount ?></div>
            <div class="stat-label">Orders</div>
        </div>

        <div class="stat-card">
            <div class="stat-number"><?= $purchasedCount ?></div>
            <div class="stat-label">Purchased</div>
        </div>
    </div>

    <div class="profile-actions">
        <a href="<?= url('customer/edit_profile.php') ?>" class="btn btn-primary">
            Edit Profile
        </a>
    </div>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>