<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

// Handle settings save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_homepage'])) {
    $errors = [];
    $heroTitle     = trim($_POST['hero_title']     ?? '');
    $heroText      = trim($_POST['hero_text']      ?? '');
    $heroTag       = trim($_POST['hero_tag']        ?? '');
    $aboutHeadline = trim($_POST['about_headline'] ?? '');
    $aboutText     = trim($_POST['about_text']     ?? '');
    $customers     = trim($_POST['customers']      ?? '');
    $owner         = trim($_POST['owner']          ?? '');
    $facebook      = trim($_POST['facebook_page']  ?? '');
    $contact       = trim($_POST['contact_number'] ?? '');
    $location      = trim($_POST['location']       ?? '');
    $mapLat        = trim($_POST['map_lat']        ?? '');
    $mapLng        = trim($_POST['map_lng']        ?? '');
    $logoIcon      = trim($_POST['logo_icon']      ?? '');
    $logoImage     = $_FILES['logo_image']        ?? null;
    $ownerImage    = $_FILES['owner_image']       ?? null;
    $staffNames    = $_POST['staff_name']         ?? [];
    $staffRoles    = $_POST['staff_role']         ?? [];
    $staffPhotos   = $_FILES['staff_photo']       ?? null;
    $staffPhotoExisting = $_POST['staff_photo_existing'] ?? [];
    $hours         = trim($_POST['opening_hours']  ?? '');
    $footerText    = trim($_POST['footer_text']    ?? '');

    if (!$heroTitle) $errors[] = 'Hero title is required.';

    if (empty($errors)) {
        // Handle hero image upload
        $heroImageSql = '';
        if (!empty($_FILES['hero_image']['name'])) {
            $uploaded = uploadFile($_FILES['hero_image'], 'products', $errors);
            if ($uploaded) $heroImageSql = ", hero_image = '" . escape($uploaded) . "'";
        }

        // Handle logo image upload
        $logoImageSql = '';
        if (!empty($logoImage['name'])) {
            $uploadedLogo = uploadFile($logoImage, 'logo', $errors);
            if ($uploadedLogo) {
                $logoImageSql = ", logo_image = '" . escape($uploadedLogo) . "'";
            }
        }

        // Handle owner image upload
        $ownerImageSql = '';
        if (!empty($ownerImage['name'])) {
            $uploadedOwner = uploadFile($ownerImage, 'profile', $errors);
            if ($uploadedOwner) {
                $ownerImageSql = ", owner_image = '" . escape($uploadedOwner) . "'";
            }
        }

        // Handle staff image uploads and build staff lines
        $staffLines = [];
        foreach ($staffNames as $index => $name) {
            $name = trim($name);
            $role = trim($staffRoles[$index] ?? '');
            if ($name === '') {
                continue;
            }

            $photoPath = trim($staffPhotoExisting[$index] ?? '');
            if (!empty($staffPhotos['name'][$index])) {
                $file = [
                    'name' => $staffPhotos['name'][$index],
                    'type' => $staffPhotos['type'][$index],
                    'tmp_name' => $staffPhotos['tmp_name'][$index],
                    'error' => $staffPhotos['error'][$index],
                    'size' => $staffPhotos['size'][$index],
                ];
                $uploadedStaffPhoto = uploadFile($file, 'profile', $errors);
                if ($uploadedStaffPhoto) {
                    $photoPath = $uploadedStaffPhoto;
                }
            }

            $staffLines[] = escape($name) . '|' . escape($role) . '|' . escape($photoPath);
        }
        $staffs = implode("\n", $staffLines);


        // Ensure homepage metadata columns exist
        if (!columnExists('homepage', 'map_lat')) runQuery("ALTER TABLE homepage ADD COLUMN map_lat DOUBLE DEFAULT NULL");
        if (!columnExists('homepage', 'map_lng')) runQuery("ALTER TABLE homepage ADD COLUMN map_lng DOUBLE DEFAULT NULL");
        if (!columnExists('homepage', 'logo_icon')) runQuery("ALTER TABLE homepage ADD COLUMN logo_icon VARCHAR(10) DEFAULT '⚡'");
        if (!columnExists('homepage', 'logo_image')) runQuery("ALTER TABLE homepage ADD COLUMN logo_image VARCHAR(255) DEFAULT NULL");
        if (!columnExists('homepage', 'owner_image')) runQuery("ALTER TABLE homepage ADD COLUMN owner_image VARCHAR(255) DEFAULT NULL");
        if (!columnExists('homepage', 'owner')) runQuery("ALTER TABLE homepage ADD COLUMN owner VARCHAR(255) DEFAULT NULL");
        if (!columnExists('homepage', 'staffs')) runQuery("ALTER TABLE homepage ADD COLUMN staffs TEXT DEFAULT NULL");
        runQuery("UPDATE homepage SET
            hero_title     = '" . escape($heroTitle)     . "',
            hero_text      = '" . escape($heroText)      . "',
            hero_tag       = '" . escape($heroTag)        . "',
            about_headline = '" . escape($aboutHeadline) . "',
            about_text     = '" . escape($aboutText)     . "',
            customers      = '" . escape($customers)     . "',
            owner          = '" . escape($owner)         . "',
            staffs         = '" . escape($staffs)        . "',
            facebook_page  = '" . escape($facebook)      . "',
            contact_number = '" . escape($contact)       . "',
            location       = '" . escape($location)      . "',
            opening_hours  = '" . escape($hours)         . "',
            logo_icon      = '" . escape($logoIcon ?: '⚡') . "',
            map_lat        = '" . escape($mapLat)        . "',
            map_lng        = '" . escape($mapLng)        . "',
            footer_text    = '" . escape($footerText)    . "'
            {$heroImageSql}
            {$logoImageSql}
            {$ownerImageSql}
            LIMIT 1");
        $success = 'Homepage settings saved successfully!';
    }
}

