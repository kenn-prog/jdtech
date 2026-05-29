<?php
// ============================================================
//  JDTech — Add Product (Admin Only Backend Handler)
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
}

requireAdminAPI(); // 🔒 Admins only

$name        = trim($_POST['name']        ?? '');
$price       = floatval($_POST['price']   ?? 0);
$stock_status= trim($_POST['stock_status'] ?? 'On-hand');
$icon        = trim($_POST['icon']        ?? 'phone');
$stock       = intval($_POST['stock']     ?? 10);
$rating      = min(5, max(1, intval($_POST['rating'] ?? 5)));
$badge       = trim($_POST['badge']       ?? '');
$description = trim($_POST['description'] ?? '');
$imageUrl    = trim($_POST['image_url']   ?? '');

if (!$name || $price <= 0) {
    jsonResponse(['ok' => false, 'msg' => 'Product name and a valid price are required.']);
}

// Handle image upload
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
if ($errors) {
    jsonResponse(['ok' => false, 'msg' => implode(' ', $errors)]);
}

$imgSql = $imageUrl ? "'" . escape($imageUrl) . "'" : 'NULL';
runQuery("INSERT INTO items (name, price, stock_status, icon, stock, rating, badge, description, image_url, reviews)
    VALUES (
        '" . escape($name)        . "',
        " . number_format($price, 2, '.', '') . ",
        '" . escape($stock_status) . "',
        '" . escape($icon)        . "',
        {$stock}, {$rating},
        '" . escape($badge)       . "',
        '" . escape($description) . "',
        {$imgSql}, 0
    )");

jsonResponse(['ok' => true, 'id' => lastInsertId()]);
