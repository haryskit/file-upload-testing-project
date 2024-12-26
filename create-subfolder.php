<?php
$message = '';
$mainFolders = [];

// Read existing folders dynamically
$uploadsDir = __DIR__ . '/uploads';
if (file_exists($uploadsDir)) {
    $mainFolders = array_filter(scandir($uploadsDir), function ($item) use ($uploadsDir) {
        return is_dir($uploadsDir . '/' . $item) && $item !== '.' && $item !== '..';
    });
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mainFolder = $_POST['mainFolder'] ?? '';
    $subFolder = $_POST['subFolder'] ?? '';
    $uploadDir = __DIR__ . "/uploads/$mainFolder/$subFolder";

    if (!empty($mainFolder) && !empty($subFolder) && !file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
        $message = "Subfolder '$subFolder' created under '$mainFolder'!";
    } else {
        $message = "Subfolder already exists or invalid name!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Create Subfolder</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Create Subfolder</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="mainFolder" class="form-label">Main Folder</label>
                <select class="form-select" id="mainFolder" name="mainFolder" required>
                    <option value="">Choose Folder</option>
                    <?php foreach ($mainFolders as $folder): ?>
                        <option value="<?= htmlspecialchars($folder) ?>"><?= htmlspecialchars($folder) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subFolder" class="form-label">Subfolder Name</label>
                <input type="text" class="form-control" id="subFolder" name="subFolder" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Subfolder</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
