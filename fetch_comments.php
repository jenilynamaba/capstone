<?php
    session_start();
    require_once 'Database.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['postId'])) {

            $postId = $_POST['postId'];

            $db = new Database();
            $pdo = $db->getConnection();

            $stmt = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = :postId ORDER BY comments.created_at DESC");
            $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($comments as $comment) {
                echo '<div class="mb-2 comment-container">';
                echo '<img src="uploads/' . htmlspecialchars($comment['profile_picture']) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">';
                echo '<strong class="card-title">' . ' ' . htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) . '</strong> <br>' . htmlspecialchars($comment['content']) . '<br>';
                if ($_SESSION['user']['id'] == $comment['user_id']) {
                    echo '<a href="edit_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Edit</a> ';
                    echo '<a href="delete_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Delete</a>';
                }
                echo '<br><small class="text-muted">' . htmlspecialchars($comment['created_at']) . '</small>'; // Display comment date
                echo '<div class="reply-text" style="cursor: pointer; color: blue;">Reply</div>';
                echo '<div class="reply-form" style="display: none;">';
                echo '<form action="process_comment.php" method="post" class="mb-2 ms-3">';
                echo '<div class="mb-1">';
                echo '<input class="form-control" type="text" id="replyContent" name="replyContent" placeholder="Your Reply">';
                echo '<input type="hidden" name="commentId" value="' . htmlspecialchars($comment['id']) . '">';
                echo '<input type="hidden" name="postId" value="' . htmlspecialchars($postId) . '">';
                echo '</div>';
                echo '<div class="input-group-append">';
                echo '<button type="submit" class="btn btn-primary">Reply</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            $db = new Database();
            $pdo = $db->getConnection();

            $userId = $_SESSION['user']['id'];

            $stmt = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.user_id = :userId ORDER BY comments.created_at DESC");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $userComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode(['userComments' => $userComments]);
    }
}
?>
