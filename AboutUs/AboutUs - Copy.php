<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate It All ! | About Us</title>
    <link rel="icon" type = "image/svg+xml" href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../mainStyle.css"/>
    <link rel="stylesheet" href="AboutUs.css"/>
</head>
<body>
    <header>
        <?php include "../nav.php"?>
    </header>
    
    <!-- review modal form -->
    <?php include "../review.php"?>
    <section id="flashMessage" class="flash-message"></section>

    <main>
      <section class="about-hero">
        <h2>What Is Rate It All!</h2>
        <p><strong>Rate It All!</strong> provides a structured platform for sharing and exploring reviews across various categories, such as Places, Food, Media and Concepts. Reviewers can see what others think, contribute their own perspectives, and access organised insights to make informed choices or simply connect.</p>
      </section>
      
      <section class="team-section">
        <article class="team-intro">
          <h2>Meet the team</h2>
          <figure class="team-photo">
            <img src="formalPic.png" alt="The Rate It All! team working together" class="team-group-photo">
            <figcaption>Our dedicated team behind Rate It All!</figcaption>
          </figure>
        </article>
        
        <section class="team-grid">
          <article class="team-member">
            <figure>
              <img src="ngoako.jpg" alt="Ngoako Mapokgole - CEO" class="member-photo">
            </figure>
            <section class="member-info">
              <h3>Ngoako Mapokgole</h3>
              <p class="member-role">Chief Executive Officer</p>
              <p>Ngoako Mapokgole is the CEO and founder of Rate it All!. As a former Project Manager, he has proven his leadership experience in guiding teams, managing projects, and turning ideas into real solutions.</p>
            </section>
          </article>
          
          <article class="team-member">
            <figure>
              <img src="ane.jpg" alt="Anesipho Nkonkobe - Back-End Developer" class="member-photo">
            </figure>
            <section class="member-info">
              <h3>Anesipho Nkonkobe</h3>
              <p class="member-role">Back-End Developer</p>
              <p>Anesipho is the problem-solver behind the scenes, making sure our review platform runs smoothly, securely and at scale. Her blend of technical expertise makes her a driving force.</p>
            </section>
          </article>
          
          <article class="team-member">
            <figure>
              <img src="mathlo.jpg" alt="Mathlo Lethabo - Front-End Developer" class="member-photo">
            </figure>
            <section class="member-info">
              <h3>Mathlo Lethabo</h3>
              <p class="member-role">Front-End Developer</p>
              <p>Mathlo brings analytical precision and creative vision to the company. Using his outside the box thinking, he ensures users have an interactive experience through user-centered design.</p>
            </section>
          </article>
        </section>
      </section>
    </main>
    
    <?php include "../foot.php"?>
    <script>
        const isLoggedIn = <?php echo isset($_SESSION['userID']) ? 'true' : 'false'; ?>;
        const userId = <?php echo isset($_SESSION['userID']) ? $_SESSION['userID'] : 'null'; ?>;

        const reviewStatus = <?php
        if(isset($_SESSION['reviewStatus'])) {
            echo json_encode($_SESSION['reviewStatus']);
            unset($_SESSION['reviewStatus']); // clear flash after reading
        } else {
            echo 'null';
        }
        ?>;
    </script>
    <script src="../mainScript.js"></script>
    <script src="home.js"></script>
</body>
</body>
</html>