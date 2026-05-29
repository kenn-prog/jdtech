<?php
// ============================================================
//  Backend: Delete Staff Member
//  - Delete staff record
//  - Delete associated image file
//  - Return JSON response
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?? [];
$id = (int) ($data['id'] ?? 0);

if (!$id) {
    jsonResponse(['ok' => false, 'msg' => 'Invalid staff ID'], 400);
    exit;
}

// Get staff record
$staff = fetchOne("SELECT id, image FROM staff WHERE id = {$id}");

if (!$staff) {
    jsonResponse(['ok' => false, 'msg' => 'Staff member not found'], 404);
    exit;
}

// Delete image file if it exists
if ($staff['image'] && file_exists('../uploads/staff/' . $staff['image'])) {
    unlink('../uploads/staff/' . $staff['image']);
}

// Delete database record
if (!runQuery("DELETE FROM staff WHERE id = {$id}")) {
    jsonResponse(['ok' => false, 'msg' => 'Failed to delete staff member'], 500);
    exit;
}

jsonResponse(['ok' => true, 'msg' => 'Staff member deleted']);
