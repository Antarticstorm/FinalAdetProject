<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password,role FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["fullname"] = $row["fullname"];
                $_SESSION["role"] = $row["role"];
                if ($row["role"] == "admin") {
                    redirect("admin/dashboard.php");
                } else {
                    redirect("index.php");
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }

        $stmt->close();
    }
}
?>



<div class="grid">
    <div class="card auth-box">
        <h1>Login</h1>
        <p>Welcome back to The Literary Nook.</p>

        <?php if (isset($_GET["reset"]) && $_GET["reset"] == "success"): ?>
            <div class="alert alert-success">
                Password updated successfully. You may now log in.
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p class="small-text">

        <a href="forgot_password.php">

        Forgot Password?

        </a>

        </p>

        <p class="small-text">Don't have an account? <a href="register.php">Create one</a></p>
    </div>
</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>