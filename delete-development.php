<?php
// Include Database class and start the session
require_once 'Database.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

// Check if the post ID is provided in the URL
if (!isset($_GET['postId'])) {
    header('Location: index.php');
    exit();
}

$postId = $_GET['postId'];

// Fetch the post from the database
$database = new Database();
$post = $database->getPostById($postId);

// Check if the post exists
if (!$post) {
    header('Location: index.php');
    exit();
}

// Check if the logged-in user is the author of the post
if ($_SESSION['user']['id'] !== $post['user_id']) {
    header('Location: index.php');
    exit();
}

// Check if the form is submitted for deleting the post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteConfirmed'])) {
    // Delete the post from the database
    $database->deletePost($postId);

    // Redirect to the main page after deletion
    header('Location: development.php');
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Development</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css -->
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./CSS/dashboard.css">
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
    <!-- Include your navigation bar here if needed -->

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        Delete Development Post
                    </div>
                    <div class="card-body">
                        <!-- Display the current post content and ask for confirmation -->
                        <p>Are you sure you want to delete the following post?</p>
                        <div class="mb-3">
                            <strong>Author:</strong> <?php echo htmlspecialchars($post['author_name']); ?>
                        </div>
                        <div class="mb-3">
                            <strong>Content:</strong> <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </div>
                        <form method="post">
                            <button type="submit" name="deleteConfirmed" class="btn btn-danger">Yes, Delete Post</button>
                            <a href="index.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- custom js -->
    <script src="./JS/script.js"></script>
    <script src="./JS/createPost_ajax.js"></script>
    <script src="./JS/kyc.js"></script>
    <script src="./JS/reset_password_success.js"></script>
    <script src="./JS/load_reset_button.js"></script>
    <script src="./JS/upload_icon.js"></script>

</body>
</html>

