<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'Database.php';

    $postId = $_POST['postId'];

    try {
        $database = new Database();

        $database->deletePost($postId);

        header('Location: tips.php');
        exit();
    } catch (Exception $e) {
        echo 'An error occurred while deleting the post. Please try again later.';
        exit();
    }
}
?>
