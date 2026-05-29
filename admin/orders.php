<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$isAdmin   = true;
$pageTitle = 'Manage Orders';
include '../includes/header.php';
?>
<div class="admin-layout">
  <?php include '../includes/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar"><h1>🧾 Orders</h1></div>
    <div id="ordersTable">Loading…</div>
  </main>
</div>
<script>
window.adminOrders = [];

async function loadOrders() {
  const res = await fetch('../api/users.php?action=get_orders_admin', { credentials: 'same-origin' });
  const data = await res.json();
  const orders = data.orders || [];
  window.adminOrders = orders;
  if (!orders.length) {
    document.getElementById('ordersTable').innerHTML = '<p class="empty-msg">No orders yet.</p>';
    return;
  }
  document.getElementById('ordersTable').innerHTML = `
    <table class="data-table">
      <thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th><th>Visible</th><th>Actions</th></tr></thead>
      <tbody>${orders.map(o => {
        const visible = o.show_feedback ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>';
        return `
        <tr>
          <td>#${o.id}</td>
          <td>${o.first_name||''} ${o.last_name||''}<br><small>${o.email||''}</small></td>
          <td>${(o.items||[]).map(i=>i.name).join(', ')}</td>
          <td>₱${Number(o.total).toLocaleString('en-PH')}</td>
          <td>
            <select onchange="updateStatus(${o.id},this.value)">
              ${['Processing','On the Way','Delivered','Cancelled'].map(s=>`<option ${o.status===s?'selected':''}>${s}</option>`).join('')}
            </select>
          </td>
          <td>${new Date(o.date).toLocaleDateString('en-PH')}</td>
          <td>${visible}</td>
          <td><button class="btn btn-ghost" type="button" onclick="viewOrder(${o.id})">View</button></td>
        </tr>`;
      }).join('')}
      </tbody>
    </table>`;
}

async function updateStatus(id,status) {
  const f = new FormData(); f.append('id', id); f.append('status', status);
  await fetch('../api/users.php?action=update_order_status', { method: 'POST', body: f, credentials: 'same-origin' });
}

function closeOrderModal() {
  document.getElementById('orderModal').style.display = 'none';
}

async function toggleOrderFeedbackVisibility() {
  const btn = document.getElementById('modalToggleFeedback');
  const orderId = btn.dataset.orderId;
  const currentShow = btn.dataset.currentShow === '1';
  const newShow = currentShow ? 0 : 1;
  const fd = new FormData();
  fd.append('id', orderId);
  fd.append('show', newShow);

  try {
    const res = await fetch('../backend/toggle-feedback.php', { method: 'POST', body: fd, credentials: 'same-origin' });
    const data = await res.json();
    if (!data.ok) {
      alert(data.msg || 'Failed to update visibility.');
      return;
    }
    const order = window.adminOrders.find(x => String(x.id) === String(orderId));
    if (order) {
      order.show_feedback = newShow;
    }
    document.getElementById('modalFeedbackStatus').textContent = newShow ? 'Visible on homepage' : 'Hidden from homepage';
    btn.textContent = newShow ? 'Hide from homepage' : 'Show on homepage';
    btn.dataset.currentShow = newShow ? '1' : '0';
    loadOrders();
  } catch (err) {
    alert('Could not update visibility.');
  }
}

