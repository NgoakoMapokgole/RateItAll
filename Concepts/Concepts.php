<?php
session_start();
include "../../rate.php"; // Database connection

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

// --- SEARCH AND CATEGORY FILTER ---
$query = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// --- LOAD WORD MAP FOR CONTENT GROUPING ---
$wordMap = [];
$cmRes = $conn->query("SELECT texts, category, entityType, rootName FROM contentmanagement WHERE category!='banned'");
while($row = $cmRes->fetch_assoc()){
    $wordMap[strtolower($row['texts'])] = [
        'category' => $row['category'],
        'entityType' => $row['entityType'],
        'rootName' => $row['rootName']
    ];
}

// --- FETCH POSTS ---
$sql = "SELECT p.*, u.userName, u.profPic 
        FROM post p
        JOIN users u ON p.userID = u.userID
        WHERE p.deleted = 0";
$params = [];
$types = "";

// Apply search filter
if($query){
    $sql .= " AND (p.Title LIKE ? OR p.tags LIKE ?)";
    $likeQuery = "%$query%";
    $params[] = &$likeQuery;
    $params[] = &$likeQuery;
    $types .= "ss";
}

// Apply category filter
if($categoryFilter){
    $sql .= " AND p.category = ?";
    $params[] = &$categoryFilter;
    $types .= "s";
}

$sql .= " ORDER BY p.postID DESC";

