<?php
// Mounted only in the disposable smoke-test container; never a public app route.
header('Content-Type: application/json');
if (PHP_SAPI !== 'apache2handler' || PHP_VERSION_ID < 80400 || PHP_VERSION_ID >= 80500 || !session_start()) {
    http_response_code(500);
    echo json_encode(['error' => 'Unexpected runtime or session failure']);
    exit;
}

$_SESSION['requests'] = ($_SESSION['requests'] ?? 0) + 1;
$uploadHash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upload = $_FILES['file'] ?? null;
    if (!$upload || $upload['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Multipart upload failed']);
        exit;
    }
    $uploadHash = hash_file('sha256', $upload['tmp_name']);
}

echo json_encode([
    'sapi' => PHP_SAPI,
    'version' => PHP_VERSION,
    'requests' => $_SESSION['requests'],
    'upload_sha256' => $uploadHash,
]);
