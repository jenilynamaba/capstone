<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $_GET['commentId'];

    $updatedContent = $_POST['commentContent'];

    $database = new Database();
    $pdo = $database->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :commentId AND user_id = :userId");
    $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment) {
        $stmtUpdate = $pdo->prepare("UPDATE comments SET content = :content WHERE id = :commentId");
        $stmtUpdate->bindParam(':content', $updatedContent, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':commentId', $commentId, PDO::PARAM_INT);
        $stmtUpdate->execute();

        header('Location: dashboard.php');
        exit();
    } else {
        echo 'You do not have permission to edit this comment.';
    }
} else {
    $commentId = $_GET['commentId'];

    $database = new Database();
    $pdo = $database->getConnection();
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = :commentId AND user_id = :userId");
    $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $_SESSION['user']['id'], PDO::PARAM_INT);
    $stmt->execute();
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($comment) {
        echo '<form method="post" action="edit_comment.php?commentId=' . htmlspecialchars($commentId) . '">';
        echo 'Comment: <input type="text" name="commentContent" value="' . htmlspecialchars($comment['content']) . '">';
        echo '<input type="submit" value="Save">';
        echo '</form>';
    } else {
        echo 'You do not have permission to edit this comment.';
    }
}
?>

<head>
<!-- Include Bootstrap CSS and JS here -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
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
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"] {
            width: 50%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: red;

        }

    </style>
    </head>