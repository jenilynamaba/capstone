<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteTestimony'])) {
    $database = new Database();

    $testimonyId = $_POST['testimonyId'];

    try {
        $database->deletePost($testimonyId);
        header("Location: testimonies.php");
        exit();
    } catch (Exception $e) {
        $errorMessage = 'An error occurred while deleting the testimony. Please try again later.';
    }
}

if (isset($_GET['testimonyId'])) {
    $testimonyId = $_GET['testimonyId'];
    $database = new Database();
    $testimony = $database->getPostById($testimonyId);

    if (!$testimony) {
        header('Location: testimonies.php');
        exit();
    }

    $authorId = $testimony['user_id'];

    $isAuthor = ($_SESSION['user']['id'] === $authorId);
} else {
    header('Location: testimonies.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Testimony</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Delete Testimony
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($errorMessage)) {
                            echo '<div class="alert alert-danger" role="alert">' . $errorMessage . '</div>';
                        } elseif ($isAuthor) {
                        ?>
                            <p>Are you sure you want to delete this testimony?</p>
                            <form method="post">
                                <input type="hidden" name="testimonyId" value="<?php echo $testimony['id']; ?>">
                                <button type="submit" name="deleteTestimony" class="btn btn-danger btn-sm">Delete Testimony</button>
                                <a href="testimonies.php" class="btn btn-secondary btn-sm">Cancel</a>
                            </form>
                        <?php
                        } else {
                            echo '<div class="alert alert-danger" role="alert">You do not have permission to delete this testimony.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
