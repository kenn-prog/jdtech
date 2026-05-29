<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireGuest(); // Already logged in? Redirect away.

$pageTitle = 'Register';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main class="auth-page">
  <div class="auth-container">
    <div class="auth-card">

      <div class="auth-header">
        <a href="index.php" class="logo">
          <span class="logo-mark">
            <?php if ($logoImage = getLogoImage()): ?>
              <img src="<?= APP_URL ?>/<?= h($logoImage) ?>" alt="<?= h(APP_NAME) ?>" />
            <?php else: ?>
              <?= h(getLogoIcon()) ?>
            <?php endif; ?>
          </span>JD<span>Tech</span>
        </a>
        <h1>Create Account</h1>
        <p>Join JDTech and start shopping</p>
      </div>

      <div id="formError" class="form-error" style="display:none"></div>

      <div class="form-row">
        <div class="form-group">
          <label for="first_name">First Name</label>
          <input type="text" id="first_name" placeholder="Juan" required />
        </div>
        <div class="form-group">
          <label for="last_name">Last Name</label>
          <input type="text" id="last_name" placeholder="Dela Cruz" required />
        </div>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" placeholder="you@email.com" required />
      </div>
      <div class="form-group">
        <label for="phone">Phone Number (optional)</label>
        <input type="tel" id="phone" placeholder="+63 917 000 0000" />
      </div>
      <div class="form-group">
        <label for="password">Password <small>(min. 8 characters)</small></label>
        <input type="password" id="password" placeholder="Create a strong password" required />
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" placeholder="Repeat your password" required />
      </div>

      <button class="btn btn-primary btn-full" id="registerBtn" onclick="handleRegister()">
        Create Account
      </button>

      <p class="auth-switch">
        Already have an account? <a href="login.php">Sign in here</a>
      </p>
    </div>
  </div>
</main>

<script>
async function handleRegister() {
  const firstName  = document.getElementById('first_name').value.trim();
  const lastName   = document.getElementById('last_name').value.trim();
  const email      = document.getElementById('email').value.trim();
  const phone      = document.getElementById('phone').value.trim();
  const password   = document.getElementById('password').value;
  const confirmPw  = document.getElementById('confirm_password').value;
  const btn        = document.getElementById('registerBtn');

  if (!firstName || !lastName || !email || !password) {
    showError('Please fill in all required fields.'); return;
  }
  if (password.length < 8) {
    showError('Password must be at least 8 characters.'); return;
  }
  if (password !== confirmPw) {
    showError('Passwords do not match.'); return;
  }

  btn.disabled    = true;
  btn.textContent = 'Creating account…';

  const form = new FormData();
  form.append('first_name', firstName);
  form.append('last_name',  lastName);
  form.append('email',      email);
  form.append('phone',      phone);
  form.append('password',   password);

  try {
    const res  = await fetch('backend/register-process.php', { method: 'POST', body: form });
    const data = await res.json();
    if (data.ok) {
      window.location.href = 'dashboard.php';
    } else {
      showError(data.msg || 'Registration failed. Please try again.');
      btn.disabled    = false;
      btn.textContent = 'Create Account';
    }
  } catch(e) {
    showError('Network error. Please try again.');
    btn.disabled    = false;
    btn.textContent = 'Create Account';
  }
}

function showError(msg) {
  const div = document.getElementById('formError');
  div.textContent = msg;
  div.style.display = 'block';
}
</script>

<?php include 'includes/footer.php'; ?>
