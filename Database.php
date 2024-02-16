<?php
    class Database {
        private $host = 'localhost';
        private $username = 'root';
        private $password = '';
        private $dbname = 'newinfa';
        private $conn;

        private $connection;
        
        public function __construct() {
            try {
                $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
                die();
            }
        }
    
        public function getConnection() {
            return $this->conn;
        }
        
    
        // public function getUserByEmail($email) {
        //     $sql = "SELECT * FROM users WHERE email = :email";
        //     $stmt = $this->conn->prepare($sql);
        //     $stmt->bindParam(':email', $email);
        //     $stmt->execute();
        //     return $stmt->fetch(PDO::FETCH_ASSOC);
        // }

        public function getUserByEmail($email){
            // First, try to get a user
            $sqlUser = "SELECT * FROM users WHERE email = :email";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bindParam(':email', $email);
            $stmtUser->execute();
            $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        
            // If no user is found, try to get an admin
            if (!$user) {
                $sqlAdmin = "SELECT * FROM admin WHERE email = :email";
                $stmtAdmin = $this->conn->prepare($sqlAdmin);
                $stmtAdmin->bindParam(':email', $email);
                $stmtAdmin->execute();
                $user = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
            }
        
            return $user;
        }
        

        public function getMedicalStaffPosts($loggedInUserId) {
            $role = 'medical_staff';
            $sql = "SELECT * FROM posts WHERE user_id = :userId AND role = :role ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $loggedInUserId, PDO::PARAM_INT);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);
            $stmt->execute();
        
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            return $posts;
        }

        public function insertPost($userId, $postContent, $page) {
            try {
                $stmt = $this->conn->prepare("INSERT INTO posts (user_id, content, page) VALUES (:userId, :content, :page)");
                $stmt->bindParam(':userId', $userId);
                $stmt->bindParam(':content', $postContent);
                $stmt->bindParam(':page', $page);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }

        public function getPostsByPageAndRole($page, $role) { 
            try {
                $sql = "SELECT * FROM posts WHERE page = :page ORDER BY id DESC";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':page', $page, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Error: ' . $e->getMessage());
                return [];
            }
        }


        public function getAllUsers() {
            $sql = "SELECT * FROM users";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function updateUserStatus($userId, $status) {
            $sql = "UPDATE users SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $userId);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        }
        
        public function getUserInfoById($userId) {
            $query = "SELECT * FROM users WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
    
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function deletePost($postId) {
            try {
                $query = "DELETE FROM posts WHERE id = :id";
                $statement = $this->conn->prepare($query);
                $statement->bindParam(':id', $postId);
                $statement->execute();
            } catch (PDOException $e) {
                throw new Exception("Database error: " . $e->getMessage());
            }
        }
        

        public function updatePost($postId, $newContent) {
            try {
                $query = "UPDATE posts SET content = :newContent WHERE id = :postId";
                $statement = $this->conn->prepare($query);
                $statement->bindParam(':newContent', $newContent);
                $statement->bindParam(':postId', $postId);
                $statement->execute();
        
                if ($statement->rowCount() === 0) {
                    throw new Exception("Post update failed");
                }
            } catch (PDOException $e) {
                throw new Exception("Database error: " . $e->getMessage());
            }
        }

        public function getPostById($postId) {
            try {
                $sql = "SELECT * FROM posts WHERE id = :postId";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
                $stmt->execute();
        
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log('Error: ' . $e->getMessage());
                return null;
            }
        }
        
        public function updatePostContent($postId, $newContent) {
            $postId = $this->conn->quote($postId);
            $newContent = $this->conn->quote($newContent);
    
            $sql = "UPDATE posts SET content = $newContent WHERE id = $postId";
            $this->conn->exec($sql);
        }
        
        public function getCommentsByPostId($postId) {
            $sql = "SELECT comments.*, users.first_name, users.last_name, users.profile_picture FROM comments 
                    JOIN users ON comments.user_id = users.id 
                    WHERE comments.post_id = :postId 
                    ORDER BY comments.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function createComment($userId, $postId, $content, $isNotification) {
            $sql = "INSERT INTO comments (user_id, post_id, content, is_notification) VALUES (:userId, :postId, :content, :isNotification)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':postId', $postId, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':isNotification', $isNotification, PDO::PARAM_INT); // Assuming isNotification is an integer
        
            try {
                $stmt->execute();
                return $this->conn->lastInsertId();
            } catch (PDOException $e) {
                error_log('Error creating comment: ' . $e->getMessage());
                return false;
            }
        }
        
        // admin
        public function getAdminByEmail($email)
    {
        $sql = "SELECT * FROM admin WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createAdmin($email, $password)
        {
            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Set the default role to "admin"
            $defaultRole = 'admin';

            $sql = "INSERT INTO admin (email, password, role) VALUES (:email, :password, :role)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':role', $defaultRole, PDO::PARAM_STR);

            try {
                $stmt->execute();
                return $this->conn->lastInsertId(); // Return the ID of the newly inserted row
            } catch (PDOException $e) {
                error_log('Error creating admin: ' . $e->getMessage());
                return false;
            }
        }


    public function getAdminById($adminId)
    {
        $sql = "SELECT * FROM admin WHERE id = :adminId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAdminPassword($adminId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET password = :password WHERE id = :adminId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':adminId', $adminId, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            error_log('Error updating admin password: ' . $e->getMessage());
            return false;
        }
    }
    

    }
?>  