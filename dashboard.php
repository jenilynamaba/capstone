<?php
session_start();
require_once 'Database.php';

// Check if the user is logged in
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
        .unread{
            color: red;
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
                        <a href="#" class="btn btn-link nav-link dropdown-toggle" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="badge badge-light" id="notificationBadge">0</span>
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
                            <!-- Messages will be displayed here -->
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
                        <h5 class="mt-4"><i class="far fa-newspaper"></i><a href="dashboard.php" class="link" style="color:#ff7c80;"> Posts</a> <i class="fas fa-arrow-left"></i></h5>
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
            <div class="col-md-7 scrollable-column">
                <div class="card">
                    <div class="card-body post">
                        <div id="postResult"></div>
                        <h5 class="card-title">Create Post</h5>
                        <?php
                        // Allow posting only for users with "Approved" status
                        if ($_SESSION['user']['status'] === 'Approved') {
                            echo '<form id="postForm">';
                            echo '    <div class="mb-3 position-relative">';
                            echo '        <textarea class="form-control" id="postContent" name="postContent" rows="3" placeholder="What\'s on your mind?"></textarea>';
                            echo '        <i class="fas fa-image fa-lg position-absolute upload-icon"></i>';
                            echo '        <input class="form-control visually-hidden" type="file" id="postImage" name="postImage">';
                            echo '    </div>';
                            echo '    <button type="submit" class="btn btn-primary">Post</button>';
                            echo '</form>';
                        } else {
                            echo '<p class="text-danger">You cannot post content until your account is approved.</p>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Display Posts -->
                <h4 style="color:#333;margin:30px 0 15px 0;">Timeline</h4>
                <?php
                    require_once 'Database.php';

                    $database = new Database();
                    $pdo = $database->getConnection();

                    $stmt = $pdo->query("SELECT posts.*, users.first_name, users.last_name, users.profile_picture FROM posts JOIN users ON posts.user_id = users.id WHERE (posts.page IS NULL OR posts.page = '') ORDER BY posts.created_at DESC");
                    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $testimony = [];

                foreach ($posts as $post) {
                    if ($post['page'] == 'testimonies') {
                        continue;
                    }
                    if (empty($testimony)) {
                        $testimony = $database->getPostsByPageAndRole('testimonies', $_SESSION['user']['role']);
                    }           
                    echo '<div class="card mb-3">';
                    echo '<div class="card-body posted">';
                    if ($_SESSION['user']['id'] == $post['user_id']) {
                        echo '<a href="main_profile.php">';
                    } else {    
                        echo '<a href="view_profile.php?userId=' . htmlspecialchars($post['user_id']) . '">';
                    }

                    echo '<span><img src="uploads/' . htmlspecialchars($post['profile_picture']) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;"></span>'.' <span class="card-title" style="text-decoration:none;">' . htmlspecialchars($post['first_name'] . ' ' . $post['last_name']) . '</span>';
                    echo '</a>';

                    echo '<span><p class="card-text mt-3 mb-3">' . htmlspecialchars($post['content']) . '</p></span>';

                    if (!empty($post['image_url'])) {
                        echo '<a href="' . htmlspecialchars($post['image_url']) . '" target="_blank">'; 
                        echo '<img src="' . htmlspecialchars($post['image_url']) . '" class="img-fluid mb-3" alt="Post Image" style="width: 100%; height:400px;">';
                        echo '</a>';
                    }
                    if ($post['user_id'] === $_SESSION['user']['id']) {
                        echo '<form action="edit_post_dashboard.php" method="post" class="d-inline">';
                        echo '    <input type="hidden" name="postId" value="' . htmlspecialchars($post['id']) . '">';
                        echo '    <button type="submit" class="btn btn-sm btn-primary">Edit</button>';
                        echo '</form>';

                        echo '<form action="delete_post_dashboard.php" method="post" class="d-inline">';
                        echo '    <input type="hidden" name="postId" value="' . htmlspecialchars($post['id']) . '">';
                        echo '    <button type="submit" class="btn btn-sm btn-danger">Delete</button>';
                        echo '</form>';
                    }

                    $postedDateTime = isset($post['created_at']) ? date("F j, Y, g:i a", strtotime($post['created_at'])) : '';

                    echo '<p class="card-text"><small class="text-muted">' . $postedDateTime . '</small></p>';


                    echo '<form action="process_comment.php" method="post" class="mb-3 comment-form" id="commentForm' . htmlspecialchars($post['id']) . '">';
                    echo '    <div class="input-group mb-3">';
                    echo '        <input type="text" class="form-control" id="commentContent" name="commentContent" placeholder="Your Comment" aria-label="Your Comment" aria-describedby="basic-addon2" autocomplete="off">';
                    echo '        <div class="input-group-append">';
                    echo '            <input type="hidden" name="postId" value="' . htmlspecialchars($post['id']) . '">';
                    echo '            <button class="btn btn-primary" type="button" onclick="submitCommentForm(' . htmlspecialchars($post['id']) . ')">Comment</button>';
                    echo '        </div>';
                    echo '    </div>';
                    echo '</form>';
                    // display comment output async
                    echo '<div id="commentsContainer' . htmlspecialchars($post['id']) . '"></div>';

                    $stmt = $pdo->prepare("SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = :postId ORDER BY comments.created_at DESC");
                    $stmt->bindParam(':postId', $post['id'], PDO::PARAM_INT);
                    $stmt->execute();
                    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        //display comments
                        foreach ($comments as $comment) {
                            echo '<div class="mb-2 comment-container">';
                            echo '<img src="uploads/' . htmlspecialchars($comment['profile_picture']) . '" alt="Profile Picture" class="profile-picture" style="height:50px;width:50px;border-radius:50%;">';
                            echo '<strong class="card-title">'.' '. htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']) . '</strong> <br><br>' . htmlspecialchars($comment['content']).'<br>';
                            
                            if ($_SESSION['user']['id'] == $comment['user_id']) {
                                echo '<a href="edit_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Edit</a> ';
                                echo '<a href="delete_comment.php?commentId=' . htmlspecialchars($comment['id']) . '">Delete</a>';
                            }
                            
                            $postedDateTime = isset($post['created_at']) ? date("F j, Y, g:i a", strtotime($post['created_at'])) : '';
                            echo '<p class="card-text mt-2"><small class="text-muted">' . $postedDateTime . '</small></p>';
                            
                            // Removed the reply-related code
                    
                            echo '</div>';
                        }
                    echo '</div>';
                    echo '</div>';
                }
                ?>
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

// Declare notificationBell globally
var notificationBell;

// Function to update the notification bell
function updateNotificationBell(notifications) {
    var notificationsList = $('#notificationsList');
    notificationBell = $('#notificationsDropdown i');
    var notificationBadge = $('#notificationBadge');

    //console.log('Unread Notifications:', notifications.length);

    // Check if there are new notifications
    if (notifications.length > 0) {
        // Clear existing notifications
        notificationsList.empty();

        // Iterate through new notifications
        notifications.forEach(function (notification) {
            // Customize this part based on your notification content
            var notificationItem = '<li><a class="dropdown-item" href="view_post.php?postId=' + notification.post_id + '">' +
                                '<small style="color:#ff9fa2;">Someone commented on your post:</small><br>' + 
                                notification.content + '</a></li>';
            notificationsList.append(notificationItem);
        });

        // Update the badge content with the number of unread notifications
        notificationBadge.text(notifications.length);

        // Set the color of the bell icon to red
        notificationBell.css('color', 'red');

        // Store the unread state in local storage
        localStorage.setItem('unreadNotifications', 'true');
    } else {
        // Reset the badge to '0'
        notificationBadge.text('0');

        // Reset the color of the bell icon
        notificationBell.css('color', '');

        // Remove the unread state from local storage
        localStorage.removeItem('unreadNotifications');
    }
}

// Event listener for the bell icon click
$('#notificationsDropdown').on('click', function () {
    // Reset the color of the bell icon
    notificationBell.css('color', '');

    // Remove the unread state from local storage
    localStorage.removeItem('unreadNotifications');
});

// Check local storage for the unread state on page load
$(document).ready(function () {
    var hasUnreadNotifications = localStorage.getItem('unreadNotifications');
    if (hasUnreadNotifications === 'true' && notificationBell) {
        // Set the color of the bell icon to red if there are unread notifications
        notificationBell.css('color', 'red');
    }
});


        // Fetch notifications initially
        fetchNotifications();

        // Set up an interval to fetch notifications periodically
        setInterval(fetchNotifications, 12000);
    });
    </script>


