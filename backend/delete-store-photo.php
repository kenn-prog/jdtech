<?php
// ============================================================
//  Backend: Delete Store Photo
//  - Remove photo path from homepage store_photos
//  - Delete the image file from disk
//  - Return JSON response
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$photo = trim($data['photo'] ?? '');

if (!$photo) {
    jsonResponse(['ok' => false, 'msg' => 'Photo path is required.'], 400);
    exit;
}

// Normalize the path by converting backslashes to forward slashes
$normalized = str_replace('\\', '/', $photo);

// Remove leading slashes for comparison
$normalized = ltrim($normalized, '/');

// Check if it starts with uploads/store/ (it should)
if (strpos($normalized, 'uploads/store/') !== 0) {
    jsonResponse(['ok' => false, 'msg' => 'Invalid photo path. Must be in uploads/store/ folder.'], 400);
    exit;
}

$homepage = fetchOne('SELECT store_photos FROM homepage LIMIT 1');
$existingPhotos = [];
if (!empty($homepage['store_photos'])) {
    $existingPhotos = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $homepage['store_photos'])), fn($value) => $value !== ''));
}

// Find and remove the photo (normalize all entries for comparison)
$newPhotos = [];
foreach ($existingPhotos as $existingPhoto) {
    $normalizedExisting = ltrim(str_replace('\\', '/', $existingPhoto), '/');
    if ($normalizedExisting !== $normalized) {
        $newPhotos[] = $existingPhoto;
    }
}

if (count($newPhotos) === count($existingPhotos)) {
    jsonResponse(['ok' => false, 'msg' => 'Store photo not found in list.'], 404);
    exit;
}

if (!runQuery("UPDATE homepage SET store_photos = " . ($newPhotos ? "'" . escape(implode("\n", $newPhotos)) . "'" : "NULL") . " LIMIT 1")) {
    jsonResponse(['ok' => false, 'msg' => 'Failed to update store photo list.'], 500);
    exit;
}

// Delete the file from disk
$filePath = __DIR__ . '/../' . $normalized;
if (file_exists($filePath)) {
    @unlink($filePath);
}

jsonResponse(['ok' => true, 'msg' => 'Store photo deleted.']);
