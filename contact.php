<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'Contact Us';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main style="padding:80px 0;background:var(--muted-bg);min-height:80vh;">
  <div class="container">
    <div class="section-title">
      <h2>Get in Touch</h2>
      <p>We're here to help — Monday to Saturday</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-top:40px;align-items:start;">

      <!-- Contact Info -->
      <div>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;margin-bottom:20px;">
          <h3 style="margin-bottom:20px;">📍 Store Info</h3>
          <div style="display:grid;gap:16px;">
            <div style="display:flex;gap:14px;align-items:center;justify-content:center;">
              <span style="font-size:22px;">📍</span>
              <div style="text-align:center;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Location</div>
                <div style="color:var(--muted);font-size:14px;" id="storeAddress">Loading…</div>
              </div>
            </div>
            <div style="display:flex;gap:14px;align-items:center;justify-content:center;">
              <span style="font-size:22px;">📞</span>
              <div style="text-align:center;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Phone / Messenger</div>
                <div style="color:var(--muted);font-size:14px;" id="storePhone">Loading…</div>
              </div>
            </div>
            <div style="display:flex;gap:14px;align-items:center;justify-content:center;">
              <span style="font-size:22px;">🕐</span>
              <div style="text-align:center;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Opening Hours</div>
                <div style="color:var(--muted);font-size:14px;" id="storeHours">Loading…</div>
              </div>
            </div>
            <div style="display:flex;gap:14px;align-items:center;justify-content:center;">
              <span class="fb-icon" style="width:28px;height:28px;font-size:16px;">f</span>
              <div style="text-align:center;">
                <div style="font-weight:700;font-size:14px;margin-bottom:4px;">Facebook Page</div>
                <a id="storeFacebook" href="#" target="_blank" rel="noopener" style="color:var(--primary);font-size:14px;font-weight:600;">Loading…</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Facebook Contact Section -->
      <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;">
        <h3 style="margin-bottom:20px;">💬 Message Us</h3>
        <div style="text-align:center;">
          <p style="color:var(--muted);margin-bottom:24px;">For faster response, message us directly on our Facebook page.</p>
          <a id="facebookContactBtn" href="https://m.me/hernandezcomputertech" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:12px;padding:16px 32px;font-size:16px;">
            <span class="fb-icon" style="width:28px;height:28px;font-size:16px;line-height:28px;">f</span>
            <span>Message Us</span>
          </a>
          <p style="color:var(--muted);font-size:12px;margin-top:24px;">
            You can also use our other contact methods below.<br/>
            The Messenger link opens in a new tab and works on mobile apps too.
          </p>
        </div>
      </div>
    </div>

  <div style="margin-top:40px;">
    <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;">
      <h3 style="margin-bottom:20px;">📍 Our Store</h3>
      <div id="storeMap" style="width:100%;height:420px;border-radius:12px;overflow:hidden;background:var(--bg);display:flex;align-items:center;justify-content:center;color:var(--muted);">
        Loading map…
</main>

<script>
// Load store contact info from the homepage API
async function loadContactInfo() {
  try {
    const res  = await fetch(window.APP_URL + '/api/products.php?action=get_homepage');
    if (!res.ok) throw new Error(`Homepage API returned ${res.status}`);
    const data = await res.json();
    if (!data.ok || !data.homepage) return;
    const hp = data.homepage;
    document.getElementById('storeAddress').textContent  = hp.address  || 'Not available';
    document.getElementById('storePhone').textContent    = hp.contact  || 'Not available';
    document.getElementById('storeHours').textContent    = hp.hours    || 'Monday–Saturday';
    const fb = document.getElementById('storeFacebook');
    fb.textContent = 'hernandezcomputertech';
    fb.href        = 'https://www.facebook.com/hernandezcomputertech/';
    
    // Also keep the Messenger button pointing to the official direct message link
    const fbBtn = document.getElementById('facebookContactBtn');
    fbBtn.href = 'https://m.me/hernandezcomputertech';

    // Render store map
    try {
      const mapContainer = document.getElementById('storeMap');
      if (mapContainer) {
        const lat = hp.map_lat || hp.mapLat || null;
        const lng = hp.map_lng || hp.mapLng || null;
        if (lat && lng) {
          const src = `https://www.google.com/maps?q=${encodeURIComponent(lat)},${encodeURIComponent(lng)}&z=15&output=embed`;
          mapContainer.innerHTML = `<iframe src="${src}" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
        } else if (hp.address) {
          const src = `https://www.google.com/maps?q=${encodeURIComponent(hp.address)}&z=15&output=embed`;
          mapContainer.innerHTML = `<iframe src="${src}" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
        }
      }
    } catch(e) { console.warn('Map render failed', e); }
  } catch(e) {}
}

loadContactInfo();
</script>

<?php include 'includes/footer.php'; ?>
