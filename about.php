<?php
require_once 'includes/config.php';
require_once 'includes/session.php';
require_once 'includes/functions.php';

$pageTitle = 'About Us';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<main>
  <!-- Hero Banner -->
  <section style="padding:80px 0 60px;background:linear-gradient(135deg,rgba(248,228,228,0.95) 0%,rgba(255,255,255,1) 100%);color:var(--fg);text-align:center;">
    <div class="container">
      <span style="font-size:13px;font-weight:700;letter-spacing:.12em;color:var(--primary);text-transform:uppercase;">Our Story</span>
      <h1 style="font-size:clamp(32px,5vw,56px);font-weight:800;margin:16px 0 20px;" id="aboutHeadline">Why Choose JDTech?</h1>
      <p style="max-width:560px;margin:0 auto;opacity:.9;font-size:16px;" id="aboutText">Everything you need, all in one place.</p>
      <div style="margin-top:32px;display:flex;justify-content:center;">
        <a href="https://m.me/hernandezcomputertech" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="gap:12px;display:inline-flex;align-items:center;padding:16px 32px;">
          <span class="fb-icon" style="width:28px;height:28px;font-size:16px;line-height:28px;">f</span>
          <span>Message Us on Facebook</span>
        </a>
      </div>

      <div style="margin-top:48px;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;justify-items:center;">
        <?php
        $values = [
          ['📱', 'Latest Devices', 'Smartphones, laptops, tablets & gaming devices with cutting-edge performance.'],
          ['🎧', 'Elite Accessories', 'Headphones, keyboards, chargers, mouse & phone cases built for professionals.'],
          ['🚚', 'Fast Delivery', 'Same-day dispatch on in-stock items. Free shipping on orders over ₱2,000.'],
          ['🛡️', 'Warranty Guarantee', 'All products come with official manufacturer warranty. We handle claims for you.'],
          ['💳', 'Flexible Payments', 'GCash, Maya, credit cards, bank transfer, and installment plans available.'],
          ['📞', 'Trusted Support', 'Fast response, warranty assistance, and a reliable team Monday–Saturday.'],
        ];
        foreach ($values as $v): ?>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:28px;text-align:center;max-width:280px;">
          <div style="font-size:40px;margin-bottom:16px;"><?= $v[0] ?></div>
          <h3 style="font-size:18px;margin-bottom:10px;"><?= $v[1] ?></h3>
          <p style="color:var(--muted);font-size:14px;line-height:1.6;"><?= $v[2] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Mission Section -->
  <section style="padding:80px 0;background:var(--bg);">
    <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;">
      <div>
        <span style="font-size:13px;font-weight:700;letter-spacing:.1em;color:var(--primary);text-transform:uppercase;">Our Mission</span>
        <h2 style="font-size:36px;font-weight:800;margin:12px 0 20px;">Bringing Technology Closer to Everyone</h2>
        <p style="color:var(--muted);line-height:1.8;margin-bottom:16px;">JDTech was founded with a simple mission: make premium technology accessible, affordable, and supported. We believe everyone deserves quality electronics backed by honest customer service.</p>
        <p style="color:var(--muted);line-height:1.8;">From smartphones to gaming setups, we source only authentic products and provide real warranty support — not just a sales counter.</p>
      </div>
      <div style="background:var(--muted-bg);border-radius:24px;overflow:hidden;aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
        <img src="assets/images/mission-photo.jpg" alt="JDTech mission image" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.style.display='none'" />
      </div>
    </div>
  </section>

  <!-- Owner and Staff Section -->
  <section id="teamSection" style="padding:80px 0;background:var(--bg);display:none;">
    <div class="container">
      <div style="text-align:center;max-width:720px;margin:0 auto 40px;">
        <span style="font-size:13px;font-weight:700;letter-spacing:.1em;color:var(--primary);text-transform:uppercase;">Meet the Team</span>
        <h2 style="font-size:36px;font-weight:800;margin:12px 0 10px;">Owner & Staff Members</h2>
        <p style="color:var(--muted);line-height:1.8;">Our people are the heart of JDTech — from leadership to the hands-on staff serving customers every day.</p>
      </div>
      <div style="display:grid;grid-template-columns:1fr;gap:32px;">
        <div id="ownerCard" style="display:none;background:var(--card);border:1px solid var(--border);border-radius:24px;padding:30px;text-align:center;"></div>
        <div id="staffCards" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;"></div>
      </div>
    </div>
  </section>

</main>

<script>
async function loadAbout() {
  try {
    const res  = await fetch(window.APP_URL + '/api/products.php?action=get_homepage');
    const data = await res.json();
    if (!data.ok || !data.homepage) return;
    const hp = data.homepage;

    if (hp.aboutHeadline) document.getElementById('aboutHeadline').textContent = hp.aboutHeadline;
    if (hp.aboutText)     document.getElementById('aboutText').textContent     = hp.aboutText;

    const teamSection = document.getElementById('teamSection');
    const ownerCard = document.getElementById('ownerCard');
    const staffCards = document.getElementById('staffCards');
    if (hp.owner || Array.isArray(hp.staffMembers) && hp.staffMembers.length) {
      teamSection.style.display = 'block';
      if (hp.owner) {
        ownerCard.style.display = 'block';
        ownerCard.innerHTML = `
          ${hp.ownerImage ? `<a href="${window.APP_URL}/${hp.ownerImage}" target="_blank" rel="noopener"><img src="${window.APP_URL}/${hp.ownerImage}" alt="${hp.owner}" style="width:120px;height:120px;border-radius:50%;object-fit:cover;margin-bottom:18px;"></a>` : ''}
          <div style="font-size:16px;color:var(--primary);font-weight:700;margin-bottom:10px;">Owner</div>
          <div style="font-size:28px;font-weight:800;">${hp.owner}</div>
        `;
      } else {
        ownerCard.style.display = 'none';
      }
      if (Array.isArray(hp.staffMembers) && hp.staffMembers.length) {
        staffCards.innerHTML = hp.staffMembers.map(m => `
          <div style="background:var(--card);border:1px solid var(--border);border-radius:20px;padding:24px;text-align:center;">
            ${m.photo ? `<a href="${window.APP_URL}/${m.photo}" target="_blank" rel="noopener"><img src="${window.APP_URL}/${m.photo}" alt="${m.name}" style="width:120px;height:120px;border-radius:50%;object-fit:cover;margin-bottom:16px;"></a>` : ''}
            <div style="font-size:20px;font-weight:700;">${m.name}</div>
            <div style="color:var(--muted);margin-top:8px;">${m.role || 'Staff'}</div>
          </div>
        `).join('');
      } else {
        staffCards.innerHTML = '';
      }
    }
  } catch(e) { console.error(e); }
}

loadAbout();
</script>

<?php include 'includes/footer.php'; ?>
