<?php
// ============================================================
//  JDTech — Homepage
//  PURPOSE: Main landing page. Loads dynamic content from
//           the database via the API (api/products.php).
//
//  HOW IT WORKS:
//  1. PHP includes header, navbar, and footer partials
//  2. The HTML content is served as a static structure
//  3. JavaScript (in <script>) calls api/products.php to
//     fetch products and homepage data dynamically
//  4. This is called "Ajax" — updating the page without reload
// ============================================================

require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'Home — Premium Electronics & Gadgets';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Hero Section -->
<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="tag" id="heroTag">NEW ARRIVALS 2026</span>
      <h1 id="heroTitle">Welcome to <br /><span class="accent">JDTech</span></h1>
      <p id="heroText">Find the latest devices, accessories, and trusted customer support in one premium destination.</p>
      <div class="hero-ctas">
        <a href="#products" class="btn btn-primary">Shop Now</a>
        <a href="about.php" class="btn btn-outline">Learn More</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="assets/images/hero-devices.jpg" alt="Latest gadgets" id="heroImage" width="1200" height="1200" />
    </div>
  </div>
</section>

<!-- Stats Bar -->
<section class="stats">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-num" id="statCustomers">...</div>
        <div class="stat-label">Happy Customers</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" id="statProducts">...</div>
        <div class="stat-label">Products Available</div>
      </div>
      <div class="stat-item">
        <div class="stat-num" id="shopRatingNum">...</div>
        <div class="stat-label">Shop Rating</div>
      </div>
    </div>
  </div>
</section>

<!-- Features / About Section -->
<section class="features" id="about">
  <div class="container">
    <div class="section-title">
      <h2 id="aboutHeadline">Why Choose JDTech?</h2>
      <p id="aboutText">Everything you need, all in one place</p>
    </div>
    <div class="feature-grid">
      <div class="feature-card"><div class="feature-icon">📱</div><h3>Latest Devices</h3><p>Smartphones, laptops, tablets & gaming devices with cutting-edge performance.</p></div>
      <div class="feature-card"><div class="feature-icon">🎧</div><h3>Elite Accessories</h3><p>Headphones, keyboards, chargers, mouse & phone cases built for professionals.</p></div>
      <div class="feature-card"><div class="feature-icon">🚚</div><h3>Fast Delivery</h3><p>Same-day dispatch on in-stock items. Free shipping on orders over ₱2,000.</p></div>
      <div class="feature-card"><div class="feature-icon">🛡️</div><h3>Warranty Guarantee</h3><p>All products come with official manufacturer warranty. We handle claims for you.</p></div>
      <div class="feature-card"><div class="feature-icon">💳</div><h3>Flexible Payments</h3><p>GCash, Maya, credit cards, bank transfer, and installment plans available.</p></div>
      <div class="feature-card"><div class="feature-icon">📞</div><h3>Trusted Support</h3><p>Fast response, warranty assistance, and a reliable team Monday–Saturday.</p></div>
    </div>
  </div>
</section>

<!-- Products Section -->
<section class="products" id="products">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Featured Products</h2>
        <p>The best of modern engineering</p>
      </div>
      <div class="filters stock-filters">
        <button class="filter stock-filter active" data-stock="all">All Stock</button>
        <button class="filter stock-filter" data-stock="On-hand">On-hand</button>
        <button class="filter stock-filter" data-stock="Pre-order">Pre-order</button>
      </div>
    </div>
    <div class="product-grid" id="productGrid">
      <div class="loading-products">Loading products...</div>
    </div>
  </div>
</section>

<!-- Testimonials / Customer Feedback -->
<section class="testimonials" id="testimonialsSection" style="display:none;">
  <div class="container">
    <div class="section-title">
      <h2>What Our Customers Say</h2>
      <p>Real feedback from recent orders.</p>
    </div>
    <div class="testimonial-grid" id="testimonialsGrid">
      <!-- Filled by JS -->
    </div>
  </div>
</section>

<!-- Chat Widget -->
<div class="chat">
  <div class="chat-panel" id="chatPanel">
    <div class="chat-header">
      <div class="info"><div class="chat-avatar">💬</div><div><h4>JDTech Support</h4><small>Typically replies within minutes</small></div></div>
      <button class="chat-close" id="chatClose">✕</button>
    </div>
    <div class="chat-body">
      <div class="chat-bubble">Hi there! 👋 Welcome to JDTech. How can we help you today?</div>
      <a class="chat-link" href="https://m.me/jdtechstore" target="_blank" rel="noopener">
        <div class="fb">f</div>
        <div class="meta"><p>Chat on Facebook</p><small>Connect via Messenger</small></div>
        <span>➤</span>
      </a>
    </div>
    <div class="chat-foot">Powered by JDTech Customer Support</div>
  </div>
  <button class="chat-toggle" id="chatToggle" aria-label="Open chat">💬</button>
