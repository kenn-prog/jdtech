<?php
// ============================================================
//  JDTech — Footer (included at the bottom of every page)
//  PURPOSE: Closing HTML, footer links, and JS script tags.
// ============================================================
?>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Brand Column -->
      <div class="footer-brand">
        <a href="<?= APP_URL ?>/index.php" class="logo">
          <span class="logo-mark">
            <?php if ($logoImage = getLogoImage()): ?>
              <img src="<?= APP_URL ?>/<?= h($logoImage) ?>" alt="<?= h(APP_NAME) ?>" />
            <?php else: ?>
              <?= h(getLogoIcon()) ?>
            <?php endif; ?>
          </span>JD<span>Tech</span>
        </a>
        <p>Your premium destination for the latest electronics, gadgets, and accessories.</p>
        <div class="socials" style="margin-top:18px;">
          <a class="social" href="https://m.me/hernandezcomputertech" target="_blank" rel="noopener noreferrer" aria-label="Message us on Facebook Messenger"><span class="fb-icon">f</span></a>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="footer-col">
        <h4>Quick Links</h4>
        <a href="<?= APP_URL ?>/index.php">Home</a>
        <a href="<?= APP_URL ?>/products.php">Products</a>
        <a href="<?= APP_URL ?>/about.php">About Us</a>
        <a href="<?= APP_URL ?>/contact.php">Contact</a>
      </div>

      <!-- Account Links -->
      <div class="footer-col">
        <h4>Account</h4>
        <a href="<?= APP_URL ?>/login.php">Login</a>
        <a href="<?= APP_URL ?>/register.php">Register</a>
        <a href="<?= APP_URL ?>/dashboard.php">Dashboard</a>
        <a href="<?= APP_URL ?>/cart.php">Cart</a>
      </div>

      <!-- Legal -->
      <div class="footer-col">
        <h4>Legal</h4>
        <a href="<?= APP_URL ?>/privacy.php">Privacy Policy</a>
        <a href="<?= APP_URL ?>/terms.php">Terms of Service</a>
        <a href="<?= APP_URL ?>/faq.php">FAQ</a>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
    </div>
  </div>
</footer>

<div class="product-modal-overlay" id="productModal" aria-hidden="true" onclick="closeProductModal(event)">
  <div class="product-modal-card" role="dialog" aria-modal="true" aria-labelledby="modalProductName" onclick="event.stopPropagation()">
    <button type="button" class="modal-close" onclick="closeProductModal()" aria-label="Close product details">✕</button>
    <div class="modal-gallery">
      <button type="button" class="modal-nav prev" onclick="changeModalImage(-1)">‹</button>
      <img id="modalProductImage" src="" alt="Product image" />
      <button type="button" class="modal-nav next" onclick="changeModalImage(1)">›</button>
      <div class="modal-count" id="modalImageCount">0 / 0</div>
    </div>
      <div class="modal-details">
      <div>
        <span class="badge" id="modalProductBadge" style="display:none"></span>
        <h2 id="modalProductName">Product Name</h2>
      </div>
      <div class="modal-meta">
        <span id="modalProductRating">Rating</span>
      </div>
      <div class="modal-desc" id="modalProductDescription">Product description goes here.</div>
      <ul class="modal-specs">
        <li><strong>Price</strong><span id="modalProductPrice">₱0.00</span></li>
        <li><strong>Rating</strong><span id="modalProductSpecRating">5★</span></li>
      </ul>
      <button type="button" class="btn btn-primary btn-full" onclick="addToCartFromModal()">Add to Order</button>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="confirmation-modal-overlay" id="confirmationModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;z-index:10000" onclick="closeConfirmation()">
  <div class="confirmation-modal" style="background:var(--card);border-radius:16px;padding:32px;max-width:380px;box-shadow:0 10px 40px rgba(0,0,0,0.2)" onclick="event.stopPropagation()">
    <div class="confirmation-content" style="text-align:center">
      <div style="font-size:48px;margin-bottom:16px" id="confirmationIcon">⚠️</div>
      <h3 id="confirmationTitle" style="margin-bottom:8px">Confirm Action</h3>
      <p id="confirmationMessage" style="color:var(--muted);margin-bottom:24px">Are you sure you want to proceed?</p>
    </div>
    <div class="confirmation-buttons" style="display:flex;gap:12px">
      <button type="button" class="btn btn-secondary" style="flex:1" onclick="closeConfirmation()">Cancel</button>
      <button type="button" class="btn btn-danger" id="confirmationButton" style="flex:1" onclick="performConfirmation()">Delete</button>
    </div>
  </div>
</div>

<!-- Core JavaScript (loaded at end of body for faster page load) -->
<button id="goTop" class="go-top" title="Go to top" aria-label="Go to top">↑</button>
<script src="<?= APP_URL ?>/assets/js/script.js"></script>
<script src="<?= APP_URL ?>/assets/js/validation.js"></script>
<script src="<?= APP_URL ?>/assets/js/ajax.js"></script>

<script>
// Confirmation Modal Functions
window.confirmationAllowed = false;
let pendingConfirmation = null;

window.addEventListener('pageshow', () => {
  window.confirmationAllowed = false;
  pendingConfirmation = null;
});

window.addEventListener('DOMContentLoaded', () => {
  window.confirmationAllowed = false;
});

function showConfirmation(title, message, icon = '⚠️', onConfirm = null) {
  if (!window.confirmationAllowed) {
    console.warn('Confirmation blocked: not in an approved delete flow.');
    return;
  }
  window.confirmationAllowed = false;
  if (typeof onConfirm !== 'function') {
    console.warn('Confirmation skipped: no valid callback provided.');
    return;
  }
  const modal = document.getElementById('confirmationModal');
  document.getElementById('confirmationTitle').textContent = title;
  document.getElementById('confirmationMessage').textContent = message;
  document.getElementById('confirmationIcon').textContent = icon;
  pendingConfirmation = onConfirm;
  modal.style.display = 'flex';
  modal.setAttribute('aria-hidden', 'false');
  document.getElementById('confirmationButton').focus();
}

function closeConfirmation() {
  const modal = document.getElementById('confirmationModal');
  modal.style.display = 'none';
  modal.setAttribute('aria-hidden', 'true');
  pendingConfirmation = null;
  window.confirmationAllowed = false;
}

function performConfirmation() {
  if (pendingConfirmation && typeof pendingConfirmation === 'function') {
    const callback = pendingConfirmation;
    closeConfirmation();
    callback();
  }
}

// Close on Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && document.getElementById('confirmationModal').style.display !== 'none') {
    closeConfirmation();
  }
});
</script>

</body>
</html>
