<?php
// ============================================================
//  Backend: Add/Edit Staff Member
//  - Handle image upload
//  - Insert or update staff record
//  - Return JSON response
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

header('Content-Type: application/json');

$id          = $_POST['id'] ?? '';
$name        = trim($_POST['name'] ?? '');
$position    = trim($_POST['position'] ?? '');
$description = trim($_POST['description'] ?? '');
$facebook    = trim($_POST['facebook'] ?? '');
$linkedin    = trim($_POST['linkedin'] ?? '');
$twitter     = trim($_POST['twitter'] ?? '');
$instagram   = trim($_POST['instagram'] ?? '');
$removeImage = !empty($_POST['remove_image']) && in_array($_POST['remove_image'], ['1', 'true', 'yes'], true);

// Validate input
if (!$name || !$position) {
    jsonResponse(['ok' => false, 'msg' => 'Name and position are required'], 400);
    exit;
}

$requiredColumns = ['facebook', 'linkedin', 'twitter', 'instagram'];
foreach ($requiredColumns as $column) {
    if (!columnExists('staff', $column)) {
        runQuery("ALTER TABLE staff ADD COLUMN {$column} VARCHAR(255) DEFAULT NULL");
    }
}

$isNew = $id === 'new' || !$id;
$imageFilename = null;
$errors = [];

// Handle image upload
if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploaded = uploadFile($_FILES['image'], 'staff', $errors);
    if (!$uploaded) {
        $errors[] = 'Staff image upload failed.';
    } else {
        $imageFilename = basename($uploaded);
    }
}

if (!empty($errors)) {
    jsonResponse(['ok' => false, 'msg' => implode(' ', $errors)], 400);
    exit;
}

// Insert new staff member
if ($isNew) {
    $query = "INSERT INTO staff (name, position, description, image, facebook, linkedin, twitter, instagram, created_at) VALUES (
        '" . escape($name) . "',
        '" . escape($position) . "',
        '" . escape($description) . "',
        '" . escape($imageFilename ?? '') . "',
        '" . escape($facebook) . "',
        '" . escape($linkedin) . "',
        '" . escape($twitter) . "',
        '" . escape($instagram) . "',
        '" . date('Y-m-d H:i:s') . "'
    )";
    
    if (!runQuery($query)) {
        jsonResponse(['ok' => false, 'msg' => 'Failed to add staff member'], 500);
        exit;
    }
    
    jsonResponse(['ok' => true, 'msg' => 'Staff member added', 'id' => lastInsertId()]);
} else {
    // Update existing staff member
    $staffId = (int) $id;
    $existing = fetchOne("SELECT image FROM staff WHERE id = {$staffId}");
    
    if (!$existing) {
        jsonResponse(['ok' => false, 'msg' => 'Staff member not found'], 404);
        exit;
    }
    
    // If removing the current image, delete it and clear the field
    if ($removeImage && $existing['image'] && file_exists('../uploads/staff/' . $existing['image'])) {
        unlink('../uploads/staff/' . $existing['image']);
        $existing['image'] = '';
    }

    // If new image uploaded, delete old one
    if ($imageFilename && $existing['image'] && file_exists('../uploads/staff/' . $existing['image'])) {
        unlink('../uploads/staff/' . $existing['image']);
    }
    
    // Use new image if uploaded; otherwise keep existing or blank if removed
    $finalImage = $imageFilename ?? $existing['image'];
    
    $query = "UPDATE staff SET
        name = '" . escape($name) . "',
        position = '" . escape($position) . "',
        description = '" . escape($description) . "',
        image = '" . escape($finalImage) . "',
        facebook = '" . escape($facebook) . "',
        linkedin = '" . escape($linkedin) . "',
        twitter = '" . escape($twitter) . "',
        instagram = '" . escape($instagram) . "'
        WHERE id = {$staffId}
    ";
    
    if (!runQuery($query)) {
        jsonResponse(['ok' => false, 'msg' => 'Failed to update staff member'], 500);
        exit;
    }
    
    jsonResponse(['ok' => true, 'msg' => 'Staff member updated']);
}
