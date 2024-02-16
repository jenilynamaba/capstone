<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['postId'])) {
    $postId = $_POST['postId'];

    $database = new Database();
    $pdo = $database->getConnection();

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $_SESSION['user']['id']]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Post</title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>

        <body class="container mt-5">
            <h1>Edit Post</h1>
            <form action="update_post.php" method="post">
                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <div class="mb-3">
                    <label for="content" class="form-label">Post Content</label>
                    <textarea class="form-control" id="content" name="content" rows="4" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Post</button>
            </form>
        </body>

        </html>
        <?php
    } else {
        header('Location: main_profile.php');
        exit();
    }
} else {
    header('Location: main_profile.php');
    exit();
}
?>
