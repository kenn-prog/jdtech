<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
requireAdminAPI();

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) jsonResponse(['ok' => false, 'msg' => 'Invalid product ID.']);

runQuery("DELETE FROM items WHERE id = {$id} LIMIT 1");
jsonResponse(['ok' => true]);
