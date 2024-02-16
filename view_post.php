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

if (!isset($_GET['postId'])) {
    header('Location: dashboard.php'); // Redirect to the dashboard if postId is not provided
    exit();
}

$postId = $_GET['postId'];

$database = new Database();
$pdo = $database->getConnection();

// Fetch post details
$stmtPost = $pdo->prepare("SELECT posts.*, users.first_name, users.last_name, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmtPost->execute([$postId]);
$post = $stmtPost->fetch(PDO::FETCH_ASSOC);

// Fetch comments for the post
$stmtComments = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
$stmtComments->execute([$postId]);
$comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> -->
    <style>
        @media (max-width: 767px) {
            .navbar-nav {
                flex-direction: column;
            }
        }
        #searchForm {
            position: relative;
        }
        #searchResults {
            position: absolute;
            top: 100%;
            left: 0;
            width: 97%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #bb6b6e;
            background-color: #bb6b6e;
            z-index: 1000;
            display: none;
            border-radius:5px;
            color:#fff;
        }
        .search-result {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .search-result:hover {
            background-color: #ff9fa2;
        }
        .badge{
            font-size:9px;
            font-weight:300;
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
                 <!-- Search Bar -->
                <form id="searchForm" class="d-flex mx-auto">
                    <input id="searchInput" class="form-control me-2" type="search" placeholder="Search Name or Email" aria-label="Search" autocomplete="off">
                    <div id="searchResults"></div>
                </form>

                <ul class="navbar-nav">
                    <!-- Notifications dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i> <!-- Notification bell icon -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="notificationsDropdown" id="notificationsList">
                            <!-- Notifications will be displayed here -->
                        </ul>
                    </li>
                    <!-- Inbox Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="inboxDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-inbox"></i>
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
                            $statusIndicator = '';
                            $userStatus = $_SESSION['user']['status'];

                            if ($userStatus === 'Approved') {
                                $statusIndicator = '<span class="badge bg-success">Approved</span>';
                            } elseif ($userStatus === 'Declined') {
                                $statusIndicator = '<span class="badge bg-danger">Declined</span>';
                            } else {
                                $statusIndicator = '<span class="badge bg-warning text-dark">Pending</span>';
                            }

                            echo '<a href="main_profile.php" style="text-decoration:none;color:#2a2a2a;">
                                    <span>
                                        <img src="' . htmlspecialchars($profilePicture) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">' . ' ' . htmlspecialchars($firstName . ' ' . $lastName) . ' ' . $statusIndicator . '
                                    </span>
                                </a>';
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
            <div class="col-md scrollable-column">
                <!-- <div class="card">
                    <div class="card-body post">
                        <div id="postResult"></div>
                        <h5 class="card-title">Create Post</h5>
                        <form id="postForm">
                            <div class="mb-3 position-relative">
                                <textarea class="form-control" id="postContent" name="postContent" rows="3" placeholder="What's on your mind?"></textarea>
                                
                                <i class="fas fa-image fa-lg position-absolute upload-icon"></i>
                                
                                <input class="form-control visually-hidden" type="file" id="postImage" name="postImage">
                            </div>
                            <button type="submit" class="btn btn-primary">Post</button>
                        </form>
                    </div>
                </div> -->

            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <div class="card">
                            <div class="card-body">
                                <h2 class="card-title"><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></h2>
                                <p class="card-text"><?php echo htmlspecialchars($post['content']); ?></p>
                                <?php
                                if (!empty($post['image_url'])) {
                                    echo '<img src="' . htmlspecialchars($post['image_url']) . '" class="img-fluid mb-3" alt="Post Image" style="width: 100%; height:400px;">';
                                }
                                ?>
                                <p class="card-text">
                                    <small class="text-muted">Posted on <?php echo date("F j, Y, g:i a", strtotime($post['created_at'])); ?></small>
                                </p>
                            </div>
                        </div>

                        <!-- Display comments -->
                        <div class="mt-4">
                            <h3>Comments</h3>
                            <?php foreach ($comments as $comment) : ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <img src="uploads/<?php echo htmlspecialchars($comment['profile_picture']); ?>" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">
                                        <strong class="card-title"><?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></strong><br>
                                        <?php echo htmlspecialchars($comment['content']); ?><br>
                                        <small class="text-muted"><?php echo date("F j, Y, g:i a", strtotime($comment['created_at'])); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Add a comment form -->
                        <form action="process_comment.php" method="post" class="mb-3 comment-form" id="commentForm">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="commentContent" name="commentContent" placeholder="Your Comment" aria-label="Your Comment" aria-describedby="basic-addon2" autocomplete="off">
                                <div class="input-group-append">
                                    <input type="hidden" name="postId" value="<?php echo htmlspecialchars($postId); ?>">
                                    <button class="btn btn-primary" type="submit">Comment</button>
                                </div>
                            </div>
                        </form>
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
    <script src="./JS/fetch_noti.js"></script>
    <script src="./JS/reply_toggle.js"></script>
    <script src="./JS/search.js"></script>
    
    <script>
        function submitCommentForm(postId) {
            var formData = new FormData(document.getElementById('commentForm' + postId));

            $.ajax({
                type: "POST",
                url: "process_comment.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    fetchLastComment(postId);

                    clearCommentForm(postId);
                }
            });
        }
        function fetchLastComment(postId) {
            $.ajax({
                type: "POST",
                url: "fetch_last_comment.php",
                data: { postId: postId },
                success: function (data) {
                    $("#commentsContainer" + postId).append(data);
                }
            });
        }
        function clearCommentForm(postId) {
            $("#commentForm" + postId)[0].reset();
        }
        function loadReplyForm(commentId, parentId, postId) {
            $.ajax({
                type: "GET",
                url: "reply_form.php",
                success: function (data) {
                    $("#replyFormContainer" + commentId).html(data);

                    $("#commentId").val(commentId);
                    $("#parentId").val(parentId);
                    $("#postId").val(postId);

                    $("#replyFormContainer" + commentId).show();
                }
            });
        }
        function submitReplyForm() {
            var formData = new FormData(document.getElementById('replyForm'));

            $.ajax({    
                type: "POST",
                url: "process_comment.php",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    fetchLastReply();
                    clearReplyForm();
                }
            });
        }
        function clearReplyForm() {
            $("#replyForm")[0].reset();
        }
    </script>

    <script>
    $(document).ready(function () {
        // Function to fetch notifications
        function fetchNotifications() {
            $.ajax({
                type: "POST",
                url: "fetch_notifications.php",
                success: function (data) {
                    updateNotificationBell(data.notifications);
                },
                error: function () {
                    console.error('Failed to fetch notifications');
                }
            });
        }

        // Function to update the notification bell
        function updateNotificationBell(notifications) {
            var notificationsList = $('#notificationsList');

            notificationsList.empty(); // Clear existing notifications

            if (notifications.length > 0) {
                notifications.forEach(function (notification) {
                    // Customize this part based on your notification content
                    var notificationItem = '<li><a class="dropdown-item" href="view_post.php?postId=' + notification.post_id + '">' +
                                        '<small style="color:#ff9fa2;">Someone commented on your post:</small><br>' + 
                                        notification.content + '</a></li>';
                    notificationsList.append(notificationItem);
                });
            } else {
                notificationsList.append('<li><span class="dropdown-item">No new notifications</span></li>');
            }
        }

        // Fetch notifications initially
        fetchNotifications();

        // Set up an interval to fetch notifications periodically
        setInterval(fetchNotifications, 5000);
    });
    </script>
</body>

</html>
