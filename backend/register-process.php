<?php
// ============================================================
//  JDTech — Register Process (Backend Handler)
//  PURPOSE: Receives registration form data, validates it,
//           saves new user to database, creates a session.
//
//  SECURITY:
//  ✅ password_hash() — NEVER store plain-text passwords!
//  ✅ Duplicate email check before inserting
//  ✅ Input trimming and validation
//  ✅ Session created immediately after registration
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
}

// Get and trim inputs
$firstName = trim($_POST['first_name'] ?? '');
$lastName  = trim($_POST['last_name']  ?? '');
$email     = trim($_POST['email']      ?? '');
$phone     = trim($_POST['phone']      ?? '');
$password  = trim($_POST['password']   ?? '');

// Validate required fields
if (!$firstName || !$lastName || !$email || !$password) {
    jsonResponse(['ok' => false, 'msg' => 'Please fill in all required fields.']);
}

if (!isValidEmail($email)) {
    jsonResponse(['ok' => false, 'msg' => 'Please enter a valid email address.']);
}

if (strlen($password) < 8) {
    jsonResponse(['ok' => false, 'msg' => 'Password must be at least 8 characters.']);
}

// Check if email already registered
$emailSafe = escape($email);
$existing  = fetchOne("SELECT id FROM users WHERE email = '{$emailSafe}' LIMIT 1");

if ($existing) {
    jsonResponse(['ok' => false, 'msg' => 'This email address is already registered.']);
}

// Hash the password — NEVER store plain text passwords
// password_hash() automatically adds a salt and uses bcrypt
$passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => BCRYPT_COST]);
$joinedAt     = date('Y-m-d H:i:s');

// Insert new user into database
$inserted = runQuery("INSERT INTO users 
    (email, password, role, first_name, last_name, phone, joined_at, avatar) 
    VALUES (
        '" . escape($email)      . "',
        '" . escape($passwordHash) . "',
        'user',
        '" . escape($firstName) . "',
        '" . escape($lastName)  . "',
        '" . escape($phone)     . "',
        '{$joinedAt}',
        '👤'
    )");

if (!$inserted) {
    jsonResponse(['ok' => false, 'msg' => 'Registration failed. Please try again.']);
}

$userId = lastInsertId();

// Create session for newly registered user (auto-login)
session_regenerate_id(true);
$_SESSION['user'] = [
    'id'        => $userId,
    'role'      => 'user',
    'firstName' => $firstName,
    'lastName'  => $lastName,
    'email'     => $email,
    'phone'     => $phone,
    'avatar'    => '👤',
    'joinedAt'  => $joinedAt,
];

jsonResponse(['ok' => true, 'user' => $_SESSION['user']]);
