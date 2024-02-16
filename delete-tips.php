<?php
session_start();

require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['post_id'])) {
    header('Location: tips.php');
    exit();
}

$postId = $_GET['post_id'];
$database = new Database();

$post = $database->getPostById($postId);

if (!$post || $post['user_id'] !== $_SESSION['user']['id']) {
    header('Location: tips.php');
    exit();
}

try {
    $database->deletePost($postId);
    echo '<div class="alert alert-success mt-4" role="alert">';
    header("Location:tips.php");
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger mt-4" role="alert">';
    echo 'Error: ' . $e->getMessage();
    echo '</div>';
}
?>