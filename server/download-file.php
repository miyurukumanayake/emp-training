<?php
if (isset($_POST['file'])) {
    $file = $_POST['file'];
    $filename = $_POST['filename'] ?? basename($file);
    $filePath = __DIR__ . '/../' . $file;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        ob_clean();
        flush();
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'File not found']);
    }
}
