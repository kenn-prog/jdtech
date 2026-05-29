<?php
// ============================================================
//  JDTech — HTML Header (included at the top of every page)
//  PURPOSE: Outputs the <head> section with all CSS imports.
//           Set $pageTitle before including this file.
//
//  HOW FRONTEND CONNECTS TO BACKEND:
//  HTML pages (frontend) load CSS/JS from /assets/ folder.
//  When a user submits a form, data goes to a PHP file (backend).
//  PHP processes it, talks to MySQL, then sends back a response.
//  JavaScript (ajax.js) can also send requests without page reload.
// ============================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/db.php';
// Ensure helper functions (e.g. `h()`) are available to all pages
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= h($pageTitle) ?> — <?= APP_NAME ?></title>
  <meta name="description" content="<?= h($metaDescription ?? 'JDTech — Premium Electronics & Gadgets in Makati City.') ?>" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap" rel="stylesheet" />

  <!-- Core Styles -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css" />
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/responsive.css" />
  <script>window.APP_URL = <?= json_encode(APP_URL) ?>;</script>

  <?php if (isset($isAdmin) && $isAdmin): ?>
  <!-- Admin-only styles -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css" />
  <script defer src="<?= APP_URL ?>/assets/js/ajax.js"></script>
  <?php endif; ?>
</head>
<body>
