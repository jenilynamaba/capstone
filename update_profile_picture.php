<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
    $userId = $_SESSION['user']['id'];
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['profilePicture']['name']);

    if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $uploadFile)) {
        $database = new Database();
        $pdo = $database->getConnection();

        $sql = "UPDATE users SET profile_picture = :profilePicture WHERE id = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':profilePicture', $_FILES['profilePicture']['name']);
        $stmt->bindParam(':userId', $userId);

        try {
            if ($stmt->execute()) {
                $_SESSION['user']['profile_picture'] = $_FILES['profilePicture']['name'];
                header('Location: main_profile.php');
                exit();
            } else {
                echo 'Failed to update profile picture in the database.';
            }
        } catch (PDOException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        echo 'Failed to move the uploaded file.';
    }
} else {
    header('Location: main_profile.php');
    exit();
}
?>
