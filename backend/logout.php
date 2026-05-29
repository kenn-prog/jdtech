<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Support browser navigation or direct clicks as well as AJAX.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    destroySession();
    jsonResponse(['ok' => true]);
}

destroySession();
header('Location: ' . APP_URL . '/index.php');
exit;
