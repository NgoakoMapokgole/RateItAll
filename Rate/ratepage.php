<?php
session_start();
include "ratepageStuff.php";
?>

<!doctype html> 
<html> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate It All ! | Rate the Site</title>
    <link rel="icon" type = "image/svg+xml" href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../mainStyle.css"/>
    <link rel="stylesheet" href="rate.css"/>
</head>

<body>
    <header>
        <?php include "../../nav.php"?>
    </header>
    
    <!-- review modal form -->
    <?php include "../../review.php"?>
    <section id="flashMessage" class="flash-message"></section>



<section class="site-rating-page">
  <main>
    <header>
      <h1>Rate Our Site</h1>
      <p>We’d love to hear your thoughts about your experience on Rate It All!</p>
    </header>

    <!-- Flash/Error Message -->
    <section id="flashMessage" class="flash-message"></section>

    <!-- Thank you message (from PHP redirect) -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
      <section class="flash-message success">
        Thank you for your feedback!
      </section>
    <?php elseif (isset($_GET['error'])): ?>
      <section class="flash-message error">
        <?php echo htmlspecialchars($_GET['error']); ?>
      </section>
    <?php endif; ?>

    <section aria-labelledby="site-rating">
      <h2 id="site-rating">Your Rating</h2>
      <form action="ratepageStuff.php" method="post">
        <fieldset>
          <legend>Overall Experience</legend>
          <label><input type="radio" name="rating" value="5" required /> Excellent</label><br>
          <label><input type="radio" name="rating" value="4" /> Good</label><br>
          <label><input type="radio" name="rating" value="3" /> Average</label><br>
          <label><input type="radio" name="rating" value="2" /> Poor</label><br>
          <label><input type="radio" name="rating" value="1" /> Terrible</label>
        </fieldset>

        <fieldset>
          <legend>Tell Us About Your Experience</legend>
          <label for="feedback">What do you like or dislike about Rate It All?</label><br>
          <textarea id="feedback" name="feedback" rows="5" cols="50" placeholder="Your feedback helps us improve..." required></textarea>
        </fieldset>

        <fieldset>
          <legend>Your Details (optional)</legend>
          <label for="name">Name:</label><br>
          <input id="name" name="name" type="text" placeholder="Enter your name" /><br><br>
          <label for="email">Email:</label><br>
          <input id="email" name="email" type="email" placeholder="you@example.com" />
        </fieldset>

        <footer>
          <button type="submit">Submit Feedback</button>
        </footer>
      </form>
    </section>
  </main>
</section>
    <?php include "../../foot.php"?>
    <script src="../../mainScript.js"></script>
    <script src="ratepage.js"></script>
  </body>
</html>
