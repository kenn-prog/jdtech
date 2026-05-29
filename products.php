<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'Products';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding: 60px 0 80px; background: var(--muted-bg); min-height: 80vh;">
  <div class="container">
    <div class="section-title">
      <h2>All Products</h2>
      <p>Browse our full catalog</p>
    </div>

    <div class="filters" style="margin-bottom:32px; display:flex; gap:8px; flex-wrap:wrap;">
      <button class="filter stock-filter active" data-stock="all">All Stock</button>
      <button class="filter stock-filter" data-stock="On-hand">On-hand</button>
      <button class="filter stock-filter" data-stock="Pre-order">Pre-order</button>
    </div>

    <div class="product-grid" id="productGrid">
      <div class="loading-products">Loading products…</div>
    </div>
  </div>
</main>

<div id="toast" class="toast"></div>

<script>
let allProducts = [];
let activeStock = 'all';

function normalizeBadge(badge) {
  if (!badge) return '';
  const normalized = String(badge).trim().toLowerCase();
  if (normalized === 'new') return 'Pre Order';
  if (normalized === 'hot') return 'Onhand';
  if (normalized === 'sale') return '';
  return badge;
}

function escapeHtml(value) {
  return String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

async function loadProducts() {
  try {
    const res = await fetch(window.APP_URL + '/api/products.php?action=get_products');
    const data = await res.json();
    allProducts = Array.isArray(data.products) ? data.products : [];
    renderProducts('all');
  } catch (error) {
    console.error('Failed to load products', error);
    const grid = document.getElementById('productGrid');
    if (grid) {
      grid.innerHTML = '<div class="empty-products" style="grid-column:1/-1;text-align:center;padding:60px"><div style="font-size:48px">⚠️</div><p style="color:var(--danger)">Unable to load products.</p></div>';
    }
  }
}

function createProductCard(product) {
  const badgeText = normalizeBadge(product.badge);
  const card = document.createElement('article');
  card.className = 'product';
  card.dataset.productId = product.id;

  const media = document.createElement('div');
  media.className = 'product-media';

  if (badgeText) {
    const badge = document.createElement('div');
    badge.className = 'badge';
    badge.textContent = badgeText;
    media.appendChild(badge);
  }

  const imageWrapper = document.createElement('div');
  imageWrapper.className = 'product-image';
  imageWrapper.style.cssText = 'background:var(--muted-bg);border-radius:12px;aspect-ratio:1;display:flex;align-items:center;justify-content:center;overflow:hidden';
  if (product.image_url) {
    const img = document.createElement('img');
    img.src = product.image_url;
    img.alt = product.name || 'Product image';
    img.style.cssText = 'width:100%;height:100%;object-fit:cover';
    imageWrapper.appendChild(img);
  } else {
    const placeholder = document.createElement('span');
    placeholder.style.fontSize = '48px';
    placeholder.textContent = '📦';
    imageWrapper.appendChild(placeholder);
  }

  media.appendChild(imageWrapper);
  card.appendChild(media);

  const info = document.createElement('div');
  info.className = 'product-info';

  const row = document.createElement('div');
  row.className = 'product-row';
  const title = document.createElement('h3');
  title.textContent = product.name || 'Product';
  const price = document.createElement('span');
  price.className = 'price';
  price.textContent = `₱${Number(product.price || 0).toLocaleString('en-PH')}`;
  row.appendChild(title);
  row.appendChild(price);

  const stars = document.createElement('div');
  stars.className = 'stars';
  stars.textContent = '★'.repeat(product.rating || 5);

  const desc = document.createElement('p');
  desc.style.cssText = 'font-size:13px;color:var(--muted);margin:6px 0 12px';
  desc.textContent = product.description ? product.description.substring(0, 80) + '…' : '';

  const statusBadge = document.createElement('div');
  statusBadge.style.cssText = 'display:inline-block;margin-bottom:10px;padding:4px 10px;font-size:12px;border-radius:999px;border:1px solid var(--border);color:var(--muted);background:var(--card);';
  statusBadge.textContent = product.stock_status || 'On-hand';

  const button = document.createElement('button');
  button.type = 'button';
  button.className = 'btn btn-primary btn-add-cart';
  button.style.width = '100%';
  button.dataset.name = product.name || '';
  button.dataset.price = Number(product.price || 0);
  button.textContent = 'Add to Cart';

  info.appendChild(row);
  info.appendChild(stars);
  info.appendChild(desc);
  info.appendChild(statusBadge);
  info.appendChild(button);
  card.appendChild(info);

  return card;
}

function renderProducts(stock = activeStock) {
  activeStock = stock;

  const grid = document.getElementById('productGrid');
  const filtered = allProducts.filter(p => {
    const stockMatch = stock === 'all' || (p.stock_status || 'On-hand') === stock;
    return stockMatch;
  });
  if (!grid) return;
  grid.innerHTML = '';

  if (!filtered.length) {
    const empty = document.createElement('div');
    empty.className = 'empty-products';
    empty.style.cssText = 'grid-column:1/-1;text-align:center;padding:60px';
    empty.innerHTML = '<div style="font-size:48px">📦</div><p style="color:var(--muted)">No products match the selected filters.</p>';
    grid.appendChild(empty);
    return;
  }

  filtered.forEach(product => grid.appendChild(createProductCard(product)));
}

function addToCart(name, price) {
  let cart = JSON.parse(sessionStorage.getItem('jdcart') || '[]');
  const i = cart.findIndex(x => x.name === name);
  if (i >= 0) cart[i].qty++;
  else cart.push({ name, price, qty: 1 });
  sessionStorage.setItem('jdcart', JSON.stringify(cart));
  const count = cart.reduce((s, x) => s + x.qty, 0);
  document.querySelectorAll('.cart-count').forEach(el => { el.textContent = count; el.classList.toggle('visible', count > 0); });
  const t = document.getElementById('toast');
  if (t) {
    t.textContent = `${name} added to cart 🎉`;
    t.classList.add('visible');
    setTimeout(() => t.classList.remove('visible'), 3000);
  }
}

document.querySelectorAll('.cat-filter').forEach(btn => btn.addEventListener('click', () => {
  document.querySelectorAll('.cat-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderProducts(btn.dataset.cat, activeStock);
}));

document.querySelectorAll('.stock-filter').forEach(btn => btn.addEventListener('click', () => {
  document.querySelectorAll('.stock-filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderProducts(activeCategory, btn.dataset.stock);
}));

document.getElementById('productGrid').addEventListener('click', function(event) {
  const addButton = event.target.closest('.btn-add-cart');
  if (addButton) {
    event.stopPropagation();
    const name = addButton.dataset.name || '';
    const price = Number(addButton.dataset.price || 0);
    addToCart(name, price);
    return;
  }

  const card = event.target.closest('.product');
  if (!card) return;
  const id = card.dataset.productId;
  if (!id) return;
  const product = allProducts.find(p => String(p.id) === String(id));
  if (!product) {
    console.warn('Product not found', id);
    return;
  }
  if (typeof window.showProductModal === 'function') {
    window.showProductModal(product);
  } else {
    handleProductClick(event, id);
  }
});

function handleProductClick(event, id) {
  if (event && typeof event.preventDefault === 'function') {
    event.preventDefault();
  }
  const product = allProducts.find(p => p.id === id || String(p.id) === String(id));
  if (!product) return console.warn('Product not found', id);
  if (typeof window.showProductModal === 'function') {
    window.showProductModal(product);
    return;
  }
  try {
    const modal = document.getElementById('productModal');
    if (!modal) throw new Error('Modal element missing');
    const images = (product.image_url || '').split(/[,|;]+/).map(i => i.trim()).filter(Boolean);
    modal.dataset.images = JSON.stringify(images);
    modal.dataset.imageIndex = 0;
    const img = document.getElementById('modalProductImage'); if (img) { img.src = images[0] || product.image_url || ''; img.alt = product.name || 'Product image'; }
    const nameEl = document.getElementById('modalProductName'); if (nameEl) nameEl.textContent = product.name || 'Product';
    const priceEl = document.getElementById('modalProductPrice'); if (priceEl) priceEl.textContent = `₱${Number(product.price || 0).toLocaleString('en-PH')}`;
    const desc = document.getElementById('modalProductDescription'); if (desc) desc.textContent = product.description || '';
    const badge = document.getElementById('modalProductBadge');
    if (badge) {
      const badgeText = normalizeBadge(product.badge);
      if (badgeText) {
        badge.textContent = badgeText;
        badge.style.display = 'inline-flex';
      } else {
        badge.style.display = 'none';
      }
    }
    const count = document.getElementById('modalImageCount'); if (count) count.textContent = `${(images.length ? 1 : 0)}/${images.length || 1}`;
    modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false');
  } catch (e) { console.error('Modal fallback error', e); }
}

loadProducts();
</script>

<?php include 'includes/footer.php'; ?>
