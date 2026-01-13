<?php
$to = "ziadhmk.12345@gmail.com";
$subject = "Test Email from Your Website";
$message = "This is a test email to verify that your server can send emails.";
$headers = "From: no-reply@yourdomain.com\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email.";
}
?>