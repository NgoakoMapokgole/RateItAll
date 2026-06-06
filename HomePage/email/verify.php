<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP server configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.elasticemail.com';     // e.g., smtp.gmail.com or smtp.elasticemail.com
    $mail->SMTPAuth   = true;
    $mail->Username   = 'anesiphonkonkobe@gmail.com'; // your SMTP username
    $mail->Password   = 'YOUR_SMTP_PASSWORD';         // your SMTP password or API key
    $mail->SMTPSecure = 'tls';                        // encryption: 'tls' or 'ssl'
    $mail->Port       = 587;                          // port: 587 for TLS, 465 for SSL

    // Sender and recipient
    $mail->setFrom('anesiphonkonkobe@gmail.com', 'Anesipho');
    $mail->addAddress('nkonkobeanesipho@gmail.com', 'Esipho');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test Email';
    $mail->Body    = '<h1>Hello Esipho!</h1><p>This is a test email sent with PHPMailer via SMTP.</p>';
    $mail->AltBody = 'Hello Esipho! This is a test email sent with PHPMailer via SMTP.';

    $mail->send();
    echo '✅ Email has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>

