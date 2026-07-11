<?php
include("includes/db.php");
include("includes/header.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$error = "";
$success = "";

$stmt = $conn->prepare("SELECT fullname, phone, address FROM customers WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);

    if (empty($fullname) || empty($phone) || empty($address)) {
        $error = "Please fill in all fields.";
    } else {
        $update = $conn->prepare("UPDATE customers SET fullname = ?, phone = ?, address = ? WHERE id = ?");
        $update->bind_param("sssi", $fullname, $phone, $address, $user_id);

        if ($update->execute()) {
            $_SESSION["fullname"] = $fullname;
            $success = "Profile updated successfully.";

            $user["fullname"] = $fullname;
            $user["phone"] = $phone;
            $user["address"] = $address;
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

        <form method="POST" action="">
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

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>