function viewOrder(id) {
  const orders = window.adminOrders || [];
  const o = orders.find(x => Number(x.id) === Number(id));
  if (!o) return;
  document.getElementById('modalOrderId').textContent = '#' + o.id;
  document.getElementById('modalCustomer').innerHTML = (o.first_name||'') + ' ' + (o.last_name||'') + '<br><small>' + (o.email||'') + '</small>';
  document.getElementById('modalAddress').textContent = o.delivery_address || o.address || 'Not provided';
  document.getElementById('modalItems').innerHTML = (o.items||[]).map(i => '<div style="padding:8px 0;border-bottom:1px solid var(--border);"><strong>' + (i.name||'') + '</strong> × ' + (i.qty||1) + ' — ₱' + (Number(i.price||0)).toLocaleString('en-PH') + '</div>').join('');
  document.getElementById('modalTotal').textContent = '₱' + Number(o.total).toLocaleString('en-PH');
  document.getElementById('modalContact').textContent = o.contact_number || '';
  document.getElementById('modalPayment').textContent = o.payment_method || '';
  document.getElementById('modalStatus').textContent = o.status || '';
  document.getElementById('modalProductReview').innerHTML = o.product_rating ? `Rating: ${o.product_rating}/5<br>${(o.product_feedback || '').replace(/\n/g, '<br>')}` : 'No product review yet.';
  document.getElementById('modalShopReview').innerHTML = o.shop_rating ? `Rating: ${o.shop_rating}/5<br>${(o.shop_feedback || '').replace(/\n/g, '<br>')}` : 'No shop review yet.';
  const showBtn = document.getElementById('modalToggleFeedback');
  if (o.shop_feedback) {
    const currentShow = o.show_feedback ? 1 : 0;
    showBtn.style.display = 'inline-flex';
    showBtn.dataset.orderId = o.id;
    showBtn.dataset.currentShow = currentShow ? '1' : '0';
    showBtn.textContent = currentShow ? 'Hide from homepage' : 'Show on homepage';
    document.getElementById('modalFeedbackStatus').textContent = currentShow ? 'Visible on homepage' : 'Hidden from homepage';
  } else {
    showBtn.style.display = 'none';
    document.getElementById('modalFeedbackStatus').textContent = 'No shop feedback available.';
  }
  document.getElementById('orderModal').style.display = 'flex';
}

loadOrders();
</script>

<!-- Order Details Modal -->
<div id="orderModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:24px;z-index:20000">
  <div style="width:100%;max-width:820px;background:var(--card);border-radius:16px;padding:20px;border:1px solid var(--border);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <h3 style="margin:0">Order <span id="modalOrderId"></span></h3>
      <button onclick="closeOrderModal()" style="background:none;border:0;font-size:20px;cursor:pointer">✕</button>
    </div>
    <div id="modalOrderContent" style="display:grid;grid-template-columns:1fr 320px;gap:16px;">
      <div>
        <h4>Customer</h4>
        <div id="modalCustomer"></div>
        <h4 style="margin-top:16px">Delivery Address</h4>
        <div id="modalAddress" style="white-space:pre-wrap;word-break:break-word;color:var(--muted);"></div>
        <h4 style="margin-top:16px">Items</h4>
        <div id="modalItems"></div>
      </div>
      <aside style="background:var(--muted-bg);padding:16px;border-radius:12px;">
        <div><strong>Total:</strong> <span id="modalTotal"></span></div>
        <div style="margin-top:8px"><strong>Contact:</strong> <span id="modalContact"></span></div>
        <div style="margin-top:8px"><strong>Payment:</strong> <span id="modalPayment"></span></div>
        <div style="margin-top:12px"><strong>Status:</strong> <span id="modalStatus"></span></div>
        <div style="margin-top:12px"><strong>Product Review:</strong> <div id="modalProductReview" style="margin-top:6px;line-height:1.4;color:var(--muted);"></div></div>
        <div style="margin-top:12px"><strong>Shop Review:</strong> <div id="modalShopReview" style="margin-top:6px;line-height:1.4;color:var(--muted);"></div></div>
        <div style="margin-top:12px"><strong>Homepage Visibility:</strong> <div id="modalFeedbackStatus" style="margin-top:6px;line-height:1.4;color:var(--muted);"></div></div>
        <button id="modalToggleFeedback" type="button" class="btn btn-primary" style="margin-top:12px;display:none;" onclick="toggleOrderFeedbackVisibility()">Toggle Homepage Visibility</button>
      </aside>
    </div>
  </div>
</div>

<?php echo '</div></body></html>'; ?>