</div>

<div id="toast" class="toast" role="status" aria-live="polite"></div>

<script>
// Load homepage data and products from our PHP API
async function initHomepage() {
  try {
    // Fetch homepage content
    const hpRes  = await fetch('api/products.php?action=get_homepage');
    const hpData = await hpRes.json();
    // Normalize homepage data object for use below
    const hp = (hpData.ok && hpData.homepage) ? hpData.homepage : {};
    if (Object.keys(hp).length) {
      document.getElementById('heroTag').textContent   = hp.heroTag   || 'NEW ARRIVALS 2026';
      document.getElementById('heroTitle').innerHTML   = (hp.heroTitle || 'Welcome to JDTech').replace('JDTech', '<span class="accent">JDTech</span>');
      document.getElementById('heroText').textContent  = hp.heroText  || '';
      document.getElementById('statCustomers').textContent = hp.customers || '0';
      document.getElementById('aboutHeadline').textContent = hp.aboutHeadline || 'Why Choose JDTech?';
      document.getElementById('aboutText').textContent     = hp.aboutText    || '';
      const _ownerNameEl = document.getElementById('ownerName');
      if (_ownerNameEl) _ownerNameEl.textContent = hp.owner || 'JDTech Owner';
      const _ownerInfoEl = document.getElementById('ownerInfo');
      if (_ownerInfoEl) _ownerInfoEl.textContent = hp.owner ? 'This owner leads JDTech with passion, quality service, and product expertise.' : _ownerInfoEl.textContent;
      const shopRatingEl = document.getElementById('shopRatingNum');
      if (shopRatingEl) shopRatingEl.textContent = (hp.shopRating ? (hp.shopRating + '/5 ⭐') : '0/5');

      // If admin uploaded a hero image, use it
      try {
        if (hp.heroImage) {
          const img = document.getElementById('heroImage'); if (img) img.src = hp.heroImage;
        }
      } catch(e) { console.warn('Hero image update failed', e); }

      // Map will be rendered on contact page instead
      try {
        // Skip map rendering on homepage
      } catch(e) { console.warn('Map render skipped', e); }
    }

    // Render testimonials if available
    try {
      const tSection = document.getElementById('testimonialsSection');
      const tGrid = document.getElementById('testimonialsGrid');
      if (tSection && tGrid && Array.isArray(hp.feedbacks) && hp.feedbacks.length) {
        tGrid.innerHTML = hp.feedbacks.map(f => `
          <div class="testimonial-card">
            <div class="testimonial-stars">${'★'.repeat(f.rating || 5)}</div>
            <div class="testimonial-text">${(f.text || '').replace(/</g,'&lt;')}</div>
            <div class="testimonial-author">
              <div class="author-avatar">${(f.author || 'C').charAt(0)}</div>
              <div>
                <div class="author-name">${f.author || 'Customer'}</div>
                <div class="author-role">${f.date ? new Date(f.date).toLocaleDateString() : ''}</div>
              </div>
            </div>
          </div>
        `).join('');
        tSection.style.display = '';
      } else if (tSection) {
        tSection.style.display = 'none';
      }
    } catch(e) { console.warn('Testimonials render failed', e); }

    // Fetch products
    const prRes  = await fetch('api/products.php?action=get_products');
    const prData = await prRes.json();
    const products = prData.ok ? prData.products : [];
    window.homepageProducts = products;
    document.getElementById('statProducts').textContent = products.length || '0';
    let activeStock = 'all';
    renderProducts(products, activeStock);

    // Bind stock buttons
    document.querySelectorAll('.stock-filter').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.stock-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeStock = btn.dataset.stock || 'all';
        renderProducts(products, activeStock);
      });
    });

    // Handle clicks on product cards
    document.getElementById('productGrid').addEventListener('click', function(event) {
      const card = event.target.closest('.product');
      if (!card) return;
      const id = card.dataset.productId;
      if (!id) return;
      const product = products.find(p => String(p.id) === String(id));
      if (!product) return console.warn('Product not found on homepage', id);
      window.showProductModal ? window.showProductModal(product) : handleProductClick(event, id);
    });
  } catch(e) {
    console.error('Init error:', e);
  }
}

