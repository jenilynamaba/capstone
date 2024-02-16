<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId']) && isset($_POST['content'])) {
    $postId = $_POST['postId'];
    $content = $_POST['content'];

    $database = new Database();
    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("UPDATE posts SET content = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$content, $postId, $_SESSION['user']['id']]);

    header('Location: main_profile.php');
    exit();
} else {
    header('Location: main_profile.php');
    exit();
}
?>
