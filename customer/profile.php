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
$stmt = $conn->prepare("
    SELECT
        fullname,
        email,
        phone,
        address,
        avatar,
        membership_status,
        created_at
    FROM customers
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>

<<<<<<< Updated upstream
<div class="card">
    <h1>Your Profile</h1>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user["fullname"]); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user["phone"]); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($user["address"]); ?></p>
    <p><strong>Member Since:</strong> <?php echo date("F d, Y", strtotime($user["created_at"])); ?></p>
=======
<div class="card profile-card">

    <div class="profile-header">

        <img
            src="<?= BASE_URL . htmlspecialchars($user["avatar"]) ?>"
            class="profile-avatar"
            alt="Avatar">

        <div>

            <h1><?= htmlspecialchars($user["fullname"]) ?></h1>

            <span class="membership-badge">
                <?= htmlspecialchars($user["membership_status"]) ?> Member
            </span>

        </div>

    </div>
>>>>>>> Stashed changes

    <hr>

    <div class="profile-info">

        <p><strong>Email</strong><br><?= htmlspecialchars($user["email"]) ?></p>

        <p><strong>Phone</strong><br><?= htmlspecialchars($user["phone"]) ?></p>

        <p><strong>Address</strong><br><?= htmlspecialchars($user["address"]) ?></p>

        <p><strong>Member Since</strong><br><?= date("F d, Y", strtotime($user["created_at"])) ?></p>

    </div>

</div>

<div class="profile-stats">

    <div class="stat-card">
        <div class="stat-number"><?= $wishlistCount ?></div>
        <div class="stat-label">Wishlist</div>
    </div>

    <div class="stat-card">
        <div class="stat-number">0</div>
        <div class="stat-label">Orders</div>
    </div>

    <div class="stat-card">
        <div class="stat-number">0</div>
        <div class="stat-label">Purchased</div>
    </div>

</div>

<div style="margin-top:25px;">
    <a href="edit_profile.php" class="btn btn-primary">
        Edit Profile
    </a>
</div>
<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>