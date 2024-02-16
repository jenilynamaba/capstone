<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'Database.php';

    $postId = $_POST['postId'];
    $newContent = $_POST['editPostContent'];

    try {
        $database = new Database();

        $database->updatePost($postId, $newContent);

        header('Location: tips.php');
        exit();
    } catch (Exception $e) {
        echo 'An error occurred while updating the post. Please try again later.';
        exit();
    }
}
?>
