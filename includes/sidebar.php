<?php
// ============================================================
//  JDTech — Admin Sidebar
//  PURPOSE: Navigation sidebar for the admin panel.
//           Only shown inside /admin/ pages.
// ============================================================
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="admin-sidebar">
  <div class="sidebar-header">
    <a href="<?= APP_URL ?>/index.php" class="logo">
      <span class="logo-mark">
        <?php if ($logoImage = getLogoImage()): ?>
          <img src="<?= APP_URL ?>/<?= h($logoImage) ?>" alt="<?= h(APP_NAME) ?>" />
        <?php else: ?>
          <?= h(getLogoIcon()) ?>
        <?php endif; ?>
      </span>JD<span>Tech</span>
    </a>
    <span class="sidebar-badge">Admin</span>
  </div>

  <nav class="sidebar-nav">
    <a href="<?= APP_URL ?>/admin/index.php"    class="<?= $currentPage === 'index.php'    ? 'active' : '' ?>">📊 Dashboard</a>
    <a href="<?= APP_URL ?>/admin/products.php" class="<?= $currentPage === 'products.php' ? 'active' : '' ?>">📦 Products</a>
    <a href="<?= APP_URL ?>/admin/orders.php"   class="<?= $currentPage === 'orders.php'   ? 'active' : '' ?>">🧾 Orders</a>
    <a href="<?= APP_URL ?>/admin/users.php"    class="<?= $currentPage === 'users.php'    ? 'active' : '' ?>">👥 Users</a>
    <a href="<?= APP_URL ?>/admin/settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">⚙️ Settings</a>
    <hr />
    <a href="<?= APP_URL ?>/index.php">🌐 View Site</a>
    <a href="<?= APP_URL ?>/backend/logout.php" onclick="logoutUser(); return false;">🚪 Logout</a>
  </nav>
</aside>
