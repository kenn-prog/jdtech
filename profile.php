<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin(); // 🔒 Protected page

$user      = getUser();
$pageTitle = 'My Profile';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding:60px 0 80px;background:var(--muted-bg);min-height:80vh;">
  <div class="container" style="max-width:720px;">
    <h2 style="margin-bottom:8px;">👤 My Profile</h2>
    <p style="color:var(--muted);margin-bottom:32px;">Update your personal information</p>

    <div id="formMsg" style="display:none;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-size:14px;"></div>

    <!-- Profile Card -->
    <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;margin-bottom:20px;">
      <div style="display:flex;align-items:center;gap:20px;margin-bottom:28px;">
        <div style="font-size:56px;width:80px;height:80px;border-radius:50%;background:var(--muted-bg);display:flex;align-items:center;justify-content:center;border:3px solid var(--border);overflow:hidden;" id="avatarDisplay" data-avatar="<?= h($user['avatar'] ?? '👤') ?>">
        </div>
        <div>
          <h3 style="margin-bottom:4px;"><?= h(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? '')) ?></h3>
          <p style="color:var(--muted);font-size:14px;"><?= h($user['email'] ?? '') ?></p>
          <span style="font-size:12px;background:var(--primary-soft);color:var(--primary);padding:3px 10px;border-radius:999px;font-weight:700;">USER</span>
        </div>
      </div>

      <div class="form-group">
        <label>Profile Photo</label>
        <input type="file" id="avatarInput" accept="image/*" />
        <small style="color:var(--muted);">Upload an image to update your avatar.</small>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" id="firstName" value="<?= h($user['firstName'] ?? '') ?>" placeholder="Juan" />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" id="lastName" value="<?= h($user['lastName'] ?? '') ?>" placeholder="Dela Cruz" />
        </div>
      </div>
      <div class="form-group">
        <label>Email Address <span style="color:var(--muted);font-weight:400;">(cannot be changed)</span></label>
        <input type="email" value="<?= h($user['email'] ?? '') ?>" disabled style="opacity:.6;cursor:not-allowed;" />
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" id="phone" value="<?= h($user['phone'] ?? '') ?>" placeholder="+63 917 000 0000" />
      </div>
      <div class="form-group">
        <label>Contact Number</label>
        <input type="tel" id="contactNumber" value="<?= h($user['contactNumber'] ?? '') ?>" placeholder="+63 917 000 0000" />
      </div>
      <div class="form-group">
        <label>Delivery Address</label>
        <textarea id="address" rows="3" placeholder="House no., street, barangay, city"><?= h($user['address'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Mode of Payment</label>
        <select id="paymentMethod">
          <option value="">Select a payment method</option>
          <option value="GCash" <?= isset($user['paymentMethod']) && $user['paymentMethod'] === 'GCash' ? 'selected' : '' ?>>GCash</option>
          <option value="Maya" <?= isset($user['paymentMethod']) && $user['paymentMethod'] === 'Maya' ? 'selected' : '' ?>>Maya</option>
          <option value="Credit Card" <?= isset($user['paymentMethod']) && $user['paymentMethod'] === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
          <option value="Bank Transfer" <?= isset($user['paymentMethod']) && $user['paymentMethod'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
          <option value="Cash on Delivery" <?= isset($user['paymentMethod']) && $user['paymentMethod'] === 'Cash on Delivery' ? 'selected' : '' ?>>Cash on Delivery</option>
        </select>
      </div>

      <button class="btn btn-primary" onclick="saveProfile()" id="saveBtn">
        💾 Save Changes
      </button>
    </div>

    <!-- Danger Zone -->
    <div style="background:var(--card);border:1px solid #fecaca;border-radius:20px;padding:28px;">
      <h3 style="color:#dc2626;margin-bottom:8px;">⚠️ Danger Zone</h3>
      <p style="color:var(--muted);font-size:14px;margin-bottom:16px;">Permanently delete your account and all associated data. This cannot be undone.</p>
      <button class="btn" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;" onclick="showDeleteConfirm()">
        🗑️ Delete My Account
      </button>

      <div id="deleteConfirm" style="display:none;margin-top:20px;padding:20px;background:var(--muted-bg);border-radius:12px;">
        <p style="font-size:14px;font-weight:600;margin-bottom:12px;">Enter your password to confirm deletion:</p>
        <input type="password" id="deletePassword" placeholder="Your current password" style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:14px;background:var(--bg);color:var(--fg);margin-bottom:12px;" />
        <div style="display:flex;gap:10px;">
          <button class="btn" style="background:#fee2e2;color:#dc2626;border:1px solid #fecaca;" onclick="deleteAccount()">Yes, Delete My Account</button>
          <button class="btn btn-outline" onclick="document.getElementById('deleteConfirm').style.display='none'">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
function renderAvatar() {
  const el = document.getElementById('avatarDisplay');
  const avatar = el?.dataset?.avatar || '👤';
  if (!el) return;

  if (typeof avatar === 'string' && avatar.startsWith('uploads/')) {
    el.innerHTML = `<img src="${avatar}" alt="Profile avatar" style="width:100%;height:100%;object-fit:cover;" />`;
  } else {
    el.textContent = avatar || '👤';
  }
}

async function saveProfile() {
  const firstName     = document.getElementById('firstName').value.trim();
  const lastName      = document.getElementById('lastName').value.trim();
  const phone         = document.getElementById('phone').value.trim();
  const contactNumber = document.getElementById('contactNumber').value.trim();
  const address       = document.getElementById('address').value.trim();
  const paymentMethod = document.getElementById('paymentMethod').value;
  const avatarInput   = document.getElementById('avatarInput');
  const btn           = document.getElementById('saveBtn');

  if (!firstName || !lastName) { showMsg('First and last name are required.', 'error'); return; }
  if (!address) { showMsg('Delivery address is required.', 'error'); return; }
  if (!paymentMethod) { showMsg('Please select a payment method.', 'error'); return; }
  if (!contactNumber) { showMsg('Contact number is required.', 'error'); return; }

  btn.disabled    = true;
  btn.textContent = 'Saving…';

  const form = new FormData();
  form.append('first_name',     firstName);
  form.append('last_name',      lastName);
  form.append('phone',          phone);
  form.append('contact_number', contactNumber);
  form.append('address',        address);
  form.append('payment_method', paymentMethod);

  if (avatarInput.files.length) {
    const avatarForm = new FormData();
    avatarForm.append('type', 'profile');
    avatarForm.append('file', avatarInput.files[0]);

    const uploadRes = await fetch('backend/upload.php', { method: 'POST', body: avatarForm });
    const uploadData = await uploadRes.json();
    if (!uploadData.ok) {
      btn.disabled = false;
      btn.textContent = '💾 Save Changes';
      showMsg(uploadData.msg || 'Could not upload profile photo.', 'error');
      return;
    }
    form.append('avatar_path', uploadData.path);
  }

  const res  = await fetch('backend/update-profile.php', { method:'POST', body:form });
  const data = await res.json();

  btn.disabled    = false;
  btn.textContent = '💾 Save Changes';

  if (data.ok) {
    if (data.user?.avatar) {
      document.getElementById('avatarDisplay').dataset.avatar = data.user.avatar;
      renderAvatar();
    }
    showMsg('Profile updated successfully!', 'success');
  } else {
    showMsg(data.msg || 'Update failed.', 'error');
  }
}

function showDeleteConfirm() {
  document.getElementById('deleteConfirm').style.display = 'block';
}

async function deleteAccount() {
  const password = document.getElementById('deletePassword').value;
  if (!password) { alert('Please enter your password.'); return; }

  const form = new FormData();
  form.append('password', password);

  const res  = await fetch('backend/delete-account.php', { method:'POST', body:form });
  const data = await res.json();

  if (data.ok) {
    alert('Your account has been deleted. Goodbye!');
    window.location.href = 'index.php';
  } else {
    alert(data.msg || 'Deletion failed. Check your password.');
  }
}

function showMsg(msg, type) {
  const el = document.getElementById('formMsg');
  el.textContent  = msg;
  el.style.display = 'block';
  el.style.background = type === 'success' ? '#d1fae5' : '#fee2e2';
  el.style.color      = type === 'success' ? '#065f46' : '#991b1b';
  el.style.border     = '1px solid ' + (type === 'success' ? '#a7f3d0' : '#fecaca');
  setTimeout(() => { el.style.display = 'none'; }, 4000);
}

renderAvatar();
</script>

<?php include 'includes/footer.php'; ?>
