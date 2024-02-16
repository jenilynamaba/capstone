<?php
require 'Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['userId'];
    $editedFirstName = $_POST['editFirstName'];
    $editedLastName = $_POST['editLastName'];
    $editedEmail = $_POST['editEmail'];

    $db = new Database();
    $conn = $db->getConnection();

    $sql = "UPDATE users SET first_name = :firstName, last_name = :lastName, email = :email WHERE id = :userId";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':firstName', $editedFirstName);
    $stmt->bindParam(':lastName', $editedLastName);
    $stmt->bindParam(':email', $editedEmail);
    $stmt->bindParam(':userId', $userId);

    if ($stmt->execute()) {
        header('Location: admin.php');
        exit();
    } else {
        echo "Error updating user details";
    }
}
?>
