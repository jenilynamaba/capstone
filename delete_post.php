<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId'])) {
    $postId = $_POST['postId'];

    $database = new Database();
    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user']['id']]);

    header('Location: main_profile.php');
    exit();
} else {
    header('Location: main_profile.php');
    exit();
}
?>
