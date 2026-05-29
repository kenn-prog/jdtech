<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'POST required'], 405);
requireAdminAPI();

$id = intval($_POST['id'] ?? 0);
$show = intval($_POST['show'] ?? 0) ? 1 : 0;
if ($id <= 0) jsonResponse(['ok' => false, 'msg' => 'Invalid id']);

// Ensure the column exists
if (!fetchOne("SHOW COLUMNS FROM orders LIKE 'show_feedback'")) {
    runQuery("ALTER TABLE orders ADD COLUMN show_feedback TINYINT(1) DEFAULT 0");
}

runQuery("UPDATE orders SET show_feedback = {$show} WHERE id = {$id} LIMIT 1");
jsonResponse(['ok' => true]);
