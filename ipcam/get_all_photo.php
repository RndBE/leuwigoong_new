<?php

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function get_last_modified_file($folder_path) {
    $files = scandir($folder_path);
    $fileModifiedTimes = [];

    foreach ($files as $file) {
        $filePath = $folder_path . '/' . $file;
        if (is_file($filePath)) {
            $fileModifiedTimes[$file] = filemtime($filePath);
        }
    }

    arsort($fileModifiedTimes);
    $sortedFiles = array_keys($fileModifiedTimes);

    // Ambil index 1 kalau index 0 adalah .
    return isset($sortedFiles[1]) ? $sortedFiles[1] : null;
}

echo json_encode(
    array(
        '10352' => 'https://leuwigoong.beacontelemetry.com/ipcam/10352/' . get_last_modified_file('./10352'),
        '10353' => 'https://leuwigoong.beacontelemetry.com/ipcam/10353/' . get_last_modified_file('./10353'),
        '10354' => 'https://leuwigoong.beacontelemetry.com/ipcam/10354/' . get_last_modified_file('./10354'),
        '10355' => 'https://leuwigoong.beacontelemetry.com/ipcam/10355/' . get_last_modified_file('./10355'),
        '10356' => 'https://leuwigoong.beacontelemetry.com/ipcam/10356/' . get_last_modified_file('./10356'),
        '10357' => 'https://leuwigoong.beacontelemetry.com/ipcam/10357/' . get_last_modified_file('./10357'),
    )
);

?>
