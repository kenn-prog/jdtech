/**
 * JDTech — Main JavaScript
 * PURPOSE: UI interactions that run on every page.
 *          Cart badge updates, toast messages, chat widget.
 *
 * HOW FRONTEND CONNECTS TO BACKEND (explained here):
 * 1. When the page loads, JS reads sessionStorage for cart
 * 2. When user clicks "Add to Cart", JS updates sessionStorage
 * 3. When user checks out, JS sends cart data to api/products.php
 *    via fetch() — this is called an AJAX (or Fetch API) request
 * 4. PHP receives it via $_POST, saves to MySQL, returns JSON
 * 5. JS reads the JSON response and updates the UI
 *
 * No page reload needed! This is how modern web apps work.
 */

// ── Cart Utilities ─────────────────────────────────────────

/**
 * Get cart from sessionStorage.
 * sessionStorage is erased when the browser tab is closed.
 * Use localStorage if you want it to persist.
 */
function getCart() {
  try {
    return JSON.parse(sessionStorage.getItem('jdcart') || '[]');
  } catch {
    return [];
  }
}

function saveCart(cart) {
  sessionStorage.setItem('jdcart', JSON.stringify(cart));
}

function getCartCount() {
  return getCart().reduce((sum, item) => sum + (item.qty || 1), 0);
}

function updateCartBadge() {
  const count = getCartCount();
  document.querySelectorAll('.cart-count').forEach(el => {
    el.textContent = count;
    el.classList.toggle('visible', count > 0);
  });
}

function getProductImages(product) {
  if (!product || !product.image_url) return [];
  const items = product.image_url.split(/[,|;]+/).map(i => i.trim()).filter(Boolean);
  return items.length ? items : [product.image_url];
}

function normalizeBadge(badge) {
  if (!badge) return '';
  const raw = String(badge).trim().toLowerCase();
  if (raw === 'new') return 'Pre Order';
  if (raw === 'hot') return 'Onhand';
  if (raw === 'sale') return '';
  return badge;
}

function showProductModal(product) {
  if (!product) return;
  window.modalProduct = product;
  const images = getProductImages(product);
  const imageIndex = 0;
  const modal = document.getElementById('productModal');
  if (!modal) return;
  modal.dataset.images = JSON.stringify(images);
  modal.dataset.imageIndex = imageIndex;

  const imageEl = document.getElementById('modalProductImage');
  if (imageEl) {
    imageEl.src = images[imageIndex] || '';
    imageEl.alt = product.name || 'Product Image';
  }

  const setTextContent = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
  };

  setTextContent('modalProductName', product.name || 'Product');
  setTextContent('modalProductPrice', `₱${Number(product.price || 0).toLocaleString('en-PH')}`);
  setTextContent('modalProductRating', `${product.rating || 5}★`);
  setTextContent('modalProductDescription', product.description || 'No additional details available.');
  setTextContent('modalProductDescription', product.description || 'No additional details available.');

  const badge = document.getElementById('modalProductBadge');
  if (badge) {
    const text = normalizeBadge(product.badge);
    if (text) {
      badge.textContent = text;
      badge.style.display = 'inline-flex';
    } else {
      badge.style.display = 'none';
    }
  }

  const imageCount = document.getElementById('modalImageCount');
  if (imageCount) {
    imageCount.textContent = `${imageIndex + 1} / ${images.length || 1}`;
  }

  try {
    renderProductSpecs(product);
  } catch (e) {
    console.error('Error rendering product specs', e);
  }

  modal.classList.add('open');
  modal.style.display = 'flex';
  modal.setAttribute('aria-hidden', 'false');
}

function changeModalImage(direction) {
  const modal = document.getElementById('productModal');
  if (!modal) return;
  const images = JSON.parse(modal.dataset.images || '[]');
  if (!images.length) return;
  let index = Number(modal.dataset.imageIndex || 0) + direction;
  if (index < 0) index = images.length - 1;
  if (index >= images.length) index = 0;
  modal.dataset.imageIndex = index;
  const imageEl = document.getElementById('modalProductImage');
  imageEl.src = images[index] || '';
  document.getElementById('modalImageCount').textContent = `${index + 1} / ${images.length}`;
}

function addToCartFromModal() {
  if (!window.modalProduct) return;
  if (typeof addToCart === 'function') {
    addToCart(window.modalProduct.name, window.modalProduct.price);
  }
  closeProductModal();
}

function renderProductSpecs(product) {
  const specsList = document.querySelector('.modal-specs');
  if (!specsList) return;
  let specs = {};
  try {
    if (product && product.specs) {
      if (typeof product.specs === 'string') {
        specs = JSON.parse(product.specs || '{}') || {};
      } else if (typeof product.specs === 'object') {
        specs = product.specs;
      }
    }
  } catch (e) {
    console.warn('Could not parse product.specs', e);
    specs = {};
  }

  specsList.innerHTML = '';
  const addRow = (label, value) => {
    const li = document.createElement('li');
    const strong = document.createElement('strong'); strong.textContent = label;
    const span = document.createElement('span'); span.textContent = value || '-';
    li.appendChild(strong);
    li.appendChild(span);
    specsList.appendChild(li);
  };

  addRow('Price', `₱${Number(product.price || 0).toLocaleString('en-PH')}`);
  addRow('Rating', `${product.rating || 5}★`);
  const badgeText = normalizeBadge(product.badge);
  if (badgeText) addRow('Badge', badgeText);

  const keys = Object.keys(specs || {});
  if (!keys.length) {
    const note = document.createElement('li');
    note.textContent = 'No additional specifications.';
    note.style.opacity = '.8';
    specsList.appendChild(note);
    return;
  }

  for (const [key, value] of Object.entries(specs)) {
    if (value === null || value === undefined || String(value).trim() === '') continue;
    const label = key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    addRow(label, Array.isArray(value) ? value.join(', ') : String(value));
  }
}

function closeProductModal(event) {
  if (event && event.target !== event.currentTarget) return;
  const modal = document.getElementById('productModal');
  if (!modal) return;
  modal.classList.remove('open');
  modal.setAttribute('aria-hidden', 'true');
  modal.style.display = 'none';
  // Reset modal state so it can be reopened cleanly
  try {
    window.modalProduct = null;
    modal.dataset.images = JSON.stringify([]);
    modal.dataset.imageIndex = 0;
    const imgEl = document.getElementById('modalProductImage'); if (imgEl) { imgEl.src = ''; imgEl.alt = ''; }
    const specsList = document.querySelector('.modal-specs'); if (specsList) specsList.innerHTML = '';
  } catch (e) { /* ignore */ }
}

// ── Toast Notifications ────────────────────────────────────
function showToast(message, type = 'default') {
  let toast = document.getElementById('toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = 'toast';
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.className   = 'toast visible' + (type === 'error' ? ' toast-error' : '');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => toast.classList.remove('visible'), 3000);
}

// ── Mobile Menu ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();

  // Mobile menu toggle (backup if navbar.php inline script isn't loaded)
  const toggle = document.getElementById('menuToggle');
  const menu   = document.getElementById('mobileMenu');
  if (toggle && menu) {
    toggle.addEventListener('click', () => menu.classList.toggle('open'));
  }
});

// Go-to-top button behavior
(function(){
  const btn = document.getElementById('goTop');
  if (!btn) return;
  const showAfter = 200; // px
  function onScroll(){
    if (window.scrollY > showAfter) btn.classList.add('visible'); else btn.classList.remove('visible');
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  // Init state
  onScroll();
})();
