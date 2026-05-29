<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin(); // 🔒 Must be logged in

$user      = getUser();
$pageTitle = 'Settings';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding:60px 0 80px;background:var(--muted-bg);min-height:80vh;">
  <div class="container" style="max-width:720px;">
    <h2 style="margin-bottom:8px;">⚙️ Account Settings</h2>
    <p style="color:var(--muted);margin-bottom:32px;">Manage your preferences and security</p>

    <!-- Change Password -->
    <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;margin-bottom:20px;">
      <h3 style="margin-bottom:20px;">🔒 Change Password</h3>
      <div id="pwMsg" style="display:none;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:14px;"></div>
      <div class="form-group">
        <label>Current Password</label>
        <input type="password" id="currentPw" placeholder="Your current password" />
      </div>
      <div class="form-group">
        <label>New Password <small>(min. 8 characters)</small></label>
        <input type="password" id="newPw" placeholder="Choose a strong password" />
      </div>
      <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" id="confirmPw" placeholder="Repeat new password" />
      </div>
      <button class="btn btn-primary" onclick="changePassword()">Update Password</button>
    </div>

    <!-- Account Info (read-only summary) -->
    <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;">
      <h3 style="margin-bottom:20px;">📋 Account Information</h3>
      <div style="display:grid;gap:12px;">
        <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);font-size:14px;">
          <span style="color:var(--muted);">Email</span>
          <span style="font-weight:600;"><?= h($user['email'] ?? '—') ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);font-size:14px;">
          <span style="color:var(--muted);">Role</span>
          <span style="font-weight:600;text-transform:capitalize;"><?= h($user['role'] ?? 'user') ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:14px;">
          <span style="color:var(--muted);">Member Since</span>
          <span style="font-weight:600;"><?= isset($user['joinedAt']) ? formatDate($user['joinedAt']) : '—' ?></span>
        </div>
      </div>
      <div style="margin-top:20px;">
        <a href="profile.php" class="btn btn-outline">✏️ Edit Profile Details</a>
      </div>
    </div>
  </div>
</main>

<script>
async function changePassword() {
  const currentPw = document.getElementById('currentPw').value;
  const newPw     = document.getElementById('newPw').value;
  const confirmPw = document.getElementById('confirmPw').value;

  if (!currentPw || !newPw || !confirmPw) { showPwMsg('All fields are required.', 'error'); return; }
  if (newPw.length < 8)                   { showPwMsg('New password must be at least 8 characters.', 'error'); return; }
  if (newPw !== confirmPw)                { showPwMsg('New passwords do not match.', 'error'); return; }

  const form = new FormData();
  form.append('current_password', currentPw);
  form.append('new_password',     newPw);

  const res  = await fetch('backend/change-password.php', { method: 'POST', body: form });
  const data = await res.json();
  showPwMsg(data.msg || (data.ok ? 'Password changed!' : 'Failed.'), data.ok ? 'success' : 'error');

  if (data.ok) {
    document.getElementById('currentPw').value = '';
    document.getElementById('newPw').value     = '';
    document.getElementById('confirmPw').value = '';
  }
}

function showPwMsg(msg, type) {
  const el = document.getElementById('pwMsg');
  el.textContent   = msg;
  el.style.display = 'block';
  el.style.background = type === 'success' ? '#d1fae5' : '#fee2e2';
  el.style.color      = type === 'success' ? '#065f46' : '#991b1b';
  el.style.border     = '1px solid ' + (type === 'success' ? '#a7f3d0' : '#fecaca');
}
</script>

<?php include 'includes/footer.php'; ?>
