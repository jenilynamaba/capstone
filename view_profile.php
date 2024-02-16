<?php
    session_start();
    require_once 'Database.php';

    if (!isset($_SESSION['user'])) {
        header('Location: index.php');
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();

        header('Location: index.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- css -->
    <!-- <link rel="stylesheet" href="./css/style.css"> -->
    <link rel="stylesheet" href="./CSS/dashboard.css">
    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media (max-width: 767px) {
            .navbar-nav {
                flex-direction: column;
            }
        }
        .img-fluid{
            width:500px;
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
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i> <!-- Notification bell icon -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="notificationsDropdown" id="notificationsList">
                        </ul>
                    </li>

                    <!-- Inbox Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="inboxDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-inbox"></i> <!-- Inbox icon -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="inboxDropdown" id="inboxList">
                        </ul>
                    </li>

                    <li class="nav-item">
                        <form method="post">
                            <button class="btn btn-link nav-link" type="submit" name="logout">
                                <i class="fas fa-sign-out-alt"></i> <!-- Logout icon -->
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

                        require_once 'Database.php';

                        if (isset($_SESSION['user']['id']) && isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
                            $userId = $_SESSION['user']['id'];
                            $uploadDir = 'uploads/';
                            $uploadFile = $uploadDir . basename($_FILES['profilePicture']['name']);

                            if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $uploadFile)) {
                                $db = new Database();
                                $pdo = $db->getConnection();

                                $sql = "UPDATE users SET profile_picture = :profilePicture WHERE id = :userId";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':profilePicture', $_FILES['profilePicture']['name']);
                                $stmt->bindParam(':userId', $userId);

                                try {
                                    if ($stmt->execute()) {
                                        $_SESSION['user']['profile_picture'] = $_FILES['profilePicture']['name'];
                                    } else {
                                        echo 'Failed to update profile picture in the database.';
                                    }
                                } catch (PDOException $e) {
                                    echo 'Error: ' . $e->getMessage();
                                }
                            } else {
                                echo 'Failed to move the uploaded file.';
                            }
                        }
                            $defaultProfilePicture = '/uploads/default_profile_picture.jpg';

                            if (isset($_SESSION['user']['profile_picture'])) {
                                $profilePicture = 'uploads/' . htmlspecialchars($_SESSION['user']['profile_picture']);
                            } else {
                                $profilePicture = $defaultProfilePicture;
                            }
                            echo '<a href="main_profile.php" style="text-decoration:none;color:#2a2a2a;">
                                    <span><img src="' . htmlspecialchars($profilePicture) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">' . ' ' . htmlspecialchars($firstName . ' ' . $lastName) . '</span>
                                </a>';
                        } else {
                            echo '<p><i class="fas fa-user"></i> User Name</p>';
                        }

                        echo '<form action="" method="post" enctype="multipart/form-data">';
                        echo '    <input type="file" name="profilePicture" accept="image/*">';
                        echo '    <input type="submit" value="Upload">';
                        echo '</form>';
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
                        <h5 class="mt-4"><i class="fas fa-lightbulb"></i><a href="tips.php" class="link"> Tips and Facts</a></h5>
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
            <div class="col-md-6 scrollable-column">
            <?php


        if (!isset($_SESSION['user']['id'])) {
            header('Location: login.php');
            exit();
        }

        $userId = $_GET['userId'];

        $database = new Database();
        $pdo = $database->getConnection();
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $stmtPosts = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $stmtPosts->execute([$userId]);
        $posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
        ?>

    <?php

    require_once 'Database.php';

    if (!isset($_SESSION['user']['id'])) {
        header('Location: login.php');
        exit();
    }

    $userId = $_GET['userId'];

    $database = new Database();
    $pdo = $database->getConnection();
    $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $stmtPosts = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmtPosts->execute([$userId]);
    $posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div class="profile-info">
                    <?php
                    $profilePicture = isset($user['profile_picture']) ? 'uploads/' . htmlspecialchars($user['profile_picture']) : 'path_to_default_profile_picture.jpg';
                    echo '<img src="' . htmlspecialchars($profilePicture) . '" alt="Profile Picture" class="profile-picture" style="height: 150px; width: 150px; border-radius: 50%;">';
                    echo '<h4>' . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . '</h4>';
                    echo '<p><strong>Email</strong>: ' . htmlspecialchars($user['email']) . '</p>';
                    echo '<p><strong>Gender</strong>: ' . htmlspecialchars($user['gender']) . '</p>';
                    echo '<p><strong>Birthday</strong>: ' . htmlspecialchars($user['birthday']) . '</p>';
                    echo '<p><strong>Address</strong>: ' . htmlspecialchars($user['address']) . '</p>';
                    echo '<p><strong>Role</strong>: ' . htmlspecialchars($user['role']) . '</p>';

                    $stmtPosts = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
                    $stmtPosts->execute([$userId]);
                    $posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($posts as &$post) {
                        $postId = $post['id'];

                        $stmtComments = $pdo->prepare("SELECT * FROM comments WHERE post_id = ?");
                        $stmtComments->execute([$postId]);
                        $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($comments as &$comment) {
                            $commentId = $comment['id'];

                            $stmtReplies = $pdo->prepare("SELECT * FROM replies WHERE comment_id = ?");
                            $stmtReplies->execute([$commentId]);
                            $replies = $stmtReplies->fetchAll(PDO::FETCH_ASSOC);

                            $comment['replies'] = $replies;
                        }

                        $post['comments'] = $comments;
                    }

                    ?>
                </div>
            </div>

       <!-- User Posts Column -->
        <div class="col-lg-9 mt-5">
            <h5>Posts:</h5>
            <ul class="<!-- User Posts Column -->
            <div class="col-lg-9 mt-5">
                <ul class="list-group mb-4">
                    <?php foreach ($posts as $post) : ?>
                        <li class="list-group-item mb-3" style="border:solid 1px #6A6A6A;">
                            <?php echo htmlspecialchars($post['content']); ?>
                            <?php if (!empty($post['image_url'])) : ?>
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" class="img-fluid mb-3" alt="Post Image">
                            <?php endif; ?>
                            <p class="card-text"><small class="text-muted"><?php echo formatTimestamp($post['created_at']); ?></small></p>

                            <?php foreach ($post['comments'] as $comment) : ?>
                                <div class="comment">
                                    <?php echo htmlspecialchars($comment['content']); ?>
                                    <p class="card-text"><small class="text-muted"><?php echo formatTimestamp($comment['created_at']); ?></small></p>

                                    <?php foreach ($comment['replies'] as $reply) : ?>
                                        <div class="reply">
                                            <?php echo htmlspecialchars($reply['content']); ?>
                                            <p class="card-text"><small class="text-muted"><?php echo formatTimestamp($reply['created_at']); ?></small></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <?php
            function formatTimestamp($timestamp)
            {
                return date('F j, Y, g:i a', strtotime($timestamp));
            }
            ?>
        </div>

    <a href="index.php" class="btn btn-primary">Go Back</a>


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
    <script src=".JS/comment_ajax.js"></script>

    <script>
        $(document).ready(function() {
            $('.reply-text').on('click', function() {
                $(this).siblings('.reply-form').toggle();
            });
        });
    </script>
    

</body>

</html>


