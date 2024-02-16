<?php
require 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $newStatus = $_POST['status'];

    if ($newStatus !== 'approved' && $newStatus !== 'canceled') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
        exit();
    }

    $database = new Database();
    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':id', $userId);

    try {
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
