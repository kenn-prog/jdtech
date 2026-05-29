<?php
// ============================================================
//  JDTech — Users API (Admin Only)
//  PURPOSE: Manage users from the admin panel via AJAX.
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    case 'get_users':
        requireAdminAPI();
        $users = fetchAll('SELECT id, email, first_name, last_name, phone, joined_at, avatar FROM users ORDER BY joined_at DESC');
        jsonResponse(['ok' => true, 'users' => $users]);
        break;

    case 'delete_user':
        requireAdminAPI();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'POST required.'], 405);
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) jsonResponse(['ok' => false, 'msg' => 'Invalid user ID.']);
        runQuery("DELETE FROM users WHERE id = {$id} LIMIT 1");
        jsonResponse(['ok' => true]);
        break;

    case 'get_orders_admin':
        requireAdminAPI();
        $orders = fetchAll('SELECT o.*, u.email, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.date DESC');
        foreach ($orders as &$o) {
            $o['items'] = json_decode($o['items'] ?? '[]', true) ?: [];
        }
        jsonResponse(['ok' => true, 'orders' => $orders]);
        break;

    case 'update_order_status':
        requireAdminAPI();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['ok' => false, 'msg' => 'POST required.'], 405);
        $id     = intval($_POST['id']     ?? 0);
        $status = trim($_POST['status']   ?? '');
        $allowed = ['Processing', 'On the Way', 'Delivered', 'Cancelled'];
        if ($id <= 0 || !in_array($status, $allowed, true)) {
            jsonResponse(['ok' => false, 'msg' => 'Invalid data.']);
        }
        runQuery("UPDATE orders SET status = '" . escape($status) . "' WHERE id = {$id} LIMIT 1");
        jsonResponse(['ok' => true]);
        break;

    default:
        jsonResponse(['ok' => false, 'msg' => 'Unknown action.'], 400);
}
