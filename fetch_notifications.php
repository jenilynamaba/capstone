<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$userId = $_SESSION['user']['id'];

// Assuming you have a table named 'notifications'
$sql = "SELECT comments.*, users.first_name, users.last_name, users.profile_picture
        FROM comments
        JOIN users ON comments.user_id = users.id
        JOIN posts ON comments.post_id = posts.id
        WHERE posts.user_id = :userId
            AND comments.user_id <> :userId
        ORDER BY comments.created_at DESC";


try {
    $database = new Database();
    $pdo = $database->getConnection();

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['notifications' => $notifications]);
    exit();
} catch (PDOException $e) {
    error_log('Failed to fetch notifications: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    exit();
}
?>
