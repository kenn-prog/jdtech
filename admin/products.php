<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$isAdmin   = true;
$pageTitle = 'Manage Products';
include '../includes/header.php';
?>

<div class="admin-layout">
  <?php include '../includes/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar">
      <h1>📦 Products</h1>
      <button class="btn btn-primary" onclick="showAddModal()">➕ Add Product</button>
    </div>

    <div id="productsTable">Loading…</div>

    <!-- Add/Edit Product Modal -->
    <div class="modal-overlay" id="productModal" style="display:none">
      <div class="modal">
        <div class="modal-header">
          <h3 id="modalTitle">Add Product</h3>
          <button onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="productId" value="0" />
          <div class="form-group"><label>Name *</label><input type="text" id="pName" placeholder="Product name" /></div>
          <div class="form-row">
            <div class="form-group"><label>Price (₱) *</label><input type="number" id="pPrice" min="0" step="0.01" /></div>
            <div class="form-group"><label>Stock</label><input type="number" id="pStock" value="10" min="0" /></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>Rating (1–5)</label><input type="number" id="pRating" value="5" min="1" max="5" /></div>
            <div class="form-group"><label>Stock Status</label>
              <select id="pStockStatus">
                <option value="On-hand">On-hand</option>
                <option value="Pre-order">Pre-order</option>
              </select>
            </div>
          </div>
          <div class="form-group"><label>Badge (e.g. "NEW", "SALE")</label><input type="text" id="pBadge" /></div>
          <div class="form-group"><label>Image URL (or upload below)</label><input type="text" id="pImageUrl" placeholder="https://..." /></div>
          <div class="form-group"><label>Upload Images</label><input type="file" id="pImages" accept="image/*" multiple /></div>
          <div class="form-group"><label>Description</label><textarea id="pDescription" rows="3"></textarea></div>
          <!-- Specifications removed -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
          <button class="btn btn-primary" onclick="saveProduct()">Save Product</button>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- category add/delete UI removed -->

<script>
let allProducts = [];

async function loadProducts() {
  const res  = await fetch('../api/products.php?action=get_products');
  const data = await res.json();
  allProducts = data.products || [];
  renderTable(allProducts);
}

function renderTable(products) {
  if (!products.length) {
    document.getElementById('productsTable').innerHTML = '<p class="empty-msg">No products yet. <button class="btn btn-primary" onclick="showAddModal()">Add your first product</button></p>';
    return;
  }
  document.getElementById('productsTable').innerHTML = `
    <table class="data-table">
      <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>${products.map(p => `
        <tr>
          <td>#${p.id}</td>
          <td>${p.name}</td>
          <td>₱${Number(p.price).toLocaleString('en-PH')}</td>
          <td>${p.stock}</td>
          <td>${p.stock_status || 'On-hand'}</td>
          <td>
            <button type="button" class="btn-sm btn-outline edit-product" data-id="${p.id}">✏️ Edit</button>
            <button type="button" class="btn-sm btn-danger delete-product" data-id="${p.id}" data-name="${String(p.name || '').replace(/\"/g, '&quot;')}">🗑️ Delete</button>
          </td>
        </tr>`).join('')}
      </tbody>
    </table>`;

  document.querySelectorAll('.edit-product').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      editProduct(btn.dataset.id);
    });
  });
  document.querySelectorAll('.delete-product').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      deleteProduct(btn.dataset.id, btn.dataset.name || 'this product');
    });
  });
}

function showAddModal() {
  document.getElementById('modalTitle').textContent = 'Add Product';
  document.getElementById('productId').value   = 0;
  document.getElementById('pName').value        = '';
  document.getElementById('pPrice').value       = '';
  document.getElementById('pStock').value       = 10;
  document.getElementById('pRating').value      = 5;
  document.getElementById('pBadge').value       = '';
  document.getElementById('pImageUrl').value    = '';
  const imagesInput = document.getElementById('pImages');
  if (imagesInput) imagesInput.value = '';
  document.getElementById('pDescription').value = '';
  // default stock status
  const statusEl = document.getElementById('pStockStatus'); if (statusEl) statusEl.value = 'On-hand';
  // specs removed
  document.getElementById('productModal').style.display = 'flex';
}

function editProduct(id) {
  const p = allProducts.find(x => x.id == id);
  if (!p) return;
  document.getElementById('modalTitle').textContent   = 'Edit Product';
  document.getElementById('productId').value          = p.id;
  document.getElementById('pName').value              = p.name;
  document.getElementById('pPrice').value             = p.price;
  document.getElementById('pStock').value             = p.stock;
  document.getElementById('pRating').value            = p.rating;
  document.getElementById('pBadge').value             = p.badge || '';
  document.getElementById('pImageUrl').value          = p.image_url || '';
  document.getElementById('pDescription').value       = p.description || '';
  // populate stock status if available
  try { if (document.getElementById('pStockStatus')) document.getElementById('pStockStatus').value = p.stock_status || 'On-hand'; } catch(e){}
  // specs removed
  const imagesInput = document.getElementById('pImages');
  if (imagesInput) imagesInput.value = '';
  document.getElementById('productModal').style.display = 'flex';
}

function closeModal() {
  document.getElementById('productModal').style.display = 'none';
}

// Categories and specifications removed from admin UI/backend flow

async function saveProduct() {
  const id   = document.getElementById('productId').value;
  const form = new FormData();
  form.append('id',          id);
  form.append('name',        document.getElementById('pName').value);
  form.append('price',       document.getElementById('pPrice').value);
  form.append('stock',       document.getElementById('pStock').value);
  form.append('rating',      document.getElementById('pRating').value);
  form.append('stock_status', document.getElementById('pStockStatus') ? document.getElementById('pStockStatus').value : 'On-hand');
  form.append('badge',       document.getElementById('pBadge').value);
  form.append('image_url',   document.getElementById('pImageUrl').value);
  form.append('description', document.getElementById('pDescription').value);
  // specifications removed
  const imagesInput = document.getElementById('pImages');
  if (imagesInput && imagesInput.files.length > 0) {
    Array.from(imagesInput.files).forEach((file, index) => {
      form.append('images[]', file);
    });
  }

  const url  = id > 0 ? '../backend/edit-product.php' : '../backend/add-product.php';
  const res  = await fetch(url, { method: 'POST', body: form });
  const data = await res.json();
  if (data.ok) { closeModal(); loadProducts(); }
  else alert(data.msg || 'Error saving product.');
}

async function deleteProduct(id, name) {
  window.confirmationAllowed = true;
  showConfirmation(
    'Delete Product?',
    `Are you sure you want to delete "${name}"? This cannot be undone.`,
    '🗑️',
    async () => {
      const form = new FormData();
      form.append('id', id);
      const res  = await fetch('../backend/delete-product.php', { method: 'POST', body: form });
      const data = await res.json();
      if (data.ok) loadProducts();
      else alert(data.msg || 'Error deleting product.');
    }
  );
}

loadProducts();
</script>


<?php include '../includes/footer.php'; ?>
