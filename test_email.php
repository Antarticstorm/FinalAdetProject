<?php

include("includes/mail.php");

if (
    sendEmail(
        "your@email.com",
        "Welcome to The Literary Nook",
        "
        <h2>Welcome!</h2>

        <p>
            Thank you for registering at
            <b>The Literary Nook</b>.
        </p>
        "
    )
) {
    echo "Email sent!";
} else {
    echo "Failed to send.";
}