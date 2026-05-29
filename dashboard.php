<?php
// ============================================================
//  JDTech — User Dashboard
//  PURPOSE: Shows logged-in users their orders, profile info,
//           and a welcome message.
//
//  PROTECTED PAGE: requireLogin() will redirect to login.php
//  if the user is not logged in.
// ============================================================

require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

requireLogin(); // 🔒 Protected — must be logged in

$user      = getUser();
$pageTitle = 'Dashboard';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="dashboard-page">
  <div class="container">

    <!-- Welcome Header -->
    <div class="dash-header">
      <div class="dash-user-info">
        <div class="dash-avatar"><?= h($user['avatar'] ?? '👤') ?></div>
        <div>
          <h2>Hello, <?= h($user['firstName'] ?? 'User') ?>! 👋</h2>
          <p><?= h($user['email'] ?? '') ?></p>
          <?php if (!empty($user['contactNumber']) || !empty($user['address']) || !empty($user['paymentMethod'])): ?>
            <div style="margin-top:10px;color:var(--muted);font-size:14px;line-height:1.5;">
              <?php if (!empty($user['contactNumber'])): ?><div>Contact: <?= h($user['contactNumber']) ?></div><?php endif; ?>
              <?php if (!empty($user['address'])): ?><div>Address: <?= h($user['address']) ?></div><?php endif; ?>
              <?php if (!empty($user['paymentMethod'])): ?><div>Payment: <?= h($user['paymentMethod']) ?></div><?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="dash-actions">
        <a href="products.php" class="btn btn-primary">🛒 Shop Now</a>
        <a href="profile.php"  class="btn btn-outline">✏️ Edit Profile</a>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="dash-stats">
      <div class="dash-stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-info">
          <div class="stat-num" id="totalOrders">–</div>
          <div class="stat-label">Total Orders</div>
        </div>
      </div>
      <div class="dash-stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-info">
          <div class="stat-num" id="deliveredOrders">–</div>
          <div class="stat-label">Delivered</div>
        </div>
      </div>
      <div class="dash-stat-card">
        <div class="stat-icon">🔄</div>
        <div class="stat-info">
          <div class="stat-num" id="pendingOrders">–</div>
          <div class="stat-label">Processing</div>
        </div>
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="dash-section">
      <h3>📋 Recent Orders</h3>
      <div id="ordersTable">Loading orders…</div>
    </div>

  </div>
</main>

<div id="reviewModal" class="modal" style="display:none;position:fixed;inset:0;z-index:20000;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:24px;">
  <div style="width:100%;max-width:640px;background:var(--card);border-radius:16px;padding:24px;position:relative;">
    <button onclick="closeReviewModal()" style="position:absolute;top:16px;right:16px;border:none;background:none;font-size:24px;cursor:pointer;">✕</button>
    <h3 style="margin-top:0">Rate Your Order</h3>
    <form id="reviewForm">
      <input type="hidden" name="order_id" id="reviewOrderId" value="" />
      <div style="display:grid;gap:16px;">
        <div>
          <label><strong>Product Rating</strong></label>
          <select name="product_rating" id="productRating" required>
            <option value="">Select rating</option>
            <option value="1">1 star</option>
            <option value="2">2 stars</option>
            <option value="3">3 stars</option>
            <option value="4">4 stars</option>
            <option value="5">5 stars</option>
          </select>
        </div>
        <div>
          <label><strong>Product Feedback</strong></label>
          <textarea name="product_feedback" id="productFeedback" rows="4" placeholder="Tell us what you liked or what can be improved." style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;"></textarea>
        </div>
        <div>
          <label><strong>Shop / Service Rating</strong></label>
          <select name="shop_rating" id="shopRating" required>
            <option value="">Select rating</option>
            <option value="1">1 star</option>
            <option value="2">2 stars</option>
            <option value="3">3 stars</option>
            <option value="4">4 stars</option>
            <option value="5">5 stars</option>
          </select>
        </div>
        <div>
          <label><strong>Shop / Service Feedback</strong></label>
          <textarea name="shop_feedback" id="shopFeedback" rows="4" placeholder="How was the delivery, customer support, and overall experience?" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;"></textarea>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;">
          <button type="button" onclick="closeReviewModal()" class="btn btn-outline">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Review</button>
        </div>
        <div id="reviewMessage" style="color:var(--danger);font-size:14px;min-height:18px"></div>
      </div>
    </form>
  </div>
