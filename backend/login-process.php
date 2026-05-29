<?php
// ============================================================
//  JDTech — Login Process (Backend Handler)
//  PURPOSE: Receives login form data via POST, verifies
//           credentials, creates a session, returns JSON.
//
//  SECURITY BEST PRACTICES USED HERE:
//  ✅ password_verify() — compares plain password to hash
//  ✅ mysqli_real_escape_string() — prevents SQL injection
//  ✅ session_regenerate_id() — prevents session fixation
//  ✅ Never returns which field was wrong (security through vagueness)
//  ✅ JSON responses only — no redirects from here
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
}

// Get and sanitize inputs
$email    = trim($_POST['email']    ?? '');
$password =       $_POST['password'] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    jsonResponse(['ok' => false, 'msg' => 'Email and password are required.']);
}

if (!isValidEmail($email)) {
    jsonResponse(['ok' => false, 'msg' => 'Invalid email address.']);
}

// ── Check Admin table first ──────────────────────────────
$emailSafe = escape($email);

$adminRow = fetchOne("SELECT * FROM admin WHERE username = '{$emailSafe}' OR email = '{$emailSafe}' LIMIT 1");

if ($adminRow && password_verify($password, $adminRow['password'])) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'        => (int) $adminRow['id'],
        'role'      => 'admin',
        'firstName' => $adminRow['username'],
        'lastName'  => '',
        'email'     => $adminRow['email'] ?? $email,
        'avatar'    => '⚡',
    ];

    jsonResponse(['ok' => true, 'user' => $_SESSION['user']]);
}

// ── Check Users table ────────────────────────────────────
$userRow = fetchOne("SELECT * FROM users WHERE email = '{$emailSafe}' LIMIT 1");

if ($userRow && password_verify($password, $userRow['password'])) {
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'            => (int) $userRow['id'],
        'role'          => 'user',
        'firstName'     => $userRow['first_name'],
        'lastName'      => $userRow['last_name'],
        'email'         => $userRow['email'],
        'phone'         => $userRow['phone'] ?? '',
        'address'       => $userRow['address'] ?? '',
        'paymentMethod' => $userRow['payment_method'] ?? '',
        'contactNumber' => $userRow['contact_number'] ?? '',
        'avatar'        => $userRow['avatar'] ?? '👤',
    ];

    jsonResponse(['ok' => true, 'user' => $_SESSION['user']]);
}

// ── Neither matched ──────────────────────────────────────
// Use a vague error message so attackers can't tell if email exists
jsonResponse(['ok' => false, 'msg' => 'Invalid email or password.']);
