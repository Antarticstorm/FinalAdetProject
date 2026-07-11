<?php

include("includes/db.php");

if (!isset($_GET["token"])) {
    die("Invalid reset link.");
}

$token = $_GET["token"];

/* Find token first */
$stmt = $conn->prepare("
    SELECT customer_id, expires_at
    FROM password_resets
    WHERE token = ?
");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if (!$reset = $result->fetch_assoc()) {
    die("Invalid or expired reset link.");
}

if (strtotime($reset["expires_at"]) < time()) {
    die("This reset link has expired.");
}

$customer_id = $reset["customer_id"];
$error = "";

/* Handle password update */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $update = $conn->prepare("
            UPDATE customers
            SET password = ?
            WHERE id = ?
        ");
        $update->bind_param("si", $hashed_password, $customer_id);

        if ($update->execute()) {
            $delete = $conn->prepare("
                DELETE FROM password_resets
                WHERE token = ?
            ");
            $delete->bind_param("s", $token);
            $delete->execute();

            header("Location: login.php?reset=success");
            exit();
        } else {
            $error = "Failed to update password.";
        }

        $update->close();
    }
}

include("includes/header.php");
?>

<div class="grid">
    <div class="card auth-box">
        <h1>Reset Password</h1>
        <p>Enter your new password below.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>