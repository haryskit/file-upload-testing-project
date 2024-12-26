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
    
    // Build the upload directory path based on main folder and subfolder (if provided)
    $uploadDir = __DIR__ . "/uploads/$mainFolder";
    
    if (!empty($mainFolder)) {
        // Check if the main folder exists
        if (!is_dir($uploadDir)) {
            $message = "The selected main folder does not exist!";
        } else {
            // If subfolder is selected, append it to the directory path
            if (!empty($subFolder)) {
                $uploadDir .= "/$subFolder";
                if (!is_dir($uploadDir)) {
                    // Create the subfolder if it does not exist
                    mkdir($uploadDir, 0777, true);
                    $message = "Subfolder '$subFolder' created successfully!";
                } else {
                    $message = "Uploading files to existing subfolder '$subFolder'.";
                }
            } else {
                $message = "Uploading files to main folder '$mainFolder'.";
            }
            
            // Handle file upload (add your file upload logic here)
            if (isset($_FILES['files'])) {
                $files = $_FILES['files'];
                $fileCount = count($files['name']);
                
                for ($i = 0; $i < $fileCount; $i++) {
                    $filePath = $uploadDir . '/' . basename($files['name'][$i]);
                    if (move_uploaded_file($files['tmp_name'][$i], $filePath)) {
                        $message .= " File '{$files['name'][$i]}' uploaded successfully!";
                    } else {
                        $message .= " Failed to upload file '{$files['name'][$i]}'.";
                    }
                }
            }
        }
    } else {
        $message = "Please select a main folder.";
    }
}
echo "Max file size allowed: " . ini_get('upload_max_filesize');
echo "Max post size allowed: " . ini_get('post_max_size');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Upload Files</title>
    <script>
        async function loadSubFolders(mainFolder) {
            const subFolderDropdown = document.getElementById('subFolder');
            subFolderDropdown.innerHTML = '<option value="">Choose Subfolder</option>';

            if (mainFolder) {
                const response = await fetch(`getSubFolders.php?folder=${encodeURIComponent(mainFolder)}`);
                const subFolders = await response.json();

                subFolders.forEach(subFolder => {
                    const option = document.createElement('option');
                    option.value = subFolder;
                    option.textContent = subFolder;
                    subFolderDropdown.appendChild(option);
                });
            }
        }
    </script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Upload Files</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="mainFolder" class="form-label">Main Folder</label>
                <select class="form-select" id="mainFolder" name="mainFolder" onchange="loadSubFolders(this.value)" required>
                    <option value="">Choose Folder</option>
                    <?php foreach ($mainFolders as $folder): ?>
                        <option value="<?= htmlspecialchars($folder) ?>"><?= htmlspecialchars($folder) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subFolder" class="form-label">Subfolder</label>
                <select class="form-select" id="subFolder" name="subFolder">
                    <option value="">Choose Subfolder</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="files" class="form-label">Choose Files</label>
                <input type="file" class="form-control" id="files" name="files[]" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
