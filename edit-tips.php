<?php
session_start();

require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if (!isset($_GET['post_id'])) {
    header('Location: tips.php');
    exit();
}

$postId = $_GET['post_id'];
$database = new Database();

$post = $database->getPostById($postId);

if (!$post || $post['user_id'] !== $_SESSION['user']['id']) {
    header('Location: tips.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editPostContent'])) {
    try {
        $newContent = htmlspecialchars($_POST['newContent']);
        $database->updatePostContent($postId, $newContent);

        echo '<div class="alert alert-success mt-4" role="alert">';
        header("Location: tips.php");
        echo '</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger mt-4" role="alert">';
        echo 'Error: ' . $e->getMessage();
        echo '</div>';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tips and Facts</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css -->
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./CSS/dashboard.css">
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Add some custom styles if needed */
        @media (max-width: 767px) {
            .navbar-nav {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="./IMG/logo.png" alt="Logo" width="80" height="80" class="d-inline-block align-text-top">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <div class="custom-toggler-icon">
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="notificationsDropdown" id="notificationsList">
                        
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="inboxDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-inbox"></i>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="inboxDropdown">
                            <li><a class="dropdown-item" href="#"></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <form method="post">
                            <button class="btn btn-link nav-link" type="submit" name="logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-5">
        <div class="row">
            <!-- First Column -->
            <div class="col-md-3 fixed-column">
                <div class="card">
                    <div class="card-body">
                        <?php
                            if (isset($_SESSION['user']['first_name']) && isset($_SESSION['user']['last_name'])) {
                                $firstName = $_SESSION['user']['first_name'];
                                $lastName = $_SESSION['user']['last_name'];
                                echo '<p><i class="fas fa-user"></i> ' . htmlspecialchars($firstName . ' ' . $lastName) . '</p>';
                            } else {
                                echo '<p><i class="fas fa-user"></i> User Name</p>';
                            }
                        ?>
                        <!-- Posts -->
                        <h5 class="mt-4"><i class="far fa-newspaper"></i><a href="dashboard.php" class="link"> Posts</a></h5>
                        <hr/ style="color:red;">
                        <!-- Friends -->
                        <h5 class="mt-4"><i class="fas fa-users"></i><a href="friends.php" class="link"> Friends</a></h5>
                        <hr/ style="color:red;">
                        <!-- About Us -->
                        <h5 class="mt-4"><i class="fas fa-info-circle"></i><a href="about.php" class="link"> About Us</a></h5>
                        <hr/ style="color:red;">
                        <!-- Tips and Facts -->
                        <h5 class="mt-4"><i class="fas fa-lightbulb"></i><a href="tips.php" class="link" style="color:#ff7c80;"> Tips and Facts</a> <i class="fas fa-arrow-left"></i></h5>
                        <hr/ style="color:red;">
                        <!-- Testimonies -->
                        <h5 class="mt-4"><i class="fas fa-comments"></i><a href="testimonies.php" class="link"> Testimonies</a></h5>
                        <hr/ style="color:red;">
                        <!-- Monthly Development -->
                        <h5 class="mt-4"><i class="fas fa-chart-line"></i><a href="development.php" class="link"> Monthly Development</a></h5>
                    </div>
                </div>
            </div>

            <!-- Second Column -->
            <div class="col-md-6">
                <div class="card mt-4">
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="newContent" class="form-label">Edit Post</label>
                                <textarea class="form-control" id="newContent" name="newContent" rows="5" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                            </div>
                            <button type="submit" name="editPostContent" class="btn btn-primary">Update Post</button>
                        </form>
                    </div>
                </div>
            </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#editPostForm').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'edit_post_tips.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log('Edit Post Success:', response);
                        $('#editPostModal').modal('hide');
                        location.reload();
                    },
                    error: function(error) {
                        console.error('Edit Post Error:', error);
                    }
                });
            });

            $('#confirmDeletePostBtn').click(function() {
                var postId = $(this).data('postid');
                console.log('Post ID for Deletion:', postId);

                $.ajax({
                    type: 'POST',
                    url: 'delete_post_tips.php',
                    data: { postId: postId },
                    success: function(response) {
                        console.log('Delete Post Success:', response);
                        $('#deletePostModal').modal('hide');
                        // location.reload();
                    },
                    error: function(error) {
                        console.error('Delete Post Error:', error);
                    }
                });
            });
        });
    </script>

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


