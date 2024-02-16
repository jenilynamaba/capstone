<?php
    require_once "Database.php";
    
    // Assume $email and $password are provided from user input or some other source
    $email = 'admin@admin.com';
    $password = 'admin';

    // Create an instance of the Database class
    $db = new Database();

    // Call the createAdmin method to insert data into the admin table
    $adminId = $db->createAdmin($email, $password);

    if ($adminId) {
        echo 'Admin created successfully. Admin ID: ' . $adminId;
    } else {
        echo 'Error creating admin.';
    }

?>