<?php
$folder = $_GET['folder'] ?? '';
$uploadsDir = __DIR__ . '/uploads/' . $folder;

$subFolders = [];
if (!empty($folder) && file_exists($uploadsDir)) {
    $subFolders = array_filter(scandir($uploadsDir), function ($item) use ($uploadsDir) {
        return is_dir($uploadsDir . '/' . $item) && $item !== '.' && $item !== '..';
    });
}

header('Content-Type: application/json');
echo json_encode(array_values($subFolders));
?>
