<?php
require "../../rate.php"; // your database connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $feedback = trim($_POST['feedback']);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Basic validation
    $errors = [];

    if ($rating < 1 || $rating > 5) {
        $errors[] = "Please select a valid rating between 1 and 5.";
    }
    if (empty($feedback)) {
        $errors[] = "Feedback cannot be empty.";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // If there are errors, redirect with error message
    if (!empty($errors)) {
        // Join errors into one string for display
        $errorString = urlencode(implode(" ", $errors));
        header("Location: ratepage.php?error=$errorString");
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO site_feedback (userName, userEmail, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $email, $rating, $feedback);

    if ($stmt->execute()) {
        // Redirect back with success message
        header("Location: http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/index.php?success=1");
    } else {
        header("Location: ratepage.php?error=" . urlencode("Database error. Please try again later."));
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>
