<?php
require 'Database.php';

class Admin {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllUsers() {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function approveUser($userId) {
        $sql = "UPDATE users SET status = 'Approved' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function declineUser($userId) {
        $sql = "UPDATE users SET status = 'Declined' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function setPendingStatus($userId) {
        $sql = "UPDATE users SET status = 'Pending' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}

$admin = new Admin();

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $admin->deleteUser($userId);
    header('Location: admin.php');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $admin->approveUser($userId);
    header('Location: admin.php'); 
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'decline' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $admin->declineUser($userId);
    header('Location: admin.php');
    exit();
}

if (!isset($_GET['action']) && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $admin->setPendingStatus($userId);
    header('Location: admin.php');
    exit();
}

$users = $admin->getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            margin: 20px;
        }
        .file-image {
            max-width: 100px;
            max-height: 100px;
            cursor: pointer;
        }
        .modal-body img {
            width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid table-container">
        <h1 class="text-center mb-4">User List</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Gender</th>
                        <th>Birthday</th>
                        <th>Address</th>
                        <th>Phone Numbers</th>
                        <th>Status</th>
                        <th>File Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['first_name']; ?></td>
                            <td><?php echo $user['last_name']; ?></td>
                            <td><?php echo $user['gender']; ?></td>
                            <td><?php echo $user['birthday']; ?></td>
                            <td><?php echo $user['address']; ?></td>
                            <td><?php echo $user['phone_Number']; ?></td>
                            <td>
                                <?php if ($user['status'] === 'Approved') : ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($user['status'] === 'Declined') : ?>
                                    <span class="badge bg-danger">Declined</span>
                                <?php else : ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <img src="uploads/<?php echo $user['file_name']; ?>" alt="User Image" class="file-image" data-bs-toggle="modal" data-bs-target="#imageModal<?php echo $user['id']; ?>">
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <!-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id']; ?>">
                                    Edit
                                </button> -->
                                <a href="admin.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="edit.php" method="POST">
                                            <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                                            <div class="mb-3">
                                                <input type="text" class="form-control" id="editFirstName" name="editFirstName" placeholder="Enter your first name" required value="<?php echo htmlspecialchars($user['first_name']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <input type="text" class="form-control" id="editLastName" name="editLastName" placeholder="Enter your last name" required value="<?php echo htmlspecialchars($user['last_name']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <input type="email" class="form-control" id="editEmail" name="editEmail" placeholder="Enter your email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <div class="modal fade" id="imageModal<?php echo $user['id']; ?>" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="uploads/<?php echo $user['file_name']; ?>" alt="User Image" class="img-fluid mb-3">
                                        
                                        <?php if ($user['status'] !== 'Approved' && $user['status'] !== 'Declined') : ?>
                                            <a href="admin.php?action=approve&id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve this user?')">Approve</a>
                                            
                                            <a href="admin.php?action=decline&id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Decline this user?')">Decline</a>
                                        <?php else : ?>
                                            <button class="btn btn-success btn-sm" disabled>Approved</button>
                                            <button class="btn btn-danger btn-sm" disabled>Declined</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS BuNdLe -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
