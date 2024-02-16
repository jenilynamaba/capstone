<?php
require 'Database.php';

class User {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function registerUser($role, $email, $password, $firstName, $lastName, $gender, $birthday, $address, $filename) {
        $sql = 'INSERT INTO users (role, email, password, first_name, last_name, gender, birthday, address, file_name) 
                VALUES (:role, :email, :password, :first_name, :last_name, :gender, :birthday, :address, :file_name)';
        
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashPassword);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':birthday', $birthday);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':file_name', $filename);

        return $stmt->execute();
    }

    public function isEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? true : false;
    }
}
?>
