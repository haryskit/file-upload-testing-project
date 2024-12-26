<?php
function listFilesAndFolders($dir)
{
    if (!is_dir($dir)) {
        return [];
    }
    $items = scandir($dir);
    $files = array_diff($items, ['.', '..']);
    return array_values($files); // Reset array keys for consistent indexing
}

$mainFolder = $_GET['mainFolder'] ?? '';
$subFolder = $_GET['subFolder'] ?? '';
$selectedPath = $mainFolder && $subFolder ? "$mainFolder/$subFolder" : ($mainFolder ?: '');
$files = $selectedPath ? listFilesAndFolders(__DIR__ . "/$selectedPath") : [];

$uploadsDir = __DIR__ . '/uploads';
$mainFolders = listFilesAndFolders($uploadsDir);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Download Files</title>
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

        <h2>Download Files</h2>
        <form method="GET">
            <div class="mb-3">
                <label for="mainFolder" class="form-label">Select Main Folder</label>
                <select class="form-select" id="mainFolder" name="mainFolder" onchange="loadSubFolders(this.value); this.form.submit()">
                    <option value="">Select Folder</option>
                    <?php foreach ($mainFolders as $folder): ?>
                        <option value="uploads/<?= htmlspecialchars($folder) ?>" <?= $mainFolder === "uploads/$folder" ? 'selected' : '' ?>><?= htmlspecialchars($folder) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subFolder" class="form-label">Select Subfolder</label>
                <select class="form-select" id="subFolder" name="subFolder" onchange="this.form.submit()">
                    <option value="">Choose Subfolder</option>
                    <?php if ($mainFolder): ?>
                        <?php $subFolders = listFilesAndFolders(__DIR__ . "/$mainFolder"); ?>
                        <?php foreach ($subFolders as $subFolderOption): ?>
                            <option value="<?= htmlspecialchars($subFolderOption) ?>" <?= $subFolder === $subFolderOption ? 'selected' : '' ?>><?= htmlspecialchars($subFolderOption) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>

        <div id="file-container" class="row">
            <?php if ($selectedPath && $files): ?>
                <?php foreach ($files as $index => $file): ?>
                    <?php $filePath = "$selectedPath/$file"; ?>
                    <div class="col-md-3 mb-4 file-item" <?= $index >= 10 ? 'style="display: none;"' : '' ?>>
                        <div class="card">
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)): ?>
                                <img src="<?= $filePath ?>" class="card-img-top" alt="<?= $file ?>" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-body text-center">
                                    <p class="card-text">File: <?= $file ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <p class="card-text"><?= $file ?></p>
                                <a href="<?= $filePath ?>" download class="btn btn-primary">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif ($selectedPath): ?>
                <p>No files found in the selected folder.</p>
            <?php endif; ?>
        </div>

        <?php if (count($files) > 10): ?>
            <div class="text-center">
                <button id="show-more-btn" class="btn btn-secondary">Show More</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const showMoreBtn = document.getElementById("show-more-btn");
            const fileItems = document.querySelectorAll(".file-item");
            let itemsToShow = 10;

            if (showMoreBtn) {
                showMoreBtn.addEventListener("click", function () {
                    itemsToShow += 10;
                    let shownItems = 0;

                    fileItems.forEach(item => {
                        if (shownItems < itemsToShow) {
                            item.style.display = "block";
                            shownItems++;
                        }
                    });

                    // Hide the button if all items are displayed
                    if (shownItems >= fileItems.length) {
                        showMoreBtn.style.display = "none";
                    }
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
