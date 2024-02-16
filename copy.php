<?php
    session_start();
    
    require_once 'Database.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    require_once 'PHPMailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Password reset email sent successfully
    $_SESSION['password_reset_success'] = true;

    // Create an instance of the Database class
    $database = new Database();
    $pdo = $database->getConnection();

    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        // Check if the email exists in the users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate a password reset token
            $token = bin2hex(random_bytes(32));
            $_SESSION['form_token'] = $token;

            // Store the token in the database for the user
            $stmt = $pdo->prepare("UPDATE users SET reset_token = :token WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Send the password reset email
            $mail = new PHPMailer(true);
            try {
                // SMTP configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'johnmilsof@gmail.com';
                $mail->Password = 'wcgpuervpvcztxjo';
                $mail->Port = 587;
                $mail->SMTPSecure = 'tls';

                // Sender and recipient details
                $mail->setFrom('johnmilsof@gmail.com', 'INFA-HEED');
                $mail->addAddress($email);

                // Email content
                $mail->Subject = 'INFA-HEED | Password Reset ';
                $mail->Body = "Click the link below to reset your password:\n\n";
                $mail->Body .= "http://localhost/newinfa/reset_password.php?email=" . urlencode($email) . "&token=" . urlencode($token);

                // Send the email
                $mail->send();

                echo json_encode(array('status' => 'success', 'message' => 'Password reset email sent successfully.'));
            } catch (Exception $e) {
                echo json_encode(array('status' => 'error', 'message' => 'Failed to send password reset email. Please try again later.'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No user found with the provided email.'));
        }
    }
    ?>
