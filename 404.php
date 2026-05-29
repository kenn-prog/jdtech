<?php
// ============================================================
//  JDTech — 404 Not Found Page
//  PURPOSE: Shown when a user visits a page that doesn't exist.
//
//  To make this page work for all 404 errors, add to .htaccess:
//    ErrorDocument 404 /jdtech/404.php
// ============================================================

require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

http_response_code(404);

$pageTitle = '404 — Page Not Found';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:60px 16px;">
  <div style="text-align:center;max-width:480px;">
    <div style="font-size:100px;line-height:1;margin-bottom:24px;">🔍</div>
    <h1 style="font-size:clamp(48px,8vw,96px);font-weight:800;color:var(--primary);margin-bottom:8px;">404</h1>
    <h2 style="font-size:24px;font-weight:700;margin-bottom:12px;">Page Not Found</h2>
    <p style="color:var(--muted);margin-bottom:32px;line-height:1.7;">
      Oops! The page you're looking for doesn't exist or has been moved.
      Let's get you back on track.
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
      <a href="index.php"    class="btn btn-primary">🏠 Go Home</a>
      <a href="products.php" class="btn btn-outline">🛒 Browse Products</a>
    </div>
  </div>
</main>

<?php include 'includes/footer.php'; ?>
