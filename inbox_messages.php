<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'User authentication failed.']);
    exit;
}

$user_id = $_SESSION['user']['id'];

$database = new Database();
$pdo = $database->getConnection();

$stmt = $pdo->prepare("SELECT sender_id, message, created_at FROM chat_messages WHERE receiver_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format messages as needed
$formattedMessages = [];
foreach ($messages as $message) {
    $formattedMessages[] = [
        'sender_id' => $message['sender_id'],
        'sender_name' => getUsernameById($pdo, $message['sender_id']), // Function to get username based on user ID
        'message' => $message['message'],
        'created_at' => $message['created_at'],
    ];
}

echo json_encode($formattedMessages);

function getUsernameById($pdo, $user_id)
{
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ? $user['first_name'] . ' ' . $user['last_name'] : 'Unknown User';
}
?>
