<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'My Cart';
$isAdmin = isAdmin();

include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding: 60px 0 80px; background: var(--muted-bg); min-height: 80vh;">
  <div class="container">
    <h2 style="margin-bottom:8px;">🛒 My Cart</h2>
    <p style="color:var(--muted);margin-bottom:32px;">Review your items before checkout</p>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;align-items:start;">

      <!-- Cart Items -->
      <div>
        <div id="cartItems"></div>
      </div>

      <!-- Order Summary -->
      <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:28px;position:sticky;top:80px;">
        <h3 style="margin-bottom:20px;">Order Summary</h3>
        <div id="orderSummary" style="margin-bottom:20px;"></div>
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:20px;">
          <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:700;">
            <span>Total</span>
            <span id="cartTotal" style="color:var(--primary)">₱0.00</span>
          </div>
        </div>

        <?php if (!$isAdmin): ?>
        <!-- Checkout Section (Hidden for Admin) -->
        <div style="margin-bottom:16px;">
          <label style="display:block;font-weight:700;margin-bottom:8px;">Delivery Address</label>
          <textarea id="orderAddress" rows="3" placeholder="House no., street, barangay, city, province, postal code" style="width:100%;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg);color:var(--fg);resize:vertical;font-size:14px;"></textarea>
          <small style="color:var(--muted);display:block;margin-top:8px;">Please provide your full delivery address. Include street, city, province/state, postal code, and any notes.</small>
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block;font-weight:700;margin-bottom:8px;">Contact Number</label>
          <input id="orderContact" type="tel" style="width:100%;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg);color:var(--fg);" placeholder="+63 917 000 0000" />
        </div>
        <div style="margin-bottom:20px;">
          <label style="display:block;font-weight:700;margin-bottom:8px;">Mode of Payment</label>
          <select id="orderPayment" style="width:100%;padding:12px;border:1px solid var(--border);border-radius:14px;background:var(--bg);color:var(--fg);">
            <option value="">Select payment method</option>
            <option value="GCash">GCash</option>
            <option value="Maya">Maya</option>
            <option value="Visa Card">Visa Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
            <option value="Cash on Delivery">Cash on Delivery</option>
          </select>
        </div>

        <!-- Payment Card Fields (Hidden by default) -->
        <div id="cardPaymentBox" style="display:none;margin-bottom:20px;padding:16px;background:var(--muted-bg);border-radius:12px;">
          <div style="margin-bottom:12px;">
            <label style="display:block;font-weight:700;margin-bottom:6px;font-size:13px;">Card Holder Name</label>
            <input id="cardHolderName" type="text" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;background:var(--bg);color:var(--fg);font-size:13px;" placeholder="John Doe" />
          </div>
          <div style="margin-bottom:12px;">
            <label style="display:block;font-weight:700;margin-bottom:6px;font-size:13px;">Card Number</label>
            <input id="cardNumber" type="text" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;background:var(--bg);color:var(--fg);font-size:13px;" placeholder="1234 5678 9012 3456" maxlength="19" />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div>
              <label style="display:block;font-weight:700;margin-bottom:6px;font-size:13px;">Expiration Date</label>
              <input id="cardExpiry" type="text" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;background:var(--bg);color:var(--fg);font-size:13px;" placeholder="MM/YY" maxlength="5" />
            </div>
            <div>
              <label style="display:block;font-weight:700;margin-bottom:6px;font-size:13px;">CVV</label>
              <input id="cardCVV" type="text" style="width:100%;padding:10px;border:1px solid var(--border);border-radius:12px;background:var(--bg);color:var(--fg);font-size:13px;" placeholder="123" maxlength="3" />
            </div>
          </div>
        </div>
        <button class="btn btn-primary" style="width:100%;font-size:16px;" onclick="checkout()" id="checkoutBtn">
          Checkout
        </button>
        <a href="products.php" style="display:block;text-align:center;margin-top:12px;font-size:14px;color:var(--muted);">
          ← Continue Shopping
        </a>
        <?php else: ?>
        <!-- Admin Notice -->
        <div style="padding:16px;background:var(--muted-bg);border-radius:12px;text-align:center;margin-bottom:12px;">
          <p style="color:var(--muted);font-size:14px;">Admin accounts do not require checkout. <a href="admin/index.php" style="color:var(--primary);text-decoration:underline;">Go to Dashboard</a></p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<div id="toast" class="toast"></div>

<script>
function getCart() {
  try { return JSON.parse(sessionStorage.getItem('jdcart') || '[]'); } catch { return []; }
}
function saveCart(cart) {
  sessionStorage.setItem('jdcart', JSON.stringify(cart));
}