</div>

<script>
async function loadDashboard() {
  try {
    const res  = await fetch('api/products.php?action=get_orders');
    const data = await res.json();

    if (!data.ok) {
      document.getElementById('ordersTable').innerHTML = '<p>Could not load orders.</p>';
      return;
    }

    const orders = data.orders || [];
    document.getElementById('totalOrders').textContent     = orders.length;
    document.getElementById('deliveredOrders').textContent = orders.filter(o => o.status === 'Delivered').length;
    document.getElementById('pendingOrders').textContent   = orders.filter(o => o.status === 'Processing').length;

    if (!orders.length) {
      document.getElementById('ordersTable').innerHTML = '<p class="empty-msg">No orders yet. <a href="products.php">Start shopping!</a></p>';
      return;
    }

    document.getElementById('ordersTable').innerHTML = `
      <table class="data-table">
        <thead><tr><th>Order ID</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Review</th></tr></thead>
        <tbody>${orders.map(o => {
          const reviewed = o.product_rating != null || o.shop_rating != null || (o.product_feedback || '').trim() !== '' || (o.shop_feedback || '').trim() !== '';
          const canReview = ['Delivered','Cancelled'].includes(o.status) && !reviewed;
          return `
          <tr>
            <td>#${o.id}</td>
            <td>${new Date(o.date).toLocaleDateString('en-PH')}</td>
            <td>${Array.isArray(o.items) ? o.items.map(i => i.name).join(', ') : '—'}</td>
            <td>₱${Number(o.total).toLocaleString('en-PH')}</td>
            <td><span class="status-badge status-${o.status.replace(/\s/g,'-').toLowerCase()}">${o.status}</span></td>
            <td>${canReview ? `<button class="btn btn-primary" type="button" onclick="openReviewModal(${o.id})">Review</button>` : reviewed ? '<span class="badge badge-success">Reviewed</span>' : '—'}</td>
          </tr>`;
        }).join('')}
        </tbody>
      </table>`;
  } catch(e) {
    document.getElementById('ordersTable').innerHTML = '<p>Error loading orders.</p>';
  }
}

function openReviewModal(orderId) {
  document.getElementById('reviewOrderId').value = orderId;
  document.getElementById('reviewMessage').textContent = '';
  document.getElementById('productRating').value = '';
  document.getElementById('productFeedback').value = '';
  document.getElementById('shopRating').value = '';
  document.getElementById('shopFeedback').value = '';
  document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
  document.getElementById('reviewModal').style.display = 'none';
}

document.getElementById('reviewForm').addEventListener('submit', async function(event) {
  event.preventDefault();
  const formData = new FormData(this);
  const orderId = formData.get('order_id');
  const productRating = formData.get('product_rating');
  const shopRating = formData.get('shop_rating');
  const productFeedback = (formData.get('product_feedback') || '').trim();
  const shopFeedback = (formData.get('shop_feedback') || '').trim();

  if (!orderId || !productRating || !shopRating) {
    document.getElementById('reviewMessage').textContent = 'Please complete both ratings before submitting.';
    return;
  }

  const body = new FormData();
  body.append('order_id', orderId);
  body.append('product_rating', productRating);
  body.append('shop_rating', shopRating);
  body.append('product_feedback', productFeedback);
  body.append('shop_feedback', shopFeedback);

  try {
    const res = await fetch('api/products.php?action=submit_order_feedback', {
      method: 'POST',
      body,
    });
    const data = await res.json();
    if (!data.ok) {
      document.getElementById('reviewMessage').textContent = data.msg || 'Could not submit review.';
      return;
    }
    closeReviewModal();
    loadDashboard();
    alert(data.msg || 'Review submitted successfully.');
  } catch (e) {
    document.getElementById('reviewMessage').textContent = 'Submission failed. Please try again.';
  }
});

loadDashboard();
</script>

<?php include 'includes/footer.php'; ?>
