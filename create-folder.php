<?php
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folderName = $_POST['folderName'] ?? '';
    $uploadDir = __DIR__ . '/uploads/' . $folderName;

    if (!empty($folderName) && !file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        $message = "Folder '$folderName' created successfully!";
    } else {
        $message = "Folder already exists or invalid name!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Create Folder</title>
</head>

<body>
    <?php include('navbar.php') ?>
    <div class="container mt-5">

        <h2>Create Folder</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="folderName" class="form-label">Folder Name</label>
                <input type="text" class="form-control" id="folderName" name="folderName" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Folder</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>