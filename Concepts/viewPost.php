<?php
include "../../rate.php"; // Database connection

// Get post ID
$postID = $_GET['id'] ?? null;
if (!$postID || !is_numeric($postID)) {
    die("Invalid post ID.");
}

// Fetch post
$postSql = "SELECT * FROM post WHERE deleted = 0 AND postID = ?";
$postStmt = $conn->prepare($postSql);
$postStmt->bind_param("i", $postID);
$postStmt->execute();
$postResult = $postStmt->get_result();

if ($postResult->num_rows === 0) {
    die("Post not found.");
}
$post = $postResult->fetch_assoc();

// Fetch author
$authorSql = "SELECT userName, profPic FROM users WHERE userID = ?";
$authorStmt = $conn->prepare($authorSql);
$authorStmt->bind_param("i", $post['userID']);
$authorStmt->execute();
$authorResult = $authorStmt->get_result();
$author = $authorResult->fetch_assoc();

// Fetch media
$mediaSql = "SELECT * FROM media WHERE postID = ? AND archived = 0 ORDER BY orderAppearance ASC";
$mediaStmt = $conn->prepare($mediaSql);
$mediaStmt->bind_param("i", $postID);
$mediaStmt->execute();
$mediaResult = $mediaStmt->get_result();

$mediaItems = [];
while ($row = $mediaResult->fetch_assoc()) {
    $mediaItems[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['Title']); ?> - Rate It All</title>
    <link rel="stylesheet" href="../../mainStyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background: #1a1d26; font-family: 'Lexend', sans-serif; color: #ccd; }
        main.full-review { max-width: 900px; margin: 2rem auto; }

        /* Header with poster and title */
        .review-header { display: flex; gap: 2rem; align-items: flex-start; margin-bottom: 1rem; }
        .review-image img { width: 200px; border-radius: 8px; object-fit: cover; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }
        .review-title { font-size: 2rem; font-weight: 600; margin: 0; }

        /* Author styling */
        .review-author { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.3rem; font-size: 0.9rem; color: #9ab; }
        .review-author .author-profPic { width: 32px; height: 32px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        .review-author .author-name { color: #00e054; text-decoration: none; font-weight: 500; }
        .review-author .author-name:hover { color: #00b843; }

        .review-meta { font-size: 0.9rem; color: #9ab; margin-top: 0.3rem; }

        /* Content & media */
        .review-content { margin-top: 1rem; line-height: 1.6; font-size: 1rem; color: #ddd; }
        .review-media { margin-top: 1.5rem; display: flex; flex-direction: column; gap: 1rem; }
        .review-media img, .review-media video, .review-media audio { border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.4); }

        /* Tags */
        .review-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 1rem; }
        .review-tag { font-size: 0.8rem; color: #9ab; background: #2c3440; padding: 3px 8px; border-radius: 4px; text-decoration: none; }

        /* Actions */
        .review-actions { display: flex; gap: 1rem; margin-top: 1.5rem; font-size: 0.95rem; }
        .like-btn, .dislike-btn { background: transparent; border: none; cursor: pointer; color: #9ab; font-size: 0.95rem; }
        .like-btn:hover, .dislike-btn:hover { color: #00e054; }
    </style>
</head>
<body>
    <header>
        <?php include "../../nav.php"; ?>
    </header>

    <main class="full-review">
        <!-- Header with poster and title -->
        <div class="review-header">
            <?php
            // Display first image as poster if exists
            $posterShown = false;
            foreach ($mediaItems as $media) {
                if ($media['typeMedia'] === 'Images') {
                    echo '<div class="review-image"><img src="' . htmlspecialchars($media['location']) . '" alt="' . htmlspecialchars($post['Title']) . '"></div>';
                    $posterShown = true;
                    break; // only first image
                }
            }
            ?>
            <div>
                <h1 class="review-title"><?php echo htmlspecialchars($post['Title']); ?></h1>

                <!-- Author -->
                <div class="review-author">
                    <?php if(!empty($author['profPic'])): ?>
                        <img src="<?php echo htmlspecialchars($author['profPic']); ?>" alt="<?php echo htmlspecialchars($author['userName']); ?>" class="author-profPic">
                    <?php endif; ?>
                    <a href="../personProfile.php?userID=<?php echo $post['userID']; ?>" class="author-name">
                        <?php echo htmlspecialchars($author['userName']); ?>
                    </a>
                </div>

                <!-- Meta -->
                <div class="review-meta">
                    <span><?php echo date("M d, Y", strtotime($post['dateCreated'])); ?></span> |
                    <span>⭐ <?php echo $post['rating']; ?>/5</span> |
                    <span><?php echo htmlspecialchars($post['category']); ?></span>
                </div>
            </div>
        </div>

        <!-- Full review content -->
        <div class="review-content">
            <?php echo nl2br(htmlspecialchars($post['Content'])); ?>
        </div>

        <!-- Additional media -->
        <div class="review-media">
            <?php foreach ($mediaItems as $media):
                if ($posterShown && $media['typeMedia'] === 'Images') continue; // skip poster again
                if ($media['typeMedia'] === 'Images'): ?>
                    <img src="<?php echo htmlspecialchars($media['location']); ?>" alt="">
                <?php elseif ($media['typeMedia'] === 'Video'): ?>
                    <video controls>
                        <source src="<?php echo htmlspecialchars($media['location']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php elseif ($media['typeMedia'] === 'Audio'): ?>
                    <audio controls>
                        <source src="<?php echo htmlspecialchars($media['location']); ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                <?php endif;
            endforeach; ?>
        </div>

        <!-- Tags -->
        <div class="review-tags">
            <?php foreach (explode(",", $post['tags']) as $tag):
                $tag = trim($tag);
                if ($tag): ?>
                <a href="search.php?search=<?php echo urlencode($tag); ?>" class="review-tag"><?php echo htmlspecialchars($tag); ?></a>
            <?php endif; endforeach; ?>
        </div>

        <!-- Likes / Dislikes -->
        <div class="review-actions">
            <button class="like-btn">👍 <?php echo $post['likes']; ?></button>
            <button class="dislike-btn">👎 <?php echo $post['dislikes']; ?></button>
        </div>
    </main>

    <?php include "../../foot.php"; ?>
</body>
</html>
