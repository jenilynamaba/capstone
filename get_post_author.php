<?php
session_start();
require_once 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['postId'])) {
    $postId = $_GET['postId'];

    $database = new Database();
    $pdo = $database->getConnection();

    try {
        $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = :postId");
        $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo $result['user_id'];
        } else {
            echo '';  // Return an empty string if no author found
        }
    } catch (PDOException $e) {
        error_log('Error fetching post author ID: ' . $e->getMessage());
        echo '';  // Return an empty string on error
    }
} else {
    echo '';  // Return an empty string if the request is not valid
}
?>