$hp        = fetchOne('SELECT * FROM homepage LIMIT 1') ?? [];
$isAdmin   = true;
$pageTitle = 'Admin Settings';
include '../includes/header.php';
?>
<div class="admin-layout">
  <?php include '../includes/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar"><h1>⚙️ Settings</h1></div>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-error"><?= implode('<br>', array_map('h', $errors)) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="settings-form">
      <input type="hidden" name="save_homepage" value="1" />

      <div class="settings-section">
        <h3>🦸 Hero Section</h3>
        <div class="form-group"><label>Hero Tag (e.g. "NEW ARRIVALS 2026")</label>
          <input type="text" name="hero_tag" value="<?= h($hp['hero_tag'] ?? '') ?>" /></div>
        <div class="form-group"><label>Navigation Icon</label>
          <input type="text" name="logo_icon" value="<?= h($hp['logo_icon'] ?? '⚡') ?>" maxlength="4" placeholder="⚡" /></div>
        <div class="form-group"><label>Logo Image</label>
          <input type="file" name="logo_image" accept="image/*" />
          <?php if (!empty($hp['logo_image'])): ?>
            <small>Current: <a href="../<?= h($hp['logo_image']) ?>" target="_blank">view image</a></small>
          <?php endif; ?>
        </div>
        <div class="form-group"><label>Hero Title *</label>
          <input type="text" name="hero_title" value="<?= h($hp['hero_title'] ?? 'Welcome to JDTech') ?>" required /></div>
        <div class="form-group"><label>Hero Description</label>
          <textarea name="hero_text" rows="3"><?= h($hp['hero_text'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Hero Image</label>
          <input type="file" name="hero_image" accept="image/*" />
          <?php if (!empty($hp['hero_image'])): ?>
            <small>Current: <a href="../<?= h($hp['hero_image']) ?>" target="_blank">view image</a></small>
          <?php endif; ?>
        </div>
      </div>

      <div class="settings-section">
        <h3>📖 About Section</h3>
        <div class="form-group"><label>Section Headline</label>
          <input type="text" name="about_headline" value="<?= h($hp['about_headline'] ?? 'Why Choose JDTech?') ?>" /></div>
        <div class="form-group"><label>Section Subtext</label>
          <input type="text" name="about_text" value="<?= h($hp['about_text'] ?? '') ?>" /></div>
        <div class="form-group"><label>Owner Name</label>
          <input type="text" name="owner" value="<?= h($hp['owner'] ?? '') ?>" placeholder="e.g. Jeffrey Ricaro Delos Santos" /></div>
        <div class="form-group"><label>Owner Photo</label>
          <input type="file" name="owner_image" accept="image/*" />
          <?php if (!empty($hp['owner_image'])): ?>
            <small>Current: <a href="../<?= h($hp['owner_image']) ?>" target="_blank">view image</a></small>
          <?php endif; ?>
        </div>
        <div class="form-group"><label>Staff Members</label>
          <div id="staffList">
            <?php
            $staffRows = [];
            if (!empty($hp['staffs'])) {
                foreach (preg_split('/\r\n|\r|\n/', $hp['staffs']) as $line) {
                    $line = trim($line);
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    $staffRows[] = [
                        'name' => $parts[0] ?? '',
                        'role' => $parts[1] ?? '',
                        'photo' => $parts[2] ?? '',
                    ];
                }
            }
            if (empty($staffRows)) {
                $staffRows[] = ['name' => '', 'role' => '', 'photo' => ''];
            }
            foreach ($staffRows as $staff): ?>
              <div class="staff-row" style="margin-bottom:18px;border:1px solid var(--border);border-radius:16px;padding:18px;">
                <div class="form-row" style="display:flex;flex-wrap:wrap;gap:12px;">
                  <input type="text" name="staff_name[]" value="<?= h($staff['name']) ?>" placeholder="Name" style="flex:1 1 220px;" />
                  <input type="text" name="staff_role[]" value="<?= h($staff['role']) ?>" placeholder="Role (optional)" style="flex:1 1 220px;" />
                </div>
                <div class="form-row" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:12px;">
                  <input type="file" name="staff_photo[]" accept="image/*" style="flex:1 1 220px;" />
                  <input type="hidden" name="staff_photo_existing[]" value="<?= h($staff['photo']) ?>" />
                  <?php if (!empty($staff['photo'])): ?>
                    <small style="display:block;flex:1 1 100%;">Current: <a href="../<?= h($staff['photo']) ?>" target="_blank">view image</a></small>
                  <?php endif; ?>
                  <button type="button" class="btn btn-secondary remove-staff-row">Remove</button>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button type="button" class="btn btn-primary" id="addStaffRow">Add staff member</button>
        </div>
        <div class="form-group"><label>Happy Customers Count</label>
          <input type="text" name="customers" value="<?= h($hp['customers'] ?? '') ?>" placeholder="e.g. 500+" /></div>
      </div>

      <div class="settings-section">
        <h3>📞 Contact Info</h3>
        <div class="form-row">
          <div class="form-group"><label>Facebook Page URL</label>
            <input type="url" name="facebook_page" value="<?= h($hp['facebook_page'] ?? '') ?>" /></div>
          <div class="form-group"><label>Contact Number</label>
            <input type="text" name="contact_number" value="<?= h($hp['contact_number'] ?? '') ?>" /></div>
        </div>
        <div class="form-group"><label>Store Location</label>
          <input type="text" name="location" value="<?= h($hp['location'] ?? '') ?>" /></div>
        <div class="form-row">
          <div class="form-group"><label>Map Latitude</label>
            <input type="text" name="map_lat" value="<?= h($hp['map_lat'] ?? '') ?>" placeholder="e.g. 14.5547" /></div>
          <div class="form-group"><label>Map Longitude</label>
            <input type="text" name="map_lng" value="<?= h($hp['map_lng'] ?? '') ?>" placeholder="e.g. 121.0244" /></div>
        </div>
        <div class="form-group"><label>Opening Hours</label>
          <input type="text" name="opening_hours" value="<?= h($hp['opening_hours'] ?? '') ?>" /></div>
      </div>

      <div class="settings-section">
        <h3>🦶 Footer</h3>
        <div class="form-group"><label>Footer Description</label>
          <textarea name="footer_text" rows="3"><?= h($hp['footer_text'] ?? '') ?></textarea></div>
      </div>

      <button type="submit" class="btn btn-primary">💾 Save Settings</button>
    </form>
    <script>
      const staffList = document.getElementById('staffList');
      const addStaffRowButton = document.getElementById('addStaffRow');

      function createStaffRow(name = '', role = '', photo = '') {
        const row = document.createElement('div');
        row.className = 'staff-row';
        row.style = 'margin-bottom:18px;border:1px solid var(--border);border-radius:16px;padding:18px;';
        row.innerHTML = `
          <div class="form-row" style="display:flex;flex-wrap:wrap;gap:12px;">
            <input type="text" name="staff_name[]" value="${name}" placeholder="Name" style="flex:1 1 220px;" />
            <input type="text" name="staff_role[]" value="${role}" placeholder="Role (optional)" style="flex:1 1 220px;" />
          </div>
          <div class="form-row" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-top:12px;">
            <input type="file" name="staff_photo[]" accept="image/*" style="flex:1 1 220px;" />
            <input type="hidden" name="staff_photo_existing[]" value="${photo}" />
            ${photo ? `<small style="display:block;flex:1 1 100%;">Current: <a href="../${photo}" target="_blank">view image</a></small>` : ''}
            <button type="button" class="btn btn-secondary remove-staff-row">Remove</button>
          </div>
        `;
        row.querySelector('.remove-staff-row').addEventListener('click', () => row.remove());
        return row;
      }

      addStaffRowButton.addEventListener('click', () => {
        staffList.appendChild(createStaffRow());
      });

      document.querySelectorAll('.remove-staff-row').forEach(button => {
        button.addEventListener('click', event => {
          event.currentTarget.closest('.staff-row')?.remove();
        });
      });
    </script>
  </main>
</div>
<?php echo '</div></body></html>'; ?>
