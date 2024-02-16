<?php
// Example logic to fetch the selected user's profile picture based on user ID
if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Modify this part to fetch the profile picture path based on the selected user ID
    $selectedUserProfilePicture = 'uploads/default_profile_picture.jpg';

    echo json_encode(['profilePicture' => $selectedUserProfilePicture]);
    exit;
} else {
    echo json_encode(['error' => 'User ID not provided']);
    exit;
}
?>
