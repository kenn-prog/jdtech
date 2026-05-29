<?php
// ============================================================
//  JDTech — Edit Product (Admin Only)
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
requireAdminAPI();

$id          = intval($_POST['id']          ?? 0);
$name        = trim($_POST['name']          ?? '');
$price       = floatval($_POST['price']     ?? 0);
$stock_status= trim($_POST['stock_status']  ?? 'On-hand');
$stock       = intval($_POST['stock']       ?? 10);
$rating      = min(5, max(1, intval($_POST['rating']    ?? 5)));
$badge       = trim($_POST['badge']         ?? '');
$description = trim($_POST['description']   ?? '');
$imageUrl    = trim($_POST['image_url']     ?? '');

if ($id <= 0 || !$name || $price <= 0) jsonResponse(['ok' => false, 'msg' => 'Invalid data.']);

// Handle optional new image upload
$errors = [];
if (!empty($_FILES['image']['name'])) {
    $uploaded = uploadFile($_FILES['image'], 'products', $errors);
    if ($uploaded) $imageUrl = $uploaded;
}
if (!empty($_FILES['images'])) {
    $imagePaths = [];
    foreach ($_FILES['images']['name'] as $index => $name) {
        if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_NO_FILE) {
            $file = [
                'name'     => $_FILES['images']['name'][$index],
                'type'     => $_FILES['images']['type'][$index],
                'tmp_name' => $_FILES['images']['tmp_name'][$index],
                'error'    => $_FILES['images']['error'][$index],
                'size'     => $_FILES['images']['size'][$index],
            ];
            $uploaded = uploadFile($file, 'products', $errors);
            if ($uploaded) $imagePaths[] = $uploaded;
        }
    }
    if ($imagePaths) {
        $existing = [];
        if ($imageUrl !== '') {
            $existing = array_values(array_filter(array_map('trim', preg_split('/[,|;]+/', $imageUrl))));
        }
        $imageUrl = implode(',', array_merge($existing, $imagePaths));
    }
}
if ($errors) jsonResponse(['ok' => false, 'msg' => implode(' ', $errors)]);

$imgPart = $imageUrl ? ", image_url = '" . escape($imageUrl) . "'" : '';

runQuery("UPDATE items SET
    name        = '" . escape($name)        . "',
    price       = "  . number_format($price, 2, '.', '') . ",
    stock_status= '" . escape($stock_status) . "',
    stock       = {$stock},
    rating      = {$rating},
    badge       = '" . escape($badge)       . "',
    description = '" . escape($description) . "'
    {$imgPart}
    WHERE id = {$id} LIMIT 1");

jsonResponse(['ok' => true]);
