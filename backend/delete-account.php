<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
requireLoginAPI();

$user     = getUser();
$password = $_POST['password'] ?? '';

// Require password confirmation before deleting account
$row = fetchOne("SELECT password FROM users WHERE id = " . (int) $user['id'] . " LIMIT 1");
if (!$row || !password_verify($password, $row['password'])) {
    jsonResponse(['ok' => false, 'msg' => 'Incorrect password. Account not deleted.']);
}

runQuery("DELETE FROM users WHERE id = " . (int) $user['id'] . " LIMIT 1");
destroySession();
jsonResponse(['ok' => true]);
