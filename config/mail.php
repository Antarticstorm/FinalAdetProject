<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(ROOT_PATH . "/vendor/autoload.php");

$config = require(ROOT_PATH . "/config/mail_config.php");

function sendEmail($to, $subject, $body)
{
    global $config;

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = $config["host"];
        $mail->SMTPAuth = true;
        $mail->Username = $config["username"];
        $mail->Password = $config["password"];

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config["port"];

        $mail->setFrom(
            $config["username"],
            $config["from_name"]
        );

        $mail->addAddress($to);

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {

        // During development
        echo $mail->ErrorInfo;

        return false;

    }

    function sendWelcomeEmail($email, $fullname)
{
    $body = '
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

        <p>You can now:</p>

        <ul>
            <li>Browse thousands of books</li>
            <li>Create your wishlist</li>
            <li>Track your future orders</li>
            <li>Receive exclusive promotions</li>
        </ul>

        <div style="text-align:center;margin:35px 0;">
            <a href="https://theliterarynook.freedev.app/"
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

    </div>';

    return sendEmail(
        $email,
        "Welcome to The Literary Nook",
        $body
    );
}
}