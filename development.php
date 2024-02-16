<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$userRole = $_SESSION['user']['role'];

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitContent'])) {
    try {
        $newContent = htmlspecialchars($_POST['newContent']);
        $database = new Database();

        if ($userRole === 'medicalStaff') {
            $userId = $_SESSION['user']['id'];
            $database->insertPost($userId, $newContent, 'development');

            // echo '<div class="alert alert-success mt-4" role="alert">';
            // echo 'Content submitted successfully!';
            // echo '</div>';
        } else {
            echo '<div class="alert alert-danger mt-4" role="alert">';
            echo 'Only medical staff can submit content.';
            echo '</div>';
        }
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
    <title>Monthly Development</title>
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
                        // echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="main_profile.php">View Profile</a>';
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
                        <h5 class="mt-4"><i class="fas fa-chart-line"></i><a href="development.php"  class="link" style="color:#ff7c80; " class="link"> Monthly Development</a> <i class="fas fa-arrow-left"></i></h5>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>

    <div id="app2">
    <h2>Note: These are arbitrary values and not based on real medical data</h2>
    <h4>This contains a reminder that the values used to obtain the estimates do not originate from real medical data and are solely used for demonstration purposes. These values are set in the code to showcase the functionality of the application and should not be used as a final basis for medical assessment.</h4>

    <h1>Baby Development Estimator</h1>
    <label for="age">PLEASE TYPE BABY'S MONTH:</label>
    <input v-model.number="age" type="number" id="age" min="0" @input="calculateEstimates">
  
    <div v-if="developmentMilestones.length > 0">
      <h3>Developmental Milestones</h3>
      <ul>
        <li v-for="(milestone, index) in developmentMilestones" :key="index">{{ milestone }}</li>
      </ul>
    </div>
  
    <div v-if="estimatedWeight !== null && estimatedHeight !== null">
      <h3>Estimated Weight and Height</h3>
      <p>Estimated Weight: {{ estimatedWeight.toFixed(2) }} kg</p>
      <p>Estimated Height: {{ estimatedHeight.toFixed(2) }} cm</p>
    </div>
  </div>
  
  <script>
    new Vue({
      el: '#app2',
      data() {
        return {
          age: null,
          developmentMilestones: [],
          estimatedWeight: null,
          estimatedHeight: null,
        };
      },
      methods: {
        calculateEstimates() {
          // Use a simple formula for demonstration purposes
          // Note: These are arbitrary values and not based on real medical data
          const baseWeight = 3.5; // kg
          const weightIncreasePerMonth = 0.5; // kg
          const baseHeight = 50; // cm
          const heightIncreasePerMonth = 2; // cm
  
          if (this.age !== null && this.age >= 0) {
            this.estimatedWeight = baseWeight + weightIncreasePerMonth * this.age;
            this.estimatedHeight = baseHeight + heightIncreasePerMonth * this.age;
  
            // Estimate development milestones
            this.developmentMilestones = this.calculateDevelopmentMilestones();
          } else {
            this.estimatedWeight = null;
            this.estimatedHeight = null;
            this.developmentMilestones = [];
          }
        },
        calculateDevelopmentMilestones() {
          // Define some arbitrary development milestones
          const milestones = [
            'Cognitive: Recognizes familiar faces.',
            'Motor: Starts crawling or scooting.',
            'Social: Imitates sounds and gestures.',
            'Cognitive: Shows interest in colorful objects.',
            'Motor: May start pulling up to stand.',
            'Social: Enjoys playing peek-a-boo.',
            'Cognitive: Understands simple words.',
            'Motor: May take first steps with support.',
            'Social: Responds to own name.',
          ];
  
          // Select milestones based on age
          return milestones.slice(0, this.age);
        },
      },
    });
  </script>
  
  <style scope>
#app2 {
  font-family: 'Arial', sans-serif;
  text-align: center;
  color: #2c3e50;
  margin: 20px auto;
  max-width: 600px;
  background-color: pink; /* Added pink background */
  padding: 20px; /* Added padding for better spacing */
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(50, 50, 50, 0.4);
}

h1 {
  color: #fafcfd;
  background-color: #1412127a;
  padding: 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  position: relative;
  display: inline-block;
  box-shadow: 0 4px 8px rgba(240, 60, 240, 0.4);
  text-shadow: 2px 2px 4px rgba(3, 3, 3, 0.808), 0 4px 2px rgba(50, 50, 50, 0.4);
  font-weight: bold; 
}

label {
  display: block;
  margin-bottom: 10px;
}

input {
  padding: 10px;
  width: 100%;
  box-sizing: border-box;
  margin-bottom: 20px;
}

.result-section {
  background-color: #f2f2f2;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 4px 8px rgba(50, 50, 50, 0.4);
}

h2 {
  color: #3498db;
  font-weight: bold; 
  text-shadow: 2px 2px 4px rgba(247, 247, 239, 0.808);
  margin-bottom: 10px;
  border: 2px solid #3498db; 
  padding: 8px; 
  border-radius: 4px;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  margin: 10px 0;
  background-color: #ffffff;
  padding: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(50, 50, 50, 0.2);
}

</style>


    

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

    console.log('Unread Notifications:', notifications.length);

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
                        var messageItem = '<li><a class="dropdown-item" href="#">' +
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