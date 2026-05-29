<?php
// ============================================================
//  JDTech — Login Page
//  PURPOSE: Allows users and admins to sign in.
//
//  AUTHENTICATION FLOW:
//  1. User types email + password and submits
//  2. JavaScript sends data to backend/login-process.php via fetch()
//  3. PHP checks credentials against the database
//  4. If valid: saves user data to $_SESSION, returns JSON {ok: true}
//  5. JavaScript redirects to dashboard or admin panel
//  6. If invalid: returns JSON {ok: false, msg: '...'} and shows error
// ============================================================

require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// If already logged in, redirect away from login page
requireGuest();

$pageTitle = 'Login';
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
        <h1>Welcome back</h1>
        <p>Sign in to your account</p>
      </div>

      <!-- Demo credentials removed in production -->

      <!-- Login Form -->
      <div id="formError" class="form-error" style="display:none"></div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@email.com" autocomplete="email" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password" required />
      </div>

      <button class="btn btn-primary btn-full" id="loginBtn" onclick="handleLogin()">
        Sign In
      </button>

      <p class="auth-switch">
        Don't have an account? <a href="register.php">Register here</a>
      </p>
    </div>
  </div>
</main>

<script>


async function handleLogin() {
  const email    = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value;
  const errorDiv = document.getElementById('formError');
  const btn      = document.getElementById('loginBtn');

  // Basic client-side validation
  if (!email || !password) {
    showError('Please enter your email and password.'); return;
  }

  btn.disabled   = true;
  btn.textContent = 'Signing in…';

  try {
    const form = new FormData();
    form.append('email',    email);
    form.append('password', password);

    const res  = await fetch('backend/login-process.php', { method: 'POST', body: form });
    const data = await res.json();

    if (data.ok) {
      // Login success — redirect based on role
      window.location.href = data.user.role === 'admin' ? 'admin/index.php' : 'dashboard.php';
    } else {
      showError(data.msg || 'Invalid email or password.');
      btn.disabled   = false;
      btn.textContent = 'Sign In';
    }
  } catch (e) {
    showError('Network error. Please try again.');
    btn.disabled   = false;
    btn.textContent = 'Sign In';
  }
}

function showError(msg) {
  const div = document.getElementById('formError');
  div.textContent = msg;
  div.style.display = 'block';
}

// Allow pressing Enter to submit
document.addEventListener('keydown', e => { if (e.key === 'Enter') handleLogin(); });
</script>

<?php include 'includes/footer.php'; ?>