$stmt = $conn->prepare($sql);
if($params){
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// --- CLASSIFY POSTS INTO GROUPS BASED ON CONTENT/TITLE MATCH ---
$groups = [];

while($row = $result->fetch_assoc()){
    $contentLower = strtolower($row['Content']);
    $titleLower = strtolower($row['Title']);
    $foundRoot = "EverythingElse";

    foreach($wordMap as $word => $info){
        if(strpos($contentLower, $word) !== false || strpos($titleLower, $word) !== false){
            $foundRoot = ucfirst($info['rootName']);
            break;
        }
    }

    $groups[$foundRoot][] = $row;
}

// Fallback group
if(empty($groups)){
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
body { background:#101010; color:#fff; font-family:Arial,sans-serif; }
header, footer { text-align:center; padding:1rem; background:#111; }
.search-results-layout { display:flex; gap:2rem; max-width:1200px; margin:2rem auto; }
.all-reviews { flex:3; }
.sidebar { flex:1; background: rgba(255,255,255,0.02); border-radius:8px; padding:1rem; }
.review-card { background:#1b1b1b; padding:1rem; border-radius:12px; margin-bottom:1rem; box-shadow:0 0 6px rgba(0,0,0,0.5);}
.review-header { display:flex; align-items:center; gap:1rem; }
.review-title { color:#ffcc00; margin:0; }
.review-meta { font-size:0.85rem; color:rgba(255,255,255,0.6); }
.comment-list { list-style:none; padding:0; margin-top:0.5rem; }
.comment-form { display:flex; gap:0.5rem; margin-top:0.5rem; }
.comment-input { flex:1; padding:0.3rem; border-radius:4px; border:none; }
.comment-submit { padding:0.3rem 0.7rem; border-radius:4px; background:#ffcc00; border:none; cursor:pointer; }

/* Tabs styling */
.tab-container { text-align:center; margin-bottom:1rem; }
.tab-btn { margin:0 6px; padding:6px 12px; border:none; border-radius:6px; cursor:pointer; background:#222; color:#fff; }
.tab-btn.active { background:#ffcc00; color:#000; }
.group-section { display:none; }
.group-section.active { display:block; }
</style>
</head>
<body>

<header>
<h1>Rate It All</h1>
<nav>
<a href="../Homepage/index.html" style="color:#ffcc00;">Home</a> |
<a href="#">Reviews</a> |
<a href="../HomePage/Login.html">Log-in</a>
</nav>
</header>

<section class="search-section" style="text-align:center; margin:1rem 0;">
<form method="get" action="">
<input type="text" name="search" placeholder="Search reviews..." value="<?= htmlspecialchars($query) ?>">
<select name="category">
<option value="">All Categories</option>
<?php
$categories = ['Place','Food','Media','Concept','EverythingElse'];
foreach($categories as $cat){
    $selected = ($categoryFilter === $cat) ? 'selected' : '';
    echo "<option value='$cat' $selected>$cat</option>";
}
?>
</select>
<button type="submit">Search</button>
</form>
</section>

<!-- Tabs -->
<div class="tab-container">
<?php foreach(array_keys($groups) as $i => $groupName): ?>
    <button class="tab-btn <?= $i === 0 ? 'active' : '' ?>" data-target="<?= strtolower($groupName) ?>"><?= htmlspecialchars($groupName) ?></button>
<?php endforeach; ?>
</div>

<section class="search-results-layout">
  <div class="all-reviews">
    <?php foreach($groups as $groupName => $posts): ?>
      <div class="group-section <?= $groupName === array_key_first($groups) ? 'active' : '' ?>" id="<?= strtolower($groupName) ?>">
        <?php if($posts): ?>
          <?php foreach($posts as $row): 
            $reviewId = intval($row['postID']);
            $title = htmlspecialchars($row['Title']);
            $content = htmlspecialchars(substr(strip_tags($row['Content']),0,200));
            $likes = intval($row['likes']);
            $dislikes = intval($row['dislikes']);
          ?>
          <article class="review-card" id="review-<?= $reviewId ?>">
            <div class="review-header">
              <?php if($row['profPic']): ?>
                <img src="<?= htmlspecialchars($row['profPic']) ?>" alt="<?= htmlspecialchars($row['userName']) ?>" width="40" height="40" style="border-radius:50%;">
              <?php endif; ?>
              <div>
                <h3 class="review-title"><?= $title ?></h3>
                <div class="review-meta">
                  <span>By <a href="../personProfile.php?userID=<?= $row['userID'] ?>" style="color:#ffcc00;"><?= htmlspecialchars($row['userName']) ?></a></span> |
                  <span><?= date("M d, Y", strtotime($row['dateCreated'])) ?></span> |
                  <span>⭐ <?= $row['rating'] ?>/5</span> |
                  <span><?= htmlspecialchars($row['category']) ?></span>
                </div>
              </div>
            </div>
            <p><?= $content ?>...</p>
            <div class="review-actions">
              <button class="like-btn" data-review-id="<?= $reviewId ?>">👍 <span class="like-count"><?= $likes ?></span></button>
              <button class="dislike-btn">👎 <?= $dislikes ?></button>
            </div>
            <ul class="comment-list" id="comments-<?= $reviewId ?>"></ul>
            <form class="comment-form" data-review-id="<?= $reviewId ?>">
              <input type="text" name="comment_text" class="comment-input" placeholder="Add a comment..." required>
              <button type="submit" class="comment-submit">Comment</button>
            </form>
          </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No posts in this group.</p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>

  <aside class="sidebar">
    <h3>Top Rated</h3>
    <ul>
      <?php
      $topRated = $conn->query("SELECT postID, Title, rating FROM post WHERE deleted=0 ORDER BY rating DESC LIMIT 5");
      while($tr = $topRated->fetch_assoc()):
      ?>
      <li><a href="viewPost.php?id=<?= $tr['postID'] ?>"><?= htmlspecialchars($tr['Title']) ?> ⭐ <?= $tr['rating'] ?></a></li>
      <?php endwhile; ?>
    </ul>

    <h3>Recent Reviews</h3>
    <ul>
      <?php
      $recent = $conn->query("SELECT postID, Title, dateCreated FROM post WHERE deleted=0 ORDER BY dateCreated DESC LIMIT 5");
      while($rc = $recent->fetch_assoc()):
      ?>
      <li><a href="viewPost.php?id=<?= $rc['postID'] ?>"><?= htmlspecialchars($rc['Title']) ?> (<?= date("Y-m-d", strtotime($rc['dateCreated'])) ?>)</a></li>
      <?php endwhile; ?>
    </ul>
  </aside>
</section>

<footer>&copy; 2025 Anesipho & Friends Corporation. All Rights Reserved.</footer>

<script>
$(document).ready(function(){
  // Tabs
  $('.tab-btn').click(function(){
    var target = $(this).data('target');
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    $('.group-section').removeClass('active').hide();
    $('#' + target).fadeIn(300).addClass('active');
  });
  $('.group-section:not(.active)').hide();

  // Like system
  $('.like-btn').click(function(){
    let reviewId = $(this).data('review-id');
    let btn = $(this);
    $.post('', { like_review_id: reviewId }, function(res){
      if(res.success) btn.find('.like-count').text(res.likes);
    }, 'json');
  });

  // Comment system
  $('.comment-form').submit(function(e){
    e.preventDefault();
    let reviewId = $(this).data('review-id');
    let commentInput = $(this).find('input[name="comment_text"]');
    $.post('', { comment_review_id: reviewId, comment_text: commentInput.val() }, function(res){
      if(res.success){
        $('#comments-' + reviewId).append('<li><strong>You:</strong> '+res.comment+'</li>');
        commentInput.val('');
      }
    }, 'json');
  });
});
</script>

</body>
</html>
