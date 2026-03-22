<?php
/**
 * Admin: Immediate image upload endpoint (AJAX).
 * Returns JSON { "ok": true, "path": "/assets/images/uploads/img_xxx.jpg" }
 *       or     { "ok": false, "error": "reason" }
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!defined('BASE_DIR')) define('BASE_DIR', dirname(__DIR__));
require_once BASE_DIR . '/admin/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$file = $_FILES['file'] ?? null;
if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode(['ok' => false, 'error' => 'No file received']);
    exit;
}
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'error' => 'Upload error code ' . $file['error'] . '. Max size is 2 MB.']);
    exit;
}

$ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
if (!in_array($ext, $allowed, true)) {
    echo json_encode(['ok' => false, 'error' => 'Invalid type. Allowed: JPG, PNG, WebP, GIF.']);
    exit;
}
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['ok' => false, 'error' => 'File too large. Max 5 MB.']);
    exit;
}

$upload_dir = BASE_DIR . '/assets/images/uploads';
if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);

$filename = uniqid('img_', true) . '.' . $ext;
$dest     = $upload_dir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['ok' => false, 'error' => 'Could not save file. Check directory permissions.']);
    exit;
}

echo json_encode(['ok' => true, 'path' => '/assets/images/uploads/' . $filename]);
