<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

$isAdmin   = true;
$pageTitle = 'Manage Users';
include '../includes/header.php';
?>
<div class="admin-layout">
  <?php include '../includes/sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-topbar"><h1>👥 Users</h1></div>
    <div id="usersTable">Loading…</div>
  </main>
</div>
<script>
async function loadUsers() {
  const res  = await fetch('../api/users.php?action=get_users');
  const data = await res.json();
  const users = data.users || [];
  if (!users.length) {
    document.getElementById('usersTable').innerHTML = '<p class="empty-msg">No registered users yet.</p>';
    return;
  }
  document.getElementById('usersTable').innerHTML = `
    <table class="data-table">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>${users.map(u=>`
        <tr>
          <td>#${u.id}</td>
          <td>${u.first_name||''} ${u.last_name||''}</td>
          <td>${u.email}</td>
          <td>${u.phone||'—'}</td>
          <td>${u.joined_at ? new Date(u.joined_at).toLocaleDateString('en-PH') : '—'}</td>
          <td><button type="button" class="btn-sm btn-danger delete-user" data-id="${u.id}" data-email="${String(u.email || '').replace(/"/g, '&quot;')}">🗑️ Delete</button></td>
        </tr>`).join('')}
      </tbody>
    </table>`;

  document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      deleteUser(btn.dataset.id, btn.dataset.email || 'this user');
    });
  });
}
async function deleteUser(id,email) {
  window.confirmationAllowed = true;
  showConfirmation(
    'Delete User?',
    `Are you sure you want to delete "${email}"? This also deletes their orders and cannot be undone.`,
    '🗑️',
    async () => {
      const f=new FormData(); f.append('id',id);
      const res  = await fetch('../api/users.php?action=delete_user',{method:'POST',body:f});
      const data = await res.json();
      if (data.ok) loadUsers(); else alert(data.msg||'Error.');
    }
  );
}
loadUsers();
</script>
<?php include '../includes/footer.php'; ?>
