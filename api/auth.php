<?php
// ============================================================
//  JDTech — Auth API
//  PURPOSE: Handles login, logout, register via JSON API.
//           Used by the JS frontend for AJAX authentication.
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'check':
        // Check current session (useful on page load)
        jsonResponse(['ok' => true, 'user' => getUser(), 'loggedIn' => isLoggedIn()]);
        break;

    case 'logout':
        destroySession();
        jsonResponse(['ok' => true]);
        break;

    default:
        jsonResponse(['ok' => false, 'msg' => 'Use /backend/login-process.php for login.'], 400);
}
