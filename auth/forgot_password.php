<?php

require_once("../config/app.php");

require_once(ROOT_PATH . "/includes/db.php");
require_once(ROOT_PATH . "/includes/helpers.php");
require_once(ROOT_PATH . "/includes/header.php");
require_once(ROOT_PATH . "/includes/mail.php");

$message = "";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("
        SELECT id, fullname
        FROM customers
        WHERE email = ?
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if($user = $result->fetch_assoc()){

        $token = bin2hex(random_bytes(32));

        $expires = date(
            "Y-m-d H:i:s",
            strtotime("+1 hour")
        );

        $customer_id = $user["id"];

        $fullname = $user["fullname"];

        $delete = $conn->prepare("
        DELETE FROM password_resets
        WHERE customer_id = ?
        ");

        $delete->bind_param("i",$customer_id);
        $delete->execute();

        $save = $conn->prepare("
        INSERT INTO password_resets(
        customer_id,
        token,
        expires_at
        )

        VALUES(?,?,?)
        ");

        $save->bind_param(
            "iss",
            $customer_id,
            $token,
            $expires
        );

        $save->execute();
        $delete->close();
        $save->close();
        $stmt->close();

        if ($_SERVER['HTTP_HOST'] === 'localhost') {
            $baseUrl = "http://localhost/tx23/LibrarySystem";
        } else {
            $baseUrl = "https://theliterarynook.freedev.app/";
        }

        $link = $baseUrl . "/auth/reset_password.php?token=" . $token;

        sendEmail(
            $email,
            "Reset Your Password - The Literary Nook",
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
                    🔐 Password Reset
                </h1>

                <hr style="border:1px solid #2A475E;">

                <h2>Hello, '.htmlspecialchars($fullname).'!</h2>

                <p>
                    We received a request to reset your password.
                </p>

                <p>
                    Click the button below to choose a new password.
                </p>

                <div style="text-align:center;margin:30px 0;">

                    <a
                        href="'.$link.'"
                        style="
                            background:#66C0F4;
                            color:#171A21;
                            padding:14px 28px;
                            border-radius:8px;
                            text-decoration:none;
                            font-weight:bold;
                        ">
                        Reset Password
                    </a>

                </div>

                <p>
                    This link will expire in <b>1 hour</b>.
                </p>

                <p style="color:#C7D5E0;font-size:13px;">
                    If you did not request a password reset, you can safely ignore this email.
                </p>

            </div>
            '
        );

        $message = "If the email exists, a reset link has been sent.";

    }

}

?>

<div class="grid">

<div class="card auth-box">

<h1>Forgot Password</h1>

<p>
Enter your email and we'll send a reset link.
</p>

<?php
if($message!=""){
    echo "<div class='alert alert-success'>$message</div>";
}
?>

<form method="POST">

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
required>

</div>

<button
class="btn btn-primary">

Send Reset Link

</button>

</form>

</div>

</div>

<?php require_once(ROOT_PATH . "/includes/footer.php"); ?>