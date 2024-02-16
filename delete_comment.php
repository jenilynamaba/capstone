<?php
session_start();
require_once 'Database.php';

// Check if the user is logged in
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the comment ID from the form data
    $commentId = $_POST['commentId'];

    // Fetch the comment from the database based on $commentId and verify user ownership
    $database = new Database();
    $pdo = $database->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :commentId AND user_id = :userId");
    $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment) {
        $stmtDelete = $pdo->prepare("DELETE FROM comments WHERE id = :commentId");
        $stmtDelete->bindParam(':commentId', $commentId, PDO::PARAM_INT);
        $stmtDelete->execute();

        header('Location: dashboard.php');
        exit();
    } else {
        echo 'You do not have permission to delete this comment.';
    }
} else {
    echo 'Are you sure you want to delete this comment?';
    echo '<form method="post" action="delete_comment.php">';
    echo '<input type="hidden" name="commentId" value="' . htmlspecialchars($_GET['commentId']) . '">';
    echo '<input type="submit" value="Yes, Delete">';
    echo '</form>';
}
?>

<style scoped>
          body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: blue;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        .confirmation-box {
            background-color: #d3d3d3;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .confirmation-message {
            margin-bottom: 10px;
            color: yellow;
        }

        form {
            display: inline-block;
        }

        input[type="submit"] {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #bd2130;
        }

    </style>
