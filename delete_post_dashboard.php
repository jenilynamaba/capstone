<?php
session_start();
require_once 'Database.php';

// Check if the user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

// Log user ID for debugging
error_log('User ID: ' . $_SESSION['user']['id']);

// Check if it's a POST request and if the postId is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId'])) {
    $postId = $_POST['postId'];

    // Instantiate the Database class
    $database = new Database();
    $pdo = $database->getConnection();

    try {
        // Use a prepared statement with named placeholders for clarity and security
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :postId AND user_id = :userId");
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
        $stmt->execute();

        // Check if any row was affected
        $rowCount = $stmt->rowCount();
        if ($rowCount > 0) {
            // Post deleted successfully
            header('Location: dashboard.php');
            exit();
        } else {
            // No matching post for the given user
            header('Location: dashboard.php?error=post_not_found');
            exit();
        }
    } catch (PDOException $e) {
        // Log and handle database errors
        error_log('Database Error: ' . $e->getMessage());
        header('Location: dashboard.php?error=database_error');
        exit();
    }
} else {
    // Redirect to dashboard if the request is not valid
    header('Location: dashboard.php');
    exit();
}
?>

