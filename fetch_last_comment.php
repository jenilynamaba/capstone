<?php
session_start();
require_once 'Database.php';

$database = new Database();
$pdo = $database->getConnection();

$postId = $_POST['postId'];

$stmt = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = :postId ORDER BY comments.created_at DESC LIMIT 1");
$stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

// Display the fetched comment
echo '<div class="mb-2 comment-container">';
echo '<img src="uploads/' . htmlspecialchars($comment['profile_picture']) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">';
echo '<strong class="card-title">'.' '. htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) . '</strong> <br>' . htmlspecialchars($comment['content']).'<br>';

// Display the posted date
echo '<small class="text-muted">' . htmlspecialchars($comment['created_at']) . '</small><br>';

if ($_SESSION['user']['id'] == $comment['user_id']) {
    echo '<a href="edit_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Edit</a> ';
    echo '<a href="delete_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Delete</a>';
}

echo '</div>';
?>