function renderCart() {
  const cart      = getCart();
  const itemsDiv  = document.getElementById('cartItems');
  const summaryEl = document.getElementById('orderSummary');
  const totalEl   = document.getElementById('cartTotal');
  const checkBtn  = document.getElementById('checkoutBtn');

  if (!cart.length) {
    itemsDiv.innerHTML = `
      <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:60px;text-align:center;">
        <div style="font-size:56px;margin-bottom:16px;">🛒</div>
        <h3 style="margin-bottom:8px;">Your cart is empty</h3>
        <p style="color:var(--muted);margin-bottom:24px;">Add some products to get started!</p>
        <a href="products.php" class="btn btn-primary">Browse Products</a>
      </div>`;
    summaryEl.innerHTML = '<p style="color:var(--muted);font-size:14px;">No items yet.</p>';
    totalEl.textContent = '₱0.00';
    checkBtn.disabled   = true;
    return;
  }

  checkBtn.disabled = false;
  let total = 0;

  itemsDiv.innerHTML = cart.map((item, idx) => {
    const subtotal = item.price * item.qty;
    total += subtotal;
    return `
      <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:20px;margin-bottom:12px;display:flex;align-items:center;gap:16px;">
        <div style="font-size:36px;width:60px;height:60px;display:flex;align-items:center;justify-content:center;background:var(--muted-bg);border-radius:12px;flex-shrink:0;">📦</div>
        <div style="flex:1;">
          <div style="font-weight:700;margin-bottom:4px;">${item.name}</div>
          <div style="color:var(--primary);font-weight:600;">₱${Number(item.price).toLocaleString('en-PH')}</div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <button onclick="changeQty(${idx},-1)" style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">−</button>
          <span style="font-weight:700;min-width:24px;text-align:center;">${item.qty}</span>
          <button onclick="changeQty(${idx},1)"  style="width:32px;height:32px;border-radius:8px;border:1px solid var(--border);background:var(--bg);cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;">+</button>
        </div>
        <div style="font-weight:700;min-width:100px;text-align:right;">₱${subtotal.toLocaleString('en-PH', {minimumFractionDigits:2})}</div>
        <button onclick="removeItem(${idx})" style="color:#dc2626;background:none;border:none;cursor:pointer;font-size:20px;padding:4px;">✕</button>
      </div>`;
  }).join('');

  summaryEl.innerHTML = cart.map(item => `
    <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:8px;">
      <span style="color:var(--muted);">${item.name} × ${item.qty}</span>
      <span>₱${(item.price * item.qty).toLocaleString('en-PH', {minimumFractionDigits:2})}</span>
    </div>`).join('');

  totalEl.textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits:2});
}

function changeQty(idx, delta) {
  const cart = getCart();
  cart[idx].qty += delta;
  if (cart[idx].qty <= 0) cart.splice(idx, 1);
  saveCart(cart);
  updateBadge();
  renderCart();
}

function removeItem(idx) {
  const cart = getCart();
  cart.splice(idx, 1);
  saveCart(cart);
  updateBadge();
  renderCart();
}

function updateBadge() {
  const count = getCart().reduce((s,i) => s+i.qty, 0);
  document.querySelectorAll('.cart-count').forEach(el => {
    el.textContent = count;
    el.classList.toggle('visible', count > 0);
  });
}

// Payment method handler - show/hide card fields
function setupPaymentMethodListener() {
  const paymentSelect = document.getElementById('orderPayment');
  const cardBox = document.getElementById('cardPaymentBox');
  
  if (!paymentSelect || !cardBox) return;

  paymentSelect.addEventListener('change', (e) => {
    if (e.target.value === 'Visa Card') {
      cardBox.style.display = 'block';
    } else {
      cardBox.style.display = 'none';
      // Clear card fields when hiding
      document.getElementById('cardHolderName').value = '';
      document.getElementById('cardNumber').value = '';
      document.getElementById('cardExpiry').value = '';
      document.getElementById('cardCVV').value = '';
    }
  });
}

async function checkout() {
  const cart  = getCart();
  const total = cart.reduce((s,i) => s + i.price * i.qty, 0);
  if (!cart.length) return;

  const btn = document.getElementById('checkoutBtn');
  btn.disabled    = true;
  btn.textContent = 'Placing order…';

  // Check if user is logged in
  const session = await fetch('api/auth.php?action=check').then(r => r.json());
  if (!session.loggedIn) {
    sessionStorage.setItem('jdcart', JSON.stringify(cart)); // Keep cart
    window.location.href = 'login.php';
    return;
  }

  // Get address from manual textarea
  const address       = document.getElementById('orderAddress').value.trim();
  const contactNumber = document.getElementById('orderContact').value.trim();
  const paymentMethod = document.getElementById('orderPayment').value;

  if (!address || address.length < 10) {
    showToast('Please enter your complete delivery address (min 10 characters).');
    btn.disabled = false;
    btn.textContent = 'Checkout';
    return;
  }
  if (!contactNumber) {
    showToast('Please enter your contact number.');
    btn.disabled = false;
    btn.textContent = 'Checkout';
    return;
  }
  if (!paymentMethod) {
    showToast('Please select a mode of payment.');
    btn.disabled = false;
    btn.textContent = 'Checkout';
    return;
  }

  const form = new FormData();
  form.append('action', 'add_order');
  form.append('items',  JSON.stringify(cart));
  form.append('total',  total);
  form.append('address', address);
  form.append('contact_number', contactNumber);
  form.append('payment_method', paymentMethod);

  const res  = await fetch('api/products.php?action=add_order', { method: 'POST', body: form });
  const data = await res.json();

  if (data.ok) {
    sessionStorage.removeItem('jdcart');
    updateBadge();
    const t = document.getElementById('toast');
    t.textContent = '🎉 Order placed! Redirecting to dashboard…';
    t.classList.add('visible');
    setTimeout(() => window.location.href = 'dashboard.php', 2000);
  } else {
    btn.disabled    = false;
    btn.textContent = 'Checkout';
    showToast(data.msg || 'Checkout failed. Please try again.');
  }
}

function showToast(message) {
  const t = document.getElementById('toast');
  t.textContent = message;
  t.classList.add('visible');
  setTimeout(() => t.classList.remove('visible'), 3000);
}

renderCart();
setupPaymentMethodListener();
</script>

<?php include 'includes/footer.php'; ?>
