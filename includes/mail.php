<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/mail_config.php';

function sendEmail($to, $subject, $body)
{
    global $config;

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['port'];

        $mail->setFrom(
            $config['username'],
            $config['from_name']
        );

        $mail->addAddress($to);

        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {

        return false;

    }
}