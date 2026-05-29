<?php
// ============================================================
//  JDTech — Navigation Bar
//  PURPOSE: The sticky top navbar shown on every page.
//           Shows login/register for guests, and the user's
//           name + dropdown for logged-in users.
// ============================================================

$currentUser = getUser();
?>

<header class="navbar">
  <div class="container nav-inner">

    <!-- Logo -->
    <a href="<?= APP_URL ?>/index.php" class="logo">
      <span class="logo-mark">
        <?php if ($logoImage = getLogoImage()): ?>
          <img src="<?= APP_URL ?>/<?= h($logoImage) ?>" alt="<?= h(APP_NAME) ?>" />
        <?php else: ?>
          <?= h(getLogoIcon()) ?>
        <?php endif; ?>
      </span>JD<span>Tech</span>

    <!-- Desktop Navigation Links -->
    <nav class="nav-links">
      <a href="<?= APP_URL ?>/index.php"    <?= basename($_SERVER['PHP_SELF']) === 'index.php'    ? 'class="active"' : '' ?>>Home</a>
      <a href="<?= APP_URL ?>/products.php" <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'class="active"' : '' ?>>Products</a>
      <a href="<?= APP_URL ?>/about.php"    <?= basename($_SERVER['PHP_SELF']) === 'about.php'    ? 'class="active"' : '' ?>>About</a>
      <a href="<?= APP_URL ?>/contact.php"  <?= basename($_SERVER['PHP_SELF']) === 'contact.php'  ? 'class="active"' : '' ?>>Contact</a>
    </nav>

    <!-- Desktop Action Buttons -->
    <div class="desktop-actions">
      <?php if ($currentUser): ?>
        <!-- Logged-in user pill with dropdown -->
        <div class="nav-user-pill" onclick="toggleNavDropdown()" id="navUserPill">
          <span class="nav-avatar"><?= h($currentUser['avatar'] ?? '👤') ?></span>
          <span class="nav-user-name"><?= h($currentUser['firstName'] ?? 'User') ?></span>
          <span>▾</span>
          <div class="nav-dropdown" id="navDropdown">
            <?php if ($currentUser['role'] === 'admin'): ?>
              <a href="<?= APP_URL ?>/admin/index.php">⚙️ Admin Panel</a>
            <?php endif; ?>
            <a href="<?= APP_URL ?>/dashboard.php">📊 Dashboard</a>
            <a href="<?= APP_URL ?>/profile.php">👤 My Profile</a>
            <a href="<?= APP_URL ?>/settings.php">⚙️ Settings</a>
            <button class="nav-dropdown-logout" onclick="logoutUser()">🚪 Logout</button>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= APP_URL ?>/login.php"    class="btn btn-ghost">Login</a>
        <a href="<?= APP_URL ?>/register.php" class="btn btn-primary">Register</a>
      <?php endif; ?>

      <!-- Cart Icon -->
      <a href="<?= APP_URL ?>/cart.php" class="btn btn-ghost cart-btn" style="position:relative">
        🛒 Cart
        <span class="cart-count" id="cartCount">0</span>
      </a>
    </div>

    <!-- Mobile Actions -->
    <div class="mobile-actions">
      <button class="icon-btn menu-toggle" id="menuToggle" aria-label="Menu" onclick="toggleMobileMenu()">☰</button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div class="mobile-menu" id="mobileMenu">
    <a href="<?= APP_URL ?>/index.php">Home</a>
    <a href="<?= APP_URL ?>/products.php">Products</a>
    <a href="<?= APP_URL ?>/about.php">About</a>
    <a href="<?= APP_URL ?>/contact.php">Contact</a>
    <a href="<?= APP_URL ?>/cart.php">Cart</a>
    <?php if ($currentUser): ?>
      <a href="<?= APP_URL ?>/dashboard.php">Dashboard</a>
      <a href="<?= APP_URL ?>/profile.php">My Profile</a>
      <a href="<?= APP_URL ?>/backend/logout.php" onclick="logoutUser(); return false;">Logout</a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/login.php">Login</a>
      <a href="<?= APP_URL ?>/register.php">Register</a>
    <?php endif; ?>
  </div>
</header>

<script>
function toggleMobileMenu() {
  document.getElementById('mobileMenu').classList.toggle('open');
}
function toggleNavDropdown() {
  document.getElementById('navDropdown').classList.toggle('open');
}
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
  const pill = document.getElementById('navUserPill');
  if (pill && !pill.contains(e.target)) {
    document.getElementById('navDropdown')?.classList.remove('open');
  }
});
async function logoutUser() {
  if (typeof apiPost === 'function') {
    const data = await apiPost('backend/logout.php');
    if (data.ok) {
      window.location.href = '<?= APP_URL ?>/index.php';
    } else {
      alert(data.msg || 'Could not log out. Please try again.');
    }
    return;
  }

  const res = await fetch('<?= APP_URL ?>/backend/logout.php', { method: 'POST' });
  window.location.href = '<?= APP_URL ?>/index.php';
}
</script>
