<?php
include("includes/db.php");
include("includes/header.php");

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($fullname) || empty($email) || empty($phone) || empty($address) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO customers (fullname, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fullname, $email, $phone, $address, $hashed_password);

        if ($stmt->execute()) {

            include("includes/mail.php");

            sendEmail(
                $email,
                "Welcome to The Literary Nook",
                '
                <div style="
                    max-width:600px;
                    margin:auto;
                    background:#1B2838;
                    color:white;
                    font-family:Arial,sans-serif;
                    padding:30px;
                    border-radius:10px;
                ">

                    <h1 style="color:#66C0F4;text-align:center;">
                        The Literary Nook
                    </h1>

                    <hr style="border:1px solid #2A475E;">

                    <h2>Welcome, '.htmlspecialchars($fullname).'!</h2>

                    <p>
                        Your account has been successfully created.
                    </p>

                    <p>
                        You can now:
                    </p>

                    <ul>
                        <li>Browse thousands of books</li>
                        <li>Create your wishlist</li>
                        <li>Track your future orders</li>
                        <li>Receive exclusive promotions</li>
                    </ul>

                    <div style="text-align:center;margin:35px 0;">

                        <a
                        href="https://theliterarynook.freedev.app/"
                        style="
                            background:#66C0F4;
                            color:#171A21;
                            padding:14px 28px;
                            text-decoration:none;
                            border-radius:8px;
                            font-weight:bold;
                        ">
                            Visit The Literary Nook
                        </a>

                    </div>

                    <hr style="border:1px solid #2A475E;">

                    <p style="text-align:center;color:#C7D5E0;">
                        Happy Reading!
                    </p>

                </div>
                '
            );

            $success = "Registration successful. Please check your email!";

        } else {

            $error = "Something went wrong. Please try again.";

        }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<div class="grid">
    <div class="card auth-box">
        <h1>Create Account</h1>
        <p>Join The Literary Nook.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="fullname" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Account</button>
        </form>

        <p class="small-text">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</div>

<?php include("includes/footer.php"); ?>