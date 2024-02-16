<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateTestimony'])) {
    $database = new Database();

    $testimonyId = $_POST['testimonyId'];
    $newContent = htmlspecialchars($_POST['newContent']);

    try {
        $database->updatePost($testimonyId, $newContent);
        echo header("Location: testimonies.php");
    } catch (Exception $e) {
        $errorMessage = 'An error occurred while updating the testimony. Please try again later.';
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
    <title>Edit Testimony</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Edit Testimony
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($errorMessage)) {
                        } elseif (isset($successMessage)) {
                            echo '<div class="alert alert-success" role="alert">' . $successMessage . '</div>';
                        }
                        ?>

                        <form method="post">
                            <div class="mb-3">
                                <textarea class="form-control" id="newContent" name="newContent" rows="5"
                                    required><?php echo htmlspecialchars($testimony['content']); ?></textarea>
                            </div>
                            <input type="hidden" name="testimonyId" value="<?php echo $testimony['id']; ?>">

                            <?php if ($isAuthor): ?>
                                <button type="submit" name="updateTestimony" class="btn btn-primary btn-sm">Update Testimony</button>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
