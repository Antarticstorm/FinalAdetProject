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

<div class="auth-wrapper">

    <div class="auth-left">

        <span class="auth-tag">
            THE LITERARY NOOK
        </span>

        <h1>
            Welcome Back
        </h1>

        <p class="auth-description">
            Continue your reading journey.
            Browse thousands of books, manage your wishlist,
            and keep track of every order in one place.
        </p>

        <div class="auth-features">

            <div class="feature-item">
                📚 Thousands of Books
            </div>

            <div class="feature-item">
                ❤️ Personal Wishlist
            </div>

            <div class="feature-item">
                🛒 Fast & Secure Checkout
            </div>

        </div>

    </div>

    <div class="auth-right">

        <div class="card auth-box">

            <h2>Login</h2>

            <p class="login-subtitle">

                Sign in to your account.

            </p>

            <?php if (isset($_GET["reset"]) && $_GET["reset"] == "success"): ?>

                <div class="alert alert-success">

                    Password updated successfully.

                </div>

            <?php endif; ?>

            <?php if (!empty($error)): ?>

                <div class="alert alert-error">

                    <?= $error ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <div class="password-group">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            required>

                        <button
                            type="button"
                            id="togglePasswordBtn"
                            class="toggle-password">

                            👁

                        </button>

                    </div>

                </div>

                <button
                    class="btn btn-primary btn-full">

                    Login

                </button>

            </form>

            <div class="auth-links">

                <a href="forgot_password.php">

                    Forgot Password?

                </a>

                <span>

                    Don't have an account?

                    <a href="register.php">

                        Register

                    </a>

                </span>

            </div>

        </div>

    </div>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>