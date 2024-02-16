<?php
require_once 'Database.php';

if (isset($_POST['email']) && isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND reset_token = :token");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET pword = :pword WHERE email = :email");
            $stmt->bindParam(':pword', $hashedPassword);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $stmt = $pdo->prepare("UPDATE users SET reset_token = NULL WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            echo 'Password updated successfully.';
        } else {
            echo 'New password and confirm password do not match.';
        }
    } else {
        echo 'Invalid reset link. Please try again.';
    }
} else {
    echo 'Invalid request.';
}
