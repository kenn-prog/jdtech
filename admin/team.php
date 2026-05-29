<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';

requireAdmin();
header('Location: index.php');
exit;
?>
