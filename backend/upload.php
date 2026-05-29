<?php
// ============================================================
//  JDTech — File Upload Handler
//  PURPOSE: Handles profile photo and document uploads.
//
//  FILE UPLOAD FLOW (Full Explanation):
//  1. HTML form must have: enctype="multipart/form-data"
//  2. User selects a file and submits the form
//  3. Browser sends file to this PHP script via HTTP POST
//  4. PHP receives it in $_FILES['field_name']:
//     - $_FILES['photo']['name']     = original filename
//     - $_FILES['photo']['size']     = file size in bytes
//     - $_FILES['photo']['tmp_name'] = temp path on server
//     - $_FILES['photo']['error']    = 0 means no error
//  5. We validate: size, extension, and image type
//  6. move_uploaded_file() moves from temp → uploads/ folder
//  7. We return the relative path to store in the database
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
requireLoginAPI();

$type   = trim($_POST['type'] ?? 'profile'); // profile, products, documents
$field  = 'file';
$errors = [];

if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
    jsonResponse(['ok' => false, 'msg' => 'No file was uploaded.']);
}

$path = uploadFile($_FILES[$field], $type, $errors);

if ($errors || !$path) {
    jsonResponse(['ok' => false, 'msg' => implode(' ', $errors)]);
}

// If it's a profile photo, update the user's avatar URL in the database
if ($type === 'profile') {
    $user = getUser();
    runQuery("UPDATE users SET avatar = '" . escape($path) . "' WHERE id = " . (int) $user['id'] . " LIMIT 1");
    $_SESSION['user']['avatar'] = $path;
}

jsonResponse(['ok' => true, 'path' => $path]);
