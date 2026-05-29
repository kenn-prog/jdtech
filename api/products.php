<?php
// ============================================================
//  JDTech — Products & Homepage API
//  PURPOSE: A REST-like API endpoint that returns JSON data.
//
//  API FOLDER PURPOSE:
//  The /api/ folder contains PHP files that ONLY return JSON.
//  They are called by JavaScript (fetch/AJAX) to get data
//  without reloading the whole page. This is how modern
//  websites work — the HTML is static, but data is dynamic.
//
//  ENDPOINTS (called via ?action=...):
//  GET  api/products.php?action=get_products  → all products
//  GET  api/products.php?action=get_homepage  → homepage content
//  GET  api/products.php?action=get_session   → current user session
//  GET  api/products.php?action=get_orders    → orders for current user
//  POST api/products.php?action=add_order     → place an order
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'Internal server error.', 'error' => $errstr]);
    exit;
});
set_exception_handler(function ($exception) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'msg' => 'Internal server error.', 'error' => $exception->getMessage()]);
    exit;
});

// Always respond with JSON
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';

switch ($action) {

    // ── Get all products ──────────────────────────────────
    case 'get_products':
        $products = fetchAll('SELECT * FROM items ORDER BY created_at DESC');
        // No specs decoding — specifications removed
        jsonResponse(['ok' => true, 'products' => $products]);
        break;

    // ── Get homepage content ──────────────────────────────
    case 'get_homepage':
        $hp = fetchOne('SELECT * FROM homepage LIMIT 1');
        if ($hp) {
            // Map snake_case DB fields to camelCase for JavaScript
            $hp['heroTag']       = $hp['hero_tag']      ?? null;
            $hp['heroTitle']     = $hp['hero_title']    ?? null;
            $hp['heroText']      = $hp['hero_text']     ?? null;
            $hp['heroImage']     = $hp['hero_image']    ?? null;
            $hp['aboutHeadline'] = $hp['about_headline'] ?? null;
            $hp['aboutText']     = $hp['about_text']    ?? null;
            $hp['customers']     = $hp['customers']     ?? null;
            $hp['footerText']    = $hp['footer_text']   ?? null;
            $hp['facebook']      = $hp['facebook_page'] ?? null;
            $hp['contact']       = $hp['contact_number'] ?? null;
            $hp['address']       = $hp['location']      ?? null;
            $hp['hours']         = $hp['opening_hours'] ?? null;
            $hp['owner']         = $hp['owner']         ?? null;
            $hp['ownerImage']    = $hp['owner_image']   ?? null;

            // Parse staff members (stored as multi-line "Name|Role|PhotoPath")
            $hp['staffMembers'] = [];
            if (!empty($hp['staffs'])) {
                foreach (preg_split('/\r\n|\r|\n/', $hp['staffs']) as $line) {
                    $line = trim($line);
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    $hp['staffMembers'][] = [
                        'name'  => $parts[0] ?? $line,
                        'role'  => $parts[1] ?? '',
                        'photo' => $parts[2] ?? '',
                    ];
                }
            }


            // Recent shop feedbacks (if orders table supports them)
            $hp['feedbacks'] = [];
            $hp['shopRating'] = null;
            if (columnExists('orders', 'shop_feedback')) {
                // Only include feedbacks the admin has approved (show_feedback = 1) when available
                $whereShow = columnExists('orders', 'show_feedback') ? "AND o.show_feedback = 1" : "";
                // Only include feedbacks from delivered or cancelled orders
                $rows = fetchAll("SELECT o.shop_feedback, o.shop_rating, o.date, o.status, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.shop_feedback IS NOT NULL AND o.status IN ('Delivered','Cancelled') {$whereShow} ORDER BY o.date DESC LIMIT 6");
                $fb = [];
                foreach ($rows as $r) {
                    $fb[] = [
                        'text' => $r['shop_feedback'] ?? '',
                        'rating' => isset($r['shop_rating']) ? (int)$r['shop_rating'] : null,
                        'author' => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Customer',
                        'date' => $r['date'] ?? null,
                    ];
                }
                $hp['feedbacks'] = $fb;
            }

            // Admin-only feedback listing (used by admin panel)
            if (getUser() && getUser()['role'] === 'admin' && isset($_REQUEST['include_admin_feedback']) && $_REQUEST['include_admin_feedback']) {
                $feedbacks = [];
                if (columnExists('orders', 'shop_feedback')) {
                    // Admin view: include order status and only show feedbacks from delivered/cancelled orders
                    $rows = fetchAll("SELECT o.id, o.shop_feedback, o.shop_rating, o.date, o.show_feedback, o.status, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.shop_feedback IS NOT NULL AND o.status IN ('Delivered','Cancelled') ORDER BY o.date DESC");
                    foreach ($rows as $r) {
                        $feedbacks[] = [
                            'id' => $r['id'],
                            'text' => $r['shop_feedback'] ?? '',
                            'rating' => isset($r['shop_rating']) ? (int)$r['shop_rating'] : null,
                            'author' => trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: 'Customer',
                            'date' => $r['date'] ?? null,
                            'status' => $r['status'] ?? null,
                            'show' => !empty($r['show_feedback']) ? 1 : 0,
                        ];
                    }
                }
                $hp['admin_feedbacks'] = $feedbacks;
            }
            if (columnExists('orders', 'shop_rating')) {
                $avg = fetchOne("SELECT AVG(shop_rating) AS avg_rating FROM orders WHERE shop_rating IS NOT NULL");
                if (!empty($avg) && isset($avg['avg_rating'])) {
                    $hp['shopRating'] = round((float)$avg['avg_rating'], 1);
                }
            }
        }
        jsonResponse(['ok' => true, 'homepage' => $hp]);
        break;

    // ── Get current session ───────────────────────────────
    case 'get_session':
        jsonResponse(['ok' => true, 'session' => getUser()]);
        break;

    // ── Get orders (user sees own, admin sees all) ────────
    case 'get_orders':
        $user = getUser();
        if (!$user) {
            jsonResponse(['ok' => false, 'msg' => 'Unauthorized.'], 401);
        }

        if ($user['role'] === 'admin') {
            $orders = fetchAll('SELECT * FROM orders ORDER BY date DESC');
        } else {
            $userId = (int) $user['id'];
            $orders = fetchAll("SELECT * FROM orders WHERE user_id = {$userId} ORDER BY date DESC");
        }

        // Decode items JSON for each order
        foreach ($orders as &$order) {
            $order['items'] = json_decode($order['items'] ?? '[]', true) ?: [];
        }
        unset($order);

        jsonResponse(['ok' => true, 'orders' => $orders]);
        break;

    // ── Place a new order ────────────────────────────────
    case 'add_order':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['ok' => false, 'msg' => 'POST required.'], 405);
        }
        $user = getUser();
        if (!$user) jsonResponse(['ok' => false, 'msg' => 'Unauthorized.'], 401);

        $items         = json_decode($_POST['items'] ?? '[]', true);
        $total         = floatval($_POST['total'] ?? 0);
        $address       = trim($_POST['address'] ?? '');
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $contactNumber = trim($_POST['contact_number'] ?? '');

        if (empty($items) || $total <= 0) {
            jsonResponse(['ok' => false, 'msg' => 'Invalid order data.']);
        }

        if (!$address || !$paymentMethod || !$contactNumber) {
            jsonResponse(['ok' => false, 'msg' => 'Address, payment method, and contact number are required.']);
        }

        if (!columnExists('orders', 'delivery_address')) {
            runQuery("ALTER TABLE orders ADD COLUMN delivery_address TEXT DEFAULT NULL");
        }
        if (!columnExists('orders', 'payment_method')) {
            runQuery("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(100) DEFAULT NULL");
        }
        if (!columnExists('orders', 'contact_number')) {
            runQuery("ALTER TABLE orders ADD COLUMN contact_number VARCHAR(100) DEFAULT NULL");
        }

        runQuery("INSERT INTO orders (user_id, status, date, items, total, delivery_address, payment_method, contact_number) VALUES (
            " . (int) $user['id'] . ",
            'Processing',
            '" . date('Y-m-d H:i:s') . "',
            '" . escape(json_encode($items)) . "',
            " . number_format($total, 2, '.', '') . ",
            '" . escape($address) . "',
            '" . escape($paymentMethod) . "',
            '" . escape($contactNumber) . "'
        )");

        jsonResponse(['ok' => true, 'order_id' => lastInsertId()]);
        break;

    // ── Submit product & shop feedback for an order ───────
    case 'submit_order_feedback':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['ok' => false, 'msg' => 'POST required.'], 405);
        }
        $user = getUser();
        if (!$user) jsonResponse(['ok' => false, 'msg' => 'Unauthorized.'], 401);

        $orderId         = intval($_POST['order_id'] ?? 0);
        $productRating   = intval($_POST['product_rating'] ?? 0);
        $shopRating      = intval($_POST['shop_rating'] ?? 0);
        $productFeedback = trim($_POST['product_feedback'] ?? '');
        $shopFeedback    = trim($_POST['shop_feedback'] ?? '');

        if ($orderId <= 0) {
            jsonResponse(['ok' => false, 'msg' => 'Invalid order ID.']);
        }

        if (!columnExists('orders', 'product_rating')) {
            runQuery('ALTER TABLE orders ADD COLUMN product_rating INT DEFAULT NULL');
        }
        if (!columnExists('orders', 'product_feedback')) {
            runQuery('ALTER TABLE orders ADD COLUMN product_feedback TEXT DEFAULT NULL');
        }
        if (!columnExists('orders', 'shop_rating')) {
            runQuery('ALTER TABLE orders ADD COLUMN shop_rating INT DEFAULT NULL');
        }
        if (!columnExists('orders', 'shop_feedback')) {
            runQuery('ALTER TABLE orders ADD COLUMN shop_feedback TEXT DEFAULT NULL');
        }

        $order = fetchOne("SELECT * FROM orders WHERE id = {$orderId} AND user_id = " . (int) $user['id'] . " LIMIT 1");
        if (!$order) {
            jsonResponse(['ok' => false, 'msg' => 'Order not found.'], 404);
        }

        if (!in_array($order['status'], ['Delivered', 'Cancelled'], true)) {
            jsonResponse(['ok' => false, 'msg' => 'Feedback may only be submitted for delivered or cancelled orders.']);
        }

        $alreadyReviewed = ($order['product_rating'] !== null || $order['shop_rating'] !== null || !empty($order['product_feedback']) || !empty($order['shop_feedback']));
        if ($alreadyReviewed) {
            jsonResponse(['ok' => false, 'msg' => 'Feedback has already been submitted for this order.']);
        }

        if ($productRating < 1 || $productRating > 5) {
            jsonResponse(['ok' => false, 'msg' => 'Product rating must be between 1 and 5.']);
        }
        if ($shopRating < 1 || $shopRating > 5) {
            jsonResponse(['ok' => false, 'msg' => 'Shop rating must be between 1 and 5.']);
        }

        runQuery("UPDATE orders SET 
            product_rating = {$productRating},
            product_feedback = '" . escape($productFeedback) . "',
            shop_rating = {$shopRating},
            shop_feedback = '" . escape($shopFeedback) . "'
            WHERE id = {$orderId} LIMIT 1");

        jsonResponse(['ok' => true, 'msg' => 'Thank you. Your review has been submitted.']);
        break;

    // ── Get all staff members ────────────────────────────
    case 'get_staff':
        $requiredColumns = ['facebook', 'linkedin', 'twitter', 'instagram'];
        foreach ($requiredColumns as $column) {
            if (!columnExists('staff', $column)) {
                runQuery("ALTER TABLE staff ADD COLUMN {$column} VARCHAR(255) DEFAULT NULL");
            }
        }

        $staff = fetchAll('SELECT id, name, position, description, image, facebook, linkedin, twitter, instagram, created_at FROM staff ORDER BY created_at ASC');

        if (empty($staff)) {
            $hp = fetchOne('SELECT * FROM homepage LIMIT 1');
            $fallbackStaff = [];
            if ($hp && !empty($hp['staffs'])) {
                foreach (preg_split('/\r\n|\r|\n/', $hp['staffs']) as $line) {
                    $line = trim($line);
                    if (!$line) continue;
                    $parts = array_map('trim', explode('|', $line));
                    $fallbackStaff[] = [
                        'id' => null,
                        'name' => $parts[0] ?? '',
                        'position' => $parts[1] ?? '',
                        'description' => $parts[2] ?? '',
                        'image' => $parts[3] ?? '',
                        'facebook' => '',
                        'linkedin' => '',
                        'twitter' => '',
                        'instagram' => '',
                        'created_at' => null,
                    ];
                }
            }
            jsonResponse(['ok' => true, 'staff' => $fallbackStaff]);
            break;
        }

        jsonResponse(['ok' => true, 'staff' => $staff]);
        break;

    default:
        jsonResponse(['ok' => false, 'msg' => 'Unknown action.'], 400);
}
