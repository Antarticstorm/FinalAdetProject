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
$error = "";
$success = "";

$stmt = $conn->prepare("
    SELECT
        fullname,
        phone,
        address,
        avatar
    FROM customers
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    
    /* Current avatar */

$avatar = $user["avatar"];

/* Upload avatar */

if (
    isset($_FILES["avatar"]) &&
    $_FILES["avatar"]["error"] == 0
) {

    $allowed = ["jpg","jpeg","png","webp"];

    $extension = strtolower(
        pathinfo(
            $_FILES["avatar"]["name"],
            PATHINFO_EXTENSION
        )
    );

    if (in_array($extension,$allowed)) {

        $filename = uniqid("avatar_") . "." . $extension;

        $destination = ROOT_PATH . "/uploads/avatars/" . $filename;

        if (
            move_uploaded_file(
                $_FILES["avatar"]["tmp_name"],
                $destination
            )
        ) {

            if (
                $avatar != "uploads/avatars/default.webp" &&
                file_exists(ROOT_PATH . "/" . $avatar)
            ) {

                unlink(ROOT_PATH . "/" . $avatar);

            }

            $avatar = "uploads/avatars/" . $filename;

        }

    }

}

    if (empty($fullname) || empty($phone) || empty($address)) {
        $error = "Please fill in all fields.";
    } else {
        $update = $conn->prepare("
        UPDATE customers
        SET
            fullname = ?,
            phone = ?,
            address = ?,
            avatar = ?
        WHERE id = ?
        ");

        $update->bind_param(
            "ssssi",
            $fullname,
            $phone,
            $address,
            $avatar,
            $user_id
        );

        if ($update->execute()) {
            $_SESSION["fullname"] = $fullname;

            $success = "Profile updated successfully.";

            $user["fullname"] = $fullname;
            $user["phone"] = $phone;
            $user["address"] = $address;
            $user["avatar"] = $avatar;
        } else {
            $error = "Failed to update profile.";
        }

        $update->close();
    }
}
?>

<div class="grid">
    <div class="card auth-box">
        <h1>Edit Profile</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user["fullname"]); ?>" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user["phone"]); ?>" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required><?php echo htmlspecialchars($user["address"]); ?></textarea>
            </div>

            <div class="avatar-upload">

                <label for="avatar">

                    <img
                        src="<?= BASE_URL . htmlspecialchars($user["avatar"]) ?>"
                        class="profile-avatar editable-avatar"
                        id="avatarPreview">

                    <div class="avatar-overlay">

                        Change Photo

                    </div>

                </label>

                <input
                    type="file"
                    id="avatar"
                    name="avatar"
                    accept="image/webp,image/png,image/jpeg"
                    hidden>

            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>

        </form>
    </div>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>