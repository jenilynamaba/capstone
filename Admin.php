<?php
require 'Database.php';
session_start();

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

    // Add this method to your Admin class
    public function getUserPosts($userId) {
        $sql = "SELECT * FROM posts WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePost($post_id) {
        try {
            $sql = "DELETE FROM posts WHERE id = :post_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

            // Execute the statement
            $stmt->execute();

            // Check if any rows were affected (post deleted)
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error deleting post: ' . $e->getMessage());
            return false;
        }
    }
    
    // Add this method to check if the user is logged in
    public static function isLoggedIn() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
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

// Check if the user is an admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php'); // Redirect to dashboard or another page
    exit();
}

$users = $admin->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">

    <!-- Update Bootstrap and Font Awesome links -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
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

	<div class="wrapper d-flex align-items-stretch">
	<nav id="sidebar">
		<div class="custom-menu">
			<button type="button" id="sidebarCollapse" class="btn btn-primary">
				<i class="fas fa-bars"></i>
				<span class="visually-hidden">Toggle Menu</span>
			</button>
		</div>
		<div class="p-4">
			<h1><a href="javascript:;" class="logo">Admin<span style="font-size:15px;">INFA-HEED</span></a></h1>
			<ul class="list-unstyled components mb-5">
				<li class="active" onclick="showContent('home')">
					<a href="javascript:;"><span class="fas fa-home me-3"></span> List of Users</a>
				</li>
				<li onclick="showContent('about')">
					<a href="javascript:;"><span class="fas fa-user me-3"></span> Users Post</a>
				</li>
                <li class="nav-item">
					<a class="nav-link" href="./logout.php">
					<span class="fas fa-sign-out-alt me-3"></span> Logout
					</a>
				</li>
			</ul>

			<div class="footer">
				<p>
					Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved
					<i class="fas fa-heart" aria-hidden="true"></i>
					<a href="javascript:;" target="_blank">INFA-HEED</a>
				</p>
			</div>
		</div>
	</nav>
	<!-- Page Content -->
	<div id="content" class="p-4 p-md-5 pt-5">
		<div id="homeContent">
        <div id="allUsersContent" class="p-4 p-md-5 pt-5">
        <h2 class="mb-4">All Users</h2>
        <div class="container-fluid table-container">
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
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id']; ?>">
                                    Edit
                                </button>
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
    </div>
		</div>
        
        <div id="aboutContent" style="display: none;">
            <h2 class="mb-4">Users Post</h2>

            <?php foreach ($users as $user) : ?>
    <div class="user-posts" id="userPosts<?php echo $user['id']; ?>">
        <h3>
            <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>'s Posts
            <!-- <span class="badge bg-primary"><?php echo $user['id']; ?></span> -->
            <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#userPostsCollapse<?php echo $user['id']; ?>" aria-expanded="false" aria-controls="userPostsCollapse<?php echo $user['id']; ?>">
                Minimize
            </button>
        </h3>

        <div class="collapse" id="userPostsCollapse<?php echo $user['id']; ?>">
            <?php
            $userPosts = $admin->getUserPosts($user['id']);
            foreach ($userPosts as $post) :
                // Format the date here
                $postedDateTime = isset($post['created_at']) ? date("F j, Y, g:i a", strtotime($post['created_at'])) : '';
            ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="card-text"><?php echo $post['content']; ?></p>
                        <p class="card-text"><small class="text-muted"><?php echo $postedDateTime; ?></small></p>
                        <!-- Category Badge -->
                        <span class="badge bg-info"><?php echo $post['page']; ?></span>

                        <!-- Delete Button -->
                        <form action="delete.php" method="post" style="display:inline;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

        </div>

	<!-- Scripts at the end of the body section -->
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
	<script src="js/admin.js"></script>
	<script>
		function showContent(contentId) {
			// Hide all content divs
			document.querySelectorAll('#content > div').forEach(function (content) {
				content.style.display = 'none';
			});
	
			// Show the selected content
			var selectedContent = document.getElementById(contentId + 'Content');
			if (selectedContent) {
				selectedContent.style.display = 'block';
			}
		}
	
		// Call showContent initially for the default content
		showContent('home');
	</script>
	
</body>
</html>
