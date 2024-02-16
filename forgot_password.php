<?php
    session_start();
    
    require_once 'Database.php';
    require_once 'PHPMailer/src/PHPMailer.php';
    require_once 'PHPMailer/src/SMTP.php';
    require_once 'PHPMailer/src/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    $_SESSION['password_reset_success'] = true;

    $database = new Database();
    $pdo = $database->getConnection();

    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!isset($_POST['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid form submission.'));
            exit();
        }

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['form_token'] = $token;

            $stmt = $pdo->prepare("UPDATE users SET reset_token = :token WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'johnmilsof@gmail.com';
                $mail->Password = 'wcgpuervpvcztxjo';
                $mail->Port = 587;
                $mail->SMTPSecure = 'tls';

                $mail->setFrom('johnmilsof@gmail.com', 'INFA-HEED');
                $mail->addAddress($email);

                $mail->Subject = 'INFA-HEED | Password Reset ';
                $mail->Body = "Click the link below to reset your password:\n\n";
                $mail->Body .= "http://localhost/newinfa/reset_password.php?email=" . urlencode($email) . "&token=" . urlencode($token);

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
