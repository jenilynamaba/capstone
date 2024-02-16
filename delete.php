<?php
require_once 'Admin.php'; // Adjust the path accordingly

$admin = new Admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    
    if ($admin->deletePost($post_id)) {
        echo 'Post deleted successfully.';
    } else {
        echo 'Error deleting post.';
    }
} else {
    echo 'Invalid request.';
}