function normalizeBadge(badge) {
  if (!badge) return '';
  const raw = String(badge).trim().toLowerCase();
  if (raw === 'new') return 'Pre Order';
  if (raw === 'hot') return 'Onhand';
  if (raw === 'sale') return '';
  return badge;
}

function renderProducts(products, stock) {
  const grid = document.getElementById('productGrid');
  const filtered = (stock === 'all' || !stock) ? products : products.filter(p => ((p.stock_status || 'On-hand') === stock));
  if (!filtered.length) {
    grid.innerHTML = '<div class="empty-products"><div style="font-size:48px">📦</div><p>No products yet.</p></div>';
    return;
  }
  grid.innerHTML = filtered.map(p => {
    const badgeText = normalizeBadge(p.badge);
    return `
    <article class="product" data-product-id="${p.id}">
      <div class="product-media">
        ${badgeText ? `<div class="badge">${badgeText}</div>` : ''}
        <div class="product-image">${p.image_url ? `<img src="${p.image_url}" alt="${p.name}">` : '📦'}</div>
      </div>
      <div class="product-info">
        <div class="product-row"><h3>${p.name}</h3><span class="price">₱${Number(p.price).toLocaleString('en-PH')}</span></div>
        <div class="stars">${'★'.repeat(p.rating || 5)}</div>
        <button type="button" class="add-cart btn btn-primary" onclick="event.stopPropagation(); addToCart('${p.name.replace(/'/g,"\\'")}', ${p.price})">Add to Cart</button>
      </div>
    </article>`;
  }).join('');
}

function handleProductClick(event, id) {
  if (event && typeof event.preventDefault === 'function') {
    event.preventDefault();
  }
  console.log('homepage handleProductClick', id);
  const products = window.homepageProducts || [];
  const product  = products.find(p => p.id === id || String(p.id) === String(id));
  if (!product) return console.warn('Product not found on homepage', id);
  if (typeof window.showProductModal === 'function') {
    window.showProductModal(product);
    return;
  }
  // Fallback similar to products.php
  try {
    const modal = document.getElementById('productModal');
    if (!modal) throw new Error('Modal element missing');
    const images = (product.image_url || '').split(/[,|;]+/).map(i=>i.trim()).filter(Boolean);
    modal.dataset.images = JSON.stringify(images);
    modal.dataset.imageIndex = 0;
    const img = document.getElementById('modalProductImage'); if (img) { img.src = images[0] || product.image_url || ''; img.alt = product.name || 'Product image'; }
    const nameEl = document.getElementById('modalProductName'); if (nameEl) nameEl.textContent = product.name || 'Product';
    const priceEl = document.getElementById('modalProductPrice'); if (priceEl) priceEl.textContent = `₱${Number(product.price||0).toLocaleString('en-PH')}`;
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
    const count = document.getElementById('modalImageCount'); if (count) count.textContent = `${(images.length?1:0)}/${images.length||1}`;
    modal.classList.add('open'); modal.setAttribute('aria-hidden','false');
  } catch(e){ console.error('Modal fallback error', e); }
}

function addToCart(name, price) {
  let cart = JSON.parse(sessionStorage.getItem('jdcart') || '[]');
  const idx = cart.findIndex(i => i.name === name);
  if (idx >= 0) cart[idx].qty++; else cart.push({name, price, qty: 1});
  sessionStorage.setItem('jdcart', JSON.stringify(cart));
  const total = cart.reduce((s, i) => s + i.qty, 0);
  document.querySelectorAll('.cart-count').forEach(el => { el.textContent = total; el.classList.toggle('visible', total > 0); });
  showToast(`${name} added to cart 🎉`);
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.classList.add('visible');
  setTimeout(() => t.classList.remove('visible'), 3000);
}

// Chat widget
document.getElementById('chatToggle').addEventListener('click', () => document.getElementById('chatPanel').classList.toggle('open'));
document.getElementById('chatClose').addEventListener('click',  () => document.getElementById('chatPanel').classList.remove('open'));

// Update cart count on load
const cart = JSON.parse(sessionStorage.getItem('jdcart') || '[]');
const total = cart.reduce((s, i) => s + i.qty, 0);
document.querySelectorAll('.cart-count').forEach(el => { el.textContent = total; el.classList.toggle('visible', total > 0); });

initHomepage();
</script>

<?php include 'includes/footer.php'; ?>
