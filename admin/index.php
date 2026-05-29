<?php
// ============================================================
//  JDTech — Admin Dashboard
//  PURPOSE: Admin panel homepage. Shows store statistics,
//           recent orders, and quick action buttons.
//
//  ADMIN PANEL STRUCTURE:
//  - admin/index.php    → Dashboard overview (stats)
//  - admin/products.php → Add, edit, delete products
//  - admin/orders.php   → View and update order statuses
//  - admin/users.php    → View and remove registered users
//  - admin/settings.php → Edit homepage content, admin profile
//
//  Every admin page starts with requireAdmin() which
//  redirects non-admins before any content is shown.
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin(); // 🔒 Admins only — redirects everyone else

$isAdmin   = true;
$admin     = getUser();
$pageTitle = 'Admin Dashboard';

// Quick stats from database
$totalProducts = fetchOne('SELECT COUNT(*) as c FROM items')['c'] ?? 0;
$totalUsers    = fetchOne('SELECT COUNT(*) as c FROM users')['c'] ?? 0;
$totalOrders   = fetchOne('SELECT COUNT(*) as c FROM orders')['c'] ?? 0;
$totalRevenue  = fetchOne("SELECT SUM(total) as t FROM orders WHERE status != 'Cancelled'")['t'] ?? 0;

include '../includes/header.php';
?>

<div class="admin-layout">
  <?php include '../includes/sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1>📊 Dashboard</h1>
      <div class="admin-user">
        <span><?= h($admin['avatar'] ?? '⚡') ?> <?= h($admin['firstName'] ?? 'Admin') ?></span>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="admin-stats">
      <div class="admin-stat-card">
        <div class="stat-icon">📦</div>
        <div><div class="stat-num"><?= $totalProducts ?></div><div class="stat-label">Products</div></div>
      </div>
      <div class="admin-stat-card">
        <div class="stat-icon">👥</div>
        <div><div class="stat-num"><?= $totalUsers ?></div><div class="stat-label">Users</div></div>
      </div>
      <div class="admin-stat-card">
        <div class="stat-icon">🧾</div>
        <div><div class="stat-num"><?= $totalOrders ?></div><div class="stat-label">Orders</div></div>
      </div>
      <div class="admin-stat-card">
        <div class="stat-icon">💰</div>
        <div><div class="stat-num"><?= formatPrice((float)$totalRevenue) ?></div><div class="stat-label">Revenue</div></div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-section">
      <h3>Quick Actions</h3>
      <div class="admin-actions">
        <a href="products.php" class="btn btn-primary">➕ Add Product</a>
        <a href="orders.php"   class="btn btn-outline">🧾 View Orders</a>
        <a href="users.php"    class="btn btn-outline">👥 Manage Users</a>
        <a href="settings.php" class="btn btn-outline">⚙️ Settings</a>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="admin-section">
      <h3>Recent Orders</h3>
      <div id="recentOrders">Loading…</div>
    </div>
  </main>
</div>

<script>
async function loadRecentOrders() {
  const res  = await fetch('../api/users.php?action=get_orders_admin');
  const data = await res.json();
  const orders = (data.orders || []).slice(0, 10); // Show last 10
  if (!orders.length) {
    document.getElementById('recentOrders').innerHTML = '<p class="empty-msg">No orders yet.</p>';
    return;
  }
  document.getElementById('recentOrders').innerHTML = `
    <table class="data-table">
      <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>${orders.map(o => `
        <tr>
          <td>#${o.id}</td>
          <td>${o.first_name || ''} ${o.last_name || ''} <small>${o.email || ''}</small></td>
          <td>₱${Number(o.total).toLocaleString('en-PH')}</td>
          <td>
            <select class="status-select" onchange="updateStatus(${o.id}, this.value)">
              ${['Processing','On the Way','Delivered','Cancelled'].map(s =>
                `<option ${o.status===s?'selected':''}>${s}</option>`).join('')}
            </select>
          </td>
          <td>${new Date(o.date).toLocaleDateString('en-PH')}</td>
          <td><span class="status-badge">${o.status}</span></td>
        </tr>`).join('')}
      </tbody>
    </table>`;
}

async function updateStatus(id, status) {
  const form = new FormData();
  form.append('id', id);
  form.append('status', status);
  await fetch('../api/users.php?action=update_order_status', { method: 'POST', body: form });
}

loadRecentOrders();
</script>

<?php
// Admin pages don't use the public footer — just close HTML
echo '</div></body></html>';
?>
