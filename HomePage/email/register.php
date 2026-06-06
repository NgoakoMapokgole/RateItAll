<?php
session_start();

// Sanitize and validate email
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    exit("Invalid email address.");
}

// Simulate token generation
$token = bin2hex(random_bytes(16));

// Simulate saving user (to session)
$_SESSION['verification'][$token] = [
    'email' => $email,
    'verified' => false
];

// Construct verification link
$verifyLink = "http://localhost/verify.php?token=$token";

// Simulate sending email (just output the link on screen)
echo "<h2>Verification Email Sent!</h2>";
echo "<p>Click the link below to verify your email:</p>";
echo "<a href='$verifyLink'>$verifyLink</a>";
?>
