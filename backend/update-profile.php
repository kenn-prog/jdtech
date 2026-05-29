<?php
// ============================================================
//  JDTech — Update Profile (Backend Handler)
//  PURPOSE: Allows logged-in users to update their name/phone.
// ============================================================

require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'msg' => 'Method not allowed.'], 405);
}

requireLoginAPI(); // Must be logged in

$user          = getUser();
$firstName     = trim($_POST['first_name']     ?? '');
$lastName      = trim($_POST['last_name']      ?? '');
$phone         = trim($_POST['phone']          ?? '');
$address       = trim($_POST['address']        ?? '');
$paymentMethod = trim($_POST['payment_method'] ?? '');
$contactNumber = trim($_POST['contact_number'] ?? '');

if (!$firstName || !$lastName) {
    jsonResponse(['ok' => false, 'msg' => 'First and last name are required.']);
}

// Ensure the profile columns exist before updating.
if (!columnExists('users', 'address')) {
    runQuery("ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL");
}
if (!columnExists('users', 'payment_method')) {
    runQuery("ALTER TABLE users ADD COLUMN payment_method VARCHAR(100) DEFAULT NULL");
}
if (!columnExists('users', 'contact_number')) {
    runQuery("ALTER TABLE users ADD COLUMN contact_number VARCHAR(100) DEFAULT NULL");
}

runQuery("UPDATE users SET 
    first_name      = '" . escape($firstName)     . "',
    last_name       = '" . escape($lastName)      . "',
    phone           = '" . escape($phone)         . "',
    address         = '" . escape($address)       . "',
    payment_method  = '" . escape($paymentMethod) . "',
    contact_number  = '" . escape($contactNumber) . "'
    WHERE id = " . (int) $user['id'] . " LIMIT 1");

// Update session data too
$_SESSION['user']['firstName']    = $firstName;
$_SESSION['user']['lastName']     = $lastName;
$_SESSION['user']['phone']        = $phone;
$_SESSION['user']['address']      = $address;
$_SESSION['user']['paymentMethod']= $paymentMethod;
$_SESSION['user']['contactNumber']= $contactNumber;

jsonResponse(['ok' => true, 'user' => $_SESSION['user']]);
