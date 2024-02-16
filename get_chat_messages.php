<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    echo "User authentication failed.";
    exit;
}

$userId = $_GET['userId'];

$database = new Database();
$pdo = $database->getConnection();

$user_id = $_SESSION['user']['id'];

// Use JOIN to get sender's and receiver's information
$stmt = $pdo->prepare("
    SELECT chat_messages.*, sender.first_name AS sender_first_name, sender.last_name AS sender_last_name, receiver.first_name AS receiver_first_name, receiver.last_name AS receiver_last_name
    FROM chat_messages
    JOIN users AS sender ON chat_messages.sender_id = sender.id
    JOIN users AS receiver ON chat_messages.receiver_id = receiver.id
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$userId, $user_id, $user_id, $userId]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message) {
    $messageClass = ($message['sender_id'] == $_SESSION['user']['id']) ? 'sender-message' : 'receiver-message';
    $formattedDate = date('Y-m-d H:i:s', strtotime($message['created_at'])); // Format the date

    // Display sender's and receiver's names along with the message
    $senderName = ($message['sender_id'] == $_SESSION['user']['id'])
        ? 'You'
        : htmlspecialchars($message['sender_first_name'] . ' ' . $message['sender_last_name']);
    
    $receiverName = ($message['receiver_id'] == $_SESSION['user']['id'])
        ? 'You'
        : htmlspecialchars($message['receiver_first_name'] . ' ' . $message['receiver_last_name']);

    echo '<div class="message ' . $messageClass . '">';
    echo '<strong>' . $senderName . ' to ' . $receiverName . ':</strong> ' . htmlspecialchars($message['message']);
    echo '<br><span class="message-date">' . $formattedDate . '</span>';
    echo '</div>';
}
?>