<script>
    // Variable to track if there are unread messages
    var unreadMessages = false;

    // Function to fetch and update chat messages in the inbox
    function updateInbox() {
        var inboxList = $('#inboxList');
        var inboxDropdown = $('#inboxDropdown');

        // Make an AJAX request to fetch chat messages
        $.ajax({
            type: 'GET',
            url: 'inbox_messages.php', // Replace with the actual endpoint for fetching messages
            dataType: 'json',
            success: function (messages) {
                inboxList.empty(); // Clear existing messages
                unreadMessages = false; // Reset the unreadMessages variable

                if (messages.length > 0) {
                    messages.forEach(function (message) {
                        // Customize this part based on your message content
                        var messageItem = '<li><a class="dropdown-item" href="friends.php?FindItHere">' +
                            '<strong>' + message.sender_name + ':</strong> ' + message.message +
                            '</a></li>';
                        inboxList.append(messageItem);
                        unreadMessages = true; // Set to true if there are unread messages
                    });
                } else {
                    inboxList.append('<li><span class="dropdown-item">No new messages</span></li>');
                }

                // Update the inbox icon color based on unreadMessages
                updateInboxIconColor();
            },
            error: function () {
                console.log('Failed to fetch inbox messages.');
            }
        });
    }

    // Function to update the color of the inbox icon
    function updateInboxIconColor() {
        var inboxDropdown = $('#inboxDropdown');

        if (unreadMessages) {
            // If there are unread messages, set the color to red
            inboxDropdown.addClass('text-danger');
        } else {
            // If there are no unread messages, reset the color
            inboxDropdown.removeClass('text-danger');
        }
    }

    // Call the function initially to populate the inbox
    updateInbox();

    // Set interval to periodically check for new messages
    setInterval(updateInbox, 12000); 

    // Attach a click event to the inbox icon to reset the color when clicked
    $('#inboxDropdown').on('click', function () {
        unreadMessages = false; // Reset the unreadMessages variable
        updateInboxIconColor(); // Update the color immediately
    });
</script>



</body>
</html>


