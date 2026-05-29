<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
requireLoginAPI();

$user      = getUser();
$currentPw = $_POST['current_password'] ?? '';
$newPw     = $_POST['new_password']     ?? '';

if (!$currentPw || !$newPw) jsonResponse(['ok' => false, 'msg' => 'Both fields are required.']);
if (strlen($newPw) < 8)     jsonResponse(['ok' => false, 'msg' => 'New password must be at least 8 characters.']);

// Verify current password
$row = fetchOne("SELECT password FROM users WHERE id = " . (int) $user['id'] . " LIMIT 1");
if (!$row || !password_verify($currentPw, $row['password'])) {
    jsonResponse(['ok' => false, 'msg' => 'Current password is incorrect.']);
}

$newHash = password_hash($newPw, PASSWORD_DEFAULT, ['cost' => BCRYPT_COST]);
runQuery("UPDATE users SET password = '" . escape($newHash) . "' WHERE id = " . (int) $user['id'] . " LIMIT 1");

jsonResponse(['ok' => true, 'msg' => 'Password changed successfully!']);
