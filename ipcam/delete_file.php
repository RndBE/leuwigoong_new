<?php

function deleteFilesExceptLastModified($directory) {
    // Ensure the directory exists and is a directory
    if (!is_dir($directory)) {
        throw new InvalidArgumentException("The specified path is not a directory.");
    }

    // Scan the directory and get all files
    $files = scandir($directory);

    // Filter out the current and parent directory entries
    $files = array_diff($files, array('.', '..'));

    // Create an associative array with file modification times
    $fileModificationTimes = [];
    foreach ($files as $file) {
        $filePath = $directory . '/' . $file;
        if (is_file($filePath)) {
            $fileModificationTimes[$filePath] = filemtime($filePath);
        }
    }

    // Sort files by modification time in descending order
    arsort($fileModificationTimes);

    // Get the paths of the last 3 modified files
    $filesToKeep = array_slice(array_keys($fileModificationTimes), 0, 3);

    // Loop through all files and delete those not in the keep list
    foreach ($fileModificationTimes as $filePath => $modTime) {
        if (!in_array($filePath, $filesToKeep)) {
            unlink($filePath); // Delete the file
        }
    }

    return true; // Return true on success
}

// Usage example
try {
    deleteFilesExceptLastModified('./10352');
	deleteFilesExceptLastModified('./10353');
	deleteFilesExceptLastModified('./10354');
	deleteFilesExceptLastModified('./10355');
	deleteFilesExceptLastModified('./10356');
	deleteFilesExceptLastModified('./10357');
    echo "Files deleted successfully, except for the last 3 modified files.";
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
