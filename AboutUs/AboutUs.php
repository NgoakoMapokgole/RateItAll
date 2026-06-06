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
  <?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentCategory = $_GET['category'] ?? '';
?>
<header>
    <nav class="navbar">
        <a href ="../Content/game.php"><h1 class="logo">Rate It All !</h1></a>

        <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation menu">
            <i class="fa fa-bars"></i>
        </button>

        <ul class="nav-links">
            <li>
                <a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
            </li>
            <li>
                <a id="openReviewBtn" class="<?= $currentPage === 'writeReview.php' ? 'active' : '' ?>">Write a review</a>
            </li>
            <li class="dropdown <?= $currentPage === 'search.php' ? 'active' : '' ?>">
                <a href="#" class="dropdown-toggle">Categories</a>
                <ul class="dropdown-menu">
                    <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php?search=&category=Place" class="<?= $currentCategory === 'Place' ? 'active' : '' ?>">Places</a></li>
                    <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php?search=&category=Food" class="<?= $currentCategory === 'Food' ? 'active' : '' ?>">Food</a></li>
                    <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php?search=&category=Media" class="<?= $currentCategory === 'Media' ? 'active' : '' ?>">Media</a></li>
                    <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php?search=&category=Concept" class="<?= $currentCategory === 'Concept' ? 'active' : '' ?>">Concepts</a></li>
                    <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php?search=&category=EverythingElse" class="<?= $currentCategory === 'EverythingElse' ? 'active' : '' ?>">Wild Card</a></li>
                </ul>
            </li>
            <li>
                <a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/AboutUs/AboutUs.php" class="<?= $currentPage === 'AboutUs.php' ? 'active' : '' ?>">About Us</a>
            <!-- </li>
            <li>
                <a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/Content/Other/Other.php" class="<?= $currentPage === 'Other.php' ? 'active' : '' ?>">Other</a>
            </li> -->

            <!-- Search form for desktop -->
            <li class="nav-search-desktop">
                <form action="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php" method="get" class="nav-search">
                    <input type="text" name="search" placeholder="Search..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <select name="category">
                        <option value="">All Categories</option>
                        <option value="Place" <?= $currentCategory === 'Place' ? 'selected' : '' ?>>Places</option>
                        <option value="Food" <?= $currentCategory === 'Food' ? 'selected' : '' ?>>Food</option>
                        <option value="Media" <?= $currentCategory === 'Media' ? 'selected' : '' ?>>Media</option>
                        <option value="Concept" <?= $currentCategory === 'Concept' ? 'selected' : '' ?>>Concepts</option>
                        <option value="EverythingElse" <?= $currentCategory === 'EverythingElse' ? 'selected' : '' ?>>Everything Else</option>
                    </select>
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </li>

            <?php if (isset($_SESSION['userID']) && isset($_SESSION['userName'])): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle"><?= htmlspecialchars($_SESSION['userName']) ?></a>
                    <ul class="dropdown-menu">
                        <?php if ($_SESSION['role'] === "Admin"): ?>
                            <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/content/adminDashboard.php">AdminPage</a></li>
                        <?php elseif ($_SESSION['role'] === "Mod"): ?>
                            <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/profile.php">ModeratorPage</a></li>
                        <?php endif; ?>
                        <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/profile.php">Profile</a></li>
                        <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/settings.php">Settings</a></li>
                        <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/homepage/notification.php">Notification</a></li>
                        <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/Logout.php" onclick="return confirm('Are you sure you want to log out?');">Log Out</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/Login.php" class="<?= $currentPage === 'Login.php' ? 'active' : '' ?>">Log-in</a></li>
            <?php endif; ?>
        </ul>

        <!-- Search toggle and mobile search form -->
        <button class="search-toggle" id="search-toggle" aria-label="Toggle search">
            <i class="fa fa-search"></i>
        </button>

        <form action="http://cs3-dev.ict.ru.ac.za/practicals/4a2/HomePage/search.php" method="get" class="nav-search-mobile">
            <input type="text" name="search" placeholder="Search..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <select name="category">
                <option value="">All Categories</option>
                <option value="Place" <?= $currentCategory === 'Place' ? 'selected' : '' ?>>Places</option>
                <option value="Food" <?= $currentCategory === 'Food' ? 'selected' : '' ?>>Food</option>
                <option value="Media" <?= $currentCategory === 'Media' ? 'selected' : '' ?>>Media</option>
                <option value="Concept" <?= $currentCategory === 'Concept' ? 'selected' : '' ?>>Concepts</option>
                <option value="EverythingElse" <?= $currentCategory === 'EverythingElse' ? 'selected' : '' ?>>Everything Else</option>
            </select>
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </nav>
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