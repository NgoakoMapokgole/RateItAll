<?php
include "../../rate.php";
session_start();

if (!isset($_SESSION['userID'])) {
  echo "Please log in to continue.";
  exit;
}

$user_id = $_SESSION['userID'];

// --- LIKE HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like_review_id'])) {
  $review_id = intval($_POST['like_review_id']);
  $check = $conn->prepare("SELECT * FROM likes WHERE review_id = ? AND user_id = ?");
  $check->bind_param("ii", $review_id, $user_id);
  $check->execute();
  $exists = $check->get_result()->num_rows > 0;

  if (!$exists) {
    $conn->query("UPDATE post SET likes = likes + 1 WHERE postID = $review_id");
    $conn->query("INSERT INTO likes (user_id, review_id) VALUES ($user_id, $review_id)");
  }

  $likes = $conn->query("SELECT likes FROM post WHERE postID = $review_id")->fetch_assoc()['likes'];
  echo json_encode(["success" => true, "likes" => $likes]);
  exit;
}

// --- COMMENT HANDLER ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_review_id'], $_POST['comment_text'])) {
  $review_id = intval($_POST['comment_review_id']);
  $comment_text = htmlspecialchars(substr($_POST['comment_text'], 0, 500));

  $insert = $conn->prepare("INSERT INTO comments (userID, postID, content) VALUES (?, ?, ?)");
  $insert->bind_param("iis", $user_id, $review_id, $comment_text);
  $insert->execute();

  echo json_encode(["success" => true, "comment" => $comment_text]);
  exit;
}

// --- LOAD CONTENTMANAGEMENT TABLE ---
$wordMap = [];
$sql = "SELECT texts, category, entityType, rootName FROM contentmanagement where category!='banned'";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
  $wordMap[strtolower($row['texts'])] = [
    'category' => $row['category'],
    'entityType' => $row['entityType'],
    'rootName' => $row['rootName']
  ];
}

// --- FETCH ALL POSTS ---
$result = $conn->query("SELECT * FROM post WHERE deleted = 0 and category='Food' ORDER BY postID DESC");

// --- CLASSIFY POSTS INTO GROUPS BASED ON CONTENT WORD MATCHES ---
$groups = [];

while ($row = $result->fetch_assoc()) {
  $contentLower = strtolower($row['Content']);
  $foundRoot = "EverythingElse";

  foreach ($wordMap as $word => $info) {
    if (strpos($contentLower, $word) !== false) {
      $foundRoot = ucfirst($info['rootName']);
      break; // Stop at first match to assign a root group
    }
  }
  $contentLower = strtolower($row['Title']);
  foreach ($wordMap as $word => $info) {
    if (strpos($contentLower, $word) !== false) {
      $foundRoot = ucfirst($info['rootName']);
      break; // Stop at first match to assign a root group
    }
  }

  // Group posts under detected rootName
  $groups[$foundRoot][] = $row;
}

// If no groups found, add fallback
if (empty($groups)) {
  $groups['EverythingElse'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Rate It All | Reviews</title>
<link rel="stylesheet" href="../../mainStyle.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
body {
  background-color: #101010;
  color: #fff;
  font-family: Arial, sans-serif;
}
.reviews {
  width: 80%;
  margin: 2rem auto;
}
.review-card {
  background: #1b1b1b;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  box-shadow: 0 0 6px rgba(0,0,0,0.5);
}
.review-header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.review-title {
  font-size: 1.3rem;
  color: #ffcc00;
}
.review-rating {
  color: #0f0;
}
.tab-container button {
  margin: 0 10px;
  background: #222;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  color: #fff;
  cursor: pointer;
}
.tab-container button.active {
  background: #ffcc00;
  color: #000;
}
</style>
</head>

<body>

<header>
  <h1 style="text-align:center; margin: 1rem 0;">Rate It All!</h1>
  <nav style="text-align:center;">
    <a href="../Homepage/index.html" style="color:#ffcc00;">Home</a> |
    <a href="#">Reviews</a> |
    <a href="../HomePage/Login.html">Log-in</a>
  </nav>
</header>

<main class="reviews">

  <!-- Tabs for each detected rootname -->
  <div class="tab-container" style="text-align:center; margin-bottom:2rem;">
    <?php foreach (array_keys($groups) as $index => $root): ?>
      <button class="tab-btn <?= $index === 0 ? 'active' : '' ?>" data-target="<?= strtolower($root) ?>">
        <?= htmlspecialchars($root) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- Grouped reviews -->
  <?php foreach ($groups as $root => $posts): ?>
    <section class="reviews-grid group-section <?= $root === array_key_first($groups) ? 'active' : '' ?>" id="<?= strtolower($root) ?>">
      <?php foreach ($posts as $row): ?>
        <?php
          $reviewId = intval($row['postID']);
          $title = htmlspecialchars($row['Title']);
          $content = htmlspecialchars($row['Content']);
          $rating = intval($row['rating']);
          $likes = intval($row['likes']);
        ?>
        <article class="review-card" id="review-<?= $reviewId ?>">
          <div class="review-header">
            <div class="review-header-content">
              <h3 class="review-title"><?= $title ?></h3>
              <div class="review-rating">⭐ <?= $rating ?>/5</div>
            </div>
          </div>
          <div class="review-content"><?= nl2br($content) ?></div>

          <div class="review-actions">
            <button class="like-btn" data-review-id="<?= $reviewId ?>">👍 <span class="like-count"><?= $likes ?></span></button>
          </div>

          <section class="comments">
            <h4>Comments</h4>
            <ul class="comment-list" id="comments-<?= $reviewId ?>"></ul>
            <form class="comment-form" data-review-id="<?= $reviewId ?>">
              <input type="text" name="comment_text" class="comment-input" placeholder="Add a comment..." required>
              <button type="submit" class="comment-submit">Comment</button>
            </form>
          </section>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>

</main>

<footer style="text-align:center; padding:1rem; background:#111;">
  <p>&copy; 2025 Anesipho & Friends Corporation. All Rights Reserved.</p>
</footer>

<script>
$(document).ready(function() {
  // Tab switching
  $('.tab-btn').click(function() {
    var target = $(this).data('target');
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    $('.group-section').removeClass('active').hide();
    $('#' + target).fadeIn(300).addClass('active');
  });
  $('.group-section:not(.active)').hide();

  // Like system
  $('.like-btn').on('click', function() {
    let reviewId = $(this).data('review-id');
    let btn = $(this);
    $.post('', { like_review_id: reviewId }, function(res) {
      if (res.success) btn.find('.like-count').text(res.likes);
    }, 'json');
  });

  // Comment system
  $('.comment-form').on('submit', function(e) {
    e.preventDefault();
    let reviewId = $(this).data('review-id');
    let commentInput = $(this).find('input[name="comment_text"]');
    $.post('', { comment_review_id: reviewId, comment_text: commentInput.val() }, function(res) {
      if (res.success) {
        $('#comments-' + reviewId).append(`<li><strong>You:</strong> ${res.comment}</li>`);
        commentInput.val('');
      }
    }, 'json');
  });
});
</script>

</body>
</html>
